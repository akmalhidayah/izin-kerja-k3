{{--
    Status masa berlaku Working Permit untuk dashboard vendor/PGO.
    Parameter: $permitValidity (dari PermitValidityService::state), $notification, $mode = 'banner' | 'step'
--}}
@php
    $validity = $permitValidity ?? null;
    $mode = $mode ?? 'step';
    $fmt = fn ($date) => $date ? $date->timezone(config('app.timezone'))->format('d-m-Y H:i') : '-';
@endphp

@if ($validity && $notification)
    @if ($mode === 'banner')
        @if ($validity['expired'])
            <div class="mb-4 rounded-xl border border-orange-300 bg-orange-50 p-3 text-sm text-orange-800">
                <div class="font-semibold">Working Permit kedaluwarsa sejak {{ $fmt($validity['expires_at']) }}.</div>
                <div class="mt-1 text-[13px]">
                    @if ($validity['is_pgo'])
                        Permit belum disetujui admin dalam {{ $validity['days'] }} hari. Buat permit baru di step Input Working Permit, lengkapi tanda tangan, lalu kirim ulang untuk disetujui.
                    @else
                        Step setelah Input Working Permit dikunci sementara. Buat permit baru, lengkapi tanda tangan, dan tunggu persetujuan admin. Setelah itu Anda bisa melanjutkan dari step terakhir.
                    @endif
                </div>
            </div>
        @endif
    @else
        @if ($validity['expired'])
            <div class="mt-1 w-full max-w-[220px] rounded-lg border border-orange-300 bg-orange-50 p-2 text-center">
                <div class="text-[11px] font-semibold text-orange-700">Permit kedaluwarsa</div>
                <div class="text-[10px] text-orange-700">sejak {{ $fmt($validity['expires_at']) }}</div>
                <form method="POST" action="{{ route('working-permit.renew', $notification->id) }}" class="mt-2"
                      onsubmit="return confirm('Buat permit baru? Data permit lama disalin, tetapi semua tanda tangan, tanggal, dan jam dikosongkan. Permit baru harus disetujui admin lagi.')">
                    @csrf
                    <button type="submit" class="w-full rounded-full bg-orange-600 px-3 py-1 text-[10px] font-semibold text-white hover:bg-orange-700">
                        Buat Permit Baru
                    </button>
                </form>
            </div>
        @elseif ($validity['active'])
            <div class="mt-1 text-center text-[10px] leading-tight {{ ($validity['remaining_days'] ?? 99) <= 2 ? 'text-orange-600 font-semibold' : 'text-gray-500' }}">
                @if ($validity['is_pgo'])
                    Harus disetujui admin sebelum<br>{{ $fmt($validity['expires_at']) }}
                @else
                    Permit berlaku s.d.<br>{{ $fmt($validity['expires_at']) }}
                @endif
                <span class="block">(sisa {{ $validity['remaining_days'] }} hari)</span>
            </div>
        @endif
    @endif
@endif
