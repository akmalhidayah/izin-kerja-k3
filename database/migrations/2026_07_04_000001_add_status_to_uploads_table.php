<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('uploads') || Schema::hasColumn('uploads', 'status')) {
            return;
        }

        Schema::table('uploads', function (Blueprint $table) {
            $table->string('status', 20)->default('pending')->after('file_path');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('uploads') || !Schema::hasColumn('uploads', 'status')) {
            return;
        }

        Schema::table('uploads', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
