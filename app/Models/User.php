<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const USERTYPE_ADMIN = 'admin';
    public const USERTYPE_USER = 'user';
    public const USERTYPE_PGO = 'pgo';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'usertype',
        'jabatan',
        'role_id', // ini penting ditambahkan biar bisa mass assign
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function isAdmin(): bool
    {
        return $this->usertype === self::USERTYPE_ADMIN;
    }

    public function isUser(): bool
    {
        return $this->usertype === self::USERTYPE_USER;
    }

    public function isPgo(): bool
    {
        return $this->usertype === self::USERTYPE_PGO;
    }

    /**
     * Relasi ke Role (many-to-one)
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
// di User.php
public function handledNotifications()
{
    return $this->hasMany(Notification::class, 'assigned_admin_id');
}


}
