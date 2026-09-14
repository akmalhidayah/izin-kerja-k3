<?php

namespace Tests\Feature;

use App\Models\DataKontraktor;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class DataKontraktorTokenFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_token_form_renders_rows_cast_to_arrays(): void
    {
        $dataKontraktor = $this->createDataKontraktor();

        $response = $this->get(route('izin-kerja.data-kontraktor.token', $dataKontraktor->token));

        $response->assertOk();
        $response->assertSee('Form Data Kontraktor');
        $response->assertSee('Welder');
        $response->assertSee('Mesin Las');
        $response->assertSee('Helm');
    }

    public function test_token_form_can_render_legacy_double_encoded_rows(): void
    {
        $dataKontraktor = $this->createDataKontraktor();

        DB::table('data_kontraktors')
            ->where('id', $dataKontraktor->id)
            ->update([
                'tenaga_kerja' => json_encode(json_encode([['nama' => 'Legacy Worker', 'jumlah' => 1, 'satuan' => 'orang']])),
            ]);

        $response = $this->get(route('izin-kerja.data-kontraktor.token', $dataKontraktor->token));

        $response->assertOk();
        $response->assertSee('Legacy Worker');
    }

    public function test_token_form_stores_rows_as_json_arrays(): void
    {
        $dataKontraktor = $this->createDataKontraktor();
        $rows = [['nama' => 'Supervisor', 'jumlah' => 2, 'satuan' => 'orang']];

        $response = $this->post(route('izin-kerja.data-kontraktor.store', $dataKontraktor->token), [
            'nama_perusahaan' => 'PT Pengujian',
            'tenaga_kerja' => json_encode($rows),
            'peralatan_kerja' => '[]',
            'apd' => '[]',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $storedRows = DB::table('data_kontraktors')
            ->where('id', $dataKontraktor->id)
            ->value('tenaga_kerja');

        $this->assertSame($rows, json_decode($storedRows, true));
    }

    private function createDataKontraktor(): DataKontraktor
    {
        $user = User::factory()->create();
        $notification = Notification::create([
            'user_id' => $user->id,
            'type' => 'notif',
            'number' => 'TEST-'.uniqid(),
            'description' => 'Pekerjaan pengujian',
            'status' => 'menunggu',
        ]);

        $dataKontraktor = DataKontraktor::create([
            'notification_id' => $notification->id,
            'nama_perusahaan' => 'PT Pengujian',
            'jenis_pekerjaan' => 'Pengelasan',
            'tenaga_kerja' => [['nama' => 'Welder', 'jumlah' => 1, 'satuan' => 'orang']],
            'peralatan_kerja' => [['nama' => 'Mesin Las', 'jumlah' => 1, 'satuan' => 'unit']],
            'apd' => [['nama' => 'Helm', 'jumlah' => 1, 'satuan' => 'buah']],
            'token' => (string) Str::uuid(),
        ]);

        $dataKontraktor->forceFill(['token_expires_at' => now()->addDay()])->save();

        return $dataKontraktor;
    }
}
