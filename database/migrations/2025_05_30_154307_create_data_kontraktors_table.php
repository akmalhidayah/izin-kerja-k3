<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('data_kontraktors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_id')->constrained('notifications')->onDelete('cascade');
            $table->string('nama_perusahaan');
            $table->string('jenis_pekerjaan')->nullable();
            $table->string('lokasi_kerja')->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();

            $table->json('tenaga_kerja')->nullable(); // [{nama, jumlah, satuan}]
            $table->json('peralatan_kerja')->nullable(); // [{nama, jumlah, satuan}]
            $table->json('apd')->nullable(); // [{nama, jumlah, satuan}]

            // Kolom tanda tangan dan nama
            $table->string('manager_nama')->nullable();
            $table->longText('ttd_manager')->nullable();
            $table->string('perusahaan_nama')->nullable();
            $table->longText('ttd_perusahaan')->nullable();

            $table->string('diverifikasi_nama')->nullable();
            $table->longText('diverifikasi_signature')->nullable();

            $table->string('token')->unique()->nullable();


            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_kontraktors');
    }
};

