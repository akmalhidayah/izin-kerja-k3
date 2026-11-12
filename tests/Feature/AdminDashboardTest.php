<?php

namespace Tests\Feature;

use App\Models\Jsa;
use App\Models\Notification;
use App\Models\Role;
use App\Models\StepApproval;
use App\Models\StepApprovalLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Dashboard dan daftar permintaan admin: angka tetap benar dan jumlah query tidak ikut
 * membesar bersama jumlah pengajuan (sebelumnya satu query approval per baris).
 */
class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function admin(): User
    {
        $role = Role::firstOrCreate(['name' => 'Admin']);

        return User::factory()->create(['usertype' => 'admin', 'role_id' => $role->id]);
    }

    private function notification(User $user, array $approvals = [], array $extra = []): Notification
    {
        $notification = Notification::create(array_merge([
            'user_id' => $user->id,
            'type' => 'notif',
            'number' => 'N-'.uniqid(),
            'description' => 'Pekerjaan uji',
            'status' => 'menunggu',
        ], $extra));

        foreach ($approvals as $step => $status) {
            StepApproval::create([
                'notification_id' => $notification->id,
                'step' => $step,
                'status' => $status,
            ]);
        }

        return $notification;
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

    private function countQueries(callable $fn): int
    {
        DB::flushQueryLog();
        DB::enableQueryLog();
        $fn();
        $count = count(DB::getQueryLog());
        DB::disableQueryLog();

        return $count;
    }

    public function test_dashboard_shows_correct_counts(): void
    {
        $admin = $this->admin();
        $vendor = User::factory()->create(['usertype' => 'user', 'name' => 'PT Vendor Uji']);
        $pgo = User::factory()->create(['usertype' => 'pgo', 'name' => 'Karyawan Uji']);

        $this->notification($vendor, ['op_spk' => 'disetujui', 'data_kontraktor' => 'revisi']);
        $this->notification($vendor, ['op_spk' => 'disetujui', 'sik' => 'disetujui']);
        $this->notification($pgo, ['op_spk' => 'disetujui']);
        $this->notification($pgo);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();

        $summary = $response->viewData('summaryRequests');
        $this->assertCount(4, $summary);
        $this->assertSame(1, $summary->where('status', 'Perlu Revisi')->count());
        $this->assertSame(1, $summary->where('status', 'Terbit SIK')->count());
        $this->assertSame(2, $summary->where('status', 'Menunggu')->count());

        $topVendor = $response->viewData('topVendorUser');
        $this->assertSame('PT Vendor Uji', $topVendor->first()->name);
        $this->assertSame(2, $topVendor->first()->total_requests);

        $topPgo = $response->viewData('topVendorPgo');
        $this->assertSame('Karyawan Uji', $topPgo->first()->name);

        $this->assertSame(array_sum($response->viewData('monthlyChart')), 4);
        $this->assertContains((int) now()->year, $response->viewData('years')->all());
    }

    public function test_dashboard_query_count_does_not_grow_with_requests(): void
    {
        $admin = $this->admin();
        $vendor = User::factory()->create(['usertype' => 'user']);

        for ($i = 0; $i < 40; $i++) {
            $this->notification($vendor, ['op_spk' => 'disetujui', 'jsa' => $i % 3 === 0 ? 'revisi' : 'menunggu']);
        }

        $queries = $this->countQueries(fn () => $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk());

        $this->assertLessThan(45, $queries, "Dashboard menjalankan {$queries} query untuk 40 pengajuan.");
    }

    public function test_vendor_list_rows_keep_status_and_query_count_bounded(): void
    {
        $admin = $this->admin();
        $vendor = User::factory()->create(['usertype' => 'user']);

        $withJsa = $this->notification($vendor, ['op_spk' => 'disetujui']);
        $this->jsa($withJsa);

        $revisi = $this->notification($vendor, ['op_spk' => 'disetujui', 'data_kontraktor' => 'revisi']);

        for ($i = 0; $i < 8; $i++) {
            $this->notification($vendor);
        }

        $rows = null;
        $queries = $this->countQueries(function () use ($admin, &$rows) {
            $response = $this->actingAs($admin)->get(route('admin.permintaansik'));
            $response->assertOk();
            $rows = $response->viewData('requests')->getCollection()->keyBy('id');
        });

        $this->assertCount(10, $rows);
        $this->assertLessThan(24, $queries, "Daftar vendor menjalankan {$queries} query untuk 10 baris.");

        $this->assertSame('Perlu Disetujui', $rows[$withJsa->id]->status, 'JSA tersimpan harus terbaca sebagai perlu disetujui');
        $this->assertSame(2, $rows[$withJsa->id]->current_step);
        $this->assertSame('Input Data Kontraktor', $rows[$withJsa->id]->current_step_title);
        $this->assertSame('Perlu Revisi', $rows[$revisi->id]->status);
        $this->assertSame(1, $rows[$withJsa->id]->approved_steps);
    }

    public function test_pgo_list_uses_three_steps(): void
    {
        $admin = $this->admin();
        $pgo = User::factory()->create(['usertype' => 'pgo', 'jabatan' => 'Staf']);

        $done = $this->notification($pgo, ['op_spk' => 'disetujui', 'jsa' => 'disetujui', 'working_permit' => 'disetujui']);

        $response = $this->actingAs($admin)->get(route('admin.permintaansikpgo'));
        $response->assertOk();

        $row = $response->viewData('requests')->getCollection()->firstWhere('id', $done->id);
        $this->assertTrue($row->is_completed);
        $this->assertSame(100.0, (float) $row->progress);
        $this->assertSame('Staf', $row->user_jabatan);
        $this->assertSame(3, $response->viewData('totalSteps'));
    }

    public function test_cards_split_counts_by_vendor_and_pgo(): void
    {
        $admin = $this->admin();
        $vendor = User::factory()->create(['usertype' => 'user']);
        $pgo = User::factory()->create(['usertype' => 'pgo']);

        // menunggu (vendor), belum ditangani
        $this->notification($vendor, ['op_spk' => 'disetujui']);
        // menunggu (pgo), sudah ditangani
        $this->notification($pgo, ['op_spk' => 'disetujui'], ['assigned_admin_id' => $admin->id]);
        // revisi (vendor)
        $this->notification($vendor, ['op_spk' => 'disetujui', 'bpjs' => 'revisi'], ['assigned_admin_id' => $admin->id]);
        // selesai vendor bulan ini
        $this->notification($vendor, ['sik' => 'disetujui'], ['assigned_admin_id' => $admin->id]);
        // selesai pgo bulan ini
        $this->notification($pgo, ['op_spk' => 'disetujui', 'jsa' => 'disetujui', 'working_permit' => 'disetujui'], ['assigned_admin_id' => $admin->id]);
        // vendor dengan working_permit disetujui tetap belum selesai (bukan PGO)
        $this->notification($vendor, ['working_permit' => 'disetujui'], ['assigned_admin_id' => $admin->id]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        $response->assertOk();

        $cards = $response->viewData('cards');

        $this->assertSame(['total' => 3, 'vendor' => 2, 'pgo' => 1], array_intersect_key($cards['menunggu'], ['total' => 1, 'vendor' => 1, 'pgo' => 1]));
        $this->assertSame(['total' => 1, 'vendor' => 1, 'pgo' => 0], array_intersect_key($cards['belum_ditangani'], ['total' => 1, 'vendor' => 1, 'pgo' => 1]));
        $this->assertSame(['total' => 1, 'vendor' => 1, 'pgo' => 0], array_intersect_key($cards['revisi'], ['total' => 1, 'vendor' => 1, 'pgo' => 1]));
        $this->assertSame(['total' => 2, 'vendor' => 1, 'pgo' => 1], array_intersect_key($cards['selesai'], ['total' => 1, 'vendor' => 1, 'pgo' => 1]));
        $this->assertSame('+2 dari bulan lalu', $cards['selesai']['note']);
        $this->assertSame(0, $response->viewData('stale')['total']);
    }

    public function test_my_queue_lists_own_and_unassigned_requests_oldest_first(): void
    {
        $me = $this->admin();
        $other = $this->admin();
        $vendor = User::factory()->create(['usertype' => 'user', 'name' => 'PT Antre']);

        $mine = $this->notification($vendor, ['op_spk' => 'disetujui'], ['assigned_admin_id' => $me->id]);
        $unassigned = $this->notification($vendor, [], []);
        $others = $this->notification($vendor, [], ['assigned_admin_id' => $other->id]);
        $finished = $this->notification($vendor, ['sik' => 'disetujui'], ['assigned_admin_id' => $me->id]);

        $unassigned->forceFill(['created_at' => now()->subDays(10)])->save();

        $response = $this->actingAs($me)->get(route('admin.dashboard'));
        $response->assertOk();

        $queue = $response->viewData('myQueue');
        $ids = $queue->pluck('id')->all();

        $this->assertSame([$unassigned->id, $mine->id], $ids, 'Yang paling lama menunggu tampil lebih dulu');
        $this->assertNotContains($others->id, $ids);
        $this->assertNotContains($finished->id, $ids);
        $this->assertTrue($queue[0]->is_unassigned);
        $this->assertSame(10, $queue[0]->waiting_days);
        $this->assertSame(2, $queue[1]->current_step);
        $this->assertSame('Input Data Kontraktor', $queue[1]->current_step_title);
        $this->assertSame(2, $response->viewData('myQueueTotal'));
        $response->assertSee('Ambil');
    }

    public function test_stale_requests_are_counted_and_filterable(): void
    {
        $admin = $this->admin();
        $vendor = User::factory()->create(['usertype' => 'user']);

        $stale = $this->notification($vendor, ['op_spk' => 'disetujui']);
        $stale->forceFill(['created_at' => now()->subDays(12)])->save();
        StepApproval::where('notification_id', $stale->id)->update(['updated_at' => now()->subDays(9)]);

        $recentlyTouched = $this->notification($vendor, ['op_spk' => 'disetujui']);
        $recentlyTouched->forceFill(['created_at' => now()->subDays(12)])->save();

        $fresh = $this->notification($vendor);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        $this->assertSame(['total' => 1, 'vendor' => 1, 'pgo' => 0], array_intersect_key($response->viewData('stale'), ['total' => 1, 'vendor' => 1, 'pgo' => 1]));

        $list = $this->actingAs($admin)->get(route('admin.permintaansik', ['kondisi' => 'mandek']));
        $list->assertOk();
        $this->assertSame([$stale->id], $list->viewData('requests')->getCollection()->pluck('id')->all());

        $tabs = collect($list->viewData('kondisiTabs'))->keyBy('key');
        $this->assertSame(1, $tabs['mandek']['count']);
        $this->assertSame(3, $tabs['']['count']);
        $this->assertSame(3, $tabs['menunggu']['count']);
        $this->assertTrue($tabs['mandek']['active']);
    }

    public function test_kondisi_filter_on_lists(): void
    {
        $admin = $this->admin();
        $vendor = User::factory()->create(['usertype' => 'user']);
        $pgo = User::factory()->create(['usertype' => 'pgo']);

        $unassigned = $this->notification($vendor);
        $revisi = $this->notification($vendor, ['op_spk' => 'revisi'], ['assigned_admin_id' => $admin->id]);
        $done = $this->notification($vendor, ['sik' => 'disetujui'], ['assigned_admin_id' => $admin->id]);
        $pgoDone = $this->notification($pgo, ['working_permit' => 'disetujui'], ['assigned_admin_id' => $admin->id]);
        $pgoOpen = $this->notification($pgo, ['op_spk' => 'disetujui'], ['assigned_admin_id' => $admin->id]);

        $ids = fn ($route, $kondisi) => $this->actingAs($admin)
            ->get(route($route, ['kondisi' => $kondisi]))
            ->viewData('requests')->getCollection()->pluck('id')->sort()->values()->all();

        $this->assertSame([$unassigned->id], $ids('admin.permintaansik', 'belum_ditangani'));
        $this->assertSame([$revisi->id], $ids('admin.permintaansik', 'revisi'));
        $this->assertSame([$done->id], $ids('admin.permintaansik', 'selesai'));
        $this->assertSame([$unassigned->id], $ids('admin.permintaansik', 'menunggu'));
        $this->assertSame([$pgoDone->id], $ids('admin.permintaansikpgo', 'selesai'));
        $this->assertSame([$pgoOpen->id], $ids('admin.permintaansikpgo', 'menunggu'));

        // kondisi tak dikenal diabaikan
        $this->assertCount(3, $ids('admin.permintaansik', 'apa-saja'));
    }

    public function test_recent_activity_hides_backfill_rows(): void
    {
        $admin = $this->admin();
        $vendor = User::factory()->create(['usertype' => 'user', 'name' => 'PT Lama']);
        $notification = $this->notification($vendor, ['op_spk' => 'disetujui']);

        StepApprovalLog::create([
            'notification_id' => $notification->id,
            'step' => 'op_spk',
            'action' => StepApprovalLog::ACTION_BACKFILL,
            'to_status' => 'disetujui',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $this->assertCount(0, $response->viewData('recentActivities'));
        $response->assertDontSee('Data awal');
    }

    public function test_monthly_chart_stops_at_current_month(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        $this->assertSame((int) now()->month, $response->viewData('lastMonth'));

        $past = $this->actingAs($admin)->get(route('admin.dashboard', ['year' => now()->year - 1]));
        $this->assertSame(12, $past->viewData('lastMonth'));
    }
}
