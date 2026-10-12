<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\UmumWorkPermit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkingPermitFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_load_a_working_permit_form_on_demand(): void
    {
        $user = User::factory()->create(['usertype' => 'user']);
        $notification = $this->createNotification($user);
        UmumWorkPermit::create(['notification_id' => $notification->id]);

        $this->actingAs($user)
            ->get(route('working-permit.form', [
                'notification' => $notification->id,
                'type' => 'umum',
            ]))
            ->assertOk()
            ->assertSee('lokasi_pekerjaan', false)
            ->assertSee((string) $notification->id);
    }

    public function test_user_cannot_load_another_users_working_permit_form(): void
    {
        $owner = User::factory()->create(['usertype' => 'user']);
        $otherUser = User::factory()->create(['usertype' => 'user']);
        $notification = $this->createNotification($owner);

        $this->actingAs($otherUser)
            ->get(route('working-permit.form', [
                'notification' => $notification->id,
                'type' => 'umum',
            ]))
            ->assertForbidden();
    }

    public function test_dashboard_does_not_render_permit_fields_before_modal_is_opened(): void
    {
        $user = User::factory()->create(['usertype' => 'user']);
        $notification = $this->createNotification($user);
        UmumWorkPermit::create(['notification_id' => $notification->id]);

        $this->actingAs($user)
            ->get(route('dashboard', ['notification_id' => $notification->id]))
            ->assertOk()
            ->assertSee('working-permit-form/umum', false)
            ->assertDontSee('izin_khusus[]', false);
    }

    private function createNotification(User $user): Notification
    {
        return Notification::create([
            'user_id' => $user->id,
            'type' => 'notif',
            'number' => 'TEST-'.uniqid(),
            'description' => 'Pekerjaan pengujian permit',
            'status' => 'menunggu',
        ]);
    }
}
