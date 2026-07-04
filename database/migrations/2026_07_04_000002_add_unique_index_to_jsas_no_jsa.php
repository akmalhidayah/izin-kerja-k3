<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('jsas')) {
            return;
        }

        Schema::table('jsas', function (Blueprint $table) {
            $table->unique('no_jsa', 'jsas_no_jsa_unique');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('jsas')) {
            return;
        }

        Schema::table('jsas', function (Blueprint $table) {
            $table->dropUnique('jsas_no_jsa_unique');
        });
    }
};
