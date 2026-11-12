<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('step_approval_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_id')->constrained()->cascadeOnDelete();
            $table->string('step', 50);
            // status_changed: admin mengubah status; reset: data/file dihapus sehingga step kembali menunggu;
            // backfill: baris awal dari data lama saat tabel ini dibuat.
            $table->string('action', 30);
            $table->string('from_status', 30)->nullable();
            $table->string('to_status', 30);
            $table->text('catatan')->nullable();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->nullable();

            $table->index(['notification_id', 'step']);
            $table->index('created_at');
        });

        // Pengajuan lama belum punya riwayat: isi satu baris awal per step dari status yang ada
        // agar modal riwayat tidak kosong. Waktu memakai updated_at approval (perubahan terakhir).
        DB::table('step_approvals')->orderBy('id')->chunk(500, function ($rows) {
            $logs = [];

            foreach ($rows as $row) {
                $logs[] = [
                    'notification_id' => $row->notification_id,
                    'step' => $row->step,
                    'action' => 'backfill',
                    'from_status' => null,
                    'to_status' => $row->status ?: 'menunggu',
                    'catatan' => $row->catatan,
                    'actor_id' => $row->approved_by,
                    'created_at' => $row->updated_at ?? $row->created_at ?? now(),
                ];
            }

            if ($logs) {
                DB::table('step_approval_logs')->insert($logs);
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('step_approval_logs');
    }
};
