# AGENTS.md

Panduan singkat untuk agen/kontributor yang mengubah aplikasi Izin Kerja K3 (SIK) PT Semen Tonasa.
Detail lokasi file ada di [docs/CODEMAP.md](docs/CODEMAP.md); langkah kerja dan resep pengembangan ada di [docs/PANDUAN.md](docs/PANDUAN.md). Baca ketiganya sebelum mengubah alur kerja.

## Ringkasan aplikasi

- Stack: PHP 8.2+, Laravel 11, Blade + Alpine.js, Tailwind 3, Vite 5, Signature Pad, DomPDF. DB dev/test SQLite, produksi bisa MySQL (jaga portabilitas).
- Lima konteks akses:
  - `user` (vendor/kontraktor): 13 step lengkap.
  - `pgo` (karyawan internal): hanya `op_spk`, `jsa`, `working_permit`. PGO tidak boleh mengakses data kontraktor dan upload dokumen umum.
  - `admin` (reviewer K3, usertype `admin`, role `Admin`): klaim pengajuan dan review tiap step.
  - `Super Admin` (role): kelola user/role, tanda tangan akhir SIK.
  - Pemegang token (tanpa login): edit satu form kontraktor/JSA/permit dan lihat PDF-nya selama token belum expired.
- `Notification` adalah aggregate root satu pengajuan. Ia memiliki uploads, data kontraktor, JSA, record permit, `StepApproval`, dan `assigned_admin_id`.

## Step dan status (wajib konsisten di semua layer)

Kunci step vendor, berurutan: `op_spk`, `data_kontraktor`, `bpjs`, `jsa`, `working_permit`, `fakta_integritas`, `sertifikasi_ak3`, `ktp`, `surat_kesehatan`, `struktur_organisasi`, `post_test`, `bukti_serah_terima`, `sik`.

- Dashboard user menamai step terakhir `surat_izin_kerja`, tetapi kunci approval yang tersimpan adalah `sik`. Ini alias kompatibilitas; jangan tambah nama baru atau ganti di satu layer saja.
- Step berurutan: step berikutnya terbuka hanya jika `StepApproval` step sebelumnya `disetujui`. Form/file yang sudah dikirim bukan berarti disetujui.
- `StepApproval.status`: `menunggu`, `perlu_disetujui`, `disetujui`, `revisi`. UI user memetakan ke `pending`/`done`/`revisi`; jangan simpan label UI ke DB.
- `Notification.status`, `Upload.status`, `StepApproval.status` adalah konsep berbeda. Untuk progres workflow, `StepApproval` yang otoritatif.
- Total step dihitung dari daftar step per usertype (PGO = 3), bukan asumsi 13.
- Tanda tangan akhir disimpan di baris `StepApproval` step `sik` (`signature_senior_manager`, `signature_manager`) dan hanya boleh disimpan setelah status `sik` = `disetujui`.
- Admin: detail pengajuan yang belum ter-assign otomatis di-assign ke admin pertama yang membukanya. Hanya `assigned_admin_id` yang boleh ubah status atau hapus data pengajuan itu.

## Working permit

Kunci permit: `umum`, `gaspanas`, `air`, `ketinggian`, `pengangkatan`, `penggalian`, `risiko-panas`, `ruang-tertutup`, `beban`, `perancah`, `listrik-tegangan-tinggi`.

- Satu record permit per notifikasi per jenis. Sebagian berbagi `WorkPermitDetail` + `WorkPermitClosure` yang dibedakan `permit_type`; `pengangkatan` dan `listrik-tegangan-tinggi` tidak memakai detail/closure.
- Alias legacy yang harus dijaga: `ruang-tertutup` (URL user/token) vs `ruangtertutup` (nama route, `permit_type`, peta admin); `risiko-panas` (route) vs `panas-risiko` (alias form lazy dan dropdown "tambah lainnya"); controller risiko panas bernama `PanasRisikoPermitController`.
- `listrik-tegangan-tinggi` adalah permit khusus: `umum` harus sudah ada sebelum dibuat.
- Jangan asumsikan dua permit berperilaku sama. Field, JSON, tanda tangan, detail/closure, dan layout PDF-nya berbeda.
- Masa berlaku permit (`App\Services\PermitValidityService`, `config/permit_validity.php`, default 7 hari) berlaku untuk semua permit satu pengajuan. Vendor: jam mulai saat admin menyetujui `working_permit`, berlaku sampai `sik` disetujui. PGO: jam mulai saat permit pertama dibuat, kedaluwarsa bila belum disetujui. Pengajuan dengan `permit_expires_at` NULL (data lama) tidak pernah kedaluwarsa.
- Saat kedaluwarsa, step setelah `working_permit` hanya dikunci di tampilan (label `kedaluwarsa`/`terkunci`, tidak disimpan) dan dicek ulang di server: middleware `permit.active` pada route simpan/form token permit, `UploadController`, dan `updateStatus` admin. Pemohon memakai "Buat Permit Baru" (`working-permit.renew`): data + PDF diarsipkan ke `permit_archives` (disk `local`, khusus admin), tanda tangan dan tanggal dikosongkan, token diputar, `working_permit` kembali `menunggu`.
- Perubahan form permit belum selesai sampai Blade komponen modal, Blade token, validasi/penyimpanan controller, `$fillable`/`$casts` model, migration, preview, dan Blade PDF memakai nama field yang sama.

## Otorisasi dan token

- Baca/tulis user dan PGO wajib dibatasi ke `auth()->id()` lewat `findAccessibleNotification()` di base `Controller`.
- Aksi admin dan Super Admin tetap di balik middleware `auth`, `verified`, `usertype`, `role` yang sudah ada.
- Jangan pernah mengotorisasi dari hidden field, boolean request, `notification_id`, ID model, atau tombol yang di-disable. Jangan percaya `_token_access` dari klien; beberapa `store()` permit lama masih melakukannya, jangan tiru polanya.
- Request token harus: resolve record lewat UUID `token`, panggil `abortIfPermitTokenExpired()`, lalu turunkan `notification_id` dari record itu. Tidak boleh menerima ID notifikasi pengganti.
- PDF token memakai request attribute `token_pdf_access`; set hanya setelah token valid dan belum expired (lihat `TokenPdfController`).
- Regenerasi token hanya oleh owner/admin dan harus memutar `token` + `token_expires_at` (`regeneratePermitToken()`). Masa berlaku dari `config/permit_tokens.php` (`PERMIT_TOKEN_EXPIRES_IN_DAYS`, default 3 hari).
- Route token publik tetap di grup `throttle:30,1`. Token adalah kredensial bearer; jangan ditulis ke log.
- Setiap route yang menerima ID pengajuan (termasuk PDF) harus dicek IDOR. Route PDF SIK terautentikasi `izin-kerja.sik.pdf` mengarah ke `AdminPermintaanController::viewSik()` yang belum memeriksa kepemilikan non-admin; perbaiki dengan sengaja, bukan sambil lalu.

## Data, file, PDF

- Validasi sebelum simpan; gunakan Form Request bila form diubah besar.
- Gunakan transaksi saat satu aksi menulis permit + detail + closure + approval + file sebagai satu unit.
- `updateOrCreate()` menyatakan niat satu record per notifikasi, tetapi hanya `work_permit_listrik_tegangan_tinggi` yang punya unique index DB. Jangan andalkan untuk konkurensi tanpa migration baru.
- Skema berubah lewat migration baru; jangan edit migration yang sudah deploy. Hindari SQL vendor-spesifik.
- Field JSON harus array di model (`$casts`), hindari `json_encode()` ganda. Decoding legacy double-encoded hanya di tempat yang memang punya data lama (data kontraktor).
- `filterEmptyPermitValues()` sengaja hanya membuang `null`/`''`; `false`, `0`, dan array kosong bisa bermakna.
- Nomor JSA: `{seq 3 digit}/JSA/ST/{mmyyyy}`, max numerik bulan berjalan, unique global, `lockForUpdate` + 3 retry; jaga kompatibilitas SQLite.
- Upload dan tanda tangan di disk `public`. Path upload tanpa slash awal; path tanda tangan diawali `storage/`. Pakai `Storage`, bukan `file_put_contents()`.
- Tanda tangan base64 harus data URL PNG, didecode strict, dibatasi ukuran, divalidasi sebagai gambar. Pertahankan tanda tangan lama bila edit tidak mengirim pengganti.
- Saat replace/hapus record, hapus file fisiknya; cascade DB tidak menghapus file.
- Template DomPDF: HTML/CSS sederhana, path storage yang bekerja di luar sesi browser. Uji respons diawali `%PDF-` bila memungkinkan. Setelah setup baru jalankan `php artisan storage:link`.

## View dan frontend

- Dashboard utama: `resources/views/pengajuan-user/izin-kerja.blade.php` dan `izin-kerja-pgo.blade.php`. Modal step di `components/steps/`, form permit di `components/permits/`, form token dan PDF di `pengajuan-user/`.
- Form permit dimuat lazy lewat `components/steps/modal-working-permit-lazy.blade.php` + route `working-permit.form`; hanya `umum` yang di-include langsung.
- State Alpine lokal (`activeModal`, `selectedPermit`); nama field form harus sama persis dengan validasi controller.
- Jangan tambah query Eloquent di Blade; muat data di controller.
- Enforcement workflow tidak boleh hanya di UI (tombol disabled); ulangi di controller.
- Setelah ubah Blade/Tailwind, jalankan build produksi.

## Konvensi

- PSR-12, Laravel conventions, `vendor/bin/pint` untuk PHP yang disentuh.
- Controller tetap fokus; logika bersama permit masuk base `Controller` hanya jika semantiknya sama untuk semua permit, selain itu buat service/action.
- Definisi step dan peta permit saat ini terduplikasi (lihat CODEMAP "Definisi terduplikasi"). Ubah semua konsumennya atau sentralisasi dulu dengan tes regresi.
- Gunakan enum/konstanta untuk status/step baru. Pertahankan istilah Indonesia di UI.
- Jangan tampilkan pesan exception ke user; log tanpa token, tanda tangan, dokumen personal.
- Jangan ubah `vendor/`, `node_modules/`, `public/build/` secara manual.

## Verifikasi

Backend:

```bash
composer install
php artisan migrate:fresh --env=testing
php artisan test
vendor/bin/pint --test
```

View/CSS/JS: `npm ci && npm run build`.

Cakupan regresi minimum per jenis perubahan:

- otorisasi route/controller: aktor yang diizinkan dan ditolak;
- alur token: valid, expired, token lama setelah regenerate, record lain;
- workflow: kunci berurutan, revisi, approval, total user vs PGO;
- form permit: simpan terautentikasi, simpan via token, round-trip JSON, tanda tangan terjaga, respons PDF;
- aksi destruktif: pembatasan assigned admin, hapus DB, hapus file, reset approval;
- migration: `migrate:fresh` bersih di SQLite plus review dialek produksi.

Jika PHP atau Node tidak tersedia, sebutkan perintah mana yang tidak bisa dijalankan; jangan klaim verifikasi berhasil.

## Sharp edges yang diketahui

- `viewSik()` dipakai route user dan admin tanpa cek kepemilikan/status final untuk non-admin.
- Beberapa `store()` permit melewati cek kepemilikan saat `_token_access` truthy.
- Definisi step, peta permit, dan perhitungan progres terduplikasi dengan alias/urutan yang kadang berbeda (urutan `sik` vs `bukti_serah_terima` di `AdminDashboardController::index`).
- `JsaController::edit()` merujuk view `pengajuan-user.jsa.edit` yang tidak ada; edit JSA nyata lewat modal `modal-jsa-edit`.
- Cascade model tidak konsisten membersihkan file upload/tanda tangan.
- Model/tabel `WorkPermitLifeSaving`, `WorkPermitProsedurKhusus`, `LiftingPermit` dan komponen `permits/lifesaving`, `permits/procedures` tidak punya controller/route (legacy).
- `README.md` belum lengkap; gunakan file ini dan `docs/PANDUAN.md`.

Jangan perbaiki sharp edge yang tidak terkait dalam tugas sempit. Sebutkan bila relevan, jaga fokus perubahan, dan tambahkan tes di sekitar edge yang sengaja diubah.

## Checklist sebelum serah terima

1. Telusuri aktor yang terpengaruh dan jalur terautentikasi + token.
2. Pastikan nama step, permit, status, dan field kanonik di semua layer.
3. Cek ulang kepemilikan, middleware role, aturan assigned admin, expiry token.
4. Pastikan record DB dan file fisik konsisten saat create/update/delete.
5. Jalankan tes terfokus lalu suite penuh.
6. Jalankan Pint dan build frontend bila relevan.
7. Laporkan file yang berubah, perilaku, tes yang dijalankan, dan perintah yang terblokir lingkungan.
