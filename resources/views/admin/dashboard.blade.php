<x-admin-layout>
    @php
        $topVendorRequests = $topVendorRequests ?? collect();
        $totalRequests = $summaryRequests->count();
        $inProgress = $summaryRequests->whereIn('status', ['Menunggu', 'Diproses', 'Perlu Disetujui'])->count();
        $needRevision = $summaryRequests->where('status', 'Perlu Revisi')->count();
   $completed = $summaryRequests->where('is_sik_approved', true)->count();
        $avgProgress = round($summaryRequests->avg('progress') ?? 0);
        $recentRequests = $summaryRequests->sortByDesc('created_at')->take(5);
        $adminLoads = $summaryRequests->groupBy('handled_by')->map->count()->sortDesc()->take(3);
        $maxVendorRequest = max((int) ($topVendorRequests->max('total_requests') ?? 0), 1);
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

    <!-- 🔶 PROSES -->
    <div class="group bg-gradient-to-br from-yellow-500 to-orange-500 text-white p-5 rounded-2xl shadow-md hover:shadow-xl hover:-translate-y-1 transition duration-300 flex items-center justify-between">

        <div class="flex items-center gap-4">
            <div class="bg-white/20 p-3 rounded-xl backdrop-blur">
                <i class="fas fa-spinner text-lg animate-spin"></i>
            </div>

            <div>
                <p class="text-[11px] text-white/80">Pengajuan Proses</p>
                <p class="text-2xl font-extrabold">{{ $inProgress }}</p>

                <!-- 🔥 insight kecil -->
                <p class="text-[10px] text-white/70">Aktif saat ini</p>
            </div>
        </div>

        <i class="fas fa-arrow-up text-white/40 group-hover:text-white transition"></i>
    </div>

    <!-- 🟢 SELESAI -->
    <div class="group bg-gradient-to-br from-emerald-500 to-green-600 text-white p-5 rounded-2xl shadow-md hover:shadow-xl hover:-translate-y-1 transition duration-300 flex items-center justify-between">

        <div class="flex items-center gap-4">
            <div class="bg-white/20 p-3 rounded-xl">
                <i class="fas fa-check-circle text-lg"></i>
            </div>

            <div>
                <p class="text-[11px] text-white/80">SIK Terbit</p>
                <p class="text-2xl font-extrabold">{{ $completed }}</p>

                <p class="text-[10px] text-white/70">Dokumen selesai</p>
            </div>
        </div>

        <i class="fas fa-arrow-up text-white/40 group-hover:text-white transition"></i>
    </div>

    <!-- 🔴 REVISI -->
    <div class="group bg-gradient-to-br from-red-600 to-rose-500 text-white p-5 rounded-2xl shadow-md hover:shadow-xl hover:-translate-y-1 transition duration-300 flex items-center justify-between">

        <div class="flex items-center gap-4">
            <div class="bg-white/20 p-3 rounded-xl">
                <i class="fas fa-triangle-exclamation text-lg"></i>
            </div>

            <div>
                <p class="text-[11px] text-white/80">Perlu Revisi</p>
                <p class="text-2xl font-extrabold">{{ $needRevision }}</p>

                <p class="text-[10px] text-white/70">Butuh tindakan</p>
            </div>
        </div>

        <i class="fas fa-arrow-down text-white/40 group-hover:text-white transition"></i>
    </div>

    <!-- 🔵 PROGRESS -->
    <div class="group bg-gradient-to-br from-blue-600 to-indigo-600 text-white p-5 rounded-2xl shadow-md hover:shadow-xl hover:-translate-y-1 transition duration-300 flex items-center justify-between">

        <div class="flex items-center gap-4">
            <div class="bg-white/20 p-3 rounded-xl">
                <i class="fas fa-chart-line text-lg"></i>
            </div>

            <div>
                <p class="text-[11px] text-white/80">Rata-rata Progress</p>
                <p class="text-2xl font-extrabold">{{ $avgProgress }}%</p>

                <p class="text-[10px] text-white/70">Kinerja sistem</p>
            </div>
        </div>

        <i class="fas fa-chart-simple text-white/40 group-hover:text-white transition"></i>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

    <!-- LEFT (CHART 2 COL) -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 lg:col-span-2">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-semibold text-gray-800 flex items-center gap-2">
                <i class="fas fa-chart-area text-blue-500"></i>
                Statistik Pengajuan Vendor & Karyawan
            </h2>
        </div>

        <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 pt-6 pb-8">

            @if ($topVendorPgo->isNotEmpty() || $topVendorUser->isNotEmpty())


                <!-- 🟢 VENDOR -->
                  <div class="mb-6">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                        <h3 class="text-xs font-semibold text-gray-700">Top 10 Pengajuan SIK Vendor</h3>
                    </div>
                    <canvas id="chartVendor" height="120"></canvas>
                </div>
                 <!-- 🔵 PGO -->
                <div class="mb-8">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-3 h-3 bg-blue-500 rounded-full"></span>
                        <h3 class="text-xs font-semibold text-gray-700">Top 5 Pengajuan SIK Karyawan</h3>
                    </div>
                    <canvas id="chartPgo" height="120"></canvas>
                </div>

            @else
                <div class="py-10 text-center text-sm text-gray-500">
                    Belum ada data vendor.
                </div>
            @endif

        </div>
    </div>

    <!-- RIGHT SIDEBAR -->
    <div class="space-y-6">

<!-- AKTIVITAS -->
<div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
    
    <!-- HEADER -->
    <div class="flex items-center justify-between mb-3">
        <h3 class="text-xs font-semibold text-gray-700 flex items-center gap-2">
            <span class="w-6 h-6 flex items-center justify-center rounded-lg bg-blue-100">
                <i class="fas fa-clock text-blue-500 text-xs"></i>
            </span>
            Aktivitas Terbaru
        </h3>
    </div>

    <div class="space-y-2">
        @forelse ($recentRequests as $item)
            @php
                $badgeClass = match($item->status) {
                    'Perlu Revisi' => 'bg-yellow-100 text-yellow-700',
                    'Terbit SIK' => 'bg-green-100 text-green-700',
                    'Perlu Disetujui' => 'bg-blue-100 text-blue-700',
                    'Diproses' => 'bg-sky-100 text-sky-700',
                    default => 'bg-gray-100 text-gray-700',
                };

                $icon = match($item->status) {
                    'Perlu Revisi' => 'fa-rotate-left text-yellow-500',
                    'Terbit SIK' => 'fa-check-circle text-green-500',
                    'Perlu Disetujui' => 'fa-hourglass-half text-blue-500',
                    'Diproses' => 'fa-spinner text-sky-500',
                    default => 'fa-circle text-gray-400',
                };

                $stepLabel = match($item->current_step_title) {
                    'op_spk' => 'Pengajuan dibuat',
                    'jsa' => 'Analisis pekerjaan (JSA)',
                    'working_permit' => 'Izin kerja diproses',
                    default => 'Tahap proses berjalan'
                };
            @endphp

            <div class="flex items-center justify-between gap-2 p-2 rounded-lg hover:bg-gray-50 transition">

                <!-- LEFT -->
                <div class="flex items-center gap-2 min-w-0">

                    <!-- ICON -->
                    <div class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100">
                        <i class="fas {{ $icon }} text-xs"></i>
                    </div>

                    <!-- TEXT -->
                    <div class="min-w-0">
                        <div class="text-xs font-semibold text-gray-800 truncate">
                            {{ $item->user_name }}
                        </div>

                        <div class="text-[10px] text-gray-500">
                            {{ $stepLabel }}
                        </div>

                        <div class="text-[9px] text-gray-400">
                            {{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}
                        </div>
                    </div>
                </div>

                <!-- RIGHT -->
                <div class="flex flex-col items-end gap-1">

                    <!-- STATUS -->
                    <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[9px] font-semibold {{ $badgeClass }}">
                        <i class="fas fa-circle text-[5px]"></i>
                        {{ $item->status }}
                    </span>

                </div>
            </div>

        @empty
            <div class="text-[11px] text-gray-500 text-center py-4">
                Belum ada aktivitas terbaru.
            </div>
        @endforelse
    </div>
</div>

<!-- SIK -->
<div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
    <h3 class="text-sm font-semibold text-gray-800 mb-4 flex items-center gap-2">
        <i class="fas fa-chart-pie text-blue-500"></i>
        SIK Yang Ditangani
    </h3>

    <!-- 🔥 CHART -->
    <div class="flex items-center justify-center">
        <canvas id="adminChart" height="160"></canvas>
    </div>

    <!-- 🔥 LEGEND CUSTOM -->
    <div class="mt-5 space-y-3 text-xs">
        @php
            $total = collect($adminLoads)->sum() ?: 1;
        @endphp

        @foreach ($adminLoads as $name => $count)
            @php 
                $percent = round(($count / $total) * 100);
            @endphp

            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                    <span class="text-gray-700">{{ $name ?: '-' }}</span>
                </div>

                <span class="text-[11px] font-semibold text-gray-600">
                    {{ $count }} ({{ $percent }}%)
                </span>
            </div>
        @endforeach
    </div>
</div>
   <script>
document.addEventListener("DOMContentLoaded", function () {



    const ctx = document.getElementById('adminChart');

    if (ctx) {

        const dataValues = {!! json_encode(array_values($adminLoads->toArray())) !!};
        const labels = {!! json_encode(array_keys($adminLoads->toArray())) !!};

        const colors = [
            '#3b82f6', '#22c55e', '#f59e0b',
            '#ef4444', '#8b5cf6', '#06b6d4'
        ];

        new Chart(ctx, {
            type: 'pie', // 🔥 FULL LINGKARAN
            data: {
                labels: labels,
                datasets: [{
                    data: dataValues,
                    backgroundColor: colors,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // 🔥 penting biar ikut container
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#111827',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        callbacks: {
                            label: function(context) {
                                let total = context.dataset.data.reduce((a,b)=>a+b,0);
                                let value = context.raw;
                                let percent = Math.round((value / total) * 100);
                                return `${value} (${percent}%)`;
                            }
                        }
                    }
                }
            }
        });
    }


   function createChart(ctx, labels, data, color) {

    const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 200);
    gradient.addColorStop(0, color.replace('1)', '0.25)'));
    gradient.addColorStop(1, color.replace('1)', '0.02)'));

    return new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                borderColor: color,
                backgroundColor: gradient,
                borderWidth: 2.5,
                tension: 0.45,
                fill: true,

                // 🔥 titik halus
                pointRadius: 3,
                pointHoverRadius: 6,
                pointBackgroundColor: color,
                pointBorderWidth: 0
            }]
        },
        options: {
            responsive: true,
            animation: {
                duration: 1000,
                easing: 'easeOutCubic'
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#111827',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    padding: 10,
                    displayColors: false
                }
            },
            interaction: {
                intersect: false,
                mode: 'nearest'
            },
            scales: {
                x: {
                    ticks: { display: false },
                    grid: {
                        display: false
                    }
                },
                y: {
                    ticks: { display: false },
                    grid: {
                        color: 'rgba(0,0,0,0.04)' // 🔥 garis halus banget
                    }
                }
            }
        }
    });
}

    // 🔵 PGO (KARYAWAN)
    const ctxPgo = document.getElementById('chartPgo');
    if (ctxPgo) {
        createChart(
            ctxPgo,
            {!! json_encode($topVendorPgo->pluck('name')) !!},
            {!! json_encode($topVendorPgo->pluck('total_requests')) !!},
            'rgba(37, 99, 235, 1)' // biru
        );
    }

    // 🟢 VENDOR
    const ctxVendor = document.getElementById('chartVendor');
    if (ctxVendor) {
        createChart(
            ctxVendor,
            {!! json_encode($topVendorUser->pluck('name')) !!},
            {!! json_encode($topVendorUser->pluck('total_requests')) !!},
            'rgba(22, 163, 74, 1)' // hijau
        );
    }

});
</script>
</x-admin-layout>
