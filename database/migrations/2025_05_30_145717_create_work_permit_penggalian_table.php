<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('work_permit_penggalian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_id')->constrained('notifications')->onDelete('cascade');

         
            // Bagian 2
            $table->json('denah')->nullable();
            $table->string('denah_status')->nullable();
            $table->string('file_denah')->nullable();

            // Bagian 3
            $table->json('syarat_penggalian')->nullable();

            // Bagian 4
            $table->text('rekomendasi_tambahan')->nullable();
            $table->string('rekomendasi_status')->nullable();

            // Bagian 5
            $table->string('permit_requestor_name')->nullable();
            $table->string('signature_permit_requestor')->nullable();
            $table->date('permit_requestor_date')->nullable();
            $table->time('permit_requestor_time')->nullable();

            // Bagian 6
            $table->string('verificator_name')->nullable();
            $table->string('signature_verificator')->nullable();
            $table->date('verificator_date')->nullable();
            $table->time('verificator_time')->nullable();

            // Bagian 7
            $table->string('permit_issuer_name')->nullable();
            $table->string('signature_permit_issuer')->nullable();
            $table->date('permit_issuer_date')->nullable();
            $table->time('permit_issuer_time')->nullable();
            $table->date('izin_berlaku_dari')->nullable();
            $table->time('izin_berlaku_jam_dari')->nullable();
            $table->date('izin_berlaku_sampai')->nullable();
            $table->time('izin_berlaku_jam_sampai')->nullable();

            // Bagian 8
            $table->string('permit_authorizer_name')->nullable();
            $table->string('signature_permit_authorizer')->nullable();
            $table->date('permit_authorizer_date')->nullable();
            $table->time('permit_authorizer_time')->nullable();

            // Bagian 9
            $table->string('permit_authorizer_name')->nullable();
            $table->string('signature_permit_authorizer')->nullable();
            $table->date('permit_uthorizer_date')->nullable();
            $table->time('permit_uthorizer_time')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_permit_penggalian');
    }
};
