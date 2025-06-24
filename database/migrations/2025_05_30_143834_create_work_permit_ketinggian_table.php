<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('work_permit_ketinggian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_id')->constrained('notifications')->onDelete('cascade');

            // Bagian 2
            $table->json('nama_pekerja')->nullable();
            $table->string('paraf_pekerja')->nullable();
            $table->string('sketsa_pekerjaan')->nullable();

            // Bagian 3
            $table->json('kerja_aman_ketinggian')->nullable();

            // Bagian 4
            $table->text('rekomendasi_tambahan')->nullable();
            $table->string('rekomendasi_ada')->nullable();

            // Bagian 5
            $table->string('permit_requestor_name')->nullable();
            $table->string('signature_permit_requestor')->nullable();
            $table->date('permit_requestor_date')->nullable();
            $table->time('permit_requestor_time')->nullable();

            // Bagian 6
            $table->json('authorized_workers')->nullable();
            $table->string('verifikator_name')->nullable();
            $table->string('signature_verifikator')->nullable();
            $table->date('verifikator_date')->nullable();
            $table->time('verifikator_time')->nullable();

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
            $table->string('permit_receiver_name')->nullable();
            $table->string('signature_permit_receiver')->nullable();
            $table->date('permit_receiver_date')->nullable();
            $table->time('permit_receiver_time')->nullable();

            $table->string('token')->unique()->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_permit_ketinggian');
    }
};
