# CODEMAP — Izin Kerja K3 (SIK)

Peta lokasi kode untuk navigasi cepat. Aturan dan invarian ada di [AGENTS.md](../AGENTS.md); resep pengembangan ada di [PANDUAN.md](PANDUAN.md).

## 1. Struktur direktori

```
app/
  Http/Controllers/
    Controller.php                 # base: ownership, token expiry, PDF access, tanda tangan
    Auth/*                         # Laravel Breeze (login redirect per usertype)
    ProfileController.php
    User/
      IzinKerjaController.php      # dashboard vendor (13 step); delegasi ke PGO jika isPgo()
      IzinKerjaPgoController.php   # dashboard PGO (3 step)
      UserNotificationController.php   # step 1: buat Notification (po/spk/notif) + notif admin
      DataKontraktorController.php     # step 2 + form token + PDF
      UploadController.php             # step 3,6-12: upload dokumen (PGO ditolak)
      JsaController.php                # step 4: JSA, nomor JSA, form token, PDF
      WorkingPermitFormController.php  # route working-permit.form: muat form permit secara lazy
      TokenLinkController.php          # regenerate token (owner/admin)
      TokenPdfController.php           # PDF publik via token -> set attribute token_pdf_access
      WorkingPermit/*PermitController.php  # 11 controller permit (store, showByToken, storeByToken, preview)
    Admin/
      AdminDashboardController.php     # dashboard admin (agregat via subquery), daftar vendor & PGO (satu query per halaman), aktivitas terbaru dari log
      AdminPermintaanController.php    # detail permintaan + riwayat, auto-assign, update status (dicatat), hapus data (reset dicatat), viewSik
      ApproveSikController.php         # daftar SIK + tanda tangan senior manager/manager
      AdminNotificationController.php  # tandai notifikasi admin terbaca
      RolePermissionController.php     # Super Admin: daftar/edit/hapus Notification (nama historis)
      UserPanelController.php          # Super Admin: CRUD user (password default 'password')
      NewsController.php               # Super Admin: Master Data > Upload Berita (CRUD berita landing page)
    LandingController.php              # route '/': landing page + 6 berita aktif terbaru
  Services/
    StepApprovalLogger.php           # satu-satunya penulis riwayat persetujuan (statusChanged, reset)
  Http/Middleware/
    UsertypeMiddleware.php    # alias 'usertype:a,b'
    CheckRole.php             # alias 'role:X'; Super Admin selalu lolos
    PreventBackHistory.php    # header no-cache, dipasang ke semua route web
  Models/                     # lihat bagian 4
  Providers/AppServiceProvider.php  # alias <x-admin-layout>, directive @carbon, defaultStringLength(191)
bootstrap/app.php             # registrasi middleware alias + handler TokenMismatchException (419 -> login)
config/permit_tokens.php      # expires_in_days (env PERMIT_TOKEN_EXPIRES_IN_DAYS, default 3)
database/
  migrations/                 # 29 migration, lihat bagian 4
  seeders/RoleSeeder.php      # Super Admin, Admin, Isolation Officer, Permit Requestor/Issuer/Authorizer/Receiver
  factories/UserFactory.php   # satu-satunya factory
resources/
  js/app.js                   # Alpine, window.SignaturePad, helper formJSA/isolasiData/pekerjaData/formKontraktor
  css/app.css                 # Tailwind entry
  views/                      # lihat bagian 5
routes/web.php                # semua route aplikasi; routes/auth.php = Breeze
tests/Feature/                # lihat bagian 7
```

## 2. Aktor dan alur login

| usertype | role (tabel `roles`) | Redirect login | Dashboard |
|---|---|---|---|
| `user` | biasanya null | `dashboard` | `IzinKerjaController@index` → `pengajuan-user.izin-kerja` |
| `pgo` | null | `dashboard` | didelegasikan ke `IzinKerjaPgoController@index` → `pengajuan-user.izin-kerja-pgo` |
| `admin` | `Admin` | `admin.dashboard` | `AdminDashboardController@index` → `admin.dashboard` |
| `admin` | `Super Admin` | `admin.dashboard` | sama + user panel, role permission, approve SIK |

Redirect ada di `app/Http/Controllers/Auth/AuthenticatedSessionController.php`. Helper: `User::isAdmin()`, `isUser()`, `isPgo()`.

## 3. Peta route (`routes/web.php`)

### Grup `auth, verified, usertype:user,admin,pgo`

| Nama route | Method + URI | Controller |
|---|---|---|
| `dashboard` | GET `pengajuan-user/izin-kerja` | `IzinKerjaController@index` |
| `working-permit.form` | GET `pengajuan-user/izin-kerja/{notification}/working-permit-form/{type}` | `WorkingPermitFormController@show` |
| `token-links.regenerate` | POST `pengajuan-user/token-link/{type}/{id}/regenerate` | `TokenLinkController@regenerate` |
| `notification.store` | POST `pengajuan-user/izin-kerja/notification` | `UserNotificationController@store` |
| `jsa.store` / `jsa.edit` / `jsa.update` | POST `user/jsa/store`, GET `user/jsa/{id}/edit`, PATCH `user/jsa/{id}` | `JsaController` |
| `jsa.pdf` / `jsa.pdf.view` | GET `user/jsa/pdf/{notification_id}`, `user/jsa/pdf/view/{notification_id}` | `JsaController@downloadPdf` / `showPdf` |
| `working-permit.{slug}.store` | POST (URI bervariasi, lihat file) | `{Permit}Controller@store` |
| `working-permit.{slug}.preview` | GET `.../preview/{id}` (`id` = notification_id) | `{Permit}Controller@preview` |
| `izin-kerja.sik.pdf` | GET `izin-kerja/sik/pdf/{id}` | `AdminPermintaanController@viewSik` (tanpa cek owner, lihat sharp edge) |

### Grup `auth, verified, usertype:user,admin` (tanpa PGO)

| Nama route | Method + URI | Controller |
|---|---|---|
| `izin-kerja.data-kontraktor` | POST `pengajuan-user/izin-kerja/data-kontraktor/{notification}` | `DataKontraktorController@store` |
| `izin-kerja.data-kontraktor.pdf` | GET `.../data-kontraktor/pdf/{notification_id}` | `DataKontraktorController@previewPdf` |
| `upload.store` / `upload.updateStatus` / `upload.delete` | POST `user/upload`, PATCH `user/upload/{id}/status`, DELETE `user/upload/{id}` | `UploadController` |

### Grup admin `auth, verified, usertype:admin`, prefix `admin`, nama `admin.`

| Nama route | URI | Controller |
|---|---|---|
| `admin.dashboard` | GET `/dashboard` | `AdminDashboardController@index` |
| `admin.permintaansik` / `admin.permintaansikpgo` | GET | `AdminDashboardController@permintaanSIK` / `permintaanSIKPgo` |
| `admin.permintaansik.show` | GET `/permintaansik/{id}` | `AdminPermintaanController@show` (auto-assign) |
| `admin.permintaansik.updateStatus` | POST `/permintaansik/{id}/step/{step}/status` | `updateStatus` |
| `admin.permintaansik.viewSik` | GET `/permintaansik/{id}/view-sik` | `viewSik` |
| `admin.permintaansik.deleteFile` | DELETE `/permintaansik/{id}/delete-file/{step}` | `deleteFile` |
| `admin.permintaansik.deleteDataKontraktor` / `deleteJsa` / `deleteWorkingPermit` | DELETE | masing-masing |
| `admin.notifications.read` / `readAll` | POST | `AdminNotificationController` |
| `permintaansik.downloadSik` (tanpa prefix nama) | GET `/permintaansik/{id}/download-sik` | `viewSik` |

### Grup Super Admin `auth, verified, usertype:admin, role:Super Admin`

| Nama route | Controller |
|---|---|
| `admin.userpanel.{index,create,store,edit,update,destroy}` | `UserPanelController` |
| `admin.role_permission.{index,edit,update,destroy}` (parameter `{notification}`) | `RolePermissionController` |
| `admin.news.{index,create,store,edit,update,destroy}` (URI `/admin/news`) | `NewsController` |
| `admin.approvesik.index` | `ApproveSikController@index` |
| `admin.approvesik.signature` (POST `/approvesik/{id}/ttd`) | `storeSignature` → `signature_senior_manager` |
| `admin.approvesik.signature_manager` (POST `/approvesik/{id}/ttd-manager`) | `storeManagerSignature` → `signature_manager` |

### Grup publik token `throttle:30,1` (tanpa auth)

| Nama route | URI | Controller |
|---|---|---|
| `izin-kerja.data-kontraktor.token` / `.store` | GET/POST `izin-kerja/data-kontraktor/{token}` | `DataKontraktorController@showByToken` / `storeByToken` |
| `jsa.form.token` / `jsa.form.token.store` | GET/POST `izin-kerja/jsa/form/{token}` | `JsaController` |
| `working-permit.{slug}.token` / `.token.store` | GET/POST `izin-kerja/working-permit/{key}/{token}` | `{Permit}Controller@showByToken` / `storeByToken` |
| `token-pdf.show` | GET `izin-kerja/token-pdf/{type}/{token}` | `TokenPdfController@show` |

`{type}` di `token-pdf.show` dan `token-links.regenerate`: `data-kontraktor`, `jsa`, dan 11 kunci permit (peta di masing-masing controller).

## 4. Model dan tabel

### Inti workflow

| Model | Tabel | Kolom penting | Relasi |
|---|---|---|---|
| `User` | `users` | `usertype` enum(admin,user,pgo), `jabatan`, `role_id` | `role()`, `handledNotifications()` |
| `Role` | `roles` | `name` unique | |
| `Notification` | `notifications` | `user_id`, `type` enum(po,spk,notif), `number`, `description`, `file`, `status` (default menunggu), `assigned_admin_id` | `uploads()`, `stepApprovals()`, `kontraktor()`, `umumWorkPermit()`, `workPermit*()`, `detail()`, `closure()`, `user()`, `assignedAdmin()`, `sikStep()`, `jsa()`, `stepApprovalLogs()`; scope `ofVendor`, `ofPgo`, `menunggu`, `belumDitangani`, `perluRevisi`, `selesai`, `mandek`, `kondisi`; hook `deleting` menghapus anak |
| `StepApproval` | `step_approvals` | `step` string, `status` enum(menunggu,perlu_disetujui,disetujui,revisi), `catatan`, `file_path`, `approved_by`, `signature_senior_manager`, `signature_manager` | `notification()`, `approvedBy()` |
| `StepApprovalLog` | `step_approval_logs` | `step`, `action` (status_changed, reset, backfill), `from_status`, `to_status`, `catatan`, `actor_id` (null = Sistem), `created_at` saja; baris hanya ditambah | `notification()`, `actor()`; `toHistoryRow()`, `statusLabel()`, accessor `action_label`, `actor_name` |
| `Upload` | `uploads` | `step` enum(bpjs,fakta_integritas,sertifikasi_ak3,ktp,surat_kesehatan,struktur_organisasi,post_test,bukti_serah_terima), `file_path`, `status` (default pending), `uploaded_at` | `notification()` |
| `News` | `news` | `title`, `description`, `link`, `image_path` (disk public, folder `news/`), `is_active` | scope `active()`, accessor `image_url`, `source_label` (domain dari link) |
| `AdminNotification` | `admin_notifications` | `recipient_id`, `notification_id`, `title`, `body`, `url`, `read_at` | `recipient()`, `notification()`, scope `unread()` |
| `DataKontraktor` | `data_kontraktors` | `nama_perusahaan` (required), tanggal, `tenaga_kerja`/`peralatan_kerja`/`apd` json, `ttd_manager`, `ttd_perusahaan`, `diverifikasi_signature`, `token`, `token_expires_at` | `notification()` |
| `Jsa` | `jsas` | `no_jsa` unique (`{seq}/JSA/ST/{mmyyyy}`), `langkah_kerja` json, 3 pasang nama+signature, `token`, `token_expires_at` | `notification()` |
| `WorkPermitDetail` | `work_permit_details` | `permit_type` enum(umum,ruangtertutup,penggalian,ketinggian,air,risiko-panas,gaspanas,beban,pengangkatan,perancah), lokasi, tanggal, deskripsi, alat, jumlah pekerja, kontak darurat | `notification()`, `closure()` |
| `WorkPermitClosure` | `work_permit_closures` | `work_permit_detail_id`, flag penutupan, `requestor_sign`, `issuer_sign`, `jumlah_rfid` | `detail()` |

### Permit (satu tabel per jenis)

Semua punya `notification_id`, `token` (unique), `token_expires_at`, kolom JSON yang di-cast `array`, kolom nama + `signature_*` + tanggal/jam per peran (requestor, issuer, authorizer, receiver, verificator, live testing tergantung jenis).

| Kunci | Model | Tabel | `permit_type` detail | Catatan |
|---|---|---|---|---|
| `umum` | `UmumWorkPermit` | `umum_work_permits` | `umum` | wajib ada sebelum `listrik-tegangan-tinggi` |
| `gaspanas` | `WorkPermitGasPanas` | `work_permit_gas_panas` | `gaspanas` | |
| `air` | `WorkPermitAir` | `work_permit_air` | `air` | |
| `ketinggian` | `WorkPermitKetinggian` | `work_permit_ketinggian` | `ketinggian` | |
| `pengangkatan` | `WorkPermitPengangkatan` | `work_permit_pengangkatan` | tidak dipakai (null di form controller) | |
| `penggalian` | `WorkPermitPenggalian` | `work_permit_penggalian` | `penggalian` | |
| `risiko-panas` | `WorkPermitRisikoPanas` | `work_permit_risiko_panas` | `risiko-panas` | controller `PanasRisikoPermitController`; alias `panas-risiko` |
| `ruang-tertutup` | `WorkPermitRuangTertutup` | `work_permit_ruang_tertutup` | `ruangtertutup` | nama route `working-permit.ruangtertutup.*`; `notification_id` tanpa FK |
| `beban` | `WorkPermitBeban` | `work_permit_beban` | `beban` | |
| `perancah` | `WorkPermitPerancah` | `work_permit_perancah` | `perancah` | `notification_id` tanpa FK |
| `listrik-tegangan-tinggi` | `WorkPermitListrikTeganganTinggi` | `work_permit_listrik_tegangan_tinggi` | tidak dipakai | satu-satunya dengan `notification_id` unique; token bertipe uuid |

Legacy tanpa controller/route: `WorkPermitLifeSaving` (`work_permit_life_saving`), `WorkPermitProsedurKhusus` (`work_permit_prosedur_khusus`), `LiftingPermit` (tanpa migration).

### Migration penting

- `2025_05_15_064417_create_step_approvals_table`, `2025_05_15_064556_add_assigned_admin_id_to_notifications_table`
- `2026_07_03_000001_add_token_expiration_to_token_forms` (menambah `token_expires_at` ke 12 tabel token)
- `2026_07_04_000001_add_status_to_uploads_table`, `2026_07_04_000002_add_unique_index_to_jsas_no_jsa`
- `2026_10_12_000001_create_work_permit_listrik_tegangan_tinggi_table` (contoh migration permit terbaru)

## 5. Peta view

### Layout

| File | Dipakai oleh |
|---|---|
| `layouts/app.blade.php` + `layouts/navigation.blade.php` | `<x-app-layout>` dashboard user/PGO, profil |
| `layouts/admin.blade.php` | `<x-admin-layout>` semua halaman admin (navbar + dropdown notifikasi admin) |
| `layouts/guest.blade.php` | auth Breeze |
| `layouts/sign.blade.php` | form token publik |

### Dashboard user/PGO (`resources/views/pengajuan-user/`)

- `izin-kerja.blade.php` (vendor, ~1100 baris) dan `izin-kerja-pgo.blade.php` (PGO).
- State Alpine di root: `{ expanded, activeModal, selectedPermit }`. Modal dibuka dengan `activeModal = 'modal-{code}'`; modal permit `modal-working_permit-{key}`; modal tambah permit `modal-tambah-lainnya`.
- Include: `components/steps/modal-op`, `modal-data-kontraktor`, `modal-jsa`, `modal-jsa-edit`, `modal-upload`, `modal-working-permit` (umum, langsung di-include), `modal-working-permit-{key}` (membungkus `modal-working-permit-lazy`), `modal-tambah-lainnya`, `components/token-link-action` (13x), `components/sign-pad`, `components/permit-mobile-style`.
- Data dari controller: `notifications`, `selectedId`, `notification`, `jsa`, `permits[key]`, `dataKontraktor`, `steps[]` (`title`, `code`, `status` pending/done/revisi, `enabled`), `stepApprovals`, `stepGateStatuses`, `stepNotes`, `uploadsByStep`, `generatedNoJsa`, `renderPermitFormsEagerly`.

### Komponen permit

| Kunci | Modal (`components/steps/`) | Form (`components/permits/`) | Form token (`pengajuan-user/workingpermit/`) | PDF (`pengajuan-user/workingpermit/`) |
|---|---|---|---|---|
| `umum` | `modal-working-permit` | `umum` | `formpermitumum` | `umumpdf` |
| `gaspanas` | `modal-working-permit-gaspanas` | `gaspanas` | `form-token-gaspanas` | `gaspanaspdf` |
| `air` | `modal-working-permit-air` | `air` | `form-token-air` | `airpdf` |
| `ketinggian` | `modal-working-permit-ketinggian` | `ketinggian` | `form-token-ketinggian` | `ketinggianpdf` |
| `pengangkatan` | `modal-working-permit-pengangkatan` | `pengangkatan` | `form-token-pengangkatan` | `pengangkatanpdf` |
| `penggalian` | `modal-working-permit-penggalian` | `penggalian` | `form-token-penggalian` | `penggalianpdf` |
| `risiko-panas` | `modal-working-permit-risiko-panas` | `risiko-panas` | `form-token-risiko-panas` | `risikopanaspdf` |
| `ruang-tertutup` | `modal-working-permit-ruang-tertutup` | `ruang-tertutup` | `form-token-ruang-tertutup` | `ruangtertutuppdf` |
| `beban` | `modal-working-permit-beban` | `beban` | `form-token-beban` | `bebanpdf` |
| `perancah` | `modal-working-permit-perancah` | `perancah` | `form-token-perancah` | `perancahpdf` |
| `listrik-tegangan-tinggi` | `modal-working-permit-listrik-tegangan-tinggi` | `listrik-tegangan-tinggi` | `form-token-listrik-tegangan-tinggi` | `listriktegangantinggipdf` |

Komponen bersama: `components/permits/form-signature.blade.php`, `components/sign-pad.blade.php` (modal canvas global `#signPadModal`), `components/token-link-action.blade.php` (props `record`, `routeName`, `regenerateType`, `compact`).

### Data kontraktor dan JSA

| Bagian | Modal | Form token | PDF |
|---|---|---|---|
| Data kontraktor | `components/steps/modal-data-kontraktor` | `pengajuan-user/kontraktor/form` (+ `sign-form`) | `pengajuan-user/kontraktor/pdfdatakontraktor` |
| JSA | `components/steps/modal-jsa`, `modal-jsa-edit` | `pengajuan-user/jsa/form` | `pengajuan-user/jsa/pdfjsa` |

### Admin (`resources/views/admin/`)

| View | Controller |
|---|---|
| `dashboard` | `AdminDashboardController@index` |
| `permintaansik`, `permintaansikpgo` | `AdminDashboardController@permintaanSIK` / `permintaanSIKPgo`; tab kondisi dari `partials/kondisi-tabs` |
| `detailpermintaan`, `detailpermintaanpgo` | `AdminPermintaanController@show` (dipilih berdasarkan usertype pemohon); ikon info per step + "Riwayat" di kanan atas membuka `partials/step-history-modal` lewat komponen `components/admin/history-button` |
| `approvesik` | `ApproveSikController@index` |
| `pdfsik` | `AdminPermintaanController@viewSik` (DomPDF) |
| `user-panel/{index,create,edit}` | `UserPanelController` |
| `role-permission/index` | `RolePermissionController` |

## 6. Definisi terduplikasi (ubah semuanya atau sentralisasi dulu)

| Definisi | Lokasi |
|---|---|
| Daftar 13 step vendor + judul | `IzinKerjaController@index` (2x, alias `surat_izin_kerja`), `AdminPermintaanController::stepTitles()`, `AdminDashboardController::VENDOR_STEP_TITLES` |
| Daftar 3 step PGO | `IzinKerjaPgoController@index`, `AdminPermintaanController::stepTitlesForUser()`, `AdminDashboardController::PGO_STEP_TITLES`, `ApproveSikController@index` |
| Peta kunci permit → model | `IzinKerjaController` + `IzinKerjaPgoController` (`$permits`), `WorkingPermitFormController::PERMITS`, `TokenLinkController::$types`, `TokenPdfController::$types`, `AdminPermintaanController::permitModels()` + `show()`, `AdminDashboardController::PERMIT_RELATIONS` (nama relasi di `Notification`), `ApproveSikController@index` |
| Peta kunci → `permit_type` detail | `IzinKerjaController` (`$detailTypeMap`), `WorkingPermitFormController::PERMITS`, relasi `detail()` tiap model permit |
| Status UI (pending/done/revisi) | `IzinKerjaController`, `IzinKerjaPgoController`, Blade dashboard |
| Folder tanda tangan | `saveSignature()` di tiap controller permit/JSA/kontraktor/ApproveSik |

## 7. Tes (`tests/Feature/`)

| File | Cakupan |
|---|---|
| `WorkingPermitFormTest` | route lazy form: owner OK, user lain 403, dashboard tanpa field permit, dashboard tanpa notifikasi (user & PGO) |
| `ListrikTeganganTinggiPermitTest` | prasyarat umum, simpan oleh owner, token update + PDF |
| `DataKontraktorTokenFormTest` | render/simpan JSON rows, legacy double-encoded, PDF token |
| `JsaNumberingTest` | algoritma nomor JSA, bulan berjalan, unique index |
| `JsaSignatureTest` | PNG lengkap vs terpotong di form token |
| `AdminDashboardTest` | angka dashboard dan kartu (vendor/PGO), antrean "Perlu Tindakan Saya", pengajuan mandek, filter `kondisi` di daftar, aktivitas tanpa backfill, batas bulan grafik, jumlah query tetap kecil saat data bertambah |
| `ApprovalHistoryTest` | log saat ubah status, tanpa log bila tidak berubah, admin lain ditolak, reset saat hapus JSA, riwayat tampil di detail, aktivitas dashboard, log ikut terhapus |
| `NewsTest` | CRUD berita Super Admin, admin biasa ditolak, landing menampilkan berita aktif |
| `Auth/*`, `ProfileTest`, `ExampleTest` | bawaan Breeze |

Pola setup: `User::factory()->create(['usertype' => 'user'])`, `Notification::create([...user_id...])`, `actingAs()`, `RefreshDatabase`, `Storage::fake('public')`. Konfigurasi di `phpunit.xml` (SQLite in-memory).

## 8. Alur data ringkas

```
Vendor login → dashboard (IzinKerjaController)
  step 1  notification.store        → notifications + admin_notifications ke semua admin
  step 2  izin-kerja.data-kontraktor → data_kontraktors (+ token link ke pihak penandatangan)
  step 3  upload.store (bpjs)        → uploads
  step 4  jsa.store                  → jsas (no_jsa otomatis, token)
  step 5  working-permit.{key}.store → tabel permit (+ work_permit_details/closures) (token)
  step 6-12 upload.store             → uploads
Admin  admin.permintaansik.show      → auto-assign; updateStatus → step_approvals (disetujui/revisi)
Super Admin approvesik.signature(+manager) → step_approvals.sik.signature_* (hanya jika sik disetujui)
Vendor  izin-kerja.sik.pdf           → admin.pdfsik (step 13 'surat_izin_kerja' = done bila signature_senior_manager ada)
Pihak luar  {type}/{token}           → form token → storeByToken → token-pdf.show
```
