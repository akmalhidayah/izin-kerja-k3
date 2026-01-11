<x-admin-layout>
    @php
        $totalRequests = $summaryRequests->count();
        $inProgress = $summaryRequests->whereIn('status', ['Menunggu', 'Perlu Revisi'])->count();
        $needRevision = $summaryRequests->where('status', 'Perlu Revisi')->count();
        $completed = $summaryRequests->where('current_step', '>=', $totalSteps)->count();
        $avgProgress = round($summaryRequests->avg('progress') ?? 0);
        $recentRequests = $summaryRequests->sortByDesc('created_at')->take(5);
        $adminLoads = $summaryRequests->groupBy('handled_by')->map->count()->sortDesc()->take(3);
    @endphp

    <div class="relative w-full rounded-2xl overflow-hidden shadow mb-6">
        <img src="{{ asset('images/banner-k3.png') }}" alt="Banner K3"
             class="w-full h-56 object-cover object-center opacity-80">
        <div class="absolute inset-0 bg-gradient-to-r from-gray-900/60 via-gray-900/30 to-transparent"></div>
        <div class="absolute inset-0 flex flex-col justify-center gap-3 px-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-white tracking-wide">Dashboard Admin K3</h1>
                <p class="text-xs text-gray-100 mt-1">Ringkasan status pengajuan dan performa penanganan.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <span class="inline-flex items-center rounded-full bg-white/90 text-gray-800 text-[11px] px-3 py-1">
                    Total: {{ $totalRequests }}
                </span>
                <span class="inline-flex items-center rounded-full bg-white/90 text-gray-800 text-[11px] px-3 py-1">
                    Proses: {{ $inProgress }}
                </span>
                <span class="inline-flex items-center rounded-full bg-white/90 text-gray-800 text-[11px] px-3 py-1">
                    Revisi: {{ $needRevision }}
                </span>
                <span class="inline-flex items-center rounded-full bg-white/90 text-gray-800 text-[11px] px-3 py-1">
                    Selesai: {{ $completed }}
                </span>
                <span class="inline-flex items-center rounded-full bg-white/90 text-gray-800 text-[11px] px-3 py-1">
                    Rata-rata: {{ $avgProgress }}%
                </span>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <div class="relative">
                    <span class="pointer-events-none absolute left-3 top-2.5 text-gray-400">
                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                            <circle cx="11" cy="11" r="7" />
                            <path d="M20 20l-3.5-3.5" />
                        </svg>
                    </span>
                    <input type="text" id="searchInput"
                           placeholder="Cari nama vendor/user..."
                           class="pl-9 pr-3 py-2 rounded-lg border border-white bg-white/90 backdrop-blur text-xs w-72 focus:ring-2 focus:ring-red-400 shadow">
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-br from-yellow-500 to-orange-500 text-white p-5 rounded-2xl shadow-lg flex items-center gap-4">
            <div class="bg-white/20 p-3 rounded-xl">
                <i class="fas fa-spinner text-lg"></i>
            </div>
            <div>
                <p class="text-[11px] text-white/80">Pengajuan Proses</p>
                <p class="text-xl font-extrabold">{{ $inProgress }}</p>
            </div>
        </div>
        <div class="bg-gradient-to-br from-emerald-500 to-green-600 text-white p-5 rounded-2xl shadow-lg flex items-center gap-4">
            <div class="bg-white/20 p-3 rounded-xl">
                <i class="fas fa-thumbs-up text-lg"></i>
            </div>
            <div>
                <p class="text-[11px] text-white/80">SIK Terbit</p>
                <p class="text-xl font-extrabold">{{ $completed }}</p>
            </div>
        </div>
        <div class="bg-gradient-to-br from-red-600 to-rose-500 text-white p-5 rounded-2xl shadow-lg flex items-center gap-4">
            <div class="bg-white/20 p-3 rounded-xl">
                <i class="fas fa-edit text-lg"></i>
            </div>
            <div>
                <p class="text-[11px] text-white/80">Perlu Revisi</p>
                <p class="text-xl font-extrabold">{{ $needRevision }}</p>
            </div>
        </div>
        <div class="bg-gradient-to-br from-blue-600 to-indigo-600 text-white p-5 rounded-2xl shadow-lg flex items-center gap-4">
            <div class="bg-white/20 p-3 rounded-xl">
                <i class="fas fa-chart-line text-lg"></i>
            </div>
            <div>
                <p class="text-[11px] text-white/80">Rata-rata Progress</p>
                <p class="text-xl font-extrabold">{{ $avgProgress }}%</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-semibold text-gray-800">Izin Kerja Yang Sudah Terbit SIK</h2>
                <span class="text-[11px] text-gray-500">Total: {{ $requests->count() }}</span>
            </div>
            <div class="overflow-x-auto rounded-xl border border-gray-200">
                <table class="min-w-full table-auto text-xs">
                    <thead class="bg-gray-50 text-gray-700 text-[11px] uppercase tracking-wider">
                        <tr>
                            <th class="px-4 py-3 text-left">Vendor</th>
                            <th class="px-4 py-3 text-left">Handle On</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-left">Progress</th>
                            <th class="px-4 py-3 text-left">Surat Izin Kerja</th>
                        </tr>
                    </thead>
                    <tbody id="dashboardTableBody">
                        @forelse ($requests as $request)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3">{{ $request->user_name }}</td>
                                <td class="px-4 py-3">{{ $request->handled_by }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $statusClass = match($request->status) {
                                            'Perlu Revisi' => 'bg-yellow-100 text-yellow-700',
                                            'Selesai', 'Disetujui', 'Terbit SIK' => 'bg-green-100 text-green-700',
                                            default => 'bg-gray-100 text-gray-700',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $statusClass }}">
                                        {{ $request->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="h-2 rounded-full {{ $request->progress == 100 ? 'bg-green-500' : 'bg-yellow-500' }}" style="width: {{ $request->progress }}%"></div>
                                    </div>
                                    <div class="text-[11px] text-gray-500 mt-1">{{ $request->current_step }}/{{ $totalSteps }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('admin.permintaansik.viewSik', $request->id) }}" target="_blank"
                                       class="inline-flex items-center gap-2 text-blue-600 hover:underline text-[11px]">
                                        <i class="fas fa-eye"></i>
                                        Lihat SIK
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-xs text-gray-500">
                                    Belum ada data untuk periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                <h3 class="text-sm font-semibold text-gray-800 mb-4">Aktivitas Terbaru</h3>
                <div class="space-y-3 text-xs">
                    @forelse ($recentRequests as $item)
                        @php
                            $badgeClass = match($item->status) {
                                'Perlu Revisi' => 'bg-yellow-100 text-yellow-700',
                                'Terbit SIK' => 'bg-green-100 text-green-700',
                                default => 'bg-gray-100 text-gray-700',
                            };
                        @endphp
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <div class="font-semibold text-gray-900">{{ $item->user_name }}</div>
                                <div class="text-[11px] text-gray-500">
                                    {{ $item->current_step_title ?? 'Belum Diketahui' }}
                                </div>
                            </div>
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $badgeClass }}">
                                {{ $item->status }}
                            </span>
                        </div>
                    @empty
                        <div class="text-[11px] text-gray-500">Belum ada aktivitas terbaru.</div>
                    @endforelse
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                <h3 class="text-sm font-semibold text-gray-800 mb-4">SIK Yang Ditangani</h3>
                <div class="space-y-2 text-xs">
                    @forelse ($adminLoads as $name => $count)
                        <div class="flex items-center justify-between">
                            <span class="text-gray-700">{{ $name ?: '-' }}</span>
                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-[11px] text-gray-700">
                                {{ $count }}
                            </span>
                        </div>
                    @empty
                        <div class="text-[11px] text-gray-500">Belum ada data.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <script>
        const searchInput = document.getElementById('searchInput');
        const tableBody = document.getElementById('dashboardTableBody');
        if (searchInput && tableBody) {
            searchInput.addEventListener('input', function () {
                const filter = this.value.toLowerCase();
                tableBody.querySelectorAll('tr').forEach(function (row) {
                    row.style.display = row.textContent.toLowerCase().includes(filter) ? '' : 'none';
                });
            });
        }
    </script>
</x-admin-layout>
