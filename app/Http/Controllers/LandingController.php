<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Support\Collection;

class LandingController extends Controller
{
    private const NEWS_LIMIT = 6;

    public function __invoke()
    {
        return view('welcome', ['news' => $this->latestNews()]);
    }

    /**
     * Landing page tidak boleh ikut error bila tabel berita belum dimigrasi
     * atau database sedang bermasalah; cukup sembunyikan bagian berita.
     */
    private function latestNews(): Collection
    {
        try {
            return News::active()->latest()->orderByDesc('id')->limit(self::NEWS_LIMIT)->get();
        } catch (\Throwable $e) {
            report($e);

            return collect();
        }
    }
}
