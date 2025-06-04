<x-admin-layout>
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: {!! json_encode(session('success')) !!},
                confirmButtonColor: '#3085d6'
            });
        </script>
    @endif

    @if ($showAlert)
        <script>
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian!',
                html: 'Permintaan ini sudah ditangani oleh <b>{{ $assignedAdmin }}</b>. Anda tidak dapat mengubah status.',
                confirmButtonColor: '#3085d6'
            });
        </script>
    @endif

    <div class="mb-6">
        <a href="{{ route('admin.permintaansik') }}"
           class="inline-flex items-center px-4 py-2 rounded bg-white border shadow-sm hover:bg-gray-50 transition text-sm font-medium text-gray-700">
            <i class="fas fa-arrow-left mr-2 text-blue-500"></i> Kembali ke Daftar Permintaan
        </a>
    </div>

    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <h2 class="text-xl font-bold text-red-600 mb-4">Detail Permintaan Izin Kerja</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
            <p><strong>Vendor/User:</strong> <span class="text-blue-700">{{ $data->user_name }}</span></p>
            <div><strong>Tanggal Pengajuan:</strong> {{ $data->tanggal }}</div>
            <div><strong>Ditangani Oleh:</strong> <span class="bg-gray-200 px-2 py-1 rounded text-xs">{{ $data->handled_by }}</span></div>
            <div>
                <strong>Status Umum:</strong>
                <span class="font-semibold {{ $data->status === 'revisi' ? 'text-yellow-600' : ($data->status === 'disetujui' ? 'text-green-600' : 'text-gray-600') }}">
                    {{ $data->status === 'revisi' ? 'Perlu Revisi' : ucfirst($data->status) }}
                </span>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow">
        <h3 class="text-lg font-semibold text-gray-800 mb-6">Progress Pengajuan Izin Kerja</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach ($data->step_data as $step)
                @php
                    $status = strtolower($step['status']);
                    $stepKey = $step['step'];
                    $badgeColor = match($status) {
                        'disetujui' => 'bg-green-500 text-white',
                        'revisi' => 'bg-yellow-500 text-white',
                        default => 'bg-gray-400 text-white',
                    };
                    $textColor = match($status) {
                        'disetujui' => 'text-green-600',
                        'revisi' => 'text-yellow-600',
                        default => 'text-gray-500',
                    };
                    $upload = $step['upload'] ?? null;
                @endphp

                <div class="border rounded-lg p-4 shadow-sm hover:shadow-md transition bg-gray-50 @if($stepKey === 'sik') min-h-[300px] @endif">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold {{ $badgeColor }}">{{ $loop->iteration }}</span>
                            <h4 class="text-sm font-semibold text-gray-700">{{ $step['title'] }}</h4>
                        </div>
                        <span class="text-xs font-medium {{ $textColor }}">{{ $status === 'revisi' ? 'Perlu Revisi' : ucfirst($status) }}</span>
                    </div>

                    <div class="flex flex-col gap-3 text-xs">
                        @if ($stepKey === 'op_spk' && $upload && $upload->number)
                            <div>
                                <strong>{{ strtoupper($upload->type ?? '-') }}:</strong> {{ $upload->number }}<br>
                                <span class="text-gray-500">Tanggal: {{ \Carbon\Carbon::parse($upload->created_at)->format('d-m-Y H:i') }}</span>
                            </div>
                            @if ($upload->file_path)
                                <a href="{{ asset('storage/' . $upload->file_path) }}" target="_blank"
                                   class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-full text-xs flex items-center gap-1">
                                    <i class="fas fa-file-alt"></i> Lihat File SPK/PO
                                </a>
                            @endif
                        @endif

                        {{-- SIK --}}
@if ($stepKey === 'sik')
    @if ($status == 'disetujui')
        <a href="{{ route('admin.permintaansik.viewSik', $data->id) }}" target="_blank"
           class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-full text-xs flex items-center gap-1">
            <i class="fas fa-file-pdf"></i> Lihat Surat Izin Kerja (PDF)
        </a>
                            @endif
                            <form method="POST" action="{{ route('admin.permintaansik.updateStatus', [$data->id, $stepKey]) }}" class="mt-3">
                                @csrf
                                <select name="status" onchange="toggleCatatan(this)" class="text-xs px-2 py-1 border rounded w-full mt-1 focus:outline-none @if($status == 'disetujui') bg-green-500 text-white @elseif($status == 'revisi') bg-yellow-500 text-white @else bg-gray-100 text-black @endif">
                                    <option value="menunggu" {{ $status == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                                    <option value="disetujui" {{ $status == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                                    <option value="revisi" {{ $status == 'revisi' ? 'selected' : '' }}>Perlu Revisi</option>
                                </select>
                                <textarea name="catatan" class="catatan-field mt-2 text-xs w-full border rounded px-2 py-1 text-gray-700" placeholder="Tulis alasan revisi..." style="display: {{ $status === 'revisi' ? 'block' : 'none' }};">{{ $step['catatan'] ?? '' }}</textarea>
                                <button type="submit" class="mt-2 bg-blue-500 hover:bg-blue-600 text-white text-xs px-3 py-1 rounded shadow">Simpan</button>
                            </form>
                        @else
                         @if (in_array($stepKey, ['bpjs', 'ktp']))
    @php
        $uploadFiles = \App\Models\Upload::where('notification_id', $data->notification_id)
            ->where('step', $stepKey)
            ->get();
    @endphp

    @foreach ($uploadFiles as $file)
        <div class="flex items-center gap-2">
            <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank"
               class="bg-green-500 hover:bg-green-600 text-white px-3 py-[3px] rounded-full text-xs flex items-center gap-1">
                <i class="fas fa-file-alt"></i> Lihat Dokumen
            </a>
            <form action="{{ route('admin.permintaansik.deleteFile', [$data->id, $stepKey]) }}" method="POST"
                  onsubmit="return confirm('Yakin ingin menghapus file ini?')">
                @csrf
                @method('DELETE')
                <input type="hidden" name="file_id" value="{{ $file->id }}">
                <button type="submit" class="text-red-500 text-xs px-2 py-[1px] border border-red-300 hover:bg-red-100 rounded-full">
                    Hapus
                </button>
            </form>
        </div>
    @endforeach
@elseif (!in_array($stepKey, ['op_spk', 'sik']) && $upload && $upload->file_path)
    <div class="flex items-center gap-2">
        <a href="{{ asset('storage/' . $upload->file_path) }}" target="_blank"
           class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-1 rounded-full text-xs flex items-center gap-1">
            <i class="fas fa-file-alt"></i> Lihat Dokumen
        </a>
        <form action="{{ route('admin.permintaansik.deleteFile', [$data->id, $stepKey]) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus file ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-red-500 text-xs underline">Hapus</button>
        </form>
    </div>
@endif


                            @if ($stepKey === 'data_kontraktor' && $data->data_kontraktor)
                                <a href="{{ route('izin-kerja.data-kontraktor.pdf', ['notification_id' => $data->data_kontraktor->notification_id]) }}" target="_blank"
                                   class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-full text-xs flex items-center gap-1">
                                    <i class="fas fa-file-pdf"></i> Lihat PDF Data Kontraktor
                                </a>
                            @endif

                            @if ($stepKey === 'jsa' && $data->jsa)
                                <a href="{{ route('jsa.pdf.view', ['notification_id' => $data->notification_id]) }}" target="_blank"
                                   class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-full text-xs flex items-center gap-1">
                                    <i class="fas fa-file-pdf"></i> Lihat PDF JSA
                                </a>
                            @endif

                            @if ($stepKey === 'working_permit' && $data->permits)
                                @foreach ($data->permits as $type => $permit)
                                    @if ($permit)
                                        <a href="{{ route('working-permit.' . $type . '.preview', ['id' => $permit->notification_id]) }}" target="_blank"
                                           class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-full text-xs flex items-center gap-1">
                                            <i class="fas fa-file-pdf"></i> Lihat PDF {{ ucfirst(str_replace('_', ' ', $type)) }}
                                        </a>
                                    @endif
                                @endforeach
                            @endif

                            <form method="POST" action="{{ route('admin.permintaansik.updateStatus', [$data->id, $stepKey]) }}" class="mt-3">
                                @csrf
                                <select name="status" onchange="toggleCatatan(this)" class="text-xs px-2 py-1 border rounded w-full mt-1 focus:outline-none @if($status == 'disetujui') bg-green-500 text-white @elseif($status == 'revisi') bg-yellow-500 text-white @else bg-gray-100 text-black @endif">
                                    <option value="menunggu" {{ $status == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                                    <option value="disetujui" {{ $status == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                                    <option value="revisi" {{ $status == 'revisi' ? 'selected' : '' }}>Perlu Revisi</option>
                                </select>
                                <textarea name="catatan" class="catatan-field mt-2 text-xs w-full border rounded px-2 py-1 text-gray-700" placeholder="Tulis alasan revisi..." style="display: {{ $status === 'revisi' ? 'block' : 'none' }};">{{ $step['catatan'] ?? '' }}</textarea>
                                <button type="submit" class="mt-2 bg-blue-500 hover:bg-blue-600 text-white text-xs px-3 py-1 rounded shadow">Simpan</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        function toggleCatatan(select) {
            const textarea = select.closest('form').querySelector('.catatan-field');
            textarea.style.display = select.value === 'revisi' ? 'block' : 'none';
            if (select.value === 'revisi') {
                textarea.setAttribute('required', true);
            } else {
                textarea.removeAttribute('required');
            }
        }
    </script>
</x-admin-layout>
