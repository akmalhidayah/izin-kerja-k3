{{--
    Tab kondisi di atas daftar permintaan. Butuh $kondisiTabs (dari AdminDashboardController::kondisiTabs)
    dan $routeName. Parameter lain (search, admin, bulan) ikut dibawa; halaman direset ke 1.
--}}
@php
    $tabStyles = [
        '' => ['active' => 'bg-gray-900 text-white', 'idle' => 'bg-white text-gray-700 hover:bg-gray-100'],
        'menunggu' => ['active' => 'bg-amber-500 text-white', 'idle' => 'bg-white text-amber-700 hover:bg-amber-50'],
        'belum_ditangani' => ['active' => 'bg-slate-700 text-white', 'idle' => 'bg-white text-slate-700 hover:bg-slate-100'],
        'revisi' => ['active' => 'bg-red-600 text-white', 'idle' => 'bg-white text-red-700 hover:bg-red-50'],
        'mandek' => ['active' => 'bg-orange-600 text-white', 'idle' => 'bg-white text-orange-700 hover:bg-orange-50'],
        'selesai' => ['active' => 'bg-emerald-600 text-white', 'idle' => 'bg-white text-emerald-700 hover:bg-emerald-50'],
    ];
    $carry = request()->except(['kondisi', 'page']);
@endphp
<div class="-mx-3 mb-4 flex gap-2 overflow-x-auto px-3 pb-1 sm:mx-0 sm:flex-wrap sm:px-0">
    @foreach ($kondisiTabs as $tab)
        @php
            $style = $tabStyles[$tab['key']] ?? $tabStyles[''];
            $params = $tab['key'] ? array_merge($carry, ['kondisi' => $tab['key']]) : $carry;
        @endphp
        <a href="{{ route($routeName, $params) }}"
           class="inline-flex shrink-0 items-center gap-1.5 rounded-full border border-gray-200 px-3 py-1.5 text-[11px] font-semibold shadow-sm transition {{ $tab['active'] ? $style['active'] : $style['idle'] }}">
            {{ $tab['label'] }}
            <span class="rounded-full px-1.5 py-0.5 text-[10px] {{ $tab['active'] ? 'bg-white/20' : 'bg-gray-100 text-gray-600' }}">{{ $tab['count'] }}</span>
        </a>
    @endforeach
</div>
