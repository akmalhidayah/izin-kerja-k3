<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('work_permit_gas_panas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_id')->constrained()->onDelete('cascade');

            // Bagian 2: Daftar Pekerja
            $table->json('daftar_pekerja')->nullable();

            // Bagian 3: Checklist Persyaratan Kerja Aman
            $table->json('checklist_kerja_aman')->nullable();

            // Bagian 4: Rekomendasi Tambahan
            $table->text('rekomendasi_tambahan')->nullable();
            $table->string('rekomendasi_status')->nullable();

            // Bagian 5: Permohonan Izin
            $table->string('permit_requestor_name')->nullable();
            $table->text('permit_requestor_sign')->nullable();
            $table->date('permit_requestor_date')->nullable();
            $table->time('permit_requestor_time')->nullable();

            // Bagian 6: Verifikasi
            $table->json('verified_workers')->nullable();
            $table->string('verificator_name')->nullable();
            $table->text('verificator_sign')->nullable();
            $table->date('verificator_date')->nullable();
            $table->time('verificator_time')->nullable();

            // Bagian 7: Penerbitan
            $table->string('permit_issuer_name')->nullable();
            $table->text('permit_issuer_sign')->nullable();
            $table->date('permit_issuer_date')->nullable();
            $table->time('permit_issuer_time')->nullable();
            $table->date('izin_berlaku_dari')->nullable();
            $table->time('izin_berlaku_jam_dari')->nullable();
            $table->date('izin_berlaku_sampai')->nullable();
            $table->time('izin_berlaku_jam_sampai')->nullable();

            // Bagian 8: Pengesahan
            $table->string('permit_authorizer_name')->nullable();
            $table->text('permit_authorizer_sign')->nullable();
            $table->date('permit_authorizer_date')->nullable();
            $table->time('permit_authorizer_time')->nullable();

            // Bagian 9: Pelaksanaan
            $table->string('permit_receiver_name')->nullable();
            $table->text('permit_receiver_sign')->nullable();
            $table->date('permit_receiver_date')->nullable();
            $table->time('permit_receiver_time')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_permit_gas_panas');
    }
};
