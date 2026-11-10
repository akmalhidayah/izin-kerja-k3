<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <title>Unit OHS - PT. Semen Tonasa</title>
  <meta name="description" content="Sistem Unit OHS PT. Semen Tonasa untuk pengajuan izin kerja, notifikasi/OP/SPK, JSA, working permit, dan dokumentasi K3.">
  <meta name="robots" content="index,follow">
  <meta name="author" content="PT. Semen Tonasa">
  <meta name="theme-color" content="#ffffff">
  <link rel="canonical" href="{{ url('/') }}">
  <meta property="og:title" content="Unit OHS - PT. Semen Tonasa">
  <meta property="og:description" content="Sistem Unit OHS PT. Semen Tonasa untuk pengajuan izin kerja, notifikasi/OP/SPK, JSA, working permit, dan dokumentasi K3.">
  <meta property="og:type" content="website">
  <meta property="og:url" content="{{ url('/') }}">
  <meta property="og:image" content="{{ asset('images/logo-st2.png') }}">
  <meta property="og:site_name" content="Unit OHS - PT. Semen Tonasa">
  <meta name="twitter:card" content="summary">
  <meta name="twitter:title" content="Unit OHS - PT. Semen Tonasa">
  <meta name="twitter:description" content="Sistem Unit OHS PT. Semen Tonasa untuk pengajuan izin kerja, notifikasi/OP/SPK, JSA, working permit, dan dokumentasi K3.">
  <meta name="twitter:image" content="{{ asset('images/logo-st2.png') }}">
  <link rel="icon" type="image/png" href="{{ asset('images/logo-st2.png') }}?v=1">
  <link rel="apple-touch-icon" href="{{ asset('images/logo-st2.png') }}?v=1">

  {{-- Halaman ini sengaja mandiri: tanpa Vite, tanpa CDN, tanpa video. Visual 3D memakai CSS 3D + SVG inline. --}}
  <script>document.documentElement.classList.add('js');</script>

  <style>
    :root {
      --red: #c8102e;
      --red-dark: #a00d24;
      --green: #15803d;
      --green-soft: #e8f5ec;
      --ink: #0f172a;
      --muted: #5b6577;
      --line: #e6e9ef;
      --bg: #ffffff;
      --bg-soft: #f6f8f7;
      --radius: 18px;
      --wrap: 1280px;
    }

    *, *::before, *::after { box-sizing: border-box; }
    html { scroll-behavior: smooth; -webkit-text-size-adjust: 100%; }
    body {
      margin: 0;
      font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
      color: var(--ink);
      background: var(--bg);
      line-height: 1.55;
      -webkit-font-smoothing: antialiased;
      overflow-x: hidden;
    }
    img { max-width: 100%; display: block; }
    a { color: inherit; text-decoration: none; }
    h1, h2, h3, p { margin: 0; }

    .wrap { width: 100%; max-width: var(--wrap); margin: 0 auto; padding-inline: 20px; }
    .section { padding-block: 72px; }
    .section-soft { background: var(--bg-soft); }
    .eyebrow {
      display: inline-flex; align-items: center; gap: 8px;
      font-size: 13px; font-weight: 600; color: var(--green);
      background: var(--green-soft); padding: 6px 12px; border-radius: 999px;
    }
    .eyebrow::before { content: ""; width: 7px; height: 7px; border-radius: 50%; background: var(--green); }
    .section-head { max-width: 640px; margin-bottom: 36px; }
    .section-head h2 { font-size: clamp(24px, 4.5vw, 34px); line-height: 1.2; letter-spacing: -.02em; margin-top: 14px; }
    .section-head p { color: var(--muted); margin-top: 10px; }

    /* ===== Tombol ===== */
    .btn {
      display: inline-flex; align-items: center; justify-content: center; gap: 8px;
      min-height: 48px; padding: 0 22px; border-radius: 14px;
      font-weight: 600; font-size: 15px; border: 1px solid transparent;
      transition: transform .2s ease, background-color .2s ease, box-shadow .2s ease;
      -webkit-tap-highlight-color: transparent;
    }
    .btn:active { transform: scale(.97); }
    .btn-primary { background: var(--red); color: #fff; box-shadow: 0 8px 20px -8px rgba(200,16,46,.55); }
    .btn-primary:hover { background: var(--red-dark); }
    .btn-ghost { background: #fff; color: var(--ink); border-color: var(--line); }
    .btn-ghost:hover { border-color: #cfd5de; }
    .btn-sm { min-height: 40px; padding: 0 16px; font-size: 14px; border-radius: 12px; }

    /* ===== Header ===== */
    .header {
      position: sticky; top: 0; z-index: 50;
      padding-top: env(safe-area-inset-top, 0px);
      background: rgba(255,255,255,.86);
      backdrop-filter: saturate(160%) blur(12px);
      -webkit-backdrop-filter: saturate(160%) blur(12px);
      border-bottom: 1px solid transparent;
      transition: border-color .2s ease;
    }
    .header.is-scrolled { border-bottom-color: var(--line); }
    .header-inner { display: flex; align-items: center; justify-content: space-between; gap: 12px; height: 68px; }
    .brand { display: flex; align-items: center; gap: 10px; min-width: 0; }
    .brand img { width: 38px; height: 38px; object-fit: contain; flex: none; }
    .brand-text { min-width: 0; }
    .brand-title { font-weight: 700; font-size: 15px; color: var(--red); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .brand-sub { font-size: 12px; color: var(--muted); white-space: nowrap; }

    /* ===== Hero ===== */
    .hero {
      position: relative; overflow: hidden;
      background:
        radial-gradient(60% 60% at 85% 30%, rgba(21,128,61,.10), transparent 70%),
        radial-gradient(40% 35% at 10% 72%, rgba(200,16,46,.06), transparent 70%),
        var(--bg);
    }
    .hero::before {
      content: ""; position: absolute; inset: 0; pointer-events: none;
      background-image:
        linear-gradient(rgba(15,23,42,.045) 1px, transparent 1px),
        linear-gradient(90deg, rgba(15,23,42,.045) 1px, transparent 1px);
      background-size: 32px 32px;
      -webkit-mask-image: radial-gradient(70% 60% at 60% 40%, #000, transparent 75%);
              mask-image: radial-gradient(70% 60% at 60% 40%, #000, transparent 75%);
    }
    .hero-grid {
      position: relative; display: grid; gap: 20px; align-items: center;
      padding-block: 28px 96px; /* ruang bawah untuk panel berita yang menimpa hero */
    }
    .hero-copy h1 {
      font-size: clamp(32px, 8.5vw, 56px); line-height: 1.08; letter-spacing: -.035em;
      margin-top: 18px; font-weight: 800;
    }
    .hero-copy h1 em { font-style: normal; color: var(--green); }
    .hero-actions { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 28px; }

    /* ===== Visual 3D (CSS 3D + SVG, tanpa library) ===== */
    .scene {
      order: -1; position: relative; height: 300px;
      perspective: 1000px; perspective-origin: 50% 40%;
      touch-action: pan-y; user-select: none; -webkit-user-select: none;
    }
    .sway { position: absolute; inset: 0; transform-style: preserve-3d; animation: sway 9s ease-in-out infinite; }
    .stage {
      position: absolute; inset: 0; transform-style: preserve-3d;
      transform: rotateX(var(--rx, 0deg)) rotateY(var(--ry, 0deg));
      will-change: transform;
    }
    .layer { position: absolute; transform-style: preserve-3d; }
    .disc {
      left: 50%; top: 50%; width: 250px; height: 250px; margin: -125px 0 0 -125px; border-radius: 50%;
      background: radial-gradient(circle at 35% 30%, #ffffff, #e9f3ec 60%, #d7eadd);
      box-shadow: inset 0 -12px 30px rgba(21,128,61,.12), 0 30px 60px -30px rgba(21,128,61,.35);
      transform: translateZ(-90px);
    }
    .gear-lg { right: 4%; top: 2%; width: 150px; height: 150px; transform: translateZ(-50px); color: #2f8f55; opacity: .9; }
    .gear-sm { left: 8%; bottom: 8%; width: 96px; height: 96px; transform: translateZ(-20px); color: #8cc5a0; }
    .gear-xs { left: 18%; top: 8%; width: 58px; height: 58px; transform: translateZ(-70px); color: #c4dfcd; }
    .spin { width: 100%; height: 100%; animation: spin 26s linear infinite; }
    .spin.rev { animation-direction: reverse; animation-duration: 18s; }
    .helmet-wrap { left: 50%; top: 50%; width: 220px; margin: -100px 0 0 -110px; transform: translateZ(60px); }
    .helmet { animation: float 6s ease-in-out infinite; filter: drop-shadow(0 18px 18px rgba(15,23,42,.18)); }
    .shadow {
      left: 50%; top: 50%; width: 170px; height: 26px; margin: 92px 0 0 -85px; border-radius: 50%;
      background: radial-gradient(closest-side, rgba(15,23,42,.22), transparent);
      transform: translateZ(10px); animation: shadow 6s ease-in-out infinite;
    }
    .chip {
      display: inline-flex; align-items: center; gap: 6px;
      padding: 7px 12px; border-radius: 999px; font-size: 12px; font-weight: 700;
      background: rgba(255,255,255,.92); border: 1px solid var(--line);
      box-shadow: 0 10px 24px -12px rgba(15,23,42,.35); white-space: nowrap;
    }
    .chip i { width: 8px; height: 8px; border-radius: 50%; display: block; }
    .chip-1 { left: 4%; top: 30%; transform: translateZ(110px); }
    .chip-2 { right: 2%; top: 58%; transform: translateZ(130px); }
    .chip-3 { left: 30%; bottom: 9%; transform: translateZ(90px); }
    .chip-1 i { background: var(--green); }
    .chip-2 i { background: #eab308; }
    .chip-3 i { background: var(--red); }

    @keyframes spin { to { transform: rotate(360deg); } }
    @keyframes float { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
    @keyframes shadow { 0%,100% { transform: translateZ(10px) scale(1); opacity: 1; } 50% { transform: translateZ(10px) scale(.86); opacity: .7; } }
    @keyframes sway { 0%,100% { transform: rotateY(-6deg) rotateX(4deg); } 50% { transform: rotateY(6deg) rotateX(-2deg); } }
    .is-paused .spin, .is-paused .helmet, .is-paused .shadow, .is-paused .sway { animation-play-state: paused; }

    /* ===== Alur ===== */
    .steps { list-style: none; margin: 0; padding: 0; display: grid; gap: 0; position: relative; }
    .step { position: relative; display: flex; gap: 14px; padding: 0 0 18px; }
    .step::before {
      content: ""; position: absolute; left: 19px; top: 40px; bottom: 0; width: 2px; background: var(--line);
    }
    .step:last-child::before { display: none; }
    .step-num {
      flex: none; width: 40px; height: 40px; border-radius: 12px; display: grid; place-items: center;
      font-weight: 800; font-size: 14px; background: #fff; border: 1px solid var(--line); color: var(--ink);
      position: relative; z-index: 1;
    }
    .step-body { padding-top: 8px; }
    .step-title { font-weight: 600; font-size: 15.5px; }
    .step-meta { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 6px; }
    .pill { font-size: 11.5px; font-weight: 600; padding: 3px 9px; border-radius: 999px; background: #f1f4f8; color: var(--muted); }
    .step.final .step-num { background: var(--red); border-color: var(--red); color: #fff; }

    /* ===== Berita: kartu menimpa tepi hero (tanpa kontainer pembungkus) ===== */
    .news-section {
      --cut: 112px; /* tinggi dari atas section sampai garis pergantian latar (memotong kartu) */
      display: flow-root; position: relative; z-index: 2;
      margin-top: -48px; padding-bottom: 64px;
      background: linear-gradient(180deg, transparent 0, transparent var(--cut), var(--bg-soft) var(--cut), var(--bg-soft) 100%);
    }
    .news {
      display: grid; grid-auto-flow: column; grid-auto-columns: 84%; gap: 14px;
      overflow-x: auto; scroll-snap-type: x mandatory; padding: 4px 20px 24px;
      margin-inline: -20px; scroll-padding-inline: 20px;
      scrollbar-width: none;
    }
    .news::-webkit-scrollbar { display: none; }
    .news-card {
      scroll-snap-align: start; background: #fff; border: 1px solid var(--line); border-radius: 20px;
      overflow: hidden; display: flex; flex-direction: column;
      box-shadow: 0 26px 40px -28px rgba(15,23,42,.38), 0 6px 14px -8px rgba(15,23,42,.10);
      transition: transform .25s ease, box-shadow .25s ease;
    }
    .news-card:hover { transform: translateY(-6px); box-shadow: 0 34px 50px -28px rgba(15,23,42,.45), 0 8px 18px -8px rgba(15,23,42,.12); }
    .news-card img { width: 100%; height: auto; aspect-ratio: 16 / 10; object-fit: cover; background: #eef1f4; }
    .news-body { padding: 18px; display: flex; flex-direction: column; gap: 8px; flex: 1; }
    .news-src { font-size: 12px; font-weight: 700; color: var(--red); }
    .news-title { font-weight: 700; font-size: 16px; line-height: 1.35; }
    .news-desc { color: var(--muted); font-size: 14px; }
    .news-link { margin-top: auto; padding-top: 6px; font-size: 14px; font-weight: 600; color: var(--red); }

    /* ===== Poster ===== */
    .poster { border-radius: var(--radius); overflow: hidden; border: 1px solid var(--line); background: #eef1f4; }
    .poster img { width: 100%; height: auto; }
    .poster-cap { margin-top: 10px; font-size: 13px; color: var(--muted); }
    .poster-cap a { color: var(--red); font-weight: 600; }

    /* ===== CTA + Footer ===== */
    .cta {
      border-radius: 24px; padding: 32px 24px; text-align: center; color: #fff;
      background: radial-gradient(80% 120% at 100% 0%, #1f9a50, transparent 60%), #0f5a2c;
    }
    .cta h2 { font-size: clamp(22px, 4.5vw, 30px); letter-spacing: -.02em; }
    .cta p { opacity: .85; margin-top: 8px; }
    .cta .btn { margin-top: 22px; background: #fff; color: var(--ink); }
    .footer {
      border-top: 1px solid var(--line); padding-block: 28px calc(28px + env(safe-area-inset-bottom, 0px));
      color: var(--muted); font-size: 13.5px;
    }
    .footer-inner { display: flex; flex-wrap: wrap; gap: 12px; align-items: center; justify-content: space-between; }

    /* ===== Reveal saat scroll ===== */
    .js .reveal { opacity: 0; transform: translateY(16px); transition: opacity .6s ease, transform .6s ease; }
    .js .reveal.in { opacity: 1; transform: none; }

    /* ===== Breakpoint ===== */
    @media (min-width: 640px) {
      .news { grid-auto-columns: calc((100% - 28px) / 2.2); }
      .cta { padding: 48px; }
    }
    @media (min-width: 900px) {
      .section { padding-block: 96px; }
      .hero-grid { grid-template-columns: 1.05fr 1fr; padding-block: 56px 150px; min-height: calc(100vh - 68px); max-height: 860px; }
      .news-section { --cut: 140px; margin-top: -80px; padding-bottom: 88px; }
      .scene { order: 0; height: 460px; }
      .disc { width: 360px; height: 360px; margin: -180px 0 0 -180px; }
      .gear-lg { width: 210px; height: 210px; }
      .gear-sm { width: 130px; height: 130px; }
      .gear-xs { width: 76px; height: 76px; }
      .helmet-wrap { width: 300px; margin: -140px 0 0 -150px; }
      .shadow { width: 230px; margin: 128px 0 0 -115px; }
      .chip { font-size: 13px; padding: 9px 14px; }
      .steps { grid-template-columns: repeat(3, 1fr); gap: 14px; }
      .step { flex-direction: column; gap: 12px; padding: 20px; background: #fff; border: 1px solid var(--line); border-radius: var(--radius); }
      .step::before { display: none; }
      .step-body { padding-top: 0; }
      .news { grid-auto-columns: calc((100% - 56px) / 3); gap: 28px; margin-inline: 0; padding: 4px 0 24px; overflow: visible; }
    }
    @media (max-width: 479px) {
      .brand img + img { display: none; }
    }
    @media (max-width: 359px) {
      .brand-sub { display: none; }
    }
    @media (prefers-reduced-motion: reduce) {
      html { scroll-behavior: auto; }
      .spin, .helmet, .shadow, .sway { animation: none !important; }
      .js .reveal { opacity: 1; transform: none; transition: none; }
    }
  </style>
</head>

@php
  // Roda gigi SVG dibangkitkan dari parameter, jadi tidak perlu file aset.
  $gear = function (int $teeth, float $outer, float $root, float $hole): string {
      $c = 50; $step = 2 * M_PI / $teeth; $d = '';
      for ($i = 0; $i < $teeth; $i++) {
          $a = $i * $step;
          foreach ([[-0.30, $root], [-0.17, $outer], [0.17, $outer], [0.30, $root]] as $k => [$f, $r]) {
              $x = round($c + $r * cos($a + $f * $step), 2);
              $y = round($c + $r * sin($a + $f * $step), 2);
              $d .= ($i === 0 && $k === 0 ? 'M' : 'L') . "$x $y ";
          }
      }
      $d .= 'Z ';
      $d .= 'M' . ($c + $hole) . " $c A$hole $hole 0 1 0 " . ($c - $hole) . " $c A$hole $hole 0 1 0 " . ($c + $hole) . " $c Z";
      return $d;
  };

  $steps = [
      ['Notifikasi / OP / SPK', 'Form', true],
      ['Data Kontraktor', 'Form', false],
      ['BPJS Ketenagakerjaan', 'Upload', false],
      ['Job Safety Analysis (JSA)', 'Form', true],
      ['Working Permit', 'Form', true],
      ['Fakta Integritas', 'Upload', false],
      ['Sertifikasi AK3', 'Upload', false],
      ['KTP Pekerja', 'Upload', false],
      ['Surat Hasil MCU', 'Upload', false],
      ['Struktur Organisasi', 'Upload', false],
      ['Dokumen Safety Induction', 'Upload', false],
      ['Berita Acara Serah Terima', 'Upload', false],
      ['Surat Izin Kerja terbit', 'Tanda tangan digital', false],
  ];

  $news = [
      [
          'src' => 'Semen Tonasa',
          'title' => 'Upacara Bulan K3 Tingkat Provinsi Sulsel Digelar di Semen Tonasa',
          'desc' => 'Kegiatan Bulan K3 digelar untuk meningkatkan budaya keselamatan kerja di lingkungan industri.',
          'img' => 'https://www.sementonasa.co.id/wp-content/uploads/2025/03/WhatsApp-Image-2025-03-03-at-07.49.16_7c0b11a0.jpg',
          'url' => 'https://www.sementonasa.co.id/upacara-bulan-k3-tingkat-provinsi-sulsel-digelar-di-semen-tonasa/',
      ],
      [
          'src' => 'Simpul Rakyat',
          'title' => 'PT Semen Tonasa Raih Tujuh Penghargaan K3 di Sulawesi Selatan',
          'desc' => 'Prestasi K3 ini mencerminkan konsistensi penerapan standar keselamatan dan kesehatan kerja.',
          'img' => 'https://www.sementonasa.co.id/wp-content/uploads/2025/12/WhatsApp-Image-2025-12-31-at-15.12.42.jpeg',
          'url' => 'https://www.simpulrakyat.co.id/2025/02/pt-semen-tonasa-raih-tujuh-penghargaan-k3-di-sulawesi-selatan.html',
      ],
      [
          'src' => 'Semen Tonasa',
          'title' => 'Tingkatkan Sadar Keselamatan, PT Semen Tonasa Gelar Pelatihan Dasar K3',
          'desc' => 'Pelatihan ini fokus pada peningkatan pemahaman dasar keselamatan dan kesehatan kerja.',
          'img' => 'https://www.sementonasa.co.id/wp-content/uploads/2022/11/WhatsApp-Image-2022-11-30-at-12.12.17.jpeg',
          'url' => 'https://www.sementonasa.co.id/tingkatkan-sadar-keselamatan-pt-semen-tonasa-gelar-pelatihan-dasar-dasar-keselamatan-dan-kesehatan-kerja/',
      ],
  ];
@endphp

<body>

<header class="header" id="header">
  <div class="wrap header-inner">
    <a href="{{ url('/') }}" class="brand" aria-label="Unit K3 PT. Semen Tonasa">
      <img src="{{ asset('images/logo-k3-128.png') }}" width="38" height="38" alt="Logo K3">
      <img src="{{ asset('images/logo-st-128.png') }}" width="38" height="38" alt="Logo Semen Tonasa">
      <span class="brand-text">
        <span class="brand-title" style="display:block">Unit K3 · PT. Semen Tonasa</span>
        <span class="brand-sub" style="display:block">Keselamatan &amp; Kesehatan Kerja</span>
      </span>
    </a>
    <a href="{{ route('login') }}" class="btn btn-primary btn-sm">Masuk</a>
  </div>
</header>

<main>

  {{-- ================= HERO ================= --}}
  <section class="hero" id="hero">
    <div class="wrap hero-grid">
      <div class="hero-copy">
        <span class="eyebrow">Sistem Izin Kerja Digital</span>
        <h1>Izin kerja yang <em>aman</em>, cepat, dan tertelusur.</h1>
        <div class="hero-actions">
          <a href="{{ route('login') }}" class="btn btn-primary">Masuk ke Sistem</a>
          <a href="#alur" class="btn btn-ghost">Lihat Alur</a>
        </div>
      </div>

      {{-- Visual 3D: bisa digeser dengan jari/mouse, berhenti saat tidak terlihat --}}
      <div class="scene" id="scene" aria-hidden="true">
        <div class="sway">
          <div class="stage" id="stage">
            <div class="layer disc"></div>

            <div class="layer gear-xs">
              <svg class="spin" viewBox="0 0 100 100"><path fill="currentColor" fill-rule="evenodd" d="{{ $gear(10, 48, 38, 14) }}"/></svg>
            </div>
            <div class="layer gear-lg">
              <svg class="spin" viewBox="0 0 100 100"><path fill="currentColor" fill-rule="evenodd" d="{{ $gear(14, 49, 41, 17) }}"/></svg>
            </div>
            <div class="layer gear-sm">
              <svg class="spin rev" viewBox="0 0 100 100"><path fill="currentColor" fill-rule="evenodd" d="{{ $gear(10, 48, 38, 15) }}"/></svg>
            </div>

            <div class="layer shadow"></div>

            <div class="layer helmet-wrap">
              <svg class="helmet" viewBox="0 0 240 170">
                <defs>
                  <linearGradient id="hg" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0" stop-color="#fde68a"/>
                    <stop offset=".55" stop-color="#facc15"/>
                    <stop offset="1" stop-color="#d9a406"/>
                  </linearGradient>
                  <linearGradient id="hb" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0" stop-color="#eab308"/>
                    <stop offset="1" stop-color="#a16207"/>
                  </linearGradient>
                </defs>
                <path d="M34 128 C34 66 72 26 120 26 C168 26 206 66 206 128 Z" fill="url(#hg)"/>
                <path d="M107 27 C110 60 110 98 107 128 L133 128 C130 98 130 60 133 27 C126 26 114 26 107 27 Z" fill="#d9a406" opacity=".45"/>
                <ellipse cx="84" cy="66" rx="26" ry="11" transform="rotate(-32 84 66)" fill="#fff" opacity=".45"/>
                <path d="M12 128 Q120 114 228 128 Q234 142 222 147 Q120 135 18 147 Q6 142 12 128 Z" fill="url(#hb)"/>
                <circle cx="120" cy="96" r="15" fill="#fff"/>
                <rect x="115" y="86" width="10" height="20" rx="2" fill="#15803d"/>
                <rect x="110" y="91" width="20" height="10" rx="2" fill="#15803d"/>
              </svg>
            </div>

            <div class="layer chip chip-1"><i></i>JSA</div>
            <div class="layer chip chip-2"><i></i>Working Permit</div>
            <div class="layer chip chip-3"><i></i>SIK Terbit</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- ================= BERITA (panel menimpa bagian bawah hero) ================= --}}
  <section class="news-section" id="berita">
    <div class="wrap">
      <div class="news">
        @foreach ($news as $item)
          <a class="news-card" href="{{ $item['url'] }}" target="_blank" rel="noopener noreferrer">
            <img src="{{ $item['img'] }}" alt="{{ $item['title'] }}" width="640" height="400" loading="lazy" decoding="async">
            <div class="news-body">
              <span class="news-src">{{ $item['src'] }}</span>
              <span class="news-title">{{ $item['title'] }}</span>
              <span class="news-desc">{{ $item['desc'] }}</span>
              <span class="news-link">Baca selengkapnya &rarr;</span>
            </div>
          </a>
        @endforeach
      </div>
    </div>
  </section>

  {{-- ================= ALUR ================= --}}
  <section class="section" id="alur">
    <div class="wrap">
      <div class="section-head reveal">
        <span class="eyebrow">Alur pengajuan</span>
        <h2>Dari notifikasi sampai Surat Izin Kerja.</h2>
      </div>

      <ol class="steps">
        @foreach ($steps as $i => [$title, $type])
          <li class="step reveal {{ $loop->last ? 'final' : '' }}">
            <span class="step-num">{{ $i + 1 }}</span>
            <div class="step-body">
              <div class="step-title">{{ $title }}</div>
              <div class="step-meta">
                <span class="pill">{{ $type }}</span>
              </div>
            </div>
          </li>
        @endforeach
      </ol>

    </div>
  </section>

  {{-- ================= POSTER ================= --}}
  <section class="section section-soft">
    <div class="wrap">
      <figure class="reveal" style="margin:0">
        <div class="poster">
          <img src="{{ asset('images/posterk3-web.jpg') }}" width="1400" height="990" loading="lazy" decoding="async" alt="Poster K3 PT. Semen Tonasa">
        </div>
        <figcaption class="poster-cap">
          Poster K3. <a href="{{ asset('images/posterk3.jpg') }}" target="_blank" rel="noopener">Buka resolusi penuh</a>
        </figcaption>
      </figure>
    </div>
  </section>

  {{-- ================= CTA ================= --}}
  <section class="section" style="padding-top:0">
    <div class="wrap">
      <div class="cta reveal">
        <h2>Siap mengajukan izin kerja?</h2>
        <p>Masuk dengan akun yang telah didaftarkan oleh Unit K3.</p>
        <a href="{{ route('login') }}" class="btn">Masuk ke Sistem</a>
      </div>
    </div>
  </section>

</main>

<footer class="footer">
  <div class="wrap footer-inner">
    <span>&copy; {{ date('Y') }} PT. Semen Tonasa · Unit K3</span>
    <a href="#hero">Kembali ke atas &uarr;</a>
  </div>
</footer>

<script>
(() => {
  const header = document.getElementById('header');
  const onScroll = () => header.classList.toggle('is-scrolled', window.scrollY > 8);
  onScroll();
  window.addEventListener('scroll', onScroll, { passive: true });

  const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // Reveal saat scroll
  const items = document.querySelectorAll('.reveal');
  if (reduce || !('IntersectionObserver' in window)) {
    items.forEach((el) => el.classList.add('in'));
  } else {
    const io = new IntersectionObserver((entries) => {
      entries.forEach((e) => {
        if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); }
      });
    }, { rootMargin: '0px 0px -8% 0px' });
    items.forEach((el) => io.observe(el));
  }

  // Tilt 3D: mengikuti mouse/jari, dihaluskan dengan rAF, berhenti saat tidak terlihat
  const scene = document.getElementById('scene');
  const stage = document.getElementById('stage');
  if (!scene || !stage || reduce) return;

  let tx = 0, ty = 0, cx = 0, cy = 0, raf = 0, visible = true;
  const hero = document.getElementById('hero');

  const loop = () => {
    cx += (tx - cx) * 0.08;
    cy += (ty - cy) * 0.08;
    stage.style.setProperty('--ry', (cx * 16).toFixed(2) + 'deg');
    stage.style.setProperty('--rx', (-cy * 12).toFixed(2) + 'deg');
    raf = (visible && (Math.abs(tx - cx) > 0.001 || Math.abs(ty - cy) > 0.001)) ? requestAnimationFrame(loop) : 0;
  };
  const kick = () => { if (!raf) raf = requestAnimationFrame(loop); };

  hero.addEventListener('pointermove', (e) => {
    const r = scene.getBoundingClientRect();
    tx = Math.max(-1, Math.min(1, (e.clientX - (r.left + r.width / 2)) / (r.width / 2)));
    ty = Math.max(-1, Math.min(1, (e.clientY - (r.top + r.height / 2)) / (r.height / 2)));
    kick();
  }, { passive: true });
  hero.addEventListener('pointerleave', () => { tx = 0; ty = 0; kick(); });
  hero.addEventListener('pointerup', (e) => { if (e.pointerType === 'touch') { tx = 0; ty = 0; kick(); } });

  if ('IntersectionObserver' in window) {
    new IntersectionObserver(([e]) => {
      visible = e.isIntersecting;
      scene.classList.toggle('is-paused', !visible);
    }).observe(scene);
  }
  document.addEventListener('visibilitychange', () => scene.classList.toggle('is-paused', document.hidden));
})();
</script>

</body>
</html>
