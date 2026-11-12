{{--
    Filter daftar admin: tombol bulat melayang (kanan bawah) yang membuka modal berisi form GET.
    Pakai: <x-admin.filter-modal :action="route(...)" :reset-url="route(...)" :active-keys="['search', ...]" title="...">
               ...field form (tanpa tombol submit)...
           </x-admin.filter-modal>
    Di HP modal muncul dari bawah (bottom sheet), di layar lebar di tengah.
--}}
@props([
    'action',
    'resetUrl',
    'activeKeys' => [],
    'title' => 'Filter',
])
@php
    $activeCount = collect($activeKeys)->filter(fn ($key) => request()->filled($key))->count();
@endphp
<div x-data="{ filterOpen: false }" @keydown.escape.window="filterOpen = false" data-filter-modal>
    @if ($activeCount > 0)
        <div class="mb-4 flex flex-wrap items-center gap-x-3 gap-y-1 rounded-xl border border-red-100 bg-red-50 px-3 py-2 text-xs text-red-800">
            <svg viewBox="0 0 24 24" class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 5h16l-6 7.5V19l-4 1v-7.5z" /></svg>
            <span><b>{{ $activeCount }}</b> filter aktif</span>
            <button type="button" @click="filterOpen = true" class="font-semibold underline hover:text-red-900">Ubah</button>
            <a href="{{ $resetUrl }}" class="ml-auto font-semibold underline hover:text-red-900">Hapus filter</a>
        </div>
    @endif

    {{-- Tombol melayang. Disembunyikan saat menu HP terbuka. --}}
    <button type="button"
            @click="filterOpen = true"
            x-show="!(typeof mobileOpen !== 'undefined' && mobileOpen)"
            class="fixed right-5 z-[35] inline-flex h-14 w-14 items-center justify-center rounded-full bg-red-600 text-white shadow-lg shadow-red-600/30 transition hover:bg-red-700 active:scale-95"
            style="bottom: calc(1.25rem + env(safe-area-inset-bottom, 0px))"
            title="Filter" aria-label="Buka filter">
        <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 5h16l-6 7.5V19l-4 1v-7.5z" /></svg>
        @if ($activeCount > 0)
            <span class="absolute -right-0.5 -top-0.5 inline-flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-amber-400 px-1 text-[10px] font-bold text-slate-900 ring-2 ring-white">{{ $activeCount }}</span>
        @endif
    </button>

    {{-- Modal --}}
    <div x-show="filterOpen" x-cloak x-transition.opacity
         class="fixed inset-0 z-[80] flex items-end justify-center bg-slate-900/50 sm:items-center sm:p-4"
         @click.self="filterOpen = false">
        <div x-show="filterOpen"
             x-transition:enter="transition duration-200 ease-out"
             x-transition:enter-start="translate-y-6 opacity-0"
             x-transition:enter-end="translate-y-0 opacity-100"
             class="flex max-h-[90vh] w-full flex-col overflow-hidden rounded-t-3xl bg-white shadow-2xl sm:max-w-lg sm:rounded-2xl"
             role="dialog" aria-modal="true" aria-label="{{ $title }}">
            <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
                <div class="flex items-center gap-2">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-red-600 text-white">
                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 5h16l-6 7.5V19l-4 1v-7.5z" /></svg>
                    </span>
                    <h3 class="text-sm font-bold text-slate-800">{{ $title }}</h3>
                </div>
                <button type="button" @click="filterOpen = false"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700"
                        aria-label="Tutup">
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6L6 18" /></svg>
                </button>
            </div>

            <form method="GET" action="{{ $action }}" class="flex min-h-0 flex-1 flex-col">
                <div class="grid grid-cols-1 gap-3 overflow-y-auto px-5 py-4">
                    {{ $slot }}
                </div>
                <div class="flex items-center gap-3 border-t border-slate-100 px-5 py-3"
                     style="padding-bottom: calc(0.75rem + env(safe-area-inset-bottom, 0px))">
                    <a href="{{ $resetUrl }}" class="text-xs font-semibold text-slate-500 underline hover:text-red-600">Reset</a>
                    <button type="submit"
                            class="ml-auto inline-flex items-center gap-2 rounded-lg bg-red-600 px-5 py-2 text-xs font-semibold text-white hover:bg-red-700">
                        Terapkan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
