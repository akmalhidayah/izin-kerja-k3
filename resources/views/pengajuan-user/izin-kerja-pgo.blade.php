<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-sm text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Pengajuan Izin Kerja - User PT. Semen Tonasa') }}
        </h2>
    </x-slot>

    <section class="bg-cover bg-center bg-no-repeat py-10 px-4" style="background-image: url('/images/bg-login.jpg');">
        <div class="max-w-6xl mx-auto bg-white rounded-xl shadow-md p-6">
            <div x-data="{ expanded: true, activeModal: null, selectedPermit: 'umum' }">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
                    <form method="GET" action="{{ route('dashboard') }}" class="flex flex-col sm:flex-row sm:items-center gap-3 w-full md:w-auto">
                        <label for="notification_id" class="text-sm font-semibold text-gray-800 whitespace-nowrap">
                            Pilih Pengajuan
                        </label>

                        <select name="notification_id" id="notification_id" onchange="this.form.submit()"
                            class="w-full sm:w-[280px] border border-gray-300 text-sm rounded px-3 py-2 shadow focus:outline-none focus:ring-1 focus:ring-blue-500">
                            @foreach ($notifications as $notif)
                                <option value="{{ $notif->id }}" {{ $notif->id == $selectedId ? 'selected' : '' }}>
                                    {{ $notif->number }} - {{ $notif->created_at->format('d/m/Y') }}
                                </option>
                            @endforeach
                        </select>
                    </form>

                    <button @click="activeModal = 'modal-op_spk'"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded shadow w-full md:w-auto">
                        + Buat Pengajuan Baru
                    </button>
                </div>

                @if(session('success'))
                    <div class="bg-green-500 text-white p-2 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-500 text-white p-2 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="flex flex-wrap justify-between items-center text-sm text-gray-800 mb-4">
                    <div>
                        <p><span class="font-semibold">Nama User:</span> {{ Auth::user()->name }}</p>
                        <p class="text-[11px] text-gray-600 mt-1">
                            <span class="font-semibold">Jabatan:</span>
                            @if (Auth::user()->jabatan)
                                {{ Auth::user()->jabatan }}
                            @else
                                <span class="text-red-600">Harap isi jabatan di halaman profile.</span>
                            @endif
                        </p>
                        <p><span class="font-semibold">Admin K3:</span> {{ $notification?->assignedAdmin?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p><span class="font-semibold">Tanggal:</span> {{ now()->format('d-m-Y H:i') }}</p>
                    </div>
                </div>

                <div x-show="expanded">
                    <h2 class="text-xl font-bold text-center text-gray-800 mb-6">Step Pengajuan Izin Kerja (PGO)</h2>
                    <div class="flex flex-wrap justify-center items-start gap-y-12 gap-x-8 relative">
                        @php
                            $colors = array(
                                'done' => 'bg-green-500 text-white',
                                'on' => 'bg-blue-600 text-white animate-pulse',
                                'pending' => 'bg-gray-300 text-gray-800',
                                'revisi' => 'bg-red-500 text-white',
                            );
                        @endphp

                        @foreach ($steps as $index => $step)
                            @php
                                $color = $colors[$step['status']] ?? $colors['pending'];
                                $isLast = $index === count($steps) - 1;
                            @endphp

                            <div class="relative flex flex-col items-center w-40">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $color }} z-10 relative">
                                    {{ $index + 1 }}
                                </div>

                                @if (!$isLast)
                                    <div class="hidden lg:block absolute top-5 right-[-36px] h-1 w-9 bg-blue-200 z-0"></div>
                                @endif

                                <p class="mt-2 text-xs text-center text-gray-700 font-medium leading-tight">
                                    {{ $step['title'] }}
                                </p>

                                <p class="text-[10px] mt-1 {{
                                    $step['status'] === 'done' ? 'text-green-600' :
                                    ($step['status'] === 'revisi' ? 'text-red-600' : 'text-gray-500')
                                }}">
                                    {{
                                        $step['status'] === 'done' ? 'Selesai' :
                                        ($step['status'] === 'revisi' ? 'Revisi' : 'Pending')
                                    }}
                                </p>

                                @if ($step['code'] === 'op_spk')
                                    @php
                                        $catatanRevisi = $notification
                                            ? \App\Models\StepApproval::where('notification_id', $notification->id)
                                                ->where('step', 'op_spk')
                                                ->value('catatan')
                                            : null;
                                    @endphp

                                    @if ($notification)
                                        <div class="text-center text-[11px] text-gray-700 font-medium leading-tight mt-1">
                                            {{ strtoupper($notification->type) }}: {{ $notification->number }}<br>
                                            Tanggal: {{ \Carbon\Carbon::parse($notification->created_at)->format('d-m-Y H:i') }}
                                        </div>

                                        @if ($notification?->file)
                                            <a href="{{ asset('storage/' . $notification->file) }}" target="_blank"
                                                class="flex items-center gap-1 mt-1 bg-green-500 hover:bg-green-600 text-white text-[9px] px-3 py-[3px] rounded-full">
                                                Lihat File SPK/PO
                                            </a>
                                        @endif
                                        @if ($step['status'] === 'revisi' && $catatanRevisi)
                                            <p class="text-[10px] text-red-600 italic mt-1">Catatan: {{ $catatanRevisi }}</p>
                                        @endif
                                    @else
                                        <div class="text-center text-[11px] text-gray-500 italic mt-1">
                                            Belum ada notifikasi/PO/SPK
                                        </div>
                                        <button @click="activeModal = 'modal-op_spk'"
                                            class="flex items-center gap-1 mt-1 bg-blue-600 hover:bg-blue-700 text-white text-[9px] px-3 py-[3px] rounded-full transition">
                                            Input Notification/PO/SPK
                                        </button>
                                    @endif
                                @endif

                                @if ($step['code'] === 'jsa')
                                    @php
                                        $prevStepStatus = $steps[$index - 1]['status'] ?? 'done';
                                        $prevStepApproved = $prevStepStatus === 'done';
                                        $label = 'Input JSA';
                                    @endphp

                                    @if (!$prevStepApproved)
                                        <span class="text-[10px] text-gray-400 italic mt-1">Langkah perizinan harus dilakukan secara bertahap.</span>
                                    @else
                                        <div class="flex flex-col items-center space-y-2">
                                            @if (!$jsa)
                                                <button @click="activeModal = 'modal-jsa-create'"
                                                    class="flex items-center gap-1 mt-2 bg-blue-600 hover:bg-blue-700 text-white text-[10px] px-4 py-[5px] rounded-full transition">
                                                    {{ $label }}
                                                </button>
                                            @else
                                                <a href="{{ route('jsa.pdf.view', ['notification_id' => $notification->id]) }}" target="_blank"
                                                    class="flex items-center gap-1 bg-green-500 hover:bg-green-600 text-white text-[10px] px-4 py-[5px] rounded-full">
                                                    Lihat PDF
                                                </a>
                                                <button @click="activeModal = 'modal-jsa-edit'"
                                                    class="flex items-center gap-1 bg-yellow-500 hover:bg-yellow-600 text-white text-[10px] px-4 py-[5px] rounded-full transition">
                                                    Edit JSA
                                                </button>
                                                @if ($jsa->token)
                                                    <div class="mt-2 text-xs text-gray-700">
                                                        Salin link berikut dan kirimkan ke pihak terkait untuk tanda tangan:
                                                        <div class="flex items-center gap-2 mt-1">
                                                            <input type="text" value="{{ route('jsa.form.token', $jsa->token) }}" readonly
                                                                class="text-xs border-gray-300 rounded p-1 w-full bg-gray-100">
                                                            <button type="button" onclick="navigator.clipboard.writeText('{{ route('jsa.form.token', $jsa->token) }}'); alert('Link berhasil disalin!')"
                                                                class="px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700">
                                                                Salin
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endif

                                            @if ($step['status'] === 'revisi')
                                                @php
                                                    $catatanRevisi = \App\Models\StepApproval::where('notification_id', $notification->id)
                                                        ->where('step', 'jsa')
                                                        ->value('catatan');
                                                @endphp
                                                @if ($catatanRevisi)
                                                    <p class="text-[10px] text-red-600 italic mt-1">Catatan: {{ $catatanRevisi }}</p>
                                                @endif
                                            @endif
                                        </div>
                                    @endif
                                @endif

                                @if ($step['code'] === 'working_permit')
                                    @php
                                        $prevStepStatus = $steps[$index - 1]['status'] ?? 'done';
                                        $prevStepApproved = $prevStepStatus === 'done';

                                        $permitUmum = $notification
                                            ? \App\Models\UmumWorkPermit::where('notification_id', $notification->id)->first()
                                            : null;
                                        $permitGas = $notification
                                            ? \App\Models\WorkPermitGasPanas::where('notification_id', $notification->id)->first()
                                            : null;
                                        $permitAir = $notification
                                            ? \App\Models\WorkPermitAir::where('notification_id', $notification->id)->first()
                                            : null;
                                        $permitKetinggian = $notification
                                            ? \App\Models\WorkPermitKetinggian::where('notification_id', $notification->id)->first()
                                            : null;
                                        $permitRuangTertutup = $notification
                                            ? \App\Models\WorkPermitRuangTertutup::where('notification_id', $notification->id)->first()
                                            : null;
                                        $permitPerancah = $notification
                                            ? \App\Models\WorkPermitPerancah::where('notification_id', $notification->id)->first()
                                            : null;
                                        $permitRisikoPanas = $notification
                                            ? \App\Models\WorkPermitRisikoPanas::where('notification_id', $notification->id)->first()
                                            : null;
                                        $permitBeban = $notification
                                            ? \App\Models\WorkPermitBeban::where('notification_id', $notification->id)->first()
                                            : null;
                                        $permitPenggalian = $notification
                                            ? \App\Models\WorkPermitPenggalian::where('notification_id', $notification->id)->first()
                                            : null;
                                        $permitPengangkatan = $notification
                                            ? \App\Models\WorkPermitPengangkatan::where('notification_id', $notification->id)->first()
                                            : null;
                                    @endphp

                                    @if (!$prevStepApproved)
                                        <span class="text-[10px] text-gray-400 italic mt-1">Langkah perizinan harus dilakukan secara bertahap.</span>
                                    @else
                                        <div class="flex flex-col items-center gap-2 mt-1">
                                            @if ($permitUmum)
                                                <div class="flex flex-col items-center">
                                                    <span class="text-[10px] text-gray-600 mb-1">Permit Umum</span>
                                                    <div class="flex gap-1">
                                                        <a href="{{ route('working-permit.umum.preview', ['id' => $permitUmum->notification_id]) }}"
                                                            class="bg-green-500 hover:bg-green-600 text-white p-1 rounded-full" title="Lihat PDF Umum">
                                                            <i class="fas fa-file-pdf text-xs"></i>
                                                        </a>
                                                        <button @click="activeModal = 'modal-working_permit'"
                                                            class="bg-yellow-500 hover:bg-yellow-600 text-white p-1 rounded-full" title="Edit Umum">
                                                            <i class="fas fa-edit text-xs"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="flex flex-col items-center">
                                                    <span class="text-[10px] text-gray-600 mb-1">Permit Umum</span>
                                                    <button @click="activeModal = 'modal-working_permit'"
                                                        class="bg-blue-600 hover:bg-blue-700 text-white p-1 rounded-full" title="Buat Permit Umum">
                                                        <i class="fas fa-plus text-xs"></i>
                                                    </button>
                                                </div>
                                            @endif
                                            @if ($permitUmum && $permitUmum->token)
                                                <div class="mt-2 text-[10px] text-gray-700 text-center">
                                                    Salin link berikut dan kirim ke pihak terkait untuk mengisi atau tanda tangan:
                                                    <div class="flex items-center gap-2 mt-1">
                                                        <input type="text" value="{{ route('working-permit.umum.token', $permitUmum->token) }}" readonly
                                                            class="text-[10px] border-gray-300 rounded p-1 w-full bg-gray-100">
                                                        <button type="button"
                                                            onclick="navigator.clipboard.writeText('{{ route('working-permit.umum.token', $permitUmum->token) }}'); alert('Link berhasil disalin!')"
                                                            class="px-2 py-1 bg-blue-600 text-white text-[10px] rounded hover:bg-blue-700">
                                                            Salin
                                                        </button>
                                                    </div>
                                                </div>
                                            @endif

                                            @if ($permitGas)
                                                <div class="flex items-center gap-2 text-[10px] text-gray-600">
                                                    <span>Permit Gas Panas:</span>
                                                    <a href="{{ route('working-permit.gaspanas.preview', ['id' => $permitGas->notification_id]) }}"
                                                        class="bg-green-500 hover:bg-green-600 text-white p-1 rounded-full" title="Lihat PDF">
                                                        <i class="fas fa-fire text-xs"></i>
                                                    </a>
                                                    <button @click="activeModal = 'modal-working_permit-gaspanas'"
                                                        class="bg-amber-600 hover:bg-amber-700 text-white p-1 rounded-full" title="Edit">
                                                        <i class="fas fa-edit text-xs"></i>
                                                    </button>
                                                    @if ($permitGas->token)
                                                        <button type="button"
                                                            onclick="navigator.clipboard.writeText('{{ route('working-permit.gaspanas.token', $permitGas->token) }}'); alert('Link berhasil disalin!')"
                                                            class="bg-blue-600 hover:bg-blue-700 text-white p-1 rounded-full" title="Salin Link">
                                                            <i class="fas fa-link text-xs"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            @endif

                                            @if ($permitAir)
                                                <div class="flex items-center gap-2 text-[10px] text-gray-600">
                                                    <span>Permit Air:</span>
                                                    <a href="{{ route('working-permit.air.preview', ['id' => $permitAir->notification_id]) }}"
                                                        class="bg-green-500 hover:bg-green-600 text-white p-1 rounded-full" title="Lihat PDF">
                                                        <i class="fas fa-water text-xs"></i>
                                                    </a>
                                                    <button @click="activeModal = 'modal-working_permit-air'"
                                                        class="bg-teal-600 hover:bg-teal-700 text-white p-1 rounded-full" title="Edit">
                                                        <i class="fas fa-edit text-xs"></i>
                                                    </button>
                                                    @if ($permitAir->token)
                                                        <button type="button"
                                                            onclick="navigator.clipboard.writeText('{{ route('working-permit.air.token', $permitAir->token) }}'); alert('Link berhasil disalin!')"
                                                            class="bg-blue-600 hover:bg-blue-700 text-white p-1 rounded-full" title="Salin Link">
                                                            <i class="fas fa-link text-xs"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            @endif

                                            @if ($permitKetinggian)
                                                <div class="flex items-center gap-2 text-[10px] text-gray-600">
                                                    <span>Permit Ketinggian:</span>
                                                    <a href="{{ route('working-permit.ketinggian.preview', ['id' => $permitKetinggian->notification_id]) }}"
                                                        class="bg-green-500 hover:bg-green-600 text-white p-1 rounded-full" title="Lihat PDF">
                                                        <i class="fa-solid fa-person-falling text-xs"></i>
                                                    </a>
                                                    <button @click="activeModal = 'modal-working_permit-ketinggian'"
                                                        class="bg-amber-600 hover:bg-amber-700 text-white p-1 rounded-full" title="Edit">
                                                        <i class="fas fa-edit text-xs"></i>
                                                    </button>
                                                    @if ($permitKetinggian->token)
                                                        <button type="button"
                                                            onclick="navigator.clipboard.writeText('{{ route('working-permit.ketinggian.token', $permitKetinggian->token) }}'); alert('Link berhasil disalin!')"
                                                            class="bg-blue-600 hover:bg-blue-700 text-white p-1 rounded-full" title="Salin Link">
                                                            <i class="fas fa-link text-xs"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            @endif

                                            @if ($permitRuangTertutup)
                                                <div class="flex items-center gap-2 text-[10px] text-gray-600">
                                                    <span>Permit Ruang Tertutup:</span>
                                                    <a href="{{ route('working-permit.ruangtertutup.preview', ['id' => $permitRuangTertutup->notification_id]) }}"
                                                        class="bg-green-500 hover:bg-green-600 text-white p-1 rounded-full" title="Lihat PDF">
                                                        <i class="fas fa-door-closed text-xs"></i>
                                                    </a>
                                                    <button @click="activeModal = 'modal-working_permit-ruang-tertutup'"
                                                        class="bg-purple-600 hover:bg-purple-700 text-white p-1 rounded-full" title="Edit">
                                                        <i class="fas fa-edit text-xs"></i>
                                                    </button>
                                                    @if ($permitRuangTertutup->token)
                                                        <button type="button"
                                                            onclick="navigator.clipboard.writeText('{{ route('working-permit.ruangtertutup.token', $permitRuangTertutup->token) }}'); alert('Link berhasil disalin!')"
                                                            class="bg-blue-600 hover:bg-blue-700 text-white p-1 rounded-full" title="Salin Link">
                                                            <i class="fas fa-link text-xs"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            @endif

                                            @if ($permitPerancah)
                                                <div class="flex items-center gap-2 text-[10px] text-gray-600">
                                                    <span>Permit Perancah:</span>
                                                    <a href="{{ route('working-permit.perancah.preview', ['id' => $permitPerancah->notification_id]) }}"
                                                        class="bg-green-500 hover:bg-green-600 text-white p-1 rounded-full" title="Lihat PDF">
                                                        <i class="fa-solid fa-building text-xs"></i>
                                                    </a>
                                                    <button @click="activeModal = 'modal-working_permit-perancah'"
                                                        class="bg-orange-600 hover:bg-orange-700 text-white p-1 rounded-full" title="Edit">
                                                        <i class="fas fa-edit text-xs"></i>
                                                    </button>
                                                    @if ($permitPerancah->token)
                                                        <button type="button"
                                                            onclick="navigator.clipboard.writeText('{{ route('working-permit.perancah.token', $permitPerancah->token) }}'); alert('Link berhasil disalin!')"
                                                            class="bg-blue-600 hover:bg-blue-700 text-white p-1 rounded-full" title="Salin Link">
                                                            <i class="fas fa-link text-xs"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            @endif

                                            @if ($permitRisikoPanas)
                                                <div class="flex items-center gap-2 text-[10px] text-gray-600">
                                                    <span>Permit Risiko Panas:</span>
                                                    <a href="{{ route('working-permit.risiko-panas.preview', ['id' => $permitRisikoPanas->notification_id]) }}"
                                                        class="bg-green-500 hover:bg-green-600 text-white p-1 rounded-full" title="Lihat PDF">
                                                        <i class="fas fa-temperature-high text-xs"></i>
                                                    </a>
                                                    <button @click="activeModal = 'modal-working_permit-risiko-panas'"
                                                        class="bg-red-600 hover:bg-red-700 text-white p-1 rounded-full" title="Edit">
                                                        <i class="fas fa-edit text-xs"></i>
                                                    </button>
                                                    @if ($permitRisikoPanas->token)
                                                        <button type="button"
                                                            onclick="navigator.clipboard.writeText('{{ route('working-permit.risiko-panas.token', $permitRisikoPanas->token) }}'); alert('Link berhasil disalin!')"
                                                            class="bg-blue-600 hover:bg-blue-700 text-white p-1 rounded-full" title="Salin Link">
                                                            <i class="fas fa-link text-xs"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            @endif

                                            @if ($permitBeban)
                                                <div class="flex items-center gap-2 text-[10px] text-gray-600">
                                                    <span>Permit Beban:</span>
                                                    <a href="{{ route('working-permit.beban.preview', ['id' => $permitBeban->notification_id]) }}"
                                                        class="bg-green-500 hover:bg-green-600 text-white p-1 rounded-full" title="Lihat PDF">
                                                        <i class="fas fa-dumbbell text-xs"></i>
                                                    </a>
                                                    <button @click="activeModal = 'modal-working_permit-beban'"
                                                        class="bg-indigo-600 hover:bg-indigo-700 text-white p-1 rounded-full" title="Edit">
                                                        <i class="fas fa-edit text-xs"></i>
                                                    </button>
                                                    @if ($permitBeban->token)
                                                        <button type="button"
                                                            onclick="navigator.clipboard.writeText('{{ route('working-permit.beban.token', $permitBeban->token) }}'); alert('Link berhasil disalin!')"
                                                            class="bg-blue-600 hover:bg-blue-700 text-white p-1 rounded-full" title="Salin Link">
                                                            <i class="fas fa-link text-xs"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            @endif

                                            @if ($permitPenggalian)
                                                <div class="flex items-center gap-2 text-[10px] text-gray-600">
                                                    <span>Permit Penggalian:</span>
                                                    <a href="{{ route('working-permit.penggalian.preview', ['id' => $permitPenggalian->notification_id]) }}"
                                                        class="bg-green-500 hover:bg-green-600 text-white p-1 rounded-full" title="Lihat PDF">
                                                        <i class="fas fa-digging text-xs"></i>
                                                    </a>
                                                    <button @click="activeModal = 'modal-working_permit-penggalian'"
                                                        class="bg-yellow-700 hover:bg-yellow-800 text-white p-1 rounded-full" title="Edit">
                                                        <i class="fas fa-edit text-xs"></i>
                                                    </button>
                                                    @if ($permitPenggalian->token)
                                                        <button type="button"
                                                            onclick="navigator.clipboard.writeText('{{ route('working-permit.penggalian.token', $permitPenggalian->token) }}'); alert('Link berhasil disalin!')"
                                                            class="bg-blue-600 hover:bg-blue-700 text-white p-1 rounded-full" title="Salin Link">
                                                            <i class="fas fa-link text-xs"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            @endif

                                            @if ($permitPengangkatan)
                                                <div class="flex items-center gap-2 text-[10px] text-gray-600">
                                                    <span>Permit Pengangkatan:</span>
                                                    <a href="{{ route('working-permit.pengangkatan.preview', ['id' => $permitPengangkatan->notification_id]) }}"
                                                        class="bg-green-500 hover:bg-green-600 text-white p-1 rounded-full" title="Lihat PDF">
                                                        <i class="fas fa-anchor text-xs"></i>
                                                    </a>
                                                    <button @click="activeModal = 'modal-working_permit-pengangkatan'"
                                                        class="bg-pink-600 hover:bg-pink-700 text-white p-1 rounded-full" title="Edit">
                                                        <i class="fas fa-edit text-xs"></i>
                                                    </button>
                                                    @if ($permitPengangkatan->token)
                                                        <button type="button"
                                                            onclick="navigator.clipboard.writeText('{{ route('working-permit.pengangkatan.token', $permitPengangkatan->token) }}'); alert('Link berhasil disalin!')"
                                                            class="bg-blue-600 hover:bg-blue-700 text-white p-1 rounded-full" title="Salin Link">
                                                            <i class="fas fa-link text-xs"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            @endif

                                            <div class="flex flex-col items-center mt-2">
                                                <span class="text-[10px] text-gray-600 mb-1">Tambah Permit Lain</span>
                                                <button @click="activeModal = 'modal-tambah-lainnya'"
                                                    class="bg-indigo-600 hover:bg-indigo-700 text-white p-1 rounded-full" title="Tambah Permit Lain">
                                                    <i class="fas fa-plus text-xs"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                @endif

                                @if ($step['code'] === 'op_spk')
                                    @include('components.steps.modal-op', [
                                        'id' => 'modal-op_spk',
                                        'notification' => $notification,
                                        'stepName' => $step['code']
                                    ])
                                @elseif ($step['code'] === 'jsa')
                                    @if (!$jsa)
                                        @include('components.steps.modal-jsa', [
                                            'id' => 'modal-jsa-create',
                                            'notification' => $notification,
                                            'stepName' => $step['code']
                                        ])
                                    @else
                                        @include('components.steps.modal-jsa-edit', [
                                            'id' => 'modal-jsa-edit',
                                            'notification' => $notification,
                                            'stepName' => $step['code'],
                                            'jsa' => $jsa
                                        ])
                                    @endif
                                @elseif ($step['code'] === 'working_permit')
                                    @include('components.steps.modal-working-permit', [
                                        'id' => 'modal-working_permit',
                                        'notification' => $notification,
                                        'stepName' => $step['code'],
                                        'permitUmum' => $permitUmum ?? null
                                    ])

                                    @include('components.steps.modal-working-permit-gaspanas', [
                                        'label' => 'Edit Gas Panas',
                                        'id' => 'modal-working_permit-gaspanas',
                                        'notification' => $notification,
                                        'stepName' => $step['code'],
                                        'permitGas' => $permitGas ?? null,
                                        'detail' => $detail ?? null,
                                        'closure' => $closure ?? null
                                    ])

                                    @include('components.steps.modal-working-permit-air', [
                                        'label' => 'Edit Air',
                                        'id' => 'modal-working_permit-air',
                                        'notification' => $notification,
                                        'stepName' => $step['code'],
                                        'permitAir' => $permitAir ?? null,
                                        'detail' => $detail ?? null,
                                        'closure' => $closure ?? null
                                    ])

                                    @include('components.steps.modal-working-permit-ketinggian', [
                                        'label' => 'Edit Ketinggian',
                                        'id' => 'modal-working_permit-ketinggian',
                                        'notification' => $notification,
                                        'stepName' => $step['code'],
                                        'permitKetinggian' => $permitKetinggian ?? null,
                                        'detail' => $detail ?? null,
                                        'closure' => $closure ?? null
                                    ])

                                    @include('components.steps.modal-working-permit-ruang-tertutup', [
                                        'label' => 'Edit Ruang Tertutup',
                                        'id' => 'modal-working_permit-ruang-tertutup',
                                        'notification' => $notification,
                                        'stepName' => $step['code'],
                                        'permitRuangTertutup' => $permitRuangTertutup ?? null,
                                        'detail' => $detail ?? null,
                                        'closure' => $closure ?? null
                                    ])

                                    @include('components.steps.modal-working-permit-perancah', [
                                        'label' => 'Edit Perancah',
                                        'id' => 'modal-working_permit-perancah',
                                        'notification' => $notification,
                                        'stepName' => $step['code'],
                                        'permitPerancah' => $permitPerancah ?? null,
                                        'detail' => $detail ?? null,
                                        'closure' => $closure ?? null
                                    ])

                                    @include('components.steps.modal-working-permit-risiko-panas', [
                                        'label' => 'Edit Risiko Panas',
                                        'id' => 'modal-working_permit-risiko-panas',
                                        'notification' => $notification,
                                        'stepName' => $step['code'],
                                        'permitRisikoPanas' => $permitRisikoPanas ?? null,
                                        'detail' => $detail ?? null,
                                        'closure' => $closure ?? null
                                    ])

                                    @include('components.steps.modal-working-permit-beban', [
                                        'label' => 'Edit Beban',
                                        'id' => 'modal-working_permit-beban',
                                        'notification' => $notification,
                                        'stepName' => $step['code'],
                                        'permitBeban' => $permitBeban ?? null,
                                        'detail' => $detail ?? null,
                                        'closure' => $closure ?? null
                                    ])

                                    @include('components.steps.modal-working-permit-penggalian', [
                                        'label' => 'Edit Penggalian',
                                        'id' => 'modal-working_permit-penggalian',
                                        'notification' => $notification,
                                        'stepName' => $step['code'],
                                        'permitPenggalian' => $permitPenggalian ?? null,
                                        'detail' => $detail ?? null,
                                        'closure' => $closure ?? null
                                    ])

                                    @include('components.steps.modal-working-permit-pengangkatan', [
                                        'label' => 'Edit Pengangkatan',
                                        'id' => 'modal-working_permit-pengangkatan',
                                        'notification' => $notification,
                                        'stepName' => $step['code'],
                                        'permitPengangkatan' => $permitPengangkatan ?? null,
                                        'detail' => $detail ?? null,
                                        'closure' => $closure ?? null
                                    ])

                                    @include('components.steps.modal-tambah-lainnya', [
                                        'label' => 'Tambah Permit Lain',
                                        'id' => 'modal-tambah-lainnya',
                                        'notification' => $notification,
                                        'stepName' => $step['code'],
                                        'permits' => $permits ?? []
                                    ])
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="text-center mt-6">
                    <button @click="expanded = !expanded" class="text-blue-600 text-sm underline hover:text-blue-800">
                        <span x-show="!expanded">Lihat Progress Pengajuan</span>
                        <span x-show="expanded">Tampilkan lebih sedikit</span>
                    </button>
                </div>
            </div>
        </div>
    </section>

    @include('components.sign-pad')
</x-app-layout>
