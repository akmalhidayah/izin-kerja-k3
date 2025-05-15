# 🛠️ Laravel K3 ST - Izin Kerja Sistem

Sistem digitalisasi proses izin kerja di PT Semen Tonasa. Dibuat dengan Laravel 11 + Tailwind + Alpine.js.

## 📂 Struktur Fitur

- Notifikasi & SPK
- Upload dokumen wajib (BPJS, AK3, KTP, dll)
- Job Safety Analysis (JSA)
- Working Permit (multi tahap)
- Approval + Signature
- Preview & Export PDF (Surat Izin Kerja)

## 🧰 Teknologi

- Laravel 11
- Tailwind CSS
- Alpine.js
- DomPDF (PDF generator)
- Livewire (opsional)

## 📦 Cara Instalasi

```bash
git clone https://github.com/akmalhidayah/izin-kerja-k3.git
cd izin-kerja-k3
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
npm install && npm run dev
