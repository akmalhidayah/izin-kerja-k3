# PANDUAN PENGEMBANGAN — Izin Kerja K3 (SIK)

Panduan praktis untuk menyiapkan lingkungan, memahami alur, dan mengerjakan jenis perubahan yang paling sering terjadi. Aturan yang wajib dipatuhi ada di [AGENTS.md](../AGENTS.md); lokasi file ada di [CODEMAP.md](CODEMAP.md).

## 1. Setup lokal

Prasyarat: PHP 8.2+, Composer, Node 18+, npm.

```bash
git clone https://github.com/akmalhidayah/izin-kerja-k3.git
cd izin-kerja-k3
cp .env.example .env
composer install
php artisan key:generate
touch database/database.sqlite          # DB_CONNECTION=sqlite sudah default di .env.example
php artisan migrate --seed              # seed roles: Super Admin, Admin, Permit Requestor, dst.
php artisan storage:link                # wajib agar upload/tanda tangan tampil di browser
npm ci
npm run dev                             # atau npm run build untuk aset produksi
php artisan serve
```

Variabel env khusus aplikasi:

| Variabel | Default | Fungsi |
|---|---|---|
| `PERMIT_TOKEN_EXPIRES_IN_DAYS` | 3 | masa berlaku link token (data kontraktor, JSA, permit) |
| `DB_CONNECTION` | sqlite | produksi dapat memakai mysql; jaga portabilitas query |

### Membuat akun awal

Belum ada seeder user. Daftar lewat `/register` (usertype default `user`), lalu ubah via tinker:

```bash
php artisan tinker
>>> $u = App\Models\User::first();
>>> $u->usertype = 'admin'; $u->role_id = App\Models\Role::where('name','Super Admin')->value('id'); $u->save();
```

Setelah ada Super Admin, buat user lain lewat menu User Panel (`/admin/userpanel`, password default `password`). Email harus terverifikasi (`verified`); untuk lokal set `email_verified_at` lewat tinker atau gunakan `MAIL_MAILER=log` dan buka link dari `storage/logs/laravel.log`.

## 2. Perintah harian

| Tujuan | Perintah |
|---|---|
| Semua tes | `php artisan test` |
| Tes terfokus | `php artisan test --filter=ListrikTeganganTinggiPermitTest` |
| Reset DB testing | `php artisan migrate:fresh --env=testing` |
| Format PHP | `vendor/bin/pint` (cek saja: `vendor/bin/pint --test`) |
| Build aset | `npm run build` |
| Daftar route | `php artisan route:list --except-vendor` |
| Bersihkan view cache setelah ganti Blade besar | `php artisan view:clear` |

Urutan verifikasi sebelum commit: tes terfokus → suite penuh → Pint → build aset (jika menyentuh Blade/CSS/JS).

## 3. Memahami satu pengajuan dari ujung ke ujung

1. Vendor membuat Notifikasi/OP/SPK (`UserNotificationController@store`). Tipe `spk` mendapat nomor otomatis `SPK-YmdHis`. Semua admin menerima `AdminNotification`.
2. Vendor mengisi step berikutnya hanya jika step sebelumnya `disetujui` di `step_approvals`. Dashboard (`IzinKerjaController@index`) menghitung `enabled` per step dari approval, bukan dari keberadaan data.
3. Data kontraktor, JSA, dan setiap permit mendapat `token` + `token_expires_at` saat dibuat (`ensurePermitToken()`). Link token dibagikan ke pihak penandatangan lewat komponen `token-link-action`.
4. Admin membuka detail (`AdminPermintaanController@show`). Jika belum ada `assigned_admin_id`, admin itu otomatis menjadi penanggung jawab. Admin lain hanya bisa melihat.
5. Admin menyetujui/merevisi per step (`updateStatus`). Status `revisi` + `catatan` tampil di dashboard vendor.
6. Setelah step `sik` `disetujui`, Super Admin menandatangani di Approve SIK. Tanda tangan tersimpan di baris `StepApproval` step `sik`.
7. Vendor melihat step 13 `done` saat `signature_senior_manager` terisi dan dapat membuka PDF SIK.

Alur PGO sama tetapi hanya step `op_spk`, `jsa`, `working_permit`, dengan dashboard dan halaman admin tersendiri (`izin-kerja-pgo`, `permintaansikpgo`, `detailpermintaanpgo`).

## 4. Pola yang dipakai di seluruh kode

### Otorisasi di controller

```php
// user/pgo: hanya notifikasi milik sendiri; admin: semua
$notification = $this->findAccessibleNotification($request->notification_id);
if (!$notification) return back()->with('error', 'Notifikasi tidak valid.');

// untuk preview PDF (terautentikasi atau via token)
if (!$this->tokenPdfAccessAllowed()) {
    $this->abortUnlessCanAccessNotification($id);
}
```

### Form token (publik)

```php
public function showByToken(string $token)
{
    $permit = Model::where('token', $token)->firstOrFail();
    $this->abortIfPermitTokenExpired($permit);
    return view('pengajuan-user.workingpermit.form-token-xxx', ['permit' => $permit, 'notification' => $permit->notification]);
}

public function storeByToken(Request $request, string $token)
{
    $permit = Model::where('token', $token)->firstOrFail();
    $this->abortIfPermitTokenExpired($permit);
    // notification_id selalu dari record, bukan dari request
    ...
    return $this->tokenStoreResponse($response, 'Data berhasil disimpan melalui link token!',
        route('token-pdf.show', ['type' => 'xxx', 'token' => $permit->token]));
}
```

Jangan meneruskan `_token_access` dari request ke `store()` yang melewati cek kepemilikan. Untuk kode baru, pisahkan logika simpan ke method privat yang menerima `Notification` yang sudah ter-resolve, lalu panggil dari `store()` (auth) dan `storeByToken()` (token).

### Tanda tangan

- Simpan lewat `saveBase64PngSignature($base64, $role, 'signatures/<area>/<jenis>/')` dari base `Controller`. Hasil berupa `storage/signatures/...`.
- Nilai yang sudah diawali `storage/` dikembalikan apa adanya (tanda tangan lama dipertahankan).
- Jika input kosong, gunakan `?? $record->kolom_lama` agar edit tanpa tanda tangan baru tidak menghapus yang lama.
- Validasi lebih ketat (cek PNG utuh) ada di `JsaController::saveSignature()`; tiru pola itu untuk form baru.

### JSON

- Deklarasikan kolom JSON di `$casts` sebagai `array`, kirim array PHP ke `create()/update()`.
- Dari frontend, tabel dinamis diserialisasi ke hidden input oleh helper Alpine di `resources/js/app.js` (`formJSA`, `formKontraktor`, `isolasiData`, `pekerjaData`). Decode di controller sekali saja.
- Gunakan `filterEmptyPermitValues()` untuk membuang `null`/`''` tanpa menghilangkan `false`/`0`.

### File

- `$request->file('x')->store('uploads', 'public')` untuk dokumen; `Storage::disk('public')->delete()` saat mengganti/menghapus.
- Path upload tanpa prefix; path tanda tangan dengan prefix `storage/`. Helper hapus yang menangani keduanya: `AdminPermintaanController::deleteStoredPath()`.

## 5. Resep perubahan umum

### 5.1 Menambah field ke permit yang sudah ada

Checklist (semua wajib, lihat tabel komponen permit di CODEMAP bagian 5):

1. Migration baru `add_<field>_to_<tabel>` (nullable agar data lama aman).
2. Model: tambah ke `$fillable`, dan `$casts` jika JSON/tanggal.
3. Controller `store()`: tambah aturan validasi dan pemetaan ke data simpan. Jika `storeByToken()` punya validasi sendiri (pola lama), tambahkan di sana juga.
4. Blade form modal `components/permits/<key>.blade.php` dan Blade token `pengajuan-user/workingpermit/form-token-<key>.blade.php`: nama `name=""` harus sama dengan validasi.
5. Blade PDF `pengajuan-user/workingpermit/<key>pdf.blade.php`.
6. Tes: simpan terautentikasi, simpan via token, round-trip JSON, PDF `%PDF-`.
7. `npm run build`.

### 5.2 Menambah jenis permit baru

Gunakan `listrik-tegangan-tinggi` (commit terbaru) sebagai acuan karena polanya paling bersih.

1. Migration `create_work_permit_<nama>_table`: `notification_id` (FK, pertimbangkan `unique()`), kolom form, kolom JSON, kolom tanda tangan, `token` uuid unique, `token_expires_at`.
2. Model `App\Models\WorkPermit<Nama>` dengan `$fillable`, `$casts`, relasi `notification()`; tambah relasi `hasOne` di `Notification` dan di hook `deleting`.
3. Controller `App\Http\Controllers\User\WorkingPermit\<Nama>PermitController` dengan `store`, `showByToken`, `storeByToken`, `preview`, `saveSignature`.
4. Route di `routes/web.php`: `working-permit.<key>.store` dan `.preview` (grup auth), `.token` dan `.token.store` (grup throttle).
5. Daftarkan kunci di semua peta (CODEMAP bagian 6): `IzinKerjaController`, `IzinKerjaPgoController`, `WorkingPermitFormController::PERMITS`, `TokenLinkController::$types`, `TokenPdfController::$types`, `AdminPermintaanController::permitModels()` dan `show()`, `AdminDashboardController`, `ApproveSikController`.
6. View: `components/permits/<key>.blade.php`, `components/steps/modal-working-permit-<key>.blade.php` (pakai `modal-working-permit-lazy`), `pengajuan-user/workingpermit/form-token-<key>.blade.php`, `pengajuan-user/workingpermit/<key>pdf.blade.php`.
7. Tambahkan tombol/opsi di `izin-kerja.blade.php`, `izin-kerja-pgo.blade.php`, `modal-tambah-lainnya.blade.php`, dan tampilan admin `detailpermintaan*.blade.php`.
8. Jika memakai `WorkPermitDetail`, tambahkan nilai enum `permit_type` lewat migration (SQLite + MySQL) dan pemetaan di `WorkingPermitFormController` serta `$detailTypeMap` di `IzinKerjaController`.
9. Tes seperti `ListrikTeganganTinggiPermitTest`.

### 5.3 Menambah atau mengubah step workflow

Step saat ini terduplikasi di beberapa tempat (CODEMAP bagian 6). Sebelum menambah step:

1. Sentralisasikan daftar step ke satu kelas konstanta/enum (misalnya `App\Support\Steps`) dan ganti semua konsumen, dengan tes yang mengunci urutan dan total per usertype.
2. Baru tambahkan step di satu tempat, plus: enum `uploads.step` (migration), branch di Blade dashboard (`$step['code'] === '...'`), judul di admin.
3. Jaga alias `surat_izin_kerja` → `sik` di dashboard vendor.

### 5.4 Menambah aksi admin

1. Route di grup `usertype:admin` (atau `role:Super Admin`).
2. Di controller: `Notification::findOrFail`, lalu `ensureAssignedAdmin()` sebelum mutasi.
3. Jika menghapus data, hapus file fisik (`deleteModelFiles()` / `deleteStoredPath()`) dan reset `StepApproval` step terkait ke `menunggu`.
4. Tes: admin ter-assign berhasil, admin lain ditolak, user biasa 403, record + file terhapus, approval ter-reset.

### 5.5 Mengubah form token / link token

- Tipe baru harus masuk ke `TokenLinkController::$types` dan `TokenPdfController::$types`.
- Tes empat kasus: token valid, token expired (`token_expires_at` lampau), token lama setelah regenerate, dan token record lain tidak bisa memengaruhi notifikasi lain.

## 6. Menulis tes

Kerangka dasar:

```php
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

class ContohTest extends TestCase
{
    use RefreshDatabase;

    private function createSubmission(string $usertype = 'user'): array
    {
        $user = User::factory()->create(['usertype' => $usertype]);
        $notification = Notification::create([
            'user_id' => $user->id, 'type' => 'spk', 'number' => 'SPK-TEST', 'status' => 'menunggu',
        ]);
        return [$user, $notification];
    }

    public function test_owner_can_store(): void
    {
        Storage::fake('public');
        [$user, $notification] = $this->createSubmission();
        $this->actingAs($user)->post(route('...'), ['notification_id' => $notification->id])
            ->assertRedirect()->assertSessionHasNoErrors();
    }

    public function test_other_user_is_forbidden(): void { /* actingAs user lain → 403 atau session error */ }

    public function test_pdf_response(): void
    {
        $response = $this->actingAs($user)->get(route('working-permit.xxx.preview', $notification->id));
        $response->assertOk();
        $this->assertStringStartsWith('%PDF-', $response->streamedContent() ?? $response->getContent());
    }
}
```

Tanda tangan uji: gunakan PNG 1x1 base64 yang valid (lihat `JsaSignatureTest` untuk konstanta yang sudah ada).

## 7. Debugging cepat

| Gejala | Periksa |
|---|---|
| Step berikutnya tidak terbuka padahal data sudah diisi | `step_approvals` untuk step sebelumnya harus `disetujui`; data tersimpan bukan berarti disetujui |
| Link token "expired" padahal baru dibuat | `token_expires_at` null → dianggap expired; pastikan `ensurePermitToken()` dipanggil saat create |
| Gambar tanda tangan/upload 404 | `php artisan storage:link`; path tanda tangan harus `storage/...`, path upload tanpa prefix |
| PDF kosong atau gagal merender gambar | DomPDF memerlukan path file lokal (`public_path()`/`storage_path()`), bukan URL sesi |
| Admin tidak bisa ubah status | hanya `assigned_admin_id` yang boleh; cek siapa yang pertama membuka detail |
| Tombol Approve SIK ditolak | status step `sik` harus `disetujui` dulu |
| Form permit tidak muncul di modal | periksa response `working-permit.form` (403 jika bukan owner, 404 jika kunci tidak ada di `PERMITS`) |
| Nomor JSA bentrok | unique index `jsas_no_jsa_unique`; `store()` retry 3x dengan `lockForUpdate` |
| 419 setelah idle | sesi habis; handler di `bootstrap/app.php` mengarahkan ke login |

## 8. Area yang sengaja belum dirapikan

Lihat "Sharp edges" di AGENTS.md. Saat mengerjakan tugas yang menyentuh area tersebut, perbaiki secara eksplisit dengan tes; jangan memperbaikinya sambil lalu dalam tugas lain.
