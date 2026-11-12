<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\StepApproval;
use App\Models\StepApprovalLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    /** Pengajuan "mandek" bila menunggu tanpa perubahan approval selama ini (hari). */
    public const STALE_DAYS = 7;

    /**
     * Relasi permit di Notification yang dipakai untuk mendeteksi "sudah ada permit".
     * Satu kali subquery EXISTS per relasi, bukan satu query per baris.
     */
    private const PERMIT_RELATIONS = [
        'umumWorkPermit', 'workPermitGasPanas', 'workPermitAir', 'workPermitKetinggian',
        'workPermitPengangkatan', 'workPermitPenggalian', 'workPermitPerancah', 'workPermitRisikoPanas',
        'workPermitRuangTertutup', 'workPermitBeban', 'workPermitListrikTeganganTinggi',
    ];

    private const VENDOR_STEP_TITLES = [
        'op_spk' => 'Buat Notifikasi/OP/SPK',
        'data_kontraktor' => 'Input Data Kontraktor',
        'bpjs' => 'Upload BPJS',
        'jsa' => 'Input JSA',
        'working_permit' => 'Input Working Permit',
        'fakta_integritas' => 'Upload Fakta Integritas',
        'sertifikasi_ak3' => 'Upload Sertifikasi AK3',
        'ktp' => 'Upload KTP Personil K3',
        'surat_kesehatan' => 'Upload Surat Kesehatan',
        'struktur_organisasi' => 'Upload Struktur Organisasi',
        'post_test' => 'Upload Post Test',
        'bukti_serah_terima' => 'Upload Bukti Serah Terima',
        'sik' => 'Surat Penyelesaian Pekerjaan',
    ];

    private const PGO_STEP_TITLES = [
        'op_spk' => 'Buat Notifikasi/OP/SPK',
        'jsa' => 'Input JSA',
        'working_permit' => 'Input Working Permit',
    ];

    private const BULAN = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    public function index(Request $request)
    {
        $totalSteps = count(self::VENDOR_STEP_TITLES);
        $adminId = (int) auth()->id();

        // Satu query: ringkasan per pengajuan dihitung DB lewat subquery, tanpa memuat baris approval.
        $notifications = Notification::query()
            ->select(['id', 'user_id', 'assigned_admin_id', 'created_at'])
            ->with(['user:id,name,usertype', 'assignedAdmin:id,name'])
            ->withCount([
                'stepApprovals as approved_steps' => fn ($q) => $q->where('status', 'disetujui'),
                'stepApprovals as revisi_steps' => fn ($q) => $q->where('status', 'revisi'),
            ])
            ->withExists('sikStep as is_sik_approved')
            ->latest()
            ->get();

        $summaryRequests = $notifications->map(function (Notification $notif) use ($totalSteps) {
            $isSikApproved = (bool) $notif->is_sik_approved;

            $status = match (true) {
                $notif->revisi_steps > 0 => 'Perlu Revisi',
                $isSikApproved => 'Terbit SIK',
                default => 'Menunggu',
            };

            return (object) [
                'id' => $notif->id,
                'user_name' => $notif->user->name ?? 'User',
                'usertype' => $notif->user->usertype ?? 'user',
                'handled_by' => $notif->assignedAdmin?->name ?? '-',
                'status' => $status,
                'progress' => round(((int) $notif->approved_steps / $totalSteps) * 100),
                'created_at' => $notif->created_at,
                'is_sik_approved' => $isSikApproved,
            ];
        });

        $grouped = $summaryRequests
            ->groupBy(fn ($item) => $item->user_name.'|'.$item->usertype)
            ->map(function ($items) {
                $first = $items->first();

                return (object) [
                    'name' => $first->user_name,
                    'usertype' => $first->usertype,
                    'total_requests' => $items->count(),
                    'avg_progress' => round($items->avg('progress') ?? 0),
                ];
            });

        $topVendorPgo = $grouped
            ->filter(fn ($item) => $item->usertype === 'pgo')
            ->sortByDesc('total_requests')
            ->take(10)
            ->values();

        $topVendorUser = $grouped
            ->filter(fn ($item) => $item->usertype === 'user')
            ->sortByDesc('total_requests')
            ->take(10)
            ->values();

        if ($topVendorPgo->isEmpty()) {
            $topVendorPgo = $grouped->sortByDesc('total_requests')->take(10)->values();
        }
        if ($topVendorUser->isEmpty()) {
            $topVendorUser = $grouped->sortByDesc('total_requests')->take(5)->values();
        }

        // ===== Kartu ringkas: angka yang bisa ditindak, dipisah vendor / karyawan =====
        $startOfMonth = now()->startOfMonth();
        $startOfLastMonth = now()->subMonthNoOverflow()->startOfMonth();
        $staleSince = now()->subDays(self::STALE_DAYS);

        $split = fn (Builder $query) => [
            'vendor' => (clone $query)->ofVendor()->count(),
            'pgo' => (clone $query)->ofPgo()->count(),
        ];

        $menunggu = $split(Notification::query()->menunggu());
        $belumDitangani = $split(Notification::query()->belumDitangani());
        $revisi = $split(Notification::query()->perluRevisi());
        $mandek = $split(Notification::query()->mandek(self::STALE_DAYS));

        $menungguBaruBulanIni = Notification::query()->menunggu()->where('created_at', '>=', $startOfMonth)->count();
        $belumDitanganiTertua = Notification::query()->belumDitangani()->min('created_at');
        $revisiLama = Notification::query()
            ->whereHas('stepApprovals', fn ($a) => $a->where('status', 'revisi')->where('updated_at', '<', $staleSince))
            ->count();

        // Selesai: sik disetujui (vendor) / working_permit disetujui (PGO), dihitung dari waktu approval.
        $finishedBetween = function ($from, $to): array {
            $sik = StepApproval::query()
                ->where('step', 'sik')->where('status', 'disetujui')
                ->whereBetween('updated_at', [$from, $to])
                ->count();
            $permit = StepApproval::query()
                ->where('step', 'working_permit')->where('status', 'disetujui')
                ->whereBetween('updated_at', [$from, $to])
                ->whereHas('notification', fn ($n) => $n->ofPgo())
                ->count();

            return ['vendor' => $sik, 'pgo' => $permit];
        };
        $selesaiBulanIni = $finishedBetween($startOfMonth, now());
        $selesaiBulanLalu = $finishedBetween($startOfLastMonth, $startOfMonth->copy()->subSecond());

        $cards = [
            'menunggu' => [
                'total' => $menunggu['vendor'] + $menunggu['pgo'],
                'vendor' => $menunggu['vendor'],
                'pgo' => $menunggu['pgo'],
                'note' => $menungguBaruBulanIni.' masuk bulan ini',
            ],
            'belum_ditangani' => [
                'total' => $belumDitangani['vendor'] + $belumDitangani['pgo'],
                'vendor' => $belumDitangani['vendor'],
                'pgo' => $belumDitangani['pgo'],
                'note' => $belumDitanganiTertua
                    ? 'Paling lama '.(int) floor(\Carbon\Carbon::parse($belumDitanganiTertua)->diffInDays(now())).' hari'
                    : 'Semua sudah ada penanggung jawab',
            ],
            'revisi' => [
                'total' => $revisi['vendor'] + $revisi['pgo'],
                'vendor' => $revisi['vendor'],
                'pgo' => $revisi['pgo'],
                'note' => $revisiLama.' lebih dari '.self::STALE_DAYS.' hari',
            ],
            'selesai' => [
                'total' => $selesaiBulanIni['vendor'] + $selesaiBulanIni['pgo'],
                'vendor' => $selesaiBulanIni['vendor'],
                'pgo' => $selesaiBulanIni['pgo'],
                'note' => $this->deltaNote(
                    $selesaiBulanIni['vendor'] + $selesaiBulanIni['pgo'],
                    $selesaiBulanLalu['vendor'] + $selesaiBulanLalu['pgo']
                ),
            ],
        ];

        $stale = [
            'total' => $mandek['vendor'] + $mandek['pgo'],
            'vendor' => $mandek['vendor'],
            'pgo' => $mandek['pgo'],
            'days' => self::STALE_DAYS,
        ];

        // ===== Perlu tindakan saya: milik admin ini atau belum ada penanggung jawab, paling lama dulu =====
        $myQueue = Notification::query()
            ->select(['id', 'user_id', 'assigned_admin_id', 'number', 'created_at'])
            ->with('user:id,name,usertype')
            ->menunggu()
            ->where(fn ($q) => $q->where('assigned_admin_id', $adminId)->orWhereNull('assigned_admin_id'))
            ->withCount(['stepApprovals as approved_steps' => fn ($q) => $q->where('status', 'disetujui')])
            ->withMax('stepApprovals as last_activity_at', 'updated_at')
            ->orderByRaw('COALESCE(last_activity_at, created_at) asc')
            ->limit(3)
            ->get()
            ->map(function (Notification $notif) {
                $isPgo = ($notif->user->usertype ?? 'user') === 'pgo';
                $titles = $isPgo ? self::PGO_STEP_TITLES : self::VENDOR_STEP_TITLES;
                $keys = array_keys($titles);
                $approved = (int) $notif->approved_steps;
                $lastActivity = $notif->last_activity_at ? \Carbon\Carbon::parse($notif->last_activity_at) : $notif->created_at;

                return (object) [
                    'id' => $notif->id,
                    'user_name' => $notif->user->name ?? 'User',
                    'usertype' => $isPgo ? 'pgo' : 'user',
                    'number' => $notif->number,
                    'current_step' => min($approved + 1, count($keys)),
                    'current_step_title' => $titles[$keys[$approved] ?? end($keys)] ?? '-',
                    'total_steps' => count($keys),
                    'waiting_days' => (int) floor($lastActivity->diffInDays(now())),
                    'is_unassigned' => $notif->assigned_admin_id === null,
                ];
            });

        $myQueueTotal = Notification::query()
            ->menunggu()
            ->where(fn ($q) => $q->where('assigned_admin_id', $adminId)->orWhereNull('assigned_admin_id'))
            ->count();

        // ===== Aktivitas terbaru: perubahan status nyata, baris isian awal migrasi disembunyikan =====
        $stepTitles = self::VENDOR_STEP_TITLES;
        $recentActivities = StepApprovalLog::query()
            ->with(['actor:id,name', 'notification:id,user_id', 'notification.user:id,name,usertype'])
            ->where('action', '!=', StepApprovalLog::ACTION_BACKFILL)
            ->orderByDesc('id')
            ->limit(6)
            ->get()
            ->map(function (StepApprovalLog $log) use ($stepTitles) {
                $row = $log->toHistoryRow($stepTitles);
                $row['notification_id'] = $log->notification_id;
                $row['user_name'] = $log->notification?->user?->name ?? 'User';
                $row['usertype'] = $log->notification?->user?->usertype ?? 'user';
                $row['created_at'] = $log->created_at;

                return (object) $row;
            });

        // ===== Tren bulanan. Ekstraksi tahun/bulan berbeda antara MySQL dan SQLite (tes) =====
        [$yearExpr, $monthExpr] = $this->dateParts();
        $currentYear = (int) $request->input('year', now()->year);

        $years = Notification::query()
            ->selectRaw("{$yearExpr} as year")
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->map(fn ($y) => (int) $y);

        $monthlyRaw = Notification::query()
            ->selectRaw("{$monthExpr} as bulan, COUNT(*) as total")
            ->whereRaw("{$yearExpr} = ?", [$currentYear])
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        $monthlyChart = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyChart[] = (int) ($monthlyRaw[$i] ?? 0);
        }

        // Bulan terakhir yang digambar: bulan berjalan untuk tahun ini, 12 untuk tahun lampau, 0 untuk tahun depan.
        $lastMonth = match (true) {
            $currentYear < (int) now()->year => 12,
            $currentYear > (int) now()->year => 0,
            default => (int) now()->month,
        };

        return view('admin.dashboard', [
            'summaryRequests' => $summaryRequests,
            'totalSteps' => $totalSteps,
            'topVendorPgo' => $topVendorPgo,
            'topVendorUser' => $topVendorUser,
            'cards' => $cards,
            'stale' => $stale,
            'myQueue' => $myQueue,
            'myQueueTotal' => $myQueueTotal,
            'recentActivities' => $recentActivities,
            'monthlyChart' => $monthlyChart,
            'lastMonth' => $lastMonth,
            'years' => $years,
            'selectedYear' => $currentYear,
        ]);
    }

    public function permintaanSIK(Request $request)
    {
        $base = Notification::query()->ofVendor();

        $query = (clone $base);
        $this->applyListFilters($query, $request);

        $notifications = $this->withRowSummaries($query)->latest()->paginate(10)->withQueryString();
        $totalSteps = count(self::VENDOR_STEP_TITLES);

        $notifications->setCollection(
            $notifications->getCollection()->map(fn (Notification $n) => $this->summarizeRow($n, self::VENDOR_STEP_TITLES))
        );

        return view('admin.permintaansik', [
            'requests' => $notifications,
            'notifications' => $notifications,
            'adminList' => User::whereHas('handledNotifications')->pluck('name', 'id'),
            'bulanList' => self::BULAN,
            'tahunList' => range(date('Y'), date('Y') - 5),
            'totalSteps' => $totalSteps,
            'kondisiTabs' => $this->kondisiTabs($base, $request->input('kondisi')),
            'kondisiSub' => $this->kondisiSub($base, $request->input('kondisi')),
        ]);
    }

    public function permintaanSIKPgo(Request $request)
    {
        $base = Notification::query()->ofPgo();

        $query = (clone $base);
        $this->applyListFilters($query, $request);

        if ($request->filled('status')) {
            $status = strtolower($request->status);

            $query->whereHas('stepApprovals', function ($q) use ($status) {
                if ($status === 'selesai') {
                    $q->where('step', 'working_permit')->where('status', 'disetujui');
                } elseif ($status === 'disetujui') {
                    $q->where('status', 'disetujui');
                } elseif ($status === 'perlu disetujui') {
                    $q->where('status', 'perlu_disetujui');
                } elseif ($status === 'perlu revisi') {
                    $q->where('status', 'revisi');
                } elseif ($status === 'menunggu') {
                    $q->where('status', 'menunggu');
                }
            });
        }

        $notifications = $this->withRowSummaries($query)->latest()->paginate(10)->withQueryString();
        $totalSteps = count(self::PGO_STEP_TITLES);

        $notifications->setCollection(
            $notifications->getCollection()->map(fn (Notification $n) => $this->summarizeRow($n, self::PGO_STEP_TITLES))
        );

        return view('admin.permintaansikpgo', [
            'requests' => $notifications,
            'notifications' => $notifications,
            'adminList' => User::whereHas('handledNotifications')->pluck('name', 'id'),
            'bulanList' => self::BULAN,
            'tahunList' => range(date('Y'), date('Y') - 5),
            'totalSteps' => $totalSteps,
            'kondisiTabs' => $this->kondisiTabs($base, $request->input('kondisi')),
            'kondisiSub' => $this->kondisiSub($base, $request->input('kondisi')),
        ]);
    }

    /** Filter pencarian, admin, kondisi, dan rentang bulan yang sama untuk daftar vendor dan PGO. */
    private function applyListFilters(Builder $query, Request $request): void
    {
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('number', 'like', '%'.$search.'%')
                    ->orWhere('type', 'like', '%'.$search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%')
                    ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', '%'.$search.'%'));
            });
        }

        if ($request->filled('admin')) {
            $query->whereHas('assignedAdmin', fn ($q) => $q->where('name', 'like', '%'.$request->admin.'%'));
        }

        if ($request->filled('kondisi') && in_array($request->kondisi, Notification::KONDISI, true)) {
            $query->kondisi($request->kondisi, self::STALE_DAYS);
        }

        if ($request->filled('bulan_dari') && $request->filled('tahun_dari') &&
            $request->filled('bulan_sampai') && $request->filled('tahun_sampai')) {
            $from = \Carbon\Carbon::createFromDate($request->tahun_dari, $request->bulan_dari, 1)->startOfMonth();
            $to = \Carbon\Carbon::createFromDate($request->tahun_sampai, $request->bulan_sampai, 1)->endOfMonth();
            $query->whereBetween('created_at', [$from, $to]);
        }
    }

    /** Kelompok kondisi yang termasuk tab "Perlu Tindakan". */
    private const TINDAKAN_GROUP = ['perlu_tindakan', 'menunggu', 'belum_ditangani', 'mandek'];

    /**
     * Empat tab di atas daftar beserta jumlahnya (hanya dibatasi jenis pemohon, bukan filter lain):
     * Semua, Perlu Tindakan, Perlu Revisi, Riwayat. Nilai lama (menunggu, belum_ditangani, mandek, selesai)
     * tetap dikenali supaya tautan dari dashboard menyorot tab yang tepat.
     */
    private function kondisiTabs(Builder $base, ?string $active): array
    {
        $active = $active ?? '';

        $defs = [
            ['key' => '', 'label' => 'Semua', 'members' => ['']],
            ['key' => 'perlu_tindakan', 'label' => 'Perlu Tindakan', 'members' => self::TINDAKAN_GROUP],
            ['key' => 'revisi', 'label' => 'Perlu Revisi', 'members' => ['revisi']],
            ['key' => 'riwayat', 'label' => 'Riwayat', 'members' => ['riwayat', 'selesai']],
        ];

        $tabs = [];
        foreach ($defs as $def) {
            $tabs[] = [
                'key' => $def['key'],
                'label' => $def['label'],
                'count' => (clone $base)->kondisi($def['key'] ?: null, self::STALE_DAYS)->count(),
                'active' => in_array($active, $def['members'], true) || ($def['key'] === '' && ! in_array($active, Notification::KONDISI, true)),
            ];
        }

        return $tabs;
    }

    /**
     * Penyaring kecil yang hanya tampil di tab Perlu Tindakan: Semua, Belum ditangani, Mandek.
     * Kosong bila tab lain yang aktif.
     */
    private function kondisiSub(Builder $base, ?string $active): array
    {
        $active = $active ?? '';

        if (! in_array($active, self::TINDAKAN_GROUP, true)) {
            return [];
        }

        $current = in_array($active, ['belum_ditangani', 'mandek'], true) ? $active : 'perlu_tindakan';

        $defs = [
            'perlu_tindakan' => 'Semua',
            'belum_ditangani' => 'Belum ditangani',
            'mandek' => 'Mandek > '.self::STALE_DAYS.' hari',
        ];

        $sub = [];
        foreach ($defs as $key => $label) {
            $sub[] = [
                'key' => $key,
                'label' => $label,
                'count' => (clone $base)->kondisi($key, self::STALE_DAYS)->count(),
                'active' => $current === $key,
            ];
        }

        return $sub;
    }

    /**
     * Muat semua yang dibutuhkan satu baris daftar dalam satu query:
     * user, admin, status approval, dan flag EXISTS untuk JSA + tiap permit.
     */
    private function withRowSummaries(Builder $query): Builder
    {
        $exists = ['jsa as has_jsa'];
        foreach (self::PERMIT_RELATIONS as $relation) {
            $exists[] = "{$relation} as has_{$relation}";
        }

        return $query
            ->with([
                'user:id,name,jabatan,usertype',
                'assignedAdmin:id,name',
                'stepApprovals:id,notification_id,step,status',
            ])
            ->withExists($exists);
    }

    private function summarizeRow(Notification $notif, array $stepTitles): object
    {
        $stepKeys = array_keys($stepTitles);
        $totalSteps = count($stepTitles);

        $approvalMap = $notif->stepApprovals->pluck('status', 'step');

        $hasOpSpk = (bool) ($notif->number || $notif->file);
        $hasJsa = (bool) $notif->has_jsa;
        $hasPermit = false;
        foreach (self::PERMIT_RELATIONS as $relation) {
            if ($notif->{"has_{$relation}"}) {
                $hasPermit = true;
                break;
            }
        }

        $stepStatus = [];
        foreach ($stepKeys as $stepKey) {
            $approvalStatus = strtolower($approvalMap[$stepKey] ?? '');
            $hasData = match ($stepKey) {
                'op_spk' => $hasOpSpk,
                'jsa' => $hasJsa,
                'working_permit' => $hasPermit,
                default => false,
            };

            if ($approvalStatus === 'revisi') {
                $stepStatus[$stepKey] = 'revisi';
            } elseif ($approvalStatus === 'disetujui') {
                $stepStatus[$stepKey] = 'disetujui';
            } elseif ($approvalStatus === 'perlu_disetujui' || $hasData) {
                $stepStatus[$stepKey] = 'perlu_disetujui';
            } else {
                $stepStatus[$stepKey] = 'menunggu';
            }
        }

        $approvedSteps = count(array_filter($stepStatus, fn ($value) => $value === 'disetujui'));
        $currentStep = min($approvedSteps + 1, $totalSteps);
        $currentStepKey = $stepKeys[$approvedSteps] ?? null;
        $stepTitle = $stepTitles[$currentStepKey] ?? 'Belum Diketahui';

        $hasRevisi = in_array('revisi', $stepStatus, true);
        $hasNeedApproval = in_array('perlu_disetujui', $stepStatus, true);
        $currentStatus = match (true) {
            $hasRevisi => 'Perlu Revisi',
            $approvedSteps >= $totalSteps => 'Selesai',
            $hasNeedApproval => 'Perlu Disetujui',
            $approvedSteps > 0 => 'Disetujui',
            default => 'Menunggu',
        };

        return (object) [
            'id' => $notif->id,
            'user_name' => $notif->user->name ?? '-',
            'user_jabatan' => $notif->user->jabatan ?? '-',
            'number' => $notif->number,
            'description' => $notif->description ?? null,
            'file' => $notif->file,
            'tanggal' => $notif->created_at->format('d-m-Y H:i'),
            'handled_by' => $notif->assignedAdmin?->name ?? '-',
            'status' => $currentStatus,
            'progress' => round(($approvedSteps / $totalSteps) * 100),
            'current_step' => $currentStep,
            'current_step_title' => $stepTitle,
            'approved_steps' => $approvedSteps,
            'is_completed' => $approvedSteps >= $totalSteps,
        ];
    }

    private function deltaNote(int $current, int $previous): string
    {
        $delta = $current - $previous;

        return match (true) {
            $delta > 0 => '+'.$delta.' dari bulan lalu',
            $delta < 0 => $delta.' dari bulan lalu',
            default => 'Sama dengan bulan lalu',
        };
    }

    /** Ekspresi SQL tahun dan bulan dari created_at, sesuai driver DB. */
    private function dateParts(): array
    {
        return match (DB::connection()->getDriverName()) {
            'sqlite' => ["CAST(strftime('%Y', created_at) AS INTEGER)", "CAST(strftime('%m', created_at) AS INTEGER)"],
            'pgsql' => ['EXTRACT(YEAR FROM created_at)::int', 'EXTRACT(MONTH FROM created_at)::int'],
            default => ['YEAR(created_at)', 'MONTH(created_at)'],
        };
    }
}
