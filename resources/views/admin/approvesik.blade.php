<x-admin-layout>
    <div class="mb-6">
        <h1 class="text-3xl font-extrabold text-red-700 tracking-wide">Daftar Approve Surat Izin Kerja (SIK)</h1>
        <p class="text-sm text-gray-500 mt-1">Halaman ini khusus Super Admin untuk mengecek dan menyetujui pengajuan SIK.</p>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-md">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm table-auto border border-gray-200 rounded">
                <thead class="bg-gray-50 text-gray-700 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-left">Nama Vendor/User</th>
                        <th class="px-4 py-3 text-left">No. PO / Notif / SPK</th>
                        <th class="px-4 py-3 text-left">Ditangani Oleh</th>
                        <th class="px-4 py-3 text-left">Status SIK</th>
                        <th class="px-4 py-3 text-left">Lihat SIK</th>
                        <th class="px-4 py-3 text-left">Tanda Tangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($permintaansik as $row)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3 font-semibold text-gray-900">{{ $row->user->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-800">
                                {{ $row->number }}
                                @if ($row->file)
                                    <a href="{{ asset('storage/' . $row->file) }}" target="_blank" class="text-blue-600 hover:underline text-xs ml-1">📄 File</a>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-800">
                                {{ $row->assignedAdmin->name ?? 'Belum Ditugaskan' }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="{{ $row->sikStep ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold' }}">
                                    {{ $row->sikStep ? 'Disetujui' : 'Belum' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if ($row->sikStep)
                                    <a href="{{ route('admin.permintaansik.viewSik', $row->id) }}"
                                       target="_blank"
                                       class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded shadow">
                                        <i class="fas fa-eye mr-1"></i> Lihat SIK
                                    </a>
                                @else
                                    <span class="text-xs italic text-gray-400">Belum tersedia</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <button type="button"
                                        onclick="openSignPad('signature_{{ $row->id }}')"
                                        class="inline-flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs rounded shadow">
                                    <i class="fas fa-pen mr-1"></i> Tanda Tangani Dokumen
                                </button>
                                <input type="hidden" name="signature_{{ $row->id }}" id="signature_{{ $row->id }}" value="{{ $row->signature_super_admin ?? '' }}">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Signature --}}
    <div id="signPadModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-lg" onclick="event.stopPropagation()">
            <h2 class="text-lg font-bold mb-4">Tanda Tangan</h2>
            <canvas id="signaturePad" class="w-full border rounded h-48"></canvas>
            <input type="hidden" id="currentSignatureField">
            <div class="mt-4 flex justify-between">
                <button type="button" onclick="clearSignature()" class="text-sm text-red-600 hover:underline">Clear</button>
                <div class="space-x-2">
                    <button type="button" onclick="closeSignPad()" class="px-3 py-1 bg-gray-300 text-gray-800 rounded">Batal</button>
                    <button type="button" onclick="saveSignature()" class="px-4 py-1 bg-blue-600 text-white rounded">Simpan</button>
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
            signaturePadInstance.clear();
        }

        function clearSignature() {
            signaturePadInstance.clear();
        }

        function saveSignature() {
            if (signaturePadInstance.isEmpty()) {
                alert('Tanda tangan belum diisi!');
                return;
            }
            const dataURL = signaturePadInstance.toDataURL();
            const targetInput = document.getElementById(document.getElementById('currentSignatureField').value);
            if (targetInput) {
                targetInput.value = dataURL;
                alert('✅ Tanda tangan berhasil tersimpan!');
            } else {
                alert('❌ Input target tidak ditemukan!');
            }
            closeSignPad();
        }
    </script>
</x-admin-layout>
