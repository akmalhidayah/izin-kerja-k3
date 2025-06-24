<?php

// database/migrations/2024_XX_XX_create_work_permit_ruang_tertutup_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('work_permit_ruang_tertutup', function (Blueprint $table) {
            $table->id();

            // Relasi
            $table->unsignedBigInteger('notification_id')->nullable()->index();
            $table->foreign('notification_id')->references('id')->on('notifications')->onDelete('cascade');

            // Bagian 2
            $table->json('isolasi_listrik')->nullable();
            $table->json('isolasi_non_listrik')->nullable();

            // Bagian 3
            $table->json('pengukuran_gas')->nullable();

            // Bagian 4
            $table->json('syarat_ruang_tertutup')->nullable();

            // Bagian 5
            $table->text('rekomendasi_tambahan')->nullable();
            $table->string('rekomendasi_status')->nullable();

            // Bagian 6
            $table->string('permit_requestor_name')->nullable();
            $table->string('signature_permit_requestor')->nullable();
            $table->date('permit_requestor_date')->nullable();
            $table->time('permit_requestor_time')->nullable();

            // Bagian 7
            $table->string('confined_verificator_name')->nullable();
            $table->string('signature_confined_verificator')->nullable();
            $table->date('confined_verificator_date')->nullable();
            $table->time('confined_verificator_time')->nullable();

            // Bagian 8
            $table->string('permit_issuer_name')->nullable();
            $table->string('signature_permit_issuer')->nullable();
            $table->date('permit_issuer_date')->nullable();
            $table->time('permit_issuer_time')->nullable();
            $table->date('izin_berlaku_dari')->nullable();
            $table->time('izin_berlaku_jam_dari')->nullable();
            $table->date('izin_berlaku_sampai')->nullable();
            $table->time('izin_berlaku_jam_sampai')->nullable();

            // Bagian 9
            $table->string('permit_authorizer_name')->nullable();
            $table->string('signature_permit_authorizer')->nullable();
            $table->date('permit_authorizer_date')->nullable();
            $table->time('permit_authorizer_time')->nullable();

            // Bagian 10
            $table->string('permit_receiver_name')->nullable();
            $table->string('signature_permit_receiver')->nullable();
            $table->date('permit_receiver_date')->nullable();
            $table->time('permit_receiver_time')->nullable();

            // Bagian 11
            $table->json('pekerja_masuk_keluar')->nullable();

            // Bagian 12
            $table->json('live_testing_checklist')->nullable();
            $table->string('live_testing_name')->nullable();
            $table->string('live_testing_signature')->nullable();
            $table->date('live_testing_date')->nullable();
            $table->time('live_testing_time')->nullable();
            $table->string('token')->unique()->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('work_permit_ruang_tertutup');
    }
};