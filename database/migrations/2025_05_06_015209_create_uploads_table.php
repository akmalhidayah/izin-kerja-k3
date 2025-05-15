<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('uploads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_id')->constrained('notifications')->onDelete('cascade');
            $table->enum('step', [
                'bpjs', 'fakta_integritas', 'sertifikasi_ak3', 
                'ktp', 'surat_kesehatan', 'struktur_organisasi', 
                'post_test', 'bukti_serah_terima'
            ]);
            $table->string('file_path');
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uploads');
    }
};
