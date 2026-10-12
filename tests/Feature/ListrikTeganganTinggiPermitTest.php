<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\UmumWorkPermit;
use App\Models\User;
use App\Models\WorkPermitListrikTeganganTinggi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListrikTeganganTinggiPermitTest extends TestCase
{
    use RefreshDatabase;

    public function test_permit_umum_is_required_before_creating_high_voltage_permit(): void
    {
        [$user, $notification] = $this->createSubmission();

        $response = $this->actingAs($user)->post(
            route('working-permit.listrik-tegangan-tinggi.store'),
            [
                'notification_id' => $notification->id,
                'work_location' => 'Substation A',
            ]
        );

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('work_permit_listrik_tegangan_tinggi', [
            'notification_id' => $notification->id,
        ]);
    }

    public function test_owner_can_store_high_voltage_permit_after_general_permit_exists(): void
    {
        [$user, $notification] = $this->createSubmission();
        UmumWorkPermit::create(['notification_id' => $notification->id]);

        $response = $this->actingAs($user)->post(
            route('working-permit.listrik-tegangan-tinggi.store'),
            [
                'notification_id' => $notification->id,
                'work_location' => 'Substation A',
                'documentation_checks' => [
                    'method_statement' => 'ya',
                    'high_hazard_checklist' => 'na',
                ],
                'safety_requirements' => [
                    'electrical_worker_certificate' => [
                        'needed' => 'ya',
                        'fulfilled' => 'ya',
                    ],
                ],
                'additional_hazards' => [
                    ['hazard' => 'Arc flash', 'control' => 'Arc-rated PPE', 'pic' => 'Supervisor'],
                    ['hazard' => 'Sengatan listrik', 'control' => 'Isolasi energi', 'pic' => 'Teknisi'],
                ],
                'issuer_confirmations' => [
                    'area_and_equipment_checked' => 'ya',
                ],
            ]
        );

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $permit = WorkPermitListrikTeganganTinggi::where('notification_id', $notification->id)->firstOrFail();
        $this->assertNull($permit->permit_number);
        $this->assertSame('ya', $permit->documentation_checks['method_statement']);
        $this->assertSame('Arc flash', $permit->additional_hazards[0]['hazard']);
        $this->assertSame('Sengatan listrik', $permit->additional_hazards[1]['hazard']);
        $this->assertCount(2, $permit->additional_hazards);
        $this->assertNotNull($permit->token);
        $this->assertNotNull($permit->token_expires_at);
    }

    public function test_token_form_can_update_and_render_pdf(): void
    {
        [, $notification] = $this->createSubmission();
        UmumWorkPermit::create(['notification_id' => $notification->id]);
        $permit = WorkPermitListrikTeganganTinggi::create([
            'notification_id' => $notification->id,
            'work_location' => 'Substation Lama',
            'token' => (string) \Illuminate\Support\Str::uuid(),
            'token_expires_at' => now()->addDay(),
        ]);

        $this->get(route('working-permit.listrik-tegangan-tinggi.token', $permit->token))
            ->assertOk()
            ->assertSee('Izin Bekerja pada Listrik Tegangan Tinggi')
            ->assertSee('+ Tambah Bahaya')
            ->assertDontSee('Nomor Permit');

        $this->post(route('working-permit.listrik-tegangan-tinggi.token.store', $permit->token), [
            'work_location' => 'Substation Baru',
            'receiver_confirmation' => 'ya',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertDatabaseHas('work_permit_listrik_tegangan_tinggi', [
            'id' => $permit->id,
            'work_location' => 'Substation Baru',
            'receiver_confirmation' => 'ya',
        ]);

        $pdfResponse = $this->get(route('token-pdf.show', [
            'type' => 'listrik-tegangan-tinggi',
            'token' => $permit->token,
        ]));

        $pdfResponse->assertOk();
        $pdfResponse->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-', $pdfResponse->getContent());
    }

    private function createSubmission(): array
    {
        $user = User::factory()->create(['usertype' => 'user']);
        $notification = Notification::create([
            'user_id' => $user->id,
            'type' => 'notif',
            'number' => 'TEST-'.uniqid(),
            'description' => 'Pekerjaan listrik tegangan tinggi',
            'status' => 'menunggu',
        ]);

        return [$user, $notification];
    }
}
