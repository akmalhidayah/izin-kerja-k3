<?php

namespace Tests\Feature;

use App\Models\Jsa;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class JsaNumberingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::create(2026, 8, 20, 12, 0, 0));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_it_starts_at_one_when_the_current_month_has_no_jsa(): void
    {
        $this->assertSame(
            '001/JSA/ST/082026',
            app(\App\Http\Controllers\User\JsaController::class)->getGeneratedNoJsa()
        );
    }

    public function test_it_uses_the_numeric_max_instead_of_created_at(): void
    {
        $this->createJsa('069/JSA/ST/082026', Carbon::create(2026, 8, 20, 8, 43, 0));
        $this->createJsa('068/JSA/ST/082026', Carbon::create(2026, 8, 20, 9, 30, 0));

        $this->assertSame(
            '070/JSA/ST/082026',
            app(\App\Http\Controllers\User\JsaController::class)->getGeneratedNoJsa()
        );
    }

    public function test_store_uses_the_same_numbering_algorithm_as_the_preview(): void
    {
        $this->createJsa('069/JSA/ST/082026', Carbon::create(2026, 8, 20, 8, 43, 0));
        $this->createJsa('068/JSA/ST/082026', Carbon::create(2026, 8, 20, 9, 30, 0));

        $user = User::factory()->create(['usertype' => User::USERTYPE_USER]);
        $notification = $this->createNotification($user);

        $response = $this->actingAs($user)->post(route('jsa.store'), [
            'notification_id' => $notification->id,
            'nama_perusahaan' => 'PT Test',
            'no_jsa' => '069/JSA/ST/082026',
            'nama_jsa' => 'Pekerjaan Test',
            'departemen' => 'K3',
            'area_kerja' => 'Area Test',
            'tanggal' => '2026-08-20',
            'dibuat_nama' => 'Pembuat',
            'disetujui_nama' => 'Penyetuju',
            'diverifikasi_nama' => 'Verifikator',
            'langkah_kerja' => '[]',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('jsas', [
            'notification_id' => $notification->id,
            'no_jsa' => '070/JSA/ST/082026',
        ]);

        $this->assertSame(
            '071/JSA/ST/082026',
            app(\App\Http\Controllers\User\JsaController::class)->getGeneratedNoJsa()
        );
    }

    public function test_it_uses_max_plus_one_and_does_not_fill_sequence_gaps(): void
    {
        foreach ([65, 67, 69] as $number) {
            $this->createJsa(sprintf('%03d/JSA/ST/082026', $number));
        }

        $this->assertSame(
            '070/JSA/ST/082026',
            app(\App\Http\Controllers\User\JsaController::class)->getGeneratedNoJsa()
        );
    }

    public function test_it_only_considers_the_current_month(): void
    {
        $this->createJsa('150/JSA/ST/072026');
        $this->createJsa('069/JSA/ST/082026');

        $this->assertSame(
            '070/JSA/ST/082026',
            app(\App\Http\Controllers\User\JsaController::class)->getGeneratedNoJsa()
        );
    }

    public function test_it_supports_numbers_larger_than_three_digits(): void
    {
        $this->createJsa('999/JSA/ST/082026');

        $this->assertSame(
            '1000/JSA/ST/082026',
            app(\App\Http\Controllers\User\JsaController::class)->getGeneratedNoJsa()
        );
    }

    public function test_malformed_numbers_are_ignored(): void
    {
        $this->createJsa(null);
        $this->createJsa('');
        $this->createJsa('legacy/JSA/ST/082026');
        $this->createJsa('09x/JSA/ST/082026');
        $this->createJsa('009/JSA/ST/082026');

        $this->assertSame(
            '010/JSA/ST/082026',
            app(\App\Http\Controllers\User\JsaController::class)->getGeneratedNoJsa()
        );
    }

    public function test_the_global_no_jsa_unique_constraint_remains_present(): void
    {
        $indexes = Schema::getIndexes('jsas');

        $this->assertTrue(collect($indexes)->contains(
            fn (array $index): bool => $index['name'] === 'jsas_no_jsa_unique'
        ));
    }

    private function createJsa(?string $noJsa, ?Carbon $createdAt = null): Jsa
    {
        $user = User::factory()->create();
        $notification = $this->createNotification($user);

        $jsa = Jsa::create([
            'notification_id' => $notification->id,
            'nama_perusahaan' => 'PT Test',
            'no_jsa' => $noJsa,
            'nama_jsa' => 'Pekerjaan Test',
            'departemen' => 'K3',
            'area_kerja' => 'Area Test',
            'tanggal' => '2026-08-20',
            'dibuat_nama' => 'Pembuat',
            'disetujui_nama' => 'Penyetuju',
            'langkah_kerja' => [],
        ]);

        if ($createdAt) {
            $jsa->forceFill([
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ])->saveQuietly();
        }

        return $jsa;
    }

    private function createNotification(User $user): Notification
    {
        return Notification::create([
            'user_id' => $user->id,
            'type' => 'notif',
            'number' => 'TEST-'.$user->id.'-'.uniqid(),
            'description' => 'Notifikasi test',
            'status' => 'menunggu',
        ]);
    }
}
