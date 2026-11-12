<?php

namespace App\Support;

/**
 * Daftar "Persyaratan Kerja Aman" per jenis permit. Satu sumber untuk form modal,
 * form token, dan PDF supaya tidak bisa berbeda lagi.
 *
 * Jawaban tersimpan berdasarkan NOMOR URUT butir (kerja_aman_ketinggian[0], [1], ...).
 * Jangan menyisipkan atau mengurutkan ulang butir; tambahkan di akhir.
 * Teks boleh memuat <i> dan dirender dengan {!! !!}.
 */
class PermitChecklists
{
    /** @return list<string> */
    public static function ketinggian(): array
    {
        return [
            'Area kerja sudah diperiksa, semua bahaya dan risiko yang bisa diketahui sudah diidentifikasi.',
            'Area kerja/area kerja dibawah sudah diamankan dari potensi jatuhan benda/material.',
            'Area kerja sudah diamankan dari potensi pekerja terpeleset/tersandung.',
            '<i>Job Safety Analysis/Safe Working Procedure</i> sudah tersedia dan semua pengendalian bahayanya sudah dilakukan.',
            'Tepi terbuka/bukaan di lantai sudah dipasang pagar pengaman sementara/ditutup yang mencegah pekerja terjatuh.',
            'Pekerja menggunakan full body harness yang difungsikan sebagai <i>fall restraint system</i>.',
            'Semua pekerja yang terlibat terlatih, memahami bahaya, risiko dan pengendaliannya untuk bekerja di ketinggian.',
            'Semua pekerja terlibat sudah dinyatakan fit untuk bekerja di ketinggian.',
            'Semua pekerja yang terlibat sudah menggunakan full body harness dan memahami cara penggunaanya dengan aman.',
            'Semua pekerja yang terlibat memahami dimana akan mengaitkan <i>hook</i> FBH nya saat bekerja di ketinggian.',
            'Semua pekerja terlibat memahami saat berpindah di ketinggian, <i>double hook</i> FBH nya dikaitkan secara bergantian.',
            'Semua pekerja terlibat memahami teknik <i>three point contact</i> saat naik/turun tangga.',
            '<i>Full body harness</i> yang akan dipakai sudah diperiksa dan dinyatakan layak dan aman untuk dipakai.',
            'Peralatan/perlengkapan kerja yang akan digunakan sudah diperiksa dan dipastikan layak dan aman untuk digunakan.',
            'Peralatan kerja genggam tangan yang berisiko jatuh diberi tali pengikat untuk dikaitkan dengan aman saat di ketinggian.',
            '<i>Man basket</i> sudah diperiksa, dipastikan layak dan aman untuk digunakan, pintu mengarah kedalam dan ada pengunci & <i>anchor point</i>.',
            'Gondola sudah diperiksa, dipastikan layak dan aman untuk digunakan, mempunyai surat izin alat dan operator kompeten.',
            '<i>Man basket</i>/gondola dilengkapi dengan <i>tag line</i> untuk mengendalikan pengangkatan.',
        ];
    }
}
