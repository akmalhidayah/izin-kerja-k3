<x-admin-layout>
    <div class="rounded-2xl border border-gray-200 bg-gradient-to-r from-red-50 via-white to-amber-50 p-6 shadow-sm mb-6">
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div class="flex items-center gap-3">
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-red-600 text-white shadow">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M4 5h16v5H4zM4 13h9v6H4zM15 13h5v6h-5z" />
                    </svg>
                </span>
                <div>
                    <h1 class="text-3xl font-extrabold text-red-700 tracking-wide">Approve Surat Izin Kerja (SIK)</h1>
                    <p class="text-xs text-gray-600 mt-1">Khusus Senior Manager untuk verifikasi dan tanda tangan SIK.</p>
                </div>
            </div>
            <div class="text-[11px] text-gray-500">
                {{ now()->format('d M Y, H:i') }}
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-200">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between mb-4">
            <form method="GET" class="flex flex-wrap gap-2 items-center">
                <div class="relative">
                    <span class="pointer-events-none absolute left-3 top-2.5 text-gray-400">
                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M7 3v3M17 3v3M3 9h18M5 7h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2z" />
                        </svg>
                    </span>
                    <select name="bulan" class="pl-9 pr-3 py-2 border rounded-lg text-xs">
                        <option value="">Bulan</option>
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ request('bulan', now()->month) == $i ? 'selected' : '' }}>
                                {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="relative">
                    <span class="pointer-events-none absolute left-3 top-2.5 text-gray-400">
                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M4 7h16M7 3v3M17 3v3M5 11h14v8H5z" />
                        </svg>
                    </span>
                    <select name="tahun" class="pl-9 pr-3 py-2 border rounded-lg text-xs">
                        @for ($y = now()->year; $y >= 2023; $y--)
                            <option value="{{ $y }}" {{ request('tahun', now()->year) == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>
                <button type="submit" class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 text-xs rounded-lg">
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path d="M4 6h16M7 12h10M10 18h4" />
                    </svg>
                    Filter
                </button>
            </form>
            <div class="text-[11px] text-gray-500">
                Total: {{ $permintaansik->count() }}
            </div>
        </div>

        @if(session('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: {!! json_encode(session('success')) !!},
                    confirmButtonColor: '#3085d6'
                });
            </script>
        @endif

        <div class="overflow-x-auto rounded-xl border border-gray-200 bg-gradient-to-b from-white to-gray-50/60">
            <table class="min-w-full text-xs table-auto">
                <thead class="bg-gray-50/90 text-gray-700 text-[11px] uppercase tracking-wider sticky top-0 backdrop-blur">
                    <tr>
                        <th class="px-4 py-3 text-left">Nama Vendor/User</th>
                        <th class="px-4 py-3 text-left">No. PO / Notif / SPK</th>
                        <th class="px-4 py-3 text-left">Ditangani Oleh</th>
                        <th class="px-4 py-3 text-left">Dokumen Vendor</th>
                        <th class="px-4 py-3 text-left">Status SIK</th>
                        <th class="px-4 py-3 text-left">Lihat SIK</th>
                        <th class="px-4 py-3 text-left">Tanda Tangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($permintaansik as $row)
                        @php
                            $vendorName = $row->user?->name ?? '-';
                            $adminName = $row->assignedAdmin?->name ?? 'Belum Ditugaskan';
                            $isSikApproved = (bool) $row->sikStep;
                            $statusLabel = $isSikApproved ? 'Sudah Terbit' : 'Belum';
                            $statusClass = $isSikApproved ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600';
                        @endphp
                        <tr class="hover:bg-amber-50/40 transition">
                            <td class="px-4 py-3 font-semibold text-gray-900">{{ $vendorName }}</td>
                            <td class="px-4 py-3 text-gray-800">
                                <div class="font-medium text-gray-900">{{ $row->number }}</div>
                                @if ($row->file)
                                    <a href="{{ asset('storage/' . $row->file) }}" target="_blank"
                                       class="mt-1 inline-flex items-center gap-1 text-blue-600 hover:underline text-[11px]">
                                        <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="M7 3h7l5 5v12a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z" />
                                            <path d="M14 3v5h5" />
                                        </svg>
                                        File
                                    </a>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-800">
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-[11px] text-gray-700">
                                    {{ $adminName }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.permintaansik.show', $row->id) }}"
                                   class="inline-flex items-center gap-2 rounded-lg border border-blue-200 bg-blue-50 px-3 py-1 text-blue-700 text-[11px] font-semibold hover:bg-blue-100">
                                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M2 12s4-6 10-6 10 6 10 6-4 6-10 6-10-6-10-6z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                    Lihat Detail
                                </a>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $statusClass }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $isSikApproved ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if ($row->sikStep)
                                    <a href="{{ route('admin.permintaansik.viewSik', $row->id) }}" target="_blank"
                                       class="inline-flex items-center gap-2 rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-1 text-indigo-700 text-[11px] font-semibold hover:bg-indigo-100">
                                        <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path d="M2 12s4-6 10-6 10 6 10 6-4 6-10 6-10-6-10-6z" />
                                            <circle cx="12" cy="12" r="3" />
                                        </svg>
                                        Lihat SIK
                                    </a>
                                @else
                                    <span class="text-[11px] italic text-gray-400">Belum tersedia</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if (!$row->sikStep)
                                    <span class="text-[11px] italic text-gray-400">Belum tersedia</span>
                                @else
                                    <div class="flex flex-col gap-2">
                                        <div class="flex items-center gap-2">
                                            <span class="text-[11px] text-gray-500">Manager</span>
                                            @if ($row->sikStep->signature_manager)
                                                <span class="inline-flex items-center gap-2 text-green-700 text-[11px] font-semibold">
                                                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8">
                                                        <path d="M5 12l4 4L19 6" />
                                                    </svg>
                                                    Sudah
                                                </span>
                                            @else
                                                <form id="form_signature_manager_{{ $row->id }}" method="POST" action="{{ route('admin.approvesik.signature_manager', $row->id) }}">
                                                    @csrf
                                                    <input type="hidden" name="signature" id="signature_manager_{{ $row->id }}">
                                                    <button type="button" onclick="openSignPad('signature_manager_{{ $row->id }}')"
                                                        class="inline-flex items-center gap-2 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-1 text-emerald-700 text-[11px] font-semibold hover:bg-emerald-100">
                                                        <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8">
                                                            <path d="M12 19l7-7-3-3-7 7-2 4z" />
                                                            <path d="M15 9l3 3" />
                                                        </svg>
                                                        TTD
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-[11px] text-gray-500">Senior Manager</span>
                                            @if ($row->sikStep->signature_senior_manager)
                                                <span class="inline-flex items-center gap-2 text-green-700 text-[11px] font-semibold">
                                                    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8">
                                                        <path d="M5 12l4 4L19 6" />
                                                    </svg>
                                                    Sudah
                                                </span>
                                            @else
                                                <form id="form_signature_senior_{{ $row->id }}" method="POST" action="{{ route('admin.approvesik.signature', $row->id) }}">
                                                    @csrf
                                                    <input type="hidden" name="signature" id="signature_senior_{{ $row->id }}">
                                                    <button type="button" onclick="openSignPad('signature_senior_{{ $row->id }}')"
                                                        class="inline-flex items-center gap-2 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-1 text-emerald-700 text-[11px] font-semibold hover:bg-emerald-100">
                                                        <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8">
                                                            <path d="M12 19l7-7-3-3-7 7-2 4z" />
                                                            <path d="M15 9l3 3" />
                                                        </svg>
                                                        TTD
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500">
                                Belum ada data untuk periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Signature --}}
    <div id="signPadModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-2xl shadow-lg p-6 w-full max-w-lg" onclick="event.stopPropagation()">
            <h2 class="text-base font-semibold mb-4">Tanda Tangan</h2>
            <canvas id="signaturePad" class="w-full border rounded-lg h-48"></canvas>
            <input type="hidden" id="currentSignatureField">
            <div class="mt-4 flex justify-between items-center">
                <button type="button" onclick="clearSignature()" class="text-xs text-red-600 hover:underline">Clear</button>
                <div class="space-x-2">
                    <button type="button" onclick="closeSignPad()" class="px-3 py-1 bg-gray-200 text-gray-800 text-xs rounded-lg">Batal</button>
                    <button type="button" onclick="saveSignature()" class="px-4 py-1 bg-blue-600 text-white text-xs rounded-lg">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Script Signature --}}
    <script>
        let signaturePadInstance;

        function openSignPad(targetId) {
            document.getElementById('signPadModal').classList.remove('hidden');
            document.getElementById('currentSignatureField').value = targetId;

            const canvas = document.getElementById('signaturePad');
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            const ctx = canvas.getContext('2d');
            ctx.scale(ratio, ratio);

            signaturePadInstance = new SignaturePad(canvas);

            const existingSignature = document.getElementById(targetId).value;
            if (existingSignature) {
                const img = new Image();
                img.src = existingSignature;
                img.onload = function () {
                    ctx.drawImage(img, 0, 0, canvas.width / ratio, canvas.height / ratio);
                };
            }
        }

        function closeSignPad() {
            document.getElementById('signPadModal').classList.add('hidden');
            if (signaturePadInstance) signaturePadInstance.clear();
        }

        function clearSignature() {
            if (signaturePadInstance) signaturePadInstance.clear();
        }

        function saveSignature() {
            if (!signaturePadInstance || signaturePadInstance.isEmpty()) {
                alert('Tanda tangan belum diisi!');
                return;
            }
            const dataURL = signaturePadInstance.toDataURL();
            const inputId = document.getElementById('currentSignatureField').value;
            const inputEl = document.getElementById(inputId);

            if (inputEl) {
                inputEl.value = dataURL;
                const formId = 'form_signature_' + inputId.split('signature_')[1];
                document.getElementById(formId).submit();
            } else {
                alert('Input target tidak ditemukan!');
            }

            closeSignPad();
        }
    </script>
</x-admin-layout>
