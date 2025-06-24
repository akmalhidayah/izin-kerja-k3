<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('work_permit_perancah', function (Blueprint $table) {
            $table->id();

            // Relasi ke notifikasi (wajib)
            $table->unsignedBigInteger('notification_id')->nullable()->index();

            // Bagian 2: Sketsa & Persyaratan Perancah
            $table->text('sketsa_perancah')->nullable();
            $table->json('persyaratan_perancah')->nullable();

            // Bagian 3: Permohonan Penerbitan
            $table->string('permit_requestor_name')->nullable();
            $table->string('signature_permit_requestor_perancah')->nullable();
            $table->date('permit_requestor_date')->nullable();
            $table->time('permit_requestor_time')->nullable();

            // Bagian 4: Verifikasi Scaffolding
            $table->string('scaffolding_verificator_name')->nullable();
            $table->string('signature_scaffolding_verificator')->nullable();
            $table->date('scaffolding_verificator_date')->nullable();
            $table->time('scaffolding_verificator_time')->nullable();

            // Bagian 5: Penerbitan Izin
            $table->string('permit_issuer_name')->nullable();
            $table->string('signature_permit_issuer')->nullable();
            $table->date('permit_issuer_date')->nullable();
            $table->time('permit_issuer_time')->nullable();

            // Bagian 6: Informasi Masa Berlaku
            $table->date('izin_berlaku_dari')->nullable();
            $table->time('izin_berlaku_jam_dari')->nullable();
            $table->date('izin_berlaku_sampai')->nullable();
            $table->time('izin_berlaku_jam_sampai')->nullable();

            // Bagian 7: Pengesahan
            $table->string('permit_authorizer_name')->nullable();
            $table->string('signature_permit_authorizer')->nullable();
            $table->date('permit_authorizer_date')->nullable();
            $table->time('permit_authorizer_time')->nullable();

            // Bagian 8: Penerimaan
            $table->string('permit_receiver_name')->nullable();
            $table->string('signature_permit_receiver')->nullable();
            $table->date('permit_receiver_date')->nullable();
            $table->time('permit_receiver_time')->nullable();

            // Bagian 9: Persyaratan & Rekomendasi Keselamatan
            $table->json('persyaratan_keselamatan_perancah')->nullable();
            $table->text('rekomendasi_keselamatan_perancah')->nullable();
            $table->string('rekomendasi_status')->nullable();

            // Bagian 10: Persetujuan
            $table->string('scaffolding_verificator_approval')->nullable();
            $table->string('signature_verificator_approval')->nullable();
            $table->string('permit_issuer_approval')->nullable();
            $table->string('signature_issuer_approval')->nullable();
            $table->string('permit_authorizer_approval')->nullable();
            $table->string('signature_authorizer_approval')->nullable();
             $table->date('perancah_start_date')->nullable();
            $table->time('perancah_start_time')->nullable();
            $table->date('perancah_end_date')->nullable();
            $table->time('perancah_end_time')->nullable();
            $table->string('token')->unique()->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_permit_perancah');
    }
};
