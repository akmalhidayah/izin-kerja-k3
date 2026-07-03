<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
