<?php

namespace Tests\Feature;

use App\Models\Jsa;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class JsaSignatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_token_form_stores_a_complete_png_signature(): void
    {
        Storage::fake('public');
        $jsa = $this->createJsa();

        $response = $this->post(route('jsa.form.token.store', $jsa->token), [
            'disetujui_signature' => $this->validPngDataUrl(),
        ]);

        $response->assertSessionHasNoErrors();
        $jsa->refresh();

        $this->assertNotNull($jsa->disetujui_signature);
        $this->assertStringStartsWith('storage/signatures/jsa/disetujui_', $jsa->disetujui_signature);

        $relativePath = substr($jsa->disetujui_signature, strlen('storage/'));
        Storage::disk('public')->assertExists($relativePath);

        $image = @imagecreatefromstring(Storage::disk('public')->get($relativePath));
        $this->assertNotFalse($image);
        imagedestroy($image);
    }

    public function test_token_form_rejects_a_truncated_png_signature(): void
    {
        Storage::fake('public');
        $jsa = $this->createJsa();
        $validPng = base64_decode(substr($this->validPngDataUrl(), strlen('data:image/png;base64,')), true);
        $truncatedPng = substr($validPng, 0, 45);

        $response = $this->from(route('jsa.form.token', $jsa->token))
            ->post(route('jsa.form.token.store', $jsa->token), [
                'disetujui_signature' => 'data:image/png;base64,'.base64_encode($truncatedPng),
            ]);

        $response->assertRedirect(route('jsa.form.token', $jsa->token));
        $response->assertSessionHasErrors('disetujui_signature');
        $this->assertNull($jsa->fresh()->disetujui_signature);
        $this->assertSame([], Storage::disk('public')->allFiles('signatures/jsa'));
    }

    public function test_token_form_detects_an_existing_truncated_signature(): void
    {
        Storage::fake('public');
        $jsa = $this->createJsa();
        $relativePath = 'signatures/jsa/disetujui_corrupt.png';
        $validPng = base64_decode(substr($this->validPngDataUrl(), strlen('data:image/png;base64,')), true);

        Storage::disk('public')->put($relativePath, substr($validPng, 0, 45));
        $jsa->update(['disetujui_signature' => 'storage/'.$relativePath]);

        $response = $this->from(route('jsa.form.token', $jsa->token))
            ->post(route('jsa.form.token.store', $jsa->token));

        $response->assertRedirect(route('jsa.form.token', $jsa->token));
        $response->assertSessionHasErrors('disetujui_signature');
        $this->assertSame('storage/'.$relativePath, $jsa->fresh()->disetujui_signature);
    }

    private function createJsa(): Jsa
    {
        $user = User::factory()->create();
        $notification = Notification::create([
            'user_id' => $user->id,
            'type' => 'notif',
            'number' => 'TEST-'.uniqid(),
            'description' => 'Notifikasi test',
            'status' => 'menunggu',
        ]);

        $jsa = Jsa::create([
            'notification_id' => $notification->id,
            'nama_perusahaan' => 'PT Test',
            'no_jsa' => '001/JSA/ST/'.now()->format('mY'),
            'nama_jsa' => 'Pekerjaan Test',
            'departemen' => 'K3',
            'area_kerja' => 'Area Test',
            'tanggal' => now()->toDateString(),
            'dibuat_nama' => 'Pembuat',
            'disetujui_nama' => 'Permit Issuer',
            'diverifikasi_nama' => 'Verifikator',
            'langkah_kerja' => [],
            'token' => (string) Str::uuid(),
        ]);

        $jsa->forceFill(['token_expires_at' => now()->addDay()])->save();

        return $jsa;
    }

    private function validPngDataUrl(): string
    {
        return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=';
    }
}
