<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = [
        'data_kontraktors',
        'jsas',
        'umum_work_permits',
        'work_permit_gas_panas',
        'work_permit_air',
        'work_permit_ketinggian',
        'work_permit_pengangkatan',
        'work_permit_penggalian',
        'work_permit_beban',
        'work_permit_risiko_panas',
        'work_permit_ruang_tertutup',
        'work_permit_perancah',
    ];

    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            if (!Schema::hasTable($tableName) || Schema::hasColumn($tableName, 'token_expires_at')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) {
                $table->timestamp('token_expires_at')->nullable()->after('token');
            });

            DB::table($tableName)
                ->whereNotNull('token')
                ->whereNull('token_expires_at')
                ->update(['token_expires_at' => now()->addDays(3)]);
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            if (!Schema::hasTable($tableName) || !Schema::hasColumn($tableName, 'token_expires_at')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn('token_expires_at');
            });
        }
    }
};
