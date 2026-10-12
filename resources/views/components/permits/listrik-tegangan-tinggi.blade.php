@php
    $formAction = $formAction ?? route('working-permit.listrik-tegangan-tinggi.store');
    $documentationItems = [
        'method_statement' => 'Metode kerja (JPT, method statement, dll.) telah didiskusikan dan disetujui oleh Supervisor Kontraktor/Pekerja dan Permit Issuer.',
        'high_hazard_checklist' => 'High Hazard Checklist telah diperiksa untuk memastikan seluruh bahaya sudah diperiksa.',
        'supporting_documents' => 'Dokumen pendukung telah dilengkapi untuk diberikan kepada Supervisor Kontraktor/Pekerja (gambar rangkaian listrik, dll.).',
        'hazards_documented_in_lst' => 'Pekerjaan telah diperiksa ulang dan seluruh bahaya signifikan telah didokumentasikan di LST.',
    ];
    $safetyItems = [
        'electrical_worker_certificate' => 'Semua Petugas Listrik memiliki sertifikat keahlian yang masih berlaku dan sesuai.',
        'measurement_calibration_certificate' => 'Semua peralatan pengukuran memiliki sertifikat kalibrasi yang masih berlaku dan sesuai.',
        'fire_detection_protection' => 'Sistem perlindungan dan deteksi kebakaran tersedia dalam mode otomatis.',
        'portable_fire_extinguisher' => 'Alat pemadam kebakaran ringan tersedia, dalam kondisi baik, dan dapat digunakan.',
        'insulated_tools' => 'Peralatan dan perkakas kerja dilindungi bahan anti-statis atau bahan tidak menghantarkan listrik pada pegangannya.',
        'non_conductive_ppe' => 'APD dibuat dari bahan anti-statis atau bahan yang tidak menghantarkan listrik.',
        'non_conductive_ladder' => 'Tangga terbuat dari bahan bukan logam atau bahan yang tidak menghantarkan listrik.',
        'warning_signs' => 'Rambu bahaya yang sesuai dipasang pada tempat yang mudah terlihat.',
        'electrical_energy_isolation' => 'Kegiatan isolasi energi listrik perlu dilakukan dan Sertifikat Kerja Isolasi Energi dilampirkan.',
        'gfci_elcb' => 'GFCI atau ELCB tersedia dan dalam kondisi bekerja (bukan MCB).',
        'nearby_workers_notified' => 'Pekerja lain di dekat lokasi telah diberi tahu dan memahami bahaya pekerjaan.',
    ];
    $issuerStatements = [
        'area_and_equipment_checked' => 'Saya telah memeriksa area kerja dan seluruh peralatan yang akan digunakan untuk bekerja di tegangan tinggi.',
        'hazard_control_checklist_reviewed' => 'Saya telah memeriksa ulang checklist bahaya dan kontrol untuk bekerja di tegangan tinggi.',
        'hazards_discussed_with_supervisor' => 'Saya telah berdiskusi dengan Supervisor mengenai seluruh bahaya dan kontrol yang tertulis pada izin kerja ini.',
        'high_risk_hazards_emphasized' => 'Saya telah menekankan bahaya berisiko tinggi kepada Supervisor Kontraktor/Pekerja untuk dicatat dalam LST.',
        'work_declared_safe' => 'Pekerjaan dinyatakan aman dengan pengendalian pada bagian 4 telah dilakukan dan dijaga.',
    ];

    $documentationValues = old('documentation_checks', $permit?->documentation_checks ?? []);
    $safetyValues = old('safety_requirements', $permit?->safety_requirements ?? []);
    $issuerValues = old('issuer_confirmations', $permit?->issuer_confirmations ?? []);
    $hazardRows = old('additional_hazards', $permit?->additional_hazards ?? []);
    $hazardRows = is_array($hazardRows) ? array_values($hazardRows) : [];
    while (count($hazardRows) < 5) {
        $hazardRows[] = ['hazard' => '', 'control' => '', 'pic' => ''];
    }

    $signaturePrefix = $signaturePrefix ?? 'ltt-'.($permit?->id ?? 'new');
    $issuerSignatureId = $signaturePrefix.'-issuer-signature';
    $receiverSignatureId = $signaturePrefix.'-receiver-signature';
    $signatureUrl = fn ($path) => $path
        ? (str_starts_with($path, 'data:image') ? $path : asset($path))
        : '';
@endphp

<form method="POST" action="{{ $formAction }}" enctype="multipart/form-data" class="space-y-5">
    @csrf
    <input type="hidden" name="notification_id" value="{{ $notification->id ?? $permit?->notification_id }}">
    <input type="hidden" name="clear_all_signatures" value="0">

    <div class="border border-gray-800 rounded-md bg-white p-4 text-sm shadow">
        <div class="flex items-start justify-between gap-4 border-b pb-3">
            <div>
                <h2 class="text-xl font-bold uppercase">Izin Bekerja pada Listrik Tegangan Tinggi</h2>
                <p class="mt-1 text-xs text-gray-600">Permit Issuer harus melengkapi seluruh bagian pada form ini.</p>
            </div>
            <div class="w-48">
                <label class="block text-xs font-semibold">Nomor Permit</label>
                <input type="text" name="permit_number" value="{{ old('permit_number', $permit?->permit_number) }}" class="mt-1 w-full rounded border-gray-300 text-sm">
            </div>
        </div>

        <h3 class="mt-4 bg-black px-3 py-2 font-bold text-white">1. Detail Pekerjaan</h3>
        <div class="mt-3 grid grid-cols-1 gap-3 md:grid-cols-2">
            <div>
                <label class="font-semibold">Lokasi Pekerjaan</label>
                <input type="text" name="work_location" value="{{ old('work_location', $permit?->work_location) }}" class="mt-1 w-full rounded border-gray-300">
            </div>
            <div>
                <label class="font-semibold">Tanggal</label>
                <input type="date" name="work_date" value="{{ old('work_date', $permit?->work_date?->format('Y-m-d')) }}" class="mt-1 w-full rounded border-gray-300">
            </div>
            <div class="md:col-span-2">
                <label class="font-semibold">Peralatan/Mesin yang Akan Dikerjakan</label>
                <textarea name="equipment_machine" rows="2" class="mt-1 w-full rounded border-gray-300">{{ old('equipment_machine', $permit?->equipment_machine) }}</textarea>
            </div>
            <div>
                <label class="font-semibold">Nama Permit Issuer Bekerja di Tegangan Tinggi</label>
                <input type="text" name="permit_issuer_name" value="{{ old('permit_issuer_name', $permit?->permit_issuer_name) }}" class="mt-1 w-full rounded border-gray-300">
            </div>
            <div>
                <label class="font-semibold">Nomor HP</label>
                <input type="text" name="permit_issuer_phone" value="{{ old('permit_issuer_phone', $permit?->permit_issuer_phone) }}" class="mt-1 w-full rounded border-gray-300">
            </div>
            <div class="md:col-span-2">
                <label class="font-semibold">Uraian Pekerjaan dan Ruang Lingkup</label>
                <textarea name="job_scope" rows="3" class="mt-1 w-full rounded border-gray-300">{{ old('job_scope', $permit?->job_scope) }}</textarea>
            </div>
            <div>
                <label class="font-semibold">Perkiraan Jumlah Pekerja</label>
                <input type="number" min="0" name="worker_count" value="{{ old('worker_count', $permit?->worker_count) }}" class="mt-1 w-full rounded border-gray-300">
            </div>
            <div>
                <label class="font-semibold">Daftar Peralatan yang Digunakan</label>
                <textarea name="tools_equipment" rows="3" class="mt-1 w-full rounded border-gray-300">{{ old('tools_equipment', $permit?->tools_equipment) }}</textarea>
            </div>
        </div>

        <h3 class="mt-5 bg-black px-3 py-2 font-bold text-white">2. Dokumentasi</h3>
        <div class="mt-3 overflow-x-auto">
            <table class="w-full border-collapse text-sm">
                <tbody>
                    <tr>
                        <td class="border p-2">
                            <div class="font-semibold">Apakah izin kerja lain diperlukan?</div>
                            <textarea name="other_permit_details" rows="2" placeholder="Jika Ya, tulis jenis dan nomor perizinan terkait" class="mt-2 w-full rounded border-gray-300">{{ old('other_permit_details', $permit?->other_permit_details) }}</textarea>
                        </td>
                        <td class="w-40 border p-2 text-center">
                            @foreach (['ya' => 'Ya', 'na' => 'N/A'] as $value => $label)
                                <label class="mr-2 inline-flex items-center gap-1">
                                    <input type="radio" name="other_permit_status" value="{{ $value }}" @checked(old('other_permit_status', $permit?->other_permit_status) === $value)>
                                    {{ $label }}
                                </label>
                            @endforeach
                        </td>
                    </tr>
                    @foreach ($documentationItems as $key => $label)
                        <tr>
                            <td class="border p-2">{{ $label }}</td>
                            <td class="border p-2 text-center">
                                @foreach (['ya' => 'Ya', 'na' => 'N/A'] as $value => $optionLabel)
                                    <label class="mr-2 inline-flex items-center gap-1">
                                        <input type="radio" name="documentation_checks[{{ $key }}]" value="{{ $value }}" @checked(($documentationValues[$key] ?? null) === $value)>
                                        {{ $optionLabel }}
                                    </label>
                                @endforeach
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            <label class="font-semibold">Dokumen Pendukung (PDF/JPG/PNG, maksimal 5 MB)</label>
            @if ($permit?->supporting_document)
                <a href="{{ asset($permit->supporting_document) }}" target="_blank" class="ml-2 text-xs text-blue-600 underline">Lihat dokumen saat ini</a>
            @endif
            <input type="file" name="supporting_document" accept=".pdf,.jpg,.jpeg,.png" class="mt-1 block w-full text-sm">
        </div>

        <h3 class="mt-5 bg-black px-3 py-2 font-bold text-white">3. Persyaratan Kesehatan dan Keselamatan Kerja Listrik</h3>
        <div class="mt-3 overflow-x-auto">
            <table class="w-full border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border p-2 text-left">Pemeriksaan Kondisi Umum Pekerjaan</th>
                        <th class="w-32 border p-2">Butuh?</th>
                        <th class="w-32 border p-2">Dipenuhi?</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($safetyItems as $key => $label)
                        <tr>
                            <td class="border p-2">{{ $label }}</td>
                            @foreach (['needed', 'fulfilled'] as $column)
                                <td class="border p-2">
                                    <select name="safety_requirements[{{ $key }}][{{ $column }}]" class="w-full rounded border-gray-300 text-xs">
                                        <option value="">-</option>
                                        <option value="ya" @selected(($safetyValues[$key][$column] ?? null) === 'ya')>Ya</option>
                                        <option value="tidak" @selected(($safetyValues[$key][$column] ?? null) === 'tidak')>Tidak</option>
                                    </select>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            <label class="font-semibold">Sertifikat Kerja Isolasi Energi (PDF/JPG/PNG, maksimal 5 MB)</label>
            @if ($permit?->energy_isolation_certificate)
                <a href="{{ asset($permit->energy_isolation_certificate) }}" target="_blank" class="ml-2 text-xs text-blue-600 underline">Lihat sertifikat saat ini</a>
            @endif
            <input type="file" name="energy_isolation_certificate" accept=".pdf,.jpg,.jpeg,.png" class="mt-1 block w-full text-sm">
        </div>

        <h3 class="mt-5 bg-black px-3 py-2 font-bold text-white">4. Bahaya dan Kontrol Tambahan yang Teridentifikasi</h3>
        <div class="mt-3 overflow-x-auto">
            <table class="w-full border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border p-2">Bahaya</th>
                        <th class="border p-2">Kontrol</th>
                        <th class="w-40 border p-2">PIC</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($hazardRows as $index => $row)
                        <tr>
                            <td class="border p-1"><textarea name="additional_hazards[{{ $index }}][hazard]" rows="2" class="w-full border-0 text-sm">{{ $row['hazard'] ?? '' }}</textarea></td>
                            <td class="border p-1"><textarea name="additional_hazards[{{ $index }}][control]" rows="2" class="w-full border-0 text-sm">{{ $row['control'] ?? '' }}</textarea></td>
                            <td class="border p-1"><input type="text" name="additional_hazards[{{ $index }}][pic]" value="{{ $row['pic'] ?? '' }}" class="w-full border-0 text-sm"></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <h3 class="mt-5 bg-black px-3 py-2 font-bold text-white">5. Handover Perizinan</h3>
        <div class="mt-3 border p-3">
            <h4 class="font-bold">Permit Issuer</h4>
            @foreach ($issuerStatements as $key => $statement)
                <label class="mt-2 flex items-start gap-2">
                    <input type="checkbox" name="issuer_confirmations[{{ $key }}]" value="ya" class="mt-1" @checked(($issuerValues[$key] ?? null) === 'ya')>
                    <span>{{ $statement }}</span>
                </label>
            @endforeach
            <div class="mt-3 grid grid-cols-1 gap-3 md:grid-cols-4">
                <div>
                    <label class="font-semibold">Nama</label>
                    <input type="text" name="handover_issuer_name" value="{{ old('handover_issuer_name', $permit?->handover_issuer_name ?? $permit?->permit_issuer_name) }}" class="mt-1 w-full rounded border-gray-300">
                </div>
                <div>
                    <label class="font-semibold">Tanda Tangan</label>
                    @if ($permit?->handover_issuer_signature)
                        <img src="{{ $signatureUrl($permit->handover_issuer_signature) }}" alt="Tanda tangan Permit Issuer" class="mt-1 h-12 max-w-full border">
                    @endif
                    <input type="hidden" id="{{ $issuerSignatureId }}" name="handover_issuer_signature" value="{{ old('handover_issuer_signature', $permit?->handover_issuer_signature) }}" data-signature-url="{{ $signatureUrl($permit?->handover_issuer_signature) }}">
                    <button type="button" onclick="openSignPad('{{ $issuerSignatureId }}')" class="mt-1 rounded bg-blue-600 px-3 py-1 text-xs font-semibold text-white">Isi Tanda Tangan</button>
                </div>
                <div>
                    <label class="font-semibold">Tanggal</label>
                    <input type="date" name="handover_issuer_date" value="{{ old('handover_issuer_date', $permit?->handover_issuer_date?->format('Y-m-d')) }}" class="mt-1 w-full rounded border-gray-300">
                </div>
                <div>
                    <label class="font-semibold">Jam</label>
                    <input type="time" name="handover_issuer_time" value="{{ old('handover_issuer_time', $permit?->handover_issuer_time) }}" class="mt-1 w-full rounded border-gray-300">
                </div>
            </div>
        </div>

        <div class="mt-3 border p-3">
            <h4 class="font-bold">Permit Receiver</h4>
            <label class="mt-2 flex items-start gap-2">
                <input type="checkbox" name="receiver_confirmation" value="ya" class="mt-1" @checked(old('receiver_confirmation', $permit?->receiver_confirmation) === 'ya')>
                <span>Saya telah membaca dan mengerti syarat-syarat pada izin kerja ini dan akan melaksanakan pengendalian bahaya yang tertulis di sini.</span>
            </label>
            <div class="mt-3 grid grid-cols-1 gap-3 md:grid-cols-4">
                <div>
                    <label class="font-semibold">Nama</label>
                    <input type="text" name="receiver_name" value="{{ old('receiver_name', $permit?->receiver_name) }}" class="mt-1 w-full rounded border-gray-300">
                </div>
                <div>
                    <label class="font-semibold">Tanda Tangan</label>
                    @if ($permit?->receiver_signature)
                        <img src="{{ $signatureUrl($permit->receiver_signature) }}" alt="Tanda tangan Permit Receiver" class="mt-1 h-12 max-w-full border">
                    @endif
                    <input type="hidden" id="{{ $receiverSignatureId }}" name="receiver_signature" value="{{ old('receiver_signature', $permit?->receiver_signature) }}" data-signature-url="{{ $signatureUrl($permit?->receiver_signature) }}">
                    <button type="button" onclick="openSignPad('{{ $receiverSignatureId }}')" class="mt-1 rounded bg-blue-600 px-3 py-1 text-xs font-semibold text-white">Isi Tanda Tangan</button>
                </div>
                <div>
                    <label class="font-semibold">Tanggal</label>
                    <input type="date" name="receiver_date" value="{{ old('receiver_date', $permit?->receiver_date?->format('Y-m-d')) }}" class="mt-1 w-full rounded border-gray-300">
                </div>
                <div>
                    <label class="font-semibold">Jam</label>
                    <input type="time" name="receiver_time" value="{{ old('receiver_time', $permit?->receiver_time) }}" class="mt-1 w-full rounded border-gray-300">
                </div>
            </div>
        </div>

        <h3 class="mt-5 bg-black px-3 py-2 font-bold text-white">6. Instruksi Tambahan Persyaratan K3 Lainnya</h3>
        <textarea name="additional_k3_instructions" rows="6" class="mt-3 w-full rounded border-gray-300">{{ old('additional_k3_instructions', $permit?->additional_k3_instructions) }}</textarea>

        <div class="mt-5 flex justify-end">
            <button type="submit" class="rounded bg-blue-600 px-5 py-2 font-semibold text-white hover:bg-blue-700">Simpan Permit</button>
        </div>
    </div>
</form>
