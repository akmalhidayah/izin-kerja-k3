<x-admin-layout>
    @php
        $topVendorRequests = $topVendorRequests ?? collect();
        $totalRequests = $summaryRequests->count();
        $inProgress = $summaryRequests->whereIn('status', ['Menunggu', 'Diproses', 'Perlu Disetujui'])->count();
        $needRevision = $summaryRequests->where('status', 'Perlu Revisi')->count();
   $completed = $summaryRequests->where('is_sik_approved', true)->count();
        $avgProgress = round($summaryRequests->avg('progress') ?? 0);
        $recentRequests = $summaryRequests->sortByDesc('created_at')->take(5);
        $adminLoads = $summaryRequests->groupBy('handled_by')->map->count()->sortDesc()->take(5);
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
                <p class="text-2xl font-extrabold counter" data-value="{{ $inProgress }}">0</p>

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
                <p class="text-2xl font-extrabold counter" data-value="{{ $completed }}">0</p>

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
                <p class="text-2xl font-extrabold counter" data-value="{{ $needRevision }}">0</p>

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
                <p class="text-2xl font-extrabold counter" data-value="{{ $avgProgress }}" data-suffix="%">0%</p>

                <p class="text-[10px] text-white/70">Kinerja sistem</p>
            </div>
        </div>

        <i class="fas fa-chart-simple text-white/40 group-hover:text-white transition"></i>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

    <!-- LEFT (CHART AREA) -->
    <div class="lg:col-span-2 space-y-6">

        <!-- ========================= -->
        <!-- 🔥 CHART EXISTING -->
        <!-- ========================= -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
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
                    <div>
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

        <!-- ========================= -->
        <!-- 🔥 NEW: CHART BULANAN -->
        <!-- ========================= -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-chart-line text-indigo-500"></i>
Tren Pengajuan Izin Kerja ({{ $selectedYear }})
                </h2>
                 <!-- 🔥 DROPDOWN TAHUN -->
    <form method="GET">
        <select name="year" onchange="this.form.submit()"
            class="text-xs border border-gray-300 rounded-lg px-2 py-1 focus:ring-2 focus:ring-blue-500">

            @foreach ($years as $year)
                <option value="{{ $year }}" 
                    {{ $year == $selectedYear ? 'selected' : '' }}>
                    {{ $year }}
                </option>
            @endforeach

        </select>
    </form>

            </div>

            <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 pt-6 pb-8">
                <canvas id="monthlyChart" height="100"></canvas>
            </div>
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
                <i class="fas fa-bolt text-blue-500 text-xs"></i>
            </span>
            Aktivitas Terbaru
        </h3>
    </div>

    <div class="space-y-3">
        @forelse ($recentRequests as $item)

            @php
                // =========================
                // STATUS STYLE
                // =========================
                $badgeClass = match($item->status) {
                    'Perlu Revisi' => 'bg-yellow-100 text-yellow-700',
                    'Terbit SIK' => 'bg-green-100 text-green-700',
                    'Perlu Disetujui' => 'bg-blue-100 text-blue-700',
                    default => 'bg-gray-100 text-gray-700',
                };

                // =========================
                // ICON STATUS
                // =========================
                $icon = match($item->status) {
                    'Perlu Revisi' => 'fa-rotate-left text-yellow-500',
                    'Terbit SIK' => 'fa-check-circle text-green-500',
                    'Perlu Disetujui' => 'fa-hourglass-half text-blue-500',
                    default => 'fa-circle text-gray-400',
                };

                // =========================
                // STEP HUMAN READABLE
                // =========================
                $stepLabel = match($item->current_step_title) {
                    'op_spk' => 'Pengajuan dibuat',
                    'data_kontraktor' => 'Data kontraktor',
                    'bpjs' => 'Upload BPJS',
                    'jsa' => 'Analisis pekerjaan (JSA)',
                    'working_permit' => 'Izin kerja',
                    'sik' => 'Penerbitan SIK',
                    default => 'Proses berjalan'
                };

                // =========================
                // ACTIVITY MESSAGE (🔥 BARU)
                // =========================
                if ($item->status === 'Terbit SIK') {
                    $activityText = "SIK sudah terbit & selesai";
                } elseif ($item->status === 'Perlu Revisi') {
                    $activityText = "Perlu revisi pada tahap " . $stepLabel;
                } elseif ($item->status === 'Perlu Disetujui') {
                    $activityText = "Menunggu approval admin";
                } else {
                    $activityText = "Sedang di tahap " . $stepLabel;
                }
            @endphp

            <div class="flex gap-3 items-start p-2 rounded-lg hover:bg-gray-50 transition">

                <!-- TIMELINE DOT -->
                <div class="flex flex-col items-center mt-1">
                    <div class="w-2.5 h-2.5 rounded-full bg-blue-500 animate-pulse"></div>
                    <div class="w-[1px] flex-1 bg-gray-200 mt-1"></div>
                </div>

                <!-- CONTENT -->
                <div class="flex-1 min-w-0">

                    <!-- USER -->
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-semibold text-gray-800 truncate">
                            {{ $item->user_name }}
                        </p>

                        <span class="text-[9px] text-gray-400">
                            {{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}
                        </span>
                    </div>

                    <!-- ACTIVITY TEXT (🔥 UTAMA) -->
                    <p class="text-[11px] text-gray-600 mt-0.5">
                        {{ $activityText }}
                    </p>

                    <!-- DETAIL MINI -->
                    <div class="flex items-center gap-2 mt-1 flex-wrap">

                        <!-- STEP -->
                        <span class="text-[9px] text-gray-400">
                            Step: {{ $stepLabel }}
                        </span>

                        <!-- ADMIN -->
                        @if($item->handled_by && $item->handled_by !== '-')
                        <span class="text-[9px] text-gray-400">
                            • Admin: {{ $item->handled_by }}
                        </span>
                        @endif

                    </div>

                </div>

                <!-- RIGHT -->
                <div class="flex flex-col items-end gap-1">

                    <!-- ICON -->
                    <i class="fas {{ $icon }} text-xs"></i>

                    <!-- STATUS -->
                    <span class="px-2 py-0.5 rounded-full text-[9px] font-semibold {{ $badgeClass }}">
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
const counters = document.querySelectorAll('.counter');

    counters.forEach(counter => {
        const target = parseFloat(counter.getAttribute('data-value'));
        const suffix = counter.getAttribute('data-suffix') || '';
        let current = 0;

        const duration = 1200; // ms
        const startTime = performance.now();

        function animate(time) {
            const progress = Math.min((time - startTime) / duration, 1);

            // easing biar smooth (🔥 ini penting)
            const ease = 1 - Math.pow(1 - progress, 3);

            current = target * ease;

            // tampilkan angka
            counter.textContent = Math.floor(current) + suffix;

            if (progress < 1) {
                requestAnimationFrame(animate);
            } else {
                counter.textContent = target + suffix;
            }
        }

        requestAnimationFrame(animate);
    });
// ======================
// 🔥 MONTHLY CHART (TAHUNAN)
// ======================
const ctxMonthly = document.getElementById('monthlyChart');

if (ctxMonthly) {

    const labels = [
        'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
        'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
    ];

    const data = @json($monthlyChart);

    // 🔥 GRADIENT
    const gradient = ctxMonthly.getContext('2d').createLinearGradient(0, 0, 0, 200);
    gradient.addColorStop(0, 'rgba(37, 99, 235, 0.25)');
gradient.addColorStop(1, 'rgba(37, 99, 235, 0.03)');

    new Chart(ctxMonthly, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total Pengajuan',
                data: data,
                bborderColor: 'rgba(37, 99, 235, 1)',
                backgroundColor: gradient,
                fill: true,

                // 🔥 smooth line
                tension: 0.5,
                cubicInterpolationMode: 'monotone',

                // 🔥 titik
                pointRadius: 4,
                pointHoverRadius: 7,
                pointBackgroundColor: '#fff',
                pointBorderColor: 'rgba(37, 99, 235, 1)',
                pointBorderWidth: 2,

                borderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,

            // 🔥 animasi naik dari bawah
            animation: {
                duration: 1400,
                easing: 'easeInOutQuart'
            },
            animations: {
                y: {
                    from: 0
                }
            },

            interaction: {
                intersect: false,
                mode: 'nearest'
            },

            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#111827',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    padding: 10,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return context.raw + ' pengajuan';
                        }
                    }
                }
            },

            elements: {
                line: {
                    borderJoinStyle: 'round'
                }
            },

            scales: {
                x: {
                    grid: { display: false }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 10 // opsional
                    },
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    }
                }
            }
        }
    });
}
    // ======================
    // 🔵 PIE CHART ADMIN
    // ======================
    const ctx = document.getElementById('adminChart');

    if (ctx) {

        const dataValues = {!! json_encode(array_values($adminLoads->toArray())) !!};
        const labels = {!! json_encode(array_keys($adminLoads->toArray())) !!};

        const colors = [
            '#3b82f6', '#22c55e', '#f59e0b',
            '#ef4444', '#8b5cf6', '#06b6d4'
        ];

        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: dataValues,
                    backgroundColor: colors,
                    borderWidth: 0,
                    hoverOffset: 10 // 🔥 efek hover naik
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,

                // 🔥 ANIMASI MASUK
                animation: {
                    animateRotate: true,
                    animateScale: true,
                    duration: 1200,
                    easing: 'easeOutQuart'
                },

                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#111827',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        padding: 10,
                        callbacks: {
                            label: function(context) {
                                let total = context.dataset.data.reduce((a,b)=>a+b,0);
                                let value = context.raw;
                                let percent = Math.round((value / total) * 100);
                                return `${context.label}: ${value} (${percent}%)`;
                            }
                        }
                    }
                }
            }
        });
    }


    // ======================
    // 🔥 FUNCTION LINE CHART (UPGRADE)
    // ======================
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
                    borderWidth: 3, // 🔥 lebih tegas
                    tension: 0.5,   // 🔥 lebih smooth naik turun
                    fill: true,

                    // 🔥 titik lebih hidup
                    pointRadius: 4,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: color,
                    pointBorderWidth: 2,

                    cubicInterpolationMode: 'monotone' // 🔥 smooth curve
                }]
            },
            options: {
                responsive: true,

                // 🔥 ANIMASI NAIK DARI BAWAH
                animation: {
                    duration: 1400,
                    easing: 'easeInOutQuart'
                },

                animations: {
                    y: {
                        from: 0 // 🔥 efek naik dari bawah
                    }
                },

                interaction: {
                    intersect: false,
                    mode: 'nearest'
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

                elements: {
                    line: {
                        borderJoinStyle: 'round'
                    }
                },

                scales: {
                    x: {
                        ticks: { display: false },
                        grid: { display: false }
                    },
                    y: {
                        ticks: { display: false },
                        grid: {
                            color: 'rgba(0,0,0,0.04)'
                        }
                    }
                }
            }
        });
    }


    // ======================
    // 🔵 PGO (KARYAWAN)
    // ======================
    const ctxPgo = document.getElementById('chartPgo');
    if (ctxPgo) {
        createChart(
            ctxPgo,
            {!! json_encode($topVendorPgo->pluck('name')) !!},
            {!! json_encode($topVendorPgo->pluck('total_requests')) !!},
            'rgba(37, 99, 235, 1)'
        );
    }


    // ======================
    // 🟢 VENDOR
    // ======================
    const ctxVendor = document.getElementById('chartVendor');
    if (ctxVendor) {
        createChart(
            ctxVendor,
            {!! json_encode($topVendorUser->pluck('name')) !!},
            {!! json_encode($topVendorUser->pluck('total_requests')) !!},
            'rgba(22, 163, 74, 1)'
        );
    }

});
</script>
</x-admin-layout>
