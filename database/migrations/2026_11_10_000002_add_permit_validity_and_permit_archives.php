<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Satu jam masa berlaku per pengajuan, berlaku untuk semua permit (umum + khusus).
        // Pengajuan lama tetap NULL sehingga tidak pernah kedaluwarsa (aturan hanya untuk persetujuan baru).
        Schema::table('notifications', function (Blueprint $table) {
            $table->unsignedInteger('permit_cycle')->default(1);
            $table->timestamp('permit_valid_from')->nullable();
            $table->timestamp('permit_expires_at')->nullable();
        });

        // Arsip permit per periode: salinan data + PDF, hanya untuk admin.
        Schema::create('permit_archives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_id')->constrained('notifications')->cascadeOnDelete();
            $table->unsignedInteger('cycle');
            $table->string('permit_type', 40);
            $table->timestamp('valid_from')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->json('snapshot');
            $table->string('pdf_path')->nullable();
            $table->foreignId('archived_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['notification_id', 'cycle']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permit_archives');

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn(['permit_cycle', 'permit_valid_from', 'permit_expires_at']);
        });
    }
};
