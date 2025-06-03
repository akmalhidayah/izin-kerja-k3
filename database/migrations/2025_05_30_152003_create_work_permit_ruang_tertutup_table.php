<?php

// database/migrations/2024_XX_XX_create_work_permit_ruang_tertutup_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('work_permit_ruang_tertutup', function (Blueprint $table) {
            $table->id();
            $table->string('lokasi_pekerjaan')->nullable();
            $table->date('tanggal_pekerjaan')->nullable();
            $table->text('uraian_pekerjaan')->nullable();
            $table->text('peralatan_digunakan')->nullable();
            $table->integer('jumlah_pekerja')->nullable();
            $table->string('nomor_darurat')->nullable();

            // Bagian 2: Isolasi Listrik & Non-Listrik
            $table->json('isolasi_listrik')->nullable(); // Array JSON
            $table->json('isolasi_non_listrik')->nullable(); // Array JSON

            // Bagian 3: Pengukuran Gas
            $table->json('pengukuran_gas')->nullable(); // Array JSON per gas: O2, LEL, CO, H2S, O3

            // Bagian 4: Persyaratan Kerja Aman
            $table->json('syarat_ruang_tertutup')->nullable(); // Array index persyaratan

            // Bagian 5: Rekomendasi Tambahan
            $table->text('rekomendasi_tambahan')->nullable();
            $table->string('rekomendasi_status')->nullable(); // ya/na

            // Bagian 6: Permohonan Izin Kerja
            $table->string('permit_requestor_name')->nullable();
            $table->string('signature_permit_requestor')->nullable();
            $table->date('permit_requestor_date')->nullable();
            $table->time('permit_requestor_time')->nullable();

            // Bagian 7: Verifikasi Izin Kerja
            $table->string('confined_verificator_name')->nullable();
            $table->string('signature_confined_verificator')->nullable();
            $table->date('confined_verificator_date')->nullable();
            $table->time('confined_verificator_time')->nullable();

            // Bagian 8: Penerbitan Izin Kerja
            $table->string('permit_issuer_name')->nullable();
            $table->string('signature_permit_issuer')->nullable();
            $table->date('permit_issuer_date')->nullable();
            $table->time('permit_issuer_time')->nullable();
            $table->date('izin_berlaku_dari')->nullable();
            $table->time('izin_berlaku_jam_dari')->nullable();
            $table->date('izin_berlaku_sampai')->nullable();
            $table->time('izin_berlaku_jam_sampai')->nullable();

            // Bagian 9: Pengesahan Izin Kerja
            $table->string('permit_authorizer_name')->nullable();
            $table->string('signature_permit_authorizer')->nullable();
            $table->date('permit_authorizer_date')->nullable();
            $table->time('permit_authorizer_time')->nullable();

            // Bagian 10: Pelaksanaan Pekerjaan
            $table->string('permit_receiver_name')->nullable();
            $table->string('signature_permit_receiver')->nullable();
            $table->date('permit_receiver_date')->nullable();
            $table->time('permit_receiver_time')->nullable();

            // Bagian 11: Daftar Pekerja Masuk/Keluar
            $table->json('pekerja_masuk_keluar')->nullable(); // Array JSON nama, perusahaan, tanggal, masuk, keluar, sign

            // Bagian 12: Live Testing
            $table->json('live_testing_checklist')->nullable(); // Array JSON checklist live testing (boolean)
            $table->string('live_testing_name')->nullable();
            $table->string('live_testing_signature')->nullable();
            $table->date('live_testing_date')->nullable();
            $table->time('live_testing_time')->nullable();

         
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('work_permit_ruang_tertutup');
    }
};
