<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('work_permit_life_saving', function (Blueprint $table) {
            $table->id();
            $table->string('lokasi_pekerjaan')->nullable();
            $table->date('tanggal')->nullable();
            $table->text('uraian_pekerjaan')->nullable();
            $table->string('workers_supervisor')->nullable();
            $table->string('job_supervisor')->nullable();
            $table->json('bahaya_pengendalian')->nullable(); // untuk bagian 2 (bahaya utama & pengendalian)
            $table->string('sketsa_pekerjaan')->nullable(); // file upload sketsa
            $table->json('daftar_pekerja')->nullable(); // untuk bagian 3
            $table->json('daftar_hadir')->nullable(); // untuk bagian 4
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_permit_life_saving');
    }
};
