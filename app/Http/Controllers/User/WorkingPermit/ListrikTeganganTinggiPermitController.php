<?php

namespace App\Http\Controllers\User\WorkingPermit;

use App\Http\Controllers\Controller;
use App\Models\UmumWorkPermit;
use App\Models\WorkPermitListrikTeganganTinggi;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ListrikTeganganTinggiPermitController extends Controller
{
    private const DOCUMENTATION_KEYS = [
        'method_statement',
        'high_hazard_checklist',
        'supporting_documents',
        'hazards_documented_in_lst',
    ];

    private const SAFETY_KEYS = [
        'electrical_worker_certificate',
        'measurement_calibration_certificate',
        'fire_detection_protection',
        'portable_fire_extinguisher',
        'insulated_tools',
        'non_conductive_ppe',
        'non_conductive_ladder',
        'warning_signs',
        'electrical_energy_isolation',
        'gfci_elcb',
        'nearby_workers_notified',
    ];

    private const ISSUER_CONFIRMATION_KEYS = [
        'area_and_equipment_checked',
        'hazard_control_checklist_reviewed',
        'hazards_discussed_with_supervisor',
        'high_risk_hazards_emphasized',
        'work_declared_safe',
    ];

    public function store(Request $request)
    {
        $validated = $request->validate([
            'notification_id' => ['required', 'integer', 'exists:notifications,id'],
            'permit_number' => ['nullable', 'string', 'max:100'],
            'work_location' => ['nullable', 'string', 'max:255'],
            'work_date' => ['nullable', 'date'],
            'equipment_machine' => ['nullable', 'string'],
            'permit_issuer_name' => ['nullable', 'string', 'max:255'],
            'permit_issuer_phone' => ['nullable', 'string', 'max:50'],
            'job_scope' => ['nullable', 'string'],
            'worker_count' => ['nullable', 'integer', 'min:0'],
            'tools_equipment' => ['nullable', 'string'],
            'other_permit_status' => ['nullable', 'in:ya,na'],
            'other_permit_details' => ['nullable', 'string'],
            'documentation_checks' => ['nullable', 'array'],
            'documentation_checks.*' => ['nullable', 'in:ya,na'],
            'supporting_document' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'safety_requirements' => ['nullable', 'array'],
            'safety_requirements.*' => ['nullable', 'array'],
            'safety_requirements.*.needed' => ['nullable', 'in:ya,tidak'],
            'safety_requirements.*.fulfilled' => ['nullable', 'in:ya,tidak'],
            'energy_isolation_certificate' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'additional_hazards' => ['nullable', 'array'],
            'additional_hazards.*.hazard' => ['nullable', 'string'],
            'additional_hazards.*.control' => ['nullable', 'string'],
            'additional_hazards.*.pic' => ['nullable', 'string', 'max:255'],
            'issuer_confirmations' => ['nullable', 'array'],
            'issuer_confirmations.*' => ['nullable', 'in:ya'],
            'handover_issuer_name' => ['nullable', 'string', 'max:255'],
            'handover_issuer_signature' => ['nullable', 'string'],
            'handover_issuer_date' => ['nullable', 'date'],
            'handover_issuer_time' => ['nullable', 'date_format:H:i'],
            'receiver_confirmation' => ['nullable', 'in:ya'],
            'receiver_name' => ['nullable', 'string', 'max:255'],
            'receiver_signature' => ['nullable', 'string'],
            'receiver_date' => ['nullable', 'date'],
            'receiver_time' => ['nullable', 'date_format:H:i'],
            'additional_k3_instructions' => ['nullable', 'string'],
            'clear_all_signatures' => ['nullable', 'boolean'],
        ]);

        if (!$request->boolean('_token_access') && !$this->findAccessibleNotification($validated['notification_id'])) {
            abort(403, 'Akses ditolak.');
        }

        if (!UmumWorkPermit::where('notification_id', $validated['notification_id'])->exists()) {
            return back()
                ->withInput()
                ->with('error', 'Permit Umum harus diisi terlebih dahulu sebelum membuat Permit Listrik Tegangan Tinggi.');
        }

        $existing = WorkPermitListrikTeganganTinggi::where('notification_id', $validated['notification_id'])->first();

        $data = $this->filterEmptyPermitValues([
            'notification_id' => (int) $validated['notification_id'],
            'permit_number' => $validated['permit_number'] ?? null,
            'work_location' => $validated['work_location'] ?? null,
            'work_date' => $validated['work_date'] ?? null,
            'equipment_machine' => $validated['equipment_machine'] ?? null,
            'permit_issuer_name' => $validated['permit_issuer_name'] ?? null,
            'permit_issuer_phone' => $validated['permit_issuer_phone'] ?? null,
            'job_scope' => $validated['job_scope'] ?? null,
            'worker_count' => $validated['worker_count'] ?? null,
            'tools_equipment' => $validated['tools_equipment'] ?? null,
            'other_permit_status' => $validated['other_permit_status'] ?? null,
            'other_permit_details' => $validated['other_permit_details'] ?? null,
            'documentation_checks' => $this->onlyAllowedValues(
                $request->input('documentation_checks', []),
                self::DOCUMENTATION_KEYS,
                ['ya', 'na']
            ),
            'safety_requirements' => $this->normalizeSafetyRequirements(
                $request->input('safety_requirements', [])
            ),
            'additional_hazards' => $this->normalizeHazards($request->input('additional_hazards', [])),
            'issuer_confirmations' => $this->onlyAllowedValues(
                $request->input('issuer_confirmations', []),
                self::ISSUER_CONFIRMATION_KEYS,
                ['ya']
            ),
            'handover_issuer_name' => $validated['handover_issuer_name'] ?? null,
            'handover_issuer_date' => $validated['handover_issuer_date'] ?? null,
            'handover_issuer_time' => $validated['handover_issuer_time'] ?? null,
            'receiver_confirmation' => $validated['receiver_confirmation'] ?? null,
            'receiver_name' => $validated['receiver_name'] ?? null,
            'receiver_date' => $validated['receiver_date'] ?? null,
            'receiver_time' => $validated['receiver_time'] ?? null,
            'additional_k3_instructions' => $validated['additional_k3_instructions'] ?? null,
        ]);

        $data['handover_issuer_signature'] = $this->saveSignature(
            $request->input('handover_issuer_signature'),
            'permit_issuer'
        ) ?? $existing?->handover_issuer_signature;
        $data['receiver_signature'] = $this->saveSignature(
            $request->input('receiver_signature'),
            'permit_receiver'
        ) ?? $existing?->receiver_signature;

        $this->storeReplacementFile($request, $data, $existing, 'supporting_document', 'working-permit/listrik-tegangan-tinggi/documents');
        $this->storeReplacementFile($request, $data, $existing, 'energy_isolation_certificate', 'working-permit/listrik-tegangan-tinggi/isolation');

        if ($request->boolean('clear_all_signatures')) {
            $data['handover_issuer_signature'] = null;
            $data['receiver_signature'] = null;
        }

        $permit = WorkPermitListrikTeganganTinggi::updateOrCreate(
            ['notification_id' => $validated['notification_id']],
            $data
        );

        $this->ensurePermitToken($permit);

        return back()->with('success', 'Data Permit Listrik Tegangan Tinggi berhasil disimpan!');
    }

    public function showByToken(string $token)
    {
        $permit = WorkPermitListrikTeganganTinggi::with('notification')
            ->where('token', $token)
            ->firstOrFail();
        $this->abortIfPermitTokenExpired($permit);

        return view('pengajuan-user.workingpermit.form-token-listrik-tegangan-tinggi', [
            'permit' => $permit,
            'notification' => $permit->notification,
        ]);
    }

    public function storeByToken(Request $request, string $token)
    {
        $permit = WorkPermitListrikTeganganTinggi::where('token', $token)->firstOrFail();
        $this->abortIfPermitTokenExpired($permit);

        $request->merge([
            'notification_id' => $permit->notification_id,
            '_token_access' => true,
        ]);

        $response = app()->call([$this, 'store'], ['request' => $request]);

        return $this->tokenStoreResponse(
            $response,
            'Data berhasil disimpan melalui link token!',
            route('token-pdf.show', ['type' => 'listrik-tegangan-tinggi', 'token' => $permit->token])
        );
    }

    public function preview(int $id)
    {
        if (!$this->tokenPdfAccessAllowed()) {
            $this->abortUnlessCanAccessNotification($id);
        }

        $permit = WorkPermitListrikTeganganTinggi::where('notification_id', $id)->firstOrFail();

        return Pdf::loadView('pengajuan-user.workingpermit.listriktegangantinggipdf', compact('permit'))
            ->setPaper('A4')
            ->stream('izin-kerja-listrik-tegangan-tinggi.pdf');
    }

    private function onlyAllowedValues(array $input, array $keys, array $allowedValues): array
    {
        $result = [];

        foreach ($keys as $key) {
            $value = $input[$key] ?? null;
            if (in_array($value, $allowedValues, true)) {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    private function normalizeSafetyRequirements(array $input): array
    {
        $result = [];

        foreach (self::SAFETY_KEYS as $key) {
            $needed = $input[$key]['needed'] ?? null;
            $fulfilled = $input[$key]['fulfilled'] ?? null;

            if (!in_array($needed, ['ya', 'tidak'], true)) {
                $needed = null;
            }
            if (!in_array($fulfilled, ['ya', 'tidak'], true)) {
                $fulfilled = null;
            }

            if ($needed !== null || $fulfilled !== null) {
                $result[$key] = compact('needed', 'fulfilled');
            }
        }

        return $result;
    }

    private function normalizeHazards(array $rows): array
    {
        return collect($rows)
            ->map(fn ($row) => [
                'hazard' => trim((string) ($row['hazard'] ?? '')),
                'control' => trim((string) ($row['control'] ?? '')),
                'pic' => trim((string) ($row['pic'] ?? '')),
            ])
            ->filter(fn ($row) => $row['hazard'] !== '' || $row['control'] !== '' || $row['pic'] !== '')
            ->values()
            ->all();
    }

    private function saveSignature(?string $base64, string $role): ?string
    {
        return $this->saveBase64PngSignature(
            $base64,
            $role,
            'signatures/working-permit/listrik-tegangan-tinggi/'
        );
    }

    private function storeReplacementFile(
        Request $request,
        array &$data,
        ?WorkPermitListrikTeganganTinggi $existing,
        string $field,
        string $folder
    ): void {
        $existingPath = $existing ? $existing->{$field} : null;

        if (!$request->hasFile($field)) {
            if ($existingPath) {
                $data[$field] = $existingPath;
            }

            return;
        }

        $path = $request->file($field)->store($folder, 'public');
        $data[$field] = 'storage/'.$path;

        if ($existingPath) {
            $oldPath = str_starts_with($existingPath, 'storage/')
                ? substr($existingPath, strlen('storage/'))
                : $existingPath;
            Storage::disk('public')->delete($oldPath);
        }
    }
}
