<x-admin-layout plain>
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

    {{-- Banner: gradien kiri sama dengan header (biru gelap ke merah), foto banner tetap terlihat di kanan --}}
    <div class="relative w-full rounded-2xl overflow-hidden shadow-sm mb-4 min-h-[9.5rem] md:min-h-[11rem]">
        <img src="{{ asset('images/banner-k3.png') }}" alt="Banner K3"
             class="absolute inset-0 w-full h-full object-cover object-center">
        <div class="absolute inset-0"
             style="background: linear-gradient(100deg, rgba(15,23,42,.94) 0%, rgba(15,23,42,.84) 34%, rgba(153,27,27,.64) 58%, rgba(153,27,27,0) 86%);"></div>
        <div class="relative flex h-full flex-col justify-center gap-3 px-5 py-5 md:px-6">
            <h1 class="text-2xl md:text-3xl font-extrabold text-white tracking-wide">Dashboard Admin K3</h1>
            <div class="relative w-full max-w-xs sm:w-72">
                <span class="pointer-events-none absolute left-3 top-2.5 text-gray-400">
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                        <circle cx="11" cy="11" r="7" />
                        <path d="M20 20l-3.5-3.5" />
                    </svg>
                </span>
                <input type="text" id="searchInput"
                       placeholder="Cari nama vendor/user..."
                       class="w-full pl-9 pr-3 py-2 rounded-lg border border-white bg-white/90 backdrop-blur text-xs focus:ring-2 focus:ring-red-400 shadow">
            </div>
        </div>
    </div>

<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 mb-4">

    <!-- 🔶 PROSES -->
    <div class="group bg-gradient-to-br from-yellow-500 to-orange-500 text-white p-4 rounded-2xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition duration-300 flex items-center justify-between">

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
    <div class="group bg-gradient-to-br from-emerald-500 to-green-600 text-white p-4 rounded-2xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition duration-300 flex items-center justify-between">

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
    <div class="group bg-gradient-to-br from-red-600 to-rose-500 text-white p-4 rounded-2xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition duration-300 flex items-center justify-between">

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
    <div class="group bg-gradient-to-br from-blue-600 to-indigo-600 text-white p-4 rounded-2xl shadow-sm hover:shadow-xl hover:-translate-y-1 transition duration-300 flex items-center justify-between">

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

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">

    <!-- LEFT (CHART AREA) -->
    <div class="lg:col-span-2 space-y-4">

        {{-- ===== TOP PENGAJUAN (vendor + karyawan, satu grafik garis) ===== --}}
        @php
            $topVendorRows = $topVendorUser->map(fn ($i) => ['name' => $i->name, 'total' => (int) $i->total_requests])->values();
            $topPgoRows = $topVendorPgo->map(fn ($i) => ['name' => $i->name, 'total' => (int) $i->total_requests])->values();
        @endphp
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-200">
            <div class="mb-3">
                <h2 class="text-base font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-chart-line text-blue-500"></i>
                    Top Pengajuan SIK
                </h2>
            </div>

            @if ($topVendorRows->isNotEmpty() || $topPgoRows->isNotEmpty())
                <div class="relative" style="height: 320px">
                    <canvas id="chartTop"></canvas>
                </div>
            @else
                <div class="py-10 text-center text-sm text-gray-500">Belum ada data pengajuan.</div>
            @endif
        </div>

        {{-- ===== TREN TAHUNAN ===== --}}
        @php
            $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            $yearTotal = array_sum($monthlyChart);
            $peakValue = $monthlyChart ? max($monthlyChart) : 0;
            $peakMonth = $peakValue > 0 ? $monthNames[array_search($peakValue, $monthlyChart)] : '-';
            $monthAvg = (int) round($yearTotal / 12);
        @endphp
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-200">
            <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                <h2 class="text-base font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-chart-line text-indigo-500"></i>
                    Tren Pengajuan Izin Kerja {{ $selectedYear }}
                </h2>
                <form method="GET">
                    <select name="year" onchange="this.form.submit()"
                            class="text-xs border border-gray-300 rounded-lg px-2 py-1 focus:ring-2 focus:ring-blue-500">
                        @foreach ($years as $year)
                            <option value="{{ $year }}" {{ $year == $selectedYear ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div class="grid grid-cols-3 gap-2 mb-3">
                <div class="rounded-xl bg-blue-50 px-3 py-2">
                    <p class="text-[10px] text-blue-700/80">Total setahun</p>
                    <p class="text-lg font-extrabold text-blue-700 leading-tight">{{ number_format($yearTotal, 0, ',', '.') }}</p>
                </div>
                <div class="rounded-xl bg-indigo-50 px-3 py-2">
                    <p class="text-[10px] text-indigo-700/80">Rata-rata / bulan</p>
                    <p class="text-lg font-extrabold text-indigo-700 leading-tight">{{ number_format($monthAvg, 0, ',', '.') }}</p>
                </div>
                <div class="rounded-xl bg-emerald-50 px-3 py-2">
                    <p class="text-[10px] text-emerald-700/80">Bulan tertinggi</p>
                    <p class="text-lg font-extrabold text-emerald-700 leading-tight">
                        {{ $peakMonth }}
                        @if ($peakValue > 0)<span class="text-xs font-semibold">({{ number_format($peakValue, 0, ',', '.') }})</span>@endif
                    </p>
                </div>
            </div>

            <div class="relative" style="height: 320px">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>

    </div>

    <!-- RIGHT SIDEBAR -->
    <div class="space-y-4">
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
<div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-200">
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
// TREN BULANAN (1 TAHUN)
// ======================
const ctxMonthly = document.getElementById('monthlyChart');

if (ctxMonthly) {
    const labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    const data = @json($monthlyChart);
    const year = @json((string) $selectedYear);
    const line = '#2563eb';

    new Chart(ctxMonthly, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total Pengajuan',
                data: data,
                borderColor: line,
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                backgroundColor(context) {
                    const { ctx, chartArea } = context.chart;
                    if (!chartArea) return 'rgba(37, 99, 235, 0.15)';
                    const g = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                    g.addColorStop(0, 'rgba(37, 99, 235, 0.28)');
                    g.addColorStop(1, 'rgba(37, 99, 235, 0.02)');
                    return g;
                },
                pointRadius: 4,
                pointHoverRadius: 7,
                pointBackgroundColor: '#fff',
                pointBorderColor: line,
                pointBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 1200, easing: 'easeOutQuart' },
            interaction: { intersect: false, mode: 'index' },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#111827',
                    padding: 10,
                    displayColors: false,
                    callbacks: {
                        title: (items) => labels[items[0].dataIndex] + ' ' + year,
                        label: (context) => context.raw + ' pengajuan'
                    }
                }
            },
            scales: {
                x: { grid: { display: false } },
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 },
                    grid: { color: 'rgba(0,0,0,0.05)' }
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
    // TOP PENGAJUAN: dua garis (vendor & PGO) berdasarkan peringkat
    // ======================
    const ctxTop = document.getElementById('chartTop');
    if (ctxTop) {
        const vendorRows = @json($topVendorRows);
        const pgoRows = @json($topPgoRows);
        const size = Math.max(vendorRows.length, pgoRows.length);
        const labels = Array.from({ length: size }, (_, i) => '#' + (i + 1));
        const series = [
            { label: 'Vendor', rows: vendorRows, color: '#16a34a', fill: '22, 163, 74' },
            { label: 'Karyawan (PGO)', rows: pgoRows, color: '#2563eb', fill: '37, 99, 235' }
        ];

        new Chart(ctxTop, {
            type: 'line',
            data: {
                labels: labels,
                datasets: series.map((item) => ({
                    label: item.label,
                    data: Array.from({ length: size }, (_, i) => item.rows[i] ? item.rows[i].total : null),
                    borderColor: item.color,
                    borderWidth: 3,
                    tension: 0.4,
                    cubicInterpolationMode: 'monotone',
                    fill: true,
                    backgroundColor(context) {
                        const { ctx, chartArea } = context.chart;
                        if (!chartArea) return 'rgba(' + item.fill + ', 0.12)';
                        const g = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                        g.addColorStop(0, 'rgba(' + item.fill + ', 0.22)');
                        g.addColorStop(1, 'rgba(' + item.fill + ', 0.02)');
                        return g;
                    },
                    pointRadius: 4,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: item.color,
                    pointBorderWidth: 2
                }))
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 1200, easing: 'easeOutQuart' },
                interaction: { intersect: false, mode: 'index' },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#111827',
                        padding: 10,
                        boxPadding: 4,
                        callbacks: {
                            title: (items) => 'Peringkat ' + (items[0].dataIndex + 1),
                            label: (context) => {
                                const row = series[context.datasetIndex].rows[context.dataIndex];
                                return row ? row.name + ': ' + row.total + ' pengajuan' : '';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        title: { display: true, text: 'Peringkat', color: '#6b7280', font: { size: 11 } }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 },
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    }
                }
            }
        });
    }
});
</script>
</x-admin-layout>
