<?php

namespace Tests\Feature;

use App\Models\DataKontraktor;
use App\Models\Jsa;
use App\Models\Notification;
use App\Models\Role;
use App\Models\StepApproval;
use App\Models\StepApprovalLog;
use App\Models\UmumWorkPermit;
use App\Models\User;
use App\Models\WorkPermitDetail;
use App\Models\WorkPermitKetinggian;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
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
        // Badge sidebar di-cache 60 detik; kosongkan supaya setiap pengukuran setara.
        Cache::flush();
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

        $make = function (int $n) use ($vendor) {
            for ($i = 0; $i < $n; $i++) {
                $this->notification($vendor, ['op_spk' => 'disetujui', 'jsa' => $i % 3 === 0 ? 'revisi' : 'menunggu']);
            }
        };

        $make(5);
        $small = $this->countQueries(fn () => $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk());

        $make(35);
        $large = $this->countQueries(fn () => $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk());

        $this->assertSame($small, $large, "Query dashboard bertambah dari {$small} menjadi {$large} saat pengajuan bertambah.");
        $this->assertLessThan(80, $large, "Dashboard menjalankan {$large} query.");
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
        $this->assertLessThan(28, $queries, "Daftar vendor menjalankan {$queries} query untuk 10 baris.");

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
        $this->assertSame(['', 'perlu_tindakan', 'revisi', 'riwayat'], $tabs->keys()->all());
        $this->assertSame(3, $tabs['']['count']);
        $this->assertSame(3, $tabs['perlu_tindakan']['count']);
        $this->assertTrue($tabs['perlu_tindakan']['active'], 'Mandek disorot di tab Perlu Tindakan');
        $this->assertFalse($tabs['']['active']);

        $sub = collect($list->viewData('kondisiSub'))->keyBy('key');
        $this->assertSame(1, $sub['mandek']['count']);
        $this->assertTrue($sub['mandek']['active']);
        $this->assertSame(3, $sub['perlu_tindakan']['count']);
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

        // tab baru: perlu_tindakan = menunggu + belum ditangani, riwayat = selesai
        $this->assertSame([$unassigned->id], $ids('admin.permintaansik', 'perlu_tindakan'));
        $this->assertSame([$done->id], $ids('admin.permintaansik', 'riwayat'));
        $this->assertSame([$pgoOpen->id], $ids('admin.permintaansikpgo', 'perlu_tindakan'));
        $this->assertSame([$pgoDone->id], $ids('admin.permintaansikpgo', 'riwayat'));

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

    public function test_four_tabs_with_highlight_and_sub_filters(): void
    {
        $admin = $this->admin();
        $vendor = User::factory()->create(['usertype' => 'user']);

        $this->notification($vendor);
        $this->notification($vendor, ['op_spk' => 'revisi'], ['assigned_admin_id' => $admin->id]);
        $this->notification($vendor, ['sik' => 'disetujui'], ['assigned_admin_id' => $admin->id]);

        $tabsFor = fn (?string $kondisi) => collect(
            $this->actingAs($admin)->get(route('admin.permintaansik', $kondisi ? ['kondisi' => $kondisi] : []))
                ->viewData('kondisiTabs')
        )->keyBy('key');

        $this->assertTrue($tabsFor(null)['']['active']);
        $this->assertTrue($tabsFor('perlu_tindakan')['perlu_tindakan']['active']);
        $this->assertTrue($tabsFor('menunggu')['perlu_tindakan']['active']);
        $this->assertTrue($tabsFor('belum_ditangani')['perlu_tindakan']['active']);
        $this->assertTrue($tabsFor('revisi')['revisi']['active']);
        $this->assertTrue($tabsFor('riwayat')['riwayat']['active']);
        $this->assertTrue($tabsFor('selesai')['riwayat']['active']);

        $this->assertSame(3, $tabsFor(null)['']['count']);
        $this->assertSame(1, $tabsFor(null)['perlu_tindakan']['count']);
        $this->assertSame(1, $tabsFor(null)['revisi']['count']);
        $this->assertSame(1, $tabsFor(null)['riwayat']['count']);

        // penyaring kecil hanya muncul di tab Perlu Tindakan
        $this->assertSame([], $this->actingAs($admin)->get(route('admin.permintaansik', ['kondisi' => 'revisi']))->viewData('kondisiSub'));
        $this->assertCount(3, $this->actingAs($admin)->get(route('admin.permintaansik', ['kondisi' => 'perlu_tindakan']))->viewData('kondisiSub'));

        $html = $this->actingAs($admin)->get(route('admin.permintaansik', ['kondisi' => 'perlu_tindakan']))->getContent();
        $this->assertStringContainsString('Perlu Tindakan', $html);
        $this->assertStringContainsString('Riwayat', $html);
        $this->assertStringContainsString('Belum ditangani', $html);
        $this->assertStringContainsString('Mandek', $html);
    }

    public function test_permit_type_counts_for_selected_year(): void
    {
        $admin = $this->admin();
        $vendor = User::factory()->create(['usertype' => 'user']);

        $a = $this->notification($vendor);
        $b = $this->notification($vendor);
        UmumWorkPermit::create(['notification_id' => $a->id]);
        UmumWorkPermit::create(['notification_id' => $b->id]);
        WorkPermitKetinggian::create(['notification_id' => $a->id]);

        $old = WorkPermitKetinggian::create(['notification_id' => $b->id]);
        $old->forceFill(['created_at' => now()->subYear()])->save();

        $types = $this->actingAs($admin)->get(route('admin.dashboard'))->viewData('permitTypes');
        $rows = collect($types['rows'])->keyBy('key');

        $this->assertSame(3, $types['total']);
        $this->assertSame(2, $rows['umum']['count']);
        $this->assertSame(1, $rows['ketinggian']['count']);
        $this->assertTrue($rows['ketinggian']['risk']);
        $this->assertSame(1, $types['risk_total']);
        $this->assertSame('umum', $types['rows'][0]['key'], 'Urut dari yang terbanyak');
    }

    public function test_ongoing_today_reads_contractor_range_permit_date_and_jsa(): void
    {
        $admin = $this->admin();
        $vendor = User::factory()->create(['usertype' => 'user']);
        $pgo = User::factory()->create(['usertype' => 'pgo', 'name' => 'Karyawan Lapangan']);
        $pgoJsa = User::factory()->create(['usertype' => 'pgo', 'name' => 'Karyawan JSA']);

        // Vendor: hari ini di dalam rentang, tenaga kerja format lama (ter-encode dua kali).
        $running = $this->notification($vendor);
        DataKontraktor::create([
            'notification_id' => $running->id,
            'nama_perusahaan' => 'PT Sedang Kerja',
            'lokasi_kerja' => 'Kiln 4',
            'tanggal_mulai' => now()->subDays(2)->toDateString(),
            'tanggal_selesai' => now()->addDays(3)->toDateString(),
            'tenaga_kerja' => json_encode([['nama' => 'Welder', 'jumlah' => '4'], ['nama' => 'Helper', 'jumlah' => 6]]),
        ]);
        WorkPermitKetinggian::create(['notification_id' => $running->id]);

        // Vendor: sudah lewat, tidak ikut.
        $finished = $this->notification($vendor);
        DataKontraktor::create([
            'notification_id' => $finished->id,
            'nama_perusahaan' => 'PT Sudah Selesai',
            'tanggal_mulai' => now()->subDays(10)->toDateString(),
            'tanggal_selesai' => now()->subDay()->toDateString(),
        ]);

        // Karyawan: tanggal kerja di detail permit hari ini.
        $pgoNotif = $this->notification($pgo);
        WorkPermitDetail::create([
            'notification_id' => $pgoNotif->id,
            'permit_type' => 'umum',
            'location' => 'Packer 3',
            'work_date' => now()->toDateString(),
            'worker_count' => 5,
        ]);

        // Karyawan: tanpa tanggal kerja, JSA hari ini.
        $jsaNotif = $this->notification($pgoJsa);
        $this->jsa($jsaNotif)->update(['tanggal' => now()->toDateString(), 'area_kerja' => 'Gudang Klinker']);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        $ongoing = $response->viewData('ongoing');
        $vendorSet = $ongoing['vendor'];
        $pgoSet = $ongoing['pgo'];
        $vendorRows = $vendorSet['rows']->keyBy('notification_id');
        $pgoRows = $pgoSet['rows']->keyBy('notification_id');

        // Vendor: hanya yang masih dalam rentang tanggal.
        $this->assertSame(1, $vendorSet['total']);
        $this->assertSame(10, $vendorSet['workers']);
        $this->assertSame(1, $vendorSet['risk']);
        $this->assertArrayNotHasKey($finished->id, $vendorRows->all());
        $this->assertSame('PT Sedang Kerja', $vendorRows[$running->id]->name);
        $this->assertSame(10, $vendorRows[$running->id]->workers);
        $this->assertSame('Kiln 4', $vendorRows[$running->id]->location);
        $this->assertTrue($vendorRows[$running->id]->risk);
        $this->assertSame('Ketinggian', $vendorRows[$running->id]->types[0]['label']);

        // Karyawan: tanggal kerja permit atau tanggal JSA.
        $this->assertSame(2, $pgoSet['total']);
        $this->assertSame(5, $pgoSet['workers']);
        $this->assertSame(0, $pgoSet['risk']);
        $this->assertSame('Karyawan Lapangan', $pgoRows[$pgoNotif->id]->name);
        $this->assertSame(5, $pgoRows[$pgoNotif->id]->workers);
        $this->assertSame('Gudang Klinker', $pgoRows[$jsaNotif->id]->location);
        $this->assertNull($pgoRows[$jsaNotif->id]->workers);
        $this->assertSame($pgoNotif->id, $pgoSet['rows'][0]->notification_id, 'Yang punya jumlah pekerja lebih banyak di atas');

        // Dropdown default Vendor: set karyawan tersembunyi saat halaman dibuka.
        $html = $response->getContent();
        $this->assertStringContainsString("x-data=\"{ who: 'vendor' }\"", $html);
        $this->assertMatchesRegularExpression('/data-ongoing-set="pgo"/', $html);
        $this->assertMatchesRegularExpression('/x-show="who === \'pgo\'"\s+x-cloak/', $html);
    }
}
