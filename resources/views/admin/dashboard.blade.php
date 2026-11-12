<x-admin-layout plain>
    @php
        // Beban admin sepanjang waktu (donat "SIK Yang Ditangani").
        $adminLoads = $summaryRequests->groupBy('handled_by')->map->count()->sortDesc()->take(5);
        $pieColors = ['#2563eb', '#16a34a', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4'];
        $pieTotal = (int) ($adminLoads->sum() ?: 0);

        $topVendorRows = $topVendorUser->map(fn ($i) => ['name' => $i->name, 'total' => (int) $i->total_requests])->values();
        $topPgoRows = $topVendorPgo->map(fn ($i) => ['name' => $i->name, 'total' => (int) $i->total_requests])->values();

        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $yearTotal = array_sum($monthlyChart);
        $peakValue = $monthlyChart ? max($monthlyChart) : 0;
        $peakMonth = $peakValue > 0 ? $monthNames[array_search($peakValue, $monthlyChart)] : '-';
        $monthsElapsed = max($lastMonth, 1);
        $monthAvg = (int) round($yearTotal / $monthsElapsed);

        // Kartu ringkas: satu warna per makna, dipakai juga oleh tab di halaman daftar.
        $cardDefs = [
            'menunggu' => ['label' => 'Menunggu Persetujuan', 'grad' => 'from-amber-500 to-orange-500', 'icon' => 'fa-hourglass-half', 'kondisi' => 'perlu_tindakan'],
            'belum_ditangani' => ['label' => 'Belum Ditangani', 'grad' => 'from-violet-600 to-purple-600', 'icon' => 'fa-user-clock', 'kondisi' => 'belum_ditangani'],
            'revisi' => ['label' => 'Perlu Revisi', 'grad' => 'from-red-600 to-rose-500', 'icon' => 'fa-triangle-exclamation', 'kondisi' => 'revisi'],
            'selesai' => ['label' => 'Selesai Bulan Ini', 'grad' => 'from-emerald-500 to-green-600', 'icon' => 'fa-circle-check', 'kondisi' => 'riwayat'],
        ];
    @endphp

    {{-- ===== BARIS ATAS: peringatan mandek + pencarian cepat ===== --}}
    <div class="mb-4 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        @if ($stale['total'] > 0)
            <div class="flex min-w-0 flex-wrap items-center gap-x-3 gap-y-1 rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-900">
                <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-amber-500 text-white">
                    <i class="fas fa-bell text-[11px]"></i>
                </span>
                <span>
                    <b>{{ $stale['total'] }}</b> pengajuan menunggu lebih dari {{ $stale['days'] }} hari tanpa perubahan.
                </span>
                <span class="flex items-center gap-1.5">
                    @if ($stale['vendor'] > 0)
                        <a href="{{ route('admin.permintaansik', ['kondisi' => 'mandek']) }}"
                           class="rounded-full bg-white px-2 py-0.5 font-semibold text-amber-800 ring-1 ring-amber-200 hover:bg-amber-100">Vendor {{ $stale['vendor'] }}</a>
                    @endif
                    @if ($stale['pgo'] > 0)
                        <a href="{{ route('admin.permintaansikpgo', ['kondisi' => 'mandek']) }}"
                           class="rounded-full bg-white px-2 py-0.5 font-semibold text-amber-800 ring-1 ring-amber-200 hover:bg-amber-100">Karyawan {{ $stale['pgo'] }}</a>
                    @endif
                </span>
            </div>
        @else
            <div class="flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs text-emerald-800">
                <i class="fas fa-circle-check text-emerald-500"></i>
                Tidak ada pengajuan yang mandek lebih dari {{ $stale['days'] }} hari.
            </div>
        @endif

        <form method="GET" action="{{ route('admin.permintaansik') }}" id="quickSearch"
              data-vendor="{{ route('admin.permintaansik') }}" data-pgo="{{ route('admin.permintaansikpgo') }}"
              class="flex w-full shrink-0 items-stretch gap-2 lg:w-auto">
            <select name="target" class="rounded-lg border border-gray-300 bg-white px-2 py-2 text-xs focus:ring-2 focus:ring-red-300">
                <option value="vendor">Vendor</option>
                <option value="pgo">Karyawan</option>
            </select>
            <div class="relative min-w-0 flex-1 lg:w-72">
                <span class="pointer-events-none absolute left-3 top-2.5 text-gray-400">
                    <i class="fas fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" name="search" placeholder="Cari nomor SPK/PO atau nama..."
                       class="w-full rounded-lg border border-gray-300 bg-white py-2 pl-8 pr-3 text-xs focus:ring-2 focus:ring-red-300">
            </div>
            <button type="submit" class="rounded-lg bg-red-600 px-3 py-2 text-xs font-semibold text-white hover:bg-red-700">Cari</button>
        </form>
    </div>

    {{-- ===== KARTU RINGKAS (di HP bisa digeser ke samping) ===== --}}
    <div class="-mx-3 mb-4 flex snap-x snap-mandatory gap-3 overflow-x-auto px-3 pb-1 sm:mx-0 sm:px-0 md:grid md:grid-cols-4 md:overflow-visible">
        @foreach ($cardDefs as $key => $def)
            @php $card = $cards[$key]; @endphp
            <div class="min-w-[78%] snap-start rounded-2xl bg-gradient-to-br {{ $def['grad'] }} p-4 text-white shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl sm:min-w-[46%] md:min-w-0">
                <a href="{{ route('admin.permintaansik', ['kondisi' => $def['kondisi']]) }}" class="flex items-center gap-4">
                    <div class="rounded-xl bg-white/20 p-3 backdrop-blur">
                        <i class="fas {{ $def['icon'] }} text-lg"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[11px] text-white/85">{{ $def['label'] }}</p>
                        <p class="text-2xl font-extrabold leading-tight counter" data-value="{{ $card['total'] }}">0</p>
                        <p class="truncate text-[10px] text-white/75">{{ $card['note'] }}</p>
                    </div>
                </a>
                <div class="mt-3 flex items-center gap-1.5 text-[10px]">
                    <a href="{{ route('admin.permintaansik', ['kondisi' => $def['kondisi']]) }}"
                       class="rounded-full bg-white/15 px-2 py-0.5 font-semibold hover:bg-white/30">Vendor <span class="font-extrabold">{{ $card['vendor'] }}</span></a>
                    <a href="{{ route('admin.permintaansikpgo', ['kondisi' => $def['kondisi']]) }}"
                       class="rounded-full bg-white/15 px-2 py-0.5 font-semibold hover:bg-white/30">Karyawan <span class="font-extrabold">{{ $card['pgo'] }}</span></a>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mb-4 grid grid-cols-1 gap-4 lg:grid-cols-3">

        {{-- ===================== KOLOM KIRI: GRAFIK ===================== --}}
        <div class="flex flex-col gap-4 lg:col-span-2">

            {{-- Top pengajuan --}}
            <div class="overflow-hidden rounded-2xl border border-blue-200 bg-gradient-to-br from-blue-50 to-sky-100/60 p-4 shadow-sm">
                <div class="mb-3 flex items-center gap-2">
                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-blue-600 text-white">
                        <i class="fas fa-chart-line text-xs"></i>
                    </span>
                    <h2 class="text-xs font-bold uppercase tracking-wide text-blue-900">Top Pengajuan SIK</h2>
                    <span class="ml-auto flex items-center gap-3 text-[10px] text-gray-500">
                        <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-green-600"></span>Vendor</span>
                        <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-blue-600"></span>Karyawan</span>
                    </span>
                </div>

                @if ($topVendorRows->isNotEmpty() || $topPgoRows->isNotEmpty())
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-[minmax(0,1fr)_200px]">
                        <div class="relative h-56 md:h-72">
                            <canvas id="chartTop"></canvas>
                        </div>
                        <div class="grid grid-cols-2 gap-3 md:grid-cols-1">
                            @foreach ([['Vendor', $topVendorRows, 'green'], ['Karyawan', $topPgoRows, 'blue']] as [$label, $rows, $color])
                                <div class="rounded-xl bg-white/70 p-2.5 ring-1 ring-blue-100">
                                    <p class="mb-1.5 text-[10px] font-semibold uppercase tracking-wide {{ $color === 'green' ? 'text-green-700' : 'text-blue-700' }}">3 teratas {{ $label }}</p>
                                    <ol class="space-y-1.5">
                                        @forelse ($rows->take(3) as $row)
                                            <li class="flex items-center gap-2 text-[11px]">
                                                <span class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-md text-[10px] font-bold text-white {{ $color === 'green' ? 'bg-green-600' : 'bg-blue-600' }}">{{ $loop->iteration }}</span>
                                                <span class="min-w-0 flex-1 truncate text-gray-700" title="{{ $row['name'] }}">{{ $row['name'] }}</span>
                                                <span class="shrink-0 font-semibold text-gray-800">{{ $row['total'] }}</span>
                                            </li>
                                        @empty
                                            <li class="text-[11px] text-gray-400">Belum ada data.</li>
                                        @endforelse
                                    </ol>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="py-10 text-center text-sm text-gray-500">Belum ada data pengajuan.</div>
                @endif
            </div>

            {{-- Tren tahunan --}}
            <div class="overflow-hidden rounded-2xl border border-indigo-200 bg-gradient-to-br from-indigo-50 to-violet-100/60 p-4 shadow-sm">
                <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-indigo-600 text-white">
                            <i class="fas fa-chart-area text-xs"></i>
                        </span>
                        <h2 class="text-xs font-bold uppercase tracking-wide text-indigo-900">Tren Pengajuan Izin Kerja {{ $selectedYear }}</h2>
                    </div>
                    <form method="GET">
                        <select name="year" onchange="this.form.submit()"
                                class="rounded-lg border border-gray-300 px-2 py-1 text-xs focus:ring-2 focus:ring-indigo-400">
                            @foreach ($years as $year)
                                <option value="{{ $year }}" {{ $year == $selectedYear ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>

                <div class="mb-3 grid grid-cols-3 gap-2">
                    <div class="rounded-xl bg-white/70 px-3 py-2 ring-1 ring-indigo-100">
                        <p class="text-[10px] text-indigo-700/80">Total setahun</p>
                        <p class="text-lg font-extrabold leading-tight text-indigo-700">{{ number_format($yearTotal, 0, ',', '.') }}</p>
                    </div>
                    <div class="rounded-xl bg-white/70 px-3 py-2 ring-1 ring-indigo-100">
                        <p class="text-[10px] text-indigo-700/80">Rata-rata / bulan</p>
                        <p class="text-lg font-extrabold leading-tight text-indigo-700">{{ number_format($monthAvg, 0, ',', '.') }}</p>
                    </div>
                    <div class="rounded-xl bg-white/70 px-3 py-2 ring-1 ring-indigo-100">
                        <p class="text-[10px] text-indigo-700/80">Bulan tertinggi</p>
                        <p class="text-lg font-extrabold leading-tight text-indigo-700">
                            {{ $peakMonth }}
                            @if ($peakValue > 0)<span class="text-xs font-semibold">({{ number_format($peakValue, 0, ',', '.') }})</span>@endif
                        </p>
                    </div>
                </div>

                <div class="relative h-56 md:h-80">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>

            <div class="grid flex-1 grid-cols-1 gap-4 md:grid-cols-2">

                {{-- Permit per jenis (lingkaran berlapis) --}}
                @php
                    $ringRows = collect($permitTypes['rows'])->filter(fn ($r) => $r['count'] > 0)->take(5)->values();
                @endphp
                <div class="overflow-hidden rounded-2xl border border-rose-200 bg-gradient-to-br from-rose-50 to-pink-100/60 p-4 shadow-sm">
                    <div class="mb-3 flex items-center gap-2">
                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-rose-600 text-white">
                            <i class="fas fa-layer-group text-xs"></i>
                        </span>
                        <h2 class="text-xs font-bold uppercase tracking-wide text-rose-900">Permit per Jenis</h2>
                        <span class="ml-auto text-[10px] text-rose-700/70">Tahun {{ $selectedYear }}</span>
                    </div>

                    @if ($permitTypes['total'] > 0)
                        <div class="flex items-center gap-4">
                            <div class="relative h-36 w-36 shrink-0">
                                <canvas id="permitTypeChart"></canvas>
                                <div class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center">
                                    <span class="text-lg font-extrabold leading-none text-gray-800">{{ number_format($permitTypes['total'], 0, ',', '.') }}</span>
                                    <span class="mt-0.5 text-[9px] uppercase tracking-wide text-gray-400">permit</span>
                                </div>
                            </div>
                            <div class="min-w-0 flex-1 rounded-xl bg-white/70 p-2.5 ring-1 ring-rose-100">
                                <p class="text-[10px] text-rose-700/80">Risiko tinggi</p>
                                <p class="text-xl font-extrabold leading-tight text-rose-700">{{ number_format($permitTypes['risk_total'], 0, ',', '.') }}</p>
                                <p class="text-[10px] text-gray-500">
                                    {{ round($permitTypes['risk_total'] / $permitTypes['total'] * 100) }}% dari semua permit
                                </p>
                            </div>
                        </div>

                        <div class="mt-3 grid grid-cols-2 gap-x-3 gap-y-1.5 text-[11px]">
                            @foreach ($permitTypes['rows'] as $row)
                                <div class="flex items-center gap-1.5 {{ $row['count'] === 0 ? 'opacity-40' : '' }}">
                                    <span class="h-2.5 w-2.5 shrink-0 rounded-full" style="background: {{ $row['color'] }}"></span>
                                    <span class="min-w-0 flex-1 truncate text-gray-700">
                                        {{ $row['label'] }}
                                        @if ($row['risk'])<i class="fas fa-triangle-exclamation text-[8px] text-rose-500" title="Risiko tinggi"></i>@endif
                                    </span>
                                    <span class="shrink-0 font-semibold text-gray-800">{{ $row['count'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-10 text-center text-[11px] text-gray-500">Belum ada permit pada tahun {{ $selectedYear }}.</div>
                    @endif
                </div>

                {{-- Pekerjaan berlangsung hari ini --}}
                <div class="overflow-hidden rounded-2xl border border-emerald-200 bg-gradient-to-br from-emerald-50 to-green-100/60 p-4 shadow-sm">
                    <div class="mb-3 flex items-center gap-2">
                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-emerald-600 text-white">
                            <i class="fas fa-helmet-safety text-xs"></i>
                        </span>
                        <h2 class="text-xs font-bold uppercase tracking-wide text-emerald-900">Pekerjaan Berlangsung Hari Ini</h2>
                    </div>

                    <div class="mb-3 grid grid-cols-3 gap-2">
                        <div class="rounded-xl bg-white/70 px-2.5 py-2 ring-1 ring-emerald-100">
                            <p class="text-[10px] text-emerald-700/80">Pekerjaan</p>
                            <p class="text-lg font-extrabold leading-tight text-emerald-700">{{ $ongoing['total'] }}</p>
                        </div>
                        <div class="rounded-xl bg-white/70 px-2.5 py-2 ring-1 ring-emerald-100">
                            <p class="text-[10px] text-emerald-700/80">Pekerja</p>
                            <p class="text-lg font-extrabold leading-tight text-emerald-700">{{ number_format($ongoing['workers'], 0, ',', '.') }}</p>
                        </div>
                        <div class="rounded-xl bg-white/70 px-2.5 py-2 ring-1 ring-emerald-100">
                            <p class="text-[10px] text-rose-700/80">Risiko tinggi</p>
                            <p class="text-lg font-extrabold leading-tight text-rose-600">{{ $ongoing['risk'] }}</p>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        @forelse ($ongoing['rows'] as $job)
                            <a href="{{ route('admin.permintaansik.show', $job->notification_id) }}"
                               class="block rounded-xl bg-white/70 px-2.5 py-2 ring-1 ring-emerald-100 transition hover:bg-white">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="min-w-0 truncate text-[11px] font-semibold text-gray-800">
                                        {{ $job->name }}
                                        <span class="font-normal text-gray-400">· {{ $job->usertype === 'pgo' ? 'Karyawan' : 'Vendor' }}</span>
                                    </p>
                                    <span class="shrink-0 text-[10px] font-semibold text-emerald-700">
                                        {{ $job->workers !== null ? $job->workers.' org' : '-' }}
                                    </span>
                                </div>
                                <p class="mt-0.5 truncate text-[10px] text-gray-500">
                                    <i class="fas fa-location-dot text-[9px] text-gray-400"></i>
                                    {{ $job->location ?: 'Lokasi belum diisi' }}
                                    @if ($job->until)<span class="text-gray-400"> · s.d. {{ $job->until }}</span>@endif
                                </p>
                                @if ($job->types)
                                    <div class="mt-1 flex flex-wrap gap-1">
                                        @foreach (array_slice($job->types, 0, 3) as $type)
                                            <span class="rounded-full px-1.5 py-0.5 text-[9px] font-semibold text-white" style="background: {{ $type['color'] }}">{{ $type['label'] }}</span>
                                        @endforeach
                                        @if (count($job->types) > 3)
                                            <span class="rounded-full bg-gray-200 px-1.5 py-0.5 text-[9px] font-semibold text-gray-600">+{{ count($job->types) - 3 }}</span>
                                        @endif
                                    </div>
                                @endif
                            </a>
                        @empty
                            <div class="py-6 text-center text-[11px] text-gray-500">Tidak ada pekerjaan yang dijadwalkan hari ini.</div>
                        @endforelse
                    </div>

                    @if ($ongoing['total'] > $ongoing['rows']->count())
                        <p class="mt-2 text-center text-[10px] text-emerald-700/80">+{{ $ongoing['total'] - $ongoing['rows']->count() }} pekerjaan lainnya</p>
                    @endif
                </div>

            </div>

        </div>

        {{-- ===================== KOLOM KANAN ===================== --}}
        <div class="flex flex-col gap-4">

            {{-- Perlu tindakan saya --}}
            <div class="overflow-hidden rounded-2xl border border-blue-700 bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-700 p-4 text-white shadow-sm">
                <div class="mb-3 flex items-center gap-2">
                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-amber-400 text-slate-900">
                        <i class="fas fa-list-check text-xs"></i>
                    </span>
                    <h3 class="text-xs font-bold uppercase tracking-wide text-white">Perlu Tindakan Saya</h3>
                    <span class="ml-auto rounded-full bg-amber-400 px-2 py-0.5 text-[10px] font-bold text-slate-900">{{ $myQueueTotal }}</span>
                </div>

                <div class="space-y-2">
                    @forelse ($myQueue as $item)
                        @php
                            $isPgo = $item->usertype === 'pgo';
                            $dayClass = $item->waiting_days >= $stale['days'] ? 'bg-red-500/25 text-red-200' : ($item->waiting_days >= 3 ? 'bg-amber-400/25 text-amber-200' : 'bg-white/20 text-white');
                        @endphp
                        <a href="{{ route('admin.permintaansik.show', $item->id) }}"
                           class="flex items-start gap-3 rounded-xl border border-white/10 bg-white/10 p-2.5 transition hover:bg-white/20">
                            <span class="mt-0.5 inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-[11px] font-bold {{ $isPgo ? 'bg-white/20 text-white' : 'bg-emerald-300/30 text-emerald-100' }}">
                                {{ $isPgo ? 'K' : 'V' }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-xs font-semibold text-white">{{ $item->user_name }}</p>
                                <p class="truncate text-[11px] text-blue-100">
                                    Step {{ $item->current_step }}/{{ $item->total_steps }} · {{ $item->current_step_title }}
                                </p>
                                @if ($item->number)
                                    <p class="truncate text-[10px] text-blue-200">No: {{ $item->number }}</p>
                                @endif
                            </div>
                            <div class="flex shrink-0 flex-col items-end gap-1">
                                <span class="rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $dayClass }}">
                                    {{ $item->waiting_days }} hari
                                </span>
                                @if ($item->is_unassigned)
                                    <span class="rounded-full bg-blue-600 px-2 py-0.5 text-[10px] font-semibold text-white">Ambil</span>
                                @endif
                            </div>
                        </a>
                    @empty
                        <div class="py-5 text-center text-[11px] text-blue-100">
                            Tidak ada pengajuan yang menunggu tindakan Anda.
                        </div>
                    @endforelse
                </div>

                @if ($myQueueTotal > $myQueue->count())
                    <a href="{{ route('admin.permintaansik', ['kondisi' => 'perlu_tindakan']) }}"
                       class="mt-3 flex items-center justify-center gap-1 rounded-lg bg-white/10 py-1.5 text-[11px] font-semibold text-white hover:bg-white/20">
                        Lihat semua ({{ $myQueueTotal }})
                        <i class="fas fa-arrow-right text-[9px]"></i>
                    </a>
                @endif
            </div>

            {{-- Aktivitas terbaru --}}
            <div class="overflow-hidden rounded-2xl border border-teal-200 bg-gradient-to-br from-teal-50 to-cyan-100/60 p-4 shadow-sm">
                <div class="mb-3 flex items-center gap-2">
                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-teal-600 text-white">
                        <i class="fas fa-bolt text-xs"></i>
                    </span>
                    <h3 class="text-xs font-bold uppercase tracking-wide text-teal-900">Aktivitas Terbaru</h3>
                </div>

                <div class="space-y-1">
                    @forelse ($recentActivities as $item)
                        @php
                            $toStatus = $item->ke_status;
                            $badgeClass = match($toStatus) {
                                'disetujui' => 'bg-green-100 text-green-700',
                                'revisi' => 'bg-yellow-100 text-yellow-700',
                                'perlu_disetujui' => 'bg-amber-100 text-amber-700',
                                default => 'bg-gray-100 text-gray-700',
                            };
                            $dotClass = match($toStatus) {
                                'disetujui' => 'bg-green-500',
                                'revisi' => 'bg-yellow-500',
                                'perlu_disetujui' => 'bg-amber-500',
                                default => 'bg-gray-400',
                            };
                            $typeLabel = $item->usertype === 'pgo' ? 'Karyawan' : 'Vendor';
                        @endphp

                        <a href="{{ route('admin.permintaansik.show', $item->notification_id) }}"
                           class="flex items-start gap-3 rounded-lg p-2 transition hover:bg-white/70">
                            <div class="mt-1 flex flex-col items-center">
                                <div class="h-2.5 w-2.5 rounded-full {{ $dotClass }}"></div>
                                <div class="mt-1 w-[1px] flex-1 bg-teal-200"></div>
                            </div>

                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="truncate text-xs font-semibold text-gray-800">
                                        {{ $item->user_name }}
                                        <span class="font-normal text-gray-400">· {{ $typeLabel }}</span>
                                    </p>
                                    <span class="shrink-0 text-[9px] text-gray-400">
                                        {{ $item->created_at?->diffForHumans() }}
                                    </span>
                                </div>

                                <p class="mt-0.5 truncate text-[11px] text-gray-600">
                                    {{ $item->step_title }}
                                </p>

                                <div class="mt-1 flex flex-wrap items-center gap-x-2 text-[9px] text-gray-400">
                                    <span>{{ $item->dari }} → {{ $item->ke }}</span>
                                    <span>· {{ $item->aktor }}</span>
                                </div>
                                @if ($item->catatan)
                                    <p class="mt-1 truncate text-[10px] text-gray-500" title="{{ $item->catatan }}">"{{ $item->catatan }}"</p>
                                @endif
                            </div>

                            <span class="shrink-0 rounded-full px-2 py-0.5 text-[9px] font-semibold {{ $badgeClass }}">
                                {{ $item->ke }}
                            </span>
                        </a>

                    @empty
                        <div class="py-4 text-center text-[11px] text-gray-500">
                            Belum ada perubahan status. Aktivitas muncul setelah admin menyetujui atau meminta revisi.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- SIK yang ditangani --}}
            <div class="flex-1 overflow-hidden rounded-2xl border border-amber-200 bg-gradient-to-br from-amber-50 to-orange-100/50 p-4 shadow-sm">
                <div class="mb-3 flex items-center gap-2">
                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-lg bg-amber-500 text-white">
                        <i class="fas fa-chart-pie text-xs"></i>
                    </span>
                    <h3 class="text-xs font-bold uppercase tracking-wide text-amber-900">SIK Yang Ditangani</h3>
                    <span class="ml-auto text-[10px] text-amber-700/70">5 admin teratas</span>
                </div>

                <div class="relative mx-auto h-44 w-44">
                    <canvas id="adminChart"></canvas>
                    <div class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-xl font-extrabold leading-none text-gray-800">{{ number_format($pieTotal, 0, ',', '.') }}</span>
                        <span class="mt-0.5 text-[9px] uppercase tracking-wide text-gray-400">pengajuan</span>
                    </div>
                </div>

                <div class="mt-4 space-y-2.5 text-xs">
                    @foreach ($adminLoads as $name => $count)
                        @php
                            $percent = $pieTotal > 0 ? round(($count / $pieTotal) * 100) : 0;
                            $color = $pieColors[$loop->index % count($pieColors)];
                        @endphp
                        <div>
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex min-w-0 items-center gap-2">
                                    <span class="h-2.5 w-2.5 shrink-0 rounded-full" style="background: {{ $color }}"></span>
                                    <span class="truncate text-gray-700">{{ ($name && $name !== '-') ? $name : 'Belum ditangani' }}</span>
                                </div>
                                <span class="shrink-0 text-[11px] font-semibold text-gray-600">
                                    {{ $count }} <span class="text-gray-400">({{ $percent }}%)</span>
                                </span>
                            </div>
                            <div class="mt-1 h-1 w-full overflow-hidden rounded-full bg-white/80">
                                <div class="h-full rounded-full" style="width: {{ $percent }}%; background: {{ $color }}"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // Pencarian cepat: arahkan ke daftar vendor atau karyawan.
    const quickSearch = document.getElementById('quickSearch');
    if (quickSearch) {
        quickSearch.addEventListener('submit', function () {
            const target = quickSearch.querySelector('select[name="target"]');
            quickSearch.action = target && target.value === 'pgo' ? quickSearch.dataset.pgo : quickSearch.dataset.vendor;
            if (target) target.disabled = true; // jangan ikut terkirim sebagai parameter
        });
    }

    // Angka kartu beranimasi naik.
    document.querySelectorAll('.counter').forEach(counter => {
        const target = parseFloat(counter.getAttribute('data-value')) || 0;
        const suffix = counter.getAttribute('data-suffix') || '';
        const duration = 1200;
        const startTime = performance.now();

        function animate(time) {
            const progress = Math.min((time - startTime) / duration, 1);
            const ease = 1 - Math.pow(1 - progress, 3);
            counter.textContent = Math.floor(target * ease) + suffix;
            if (progress < 1) {
                requestAnimationFrame(animate);
            } else {
                counter.textContent = target + suffix;
            }
        }

        requestAnimationFrame(animate);
    });

    // ======================
    // TREN BULANAN: berhenti di bulan berjalan, tanpa bukit palsu
    // ======================
    const ctxMonthly = document.getElementById('monthlyChart');
    if (ctxMonthly) {
        const labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        const raw = @json($monthlyChart);
        const lastMonth = @json($lastMonth);
        const data = raw.map((value, index) => index < lastMonth ? value : null);
        const year = @json((string) $selectedYear);
        const line = '#4f46e5';

        new Chart(ctxMonthly, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Total Pengajuan',
                    data: data,
                    borderColor: line,
                    borderWidth: 3,
                    tension: 0.3,
                    cubicInterpolationMode: 'monotone',
                    spanGaps: false,
                    fill: true,
                    backgroundColor(context) {
                        const { ctx, chartArea } = context.chart;
                        if (!chartArea) return 'rgba(79, 70, 229, 0.15)';
                        const g = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                        g.addColorStop(0, 'rgba(79, 70, 229, 0.28)');
                        g.addColorStop(1, 'rgba(79, 70, 229, 0.02)');
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
    // DONAT ADMIN: warna sama dengan legenda
    // ======================
    const ctx = document.getElementById('adminChart');
    if (ctx) {
        const dataValues = @json(array_values($adminLoads->toArray()));
        const labels = @json(array_keys($adminLoads->toArray()));
        const colors = @json($pieColors);

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: dataValues,
                    backgroundColor: colors,
                    borderWidth: 3,
                    borderColor: 'transparent',
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                animation: { animateRotate: true, animateScale: true, duration: 1200, easing: 'easeOutQuart' },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#111827',
                        padding: 10,
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0) || 1;
                                const value = context.raw;
                                return ` ${context.label || '-'}: ${value} (${Math.round((value / total) * 100)}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // ======================
    // PERMIT PER JENIS: lingkaran berlapis, 5 jenis terbanyak (terluar = terbanyak)
    // ======================
    const ctxTypes = document.getElementById('permitTypeChart');
    if (ctxTypes) {
        const rings = @json($ringRows);
        const total = @json($permitTypes['total']);
        new Chart(ctxTypes, {
            type: 'doughnut',
            data: {
                labels: ['jumlah', 'sisa'],
                datasets: rings.map((r) => ({
                    label: r.label,
                    data: [r.count, Math.max(total - r.count, 0)],
                    backgroundColor: [r.color, 'rgba(255,255,255,0.65)'],
                    borderWidth: 0,
                    borderRadius: 6,
                    weight: 1
                }))
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '38%',
                animation: { animateRotate: true, duration: 1200, easing: 'easeOutQuart' },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#111827',
                        padding: 8,
                        filter: (item) => item.dataIndex === 0,
                        callbacks: {
                            label: (c) => ` ${c.dataset.label}: ${c.raw} (${Math.round(c.raw / (total || 1) * 100)}%)`
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
                    tension: 0.3,
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
