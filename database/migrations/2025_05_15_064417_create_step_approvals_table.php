<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('step_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_id')->constrained()->onDelete('cascade');
            $table->string('step'); // Misal: 'bpjs', 'ktp', 'jsa', 'sik', dll
            $table->enum('status', ['menunggu', 'disetujui', 'revisi'])->default('menunggu');
            $table->text('catatan')->nullable(); // Alasan revisi jika ada
            $table->string('file_path')->nullable(); // Lokasi file yang diupload (misalnya SIK)
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->longText('signature_senior_manager')->nullable();
            $table->longText('signature_manager')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('step_approvals');
    }
};
