<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('work_permit_air', function (Blueprint $table) {
            $table->id();
            // Bagian 1: Detail Pekerjaan (Diambil dari table details/permintaan)
            $table->foreignId('notification_id')->constrained('notifications')->onDelete('cascade');

            // Bagian 2: Daftar Pekerja
            $table->json('daftar_pekerja')->nullable();
            $table->string('sketsa_pekerjaan')->nullable();

            // Bagian 3: Persyaratan Kerja Aman
            $table->json('persyaratan_perairan')->nullable();

            // Bagian 4: Rekomendasi Persyaratan
            $table->text('rekomendasi_kerja_aman')->nullable();
            $table->string('rekomendasi_kerja_aman_check')->nullable();

            // Bagian 5: Permohonan Izin Kerja
            $table->string('permit_requestor_name')->nullable();
            $table->string('signature_permit_requestor')->nullable();
            $table->date('permit_requestor_date')->nullable();
            $table->time('permit_requestor_time')->nullable();

            // Bagian 6: Verifikasi Izin Kerja
            $table->json('verified_workers')->nullable();
            $table->string('verificator_name')->nullable();
            $table->string('signature_verificator')->nullable();
            $table->date('verificator_date')->nullable();
            $table->time('verificator_time')->nullable();

            // Bagian 7: Penerbitan Izin Kerja
            $table->string('permit_issuer_name')->nullable();
            $table->string('signature_permit_issuer')->nullable();
            $table->string('senior_manager_name')->nullable();
            $table->string('senior_manager_signature')->nullable();
            $table->string('general_manager_name')->nullable();
            $table->string('general_manager_signature')->nullable();
            $table->date('izin_berlaku_dari')->nullable();
            $table->time('izin_berlaku_jam_dari')->nullable();
            $table->date('izin_berlaku_sampai')->nullable();
            $table->time('izin_berlaku_jam_sampai')->nullable();

            // Bagian 8: Pengesahan Izin Kerja
            $table->string('permit_authorizer_name')->nullable();
            $table->string('signature_permit_authorizer')->nullable();
            $table->date('permit_authorizer_date')->nullable();
            $table->time('permit_authorizer_time')->nullable();

            // Bagian 9: Pelaksanaan Pekerjaan
            $table->string('permit_receiver_name')->nullable();
            $table->string('signature_permit_receiver')->nullable();
            $table->date('permit_receiver_date')->nullable();
            $table->time('permit_receiver_time')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_permit_air');
    }
};
