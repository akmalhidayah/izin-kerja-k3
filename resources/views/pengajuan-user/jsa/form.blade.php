<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-sm text-gray-800 dark:text-gray-200 leading-tight">
            Form Job Safety Analysis
        </h2>
    </x-slot>

    @php
        $langkahKerjaSource = old('langkah_kerja', $jsa->langkah_kerja ?? []);

        if (is_string($langkahKerjaSource)) {
            $decodedLangkahKerja = json_decode($langkahKerjaSource, true);
            $langkahKerjaOld = is_array($decodedLangkahKerja) ? $decodedLangkahKerja : [];
        } else {
            $langkahKerjaOld = is_array($langkahKerjaSource) ? $langkahKerjaSource : [];
        }

        $signatureButtonClass = fn ($hasSignature) => $hasSignature
            ? 'jsa-signature-button jsa-signature-button-filled'
            : 'jsa-signature-button jsa-signature-button-empty';
        $signatureUrl = fn ($signature) => $signature
            ? (str_starts_with($signature, 'data:image') ? $signature : asset($signature))
            : '';

        $dibuatSign = old('dibuat_signature', $jsa->dibuat_signature ?? '');
        $disetujuiSign = old('disetujui_signature', $jsa->disetujui_signature ?? '');
        $diverifikasiSign = old('diverifikasi_signature', $jsa->diverifikasi_signature ?? '');
    @endphp

    <style>
        .jsa-token-section {
            background-image: url('/images/bg-login.jpg');
            padding: 2.5rem 1rem;
        }

        .jsa-token-card {
            max-width: 64rem;
            margin: 0 auto;
            background: #fff;
            border-radius: .75rem;
            box-shadow: 0 10px 20px rgba(15, 23, 42, .12);
            padding: 1.5rem;
            overflow: hidden;
        }

        .jsa-input,
        .jsa-textarea {
            width: 100%;
            min-width: 0;
            border: 1px solid #d1d5db;
            border-radius: .375rem;
            padding: .45rem .55rem;
            font-size: .8125rem;
            line-height: 1.35;
            background: #fff;
        }

        .jsa-textarea {
            min-height: 5rem;
            resize: vertical;
            white-space: pre-wrap;
            overflow-wrap: break-word;
        }

        .jsa-signature-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 7rem;
            height: 2rem;
            margin-top: .35rem;
            border-radius: .375rem;
            padding: .25rem .75rem;
            font-size: .75rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .jsa-signature-button-empty {
            background: #2563eb;
            color: #fff;
            box-shadow: 0 1px 3px rgba(37, 99, 235, .25);
        }

        .jsa-signature-button-filled {
            border: 1px solid #bfdbfe;
            background: #eff6ff;
            color: #1d4ed8;
        }

        .jsa-mobile-sign-title,
        .jsa-mobile-steps {
            display: none;
        }

        .jsa-table-scroll {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .jsa-desktop-steps {
            min-width: 760px;
        }

        @media (max-width: 640px) {
            .jsa-token-section {
                padding: 1rem .5rem;
            }

            .jsa-token-card {
                padding: 1rem;
                border-radius: .75rem;
            }

            .jsa-token-card h1 {
                font-size: 1rem;
            }

            .jsa-header-table,
            .jsa-header-table tbody,
            .jsa-header-table tr,
            .jsa-header-table td,
            .jsa-sign-table,
            .jsa-sign-table tbody,
            .jsa-sign-table tr,
            .jsa-sign-table td {
                display: block;
                width: 100%;
            }

            .jsa-header-table tr {
                border-bottom: 1px solid #e5e7eb;
            }

            .jsa-header-table tr:last-child {
                border-bottom: 0;
            }

            .jsa-header-table td {
                border: 0;
                padding: .35rem .5rem;
            }

            .jsa-sign-table thead,
            .jsa-sign-table tr:first-child {
                display: none;
            }

            .jsa-sign-table td {
                border: 1px solid #e5e7eb;
                border-radius: .5rem;
                margin-bottom: .75rem;
                padding: .75rem;
            }

            .jsa-mobile-sign-title {
                display: block;
                margin-bottom: .5rem;
                font-weight: 700;
                text-align: center;
            }

            .jsa-table-scroll {
                overflow-x: visible;
            }

            .jsa-desktop-steps {
                display: none;
            }

            .jsa-mobile-steps {
                display: block;
            }

            .jsa-mobile-step-card {
                border: 1px solid #e5e7eb;
                border-radius: .625rem;
                padding: .75rem;
                margin-bottom: .75rem;
                background: #fff;
            }

            .jsa-mobile-step-title {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: .75rem;
                margin-bottom: .75rem;
                font-size: .8125rem;
                font-weight: 700;
            }

            .jsa-mobile-field {
                display: block;
                margin-bottom: .75rem;
            }

            .jsa-mobile-field span {
                display: block;
                margin-bottom: .25rem;
                font-size: .75rem;
                font-weight: 700;
            }
        }
    </style>

    <section class="jsa-token-section bg-cover bg-center bg-no-repeat">
        <div class="jsa-token-card" x-data='formJSA(@json($langkahKerjaOld))'>
            <h1 class="text-lg font-semibold mb-4">Form Job Safety Analysis</h1>

            @if(session('success'))
                <div class="fixed top-4 left-1/2 transform -translate-x-1/2 bg-green-500 text-white px-4 py-2 rounded shadow z-50">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="fixed top-4 left-1/2 transform -translate-x-1/2 bg-red-500 text-white px-4 py-2 rounded shadow z-50">
                    {{ implode(', ', $errors->all()) }}
                </div>
            @endif

            <form method="POST" action="{{ route('jsa.form.token.store', $jsa->token) }}" @submit.prevent="serializeLangkah(); $el.submit()">
                @csrf

                <input type="hidden" name="notification_id" value="{{ $notification->id ?? '' }}">
                <input type="hidden" name="dibuat_signature" id="dibuat_signature" value="{{ $dibuatSign }}" data-signature-url="{{ $signatureUrl($dibuatSign) }}">
                <input type="hidden" name="disetujui_signature" id="disetujui_signature" value="{{ $disetujuiSign }}" data-signature-url="{{ $signatureUrl($disetujuiSign) }}">
                <input type="hidden" name="diverifikasi_signature" id="diverifikasi_signature" value="{{ $diverifikasiSign }}" data-signature-url="{{ $signatureUrl($diverifikasiSign) }}">
                <input type="hidden" name="langkah_kerja" id="langkah_kerja">

                <table class="jsa-header-table table-auto w-full border text-xs mb-3">
                    <tr>
                        <td class="border px-2 py-1 font-semibold">Nama Perusahaan</td>
                        <td class="border px-2 py-1" colspan="3">
                            <input type="text" name="nama_perusahaan" class="jsa-input" value="{{ old('nama_perusahaan', $jsa->nama_perusahaan ?? '') }}">
                        </td>
                    </tr>
                    <tr>
                        <td class="border px-2 py-1 font-semibold">Job Safety Analysis No</td>
                        <td class="border px-2 py-1">
                            <input type="text" name="no_jsa" class="jsa-input" value="{{ old('no_jsa', $jsa->no_jsa ?? '') }}" readonly>
                        </td>
                        <td class="border px-2 py-1 font-semibold">Nama JSA</td>
                        <td class="border px-2 py-1">
                            <input type="text" name="nama_jsa" class="jsa-input" value="{{ old('nama_jsa', $jsa->nama_jsa ?? '') }}">
                        </td>
                    </tr>
                    <tr>
                        <td class="border px-2 py-1 font-semibold">Departemen</td>
                        <td class="border px-2 py-1">
                            <input type="text" name="departemen" class="jsa-input" value="{{ old('departemen', $jsa->departemen ?? '') }}">
                        </td>
                        <td class="border px-2 py-1 font-semibold">Area Kerja</td>
                        <td class="border px-2 py-1">
                            <input type="text" name="area_kerja" class="jsa-input" value="{{ old('area_kerja', $jsa->area_kerja ?? '') }}">
                        </td>
                    </tr>
                    <tr>
                        <td class="border px-2 py-1 font-semibold">Tanggal</td>
                        <td class="border px-2 py-1" colspan="3">
                            <input type="date" name="tanggal" class="jsa-input" value="{{ old('tanggal', isset($jsa->tanggal) ? $jsa->tanggal->format('Y-m-d') : '') }}">
                        </td>
                    </tr>
                </table>

                <table class="jsa-sign-table table-auto w-full border text-xs mb-3">
                    <tr class="text-center font-semibold">
                        <td class="border px-2 py-1">Dibuat oleh</td>
                        <td class="border px-2 py-1">Disetujui oleh</td>
                        <td class="border px-2 py-1">Diverifikasi oleh</td>
                    </tr>
                    <tr>
                        <td class="border px-2 py-4 text-center align-top">
                            <div class="jsa-mobile-sign-title">Dibuat oleh</div>
                            <input type="text" name="dibuat_nama" class="jsa-input mb-2" placeholder="Masukkan Nama" value="{{ old('dibuat_nama', $jsa->dibuat_nama ?? '') }}">
                            @if($dibuatSign)
                                <div class="flex justify-center mt-2">
                                    <img src="{{ $signatureUrl($dibuatSign) }}" class="h-12" alt="TTD Dibuat">
                                </div>
                            @endif
                            <button type="button" onclick="openSignPad('dibuat_signature')" class="{{ $signatureButtonClass($dibuatSign) }}">
                                {{ $dibuatSign ? 'Ubah TTD' : 'Tanda Tangan' }}
                            </button>
                        </td>
                        <td class="border px-2 py-4 text-center align-top">
                            <div class="jsa-mobile-sign-title">Disetujui oleh</div>
                            <input type="text" name="disetujui_nama" class="jsa-input mb-2" placeholder="Masukkan Nama" value="{{ old('disetujui_nama', $jsa->disetujui_nama ?? '') }}">
                            @if($disetujuiSign)
                                <div class="flex justify-center mt-2">
                                    <img src="{{ $signatureUrl($disetujuiSign) }}" class="h-12" alt="TTD Disetujui">
                                </div>
                            @endif
                            <button type="button" onclick="openSignPad('disetujui_signature')" class="{{ $signatureButtonClass($disetujuiSign) }}">
                                {{ $disetujuiSign ? 'Ubah TTD' : 'Tanda Tangan' }}
                            </button>
                        </td>
                        <td class="border px-2 py-4 text-center align-top">
                            <div class="jsa-mobile-sign-title">Diverifikasi oleh</div>
                            <input type="text" name="diverifikasi_nama" class="jsa-input mb-2" placeholder="Masukkan Nama" value="{{ old('diverifikasi_nama', $jsa->diverifikasi_nama ?? '') }}">
                            @if($diverifikasiSign)
                                <div class="flex justify-center mt-2">
                                    <img src="{{ $signatureUrl($diverifikasiSign) }}" class="h-12" alt="TTD Diverifikasi">
                                </div>
                            @endif
                            <button type="button" onclick="openSignPad('diverifikasi_signature')" class="{{ $signatureButtonClass($diverifikasiSign) }}">
                                {{ $diverifikasiSign ? 'Ubah TTD' : 'Tanda Tangan' }}
                            </button>
                        </td>
                    </tr>
                </table>

                <div class="jsa-table-scroll mb-3">
                    <table class="jsa-desktop-steps table-auto w-full border text-xs">
                        <thead class="bg-gray-100">
                            <tr class="text-center font-semibold">
                                <th class="border px-2 py-1 w-12">No</th>
                                <th class="border px-2 py-1">Urutan Langkah Kerja</th>
                                <th class="border px-2 py-1">Bahaya/Risiko</th>
                                <th class="border px-2 py-1">Pengendalian</th>
                                <th class="border px-2 py-1 w-16">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(row, index) in langkahKerja" :key="index">
                                <tr>
                                    <td class="border px-2 py-1 text-center" x-text="index + 1"></td>
                                    <td class="border px-2 py-1">
                                        <textarea x-model="row.langkah" class="jsa-textarea" rows="3"></textarea>
                                    </td>
                                    <td class="border px-2 py-1">
                                        <textarea x-model="row.bahaya" class="jsa-textarea" rows="3"></textarea>
                                    </td>
                                    <td class="border px-2 py-1">
                                        <textarea x-model="row.pengendalian" class="jsa-textarea" rows="3"></textarea>
                                    </td>
                                    <td class="border px-2 py-1 text-center">
                                        <button type="button" @click="hapusRow(index)" class="text-red-500 text-xs">Hapus</button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div class="jsa-mobile-steps">
                    <template x-for="(row, index) in langkahKerja" :key="index">
                        <div class="jsa-mobile-step-card">
                            <div class="jsa-mobile-step-title">
                                <span>Langkah <span x-text="index + 1"></span></span>
                                <button type="button" @click="hapusRow(index)" class="text-red-500 text-xs">Hapus</button>
                            </div>
                            <label class="jsa-mobile-field">
                                <span>Urutan Langkah Kerja</span>
                                <textarea x-model="row.langkah" class="jsa-textarea" rows="3"></textarea>
                            </label>
                            <label class="jsa-mobile-field">
                                <span>Bahaya/Risiko</span>
                                <textarea x-model="row.bahaya" class="jsa-textarea" rows="3"></textarea>
                            </label>
                            <label class="jsa-mobile-field">
                                <span>Pengendalian</span>
                                <textarea x-model="row.pengendalian" class="jsa-textarea" rows="3"></textarea>
                            </label>
                        </div>
                    </template>
                </div>

                <button type="button" @click="tambahRow()" class="bg-blue-500 text-white px-3 py-2 rounded text-xs mb-3">
                    + Tambah Baris
                </button>

                <div class="mt-4 text-right">
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded text-xs">Simpan</button>
                </div>
            </form>
        </div>
    </section>

    <script>
        function formJSA(existing) {
            const data = Array.isArray(existing) ? existing : [];

            return {
                langkahKerja: data.length ? data : [{ langkah: '', bahaya: '', pengendalian: '' }],
                tambahRow() {
                    this.langkahKerja.push({ langkah: '', bahaya: '', pengendalian: '' });
                },
                hapusRow(index) {
                    this.langkahKerja.splice(index, 1);
                },
                serializeLangkah() {
                    document.getElementById('langkah_kerja').value = JSON.stringify(this.langkahKerja);
                }
            };
        }
    </script>

    @include('components.sign-pad')
</x-app-layout>
