<?php

namespace App\Http\Middleware;

use App\Models\Notification;
use App\Models\User;
use App\Services\PermitValidityService;
use App\Support\PermitRegistry;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Menolak pengisian/penyimpanan permit yang sudah kedaluwarsa (route "permit.active").
 * Pemohon harus menekan "Buat Permit Baru" lebih dulu.
 *
 * Route token: permit dicari dari token pada URL. Route terautentikasi: pengajuan dicari dari
 * notification_id, dibatasi milik user/PGO yang login (pengecekan kepemilikan tetap di controller).
 */
class EnsurePermitNotExpired
{
    public const MESSAGE = 'Working Permit sudah kedaluwarsa. Buat permit baru dari dashboard pengajuan sebelum mengisi atau menandatangani lagi.';

    public function __construct(
        private readonly PermitValidityService $validity,
        private readonly PermitRegistry $registry,
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $notification = $this->resolveNotification($request);

        if ($notification && $this->validity->isExpired($notification)) {
            if ($request->route('token') !== null) {
                abort(403, self::MESSAGE);
            }

            return back()->with('error', self::MESSAGE);
        }

        return $next($request);
    }

    private function resolveNotification(Request $request): ?Notification
    {
        $token = $request->route('token');

        if (is_string($token) && $token !== '') {
            $permit = $this->registry->findByToken($token);

            return $permit ? Notification::find($permit->notification_id) : null;
        }

        $id = $request->input('notification_id');

        if (! is_numeric($id)) {
            return null;
        }

        $user = $request->user();

        return Notification::where('id', (int) $id)
            ->when(
                $user && in_array($user->usertype, [User::USERTYPE_USER, User::USERTYPE_PGO], true),
                fn ($query) => $query->where('user_id', $user->id)
            )
            ->first();
    }
}
