<x-admin-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-red-700">Permintaan Izin Kerja</h1>
        <input type="text" placeholder="Cari nama vendor/user..." class="mt-2 px-4 py-2 w-80 rounded-md border border-gray-300 focus:ring-red-400 text-sm">
    </div>

    <div class="bg-white p-6 rounded shadow">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Daftar Permintaan Masuk</h2>

        <table class="min-w-full text-sm table-auto">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="px-4 py-2">Vendor/User</th>
                    <th class="px-4 py-2">Tanggal Pengajuan</th>
                    <th class="px-4 py-2">Ditangani Oleh</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Progress</th>
                    <th class="px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach ($requests as $request)
                    <tr>
                        <td class="px-4 py-2">
                            <div class="font-medium text-gray-800">{{ $request->user_name }}</div>
                            <div class="text-gray-500 text-xs">{{ $request->vendor_name }}</div>
                        </td>
                        <td class="px-4 py-2">{{ $request->tanggal }}</td>
                        <td class="px-4 py-2 text-gray-700">{{ $request->handled_by ?? '-' }}</td>
                        <td class="px-4 py-2">
                            @if($request->status == 'Perlu Revisi')
                                <span class="text-yellow-600 font-medium">Perlu Revisi</span>
                            @elseif($request->status == 'Selesai')
                                <span class="text-green-600 font-medium">Selesai</span>
                            @else
                                <span class="text-gray-600">{{ $request->status }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-2">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-{{ $request->progress == 100 ? 'green' : 'yellow' }}-500 h-2 rounded-full" style="width: {{ $request->progress }}%"></div>
                            </div>
                            <span class="text-xs text-gray-500">{{ $request->current_step }}/12</span>
                        </td>
                        <td class="px-4 py-2">
                        <a href="{{ route('admin.permintaansik.show', $request->id) }}" class="text-blue-600 hover:underline">Lihat Detail</a>

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-admin-layout>
