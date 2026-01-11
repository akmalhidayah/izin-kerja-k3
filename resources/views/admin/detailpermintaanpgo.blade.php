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

    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('admin.permintaansikpgo') }}"
           class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-xs font-semibold text-gray-700 shadow-sm hover:bg-gray-50 transition">
            <svg viewBox="0 0 24 24" class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="1.8">
                <path d="M15 18l-6-6 6-6" />
            </svg>
            Kembali
        </a>
        <div class="text-[11px] text-gray-500">
            ID: #{{ $data->id }}
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-gradient-to-r from-amber-50 via-white to-amber-50 p-6 shadow-sm mb-6">
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div class="flex items-center gap-3">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-amber-600 text-white shadow">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M4 5h16v5H4zM4 13h9v6H4zM15 13h5v6h-5z" />
                    </svg>
                </span>
                <div>
                    <h2 class="text-2xl font-extrabold text-amber-700 tracking-wide">Detail Permintaan Izin Kerja User</h2>
                </div>
            </div>
            <div class="text-[11px] text-gray-500">
                {{ $data->tanggal }}
            </div>
        </div>
    </div>

    @php
        $overallStatusLabel = match($data->status) {
            'revisi' => 'Perlu Revisi',
            'disetujui' => 'Disetujui',
            default => ucfirst($data->status ?? 'menunggu'),
        };
        $overallStatusClass = match($data->status) {
            'revisi' => 'bg-yellow-100 text-yellow-700',
            'disetujui' => 'bg-green-100 text-green-700',
            default => 'bg-gray-100 text-gray-700',
        };
        $totalSteps = count($data->step_data ?? []);
    @endphp

    <div class="bg-white rounded-2xl shadow p-6 mb-6 border border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 text-sm text-gray-700">
            <div class="flex items-center gap-3 rounded-xl border border-gray-200 bg-gray-50/60 px-3 py-2">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-blue-600 text-white">
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4z" />
                        <path d="M4 20a8 8 0 0 1 16 0" />
                    </svg>
                </span>
                  <div>
                      <div class="text-[11px] text-gray-500">Nama User</div>
                      <div class="text-sm font-semibold text-gray-900">{{ $data->user_name }}</div>
                      <div class="text-[11px] text-gray-500">Jabatan: {{ $data->user_jabatan ?? '-' }}</div>
                  </div>
            </div>
            <div class="flex items-center gap-3 rounded-xl border border-gray-200 bg-gray-50/60 px-3 py-2">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-slate-900 text-white">
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M4 7h16M7 3v3M17 3v3M5 11h14v8H5z" />
                    </svg>
                </span>
                <div>
                    <div class="text-[11px] text-gray-500">Tanggal Pengajuan</div>
                    <div class="text-sm font-semibold text-gray-900">{{ $data->tanggal }}</div>
                </div>
            </div>
            <div class="flex items-center gap-3 rounded-xl border border-gray-200 bg-gray-50/60 px-3 py-2">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-amber-600 text-white">
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M12 3l8 4.5v9L12 21l-8-4.5v-9L12 3z" />
                        <path d="M12 12l8-4.5" />
                        <path d="M12 12v9" />
                        <path d="M12 12L4 7.5" />
                    </svg>
                </span>
                <div>
                    <div class="text-[11px] text-gray-500">Ditangani Oleh</div>
                    <div class="text-sm font-semibold text-gray-900">{{ $data->handled_by }}</div>
                </div>
            </div>
            <div class="flex items-center gap-3 rounded-xl border border-gray-200 bg-gray-50/60 px-3 py-2">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-600 text-white">
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M9 12l2 2 4-4" />
                        <path d="M12 3a9 9 0 1 0 9 9" />
                    </svg>
                </span>
                <div>
                    <div class="text-[11px] text-gray-500">Status Umum</div>
                    <span class="inline-flex items-center gap-1.5 rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $overallStatusClass }}">
                        <span class="h-1.5 w-1.5 rounded-full {{ $data->status === 'disetujui' ? 'bg-green-500' : ($data->status === 'revisi' ? 'bg-yellow-500' : 'bg-gray-400') }}"></span>
                        {{ $overallStatusLabel }}
                    </span>
                </div>
            </div>
        </div>
        <div class="mt-4 flex flex-wrap items-center gap-2 text-[11px] text-gray-600">
            <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2 py-0.5">
                <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M4 6h16M7 12h10M10 18h4" />
                </svg>
                {{ $data->step_summary }}
            </span>
            <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2 py-0.5">
                <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M12 6v6l4 2" />
                    <path d="M12 3a9 9 0 1 0 9 9" />
                </svg>
                Total Step: {{ $totalSteps }}
            </span>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow border border-gray-200">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-base font-semibold text-gray-800">Progress Pengajuan Izin Kerja</h3>
            <div class="text-[11px] text-gray-500">Update status tiap step di bawah.</div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach ($data->step_data as $step)
                @php
                    $status = strtolower($step['status']);
                    $stepKey = $step['step'];
                    $statusLabel = $status === 'revisi' ? 'Perlu Revisi' : ucfirst($status);
                    $badgeColor = match($status) {
                        'disetujui' => 'bg-green-100 text-green-700',
                        'revisi' => 'bg-yellow-100 text-yellow-700',
                        default => 'bg-gray-100 text-gray-700',
                    };
                    $cardStyle = match($status) {
                        'disetujui' => 'border-green-200 bg-green-50/30',
                        'revisi' => 'border-yellow-200 bg-yellow-50/30',
                        default => 'border-gray-200 bg-white',
                    };
                    $upload = $step['upload'] ?? null;
                    $permits = $data->permits ?? [];
                    $hasPermit = collect($permits)->filter()->isNotEmpty();
                @endphp

                <div class="rounded-xl border {{ $cardStyle }} p-4 shadow-sm hover:shadow-md transition">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <span class="h-8 w-8 rounded-lg bg-red-50 text-red-700 text-[11px] font-bold flex items-center justify-center border border-red-200">
                                {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                            </span>
                            <div>
                                <div class="text-sm font-semibold text-gray-800">{{ $step['title'] }}</div>
                                <div class="text-[11px] text-gray-500">Step {{ $loop->iteration }}</div>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1.5 rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $badgeColor }}">
                            <span class="h-1.5 w-1.5 rounded-full {{ $status === 'disetujui' ? 'bg-green-500' : ($status === 'revisi' ? 'bg-yellow-500' : 'bg-gray-400') }}"></span>
                            {{ $statusLabel }}
                        </span>
                    </div>

                    <div class="mt-3 flex flex-col gap-3 text-[11px] text-gray-700">
                        @if ($stepKey === 'op_spk' && $upload && $upload->number)
                            <div class="rounded-lg border border-gray-200 bg-white px-3 py-2">
                                <div class="text-[11px] text-gray-500">Dokumen</div>
                                <div class="text-sm font-semibold text-gray-900 mt-0.5">
                                    {{ strtoupper($upload->type ?? '-') }}: {{ $upload->number }}
                                </div>
                                <div class="text-[11px] text-gray-500 mt-1">
                                    Tanggal: {{ \Carbon\Carbon::parse($upload->created_at)->format('d-m-Y H:i') }}
                                </div>
                                <div class="text-[11px] text-gray-500 mt-1">
                                    Deskripsi: {{ $data->description ?? '-' }}
                                </div>
                            </div>
                            @if ($upload->file_path)
                                <a href="{{ asset('storage/' . $upload->file_path) }}" target="_blank"
                                   class="inline-flex items-center gap-2 rounded-full bg-green-500 hover:bg-green-600 text-white px-3 py-1 text-[11px]">
                                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M7 3h7l5 5v12a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z" />
                                        <path d="M14 3v5h5" />
                                    </svg>
                                    Lihat File SPK/PO
                                </a>
                            @endif
                        @endif

                        @if ($stepKey === 'jsa')
                            @if ($data->jsa)
                                <a href="{{ route('jsa.pdf.view', ['notification_id' => $data->notification_id]) }}" target="_blank"
                                   class="inline-flex items-center gap-2 rounded-full bg-green-500 hover:bg-green-600 text-white px-3 py-1 text-[11px]">
                                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M7 3h7l5 5v12a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z" />
                                        <path d="M14 3v5h5" />
                                    </svg>
                                    Lihat PDF JSA
                                </a>
                                <form action="{{ route('admin.permintaansik.deleteJsa', $data->id) }}" method="POST"
                                      onsubmit="return confirm('Yakin ingin menghapus data JSA ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-2 rounded-full bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1 text-[11px]">
                                        <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="M6 7h12" />
                                            <path d="M9 7V5h6v2" />
                                            <path d="M8 7l1 12h6l1-12" />
                                        </svg>
                                        Hapus Data
                                    </button>
                                </form>
                            @else
                                <div class="text-[11px] text-gray-500">Belum ada data JSA.</div>
                            @endif
                        @endif

                        @if ($stepKey === 'working_permit')
                            @if ($hasPermit)
                                @foreach ($permits as $type => $permit)
                                    @if ($permit)
                                        <a href="{{ route('working-permit.' . $type . '.preview', ['id' => $permit->notification_id]) }}" target="_blank"
                                           class="inline-flex items-center gap-2 rounded-full bg-green-500 hover:bg-green-600 text-white px-3 py-1 text-[11px]">
                                            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8">
                                                <path d="M7 3h7l5 5v12a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z" />
                                                <path d="M14 3v5h5" />
                                            </svg>
                                            Lihat PDF {{ ucfirst(str_replace('_', ' ', $type)) }}
                                        </a>
                                        <form action="{{ route('admin.permintaansik.deleteWorkingPermit', ['id' => $data->id, 'type' => $type]) }}" method="POST"
                                              onsubmit="return confirm('Yakin ingin menghapus data permit {{ ucfirst(str_replace('_', ' ', $type)) }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-2 rounded-full bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1 text-[11px]">
                                                <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8">
                                                    <path d="M6 7h12" />
                                                    <path d="M9 7V5h6v2" />
                                                    <path d="M8 7l1 12h6l1-12" />
                                                </svg>
                                                Hapus Data
                                            </button>
                                        </form>
                                    @endif
                                @endforeach
                            @else
                                <div class="text-[11px] text-gray-500">Belum ada data working permit.</div>
                            @endif
                        @endif

                        <form method="POST" action="{{ route('admin.permintaansik.updateStatus', [$data->id, $stepKey]) }}" class="mt-3">
                            @csrf
                            <select name="status" onchange="toggleCatatan(this)" class="text-[11px] px-2 py-1 border rounded-lg w-full mt-1 focus:outline-none @if($status == 'disetujui') bg-green-500 text-white @elseif($status == 'revisi') bg-yellow-500 text-white @else bg-gray-100 text-black @endif">
                                <option value="menunggu" {{ $status == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                                <option value="disetujui" {{ $status == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                                <option value="revisi" {{ $status == 'revisi' ? 'selected' : '' }}>Perlu Revisi</option>
                            </select>
                            <textarea name="catatan" class="catatan-field mt-2 text-[11px] w-full border rounded-lg px-2 py-1 text-gray-700" placeholder="Tulis alasan revisi..." style="display: {{ $status === 'revisi' ? 'block' : 'none' }};">{{ $step['catatan'] ?? '' }}</textarea>
                            <button type="submit" class="mt-2 inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-[11px] px-3 py-1 rounded-lg shadow">
                                <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path d="M5 12l4 4L19 6" />
                                </svg>
                                Simpan
                            </button>
                        </form>
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
