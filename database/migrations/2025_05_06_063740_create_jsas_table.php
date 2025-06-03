<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('jsas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_id')->constrained()->onDelete('cascade');
            $table->string('nama_perusahaan')->nullable();
            $table->string('no_jsa')->nullable();
            $table->string('nama_jsa');
            $table->string('departemen');
            $table->string('area_kerja');
            $table->date('tanggal');
            $table->string('dibuat_nama');
            $table->text('dibuat_signature')->nullable();
            $table->string('disetujui_nama');
            $table->text('disetujui_signature')->nullable();
            $table->string('diverifikasi_nama')->nullable(); // ✅ tambah
            $table->text('diverifikasi_signature')->nullable(); // ✅ tambah
            $table->json('langkah_kerja'); // untuk array langkah, bahaya, pengendalian
            $table->string('token')->unique()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jsas');
    }
};
