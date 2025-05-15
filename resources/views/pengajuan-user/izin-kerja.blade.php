<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-sm text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Pengajuan Izin Kerja') }}
        </h2>
    </x-slot>

    <section class="bg-cover bg-center bg-no-repeat py-10 px-4" style="background-image: url('/images/bg-login.jpg');">
        <div class="max-w-6xl mx-auto bg-white rounded-xl shadow-md p-6">
            <div x-data="{ expanded: false, activeModal: null }">
                @if(session('success'))
                    <div class="bg-green-500 text-white p-2 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="flex flex-wrap justify-between items-center text-sm text-gray-800 mb-4">
                    <div>
                        <p><span class="font-semibold">Vendor/User:</span> {{ Auth::user()->name }}</p>
                        <p><span class="font-semibold">Admin K3:</span> Budi Kurniawan</p>
                    </div>
                    <div>
                        <p><span class="font-semibold">Tanggal:</span> {{ now()->format('d-m-Y H:i') }}</p>
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

                            $buttonLabels = array(
                                'Input OP/SPK/Notification',
                                'Upload Dokumen',
                                'Buat JSA',
                                'Buat Working Permit',
                                'Upload Dokumen',
                                'Upload Dokumen',
                                'Upload Dokumen',
                                'Upload Dokumen',
                                'Upload Dokumen',
                                'Upload Dokumen',
                                'Lihat SIK',
                                'Upload Dokumen',
                            );
                        @endphp

                        @foreach ($steps as $index => $step)
                            @php
                                $color = $colors[$step['status']] ?? $colors['pending'];
                                $isLast = $index === count($steps) - 1;
                                $label = $buttonLabels[$index];
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
                                  @if ($index === 0)
                                    @if ($notification)
                                        <div class="text-center text-[11px] text-gray-700 font-medium leading-tight mt-1">
                                            {{ strtoupper($notification->type) }}: {{ $notification->number }}<br>
                                            Tanggal: {{ \Carbon\Carbon::parse($notification->created_at)->format('d-m-Y H:i') }}
                                        </div>

                                        @if ($notification->file)
                                            <a href="{{ asset('storage/' . $notification->file) }}" target="_blank"
                                                class="flex items-center gap-1 mt-1 bg-green-500 hover:bg-green-600 text-white text-[9px] px-3 py-[3px] rounded-full">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                Lihat File SPK/PO
                                            </a>
                                        @endif
                                                @if ($step['status'] === 'revisi')
            @php
                $catatanRevisi = \App\Models\StepApproval::where('notification_id', $notification->id)
                    ->where('step', 'op_spk')
                    ->value('catatan');
            @endphp
            @if ($catatanRevisi)
                <p class="text-[10px] text-red-600 italic mt-1">Catatan: {{ $catatanRevisi }}</p>
            @endif
        @endif
                                    @else
                                        <div class="text-center text-[11px] text-gray-500 italic mt-1">
                                            Belum ada notifikasi/PO/SPK
                                        </div>
                                        <button @click="activeModal = 'modal-{{ $index }}'"
                                            class="flex items-center gap-1 mt-1 bg-blue-600 hover:bg-blue-700 text-white text-[9px] px-3 py-[3px] rounded-full transition">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            {{ $label }}
                                        </button>
                                    @endif
                                @endif
                                @if ($index === 1)
    @php
        $prevStepApproved = \App\Models\StepApproval::where('notification_id', $notification?->id)
            ->where('step', 'op_spk')->value('status') === 'disetujui';
    @endphp
    @if (!$prevStepApproved)
        <span class="text-[10px] text-gray-400 italic mt-1">Langkah perizinan harus dilakukan secara bertahap.</span>
    @else
        {{-- LOGIC LAMA BPJS --}}
        @if ($notification)
            @if ($bpjsFile = \App\Models\Upload::where('notification_id', $notification->id)->where('step', 'bpjs')->first())
                <a href="{{ asset('storage/' . $bpjsFile->file_path) }}" target="_blank"
                    class="flex items-center gap-1 bg-green-500 hover:bg-green-600 text-white text-[9px] px-3 py-[3px] rounded-full">
                    Lihat BPJS
                </a>
            @else
                <button @click="activeModal = 'modal-{{ $index }}'"
                    class="flex items-center gap-1 mt-1 bg-blue-600 hover:bg-blue-700 text-white text-[9px] px-3 py-[3px] rounded-full transition">
                    Upload BPJS
                </button>
            @endif
            @if ($step['status'] === 'revisi')
    @php
        $catatanRevisi = \App\Models\StepApproval::where('notification_id', $notification->id)
            ->where('step', 'bpjs')
            ->value('catatan');
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

@if ($index === 2)
    @php
        $prevStepApproved = \App\Models\StepApproval::where('notification_id', $notification?->id)
            ->where('step', 'bpjs')->value('status') === 'disetujui';
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
@if ($index === 3)
    @php
        $prevStepApproved = \App\Models\StepApproval::where('notification_id', $notification?->id)
            ->where('step', 'jsa')->value('status') === 'disetujui';

        $permit = optional($notification)->id 
            ? \App\Models\UmumWorkPermit::where('notification_id', $notification->id)->first() 
            : null;
    @endphp

    @if (!$prevStepApproved)
        <span class="text-[10px] text-gray-400 italic mt-1">Langkah perizinan harus dilakukan secara bertahap.</span>
    @else
        <div class="flex flex-col items-center space-y-2">
            @if ($permit)
                <a href="{{ route('working-permit.umum.preview', ['id' => $permit->id]) }}" target="_blank"
                    class="flex items-center gap-1 bg-green-500 hover:bg-green-600 text-white text-[10px] px-4 py-[5px] rounded-full">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Lihat PDF
                </a>
                <button @click="activeModal = 'modal-{{ $index }}'"
                    class="flex items-center gap-1 bg-yellow-500 hover:bg-yellow-600 text-white text-[10px] px-4 py-[5px] rounded-full transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h2m-2 4h2m-2 4h2m-2 4h2m4-20a2 2 0 00-2 2v16a2 2 0 002 2h4a2 2 0 002-2V6l-6-6z"/>
                    </svg>
                    Edit
                </button>
            @else
                <button @click="activeModal = 'modal-{{ $index }}'"
                    class="flex items-center gap-1 mt-2 bg-blue-600 hover:bg-blue-700 text-white text-[10px] px-4 py-[5px] rounded-full transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    {{ $label }}
                </button>
            @endif
               @if ($step['status'] === 'revisi')
                    @php
                        $catatanRevisi = \App\Models\StepApproval::where('notification_id', $notification->id)
                            ->where('step', 'working_permit')
                            ->value('catatan');
                    @endphp
                    @if ($catatanRevisi)
                        <p class="text-[10px] text-red-600 italic mt-1">Catatan: {{ $catatanRevisi }}</p>
                    @endif
                @endif
        </div>
    @endif
@endif
@if ($index === 4)
    @php
        $prevStepApproved = \App\Models\StepApproval::where('notification_id', $notification?->id)
            ->where('step', 'working_permit')->value('status') === 'disetujui';
    @endphp

    @if (!$prevStepApproved)
        <span class="text-[10px] text-gray-400 italic mt-1">Langkah perizinan harus dilakukan secara bertahap.</span>
    @else
        @if ($notification)
            @if ($faktaFile = \App\Models\Upload::where('notification_id', $notification->id)->where('step', 'fakta_integritas')->first())
                <a href="{{ asset('storage/' . $faktaFile->file_path) }}" target="_blank"
                    class="flex items-center gap-1 bg-green-500 hover:bg-green-600 text-white text-[9px] px-3 py-[3px] rounded-full">
                    Lihat Fakta Integritas
                </a>
            @else
                <button @click="activeModal = 'modal-{{ $index }}'"
                    class="flex items-center gap-1 mt-1 bg-blue-600 hover:bg-blue-700 text-white text-[9px] px-3 py-[3px] rounded-full transition">
                    Upload Fakta Integritas
                </button>
            @endif
            @if ($step['status'] === 'revisi')
                    @php
                        $catatanRevisi = \App\Models\StepApproval::where('notification_id', $notification->id)
                            ->where('step', 'fakta_integritas')
                            ->value('catatan');
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
@if ($index === 5)
    @php
        $prevStepApproved = \App\Models\StepApproval::where('notification_id', $notification?->id)
            ->where('step', 'fakta_integritas')->value('status') === 'disetujui';
    @endphp

    @if (!$prevStepApproved)
        <span class="text-[10px] text-gray-400 italic mt-1">Langkah perizinan harus dilakukan secara bertahap.</span>
    @else
        @if ($notification)
            @if ($ak3File = \App\Models\Upload::where('notification_id', $notification->id)->where('step', 'sertifikasi_ak3')->first())
                <a href="{{ asset('storage/' . $ak3File->file_path) }}" target="_blank"
                    class="flex items-center gap-1 bg-green-500 hover:bg-green-600 text-white text-[9px] px-3 py-[3px] rounded-full">
                    Lihat Sertifikasi AK3
                </a>
            @else
                <button @click="activeModal = 'modal-{{ $index }}'"
                    class="flex items-center gap-1 mt-1 bg-blue-600 hover:bg-blue-700 text-white text-[9px] px-3 py-[3px] rounded-full transition">
                    Upload Sertifikasi AK3
                </button>
            @endif
             @if ($step['status'] === 'revisi')
                    @php
                        $catatanRevisi = \App\Models\StepApproval::where('notification_id', $notification->id)
                            ->where('step', 'sertifikasi_ak3')
                            ->value('catatan');
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
@if ($index === 6)
    @php
        $prevStepApproved = \App\Models\StepApproval::where('notification_id', $notification?->id)
            ->where('step', 'sertifikasi_ak3')->value('status') === 'disetujui';
    @endphp

    @if (!$prevStepApproved)
        <span class="text-[10px] text-gray-400 italic mt-1">Langkah perizinan harus dilakukan secara bertahap.</span>
    @else
        @if ($notification)
            @if ($ktpFile = \App\Models\Upload::where('notification_id', $notification->id)->where('step', 'ktp')->first())
                <a href="{{ asset('storage/' . $ktpFile->file_path) }}" target="_blank"
                    class="flex items-center gap-1 bg-green-500 hover:bg-green-600 text-white text-[9px] px-3 py-[3px] rounded-full">
                    Lihat KTP
                </a>
            @else
                <button @click="activeModal = 'modal-{{ $index }}'"
                    class="flex items-center gap-1 mt-1 bg-blue-600 hover:bg-blue-700 text-white text-[9px] px-3 py-[3px] rounded-full transition">
                    Upload KTP
                </button>
            @endif
             @if ($step['status'] === 'revisi')
                    @php
                        $catatanRevisi = \App\Models\StepApproval::where('notification_id', $notification->id)
                            ->where('step', 'ktp')
                            ->value('catatan');
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
@if ($index === 7)
    @php
        $prevStepApproved = \App\Models\StepApproval::where('notification_id', $notification?->id)
            ->where('step', 'ktp')->value('status') === 'disetujui';
    @endphp

    @if (!$prevStepApproved)
        <span class="text-[10px] text-gray-400 italic mt-1">Langkah perizinan harus dilakukan secara bertahap.</span>
    @else
        @if ($notification)
            @if ($suratKesehatanFile = \App\Models\Upload::where('notification_id', $notification->id)->where('step', 'surat_kesehatan')->first())
                <a href="{{ asset('storage/' . $suratKesehatanFile->file_path) }}" target="_blank"
                    class="flex items-center gap-1 bg-green-500 hover:bg-green-600 text-white text-[9px] px-3 py-[3px] rounded-full">
                    Lihat Surat Kesehatan
                </a>
            @else
                <button @click="activeModal = 'modal-{{ $index }}'"
                    class="flex items-center gap-1 mt-1 bg-blue-600 hover:bg-blue-700 text-white text-[9px] px-3 py-[3px] rounded-full transition">
                    Upload Surat Kesehatan
                </button>
            @endif
             @if ($step['status'] === 'revisi')
                    @php
                        $catatanRevisi = \App\Models\StepApproval::where('notification_id', $notification->id)
                            ->where('step', 'surat_kesehatan')
                            ->value('catatan');
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
@if ($index === 8)
    @php
        $prevStepApproved = \App\Models\StepApproval::where('notification_id', $notification?->id)
            ->where('step', 'surat_kesehatan')->value('status') === 'disetujui';
    @endphp

    @if (!$prevStepApproved)
        <span class="text-[10px] text-gray-400 italic mt-1">Langkah perizinan harus dilakukan secara bertahap.</span>
    @else
        @if ($notification)
            @if ($strukturFile = \App\Models\Upload::where('notification_id', $notification->id)->where('step', 'struktur_organisasi')->first())
                <a href="{{ asset('storage/' . $strukturFile->file_path) }}" target="_blank"
                    class="flex items-center gap-1 bg-green-500 hover:bg-green-600 text-white text-[9px] px-3 py-[3px] rounded-full">
                    Lihat Struktur Organisasi
                </a>
            @else
                <button @click="activeModal = 'modal-{{ $index }}'"
                    class="flex items-center gap-1 mt-1 bg-blue-600 hover:bg-blue-700 text-white text-[9px] px-3 py-[3px] rounded-full transition">
                    Upload Struktur Organisasi
                </button>
            @endif
              @if ($step['status'] === 'revisi')
                    @php
                        $catatanRevisi = \App\Models\StepApproval::where('notification_id', $notification->id)
                            ->where('step', 'struktur_organisasi')
                            ->value('catatan');
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
@if ($index === 9)
    @php
        $prevStepApproved = \App\Models\StepApproval::where('notification_id', $notification?->id)
            ->where('step', 'struktur_organisasi')->value('status') === 'disetujui';
    @endphp

    @if (!$prevStepApproved)
        <span class="text-[10px] text-gray-400 italic mt-1">Langkah perizinan harus dilakukan secara bertahap.</span>
    @else
        @if ($notification)
            @if ($postTestFile = \App\Models\Upload::where('notification_id', $notification->id)->where('step', 'post_test')->first())
                <a href="{{ asset('storage/' . $postTestFile->file_path) }}" target="_blank"
                    class="flex items-center gap-1 bg-green-500 hover:bg-green-600 text-white text-[9px] px-3 py-[3px] rounded-full">
                    Lihat Post Test
                </a>
            @else
                <button @click="activeModal = 'modal-{{ $index }}'"
                    class="flex items-center gap-1 mt-1 bg-blue-600 hover:bg-blue-700 text-white text-[9px] px-3 py-[3px] rounded-full transition">
                    Upload Post Test
                </button>
            @endif
              @if ($step['status'] === 'revisi')
                    @php
                        $catatanRevisi = \App\Models\StepApproval::where('notification_id', $notification->id)
                            ->where('step', 'post_test')
                            ->value('catatan');
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
@if ($index === 10) {{-- atau 11 tergantung posisinya --}}
    @php
        $prevStepApproved = \App\Models\StepApproval::where('notification_id', $notification?->id)
            ->where('step', 'post_test')->value('status') === 'disetujui';

        $sikFilePath = \App\Models\StepApproval::where('notification_id', $notification?->id)
            ->where('step', 'sik')
            ->value('file_path');
    @endphp

    @if (!$prevStepApproved)
        <span class="text-[10px] text-gray-400 italic mt-1">Langkah perizinan harus dilakukan secara bertahap.</span>
    @else
        @if ($sikFilePath)
            <a href="{{ asset('storage/' . $sikFilePath) }}" target="_blank"
                class="flex items-center gap-1 bg-green-500 hover:bg-green-600 text-white text-[9px] px-3 py-[3px] rounded-full">
                <i class="fas fa-file-pdf text-xs"></i> Lihat Surat Izin Kerja
            </a>
        @else
            <span class="text-[10px] text-gray-500 italic">Surat Izin Kerja belum tersedia</span>
        @endif
    @endif
@endif


@if ($index === 11)
    @php
        $prevStepApproved = \App\Models\StepApproval::where('notification_id', $notification?->id)
            ->where('step', 'post_test')->value('status') === 'disetujui';
    @endphp

    @if (!$prevStepApproved)
        <span class="text-[10px] text-gray-400 italic mt-1">Langkah perizinan harus dilakukan secara bertahap.</span>
    @else
        @if ($notification)
            @if ($buktiSerahFile = \App\Models\Upload::where('notification_id', $notification->id)->where('step', 'bukti_serah_terima')->first())
                <a href="{{ asset('storage/' . $buktiSerahFile->file_path) }}" target="_blank"
                    class="flex items-center gap-1 bg-green-500 hover:bg-green-600 text-white text-[9px] px-3 py-[3px] rounded-full">
                    Lihat Bukti Serah Terima
                </a>
            @else
                <button @click="activeModal = 'modal-{{ $index }}'"
                    class="flex items-center gap-1 mt-1 bg-blue-600 hover:bg-blue-700 text-white text-[9px] px-3 py-[3px] rounded-full transition">
                    Upload Bukti Serah Terima
                </button>
            @endif
             @if ($step['status'] === 'revisi')
                    @php
                        $catatanRevisi = \App\Models\StepApproval::where('notification_id', $notification->id)
                            ->where('step', 'bukti_serah_terima')
                            ->value('catatan');
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

                            @if ($index === 0)
                                @include('components.steps.modal-op', [
                                    'label' => $label,
                                    'id' => 'modal-' . $index,
                                    'notification' => $notification,
                                    'stepName' => $step['code']
                                ])
                            @elseif ($index === 2)
                                @if (!$jsa)
                                    {{-- Jika BELUM ADA JSA, tampilkan modal CREATE --}}
                                    @include('components.steps.modal-jsa', [
                                        'label' => $label,
                                        'id' => 'modal-jsa-create',  {{-- ✅ gunakan id tetap --}}
                                        'notification' => $notification,
                                        'stepName' => $step['code']
                                    ])
                                @else
                                    {{-- Jika SUDAH ADA JSA, tampilkan modal EDIT --}}
                                    @include('components.steps.modal-jsa-edit', [
                                        'label' => $label,
                                        'id' => 'modal-jsa-edit',  {{-- ✅ gunakan id tetap --}}
                                        'notification' => $notification,
                                        'stepName' => $step['code'],
                                        'jsa' => $jsa
                                    ])
                                @endif
                            @elseif ($index === 3)
                                @include('components.steps.modal-working-permit', [
                                    'label' => $label,
                                    'id' => 'modal-' . $index,
                                    'notification' => $notification,
                                    'stepName' => $step['code']
                                ])
                            @elseif ($index === 11)
                                @include('components.steps.modal-view-certificate', [
                                    'label' => $label,
                                    'id' => 'modal-' . $index,
                                    'notification' => $notification,
                                    'stepName' => $step['code']
                                ])
                            @else
                                @include('components.steps.modal-upload', [
                                    'label' => $label,
                                    'id' => 'modal-' . $index,
                                    'notification' => $notification,
                                    'stepName' => $step['code']
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
