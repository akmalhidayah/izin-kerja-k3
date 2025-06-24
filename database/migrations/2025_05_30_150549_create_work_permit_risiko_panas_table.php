<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('work_permit_risiko_panas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_id')->constrained()->cascadeOnDelete();
            $table->json('pengukuran_gas')->nullable(); // Data JSON (O2, LEL, CO, H2S, O3)
            $table->json('persyaratan_kerja_panas')->nullable(); // Array radio button
            $table->text('rekomendasi_kerja_aman_tambahan')->nullable();
            $table->string('rekomendasi_kerja_aman_setuju')->nullable();

            // Bagian 5 - Permohonan
            $table->string('requestor_name')->nullable();
            $table->string('signature_requestor')->nullable();
            $table->date('requestor_date')->nullable();
            $table->time('requestor_time')->nullable();

            // Bagian 6 - Verifikasi
            $table->string('verificator_name')->nullable();
            $table->string('signature_verificator')->nullable();
            $table->date('verificator_date')->nullable();
            $table->time('verificator_time')->nullable();

            // Bagian 7 - Penerbitan
            $table->string('permit_issuer_name')->nullable();
            $table->string('signature_permit_issuer')->nullable();
            $table->string('senior_manager_name')->nullable();
            $table->string('signature_senior_manager')->nullable();
            $table->string('general_manager_name')->nullable();
            $table->string('signature_general_manager')->nullable();
            $table->date('izin_berlaku_dari')->nullable();
            $table->time('izin_berlaku_jam_dari')->nullable();
            $table->date('izin_berlaku_sampai')->nullable();
            $table->time('izin_berlaku_jam_sampai')->nullable();

            // Bagian 8 - Pengesahan
            $table->string('authorizer_name')->nullable();
            $table->string('authorizer_signature')->nullable();
            $table->date('authorizer_date')->nullable();
            $table->time('authorizer_time')->nullable();

            // Bagian 9 - Pelaksanaan
            $table->string('receiver_name')->nullable();
            $table->string('receiver_signature')->nullable();
            $table->date('receiver_date')->nullable();
            $table->time('receiver_time')->nullable();

            // Bagian 10 - Penutupan
            $table->longText('requestor_signature_close')->nullable();
            $table->longText('issuer_signature_close')->nullable();
            $table->string('token')->unique()->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_permit_risiko_panas');
    }
};
