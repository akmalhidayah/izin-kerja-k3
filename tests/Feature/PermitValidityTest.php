<?php

namespace Tests\Feature;

use App\Models\AdminNotification;
use App\Models\Notification;
use App\Models\PermitArchive;
use App\Models\StepApproval;
use App\Models\UmumWorkPermit;
use App\Models\Upload;
use App\Models\User;
use App\Models\WorkPermitClosure;
use App\Models\WorkPermitDetail;
use App\Models\WorkPermitListrikTeganganTinggi;
use App\Services\PermitValidityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class PermitValidityTest extends TestCase
{
    use RefreshDatabase;

    private const VENDOR_STEPS_BEFORE_SIX = ['op_spk', 'data_kontraktor', 'bpjs', 'jsa', 'working_permit'];

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->freezeSecond();
        Storage::fake('public');
        Storage::fake('local');
    }

    // ===== helper =====

    private function user(string $usertype): User
    {
        return User::factory()->create(['usertype' => $usertype]);
    }

    private function submission(User $owner): Notification
    {
        return Notification::create([
            'user_id' => $owner->id,
            'type' => 'spk',
            'number' => 'SPK-TEST-' . Str::random(4),
            'description' => 'Pekerjaan uji',
            'status' => 'menunggu',
        ]);
    }

    private function approve(Notification $notification, array $steps): void
    {
        foreach ($steps as $step) {
            StepApproval::create(['notification_id' => $notification->id, 'step' => $step, 'status' => 'disetujui']);
        }
    }

    private function expire(Notification $notification): void
    {
        $notification->forceFill([
            'permit_valid_from' => now()->subDays(8),
            'permit_expires_at' => now()->subDay(),
        ])->save();
    }

    private function umumPermit(Notification $notification, array $attributes = []): UmumWorkPermit
    {
        return UmumWorkPermit::create(array_merge([
            'notification_id' => $notification->id,
            'permit_requestor_name' => 'Budi Requestor',
            'permit_requestor_sign' => 'storage/signatures/working-permit/umum/requestor.png',
            'permit_requestor_date' => '2026-11-01',
            'permit_requestor_time' => '08:00',
            'permit_issuer_name' => 'Sari Issuer',
            'permit_issuer_sign' => 'storage/signatures/working-permit/umum/issuer.png',
            'izin_berlaku_dari' => '2026-11-01',
            'izin_berlaku_sampai' => '2026-11-07',
            'rekomendasi_tambahan' => 'Gunakan APD lengkap',
            'isolasi_listrik' => [
                ['peralatan' => 'Panel A', 'nomor' => 'P-01', 'tempat' => 'Kiln', 'locked' => 'ya', 'tested' => 'ya', 'signature' => 'storage/signatures/iso.png'],
            ],
            'token' => (string) Str::uuid(),
            'token_expires_at' => now()->addDays(3),
        ], $attributes));
    }

    private function updateStatus(User $admin, Notification $notification, string $step, string $status)
    {
        return $this->actingAs($admin)->post(
            route('admin.permintaansik.updateStatus', [$notification->id, $step]),
            ['status' => $status]
        );
    }

    // ===== vendor: jam masa berlaku =====

    public function test_vendor_clock_starts_when_admin_approves_working_permit(): void
    {
        $vendor = $this->user('user');
        $admin = $this->user('admin');
        $notification = $this->submission($vendor);
        $this->umumPermit($notification);

        $this->assertNull($notification->fresh()->permit_expires_at, 'Vendor: permit belum disetujui tidak punya jam.');

        $this->updateStatus($admin, $notification, 'working_permit', 'disetujui')->assertSessionHas('success');

        $notification->refresh();
        $this->assertSame(now()->timestamp, $notification->permit_valid_from->timestamp);
        $this->assertSame(now()->addDays(7)->timestamp, $notification->permit_expires_at->timestamp);
    }

    public function test_saving_an_already_approved_status_does_not_extend_the_clock(): void
    {
        $admin = $this->user('admin');
        $notification = $this->submission($this->user('user'));
        $this->umumPermit($notification);

        $this->updateStatus($admin, $notification, 'working_permit', 'disetujui');
        $expiresAt = $notification->fresh()->permit_expires_at->timestamp;

        $this->travel(3)->days();
        $this->updateStatus($admin, $notification, 'working_permit', 'disetujui');

        $this->assertSame($expiresAt, $notification->fresh()->permit_expires_at->timestamp);
    }

    public function test_vendor_dashboard_locks_steps_after_working_permit_when_expired(): void
    {
        $vendor = $this->user('user');
        $notification = $this->submission($vendor);
        $this->umumPermit($notification);
        $this->approve($notification, [...self::VENDOR_STEPS_BEFORE_SIX, 'fakta_integritas', 'sertifikasi_ak3']);
        $this->expire($notification);

        $response = $this->actingAs($vendor)->get(route('dashboard'))->assertOk();

        $steps = collect($response->viewData('steps'))->keyBy('code');
        $gates = $response->viewData('stepGateStatuses');

        $this->assertSame(PermitValidityService::UI_EXPIRED, $steps['working_permit']['status']);
        $this->assertFalse($steps['fakta_integritas']['enabled']);
        $this->assertFalse($steps['ktp']['enabled']);
        $this->assertSame(PermitValidityService::UI_LOCKED, $gates['working_permit']);
        $this->assertSame(PermitValidityService::UI_LOCKED, $gates['sertifikasi_ak3']);
        $response->assertSee('Buat Permit Baru');

        // Progres tidak diubah: step yang sudah disetujui tetap disetujui.
        $this->assertDatabaseHas('step_approvals', [
            'notification_id' => $notification->id, 'step' => 'sertifikasi_ak3', 'status' => 'disetujui',
        ]);
    }

    public function test_legacy_or_active_permits_do_not_lock_anything(): void
    {
        $vendor = $this->user('user');
        $notification = $this->submission($vendor);
        $this->umumPermit($notification);
        $this->approve($notification, self::VENDOR_STEPS_BEFORE_SIX);
        // permit_expires_at NULL = pengajuan lama sebelum fitur aktif.

        $steps = collect($this->actingAs($vendor)->get(route('dashboard'))->assertOk()->viewData('steps'))->keyBy('code');

        $this->assertSame('done', $steps['working_permit']['status']);
        $this->assertTrue($steps['fakta_integritas']['enabled']);
    }

    public function test_finished_vendor_submission_never_expires(): void
    {
        $notification = $this->submission($this->user('user'));
        $this->approve($notification, ['working_permit', 'sik']);
        $this->expire($notification);

        $this->assertFalse(app(PermitValidityService::class)->state($notification)['expired']);
    }

    // ===== vendor: penegakan di server =====

    public function test_uploads_after_working_permit_are_blocked_when_expired_but_earlier_steps_are_not(): void
    {
        $vendor = $this->user('user');
        $notification = $this->submission($vendor);
        $this->approve($notification, self::VENDOR_STEPS_BEFORE_SIX);
        $this->expire($notification);

        $this->actingAs($vendor)->post(route('upload.store'), [
            'notification_id' => $notification->id,
            'step_name' => 'fakta_integritas',
            'file' => UploadedFile::fake()->create('fakta.pdf', 20, 'application/pdf'),
        ])->assertSessionHas('error');

        $this->assertDatabaseMissing('uploads', ['notification_id' => $notification->id, 'step' => 'fakta_integritas']);

        $this->actingAs($vendor)->post(route('upload.store'), [
            'notification_id' => $notification->id,
            'step_name' => 'bpjs',
            'file' => UploadedFile::fake()->create('bpjs.pdf', 20, 'application/pdf'),
        ])->assertSessionHas('success');

        $this->assertSame(1, Upload::where('notification_id', $notification->id)->where('step', 'bpjs')->count());
    }

    public function test_expired_permits_cannot_be_edited_until_a_new_permit_is_created(): void
    {
        $vendor = $this->user('user');
        $notification = $this->submission($vendor);
        $permit = $this->umumPermit($notification);
        $this->approve($notification, ['working_permit']);
        $this->expire($notification);

        $this->actingAs($vendor)
            ->post(route('working-permit.umum.store'), [
                'notification_id' => $notification->id,
                'permit_requestor_name' => 'Nama Diubah',
            ])
            ->assertSessionHas('error');

        $this->assertSame('Budi Requestor', $permit->fresh()->permit_requestor_name);

        // Link token untuk pihak penandatangan juga ditolak.
        $this->get(route('working-permit.umum.token', $permit->token))->assertForbidden();
        $this->post(route('working-permit.umum.token.store', $permit->token), [])->assertForbidden();
    }

    public function test_admin_cannot_approve_working_permit_or_later_steps_while_expired(): void
    {
        $admin = $this->user('admin');
        $notification = $this->submission($this->user('user'));
        $this->umumPermit($notification);
        $this->approve($notification, ['working_permit']);
        $this->expire($notification);

        $this->updateStatus($admin, $notification, 'sik', 'disetujui')->assertSessionHas('error');
        $this->assertDatabaseMissing('step_approvals', ['notification_id' => $notification->id, 'step' => 'sik']);

        $this->updateStatus($admin, $notification, 'fakta_integritas', 'disetujui')->assertSessionHas('error');
        $this->updateStatus($admin, $notification, 'working_permit', 'disetujui')->assertSessionHas('error');

        // Menandai revisi tetap boleh, dan tidak menghapus status kedaluwarsa.
        $this->updateStatus($admin, $notification, 'working_permit', 'revisi')->assertSessionHas('success');
        $this->assertTrue(app(PermitValidityService::class)->isExpired($notification->fresh()));
    }

    // ===== buat permit baru =====

    public function test_renew_archives_permits_clears_signatures_and_dates_and_requires_new_approval(): void
    {
        $vendor = $this->user('user');
        $admin = $this->user('admin');
        $notification = $this->submission($vendor);
        $notification->forceFill(['assigned_admin_id' => $admin->id])->save();

        $umum = $this->umumPermit($notification);
        $oldToken = $umum->token;
        $detail = WorkPermitDetail::create([
            'notification_id' => $notification->id,
            'permit_type' => 'umum',
            'location' => 'Kiln 2',
            'work_date' => '2026-11-01',
        ]);
        WorkPermitClosure::create([
            'work_permit_detail_id' => $detail->id,
            'requestor_name' => 'Budi Requestor',
            'requestor_sign' => 'storage/signatures/closure.png',
            'closed_date' => '2026-11-02',
        ]);
        $listrik = WorkPermitListrikTeganganTinggi::create([
            'notification_id' => $notification->id,
            'work_location' => 'Gardu Induk',
            'work_date' => '2026-11-01',
            'handover_issuer_name' => 'Andi',
            'handover_issuer_signature' => 'storage/signatures/listrik.png',
            'token' => (string) Str::uuid(),
            'token_expires_at' => now()->addDays(3),
        ]);
        $this->approve($notification, [...self::VENDOR_STEPS_BEFORE_SIX, 'fakta_integritas']);
        $this->expire($notification);

        $this->actingAs($vendor)
            ->post(route('working-permit.renew', $notification->id))
            ->assertSessionHas('success');

        // Arsip periode 1: satu baris per permit, berisi data lama.
        $archives = PermitArchive::where('notification_id', $notification->id)->get()->keyBy('permit_type');
        $this->assertEqualsCanonicalizing(['umum', 'listrik-tegangan-tinggi'], $archives->keys()->all());
        $this->assertSame(1, $archives['umum']->cycle);
        $this->assertSame('storage/signatures/working-permit/umum/requestor.png', $archives['umum']->snapshot['permit']['permit_requestor_sign']);
        $this->assertSame('Kiln 2', $archives['umum']->snapshot['detail']['location']);
        $this->assertNotNull($archives['listrik-tegangan-tinggi']->pdf_path);
        Storage::disk('local')->assertExists($archives['listrik-tegangan-tinggi']->pdf_path);

        // Permit aktif: nama & catatan tetap, tanda tangan + tanggal kosong, token diganti.
        $umum->refresh();
        $this->assertSame('Budi Requestor', $umum->permit_requestor_name);
        $this->assertSame('Gunakan APD lengkap', $umum->rekomendasi_tambahan);
        $this->assertNull($umum->permit_requestor_sign);
        $this->assertNull($umum->permit_issuer_sign);
        $this->assertNull($umum->permit_requestor_date);
        $this->assertNull($umum->izin_berlaku_dari);
        $this->assertSame('Kiln', $umum->isolasi_listrik[0]['tempat']);
        $this->assertSame('', $umum->isolasi_listrik[0]['signature']);
        $this->assertNotSame($oldToken, $umum->token);

        $detail->refresh();
        $this->assertSame('Kiln 2', $detail->location);
        $this->assertNull($detail->work_date);

        $closure = WorkPermitClosure::where('work_permit_detail_id', $detail->id)->first();
        $this->assertSame('Budi Requestor', $closure->requestor_name);
        $this->assertNull($closure->requestor_sign);
        $this->assertNull($closure->closed_date);

        $listrik->refresh();
        $this->assertSame('Andi', $listrik->handover_issuer_name);
        $this->assertNull($listrik->handover_issuer_signature);
        $this->assertNull($listrik->work_date);

        // Step 5 kembali menunggu, step lain tetap; periode naik; admin diberi tahu.
        $this->assertDatabaseHas('step_approvals', ['notification_id' => $notification->id, 'step' => 'working_permit', 'status' => 'menunggu']);
        $this->assertDatabaseHas('step_approvals', ['notification_id' => $notification->id, 'step' => 'fakta_integritas', 'status' => 'disetujui']);
        $notification->refresh();
        $this->assertSame(2, $notification->permit_cycle);
        $this->assertNull($notification->permit_expires_at);
        $this->assertTrue(AdminNotification::where('recipient_id', $admin->id)->where('notification_id', $notification->id)->exists());

        // Link token lama tidak berlaku lagi.
        $this->get(route('working-permit.umum.token', $oldToken))->assertNotFound();

        // Admin menyetujui permit baru -> jam baru, step 6 dan seterusnya terbuka lagi.
        $this->updateStatus($admin, $notification, 'working_permit', 'disetujui')->assertSessionHas('success');
        $this->assertSame(now()->addDays(7)->timestamp, $notification->fresh()->permit_expires_at->timestamp);

        $steps = collect($this->actingAs($vendor)->get(route('dashboard'))->assertOk()->viewData('steps'))->keyBy('code');
        $this->assertSame('done', $steps['working_permit']['status']);
        $this->assertTrue($steps['sertifikasi_ak3']['enabled']);
    }

    public function test_renew_is_rejected_for_other_users_and_when_not_expired(): void
    {
        $vendor = $this->user('user');
        $notification = $this->submission($vendor);
        $this->umumPermit($notification);
        $this->approve($notification, ['working_permit']);
        $notification->forceFill(['permit_expires_at' => now()->addDays(2)])->save();

        $this->actingAs($this->user('user'))
            ->post(route('working-permit.renew', $notification->id))
            ->assertForbidden();

        $this->actingAs($vendor)
            ->post(route('working-permit.renew', $notification->id))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('permit_archives', 0);
    }

    public function test_renew_still_unlocks_when_admin_already_deleted_all_permits(): void
    {
        $vendor = $this->user('user');
        $notification = $this->submission($vendor);
        $this->approve($notification, ['working_permit']);
        $this->expire($notification);

        $this->actingAs($vendor)
            ->post(route('working-permit.renew', $notification->id))
            ->assertSessionHas('success');

        $this->assertDatabaseCount('permit_archives', 0);
        $this->assertFalse(app(PermitValidityService::class)->isExpired($notification->fresh()));
        $this->assertSame(2, $notification->fresh()->permit_cycle);
    }

    // ===== PGO =====

    public function test_pgo_permit_expires_when_not_approved_within_the_period(): void
    {
        $pgo = $this->user('pgo');
        $admin = $this->user('admin');
        $notification = $this->submission($pgo);

        $this->umumPermit($notification);
        $this->assertSame(now()->addDays(7)->timestamp, $notification->fresh()->permit_expires_at->timestamp, 'PGO: jam mulai saat permit dibuat.');

        $this->travel(8)->days();

        $steps = collect($this->actingAs($pgo)->get(route('dashboard'))->assertOk()->viewData('steps'))->keyBy('code');
        $this->assertSame(PermitValidityService::UI_EXPIRED, $steps['working_permit']['status']);

        $this->updateStatus($admin, $notification, 'working_permit', 'disetujui')->assertSessionHas('error');

        $this->actingAs($pgo)->post(route('working-permit.renew', $notification->id))->assertSessionHas('success');

        $notification->refresh();
        $this->assertSame(2, $notification->permit_cycle);
        $this->assertSame(now()->addDays(7)->timestamp, $notification->permit_expires_at->timestamp, 'PGO: periode baru punya jam baru.');
    }

    public function test_pgo_approval_within_the_period_finishes_the_permit(): void
    {
        $pgo = $this->user('pgo');
        $admin = $this->user('admin');
        $notification = $this->submission($pgo);
        $this->umumPermit($notification);

        $this->travel(3)->days();
        $this->updateStatus($admin, $notification, 'working_permit', 'disetujui')->assertSessionHas('success');

        $this->assertNull($notification->fresh()->permit_expires_at);

        $this->travel(30)->days();
        $this->assertFalse(app(PermitValidityService::class)->isExpired($notification->fresh()));
    }

    // ===== arsip di admin =====

    public function test_admin_can_view_archived_pdf_but_vendor_cannot(): void
    {
        $admin = $this->user('admin');
        $vendor = $this->user('user');
        $notification = $this->submission($vendor);
        $other = $this->submission($vendor);

        Storage::disk('local')->put('permit-archives/test/umum.pdf', '%PDF-1.4 arsip');
        $archive = PermitArchive::create([
            'notification_id' => $notification->id,
            'cycle' => 1,
            'permit_type' => 'umum',
            'valid_from' => now()->subDays(8),
            'expires_at' => now()->subDay(),
            'snapshot' => ['permit' => ['permit_requestor_name' => 'Budi']],
            'pdf_path' => 'permit-archives/test/umum.pdf',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.permintaansik.permitArchivePdf', [$notification->id, $archive->id]))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');

        $this->actingAs($admin)
            ->get(route('admin.permintaansik.permitArchivePdf', [$other->id, $archive->id]))
            ->assertNotFound();

        $this->actingAs($vendor)
            ->get(route('admin.permintaansik.permitArchivePdf', [$notification->id, $archive->id]))
            ->assertForbidden();

        $this->actingAs($admin)
            ->get(route('admin.permintaansik.show', $notification->id))
            ->assertOk()
            ->assertSee('Arsip (1)')
            ->assertSee('Periode permit');
    }

    public function test_deleting_a_submission_removes_archive_files(): void
    {
        $notification = $this->submission($this->user('user'));
        Storage::disk('local')->put('permit-archives/hapus.pdf', '%PDF-1.4');
        PermitArchive::create([
            'notification_id' => $notification->id,
            'cycle' => 1,
            'permit_type' => 'umum',
            'snapshot' => ['permit' => []],
            'pdf_path' => 'permit-archives/hapus.pdf',
        ]);

        $notification->delete();

        Storage::disk('local')->assertMissing('permit-archives/hapus.pdf');
        $this->assertDatabaseCount('permit_archives', 0);
    }
}
