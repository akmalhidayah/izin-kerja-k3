<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('umum_work_permits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_id')->constrained()->onDelete('cascade');

            // Izin Kerja Khusus
            $table->json('izin_khusus')->nullable();

            // Isolasi Energi
            $table->json('isolasi_listrik')->nullable();
            $table->json('isolasi_non_listrik')->nullable();

            // Checklist Kerja Aman
            $table->json('checklist_kerja_aman')->nullable();

            // Rekomendasi
            $table->text('rekomendasi_tambahan')->nullable();
            $table->string('rekomendasi_status')->nullable();

            // Permohonan Izin
            $table->string('permit_requestor_name')->nullable();
            $table->text('permit_requestor_sign')->nullable();
            $table->date('permit_requestor_date')->nullable();
            $table->time('permit_requestor_time')->nullable();

            // Penerbitan Izin
            $table->string('permit_issuer_name')->nullable();
            $table->text('permit_issuer_sign')->nullable();
            $table->date('permit_issuer_date')->nullable();
            $table->time('permit_issuer_time')->nullable();
            $table->date('izin_berlaku_dari')->nullable();
            $table->time('izin_berlaku_jam_dari')->nullable();
            $table->date('izin_berlaku_sampai')->nullable();
            $table->time('izin_berlaku_jam_sampai')->nullable();

            // Pengesahan
            $table->string('permit_authorizer_name')->nullable();
            $table->text('permit_authorizer_sign')->nullable();
            $table->date('permit_authorizer_date')->nullable();
            $table->time('permit_authorizer_time')->nullable();

            // Pelaksanaan
            $table->string('permit_receiver_name')->nullable();
            $table->text('permit_receiver_sign')->nullable();
            $table->date('permit_receiver_date')->nullable();
            $table->time('permit_receiver_time')->nullable();

            // Live Testing
            $table->json('live_testing_items')->nullable();
            $table->string('live_testing_name')->nullable();
            $table->text('live_testing_sign')->nullable();
            $table->date('live_testing_date')->nullable();
            $table->time('live_testing_time')->nullable();

            $table->string('token')->unique()->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('umum_work_permits');
    }
};
