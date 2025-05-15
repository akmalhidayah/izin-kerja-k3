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
                <p class="text-xl font-bold text-white">36 Pengajuan</p>
            </div>
        </div>
        <div class="bg-green-500 p-6 rounded-xl shadow-lg flex items-center gap-4">
            <div class="bg-white text-green-600 p-4 rounded-full shadow">
                <i class="fas fa-thumbs-up text-2xl"></i>
            </div>
            <div>
                <p class="text-sm text-white">SIK Terbit</p>
                <p class="text-xl font-bold text-white">21 SIK</p>
            </div>
        </div>
        <div class="bg-red-700 p-6 rounded-xl shadow-lg flex items-center gap-4">
            <div class="bg-white text-red-600 p-4 rounded-full shadow">
                <i class="fas fa-edit text-2xl"></i>
            </div>
            <div>
                <p class="text-sm text-white">Pengajuan Perlu Revisi</p>
                <p class="text-xl font-bold text-white">15 Pengajuan</p>
            </div>
        </div>
    </div>

    <!-- Table of Users and Their Progress -->
    <div class="bg-white p-6 rounded-xl shadow mb-8">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Status Izin Kerja Per User</h2>
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
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-2">PT. Biringkassi Raya</td>
                        <td class="px-4 py-2">Amar</td>
                        <td class="px-4 py-2"><span class="text-yellow-600 font-medium">Perlu Revisi</span></td>
                        <td class="px-4 py-2">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-yellow-500 h-2 rounded-full" style="width: 60%"></div>
                            </div>
                        </td>
                        <td class="px-4 py-2">
                            <button class="text-sm text-blue-600 hover:underline">Lihat SIK</button>
                        </td>
                    </tr>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-2">PT. Prima Karya Manunggal</td>
                        <td class="px-4 py-2">Irwan Mahardika</td>
                        <td class="px-4 py-2"><span class="text-green-600 font-medium">Terbit SIK</span></td>
                        <td class="px-4 py-2">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full" style="width: 100%"></div>
                            </div>
                        </td>
                        <td class="px-4 py-2">
                            <button class="text-sm text-blue-600 hover:underline">Lihat SIK</button>
                        </td>
                    </tr>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-2">PT. Topabbiring</td>
                        <td class="px-4 py-2">Irwan Mahardika</td>
                        <td class="px-4 py-2"><span class="text-green-600 font-medium">Terbit SIK</span></td>
                        <td class="px-4 py-2">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full" style="width: 100%"></div>
                            </div>
                        </td>
                        <td class="px-4 py-2">
                            <button class="text-sm text-blue-600 hover:underline">Lihat SIK</button>
                        </td>
                    </tr>
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
