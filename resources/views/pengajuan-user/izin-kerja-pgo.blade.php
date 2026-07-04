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
   <p class="text-[11px] text-gray-600 mt-1">
                            <span class="font-semibold">Jabatan:</span>
                            @if (Auth::user()->jabatan)
                                {{ Auth::user()->jabatan }}
                            @else
                                <span class="text-red-600">Harap isi jabatan di halaman profile.</span>
                            @endif
                        </p>
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
                    <h2 class="text-xl font-bold text-center text-gray-800 mb-6">Step Pengajuan Izin Kerja User</h2>
                    <div class="text-center text-sm text-gray-600 mb-6">
    ⚠️ Setiap langkah harus <span class="font-semibold text-blue-600">disetujui oleh Admin K3</span> terlebih dahulu sebelum dapat melanjutkan ke tahap berikutnya.
</div>
                    <div class="flex flex-wrap justify-center items-start gap-y-12 gap-x-8 relative">
                        @php
                            $colors = array(
                                'done' => 'bg-green-500 text-white',
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
                                    @if ($step['status'] === 'revisi' && !empty($stepNotes['op_spk']))
    <p class="text-[10px] text-red-600 italic mt-1">
        Catatan: {{ $stepNotes['op_spk'] }}
    </p>
@endif

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
        $label = 'Input JSA';
    @endphp

    @if (!$step['enabled']) 
        <span class="text-[10px] text-gray-400 italic mt-1">
            Langkah perizinan harus dilakukan secara bertahap.
        </span>
    @else
        <div class="flex flex-col items-center space-y-2">

            {{-- BUTTON / DATA --}}
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

                @include('components.token-link-action', [
                    'record' => $jsa,
                    'routeName' => 'jsa.form.token',
                    'regenerateType' => 'jsa',
                ])
            @endif

            {{-- CATATAN REVISI --}}
            @if ($step['status'] === 'revisi' && !empty($stepNotes['jsa']))
                <p class="text-[10px] text-red-600 italic mt-1">
                    Catatan: {{ $stepNotes['jsa'] }}
                </p>
            @endif

        </div>
    @endif
@endif
    
                                @if ($step['code'] === 'working_permit')
                                   @php
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
@endphp

                                    @if (!$step['enabled'])
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
                                            @if ($permitUmum)
                                                @include('components.token-link-action', [
                                                    'record' => $permitUmum,
                                                    'routeName' => 'working-permit.umum.token',
                                                    'regenerateType' => 'umum',
                                                    'helpText' => 'Salin link berikut dan kirim ke pihak terkait untuk mengisi atau tanda tangan:',
                                                ])
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
                                                    @include('components.token-link-action', ['record' => $permitGas, 'routeName' => 'working-permit.gaspanas.token', 'regenerateType' => 'gaspanas', 'compact' => true])
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
                                                    @include('components.token-link-action', ['record' => $permitAir, 'routeName' => 'working-permit.air.token', 'regenerateType' => 'air', 'compact' => true])
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
                                                    @include('components.token-link-action', ['record' => $permitKetinggian, 'routeName' => 'working-permit.ketinggian.token', 'regenerateType' => 'ketinggian', 'compact' => true])
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
                                                    @include('components.token-link-action', ['record' => $permitRuangTertutup, 'routeName' => 'working-permit.ruangtertutup.token', 'regenerateType' => 'ruang-tertutup', 'compact' => true])
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
                                                    @include('components.token-link-action', ['record' => $permitPerancah, 'routeName' => 'working-permit.perancah.token', 'regenerateType' => 'perancah', 'compact' => true])
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
                                                    @include('components.token-link-action', ['record' => $permitRisikoPanas, 'routeName' => 'working-permit.risiko-panas.token', 'regenerateType' => 'risiko-panas', 'compact' => true])
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
                                                    @include('components.token-link-action', ['record' => $permitBeban, 'routeName' => 'working-permit.beban.token', 'regenerateType' => 'beban', 'compact' => true])
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
                                                    @include('components.token-link-action', ['record' => $permitPenggalian, 'routeName' => 'working-permit.penggalian.token', 'regenerateType' => 'penggalian', 'compact' => true])
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
                                                    @include('components.token-link-action', ['record' => $permitPengangkatan, 'routeName' => 'working-permit.pengangkatan.token', 'regenerateType' => 'pengangkatan', 'compact' => true])
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
