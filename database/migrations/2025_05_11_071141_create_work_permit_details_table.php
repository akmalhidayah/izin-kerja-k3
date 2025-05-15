<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('work_permit_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_id')->constrained()->onDelete('cascade');
            $table->enum('permit_type', [
                'umum', 'ruang_terbatas', 'penggalian', 'ketinggian', 'air',
                'panas', 'gaspanas', 'beban', 'pengangkatan', 'perancah'
            ]);
            $table->string('location')->nullable();
            $table->date('work_date')->nullable();
            $table->text('job_description')->nullable();
            $table->text('equipment')->nullable();
            $table->integer('worker_count')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_permit_details');
    }
};
