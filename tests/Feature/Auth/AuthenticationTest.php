<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $this->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_admin_is_redirected_to_admin_dashboard_on_login_and_when_remembered(): void
    {
        $admin = User::factory()->create(['usertype' => User::USERTYPE_ADMIN]);
        $recallerName = Auth::guard('web')->getRecallerName();

        $login = $this->withSession(['url.intended' => route('dashboard')])->post('/login', [
            'email' => $admin->email,
            'password' => 'password',
            'remember' => 'on',
        ])->assertRedirectToRoute('admin.dashboard');

        $rememberCookie = $login->getCookie($recallerName, decrypt: false);
        $this->assertNotNull($rememberCookie);
        $this->app['session']->driver()->flush();
        Auth::forgetGuards();

        $this->withUnencryptedCookie($recallerName, $rememberCookie->getValue())
            ->get('/login')
            ->assertRedirectToRoute('admin.dashboard');

        $this->assertAuthenticatedAs($admin);
        $this->assertTrue(Auth::guard('web')->viaRemember());
    }

    public function test_signed_in_pgo_opening_login_is_redirected_to_user_dashboard(): void
    {
        $pgo = User::factory()->create(['usertype' => User::USERTYPE_PGO]);

        $this->actingAs($pgo)->get('/login')->assertRedirectToRoute('dashboard');
    }

    public function test_an_expired_form_redirects_a_signed_in_admin_to_admin_dashboard(): void
    {
        // Jalankan validasi CSRF yang biasanya dilewati Laravel saat testing.
        $this->app->instance('env', 'local');
        $this->withoutVite();
        $admin = User::factory()->create(['usertype' => User::USERTYPE_ADMIN]);

        $this->actingAs($admin)->withSession(['_token' => 'current'])->post('/logout', ['_token' => 'expired'])
            ->assertRedirectToRoute('admin.dashboard');

        $this->assertAuthenticatedAs($admin);
        $this->get(route('admin.dashboard'))->assertOk()->assertSee('Perubahan belum disimpan.');
    }

    public function test_an_expired_login_form_redirects_a_guest_to_a_fresh_login(): void
    {
        $this->app->instance('env', 'local');

        $this->withSession(['_token' => 'current'])->post('/login', ['_token' => 'expired'])
            ->assertRedirectToRoute('login');

        $this->assertGuest();
        $this->get('/login')->assertOk()->assertSee('Sesi sudah berakhir. Silakan login kembali.');
    }

    public function test_an_expired_json_request_keeps_the_419_status(): void
    {
        $this->app->instance('env', 'local');

        $this->postJson('/login', ['_token' => 'expired'])->assertStatus(419)
            ->assertJsonPath('message', 'Halaman sudah kedaluwarsa. Muat ulang halaman dan coba lagi.');
    }

    public function test_an_expired_public_form_keeps_the_419_status(): void
    {
        $this->app->instance('env', 'local');

        $this->post(route('jsa.form.token.store', ['token' => '00000000-0000-4000-8000-000000000001']), ['_token' => 'expired'])
            ->assertStatus(419);
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
