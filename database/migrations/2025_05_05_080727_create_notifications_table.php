<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // <- ini ditambahkan
            $table->enum('type', ['po', 'spk', 'notif']);
            $table->string('number')->nullable(); // Nomor PO / SPK / Notification, dibuat nullable supaya tidak wajib
            $table->text('description')->nullable();
            $table->string('file')->nullable(); // Path file SPK jika ada
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
