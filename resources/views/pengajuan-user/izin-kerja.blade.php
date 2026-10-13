    <x-app-layout>
       @include('components.permit-mobile-style')
       <section class="pt-20 pb-10 px-3 sm:px-6">
<div class="max-w-7xl mx-auto bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
                <div x-data="{ expanded: true, activeModal: null, selectedPermit: 'umum' }">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
<form method="GET" action="{{ route('dashboard') }}" class="flex flex-col sm:flex-row sm:items-center gap-3 w-full md:w-auto">
    <label for="notification_id" class="text-sm font-semibold text-gray-800 whitespace-nowrap">
        Pilih Pengajuan
    </label>

    <select name="notification_id" id="notification_id" onchange="this.form.submit()"
    class="w-full sm:w-[300px] 
    border border-gray-200 
    bg-gray-50 
    text-sm rounded-lg 
    px-3 py-2.5 
    shadow-sm 
    focus:ring-2 focus:ring-blue-500 focus:border-blue-500 
    transition">
        @foreach ($notifications as $notif)
            <option value="{{ $notif->id }}" {{ $notif->id == $selectedId ? 'selected' : '' }}>
                {{ $notif->number }} - {{ $notif->created_at->format('d/m/Y') }}
            </option>
        @endforeach
    </select>
</form>


    <!-- Tombol Buat Notifikasi Baru -->
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

<div class="flex flex-wrap justify-between items-center gap-4 text-sm text-gray-700 mb-4">

    <!-- LEFT -->
    <div class="flex flex-col gap-1">

        <div class="flex items-center gap-2">
            <i class="fas fa-user text-green-600 text-xs"></i>
            <span class="text-gray-500">Vendor/User:</span>
            <span class="font-semibold text-gray-800">
                {{ Auth::user()->name }}
            </span>
        </div>

        <div class="flex items-center gap-2">
            <i class="fas fa-user-shield text-blue-600 text-xs"></i>
            <span class="text-gray-500">Admin K3:</span>
            <span class="font-semibold text-gray-800">
                {{ $notification?->assignedAdmin?->name ?? '-' }}
            </span>
        </div>

    </div>

    <!-- RIGHT -->
    <div class="flex items-center gap-2">
        <i class="fas fa-clock text-gray-500 text-xs"></i>
        <span class="text-gray-500">Tanggal:</span>
        <span class="font-semibold text-gray-800">
            {{ now()->format('d-m-Y H:i') }}
        </span>
    </div>

</div>

                    <div x-show="expanded">
                        <h2 class="text-xl font-bold text-center text-gray-800 mb-6">Step Pengajuan Izin Kerja</h2>
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

                                <div class="relative flex flex-col items-center w-32">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $color }} z-10 relative">
                                        {{ $index + 1 }}
                                    </div>

                                    @if (!$isLast && $index !== 6)
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
        $label = $step['label'] ?? 'Input Notification/PO/SPK'; // Default label aman
        $catatanRevisi = $stepNotes['op_spk'] ?? null;
    @endphp

    @if ($notification)
        <div class="text-center text-[11px] text-gray-700 font-medium leading-tight mt-1">
            {{ strtoupper($notification->type) }}: {{ $notification->number }}<br>
            Tanggal: {{ \Carbon\Carbon::parse($notification->created_at)->format('d-m-Y H:i') }}
        </div>

        @if ($notification?->file)
            <a href="{{ asset('storage/' . $notification->file) }}" target="_blank"
                class="flex items-center gap-1 mt-1 bg-green-500 hover:bg-green-600 text-white text-[9px] px-3 py-[3px] rounded-full">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
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
        <button @click="activeModal = 'modal-{{ $step['code'] }}'"
            class="flex items-center gap-1 mt-1 bg-blue-600 hover:bg-blue-700 text-white text-[9px] px-3 py-[3px] rounded-full transition">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            {{ $label }}
        </button>
    @endif
@endif

@if ($step['code'] === 'data_kontraktor')
    @php
        $prevStepCode = $steps[$index - 1]['code'] ?? null;
        $prevStepApproved = $prevStepCode
            ? (($stepGateStatuses[$prevStepCode] ?? null) === 'disetujui')
            : true; // Step pertama tidak perlu cek
    @endphp

        @if (!$prevStepApproved)
            <span class="text-[10px] text-gray-400 italic mt-1">Langkah perizinan harus dilakukan secara bertahap.</span>
        @else
            <div class="flex flex-col items-center space-y-2">
                @if (!$dataKontraktor)
                    <button @click="activeModal = 'modal-data-kontraktor'"
                        class="flex items-center gap-1 mt-2 bg-blue-600 hover:bg-blue-700 text-white text-[10px] px-4 py-[5px] rounded-full transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Input Data Kontraktor
                    </button>
                @else
                    <div class="text-[10px] text-gray-600 text-center mt-1">
                        Data Kontraktor sudah diisi.
                    </div>
                   <div class="flex flex-row items-center gap-2 mt-1">
    <button @click="activeModal = 'modal-data-kontraktor'"
        class="flex items-center gap-1 bg-yellow-500 hover:bg-yellow-600 text-white text-[9px] px-2 py-1 rounded transition">
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h2m-2 4h2m-2 4h2m-2 4h2m4-20a2 2 0 00-2 2v16a2 2 0 002 2h4a2 2 0 002-2V6l-6-6z"/>
        </svg>
        Edit
    </button>
    <a href="{{ route('izin-kerja.data-kontraktor.pdf', $notification->id) }}"
        target="_blank"
        class="flex items-center gap-1 bg-green-600 hover:bg-green-700 text-white text-[9px] px-2 py-1 rounded transition"
        title="Lihat PDF Data Kontraktor">
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5"/>
        </svg>
        Lihat PDF
    </a>
</div>
@if ($dataKontraktor)
    @include('components.token-link-action', [
        'record' => $dataKontraktor,
        'routeName' => 'izin-kerja.data-kontraktor.token',
        'regenerateType' => 'data-kontraktor',
    ])
@endif


                @endif

                @if ($step['status'] === 'revisi')
                    @php
                        $catatanRevisi = $stepNotes['data_kontraktor'] ?? null;
                    @endphp
                    @if ($catatanRevisi)
                        <p class="text-[10px] text-red-600 italic mt-1">Catatan: {{ $catatanRevisi }}</p>
                    @endif
                @endif
            </div>
        @endif
    @endif
@if ($step['code'] === 'bpjs')
   @php
    $prevStepCode = $steps[$index - 1]['code'] ?? null;

    $prevStepApproved = $prevStepCode
        ? (($stepGateStatuses[$prevStepCode] ?? null) === 'disetujui')
        : true;

    $bpjsFiles = $uploadsByStep->get('bpjs', collect());
@endphp


    @if (!$prevStepApproved)
        <span class="text-[10px] text-gray-400 italic mt-1">Langkah perizinan harus dilakukan secara bertahap.</span>
    @else
        <div class="flex flex-col items-center space-y-2">
            @if ($bpjsFiles->count())
                @foreach ($bpjsFiles as $file)
                    <div class="flex items-center gap-2">
                        <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank"
                            class="bg-green-500 hover:bg-green-600 text-white text-[9px] px-3 py-[3px] rounded-full">
                            Lihat BPJS
                        </a>
                        <form action="{{ route('upload.delete', $file->id) }}" method="POST" onsubmit="return confirm('Hapus file ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-500 text-[9px] px-2 py-[1px] border border-red-300 hover:bg-red-100 rounded-full">
                                Hapus
                            </button>
                        </form>
                    </div>
                @endforeach
            @endif

            {{-- Tombol Upload Baru --}}
            <button @click="activeModal = 'modal-{{ $step['code'] }}'"
                class="flex items-center gap-1 mt-1 bg-blue-600 hover:bg-blue-700 text-white text-[9px] px-3 py-[3px] rounded-full transition">
                +
            </button>
            <span class="text-[10px] text-gray-400 italic mt-1">Upload File dalam Bentuk PDF/JPG</span>

            {{-- Catatan revisi --}}
            @if ($step['status'] === 'revisi')
                @php
                    $catatanRevisi = $stepNotes['bpjs'] ?? null;
                @endphp
                @if ($catatanRevisi)
                    <p class="text-[10px] text-red-600 italic mt-1">Catatan: {{ $catatanRevisi }}</p>
                @endif
            @endif
        </div>
    @endif
@endif


    @if ($step['code'] === 'jsa')
    @php
        $prevStepCode = $steps[$index - 1]['code'] ?? null;
        $prevStepApproved = $prevStepCode
            ? (($stepGateStatuses[$prevStepCode] ?? null) === 'disetujui')
            : false;
        $label = 'Tambah Data'; // Default label
    @endphp

    @if (!$prevStepApproved)
        <span class="text-[10px] text-gray-400 italic mt-1">Langkah perizinan harus dilakukan secara bertahap.</span>
    @else
        <div class="flex flex-col items-center space-y-2">
            @if (!$jsa)
                <button @click="activeModal = 'modal-jsa-create'"
                    class="flex items-center gap-1 mt-2 bg-blue-600 hover:bg-blue-700 text-white text-[10px] px-4 py-[5px] rounded-full transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    {{ $label }}
                </button>
            @else
                <a href="{{ route('jsa.pdf.view', ['notification_id' => $notification->id]) }}" target="_blank"
                    class="flex items-center gap-1 bg-green-500 hover:bg-green-600 text-white text-[10px] px-4 py-[5px] rounded-full">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Lihat PDF
                </a>
                <button @click="activeModal = 'modal-jsa-edit'"
                    class="flex items-center gap-1 bg-yellow-500 hover:bg-yellow-600 text-white text-[10px] px-4 py-[5px] rounded-full transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h2m-2 4h2m-2 4h2m-2 4h2m4-20a2 2 0 00-2 2v16a2 2 0 002 2h4a2 2 0 002-2V6l-6-6z"/>
                    </svg>
                    Edit JSA
                </button>

                @include('components.token-link-action', [
                    'record' => $jsa,
                    'routeName' => 'jsa.form.token',
                    'regenerateType' => 'jsa',
                ])
            @endif

            @if ($step['status'] === 'revisi')
                @php
                    $catatanRevisi = $stepNotes['jsa'] ?? null;
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
    $prevStepCode = $steps[$index - 1]['code'] ?? null;

    $prevStepApproved = $notification
        ? (($stepGateStatuses[$prevStepCode] ?? null) === 'disetujui')
        : false;

    $permitUmum = $permits['umum'] ?? null;
    $permitGas = $permits['gaspanas'] ?? null;
    $permitAir = $permits['air'] ?? null;
    $permitKetinggian = $permits['ketinggian'] ?? null;
    $permitRuangTertutup = $permits['ruang-tertutup'] ?? null;
    $permitPerancah = $permits['perancah'] ?? null;
    $permitRisikoPanas = $permits['risiko-panas'] ?? null;
    $permitBeban = $permits['beban'] ?? null;
    $permitPenggalian = $permits['penggalian'] ?? null;
    $permitPengangkatan = $permits['pengangkatan'] ?? null;
    $permitListrikTeganganTinggi = $permits['listrik-tegangan-tinggi'] ?? null;

    $permitUmumDetail = $renderPermitFormsEagerly ? $permitUmum?->detail : null;
    $permitUmumClosure = $renderPermitFormsEagerly ? $permitUmum?->closure : null;


@endphp

        @if (!$prevStepApproved)
            <span class="text-[10px] text-gray-400 italic mt-1">
                Langkah perizinan harus dilakukan secara bertahap.
            </span>
        @else
            <div class="flex flex-col items-center gap-2 mt-1">
                {{-- Permit Umum --}}
                @if ($permitUmum)
                    <div class="flex flex-col items-center">
                        <span class="text-[10px] text-gray-600 mb-1">Permit Umum</span>
                        <div class="flex gap-1">
                            <a href="{{ route('working-permit.umum.preview', ['id' => $permitUmum->notification_id]) }}"
                                class="bg-green-500 hover:bg-green-600 text-white p-1 rounded-full" title="Lihat PDF Umum">
                                <i class="fas fa-file-pdf text-xs"></i>
                            </a>
                            <button @click="activeModal = 'modal-{{ $step['code'] }}'"
                                class="bg-yellow-500 hover:bg-yellow-600 text-white p-1 rounded-full" title="Edit Umum">
                                <i class="fas fa-edit text-xs"></i>
                            </button>
                        </div>
                    </div>
                @else
                    <div class="flex flex-col items-center">
                        <span class="text-[10px] text-gray-600 mb-1">Permit Umum</span>
                        <button @click="activeModal = 'modal-{{ $step['code'] }}'"
                            class="bg-blue-600 hover:bg-blue-700 text-white p-1 rounded-full" title="Buat Permit Umum">
                            <i class="fas fa-plus text-xs"></i>
                        </button>
                    </div>
                @endif
                @if ($permitUmum)
                    @include('components.token-link-action', [
                        'record' => $permitUmum,
                        'routeName' => 'working-permit.umum.token',
                        'regenerateType' => 'umum',
                        'helpText' => 'Salin link berikut dan kirim ke pihak terkait untuk mengisi atau tanda tangan:',
                    ])
                @endif

{{-- Permit Gas Panas --}}
@if ($permitGas)
    <div class="flex items-center gap-2 text-[10px] text-gray-600">
        <span>Permit Gas Panas:</span>
        <a href="{{ route('working-permit.gaspanas.preview', ['id' => $permitGas->notification_id]) }}"
            class="bg-green-500 hover:bg-green-600 text-white p-1 rounded-full" title="Lihat PDF">
            <i class="fas fa-fire text-xs"></i>
        </a>
        <button @click="activeModal = 'modal-{{ $step['code'] }}-gaspanas'"
            class="bg-amber-600 hover:bg-amber-700 text-white p-1 rounded-full" title="Edit">
            <i class="fas fa-edit text-xs"></i>
        </button>
        @include('components.token-link-action', ['record' => $permitGas, 'routeName' => 'working-permit.gaspanas.token', 'regenerateType' => 'gaspanas', 'compact' => true])
    </div>
@endif

{{-- Permit Air --}}
@if ($permitAir)
    <div class="flex items-center gap-2 text-[10px] text-gray-600">
        <span>Permit Air:</span>
        <a href="{{ route('working-permit.air.preview', ['id' => $permitAir->notification_id]) }}"
            class="bg-green-500 hover:bg-green-600 text-white p-1 rounded-full" title="Lihat PDF">
            <i class="fas fa-water text-xs"></i>
        </a>
        <button @click="activeModal = 'modal-{{ $step['code'] }}-air'"
            class="bg-teal-600 hover:bg-teal-700 text-white p-1 rounded-full" title="Edit">
            <i class="fas fa-edit text-xs"></i>
        </button>
        @include('components.token-link-action', ['record' => $permitAir, 'routeName' => 'working-permit.air.token', 'regenerateType' => 'air', 'compact' => true])
    </div>
@endif

{{-- Permit Ketinggian --}}
@if ($permitKetinggian)
    <div class="flex items-center gap-2 text-[10px] text-gray-600">
        <span>Permit Ketinggian:</span>
        <a href="{{ route('working-permit.ketinggian.preview', ['id' => $permitKetinggian->notification_id]) }}"
            class="bg-green-500 hover:bg-green-600 text-white p-1 rounded-full" title="Lihat PDF">
            <i class="fa-solid fa-person-falling text-xs"></i>
        </a>
        <button @click="activeModal = 'modal-{{ $step['code'] }}-ketinggian'"
            class="bg-amber-600 hover:bg-amber-700 text-white p-1 rounded-full" title="Edit">
            <i class="fas fa-edit text-xs"></i>
        </button>
        @include('components.token-link-action', ['record' => $permitKetinggian, 'routeName' => 'working-permit.ketinggian.token', 'regenerateType' => 'ketinggian', 'compact' => true])
    </div>
@endif

{{-- Permit Ruang Tertutup --}}
@if ($permitRuangTertutup)
    <div class="flex items-center gap-2 text-[10px] text-gray-600">
        <span>Permit Ruang Tertutup:</span>
        <a href="{{ route('working-permit.ruangtertutup.preview', ['id' => $permitRuangTertutup->notification_id]) }}"
            class="bg-green-500 hover:bg-green-600 text-white p-1 rounded-full" title="Lihat PDF">
            <i class="fas fa-door-closed text-xs"></i>
        </a>
        <button @click="activeModal = 'modal-{{ $step['code'] }}-ruang-tertutup'"
            class="bg-purple-600 hover:bg-purple-700 text-white p-1 rounded-full" title="Edit">
            <i class="fas fa-edit text-xs"></i>
        </button>
        @include('components.token-link-action', ['record' => $permitRuangTertutup, 'routeName' => 'working-permit.ruangtertutup.token', 'regenerateType' => 'ruang-tertutup', 'compact' => true])
    </div>
@endif

{{-- Permit Perancah --}}
@if ($permitPerancah)
    <div class="flex items-center gap-2 text-[10px] text-gray-600">
        <span>Permit Perancah:</span>
        <a href="{{ route('working-permit.perancah.preview', ['id' => $permitPerancah->notification_id]) }}"
            class="bg-green-500 hover:bg-green-600 text-white p-1 rounded-full" title="Lihat PDF">
            <i class="fa-solid fa-building text-xs"></i>
        </a>
        <button @click="activeModal = 'modal-{{ $step['code'] }}-perancah'"
            class="bg-orange-600 hover:bg-orange-700 text-white p-1 rounded-full" title="Edit">
            <i class="fas fa-edit text-xs"></i>
        </button>
        @include('components.token-link-action', ['record' => $permitPerancah, 'routeName' => 'working-permit.perancah.token', 'regenerateType' => 'perancah', 'compact' => true])
    </div>
@endif

{{-- Permit Risiko Panas --}}
@if ($permitRisikoPanas)
    <div class="flex items-center gap-2 text-[10px] text-gray-600">
        <span>Permit Risiko Panas:</span>
        <a href="{{ route('working-permit.risiko-panas.preview', ['id' => $permitRisikoPanas->notification_id]) }}"
            class="bg-green-500 hover:bg-green-600 text-white p-1 rounded-full" title="Lihat PDF">
            <i class="fas fa-temperature-high text-xs"></i>
        </a>
        <button @click="activeModal = 'modal-{{ $step['code'] }}-risiko-panas'"
            class="bg-red-600 hover:bg-red-700 text-white p-1 rounded-full" title="Edit">
            <i class="fas fa-edit text-xs"></i>
        </button>
        @include('components.token-link-action', ['record' => $permitRisikoPanas, 'routeName' => 'working-permit.risiko-panas.token', 'regenerateType' => 'risiko-panas', 'compact' => true])
    </div>
@endif
{{-- Permit Beban --}}
@if ($permitBeban)
    <div class="flex items-center gap-2 text-[10px] text-gray-600">
        <span>Permit Beban:</span>
        <a href="{{ route('working-permit.beban.preview', ['id' => $permitBeban->notification_id]) }}"
            class="bg-green-500 hover:bg-green-600 text-white p-1 rounded-full" title="Lihat PDF">
            <i class="fas fa-dumbbell text-xs"></i>
        </a>
        <button @click="activeModal = 'modal-{{ $step['code'] }}-beban'"
            class="bg-indigo-600 hover:bg-indigo-700 text-white p-1 rounded-full" title="Edit">
            <i class="fas fa-edit text-xs"></i>
        </button>
        @include('components.token-link-action', ['record' => $permitBeban, 'routeName' => 'working-permit.beban.token', 'regenerateType' => 'beban', 'compact' => true])
    </div>
@endif
{{-- Permit Penggalian --}}
@if ($permitPenggalian)
    <div class="flex items-center gap-2 text-[10px] text-gray-600">
        <span>Permit Penggalian:</span>
        <a href="{{ route('working-permit.penggalian.preview', ['id' => $permitPenggalian->notification_id]) }}"
            class="bg-green-500 hover:bg-green-600 text-white p-1 rounded-full" title="Lihat PDF">
           <i class="fas fa-digging text-xs"></i>

        </a>
        <button @click="activeModal = 'modal-{{ $step['code'] }}-penggalian'"
            class="bg-yellow-700 hover:bg-yellow-800 text-white p-1 rounded-full" title="Edit">
            <i class="fas fa-edit text-xs"></i>
        </button>
        @include('components.token-link-action', ['record' => $permitPenggalian, 'routeName' => 'working-permit.penggalian.token', 'regenerateType' => 'penggalian', 'compact' => true])
    </div>
@endif
{{-- Permit Pengangkatan --}}
@if ($permitPengangkatan)
    <div class="flex items-center gap-2 text-[10px] text-gray-600">
        <span>Permit Pengangkatan:</span>
        <a href="{{ route('working-permit.pengangkatan.preview', ['id' => $permitPengangkatan->notification_id]) }}"
            class="bg-green-500 hover:bg-green-600 text-white p-1 rounded-full" title="Lihat PDF">
<i class="fas fa-anchor text-xs"></i>
        </a>
        <button @click="activeModal = 'modal-{{ $step['code'] }}-pengangkatan'"
            class="bg-pink-600 hover:bg-pink-700 text-white p-1 rounded-full" title="Edit">
            <i class="fas fa-edit text-xs"></i>
        </button>
        @include('components.token-link-action', ['record' => $permitPengangkatan, 'routeName' => 'working-permit.pengangkatan.token', 'regenerateType' => 'pengangkatan', 'compact' => true])
    </div>
@endif
{{-- Permit Listrik Tegangan Tinggi --}}
@if ($permitListrikTeganganTinggi)
    <div class="flex items-center gap-2 text-[10px] text-gray-600">
        <span>Permit Listrik Tegangan Tinggi:</span>
        <a href="{{ route('working-permit.listrik-tegangan-tinggi.preview', ['id' => $permitListrikTeganganTinggi->notification_id]) }}"
            class="rounded-full bg-green-500 p-1 text-white hover:bg-green-600" title="Lihat PDF">
            <i class="fas fa-bolt text-xs"></i>
        </a>
        <button @click="activeModal = 'modal-{{ $step['code'] }}-listrik-tegangan-tinggi'"
            class="rounded-full bg-yellow-500 p-1 text-white hover:bg-yellow-600" title="Edit">
            <i class="fas fa-edit text-xs"></i>
        </button>
        @include('components.token-link-action', [
            'record' => $permitListrikTeganganTinggi,
            'routeName' => 'working-permit.listrik-tegangan-tinggi.token',
            'regenerateType' => 'listrik-tegangan-tinggi',
            'compact' => true,
        ])
    </div>
@endif
                {{-- Tambah Permit Lain (Selalu Muncul di Bawah) --}}
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

    @if ($step['code'] === 'fakta_integritas')
        @php
                        $prevStepCode = $steps[$index - 1]['code'] ?? null;
            $prevStepApproved = ($stepGateStatuses[$prevStepCode] ?? null) === 'disetujui';
        @endphp

        @if (!$prevStepApproved)
            <span class="text-[10px] text-gray-400 italic mt-1">Langkah perizinan harus dilakukan secara bertahap.</span>
        @else
            @if ($notification)
                @if ($faktaFile = $uploadsByStep->get('fakta_integritas', collect())->first())
                    <div class="flex items-center gap-2">
                        <a href="{{ asset('storage/' . $faktaFile->file_path) }}" target="_blank"
                            class="flex items-center gap-1 bg-green-500 hover:bg-green-600 text-white text-[9px] px-3 py-[3px] rounded-full">
                            Lihat Fakta Integritas
                        </a>
                        <form action="{{ route('upload.delete', $faktaFile->id) }}" method="POST"
                            onsubmit="return confirm('Hapus file ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-500 text-[9px] px-2 py-[1px] border border-red-300 hover:bg-red-100 rounded-full">
                                Hapus
                            </button>
                        </form>
                    </div>
                @else
                    <button @click="activeModal = 'modal-{{ $step['code'] }}'"
                        class="flex items-center gap-1 mt-1 bg-blue-600 hover:bg-blue-700 text-white text-[9px] px-3 py-[3px] rounded-full transition">
                        Upload Fakta Integritas
                    </button>
                    <span class="text-[10px] text-gray-400 italic mt-1">Upload File dalam Bentuk PDF/JPG</span>
                @endif
                @if ($step['status'] === 'revisi')
                        @php
                            $catatanRevisi = $stepNotes['fakta_integritas'] ?? null;
                        @endphp
                        @if ($catatanRevisi)
                            <p class="text-[10px] text-red-600 italic mt-1">Catatan: {{ $catatanRevisi }}</p>
                        @endif
                    @endif
            @else
                <div class="text-center text-[11px] text-gray-500 italic mt-1">Belum ada notifikasi/PO/SPK</div>
            @endif
        @endif
    @endif
    @if ($step['code'] === 'sertifikasi_ak3')
        @php
                        $prevStepCode = $steps[$index - 1]['code'] ?? null;
            $prevStepApproved = ($stepGateStatuses[$prevStepCode] ?? null) === 'disetujui';
        @endphp

        @if (!$prevStepApproved)
            <span class="text-[10px] text-gray-400 italic mt-1">Langkah perizinan harus dilakukan secara bertahap.</span>
        @else
            @if ($notification)
                @if ($ak3File = $uploadsByStep->get('sertifikasi_ak3', collect())->first())
                    <div class="flex items-center gap-2">
                        <a href="{{ asset('storage/' . $ak3File->file_path) }}" target="_blank"
                            class="flex items-center gap-1 bg-green-500 hover:bg-green-600 text-white text-[9px] px-3 py-[3px] rounded-full">
                            Lihat Sertifikasi AK3
                        </a>
                        <form action="{{ route('upload.delete', $ak3File->id) }}" method="POST"
                            onsubmit="return confirm('Hapus file ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-500 text-[9px] px-2 py-[1px] border border-red-300 hover:bg-red-100 rounded-full">
                                Hapus
                            </button>
                        </form>
                    </div>
                @else
                    <button @click="activeModal = 'modal-{{ $step['code'] }}'"
                        class="flex items-center gap-1 mt-1 bg-blue-600 hover:bg-blue-700 text-white text-[9px] px-3 py-[3px] rounded-full transition">
                        Upload Sertifikasi AK3
                    </button>
                    <span class="text-[10px] text-gray-400 italic mt-1">Upload File dalam Bentuk PDF/JPG</span>
                @endif
                @if ($step['status'] === 'revisi')
                        @php
                            $catatanRevisi = $stepNotes['sertifikasi_ak3'] ?? null;
                        @endphp
                        @if ($catatanRevisi)
                            <p class="text-[10px] text-red-600 italic mt-1">Catatan: {{ $catatanRevisi }}</p>
                        @endif
                    @endif
            @else
                <div class="text-center text-[11px] text-gray-500 italic mt-1">Belum ada notifikasi/PO/SPK</div>
            @endif
        @endif
    @endif
@if ($step['code'] === 'ktp')
  @php
    $notifId = $notification?->id;
    $prevStepCode = $steps[$index - 1]['code'] ?? null;

    $prevStepApproved = $notifId && $prevStepCode
        ? (($stepGateStatuses[$prevStepCode] ?? null) === 'disetujui')
        : false;

    $ktpFiles = $uploadsByStep->get('ktp', collect());
@endphp

    @if (!$prevStepApproved)
        <span class="text-[10px] text-gray-400 italic mt-1">
            Langkah perizinan harus dilakukan secara bertahap.
        </span>
    @else
        <div class="flex flex-col items-center space-y-2">
            @foreach ($ktpFiles as $file)
                <div class="flex items-center gap-2">
                    <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank"
                        class="bg-green-500 hover:bg-green-600 text-white text-[9px] px-3 py-[3px] rounded-full">
                        Lihat KTP
                    </a>
                    <form action="{{ route('upload.delete', $file->id) }}" method="POST"
                        onsubmit="return confirm('Hapus file ini?')">
                        @csrf
                        @method('DELETE')
                        <button class="text-red-500 text-[9px] px-2 py-[1px] border border-red-300 hover:bg-red-100 rounded-full">
                            Hapus
                        </button>
                    </form>
                </div>
            @endforeach

            {{-- Tombol Upload Baru --}}
            <button @click="activeModal = 'modal-{{ $step['code'] }}'"
                class="flex items-center gap-1 mt-1 bg-blue-600 hover:bg-blue-700 text-white text-[9px] px-3 py-[3px] rounded-full transition">
                +
            </button>

            <span class="text-[10px] text-gray-400 italic mt-1">*Diharapkan menggabungkan File KTP dalam 1 File PDF.</span>

            @if ($step['status'] === 'revisi')
                @php
                    $catatanRevisi = $stepNotes['ktp'] ?? null;
                @endphp
                @if ($catatanRevisi)
                    <p class="text-[10px] text-red-600 italic mt-1">Catatan: {{ $catatanRevisi }}</p>
                @endif
            @endif
        </div>
    @endif
@endif



    @if ($step['code'] === 'surat_kesehatan')
        @php
                        $prevStepCode = $steps[$index - 1]['code'] ?? null;
            $prevStepApproved = ($stepGateStatuses[$prevStepCode] ?? null) === 'disetujui';
        @endphp

        @if (!$prevStepApproved)
            <span class="text-[10px] text-gray-400 italic mt-1">Langkah perizinan harus dilakukan secara bertahap.</span>
        @else
            @if ($notification)
                @if ($suratKesehatanFile = $uploadsByStep->get('surat_kesehatan', collect())->first())
                    <div class="flex items-center gap-2">
                        <a href="{{ asset('storage/' . $suratKesehatanFile->file_path) }}" target="_blank"
                            class="flex items-center gap-1 bg-green-500 hover:bg-green-600 text-white text-[9px] px-3 py-[3px] rounded-full">
                            Lihat Surat Kesehatan
                        </a>
                        <form action="{{ route('upload.delete', $suratKesehatanFile->id) }}" method="POST"
                            onsubmit="return confirm('Hapus file ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-500 text-[9px] px-2 py-[1px] border border-red-300 hover:bg-red-100 rounded-full">
                                Hapus
                            </button>
                        </form>
                    </div>
                @else
                    <button @click="activeModal = 'modal-{{ $step['code'] }}'"
                        class="flex items-center gap-1 mt-1 bg-blue-600 hover:bg-blue-700 text-white text-[9px] px-3 py-[3px] rounded-full transition">
                        Upload Surat Kesehatan
                    </button>
                    <span class="text-[10px] text-gray-400 italic mt-1">Upload File dalam Bentuk PDF/JPG</span>
                @endif
                @if ($step['status'] === 'revisi')
                        @php
                            $catatanRevisi = $stepNotes['surat_kesehatan'] ?? null;
                        @endphp
                        @if ($catatanRevisi)
                            <p class="text-[10px] text-red-600 italic mt-1">Catatan: {{ $catatanRevisi }}</p>
                        @endif
                    @endif
            @else
                <div class="text-center text-[11px] text-gray-500 italic mt-1">Belum ada notifikasi/PO/SPK</div>
            @endif
        @endif
    @endif
    @if ($step['code'] === 'struktur_organisasi')
        @php
                        $prevStepCode = $steps[$index - 1]['code'] ?? null;
            $prevStepApproved = ($stepGateStatuses[$prevStepCode] ?? null) === 'disetujui';
        @endphp

        @if (!$prevStepApproved)
            <span class="text-[10px] text-gray-400 italic mt-1">Langkah perizinan harus dilakukan secara bertahap.</span>
        @else
            @if ($notification)
                @if ($strukturFile = $uploadsByStep->get('struktur_organisasi', collect())->first())
                    <div class="flex items-center gap-2">
                        <a href="{{ asset('storage/' . $strukturFile->file_path) }}" target="_blank"
                            class="flex items-center gap-1 bg-green-500 hover:bg-green-600 text-white text-[9px] px-3 py-[3px] rounded-full">
                            Lihat Struktur Organisasi
                        </a>
                        <form action="{{ route('upload.delete', $strukturFile->id) }}" method="POST"
                            onsubmit="return confirm('Hapus file ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-500 text-[9px] px-2 py-[1px] border border-red-300 hover:bg-red-100 rounded-full">
                                Hapus
                            </button>
                        </form>
                    </div>
                @else
                    <button @click="activeModal = 'modal-{{ $step['code'] }}'"
                        class="flex items-center gap-1 mt-1 bg-blue-600 hover:bg-blue-700 text-white text-[9px] px-3 py-[3px] rounded-full transition">
                        Upload Struktur Organisasi
                    </button>
                    <span class="text-[10px] text-gray-400 italic mt-1">Upload File dalam Bentuk PDF/JPG</span>
                @endif
                @if ($step['status'] === 'revisi')
                        @php
                            $catatanRevisi = $stepNotes['struktur_organisasi'] ?? null;
                        @endphp
                        @if ($catatanRevisi)
                            <p class="text-[10px] text-red-600 italic mt-1">Catatan: {{ $catatanRevisi }}</p>
                        @endif
                    @endif
            @else
                <div class="text-center text-[11px] text-gray-500 italic mt-1">Belum ada notifikasi/PO/SPK</div>
            @endif
        @endif
    @endif
    @if ($step['code'] === 'post_test')
        @php
                        $prevStepCode = $steps[$index - 1]['code'] ?? null;
            $prevStepApproved = ($stepGateStatuses[$prevStepCode] ?? null) === 'disetujui';
        @endphp

        @if (!$prevStepApproved)
            <span class="text-[10px] text-gray-400 italic mt-1">Langkah perizinan harus dilakukan secara bertahap.</span>
        @else
            @if ($notification)
                @if ($postTestFile = $uploadsByStep->get('post_test', collect())->first())
                    <div class="flex items-center gap-2">
                        <a href="{{ asset('storage/' . $postTestFile->file_path) }}" target="_blank"
                            class="flex items-center gap-1 bg-green-500 hover:bg-green-600 text-white text-[9px] px-3 py-[3px] rounded-full">
                            Lihat Dokumen
                        </a>
                        <form action="{{ route('upload.delete', $postTestFile->id) }}" method="POST"
                            onsubmit="return confirm('Hapus file ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-500 text-[9px] px-2 py-[1px] border border-red-300 hover:bg-red-100 rounded-full">
                                Hapus
                            </button>
                        </form>
                    </div>
                @else
                    <button @click="activeModal = 'modal-{{ $step['code'] }}'"
                        class="flex items-center gap-1 mt-1 bg-blue-600 hover:bg-blue-700 text-white text-[9px] px-3 py-[3px] rounded-full transition">
                        Upload Dokumen
                    </button>
                    <span class="text-[10px] text-gray-400 italic mt-1">Dokumen Safety Induction Seperti Hasil Post Test, Absensi dan Dokumentasi</span>
                    <span class="text-[10px] text-gray-400 italic mt-1">*diharapkan untuk Menggabungkan dalam 1 File PDF</span>
                @endif
                @if ($step['status'] === 'revisi')
                        @php
                            $catatanRevisi = $stepNotes['post_test'] ?? null;
                        @endphp
                        @if ($catatanRevisi)
                            <p class="text-[10px] text-red-600 italic mt-1">Catatan: {{ $catatanRevisi }}</p>
                        @endif
                    @endif
            @else
                <div class="text-center text-[11px] text-gray-500 italic mt-1">Belum ada notifikasi/PO/SPK</div>
            @endif
        @endif
    @endif
  @if ($step['code'] === 'surat_izin_kerja') {{-- atau 13 jika urutan 1-based --}}
    @php
        $prevStepCode = $steps[$index - 1]['code'] ?? null;
        $prevStepApproved = ($stepGateStatuses[$prevStepCode] ?? null) === 'disetujui';
        $statusSik = $stepGateStatuses['sik'] ?? null;
    @endphp

    @if (!$prevStepApproved)
        <span class="text-[10px] text-gray-400 italic mt-1">Menunggu Surat Penyelesaian Pekerjaan dari K3 Semen Tonasa</span>
    @else
        @if ($statusSik === 'disetujui')
            <a href="{{ route('izin-kerja.sik.pdf', $notification->id) }}" target="_blank"
                class="flex items-center gap-1 bg-green-500 hover:bg-green-600 text-white text-[9px] px-3 py-[3px] rounded-full">
                <i class="fas fa-file-pdf text-xs"></i> Lihat Surat Penyelesaian Pekerjaan
            </a>
        @else
            <span class="text-[10px] text-gray-500 italic">Surat Penyelesaian Pekerjaan belum tersedia</span>
        @endif
    @endif
@endif


    @if ($step['code'] === 'bukti_serah_terima')
        @php
                        $prevStepCode = $steps[$index - 1]['code'] ?? null;
            $prevStepApproved = ($stepGateStatuses[$prevStepCode] ?? null) === 'disetujui';
        @endphp

        @if (!$prevStepApproved)
            <span class="text-[10px] text-gray-400 italic mt-1">Langkah perizinan harus dilakukan secara bertahap.</span>
        @else
            @if ($notification)
                @if ($buktiSerahFile = $uploadsByStep->get('bukti_serah_terima', collect())->first())
                    <div class="flex items-center gap-2">
                        <a href="{{ asset('storage/' . $buktiSerahFile->file_path) }}" target="_blank"
                            class="flex items-center gap-1 bg-green-500 hover:bg-green-600 text-white text-[9px] px-3 py-[3px] rounded-full">
                            Lihat Bukti Serah Terima
                        </a>
                        <form action="{{ route('upload.delete', $buktiSerahFile->id) }}" method="POST"
                            onsubmit="return confirm('Hapus file ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-500 text-[9px] px-2 py-[1px] border border-red-300 hover:bg-red-100 rounded-full">
                                Hapus
                            </button>
                        </form>
                    </div>
                @else
                    <button @click="activeModal = 'modal-{{ $step['code'] }}'"
                        class="flex items-center gap-1 mt-1 bg-blue-600 hover:bg-blue-700 text-white text-[9px] px-3 py-[3px] rounded-full transition">
                        Upload Bukti Serah Terima
                    </button>
                @endif
                @if ($step['status'] === 'revisi')
                        @php
                            $catatanRevisi = $stepNotes['bukti_serah_terima'] ?? null;
                        @endphp
                        @if ($catatanRevisi)
                            <p class="text-[10px] text-red-600 italic mt-1">Catatan: {{ $catatanRevisi }}</p>
                        @endif
                    @endif
            @else
                <div class="text-center text-[11px] text-gray-500 italic mt-1">Belum ada notifikasi/PO/SPK</div>
            @endif
        @endif
    @endif

                                @if ($step['code'] === 'op_spk')
                                    @include('components.steps.modal-op', [
                                        'id' => 'modal-' . $step['code'],
                                        'notification' => $notification,
                                        'stepName' => $step['code']
                                    ])
                                    @elseif ($step['code'] === 'data_kontraktor')
                                    {{-- Modal Data Kontraktor --}}
                                    @include('components.steps.modal-data-kontraktor', [
                                        'label' => 'Input Data Kontraktor',
                                        'id' => 'modal-data-kontraktor',
                                        'notification' => $notification,
                                        'stepName' => 'data_kontraktor'
                                    ])
                                
                                @elseif ($step['code'] === 'jsa')
                                    @if (!$jsa)
                                        {{-- Jika BELUM ADA JSA, tampilkan modal CREATE --}}
                                        @include('components.steps.modal-jsa', [
                                            'id' => 'modal-jsa-create',  {{-- ✅ gunakan id tetap --}}
                                            'notification' => $notification,
                                            'stepName' => $step['code']
                                        ])
                                    @else
                                        {{-- Jika SUDAH ADA JSA, tampilkan modal EDIT --}}
                                        @include('components.steps.modal-jsa-edit', [
                                            'id' => 'modal-jsa-edit',  {{-- ✅ gunakan id tetap --}}
                                            'notification' => $notification,
                                            'stepName' => $step['code'],
                                            'jsa' => $jsa
                                        ])
                                    @endif
    @elseif ($step['code'] === 'working_permit')
        @if ($notification)
        @if ($renderPermitFormsEagerly)
        {{-- Modal Permit Umum --}}
        @include('components.steps.modal-working-permit', [
            'id' => 'modal-' . $step['code'],
            'notification' => $notification,
            'stepName' => $step['code'],
            'permitUmum' => $permitUmum ?? null
        ])

        {{-- Modal Permit Gas Panas --}}
        @include('components.steps.modal-working-permit-gaspanas', [
            'label' => 'Edit Gas Panas',
            'id' => 'modal-' . $step['code'] . '-gaspanas',
            'notification' => $notification,
            'stepName' => $step['code'],
            'permitGas' => $permitGas ?? null,
            'detail' => $permitGas?->detail ?? $permitUmumDetail,
            'closure' => $permitGas?->closure ?? $permitUmumClosure
        ])

        {{-- Modal Permit Air --}}
        @include('components.steps.modal-working-permit-air', [
            'label' => 'Edit Air',
            'id' => 'modal-' . $step['code'] . '-air',
            'notification' => $notification,
            'stepName' => $step['code'],
            'permitAir' => $permitAir ?? null,
            'detail' => $permitAir?->detail ?? $permitUmumDetail,
            'closure' => $permitAir?->closure ?? $permitUmumClosure
        ])
                {{-- Modal Permit Ketinggian --}}
        @include('components.steps.modal-working-permit-ketinggian', [
            'label' => 'Edit Ketinggian',
            'id' => 'modal-' . $step['code'] . '-ketinggian',
            'notification' => $notification,
            'stepName' => $step['code'],
            'permitKetinggian' => $permitKetinggian ?? null,
            'detail' => $permitKetinggian?->detail ?? $permitUmumDetail,
            'closure' => $permitKetinggian?->closure ?? $permitUmumClosure
        ])
        {{-- Modal Permit Ruang Tertutup --}}
        @include('components.steps.modal-working-permit-ruang-tertutup', [
            'label' => 'Edit Ruang Tertutup',
            'id' => 'modal-' . $step['code'] . '-ruang-tertutup',
            'notification' => $notification,
            'stepName' => $step['code'],
            'permitRuangTertutup' => $permitRuangTertutup ?? null,
            'detail' => $permitRuangTertutup?->detail ?? $permitUmumDetail,
            'closure' => $permitRuangTertutup?->closure ?? $permitUmumClosure
        ])
        {{-- Modal Permit Perancah --}}
        @include('components.steps.modal-working-permit-perancah', [
            'label' => 'Edit Perancah',
            'id' => 'modal-' . $step['code'] . '-perancah',
            'notification' => $notification,
            'stepName' => $step['code'],
            'permitPerancah' => $permitPerancah ?? null,
            'detail' => $permitPerancah?->detail ?? $permitUmumDetail,
            'closure' => $permitPerancah?->closure ?? $permitUmumClosure
        ])
        {{-- Modal Permit Risiko Panas --}}
        @include('components.steps.modal-working-permit-risiko-panas', [
            'label' => 'Edit Risiko Panas',
            'id' => 'modal-' . $step['code'] . '-risiko-panas',
            'notification' => $notification,
            'stepName' => $step['code'],
            'permitRisikoPanas' => $permitRisikoPanas ?? null,
            'detail' => $permitRisikoPanas?->detail ?? $permitUmumDetail,
            'closure' => $permitRisikoPanas?->closure ?? $permitUmumClosure
        ])
{{-- Modal Permit Beban --}}
@include('components.steps.modal-working-permit-beban', [
    'label' => 'Edit Beban',
    'id' => 'modal-' . $step['code'] . '-beban',
    'notification' => $notification,
    'stepName' => $step['code'],
    'permitBeban' => $permitBeban ?? null,
    'detail' => $permitBeban?->detail ?? $permitUmumDetail,
    'closure' => $permitBeban?->closure ?? $permitUmumClosure
])
{{-- Modal Permit Penggalian --}}
@include('components.steps.modal-working-permit-penggalian', [
    'label' => 'Edit Penggalian',
    'id' => 'modal-' . $step['code'] . '-penggalian',
    'notification' => $notification,
    'stepName' => $step['code'],
    'permitPenggalian' => $permitPenggalian ?? null,
    'detail' => $permitPenggalian?->detail ?? $permitUmumDetail,
    'closure' => $permitPenggalian?->closure ?? $permitUmumClosure
])
{{-- Modal Permit Pengangkatan --}}
@include('components.steps.modal-working-permit-pengangkatan', [
    'label' => 'Edit Pengangkatan',
    'id' => 'modal-' . $step['code'] . '-pengangkatan',
    'notification' => $notification,
    'stepName' => $step['code'],
    'permitPengangkatan' => $permitPengangkatan ?? null,
    'detail' => $permitPengangkatan?->detail ?? $permitUmumDetail,
    'closure' => $permitPengangkatan?->closure ?? $permitUmumClosure
])
{{-- Modal Permit Listrik Tegangan Tinggi --}}
@include('components.steps.modal-working-permit-listrik-tegangan-tinggi', [
    'label' => 'Edit Listrik Tegangan Tinggi',
    'id' => 'modal-' . $step['code'] . '-listrik-tegangan-tinggi',
    'notification' => $notification,
    'stepName' => $step['code'],
    'permitListrikTeganganTinggi' => $permitListrikTeganganTinggi ?? null,
])
        {{-- Modal Permit Lainnya --}}
        @include('components.steps.modal-tambah-lainnya', [
            'label' => 'Tambah Permit Lain',
            'id' => 'modal-tambah-lainnya',
            'notification' => $notification,
            'stepName' => $step['code'],
            'permits' => $permits ?? [],
            'eager' => true
        ])
        @else
            @include('components.steps.modal-working-permit-lazy', ['id' => 'modal-working_permit', 'title' => 'Form Working Permit', 'type' => 'umum', 'notification' => $notification])
            @include('components.steps.modal-working-permit-lazy', ['id' => 'modal-working_permit-gaspanas', 'title' => 'Edit Working Permit Gas Panas', 'type' => 'gaspanas', 'notification' => $notification])
            @include('components.steps.modal-working-permit-lazy', ['id' => 'modal-working_permit-air', 'title' => 'Edit Working Permit Air', 'type' => 'air', 'notification' => $notification, 'maxWidth' => 'max-w-5xl'])
            @include('components.steps.modal-working-permit-lazy', ['id' => 'modal-working_permit-ketinggian', 'title' => 'Edit Working Permit Ketinggian', 'type' => 'ketinggian', 'notification' => $notification, 'maxWidth' => 'max-w-5xl'])
            @include('components.steps.modal-working-permit-lazy', ['id' => 'modal-working_permit-ruang-tertutup', 'title' => 'Edit Working Permit Ruang Tertutup', 'type' => 'ruang-tertutup', 'notification' => $notification])
            @include('components.steps.modal-working-permit-lazy', ['id' => 'modal-working_permit-perancah', 'title' => 'Edit Perancah', 'type' => 'perancah', 'notification' => $notification, 'maxWidth' => 'max-w-6xl'])
            @include('components.steps.modal-working-permit-lazy', ['id' => 'modal-working_permit-risiko-panas', 'title' => 'Edit Risiko Panas', 'type' => 'risiko-panas', 'notification' => $notification])
            @include('components.steps.modal-working-permit-lazy', ['id' => 'modal-working_permit-beban', 'title' => 'Edit Working Permit Beban', 'type' => 'beban', 'notification' => $notification])
            @include('components.steps.modal-working-permit-lazy', ['id' => 'modal-working_permit-penggalian', 'title' => 'Edit Working Permit Penggalian', 'type' => 'penggalian', 'notification' => $notification])
            @include('components.steps.modal-working-permit-lazy', ['id' => 'modal-working_permit-pengangkatan', 'title' => 'Edit Working Permit Pengangkatan', 'type' => 'pengangkatan', 'notification' => $notification])
            @include('components.steps.modal-working-permit-lazy', ['id' => 'modal-working_permit-listrik-tegangan-tinggi', 'title' => 'Edit Listrik Tegangan Tinggi', 'type' => 'listrik-tegangan-tinggi', 'notification' => $notification, 'maxWidth' => 'max-w-5xl'])
            @include('components.steps.modal-tambah-lainnya', [
                'label' => 'Tambah Permit Lain',
                'id' => 'modal-tambah-lainnya',
                'notification' => $notification,
                'stepName' => $step['code'],
                'permits' => $permits ?? [],
                'eager' => false,
            ])
        @endif
        @endif
                                @else
                                   @include('components.steps.modal-upload', [
                                        'id' => 'modal-' . $step['code'],
                                        'notification' => $notification,
                                        'stepName' => $step['code'],
                                        'label' => $step['title'] // ⬅️ Tambahkan ini
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
