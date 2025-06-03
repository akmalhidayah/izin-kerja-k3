<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('work_permit_prosedur_khusus', function (Blueprint $table) {
            $table->id();

            $table->string('swp_nama')->nullable();
            $table->string('swp_lokasi')->nullable();
            $table->date('swp_dibuat_tanggal')->nullable();
            $table->date('swp_revisi_terakhir')->nullable();

            $table->json('bahaya')->nullable();
            $table->json('apd')->nullable();
            $table->json('kompetensi')->nullable();
            $table->json('prosedur')->nullable();
            $table->json('referensi')->nullable();

            $table->string('dibuat_oleh')->nullable();
            $table->string('ditinjau_oleh')->nullable();
            $table->string('disetujui_oleh')->nullable();

            $table->string('signature_dibuat_oleh')->nullable();
            $table->string('signature_ditinjau_oleh')->nullable();
            $table->string('signature_disetujui_oleh')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_permit_prosedur_khusus');
    }
};
