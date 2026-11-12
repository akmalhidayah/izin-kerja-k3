<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\User;
use App\Models\WorkPermitKetinggian;
use App\Support\PermitChecklists;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Daftar "Persyaratan Kerja Aman" permit ketinggian harus sama di form modal, form token, dan PDF.
 * Dulu kedua form memakai 10 butir karangan sementara PDF memakai 18 butir resmi.
 */
class KetinggianChecklistTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function permit(array $overrides = []): array
    {
        $user = User::factory()->create(['usertype' => 'user']);
        $notification = Notification::create([
            'user_id' => $user->id,
            'type' => 'notif',
            'number' => 'KT-'.uniqid(),
            'description' => 'Pekerjaan ketinggian',
            'status' => 'menunggu',
        ]);

        $permit = WorkPermitKetinggian::create(array_merge(['notification_id' => $notification->id], $overrides));

        return [$user, $notification, $permit];
    }

    public function test_checklist_has_the_eighteen_official_items(): void
    {
        $items = PermitChecklists::ketinggian();

        $this->assertCount(18, $items);
        $this->assertSame(array_values($items), $items, 'Urutan butir adalah kunci jawaban tersimpan');
        $this->assertStringContainsString('Area kerja sudah diperiksa', $items[0]);
        $this->assertStringContainsString('tag line', $items[17]);
    }

    public function test_modal_form_lists_every_pdf_item_and_no_invented_ones(): void
    {
        [$user, $notification] = $this->permit();

        $response = $this->actingAs($user)->get(route('working-permit.form', [
            'notification' => $notification->id,
            'type' => 'ketinggian',
        ]));

        $response->assertOk();

        foreach (PermitChecklists::ketinggian() as $index => $text) {
            $response->assertSee($text, false);
            $response->assertSee('name="kerja_aman_ketinggian['.$index.']"', false);
        }

        $response->assertDontSee('name="kerja_aman_ketinggian[18]"', false);
        $response->assertDontSee('Lifeline atau fall arrest', false);
        $response->assertDontSee('Supervisor melakukan briefing', false);
    }

    public function test_token_form_lists_every_pdf_item(): void
    {
        [, , $permit] = $this->permit();
        $permit->forceFill(['token' => 'tok-ketinggian-1', 'token_expires_at' => now()->addDay()])->save();

        $response = $this->get(route('working-permit.ketinggian.token', ['token' => 'tok-ketinggian-1']));

        $response->assertOk();

        foreach (PermitChecklists::ketinggian() as $index => $text) {
            $response->assertSee($text, false);
            $response->assertSee('name="kerja_aman_ketinggian['.$index.']"', false);
        }

        $response->assertDontSee('Lifeline atau fall arrest', false);
    }

    public function test_pdf_template_shows_saved_answers_against_the_same_items(): void
    {
        $answers = array_fill(0, 18, 'ya');
        $answers[2] = 'na';

        [, , $permit] = $this->permit(['kerja_aman_ketinggian' => $answers]);

        $html = view('pengajuan-user.workingpermit.ketinggianpdf', [
            'permit' => $permit,
            'detail' => null,
            'closure' => null,
        ])->render();

        foreach (PermitChecklists::ketinggian() as $text) {
            $this->assertStringContainsString($text, $html);
        }

        // 17 butir Ya dan 1 butir N/A. Sel lain memakai awalan simbol (✓/✕), jadi tidak ikut terhitung.
        $this->assertSame(17, substr_count($html, '>Ya</td>'));
        $this->assertSame(1, substr_count($html, '>N/A</td>'));
    }

    public function test_answers_are_prefilled_in_the_form_by_index(): void
    {
        $answers = array_fill(0, 18, 'ya');
        $answers[17] = 'na';

        [$user, $notification] = $this->permit(['kerja_aman_ketinggian' => $answers]);

        $content = $this->actingAs($user)->get(route('working-permit.form', [
            'notification' => $notification->id,
            'type' => 'ketinggian',
        ]))->getContent();

        $this->assertMatchesRegularExpression('/name="kerja_aman_ketinggian\[17\]" value="na"\s*checked/', $content);
        $this->assertMatchesRegularExpression('/name="kerja_aman_ketinggian\[0\]" value="ya"\s*checked/', $content);
    }
}
