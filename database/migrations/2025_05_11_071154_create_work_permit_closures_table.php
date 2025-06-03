<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('work_permit_closures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_permit_detail_id')->constrained()->onDelete('cascade');
            $table->boolean('lock_tag_removed')->default(false);
            $table->boolean('equipment_cleaned')->default(false);
            $table->boolean('guarding_restored')->default(false);
            $table->date('closed_date')->nullable();
            $table->time('closed_time')->nullable();
            $table->string('requestor_name')->nullable();
            $table->string('requestor_sign')->nullable(); // simpan path file atau base64
            $table->string('issuer_name')->nullable();
            $table->string('issuer_sign')->nullable();
            $table->unsignedInteger('jumlah_rfid')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_permit_closures');
    }
};
