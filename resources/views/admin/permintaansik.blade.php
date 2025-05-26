<x-admin-layout>
    <div class="mb-6">
        <h1 class="text-3xl font-extrabold text-red-700 tracking-wide">Permintaan Izin Kerja</h1>
       <div class="flex flex-wrap items-center gap-4 mb-6">
    <form method="GET" action="{{ route('admin.permintaansik') }}" class="flex flex-wrap items-center gap-2">
        <input type="text" name="search" placeholder="Cari user/vendor..."
            value="{{ request('search') }}"
            class="px-3 py-2 border rounded w-64 text-sm focus:ring focus:ring-red-400">

        <select name="bulan" class="px-2 py-1 border rounded text-sm">
            <option value="">Bulan</option>
            @foreach ($bulanList as $num => $nama)
                <option value="{{ $num }}" {{ request('bulan') == $num ? 'selected' : '' }}>{{ $nama }}</option>
            @endforeach
        </select>

        <select name="tahun" class="px-2 py-1 border rounded text-sm">
            <option value="">Tahun</option>
            @foreach ($tahunList as $tahun)
                <option value="{{ $tahun }}" {{ request('tahun') == $tahun ? 'selected' : '' }}>{{ $tahun }}</option>
            @endforeach
        </select>

        <button type="submit"
            class="bg-red-600 text-white text-sm px-4 py-2 rounded hover:bg-red-700 transition">Filter</button>
    </form>
</div>

    </div>

    <div class="bg-white p-6 rounded-xl shadow-md">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">📄 Daftar Permintaan Masuk</h2>

        <div class="overflow-auto rounded-md">
            <table class="min-w-full text-sm table-auto border border-gray-200 rounded">
                <thead class="bg-gray-50 text-gray-700 text-[13px] uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-left">User</th>
                        <th class="px-4 py-3 text-left">Tanggal Pengajuan</th>
                        <th class="px-4 py-3 text-left">Ditangani Oleh</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-left">Step Saat Ini</th>
                        <th class="px-4 py-3 text-left">Progress</th>
                        <th class="px-4 py-3 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($requests as $request)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3">
                                <div class="font-semibold text-gray-900">{{ $request->user_name }}</div>
                            </td>
                            <td class="px-4 py-3 text-gray-700">{{ $request->tanggal }}</td>
<td class="px-4 py-3 text-gray-700">{{ $request->handled_by ?? '-' }}</td>

                            <td class="px-4 py-3">
                                @php
                                    $statusClass = match($request->status) {
                                        'Perlu Revisi' => 'text-yellow-600 font-semibold',
                                        'Selesai' => 'text-green-600 font-semibold',
                                        default => 'text-gray-600',
                                    };
                                @endphp
                                <span class="{{ $statusClass }}">
                                    {{ $request->current_step >= 12 ? 'Selesai' : $request->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-800 text-sm font-medium">
                                Step {{ $request->current_step >= 12 ? '12 - Selesai' : $request->current_step . ' - ' . ($request->current_step_title ?? 'Belum Diketahui') }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="h-2 rounded-full {{ $request->progress == 100 ? 'bg-green-500' : 'bg-yellow-500' }}" style="width: {{ $request->progress }}%"></div>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">{{ $request->current_step }}/12</div>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.permintaansik.show', $request->id) }}" class="text-blue-600 hover:underline font-medium text-sm">Lihat Detail</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-admin-layout>
