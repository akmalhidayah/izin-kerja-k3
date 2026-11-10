<?php

namespace Tests\Feature;

use App\Models\News;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NewsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Halaman admin memakai @vite; manifest build tidak tersedia di lingkungan tes.
        $this->withoutVite();
        Storage::fake('public');
    }

    private function superAdmin(): User
    {
        $role = Role::firstOrCreate(['name' => 'Super Admin']);

        return User::factory()->create(['usertype' => 'admin', 'role_id' => $role->id]);
    }

    private function regularAdmin(): User
    {
        $role = Role::firstOrCreate(['name' => 'Admin']);

        return User::factory()->create(['usertype' => 'admin', 'role_id' => $role->id]);
    }

    private function newsPayload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Penghargaan K3 Tingkat Provinsi',
            'description' => 'Ringkasan singkat berita K3.',
            'link' => 'https://www.sementonasa.co.id/berita-k3',
            'image' => UploadedFile::fake()->image('berita.jpg', 800, 500),
            'is_active' => '1',
        ], $overrides);
    }

    private function createNews(array $attributes = []): News
    {
        $createdAt = $attributes['created_at'] ?? null;
        unset($attributes['created_at']);

        $news = News::create(array_merge([
            'title' => 'Berita contoh',
            'description' => 'Deskripsi contoh.',
            'link' => 'https://example.com/berita',
            'image_path' => 'news/contoh.jpg',
            'is_active' => true,
        ], $attributes));

        if ($createdAt) {
            $news->forceFill(['created_at' => $createdAt])->save();
        }

        return $news;
    }

    // ===== Landing page =====

    public function test_landing_page_shows_active_news_with_link_and_derived_source(): void
    {
        $this->createNews([
            'title' => 'Berita Tampil',
            'link' => 'https://www.sementonasa.co.id/artikel-1',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Berita Tampil')
            ->assertSee('https://www.sementonasa.co.id/artikel-1', false)
            ->assertSee('sementonasa.co.id')
            ->assertSee('storage/news/contoh.jpg', false)
            ->assertSee('data-interval="5000"', false);
    }

    public function test_landing_page_hides_inactive_news(): void
    {
        $this->createNews(['title' => 'Berita Disembunyikan', 'is_active' => false]);

        $this->get('/')->assertOk()->assertDontSee('Berita Disembunyikan');
    }

    public function test_landing_page_shows_only_the_six_latest_news(): void
    {
        foreach (range(1, 8) as $i) {
            $this->createNews(['title' => "Berita nomor {$i}", 'created_at' => now()->subMinutes(10 - $i)]);
        }

        $response = $this->get('/')->assertOk();

        $response->assertSee('Berita nomor 8')->assertSee('Berita nomor 3');
        $response->assertDontSee('Berita nomor 2')->assertDontSee('Berita nomor 1');
    }

    public function test_landing_page_still_renders_without_any_news(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertDontSee('id="berita"', false);
    }

    // ===== Otorisasi =====

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('admin.news.index'))->assertRedirect(route('login'));
    }

    public function test_vendor_and_pgo_cannot_manage_news(): void
    {
        foreach (['user', 'pgo'] as $usertype) {
            $user = User::factory()->create(['usertype' => $usertype]);

            $this->actingAs($user)->get(route('admin.news.index'))->assertForbidden();
            $this->actingAs($user)->post(route('admin.news.store'), $this->newsPayload())->assertForbidden();
        }

        $this->assertDatabaseCount('news', 0);
    }

    public function test_regular_admin_without_super_admin_role_is_forbidden(): void
    {
        $admin = $this->regularAdmin();
        $news = $this->createNews();

        $this->actingAs($admin)->get(route('admin.news.index'))->assertForbidden();
        $this->actingAs($admin)->post(route('admin.news.store'), $this->newsPayload())->assertForbidden();
        $this->actingAs($admin)->delete(route('admin.news.destroy', $news))->assertForbidden();

        $this->assertDatabaseCount('news', 1);
    }

    // ===== CRUD Super Admin =====

    public function test_super_admin_can_open_the_news_pages(): void
    {
        $admin = $this->superAdmin();
        $news = $this->createNews(['title' => 'Berita di daftar']);

        $this->actingAs($admin)->get(route('admin.news.index'))->assertOk()->assertSee('Berita di daftar');
        $this->actingAs($admin)->get(route('admin.news.create'))->assertOk();
        $this->actingAs($admin)->get(route('admin.news.edit', $news))->assertOk()->assertSee('Berita di daftar');
    }

    public function test_super_admin_can_create_news_and_the_photo_is_stored(): void
    {
        $admin = $this->superAdmin();

        $this->actingAs($admin)
            ->post(route('admin.news.store'), $this->newsPayload())
            ->assertRedirect(route('admin.news.index'))
            ->assertSessionHas('success');

        $news = News::firstOrFail();
        $this->assertSame('Penghargaan K3 Tingkat Provinsi', $news->title);
        $this->assertTrue($news->is_active);
        $this->assertStringStartsWith('news/', $news->image_path);
        Storage::disk('public')->assertExists($news->image_path);
    }

    public function test_create_can_save_news_as_hidden(): void
    {
        $this->actingAs($this->superAdmin())
            ->post(route('admin.news.store'), $this->newsPayload(['is_active' => '0']))
            ->assertRedirect(route('admin.news.index'));

        $this->assertFalse(News::firstOrFail()->is_active);
    }

    public function test_create_requires_photo_and_a_valid_http_link(): void
    {
        $admin = $this->superAdmin();

        $this->actingAs($admin)
            ->post(route('admin.news.store'), $this->newsPayload(['image' => null]))
            ->assertSessionHasErrors('image');

        $this->actingAs($admin)
            ->post(route('admin.news.store'), $this->newsPayload(['link' => 'javascript:alert(1)']))
            ->assertSessionHasErrors('link');

        $this->actingAs($admin)
            ->post(route('admin.news.store'), $this->newsPayload(['link' => 'bukan-link']))
            ->assertSessionHasErrors('link');

        $this->actingAs($admin)
            ->post(route('admin.news.store'), $this->newsPayload(['title' => '', 'description' => '']))
            ->assertSessionHasErrors(['title', 'description']);

        $this->assertDatabaseCount('news', 0);
    }

    public function test_create_rejects_non_image_and_oversized_files(): void
    {
        $admin = $this->superAdmin();

        $this->actingAs($admin)
            ->post(route('admin.news.store'), $this->newsPayload(['image' => UploadedFile::fake()->create('dokumen.pdf', 100, 'application/pdf')]))
            ->assertSessionHasErrors('image');

        $this->actingAs($admin)
            ->post(route('admin.news.store'), $this->newsPayload(['image' => UploadedFile::fake()->image('besar.jpg')->size(3000)]))
            ->assertSessionHasErrors('image');

        $this->assertDatabaseCount('news', 0);
    }

    public function test_update_without_new_photo_keeps_the_existing_photo(): void
    {
        $admin = $this->superAdmin();
        Storage::disk('public')->put('news/lama.jpg', 'x');
        $news = $this->createNews(['image_path' => 'news/lama.jpg']);

        $this->actingAs($admin)
            ->put(route('admin.news.update', $news), [
                'title' => 'Judul baru',
                'description' => 'Deskripsi baru.',
                'link' => 'https://example.com/baru',
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.news.index'));

        $news->refresh();
        $this->assertSame('Judul baru', $news->title);
        $this->assertSame('news/lama.jpg', $news->image_path);
        Storage::disk('public')->assertExists('news/lama.jpg');
    }

    public function test_update_with_new_photo_replaces_and_deletes_the_old_file(): void
    {
        $admin = $this->superAdmin();
        Storage::disk('public')->put('news/lama.jpg', 'x');
        $news = $this->createNews(['image_path' => 'news/lama.jpg']);

        $this->actingAs($admin)
            ->put(route('admin.news.update', $news), $this->newsPayload())
            ->assertRedirect(route('admin.news.index'));

        $news->refresh();
        $this->assertNotSame('news/lama.jpg', $news->image_path);
        Storage::disk('public')->assertMissing('news/lama.jpg');
        Storage::disk('public')->assertExists($news->image_path);
    }

    public function test_destroy_removes_the_record_and_the_photo(): void
    {
        $admin = $this->superAdmin();
        Storage::disk('public')->put('news/hapus.jpg', 'x');
        $news = $this->createNews(['image_path' => 'news/hapus.jpg']);

        $this->actingAs($admin)
            ->delete(route('admin.news.destroy', $news))
            ->assertRedirect(route('admin.news.index'));

        $this->assertDatabaseMissing('news', ['id' => $news->id]);
        Storage::disk('public')->assertMissing('news/hapus.jpg');
    }
}
