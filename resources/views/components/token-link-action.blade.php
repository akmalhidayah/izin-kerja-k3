@props([
    'record',
    'routeName',
    'regenerateType',
    'compact' => false,
    'helpText' => 'Salin link berikut dan kirimkan ke pihak terkait untuk tanda tangan:',
])

@php
    $expiresAt = $record?->token_expires_at ? \Illuminate\Support\Carbon::parse($record->token_expires_at) : null;
    $isExpired = !$record?->token || !$expiresAt || $expiresAt->isPast();
    $tokenUrl = !$isExpired ? route($routeName, $record->token) : null;
@endphp

@if ($record)
    @if ($compact)
        @if ($isExpired)
            <form method="POST" action="{{ route('token-links.regenerate', ['type' => $regenerateType, 'id' => $record->id]) }}" class="inline-flex">
                @csrf
                <button type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white p-1 rounded-full"
                    title="Regenerate Link Token">
                    <i class="fas fa-rotate text-xs"></i>
                </button>
            </form>
        @else
            <button type="button"
                onclick='copyPermitTokenLink(@json($tokenUrl))'
                class="bg-blue-600 hover:bg-blue-700 text-white p-1 rounded-full"
                title="Salin Link">
                <i class="fas fa-link text-xs"></i>
            </button>
        @endif
    @else
        <div class="mt-2 text-xs text-gray-700">
            {{ $isExpired ? 'Link token sudah expired. Regenerate untuk membuat link baru:' : $helpText }}
            <div class="flex items-center gap-2 mt-1">
                <input type="text" value="{{ $tokenUrl ?? 'Token expired' }}" readonly
                    class="text-xs border-gray-300 rounded p-1 w-full bg-gray-100">

                @if ($isExpired)
                    <form method="POST" action="{{ route('token-links.regenerate', ['type' => $regenerateType, 'id' => $record->id]) }}">
                        @csrf
                        <button type="submit"
                            class="px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700">
                            Regenerate
                        </button>
                    </form>
                @else
                    <button type="button"
                        onclick='copyPermitTokenLink(@json($tokenUrl))'
                        class="px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">
                        Salin
                    </button>
                @endif
            </div>
            @if (!$isExpired)
                <div class="mt-1 text-[10px] text-gray-500">
                    Expired: {{ $expiresAt->format('d/m/Y H:i') }}
                </div>
            @endif
        </div>
    @endif
@endif

@once
    <script>
        function showPermitTokenNotice(icon, title, text) {
            if (window.Swal) {
                Swal.fire({
                    icon: icon,
                    title: title,
                    text: text,
                    timer: 2200,
                    showConfirmButton: false,
                });
                return;
            }

            alert(text || title);
        }

        function copyPermitTokenLink(tokenUrl) {
            navigator.clipboard.writeText(tokenUrl).then(() => {
                showPermitTokenNotice('success', 'Link berhasil disalin', 'Link token sudah masuk clipboard.');
            }).catch(() => {
                showPermitTokenNotice('error', 'Gagal menyalin', 'Salin link secara manual dari kolom link.');
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            @if (session('token_regenerated'))
                showPermitTokenNotice('success', 'Regenerate sukses', @json(session('token_regenerated')));
            @endif
        });
    </script>
@endonce
