{{--
    Masa berlaku Working Permit + arsip periode sebelumnya (khusus admin).
    Parameter: $validity (PermitValidityService::state), $archives (Collection dikelompokkan per cycle), $notificationId
--}}
@php
    $validity = $validity ?? null;
    $archives = $archives ?? collect();
    $fmt = fn ($date) => $date ? $date->timezone(config('app.timezone'))->format('d-m-Y H:i') : '-';
@endphp

@if ($validity)
    <div class="flex flex-wrap items-center justify-between gap-2 rounded-lg border px-3 py-2
        {{ $validity['expired'] ? 'border-orange-300 bg-orange-50 text-orange-800' : 'border-gray-200 bg-white text-gray-700' }}">
        <div class="text-[11px] leading-snug">
            <div class="font-semibold">
                Periode permit {{ $validity['cycle'] }}
                @if ($validity['expired'])
                    · Kedaluwarsa
                @endif
            </div>
            @if ($validity['expired'])
                Kedaluwarsa sejak {{ $fmt($validity['expires_at']) }}. Pemohon harus membuat permit baru; step setelahnya terkunci.
            @elseif ($validity['active'])
                @if ($validity['is_pgo'])
                    Harus disetujui sebelum {{ $fmt($validity['expires_at']) }} (sisa {{ $validity['remaining_days'] }} hari).
                @else
                    Berlaku {{ $fmt($validity['valid_from']) }} s.d. {{ $fmt($validity['expires_at']) }} (sisa {{ $validity['remaining_days'] }} hari).
                @endif
            @elseif ($validity['finished'])
                Selesai, masa berlaku tidak lagi dihitung.
            @else
                Masa berlaku {{ $validity['days'] }} hari dimulai saat step ini disetujui.
            @endif
        </div>

        @if ($archives->isNotEmpty())
            <div x-data="{ open: false, pdf: null, title: '' }" class="shrink-0">
                <button type="button" @click="open = true"
                        class="inline-flex items-center gap-1.5 rounded-full border border-gray-300 bg-white px-2.5 py-1 text-[11px] font-semibold text-gray-700 hover:bg-gray-50"
                        title="Arsip permit periode sebelumnya">
                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <rect x="3" y="4" width="18" height="4" rx="1" />
                        <path d="M5 8v11a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V8" />
                        <path d="M10 12h4" />
                    </svg>
                    Arsip ({{ $archives->count() }})
                </button>

                <div x-show="open" x-cloak @keydown.escape.window="open = false; pdf = null"
                     class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-3" role="dialog" aria-modal="true">
                    <div @click.outside="open = false; pdf = null"
                         class="flex max-h-[92vh] w-full max-w-5xl flex-col overflow-hidden rounded-2xl bg-white shadow-xl">
                        <div class="flex items-center justify-between border-b px-4 py-3">
                            <div class="text-sm font-semibold text-gray-800">Arsip Working Permit</div>
                            <button type="button" @click="open = false; pdf = null" class="rounded-lg px-2 py-1 text-gray-500 hover:bg-gray-100" aria-label="Tutup">✕</button>
                        </div>

                        <div class="grid min-h-0 flex-1 grid-cols-1 md:grid-cols-[280px_1fr]">
                            <div class="max-h-[40vh] space-y-3 overflow-y-auto border-b p-3 md:max-h-none md:border-b-0 md:border-r">
                                @foreach ($archives as $cycle => $items)
                                    @php $first = $items->first(); @endphp
                                    <div class="rounded-xl border border-gray-200 p-3">
                                        <div class="text-[12px] font-semibold text-gray-800">Periode {{ $cycle }}</div>
                                        <div class="mt-0.5 text-[11px] text-gray-500">
                                            Berlaku: {{ $fmt($first->valid_from) }}<br>
                                            Kedaluwarsa: {{ $fmt($first->expires_at) }}<br>
                                            Diarsipkan: {{ $fmt($first->created_at) }}
                                        </div>
                                        <div class="mt-2 flex flex-wrap gap-1.5">
                                            @foreach ($items as $archive)
                                                @if ($archive->pdf_path)
                                                    <button type="button"
                                                            @click="pdf = @js(route('admin.permintaansik.permitArchivePdf', ['id' => $notificationId, 'archive' => $archive->id])); title = @js('Permit ' . $archive->label . ' · Periode ' . $cycle)"
                                                            class="rounded-full bg-green-500 px-2.5 py-1 text-[11px] text-white hover:bg-green-600">
                                                        {{ $archive->label }}
                                                    </button>
                                                @else
                                                    <span class="rounded-full bg-gray-100 px-2.5 py-1 text-[11px] text-gray-500" title="PDF gagal dibuat saat pengarsipan; data tetap tersimpan">
                                                        {{ $archive->label }} (tanpa PDF)
                                                    </span>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="flex min-h-[50vh] flex-col bg-gray-50">
                                <template x-if="pdf">
                                    <div class="flex h-full flex-col">
                                        <div class="flex items-center justify-between px-3 py-2 text-[12px] text-gray-700">
                                            <span x-text="title"></span>
                                            <a :href="pdf" target="_blank" rel="noopener" class="text-blue-600 hover:underline">Buka di tab baru</a>
                                        </div>
                                        <iframe :src="pdf" class="h-[70vh] w-full flex-1 border-0 bg-white" title="PDF arsip permit"></iframe>
                                    </div>
                                </template>
                                <div x-show="!pdf" class="flex flex-1 items-center justify-center p-6 text-center text-[12px] text-gray-500">
                                    Pilih permit di sebelah kiri untuk melihat PDF arsipnya.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endif
