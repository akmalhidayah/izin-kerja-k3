<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

abstract class Controller
{
    protected function findAccessibleNotification($notificationId): ?Notification
    {
        $user = auth()->user();

        if (!$user) {
            return null;
        }

        $query = Notification::where('id', $notificationId);

        if (in_array($user->usertype, ['user', 'pgo'], true)) {
            $query->where('user_id', $user->id);
        }

        return $query->first();
    }

    protected function radioYesValue(Request $request, string $field): ?bool
    {
        if (!$request->has($field)) {
            return null;
        }

        return $request->input($field) === 'ya';
    }

    protected function filterEmptyPermitValues(array $values): array
    {
        return array_filter($values, fn ($value) => $value !== null && $value !== '');
    }

    protected function tokenPdfAccessAllowed(): bool
    {
        return request()->attributes->get('token_pdf_access') === true;
    }

    protected function abortUnlessCanAccessNotification($notificationId): ?Notification
    {
        if ($this->tokenPdfAccessAllowed()) {
            return Notification::find($notificationId);
        }

        $notification = $this->findAccessibleNotification($notificationId);

        if (!$notification) {
            abort(403, 'Akses ditolak.');
        }

        return $notification;
    }

    protected function saveBase64PngSignature($base64, string $role, string $folder): ?string
    {
        if (!$base64 || !is_string($base64)) {
            return null;
        }

        if (str_starts_with($base64, 'storage/')) {
            return $base64;
        }

        if (!str_starts_with($base64, 'data:image/png;base64,')) {
            return null;
        }

        $filename = $role . '_' . Str::random(10) . '.png';
        $image = str_replace('data:image/png;base64,', '', $base64);
        $image = str_replace(' ', '+', $image);
        $binary = base64_decode($image, true);

        if ($binary === false) {
            return null;
        }

        if (strlen($binary) > 1024 * 1024) {
            return null;
        }

        Storage::disk('public')->put($folder . $filename, $binary);

        return 'storage/' . $folder . $filename;
    }

    protected function tokenStoreResponse($response, string $message, ?string $pdfUrl = null)
    {
        if (!session()->has('errors') && !session()->has('error')) {
            session()->flash('alert', $message);

            if ($pdfUrl) {
                session()->flash('token_saved', $message);
                session()->flash('token_pdf_url', $pdfUrl);
            }
        }

        return $response;
    }

    protected function permitTokenExpiryDays(): int
    {
        return max(1, (int) config('permit_tokens.expires_in_days', 3));
    }

    protected function nextPermitTokenExpiry(): Carbon
    {
        return now()->addDays($this->permitTokenExpiryDays());
    }

    protected function ensurePermitToken(Model $record): void
    {
        $values = [];

        if (!$record->token) {
            $values['token'] = (string) Str::uuid();
        }

        if (!$record->token_expires_at) {
            $values['token_expires_at'] = $this->nextPermitTokenExpiry();
        }

        if ($values !== []) {
            $record->forceFill($values)->save();
        }
    }

    protected function regeneratePermitToken(Model $record): void
    {
        $record->forceFill([
            'token' => (string) Str::uuid(),
            'token_expires_at' => $this->nextPermitTokenExpiry(),
        ])->save();
    }

    protected function permitTokenExpired(Model $record): bool
    {
        if (!$record->token || !$record->token_expires_at) {
            return true;
        }

        return Carbon::parse($record->token_expires_at)->isPast();
    }

    protected function abortIfPermitTokenExpired(Model $record): void
    {
        if ($this->permitTokenExpired($record)) {
            abort(403, 'Link token sudah expired. Silakan regenerate token dari halaman pengajuan.');
        }
    }
}
