<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

use App\Models\Upload;
use App\Models\Notification;
use App\Models\UmumWorkPermit;
use App\Models\WorkPermitGasPanas;
use App\Models\WorkPermitAir;
use App\Models\WorkPermitKetinggian;
use App\Models\WorkPermitRisikoPanas;
use App\Models\WorkPermitRuangTertutup;
use App\Models\WorkPermitPerancah;
use App\Models\WorkPermitBeban;
use App\Models\WorkPermitPenggalian;
use App\Models\WorkPermitPengangkatan;

use App\Models\StepApproval;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminPermintaanController extends Controller
{
    /**
     * Judul step (13 step, SIK final).
     * NOTE: status di StepApproval kamu simpan lowercase: disetujui|revisi|menunggu
     */
    private function stepTitles(): array
    {
        return [
            'op_spk'               => 'Buat Notifikasi/OP/SPK',
            'data_kontraktor'     => 'Input Data Kontraktor',
            'bpjs'                => 'Upload BPJS',
            'jsa'                 => 'Input JSA',
            'working_permit'      => 'Input Working Permit',
            'fakta_integritas'    => 'Upload Fakta Integritas',
            'sertifikasi_ak3'     => 'Upload Sertifikasi AK3',
            'ktp'                 => 'Upload KTP Pekerja',
            'surat_kesehatan'     => 'Upload Surat Kesehatan',
            'struktur_organisasi' => 'Upload Struktur Organisasi',
            'post_test'           => 'Upload Post Test',
            'bukti_serah_terima'  => 'Upload Bukti Serah Terima',
            'sik'                 => 'Surat Izin Kerja',
        ];
    }

    private function stepTitlesForUser(?string $usertype): array
    {
        if ($usertype === 'pgo') {
            return [
                'op_spk' => 'Buat Notifikasi/OP/SPK',
                'jsa' => 'Input JSA',
                'working_permit' => 'Input Working Permit',
            ];
        }

        return $this->stepTitles();
    }

    private function assertValidStep(string $step): void
    {
        if (!array_key_exists($step, $this->stepTitles())) {
            Log::warning('[ADMIN][STEP INVALID]', [
                'step' => $step,
                'admin_id' => auth()->id(),
            ]);
            abort(404);
        }
    }

    private function ensureAssignedAdmin(Notification $notification)
    {
        $adminId = auth()->id();

        if (!$notification->assigned_admin_id) {
            $notification->update(['assigned_admin_id' => $adminId]);
            $notification->refresh();

            Log::info('[ADMIN][ASSIGN] auto assign admin', [
                'notification_id' => $notification->id,
                'assigned_admin_id' => $notification->assigned_admin_id,
            ]);

            return null;
        }

        if ((int)$notification->assigned_admin_id !== (int)$adminId) {
            Log::warning('[ADMIN][DELETE] forbidden action by non-assigned admin', [
                'notification_id' => $notification->id,
                'assigned_admin_id' => $notification->assigned_admin_id,
                'admin_id' => $adminId,
            ]);

            return redirect()->back()->with('error', 'Hanya admin yang pertama kali menangani yang dapat mengubah data!');
        }

        return null;
    }

    private function deleteStoredPath(?string $path): void
    {
        if (!$path || !is_string($path)) {
            return;
        }

        $relativePath = str_starts_with($path, 'storage/')
            ? substr($path, strlen('storage/'))
            : $path;

        Storage::disk('public')->delete($relativePath);
    }

    private function deleteModelFiles(object $model): void
    {
        foreach ($model->getAttributes() as $value) {
            if (is_string($value) && str_starts_with($value, 'storage/')) {
                $this->deleteStoredPath($value);
            }
        }
    }

    private function permitModels(): array
    {
        $models = [
            'umum' => UmumWorkPermit::class,
            'gaspanas' => WorkPermitGasPanas::class,
            'air' => WorkPermitAir::class,
            'ketinggian' => WorkPermitKetinggian::class,
            'risiko-panas' => WorkPermitRisikoPanas::class,
            'ruangtertutup' => WorkPermitRuangTertutup::class,
            'perancah' => WorkPermitPerancah::class,
        ];

        if (class_exists(WorkPermitBeban::class)) {
            $models['beban'] = WorkPermitBeban::class;
        }
        if (class_exists(WorkPermitPenggalian::class)) {
            $models['penggalian'] = WorkPermitPenggalian::class;
        }
        if (class_exists(WorkPermitPengangkatan::class)) {
            $models['pengangkatan'] = WorkPermitPengangkatan::class;
        }

        return $models;
    }

    private function hasAnyPermit(int $notificationId, array $models = null): bool
    {
        $models = $models ?? $this->permitModels();

        foreach ($models as $model) {
            if ($model::where('notification_id', $notificationId)->exists()) {
                return true;
            }
        }

        return false;
    }

    public function show($id)
    {
        Log::info('[ADMIN][SHOW] open detail permintaan', [
            'notification_id' => $id,
            'admin_id' => auth()->id(),
        ]);

        $notification = Notification::with(['user', 'assignedAdmin'])->findOrFail($id);

        // ✅ Fallback file OP/SPK jika Notification.file kosong
        if (!$notification->file) {
            $fallbackUpload = Upload::where('notification_id', $id)
                ->where('step', 'op_spk')
                ->latest()
                ->first();

            if ($fallbackUpload?->file_path) {
                $notification->file = $fallbackUpload->file_path;
            }
        }

        /**
         * ✅ Auto-assign admin (atomik, hindari race)
         * logic tetap sama: kalau belum assigned, assign ke admin pertama yang membuka detail.
         */
        if (!$notification->assigned_admin_id) {
            Notification::where('id', $id)
                ->whereNull('assigned_admin_id')
                ->update(['assigned_admin_id' => auth()->id()]);

            $notification->refresh();

            Log::info('[ADMIN][ASSIGN] auto assign admin', [
                'notification_id' => $id,
                'assigned_admin_id' => $notification->assigned_admin_id,
            ]);
        }

        // ✅ Alert pakai ID (bukan nama)
        $showAlert = $notification->assigned_admin_id
            && $notification->assigned_admin_id !== auth()->id();

        $assignedAdmin = $notification->assignedAdmin?->name;

        // Data tambahan
        $dataKontraktor = \App\Models\DataKontraktor::where('notification_id', $id)->first();
        $jsa = \App\Models\Jsa::where('notification_id', $id)->first();

        // Permit types (samakan dengan user, kalau model tersedia)
        $permitTypes = [
            'umum' => UmumWorkPermit::class,
            'gaspanas' => WorkPermitGasPanas::class,
            'air' => WorkPermitAir::class,
            'ketinggian' => WorkPermitKetinggian::class,
            'risiko-panas' => WorkPermitRisikoPanas::class,
            'ruangtertutup' => WorkPermitRuangTertutup::class,
            'perancah' => WorkPermitPerancah::class,
        ];

        // Tambahan (kalau modelnya beneran ada di project kamu)
        if (class_exists(WorkPermitBeban::class)) {
            $permitTypes['beban'] = WorkPermitBeban::class;
        }
        if (class_exists(WorkPermitPenggalian::class)) {
            $permitTypes['penggalian'] = WorkPermitPenggalian::class;
        }
        if (class_exists(WorkPermitPengangkatan::class)) {
            $permitTypes['pengangkatan'] = WorkPermitPengangkatan::class;
        }

        $permits = [];
        foreach ($permitTypes as $key => $model) {
            $permits[$key] = $model::where('notification_id', $id)->first();
        }

        /**
         * ✅ Uploads: groupBy step untuk multi-file (bpjs/ktp)
         * Tapi tetap sediakan "single" upload (last) agar blade lama tidak pecah.
         */
        $uploadsByStep = Upload::where('notification_id', $id)
            ->orderBy('created_at')
            ->get()
            ->groupBy('step');

        $uploadsSingle = $uploadsByStep->map(fn($c) => $c->last());

        $approvals = StepApproval::where('notification_id', $id)
            ->get()
            ->keyBy('step');

        // Inject file dari approvals kalau upload kosong (kompatibilitas legacy)
        foreach ($approvals as $key => $approval) {
            if (!isset($uploadsSingle[$key]) && $approval->file_path) {
                $uploadsSingle[$key] = (object)['file_path' => $approval->file_path];
            }
        }

        $stepTitles = $this->stepTitlesForUser($notification->user?->usertype);

        $isPgo = ($notification->user?->usertype ?? null) === 'pgo';
        $hasOpSpk = (bool)($notification->number || $notification->file);
        $hasJsa = (bool)$jsa;
        $hasPermit = collect($permits)->filter()->isNotEmpty();

        // Bangun step data
        $stepData = [];
        foreach ($stepTitles as $step => $title) {
            $status = strtolower($approvals[$step]->status ?? 'menunggu');
            if ($isPgo) {
                $approvalStatus = strtolower($approvals[$step]->status ?? '');
                $hasData = match ($step) {
                    'op_spk' => $hasOpSpk,
                    'jsa' => $hasJsa,
                    'working_permit' => $hasPermit,
                    default => false,
                };

                if ($approvalStatus === 'revisi') {
                    $status = 'revisi';
                } elseif ($approvalStatus === 'disetujui' || $hasData) {
                    $status = 'disetujui';
                } else {
                    $status = 'menunggu';
                }
            }
            $upload = $uploadsSingle[$step] ?? null;

            // OP/SPK spesial: bawa info number/type/date + fallback file
            if ($step === 'op_spk') {
                $fileUpload = Upload::where('notification_id', $id)
                    ->where('step', 'op_spk')
                    ->latest()
                    ->first();

                $upload = (object)[
                    'file_path' => $fileUpload?->file_path ?? $notification->file ?? null,
                    'number' => $notification->number ?? null,
                    'type' => $notification->type ?? null,
                    'created_at' => $notification->created_at ?? null,
                ];
            }

            $statusClass = match ($status) {
                'disetujui' => 'bg-green-500 text-white',
                'revisi'    => 'bg-yellow-500 text-white',
                default     => 'bg-gray-300 text-black',
            };

            $stepData[] = [
                'step' => $step,
                'title' => $title,
                'status' => $status,
                'upload' => $upload,
                'status_class' => $statusClass,
                'catatan' => $approvals[$step]->catatan ?? '',
            ];
        }

        // Ringkasan step (lebih akurat: hitung berapa yang disetujui)
        $approvedCount = collect($stepData)->where('status', 'disetujui')->count();
        $totalSteps = count($stepTitles);
        $stepSummary = $approvedCount >= $totalSteps
            ? 'Selesai'
            : 'Step ' . ($approvedCount + 1) . ' - ' . array_values($stepTitles)[$approvedCount];

        $data = (object)[
            'id' => $notification->id,
            'user_name' => $notification->user->name ?? '-',
            'user_jabatan' => $notification->user->jabatan ?? '-',
            'tanggal' => $notification->created_at?->format('d-m-Y H:i') ?? '-',
            'handled_by' => $notification->assignedAdmin?->name ?? '-',
            'status' => $notification->status ?? 'menunggu',
            'usertype' => $notification->user?->usertype ?? null,
            'notification_file' => $notification->file ?? null,
            'notification_id' => $notification->id,
            'description' => $notification->description ?? '-',
            'step_data' => $stepData,
            'step_summary' => $stepSummary,
            'data_kontraktor' => $dataKontraktor,
            'jsa' => $jsa,
            'permits' => $permits,

            // optional: kirim uploadsByStep untuk dipakai blade jika mau rapihin query di view
            'uploads_by_step' => $uploadsByStep,
        ];

        // admin list (tetap seperti sebelumnya)
        $admins = User::where('usertype', 'admin')->pluck('name');

        $detailView = ($notification->user?->usertype ?? null) === 'pgo'
            ? 'admin.detailpermintaanpgo'
            : 'admin.detailpermintaan';

        return response()
            ->view($detailView, compact('data', 'admins', 'showAlert', 'assignedAdmin'))
            ->setStatusCode(200);
    }

    public function updateStatus(Request $request, $id, $step)
    {
        $this->assertValidStep($step);

        $request->validate([
            'status' => 'required|in:disetujui,revisi,menunggu',
            'catatan' => 'nullable|string|max:500',
        ]);

        $adminId = auth()->id();
        $notification = Notification::with(['user', 'assignedAdmin'])->findOrFail($id);

        // logic sama: admin pertama yang menangani = yang boleh update
        if (!$notification->assigned_admin_id) {
            $notification->update(['assigned_admin_id' => $adminId]);
            $notification->refresh();

            Log::info('[ADMIN][ASSIGN][UPDATE] assign on updateStatus', [
                'notification_id' => $id,
                'admin_id' => $adminId,
            ]);
        } elseif ((int)$notification->assigned_admin_id !== (int)$adminId) {
            Log::warning('[ADMIN][UPDATE] forbidden update by non-assigned admin', [
                'notification_id' => $id,
                'step' => $step,
                'assigned_admin_id' => $notification->assigned_admin_id,
                'admin_id' => $adminId,
            ]);

            return redirect()->back()->with('error', 'Hanya admin yang pertama kali menangani yang dapat mengubah status!');
        }

        StepApproval::updateOrCreate(
            ['notification_id' => $id, 'step' => $step],
            [
                'status' => strtolower($request->status),
                'catatan' => $request->catatan,
                'approved_by' => $adminId,
            ]
        );

        Log::info('[ADMIN][UPDATE] step status updated', [
            'notification_id' => $id,
            'step' => $step,
            'status' => $request->status,
            'admin_id' => $adminId,
        ]);

        return redirect()->back()->with('success', 'Status langkah berhasil diperbarui');
    }

    /**
     * Hapus file upload.
     * ✅ Support multi-file via file_id (opsional), fallback step-first biar blade lama tetap jalan.
     * ✅ Reset approval hanya jika file step sudah habis.
     */
    public function deleteFile(Request $request, $id, $step)
    {
        $this->assertValidStep($step);

        Log::warning('[ADMIN][DELETE FILE] request', [
            'notification_id' => $id,
            'step' => $step,
            'file_id' => $request->file_id ?? null,
            'admin_id' => auth()->id(),
        ]);

        // Prioritas file_id
        if ($request->filled('file_id')) {
            $upload = Upload::where('id', $request->file_id)
                ->where('notification_id', $id)
                ->where('step', $step)
                ->first();
        } else {
            // fallback legacy
            $upload = Upload::where('notification_id', $id)
                ->where('step', $step)
                ->first();
        }

        if ($upload) {
            try {
                Storage::disk('public')->delete($upload->file_path);
            } catch (\Throwable $e) {
                Log::error('[ADMIN][DELETE FILE] storage delete error', [
                    'upload_id' => $upload->id,
                    'file_path' => $upload->file_path,
                    'err' => $e->getMessage(),
                ]);
            }

            $upload->delete();

            Log::info('[ADMIN][DELETE FILE] deleted', [
                'upload_id' => $upload->id,
                'notification_id' => $id,
                'step' => $step,
            ]);
        } else {
            Log::warning('[ADMIN][DELETE FILE] upload not found', [
                'notification_id' => $id,
                'step' => $step,
                'file_id' => $request->file_id ?? null,
            ]);
        }

        // Reset approval hanya jika sudah tidak ada file tersisa pada step tsb
        $hasRemaining = Upload::where('notification_id', $id)
            ->where('step', $step)
            ->exists();

        if (!$hasRemaining) {
            StepApproval::where('notification_id', $id)
                ->where('step', $step)
                ->update([
                    'file_path' => null,
                    'status' => 'menunggu',
                ]);

            Log::info('[ADMIN][DELETE FILE] approval reset (no remaining files)', [
                'notification_id' => $id,
                'step' => $step,
            ]);
        }

        return redirect()->back()->with('success', 'File berhasil dihapus.');
    }

    /**
     * Stream PDF SIK
     * (Tidak mengubah logic utama: tetap stream PDF dari view)
     */
    public function viewSik($id)
    {
        Log::info('[ADMIN][VIEW SIK] request', [
            'notification_id' => $id,
            'user_id' => auth()->id(),
            'usertype' => auth()->user()->usertype ?? null,
        ]);

        $notification = Notification::with(['user', 'assignedAdmin'])->findOrFail($id);
        $dataKontraktor = \App\Models\DataKontraktor::where('notification_id', $notification->id)->first();

        $sikStep = StepApproval::where('notification_id', $notification->id)
            ->where('step', 'sik')
            ->first();

        // ✅ Optional safety (tanpa ubah alur admin):
        // Kalau route ini juga dipakai user, sebaiknya cek ownership & status.
        // Kalau kamu ingin STRICT sesuai saran sebelumnya, aktifkan blok ini.
        /*
        if ((auth()->user()->usertype ?? null) !== 'admin') {
            if ($notification->user_id !== auth()->id()) abort(403);
            if (($sikStep?->status ?? 'menunggu') !== 'disetujui') abort(403);
        }
        */

        $pdf = Pdf::loadView('admin.pdfsik', compact('notification', 'dataKontraktor', 'sikStep'));

        return response($pdf->stream('Surat-Izin-Kerja.pdf'), 200, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function deleteDataKontraktor($id)
    {
        $notification = Notification::findOrFail($id);
        if ($response = $this->ensureAssignedAdmin($notification)) {
            return $response;
        }

        $dataKontraktor = \App\Models\DataKontraktor::where('notification_id', $id)->first();
        if (!$dataKontraktor) {
            return redirect()->back()->with('error', 'Data kontraktor tidak ditemukan.');
        }

        $this->deleteModelFiles($dataKontraktor);
        $dataKontraktor->delete();

        StepApproval::where('notification_id', $id)
            ->where('step', 'data_kontraktor')
            ->update([
                'status' => 'menunggu',
                'catatan' => null,
            ]);

        return redirect()->back()->with('success', 'Data kontraktor berhasil dihapus.');
    }

    public function deleteJsa($id)
    {
        $notification = Notification::findOrFail($id);
        if ($response = $this->ensureAssignedAdmin($notification)) {
            return $response;
        }

        $jsa = \App\Models\Jsa::where('notification_id', $id)->first();
        if (!$jsa) {
            return redirect()->back()->with('error', 'Data JSA tidak ditemukan.');
        }

        $this->deleteModelFiles($jsa);
        $jsa->delete();

        StepApproval::where('notification_id', $id)
            ->where('step', 'jsa')
            ->update([
                'status' => 'menunggu',
                'catatan' => null,
            ]);

        return redirect()->back()->with('success', 'Data JSA berhasil dihapus.');
    }

    public function deleteWorkingPermit($id, $type)
    {
        $notification = Notification::findOrFail($id);
        if ($response = $this->ensureAssignedAdmin($notification)) {
            return $response;
        }

        $models = $this->permitModels();
        if (!array_key_exists($type, $models)) {
            abort(404);
        }

        $model = $models[$type];
        $permit = $model::where('notification_id', $id)->first();

        if (!$permit) {
            return redirect()->back()->with('error', 'Data working permit tidak ditemukan.');
        }

        $this->deleteModelFiles($permit);

        if (method_exists($permit, 'closure')) {
            $permit->closure()->delete();
        }
        if (method_exists($permit, 'detail')) {
            $permit->detail()->delete();
        }

        $permit->delete();

        if (!$this->hasAnyPermit($id, $models)) {
            StepApproval::where('notification_id', $id)
                ->where('step', 'working_permit')
                ->update([
                    'status' => 'menunggu',
                    'catatan' => null,
                ]);
        }

        return redirect()->back()->with('success', 'Data working permit berhasil dihapus.');
    }
}
