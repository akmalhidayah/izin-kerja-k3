    <x-app-layout>
        <x-slot name="header">
            <h2 class="font-semibold text-sm text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Pengajuan Izin Kerja') }}
            </h2>
        </x-slot>

        <section class="bg-cover bg-center bg-no-repeat py-10 px-4" style="background-image: url('/images/bg-login.jpg');">
            <div class="max-w-6xl mx-auto bg-white rounded-xl shadow-md p-6">
                <div x-data="{ expanded: true, activeModal: null, selectedPermit: 'umum' }">
                    <div class="flex justify-between items-center mb-4">
    <!-- Dropdown Pilih Notifikasi -->
<form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-3">
    <label for="notification_id" class="text-sm font-semibold text-gray-800 whitespace-nowrap">Pilih Pengajuan</label>
    
    <div class="relative w-[280px]">
        <select name="notification_id" id="notification_id" onchange="this.form.submit()"
            class="appearance-none w-full bg-white border border-gray-300 text-sm rounded-lg py-2 px-4 pr-10 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            @foreach ($notifications as $notif)
                <option value="{{ $notif->id }}" {{ $notif->id == $selectedId ? 'selected' : '' }}>
                    {{ $notif->number }} - {{ $notif->created_at->format('d/m/Y') }}
                </option>
            @endforeach
        </select>

        <!-- Custom dropdown arrow -->
        <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>
        </div>
    </div>
</form>

    <!-- Tombol Buat Notifikasi Baru -->
<button @click="activeModal = 'modal-0'"
    class="bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2 rounded shadow">
    + Buat Pengajuan Baru
</button>

</div>

                    @if(session('success'))
                        <div class="bg-green-500 text-white p-2 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="flex flex-wrap justify-between items-center text-sm text-gray-800 mb-4">
                        <div>
                            <p><span class="font-semibold">Vendor/User:</span> {{ Auth::user()->name }}</p>
                        <p><span class="font-semibold">Admin K3:</span> {{ $notification?->assignedAdmin?->name ?? '-' }}</p>
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
                              @if ($index === 0)
    @php
        $label = $step['label'] ?? 'Input Notification/PO/SPK'; // Default label aman
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
        $prevStepCode = $steps[$index - 1]['code'] ?? null;
        $prevStepApproved = $prevStepCode 
            ? (\App\Models\StepApproval::where('notification_id', $notification?->id)
                ->where('step', $prevStepCode)
                ->value('status') === 'disetujui')
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
@if ($dataKontraktor && $dataKontraktor->token)
    <div class="mt-2 text-xs text-gray-700">
        Salin link berikut dan kirimkan ke pihak terkait untuk tanda tangan:
        <div class="flex items-center gap-2 mt-1">
            <input type="text" value="{{ route('izin-kerja.data-kontraktor.token', $dataKontraktor->token) }}" readonly
                class="text-xs border-gray-300 rounded p-1 w-full bg-gray-100">
            <button type="button" onclick="navigator.clipboard.writeText('{{ route('izin-kerja.data-kontraktor.token', $dataKontraktor->token) }}'); alert('Link berhasil disalin!')"
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
                            ->where('step', 'data_kontraktor')
                            ->value('catatan');
                    @endphp
                    @if ($catatanRevisi)
                        <p class="text-[10px] text-red-600 italic mt-1">Catatan: {{ $catatanRevisi }}</p>
                    @endif
                @endif
            </div>
        @endif
    @endif
@if ($index === 2)
   @php
    $prevStepCode = $steps[$index - 1]['code'] ?? null;

    $prevStepApproved = $prevStepCode 
        ? (\App\Models\StepApproval::where('notification_id', $notification?->id)
            ->where('step', $prevStepCode)->value('status') === 'disetujui')
        : true;

    $notifId = $notification?->id;
    $bpjsFiles = $notifId
        ? \App\Models\Upload::where('notification_id', $notifId)
            ->where('step', 'bpjs')->get()
        : collect();
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
            <button @click="activeModal = 'modal-{{ $index }}'"
                class="flex items-center gap-1 mt-1 bg-blue-600 hover:bg-blue-700 text-white text-[9px] px-3 py-[3px] rounded-full transition">
                +
            </button>
            <span class="text-[10px] text-gray-400 italic mt-1">Upload File dalam Bentuk PDF/JPG</span>

            {{-- Catatan revisi --}}
            @if ($step['status'] === 'revisi')
                @php
                    $catatanRevisi = \App\Models\StepApproval::where('notification_id', $notification->id)
                        ->where('step', 'bpjs')->value('catatan');
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
        $prevStepCode = $steps[$index - 1]['code'] ?? null;
        $prevStepApproved = $prevStepCode 
            ? (\App\Models\StepApproval::where('notification_id', $notification?->id)
                ->where('step', $prevStepCode)->value('status') === 'disetujui')
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
    @if ($index === 4)
        @php
                        $prevStepCode = $steps[$index - 1]['code'] ?? null;
            $prevStepApproved = $notification
                ? (\App\Models\StepApproval::where('notification_id', $notification->id)
                    ->where('step', $prevStepCode)
                    ->value('status') === 'disetujui')
                : false;

            $permitUmum = $notification
                ? \App\Models\UmumWorkPermit::where('notification_id', $notification->id)->first()
                : null;

            $permitGas = $notification
                ? \App\Models\WorkPermitGasPanas::where('notification_id', $notification->id)->first()
                : null;

            $permit = $notification
                ? \App\Models\WorkPermitAir::where('notification_id', $notification->id)->first()
                : null;
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
                            <button @click="activeModal = 'modal-{{ $index }}'"
                                class="bg-yellow-500 hover:bg-yellow-600 text-white p-1 rounded-full" title="Edit Umum">
                                <i class="fas fa-edit text-xs"></i>
                            </button>
                        </div>
                    </div>
                @else
                    <div class="flex flex-col items-center">
                        <span class="text-[10px] text-gray-600 mb-1">Permit Umum</span>
                        <button @click="activeModal = 'modal-{{ $index }}'"
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


                {{-- Permit Gas Panas --}}
                @if ($permitGas)
                    <div class="flex flex-col items-center">
                        <span class="text-[10px] text-gray-600 mb-1">Permit Gas Panas</span>
                        <div class="flex gap-1">
                            <a href="{{ route('working-permit.gaspanas.preview', ['id' => $permitGas->notification_id]) }}"
                                class="bg-green-500 hover:bg-green-600 text-white p-1 rounded-full" title="Lihat PDF Gas Panas">
                                <i class="fas fa-fire text-xs"></i>
                            </a>
                            <button @click="activeModal = 'modal-{{ $index }}-gaspanas'"
                                class="bg-amber-600 hover:bg-amber-700 text-white p-1 rounded-full" title="Edit Gas Panas">
                                <i class="fas fa-edit text-xs"></i>
                            </button>
                        </div>
                    </div>
                @endif

                {{-- Permit Air --}}
                @if ($permit)
                    <div class="flex flex-col items-center">
                        <span class="text-[10px] text-gray-600 mb-1">Permit Air</span>
                        <div class="flex gap-1">
                            <a href="{{ route('working-permit.air.preview', ['id' => $permit->notification_id]) }}"
                                class="bg-green-500 hover:bg-green-600 text-white p-1 rounded-full" title="Lihat PDF Air">
                                <i class="fas fa-water text-xs"></i>
                            </a>
                            <button @click="activeModal = 'modal-{{ $index }}-air'"
                                class="bg-teal-600 hover:bg-teal-700 text-white p-1 rounded-full" title="Edit Air">
                                <i class="fas fa-edit text-xs"></i>
                            </button>
                        </div>
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

    @if ($index === 5)
        @php
                        $prevStepCode = $steps[$index - 1]['code'] ?? null;
            $prevStepApproved = \App\Models\StepApproval::where('notification_id', $notification?->id)
                ->where('step', $prevStepCode)->value('status') === 'disetujui';
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
                    <span class="text-[10px] text-gray-400 italic mt-1">Upload File dalam Bentuk PDF/JPG</span>
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
    @if ($index === 6)
        @php
                        $prevStepCode = $steps[$index - 1]['code'] ?? null;
            $prevStepApproved = \App\Models\StepApproval::where('notification_id', $notification?->id)
                ->where('step', $prevStepCode)->value('status') === 'disetujui';
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
                    <span class="text-[10px] text-gray-400 italic mt-1">Upload File dalam Bentuk PDF/JPG</span>
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
@if ($index === 7)
  @php
    $notifId = $notification?->id;
    $prevStepCode = $steps[$index - 1]['code'] ?? null;

    $prevStepApproved = $notifId && $prevStepCode
        ? (\App\Models\StepApproval::where('notification_id', $notifId)
            ->where('step', $prevStepCode)
            ->value('status') === 'disetujui')
        : false;

    $ktpFiles = $notifId
        ? \App\Models\Upload::where('notification_id', $notifId)
            ->where('step', 'ktp')
            ->get()
        : collect();
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
            <button @click="activeModal = 'modal-{{ $index }}'"
                class="flex items-center gap-1 mt-1 bg-blue-600 hover:bg-blue-700 text-white text-[9px] px-3 py-[3px] rounded-full transition">
                +
            </button>

            <span class="text-[10px] text-gray-400 italic mt-1">*Diharapkan menggabungkan File KTP dalam 1 File PDF.</span>

            @if ($step['status'] === 'revisi')
                @php
                    $catatanRevisi = \App\Models\StepApproval::where('notification_id', $notification->id)
                        ->where('step', 'ktp')->value('catatan');
                @endphp
                @if ($catatanRevisi)
                    <p class="text-[10px] text-red-600 italic mt-1">Catatan: {{ $catatanRevisi }}</p>
                @endif
            @endif
        </div>
    @endif
@endif



    @if ($index === 8)
        @php
                        $prevStepCode = $steps[$index - 1]['code'] ?? null;
            $prevStepApproved = \App\Models\StepApproval::where('notification_id', $notification?->id)
                ->where('step', $prevStepCode)->value('status') === 'disetujui';
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
                    <span class="text-[10px] text-gray-400 italic mt-1">Upload File dalam Bentuk PDF/JPG</span>
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
    @if ($index === 9)
        @php
                        $prevStepCode = $steps[$index - 1]['code'] ?? null;
            $prevStepApproved = \App\Models\StepApproval::where('notification_id', $notification?->id)
                ->where('step', $prevStepCode)->value('status') === 'disetujui';
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
                    <span class="text-[10px] text-gray-400 italic mt-1">Upload File dalam Bentuk PDF/JPG</span>
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
    @if ($index === 10)
        @php
                        $prevStepCode = $steps[$index - 1]['code'] ?? null;
            $prevStepApproved = \App\Models\StepApproval::where('notification_id', $notification?->id)
                ->where('step', $prevStepCode)->value('status') === 'disetujui';
        @endphp

        @if (!$prevStepApproved)
            <span class="text-[10px] text-gray-400 italic mt-1">Langkah perizinan harus dilakukan secara bertahap.</span>
        @else
            @if ($notification)
                @if ($postTestFile = \App\Models\Upload::where('notification_id', $notification->id)->where('step', 'post_test')->first())
                    <a href="{{ asset('storage/' . $postTestFile->file_path) }}" target="_blank"
                        class="flex items-center gap-1 bg-green-500 hover:bg-green-600 text-white text-[9px] px-3 py-[3px] rounded-full">
                        Lihat Dokumen
                    </a>
                @else
                    <button @click="activeModal = 'modal-{{ $index }}'"
                        class="flex items-center gap-1 mt-1 bg-blue-600 hover:bg-blue-700 text-white text-[9px] px-3 py-[3px] rounded-full transition">
                        Upload Dokumen
                    </button>
                    <span class="text-[10px] text-gray-400 italic mt-1">Dokumen Safety Induction Seperti Hasil Post Test, Absensi dan Dokumentasi</span>
                    <span class="text-[10px] text-gray-400 italic mt-1">*diharapkan untuk Menggabungkan dalam 1 File PDF</span>
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
  @if ($index === 12) {{-- atau 13 jika urutan 1-based --}}
    @php
        $prevStepCode = $steps[$index - 1]['code'] ?? null;
        $prevStepApproved = \App\Models\StepApproval::where('notification_id', $notification?->id)
            ->where('step', $prevStepCode)->value('status') === 'disetujui';

        $statusSik = \App\Models\StepApproval::where('notification_id', $notification?->id)
            ->where('step', 'sik')
            ->value('status');
    @endphp

    @if (!$prevStepApproved)
        <span class="text-[10px] text-gray-400 italic mt-1">Menunggu Surat Izin Kerja dari K3 Semen Tonasa</span>
    @else
        @if ($statusSik === 'disetujui')
            <a href="{{ route('izin-kerja.sik.pdf', $notification->id) }}" target="_blank"
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
                        $prevStepCode = $steps[$index - 1]['code'] ?? null;
            $prevStepApproved = \App\Models\StepApproval::where('notification_id', $notification?->id)
                ->where('step', $prevStepCode)->value('status') === 'disetujui';
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
                                        'id' => 'modal-' . $index,
                                        'notification' => $notification,
                                        'stepName' => $step['code']
                                    ])
                                    @elseif ($index === 1)
                                    {{-- Modal Data Kontraktor --}}
                                    @include('components.steps.modal-data-kontraktor', [
                                        'label' => 'Input Data Kontraktor',
                                        'id' => 'modal-data-kontraktor',
                                        'notification' => $notification,
                                        'stepName' => 'data_kontraktor'
                                    ])
                                
                                @elseif ($index === 3)
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
    @elseif ($index === 4)
        @php
            $permitUmum = optional($notification)->id 
                ? \App\Models\UmumWorkPermit::where('notification_id', $notification->id)->first()
                : null;

            $permitGas = optional($notification)->id 
                ? \App\Models\WorkPermitGasPanas::where('notification_id', $notification->id)->first()
                : null;

            $permit = optional($notification)->id 
                ? \App\Models\WorkPermitAir::where('notification_id', $notification->id)->first()
                : null;
        @endphp

        {{-- Modal Permit Umum --}}
        @include('components.steps.modal-working-permit', [
            'id' => 'modal-' . $index,
            'notification' => $notification,
            'stepName' => $step['code'],
            'permitUmum' => $permitUmum ?? null
        ])

        {{-- Modal Permit Gas Panas --}}
        @include('components.steps.modal-working-permit-gaspanas', [
            'label' => 'Edit Gas Panas',
            'id' => 'modal-' . $index . '-gaspanas',
            'notification' => $notification,
            'stepName' => $step['code'],
            'permitGas' => $permitGas ?? null
        ])

        {{-- Modal Permit Air --}}
        @include('components.steps.modal-working-permit-air', [
            'label' => 'Edit Air',
            'id' => 'modal-' . $index . '-air',
            'notification' => $notification,
            'stepName' => $step['code'],
            'permitAir' => $permit ?? null
        ])

        {{-- Modal Permit Lainnya --}}
        @include('components.steps.modal-tambah-lainnya', [
            'label' => 'Tambah Permit Lain',
            'id' => 'modal-tambah-lainnya',
            'notification' => $notification,
            'stepName' => $step['code'],
            'permits' => $permits ?? []
        ])
                                @else
                                   @include('components.steps.modal-upload', [
                                        'id' => 'modal-' . $index,
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
