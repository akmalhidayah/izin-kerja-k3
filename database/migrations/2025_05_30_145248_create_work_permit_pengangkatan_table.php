<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('work_permit_pengangkatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_id')->constrained('notifications')->onDelete('cascade'); // Relasi dengan notifikasi utama

            // Bagian 1
            $table->string('jenis_tipe')->nullable();
            $table->string('max_boom')->nullable();
            $table->string('min_boom')->nullable();
            $table->string('kapasitas_maks')->nullable();
            $table->string('swl_main_block')->nullable();
            $table->string('swl_aux_block')->nullable();
            $table->string('max_radius')->nullable();
            $table->string('min_radius')->nullable();
            $table->string('swl_chain')->nullable();
            $table->string('swl_master_link')->nullable();
            $table->string('swl_shackle')->nullable();
            $table->string('swl_hammer_lock')->nullable();
            $table->string('swl_spreader_bar')->nullable();
            $table->string('swl_hook')->nullable();
            $table->string('swl_pulley')->nullable();
            $table->string('swl_anchor')->nullable();
            $table->string('berat_beban')->nullable();
            $table->string('berat_man_basket')->nullable();

            // Bagian 2
            $table->json('teknik_pengikatan')->nullable();

            // Bagian 3
            $table->json('wire_rope_sling')->nullable();

            // Bagian 4
            $table->string('diagram_pesawat')->nullable();
            $table->string('diagram_pengikatan')->nullable();

            // Bagian 5
            $table->string('rigger_name')->nullable();
            $table->string('signature_rigger')->nullable();
            $table->string('operator_name')->nullable();
            $table->string('signature_operator')->nullable();
            $table->string('verificator_name')->nullable();
            $table->string('signature_verificator')->nullable();
            $table->string('token')->unique()->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_permit_pengangkatan');
    }
};
