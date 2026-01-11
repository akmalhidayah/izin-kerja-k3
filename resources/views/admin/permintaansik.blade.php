<x-admin-layout>
    <div class="rounded-2xl border border-gray-200 bg-gradient-to-r from-red-50 via-white to-amber-50 p-6 shadow-sm mb-6">
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-red-600 text-white shadow">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M4 5h16v5H4zM4 13h9v6H4zM15 13h5v6h-5z" />
                        </svg>
                    </span>
                    <h1 class="text-3xl font-extrabold text-red-700 tracking-wide">Permintaan Izin Kerja</h1>
                </div>
                <p class="text-xs text-gray-600 mt-1">Pantau progres pengajuan, status dokumen, dan penanggung jawab.</p>
            </div>
            <div class="text-[11px] text-gray-500">
                {{ now()->format('d M Y, H:i') }}
            </div>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.permintaansik') }}"
        class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3 bg-white/90 backdrop-blur p-4 border border-gray-200 rounded-xl mb-6 shadow-sm">

        {{-- Pencarian --}}
        <div class="relative">
            <span class="pointer-events-none absolute left-3 top-2.5 text-gray-400">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="7" />
                    <path d="M20 20l-3.5-3.5" />
                </svg>
            </span>
            <input type="text" name="search" placeholder="Cari vendor / nomor / deskripsi..."
            value="{{ request('search') }}"
            class="w-full pl-9 pr-3 py-2 border rounded-lg text-xs focus:ring focus:ring-red-300" />
        </div>

        {{-- Admin Penanggung Jawab --}}
        <div class="relative">
            <span class="pointer-events-none absolute left-3 top-2.5 text-gray-400">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4z" />
                    <path d="M4 20a8 8 0 0 1 16 0" />
                </svg>
            </span>
            <select name="admin" class="w-full pl-9 pr-3 py-2 border rounded-lg text-xs">
                <option value="">Admin Penanggung Jawab</option>
                @foreach ($adminList as $id => $name)
                    <option value="{{ $name }}" {{ request('admin') == $name ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Status Dokumen --}}
        <div class="relative">
            <span class="pointer-events-none absolute left-3 top-2.5 text-gray-400">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M9 12l2 2 4-4" />
                    <path d="M12 3a9 9 0 1 0 9 9" />
                </svg>
            </span>
            <select name="status" class="w-full pl-9 pr-3 py-2 border rounded-lg text-xs">
                <option value="">Status Dokumen</option>
                <option value="Menunggu" {{ request('status') == 'Menunggu' ? 'selected' : '' }}>Menunggu</option>
                <option value="Perlu Revisi" {{ request('status') == 'Perlu Revisi' ? 'selected' : '' }}>Perlu Revisi</option>
                <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
            </select>
        </div>

        {{-- Dari Bulan dan Tahun --}}
        <div class="flex gap-2 w-full">
            <div class="relative w-1/2">
                <span class="pointer-events-none absolute left-3 top-2.5 text-gray-400">
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M7 3v3M17 3v3M3 9h18M5 7h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2z" />
                    </svg>
                </span>
                <select name="bulan_dari" class="w-full pl-9 pr-3 py-2 border rounded-lg text-xs">
                <option value="">Dari Bulan</option>
                @foreach ($bulanList as $num => $nama)
                    <option value="{{ $num }}" {{ request('bulan_dari') == $num ? 'selected' : '' }}>{{ $nama }}</option>
                @endforeach
                </select>
            </div>
            <div class="relative w-1/2">
                <span class="pointer-events-none absolute left-3 top-2.5 text-gray-400">
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M4 7h16M7 3v3M17 3v3M5 11h14v8H5z" />
                    </svg>
                </span>
                <select name="tahun_dari" class="w-full pl-9 pr-3 py-2 border rounded-lg text-xs">
                <option value="">Tahun</option>
                @foreach ($tahunList as $tahun)
                    <option value="{{ $tahun }}" {{ request('tahun_dari') == $tahun ? 'selected' : '' }}>{{ $tahun }}</option>
                @endforeach
                </select>
            </div>
        </div>

        {{-- Sampai Bulan dan Tahun --}}
        <div class="flex gap-2 w-full">
            <div class="relative w-1/2">
                <span class="pointer-events-none absolute left-3 top-2.5 text-gray-400">
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M7 3v3M17 3v3M3 9h18M5 7h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2z" />
                    </svg>
                </span>
                <select name="bulan_sampai" class="w-full pl-9 pr-3 py-2 border rounded-lg text-xs">
                <option value="">Sampai Bulan</option>
                @foreach ($bulanList as $num => $nama)
                    <option value="{{ $num }}" {{ request('bulan_sampai') == $num ? 'selected' : '' }}>{{ $nama }}</option>
                @endforeach
                </select>
            </div>
            <div class="relative w-1/2">
                <span class="pointer-events-none absolute left-3 top-2.5 text-gray-400">
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M4 7h16M7 3v3M17 3v3M5 11h14v8H5z" />
                    </svg>
                </span>
                <select name="tahun_sampai" class="w-full pl-9 pr-3 py-2 border rounded-lg text-xs">
                <option value="">Tahun</option>
                @foreach ($tahunList as $tahun)
                    <option value="{{ $tahun }}" {{ request('tahun_sampai') == $tahun ? 'selected' : '' }}>{{ $tahun }}</option>
                @endforeach
                </select>
            </div>
        </div>

        {{-- Tombol Filter & Reset --}}
        <div class="flex gap-2 w-full">
            <button type="submit" class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 text-xs rounded-lg">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 6h16M7 12h10M10 18h4" />
                </svg>
                Filter
            </button>
            <a href="{{ route('admin.permintaansik') }}"
               class="inline-flex items-center gap-1.5 text-xs text-gray-600 hover:text-blue-600 underline self-center">
                <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M4 12a8 8 0 1 0 8-8" />
                    <path d="M4 4v4h4" />
                </svg>
                Reset
            </a>
        </div>
    </form>

    <div class="bg-white p-6 rounded-2xl shadow-md">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-gray-900 text-white">
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 6h16M4 12h16M4 18h10" />
                    </svg>
                </span>
                <h2 class="text-base font-semibold text-gray-800">Daftar Permintaan Masuk</h2>
            </div>
            <div class="text-[11px] text-gray-500">
                Total: {{ $requests->total() }}
            </div>
        </div>

        <div class="overflow-auto rounded-xl border border-gray-200 bg-gradient-to-b from-white to-gray-50/60">
            <table class="min-w-full text-xs table-auto">
                <thead class="bg-gray-50/90 text-gray-700 text-[11px] uppercase tracking-wider sticky top-0 backdrop-blur">
                    <tr>
                        <th class="px-4 py-3 text-left">Vendor</th>
                        <th class="px-4 py-3 text-left">No. PO/NOTIF/SPK</th>
                        <th class="px-4 py-3 text-left">Tanggal Pengajuan</th>
                        <th class="px-4 py-3 text-left">Ditangani Oleh</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Step Saat Ini</th>
                        <th class="px-4 py-3 text-left">Progress</th>
                        <th class="px-4 py-3 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($requests as $request)
                        @php
                            $isCompleted = $request->current_step >= $totalSteps;
                            $statusLabel = $isCompleted ? 'Selesai' : $request->status;
                            $statusClass = match($statusLabel) {
                                'Perlu Revisi' => 'bg-yellow-100 text-yellow-700',
                                'Selesai' => 'bg-green-100 text-green-700',
                                default => 'bg-gray-100 text-gray-700',
                            };
                            $progressColor = $request->progress == 100 ? 'bg-green-500' : 'bg-yellow-500';
                        @endphp
                        <tr class="hover:bg-amber-50/40 transition border-l-4 {{ $isCompleted ? 'border-green-300' : ($statusLabel === 'Perlu Revisi' ? 'border-yellow-300' : 'border-gray-200') }}">
                            <td class="px-4 py-3">
                                <div class="font-semibold text-gray-900">{{ $request->user_name }}</div>
                            </td>
                            <td class="px-4 py-3 text-gray-700 text-xs align-top max-w-[220px] break-all">
                                <div class="font-medium text-gray-900 leading-tight text-xs">
                                    {{ $request->number }}
                                </div>
                                @if ($request->file)
                                    <div class="mt-1 text-[11px]">
                                        <a href="{{ asset('storage/' . $request->file) }}"
                                           target="_blank"
                                           class="text-blue-600 hover:underline inline-flex items-center gap-1">
                                            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8">
                                                <path d="M7 3h7l5 5v12a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z" />
                                                <path d="M14 3v5h5" />
                                                <path d="M8.5 13.5h7M8.5 17h5" />
                                            </svg>
                                            File
                                        </a>
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-700">{{ $request->tanggal }}</td>
                            <td class="px-4 py-3 text-gray-700">
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-[11px] text-gray-700">
                                    {{ $request->handled_by ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $statusClass }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $statusLabel === 'Selesai' ? 'bg-green-500' : ($statusLabel === 'Perlu Revisi' ? 'bg-yellow-500' : 'bg-gray-400') }}"></span>
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-800 text-sm font-medium">
                                Step {{ $isCompleted ? $totalSteps . ' - Selesai' : $request->current_step . ' - ' . ($request->current_step_title ?? 'Belum Diketahui') }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="h-2 rounded-full {{ $progressColor }}" style="width: {{ $request->progress }}%"></div>
                                </div>
                                <div class="text-[11px] text-gray-500 mt-1">
                                    {{ $request->progress }}% - {{ $request->current_step }}/{{ $totalSteps }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.permintaansik.show', $request->id) }}"
                                   class="inline-flex items-center gap-2 rounded-lg border border-blue-200 bg-blue-50 px-3 py-1 text-blue-700 text-[11px] font-semibold hover:bg-blue-100">
                                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M2 12s4-6 10-6 10 6 10 6-4 6-10 6-10-6-10-6z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                    Lihat Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-sm text-gray-500">
                                Belum ada data permintaan untuk filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4 px-4 pb-4">
                {{ $requests->links() }}
            </div>
        </div>
    </div>
</x-admin-layout>
