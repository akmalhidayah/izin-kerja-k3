<?php

namespace Tests\Feature;

use App\Models\Jsa;
use App\Models\Notification;
use App\Models\Role;
use App\Models\StepApproval;
use App\Models\StepApprovalLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApprovalHistoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function admin(string $name = 'Admin Uji'): User
    {
        $role = Role::firstOrCreate(['name' => 'Admin']);

        return User::factory()->create(['usertype' => 'admin', 'role_id' => $role->id, 'name' => $name]);
    }

    private function notification(?User $owner = null): Notification
    {
        $owner ??= User::factory()->create(['usertype' => 'user']);

        return Notification::create([
            'user_id' => $owner->id,
            'type' => 'notif',
            'number' => 'N-'.uniqid(),
            'description' => 'Pekerjaan uji',
            'status' => 'menunggu',
        ]);
    }

    private function jsa(Notification $notification): Jsa
    {
        return Jsa::create([
            'notification_id' => $notification->id,
            'nama_perusahaan' => 'PT Uji',
            'no_jsa' => '001/JSA/ST/112026',
            'nama_jsa' => 'Pekerjaan uji',
            'departemen' => 'Produksi',
            'area_kerja' => 'Area uji',
            'tanggal' => now()->toDateString(),
            'dibuat_nama' => 'Pembuat',
            'disetujui_nama' => 'Penyetuju',
            'langkah_kerja' => [['langkah' => 'Uji', 'bahaya' => '-', 'pengendalian' => '-']],
        ]);
    }

    public function test_status_change_is_logged_with_actor_and_transition(): void
    {
        $admin = $this->admin('Jusriana');
        $notification = $this->notification();

        $this->actingAs($admin)
            ->post(route('admin.permintaansik.updateStatus', [$notification->id, 'op_spk']), ['status' => 'disetujui'])
            ->assertRedirect();

        $this->actingAs($admin)
            ->post(route('admin.permintaansik.updateStatus', [$notification->id, 'op_spk']), [
                'status' => 'revisi',
                'catatan' => 'Nomor SPK belum sesuai',
            ])
            ->assertRedirect();

        $logs = StepApprovalLog::where('notification_id', $notification->id)->orderBy('id')->get();

        $this->assertCount(2, $logs);

        $this->assertNull($logs[0]->from_status);
        $this->assertSame('disetujui', $logs[0]->to_status);
        $this->assertSame($admin->id, $logs[0]->actor_id);
        $this->assertSame(StepApprovalLog::ACTION_STATUS_CHANGED, $logs[0]->action);

        $this->assertSame('disetujui', $logs[1]->from_status);
        $this->assertSame('revisi', $logs[1]->to_status);
        $this->assertSame('Nomor SPK belum sesuai', $logs[1]->catatan);
        $this->assertSame('Jusriana', $logs[1]->actor_name);
    }

    public function test_resubmitting_the_same_status_writes_no_log(): void
    {
        $admin = $this->admin();
        $notification = $this->notification();

        foreach (range(1, 3) as $_) {
            $this->actingAs($admin)
                ->post(route('admin.permintaansik.updateStatus', [$notification->id, 'jsa']), ['status' => 'disetujui']);
        }

        $this->assertSame(1, StepApprovalLog::where('notification_id', $notification->id)->count());
    }

    public function test_non_assigned_admin_cannot_change_status_and_nothing_is_logged(): void
    {
        $first = $this->admin('Pertama');
        $second = $this->admin('Kedua');
        $notification = $this->notification();

        $this->actingAs($first)
            ->post(route('admin.permintaansik.updateStatus', [$notification->id, 'op_spk']), ['status' => 'disetujui']);

        $this->actingAs($second)
            ->post(route('admin.permintaansik.updateStatus', [$notification->id, 'op_spk']), ['status' => 'revisi', 'catatan' => 'x'])
            ->assertSessionHas('error');

        $this->assertSame('disetujui', StepApproval::where('notification_id', $notification->id)->where('step', 'op_spk')->value('status'));
        $this->assertSame(1, StepApprovalLog::where('notification_id', $notification->id)->count());
    }

    public function test_deleting_jsa_resets_step_and_logs_reset(): void
    {
        $admin = $this->admin();
        $notification = $this->notification();
        $notification->update(['assigned_admin_id' => $admin->id]);

        $this->jsa($notification);
        StepApproval::create(['notification_id' => $notification->id, 'step' => 'jsa', 'status' => 'disetujui', 'catatan' => 'ok']);

        $this->actingAs($admin)
            ->delete(route('admin.permintaansik.deleteJsa', $notification->id))
            ->assertRedirect();

        $approval = StepApproval::where('notification_id', $notification->id)->where('step', 'jsa')->first();
        $this->assertSame('menunggu', $approval->status);
        $this->assertNull($approval->catatan);

        $log = StepApprovalLog::where('notification_id', $notification->id)->latest('id')->first();
        $this->assertSame(StepApprovalLog::ACTION_RESET, $log->action);
        $this->assertSame('disetujui', $log->from_status);
        $this->assertSame('menunggu', $log->to_status);
        $this->assertSame($admin->id, $log->actor_id);
        $this->assertSame('Direset karena data dihapus', $log->action_label);
    }

    public function test_detail_page_contains_history_rows_for_each_step(): void
    {
        $admin = $this->admin('Jusriana');
        $notification = $this->notification();

        $this->actingAs($admin)
            ->post(route('admin.permintaansik.updateStatus', [$notification->id, 'op_spk']), [
                'status' => 'revisi',
                'catatan' => 'Lampiran SPK kabur',
            ]);

        $response = $this->actingAs($admin)->get(route('admin.permintaansik.show', $notification->id));

        $response->assertOk();
        $response->assertSee('Riwayat Persetujuan');
        $response->assertSee('Lampiran SPK kabur');
        $response->assertSee('Jusriana');

        $stepData = collect($response->viewData('data')->step_data)->keyBy('step');
        $this->assertCount(1, $stepData['op_spk']['history']);
        $this->assertSame('Perlu Revisi', $stepData['op_spk']['history'][0]['ke']);
        $this->assertSame('-', $stepData['op_spk']['history'][0]['dari']);
        $this->assertCount(0, $stepData['bpjs']['history']);
        $this->assertCount(1, $response->viewData('data')->history);
    }

    public function test_dashboard_recent_activity_reads_from_logs(): void
    {
        $admin = $this->admin('Jusriana');
        $owner = User::factory()->create(['usertype' => 'user', 'name' => 'PT Vendor Aktif']);
        $notification = $this->notification($owner);

        $this->actingAs($admin)
            ->post(route('admin.permintaansik.updateStatus', [$notification->id, 'op_spk']), ['status' => 'disetujui']);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $activities = $response->viewData('recentActivities');
        $this->assertCount(1, $activities);
        $this->assertSame('PT Vendor Aktif', $activities[0]->user_name);
        $this->assertSame('Disetujui', $activities[0]->ke);
        $this->assertSame('Jusriana', $activities[0]->aktor);
        $response->assertSee('PT Vendor Aktif');
    }

    public function test_deleting_notification_removes_its_logs(): void
    {
        $admin = $this->admin();
        $notification = $this->notification();

        $this->actingAs($admin)
            ->post(route('admin.permintaansik.updateStatus', [$notification->id, 'op_spk']), ['status' => 'disetujui']);

        $this->assertSame(1, StepApprovalLog::count());

        $notification->delete();

        $this->assertSame(0, StepApprovalLog::count());
    }
}
