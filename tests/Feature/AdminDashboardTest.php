<?php

namespace Tests\Feature;

use App\Models\Jsa;
use App\Models\Notification;
use App\Models\Role;
use App\Models\StepApproval;
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

        $this->assertLessThan(20, $queries, "Dashboard menjalankan {$queries} query untuk 40 pengajuan.");
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
        $this->assertLessThan(16, $queries, "Daftar vendor menjalankan {$queries} query untuk 10 baris.");

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
}
