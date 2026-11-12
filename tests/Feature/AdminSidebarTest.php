<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\Role;
use App\Models\StepApproval;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSidebarTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function adminWithRole(string $role): User
    {
        $roleModel = Role::firstOrCreate(['name' => $role]);

        return User::factory()->create(['usertype' => 'admin', 'role_id' => $roleModel->id]);
    }

    private function notification(User $owner, array $approvals = [], ?int $assignedAdminId = null): Notification
    {
        $notification = Notification::create([
            'user_id' => $owner->id,
            'assigned_admin_id' => $assignedAdminId,
            'type' => 'notif',
            'number' => 'SB-'.uniqid(),
            'description' => 'Uji sidebar',
            'status' => 'menunggu',
        ]);

        foreach ($approvals as $step => $status) {
            StepApproval::create(['notification_id' => $notification->id, 'step' => $step, 'status' => $status]);
        }

        return $notification;
    }

    private function seedRequests(): void
    {
        $vendor = User::factory()->create(['usertype' => 'user']);
        $pgo = User::factory()->create(['usertype' => 'pgo']);

        $handler = $this->adminWithRole('Admin');

        // 2 vendor perlu tindakan, 1 vendor revisi yang sudah ditangani (tidak dihitung), 1 vendor SIK disetujui tanpa TTD.
        $this->notification($vendor);
        $this->notification($vendor, ['op_spk' => 'disetujui']);
        $this->notification($vendor, ['op_spk' => 'revisi'], $handler->id);
        $this->notification($vendor, ['sik' => 'disetujui']);

        // 1 karyawan perlu tindakan.
        $this->notification($pgo);
    }

    public function test_admin_sees_perlu_tindakan_badges_but_not_approve_sik(): void
    {
        $this->seedRequests();
        $admin = $this->adminWithRole('Admin');

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('data-sidebar-badge="vendor" data-count="2"', false);
        $response->assertSee('data-sidebar-badge="pgo" data-count="1"', false);
        $response->assertDontSee('data-sidebar-badge="sik"', false);
        $response->assertDontSee('Approve SIK');
        $response->assertSee('Pengajuan');
        $response->assertDontSee('Pengaturan');
    }

    public function test_super_admin_sees_sik_signature_badge_and_settings_group(): void
    {
        $this->seedRequests();
        $super = $this->adminWithRole('Super Admin');

        $response = $this->actingAs($super)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('data-sidebar-badge="sik" data-count="1"', false);
        $response->assertSee('Approve SIK');
        $response->assertSee('Pengaturan');
        $response->assertSee('Upload Berita');
    }

    public function test_no_badge_when_nothing_needs_action(): void
    {
        $admin = $this->adminWithRole('Admin');

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertDontSee('data-sidebar-badge=', false);
    }

    public function test_active_menu_is_marked(): void
    {
        $admin = $this->adminWithRole('Admin');

        $content = $this->actingAs($admin)->get(route('admin.permintaansikpgo'))->getContent();

        $this->assertMatchesRegularExpression('/permintaansikpgo"[^>]*class="[^"]*nav-active/s', $content);
        $this->assertSame(1, substr_count($content, 'nav-active -mr-3'));
    }
}
