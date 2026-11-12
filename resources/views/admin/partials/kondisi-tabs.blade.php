{{--
    Empat tab kondisi di atas daftar permintaan: Semua, Perlu Tindakan, Perlu Revisi, Riwayat.
    Butuh $kondisiTabs, $kondisiSub (penyaring kecil di tab Perlu Tindakan), dan $routeName.
    Parameter lain (search, admin, bulan) ikut dibawa; halaman direset ke 1.
--}}
@php
    $tabStyles = [
        '' => ['active' => 'bg-gray-900 text-white', 'idle' => 'bg-white text-gray-700 hover:bg-gray-100'],
        'perlu_tindakan' => ['active' => 'bg-amber-500 text-white', 'idle' => 'bg-white text-amber-700 hover:bg-amber-50'],
        'revisi' => ['active' => 'bg-red-600 text-white', 'idle' => 'bg-white text-red-700 hover:bg-red-50'],
        'riwayat' => ['active' => 'bg-emerald-600 text-white', 'idle' => 'bg-white text-emerald-700 hover:bg-emerald-50'],
    ];
    $carry = request()->except(['kondisi', 'page']);
    $kondisiSub = $kondisiSub ?? [];
@endphp
<div class="mb-4">
    <div class="-mx-3 flex gap-2 overflow-x-auto px-3 pb-1 sm:mx-0 sm:flex-wrap sm:px-0">
        @foreach ($kondisiTabs as $tab)
            @php
                $style = $tabStyles[$tab['key']] ?? $tabStyles[''];
                $params = $tab['key'] ? array_merge($carry, ['kondisi' => $tab['key']]) : $carry;
            @endphp
            <a href="{{ route($routeName, $params) }}"
               class="inline-flex shrink-0 items-center gap-1.5 rounded-full border border-gray-200 px-4 py-1.5 text-xs font-semibold shadow-sm transition {{ $tab['active'] ? $style['active'] : $style['idle'] }}">
                {{ $tab['label'] }}
                <span class="rounded-full px-1.5 py-0.5 text-[10px] {{ $tab['active'] ? 'bg-white/20' : 'bg-gray-100 text-gray-600' }}">{{ $tab['count'] }}</span>
            </a>
        @endforeach
    </div>

    @if ($kondisiSub)
        <div class="mt-2 flex flex-wrap items-center gap-1.5 pl-1 text-[11px]">
            <span class="text-gray-400">Tampilkan:</span>
            @foreach ($kondisiSub as $sub)
                <a href="{{ route($routeName, array_merge($carry, ['kondisi' => $sub['key']])) }}"
                   class="rounded-full px-2.5 py-1 font-semibold transition {{ $sub['active'] ? 'bg-amber-100 text-amber-800 ring-1 ring-amber-300' : 'text-gray-500 hover:bg-gray-100' }}">
                    {{ $sub['label'] }} <span class="font-normal opacity-70">{{ $sub['count'] }}</span>
                </a>
            @endforeach
        </div>
    @endif
</div>
