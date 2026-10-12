<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('work_permit_listrik_tegangan_tinggi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_id')->unique()->constrained('notifications')->cascadeOnDelete();

            $table->string('permit_number')->nullable();
            $table->string('work_location')->nullable();
            $table->date('work_date')->nullable();
            $table->text('equipment_machine')->nullable();
            $table->string('permit_issuer_name')->nullable();
            $table->string('permit_issuer_phone')->nullable();
            $table->text('job_scope')->nullable();
            $table->unsignedInteger('worker_count')->nullable();
            $table->text('tools_equipment')->nullable();

            $table->string('other_permit_status')->nullable();
            $table->text('other_permit_details')->nullable();
            $table->json('documentation_checks')->nullable();
            $table->string('supporting_document')->nullable();

            $table->json('safety_requirements')->nullable();
            $table->string('energy_isolation_certificate')->nullable();
            $table->json('additional_hazards')->nullable();

            $table->json('issuer_confirmations')->nullable();
            $table->string('handover_issuer_name')->nullable();
            $table->string('handover_issuer_signature')->nullable();
            $table->date('handover_issuer_date')->nullable();
            $table->time('handover_issuer_time')->nullable();

            $table->string('receiver_confirmation')->nullable();
            $table->string('receiver_name')->nullable();
            $table->string('receiver_signature')->nullable();
            $table->date('receiver_date')->nullable();
            $table->time('receiver_time')->nullable();

            $table->text('additional_k3_instructions')->nullable();
            $table->uuid('token')->nullable()->unique();
            $table->timestamp('token_expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_permit_listrik_tegangan_tinggi');
    }
};
