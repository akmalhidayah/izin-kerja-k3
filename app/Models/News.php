<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Berita K3 yang tampil di landing page. Dikelola Super Admin lewat Master Data > Upload Berita.
 */
class News extends Model
{
    protected $table = 'news';

    protected $fillable = [
        'title',
        'description',
        'link',
        'image_path',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /** URL publik foto (disk public, lewat symlink storage). */
    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn () => asset('storage/' . ltrim((string) $this->image_path, '/')));
    }

    /** Nama sumber diambil dari domain link, contoh "sementonasa.co.id". */
    protected function sourceLabel(): Attribute
    {
        return Attribute::get(function () {
            $host = parse_url((string) $this->link, PHP_URL_HOST);

            return $host ? Str::of($host)->lower()->replaceMatches('/^www\./', '')->toString() : '';
        });
    }
}
