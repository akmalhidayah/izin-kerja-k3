<x-admin-layout>
    <!-- Banner Header -->
    <div class="relative w-full h-56 rounded-lg overflow-hidden shadow mb-6">
        <img src="{{ asset('images/banner-k3.png') }}" alt="Banner K3"
             class="w-full h-full object-cover object-center opacity-80">
        <div class="absolute inset-0 flex items-center justify-between px-6">
            <input type="text" id="searchInput"
                   placeholder="Cari Nama Vendor / User..."
                   class="px-4 py-2 rounded-md border border-white bg-white/90 backdrop-blur text-sm w-80 focus:ring-2 focus:ring-red-400 shadow-md">
        </div>
    </div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-yellow-500 p-6 rounded-xl shadow-lg flex items-center gap-4">
        <div class="bg-white text-yellow-600 p-4 rounded-full shadow">
            <i class="fas fa-spinner text-2xl"></i>
        </div>
        <div>
            <p class="text-sm text-white">Pengajuan Proses</p>
            <p class="text-xl font-bold text-white">{{ $summaryRequests->where('status', 'Menunggu')->count() }} Pengajuan</p>
        </div>
    </div>
    <div class="bg-green-500 p-6 rounded-xl shadow-lg flex items-center gap-4">
        <div class="bg-white text-green-600 p-4 rounded-full shadow">
            <i class="fas fa-thumbs-up text-2xl"></i>
        </div>
        <div>
            <p class="text-sm text-white">SIK Terbit</p>
            <p class="text-xl font-bold text-white">{{ $summaryRequests->where('current_step', '>=', $totalSteps)->count() }} SIK</p>
        </div>
    </div>
    <div class="bg-red-700 p-6 rounded-xl shadow-lg flex items-center gap-4">
        <div class="bg-white text-red-600 p-4 rounded-full shadow">
            <i class="fas fa-edit text-2xl"></i>
        </div>
        <div>
            <p class="text-sm text-white">Pengajuan Perlu Revisi</p>
            <p class="text-xl font-bold text-white">{{ $summaryRequests->where('status', 'Perlu Revisi')->count() }} Pengajuan</p>
        </div>
    </div>
</div>


    <!-- Table of Users and Their Progress -->
    <div class="bg-white p-6 rounded-xl shadow mb-8">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Izin Kerja Yang Sudah Terbit SIK</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto text-sm">
                <thead>
                    <tr class="bg-gray-100 text-left">
                        <th class="px-4 py-2">Vendor</th>
                        <th class="px-4 py-2">Handle On</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2">Progress</th>
                        <th class="px-4 py-2">Surat Izin Kerja</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($requests as $request)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $request->user_name }}</td>
                            <td class="px-4 py-2">{{ $request->handled_by }}</td>
                            <td class="px-4 py-2">
                                @php
                                    $statusClass = match($request->status) {
                                        'Perlu Revisi' => 'text-yellow-600 font-semibold',
                                        'Selesai', 'Disetujui', 'Terbit SIK' => 'text-green-600 font-semibold',
                                        default => 'text-gray-600',
                                    };
                                @endphp
                                <span class="{{ $statusClass }}">{{ $request->status }}</span>
                            </td>
                            <td class="px-4 py-2">
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="h-2 rounded-full {{ $request->progress == 100 ? 'bg-green-500' : 'bg-yellow-500' }}" style="width: {{ $request->progress }}%"></div>
                                </div>
                               <div class="text-xs text-gray-500 mt-1">{{ $request->current_step }}/{{ $totalSteps }}</div>

                            </td>
                            <td class="px-4 py-2">
                                @if($request->sik_file)
                                    <a href="{{ asset('storage/' . $request->sik_file) }}" target="_blank"
                                       class="text-sm text-blue-600 hover:underline">Lihat SIK</a>
                                @else
                                    <span class="text-xs text-gray-400 italic">Belum tersedia</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.getElementById('searchInput').addEventListener('input', function () {
            let filter = this.value.toLowerCase();
            document.querySelectorAll('tbody tr').forEach(function (row) {
                row.style.display = row.textContent.toLowerCase().includes(filter) ? '' : 'none';
            });
        });
    </script>
</x-admin-layout>
