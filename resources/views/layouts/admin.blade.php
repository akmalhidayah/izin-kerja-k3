@props(['plain' => false])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Admin Panel') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-st2.png') }}?v=1">
    <link rel="shortcut icon" href="{{ asset('images/logo-st2.png') }}?v=1">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-st2.png') }}?v=1">

    <!-- Font Awesome (optional, boleh hapus kalau nggak dipakai lagi) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Flatpickr -->
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">

    <!-- Tailwind & Alpine -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Lucide Icons (modern) -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        [x-cloak]{ display:none !important; }

        /* Header admin: foto pabrik + gradien gelap ke merah brand */
        .topbar-hero {
            background-color: #0f172a;
            background-image:
                linear-gradient(100deg, rgba(15,23,42,.92) 0%, rgba(15,23,42,.78) 42%, rgba(153,27,27,.78) 100%),
                url('{{ asset('images/bg-login.jpg') }}');
            background-size: cover;
            background-position: center 62%;
        }

        /* Jangan sampai sticky pecah oleh overflow-x di leluhur header (clip tidak membuat scroll container) */
        body, body > div[x-data] { overflow-x: clip; }
        .topbar-hero button:hover { background-color: rgba(255,255,255,.10); }

        /* Menu aktif sidebar: tab menyambung ke latar konten (#f8fafc = bg-slate-50) dengan lekukan atas/bawah */
        .sidebar-rail .nav-active::before,
        .sidebar-rail .nav-active::after {
            content: '';
            position: absolute;
            right: 0;
            width: 16px;
            height: 16px;
            background: transparent;
            pointer-events: none;
        }
        .sidebar-rail .nav-active::before {
            top: -16px;
            border-bottom-right-radius: 16px;
            box-shadow: 6px 6px 0 6px #f8fafc;
        }
        .sidebar-rail .nav-active::after {
            bottom: -16px;
            border-top-right-radius: 16px;
            box-shadow: 6px -6px 0 6px #f8fafc;
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-800 overflow-x-clip">
<div
    x-data="{
        sidebarOpen: true,
        mobileOpen: false,
        toggle() {
            // desktop
            if (window.innerWidth >= 1024) this.sidebarOpen = !this.sidebarOpen;
            // mobile
            else this.mobileOpen = !this.mobileOpen;
        },
        closeMobile(){ this.mobileOpen = false; }
    }"
    x-init="$watch('mobileOpen', v => document.body.classList.toggle('overflow-hidden', v))"
    class="min-h-screen overflow-x-clip"
>

    <!-- MOBILE OVERLAY -->
    <div
        x-show="mobileOpen"
        x-transition.opacity
        class="fixed inset-0 bg-black/40 z-30 lg:hidden"
        @click="closeMobile()"
        x-cloak
    ></div>

    <!-- SIDEBAR: panel merah melayang; menu aktif menyambung ke konten lewat lekukan (.nav-active) -->
    @php
        $isSuperAdmin = Auth::user()
            && Auth::user()?->isAdmin()
            && in_array(Auth::user()->role->name ?? '', ['Super Admin']);

        $isChecklist = request()->routeIs('admin.checklist.*');
        $isApd       = request()->routeIs('admin.apd.*');
        $isUserPanel = request()->routeIs('admin.userpanel.*') || request()->routeIs('admin.role_permission.*') || request()->routeIs('admin.news.*');

        $badges = $sidebarBadges ?? [];
        $fmtBadge = fn ($n) => $n > 999 ? '999+' : (string) $n;

        $navGroups = [
            ['label' => 'Utama', 'show' => true, 'items' => [
                ['href' => route('admin.dashboard'), 'active' => request()->routeIs('admin.dashboard'), 'icon' => 'home', 'text' => 'Dashboard', 'badge' => null, 'key' => 'dashboard'],
            ]],
            ['label' => 'Pengajuan', 'show' => true, 'items' => [
                ['href' => route('admin.permintaansik'), 'active' => request()->routeIs('admin.permintaansik'), 'icon' => 'clipboard-list', 'text' => 'Permintaan SIK Vendor', 'badge' => $badges['vendor'] ?? 0, 'key' => 'vendor'],
                ['href' => route('admin.permintaansikpgo'), 'active' => request()->is('admin/permintaansikpgo*'), 'icon' => 'clipboard-check', 'text' => 'Permintaan SIK Karyawan', 'badge' => $badges['pgo'] ?? 0, 'key' => 'pgo'],
            ]],
            ['label' => 'Penerbitan', 'show' => $isSuperAdmin, 'items' => [
                ['href' => $isSuperAdmin ? route('admin.approvesik.index') : '#', 'active' => request()->routeIs('admin.approvesik.index'), 'icon' => 'file-signature', 'text' => 'Approve SIK', 'badge' => $badges['sik'] ?? 0, 'key' => 'sik'],
            ]],
        ];

        $subMenu = [
            ['route' => 'admin.userpanel.index', 'active' => request()->routeIs('admin.userpanel.*'), 'icon' => 'users', 'text' => 'Manajemen User'],
            ['route' => 'admin.role_permission.index', 'active' => request()->routeIs('admin.role_permission.*'), 'icon' => 'shield-check', 'text' => 'Role & Permission'],
            ['route' => 'admin.news.index', 'active' => request()->routeIs('admin.news.*'), 'icon' => 'newspaper', 'text' => 'Upload Berita'],
        ];
    @endphp
    <aside
        class="sidebar-rail fixed inset-y-0 left-0 z-40 flex flex-col rounded-r-3xl bg-gradient-to-b from-red-700 via-red-700 to-rose-900 text-white shadow-xl shadow-red-900/20
               transition-all duration-300 lg:inset-y-3 lg:left-3 lg:rounded-3xl"
        :class="[
            (mobileOpen ? 'translate-x-0' : '-translate-x-full') + ' lg:translate-x-0',
            (sidebarOpen ? 'lg:w-72' : 'lg:w-20'),
            'w-72'
        ]"
    >
        <!-- TOP: Brand -->
        <div class="border-b border-white/10">
            <div class="flex items-center gap-3 px-4 py-4" :class="sidebarOpen ? 'justify-between' : 'lg:flex-col lg:px-2'">
                <div class="flex min-w-0 items-center gap-3">
                    <img src="{{ asset('images/logo-st.png') }}" class="h-10 w-10 shrink-0 rounded-xl bg-white object-contain p-0.5 shadow" alt="Logo">
                    <div class="min-w-0" x-show="sidebarOpen" x-transition>
                        <div class="font-extrabold leading-none tracking-tight text-white">K3 Admin</div>
                        <div class="truncate text-xs text-white/70">PT. Semen Tonasa</div>
                    </div>
                </div>

                <button
                    @click="toggle()"
                    class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl text-white/80 transition hover:bg-white/10 hover:text-white active:scale-[0.98]"
                    :title="sidebarOpen ? 'Ciutkan menu' : 'Buka menu'"
                    aria-label="Toggle Sidebar"
                >
                    <i data-lucide="panel-left" class="h-5 w-5"></i>
                </button>
            </div>
        </div>

        <!-- MENU -->
        <div class="flex-1 overflow-y-auto overflow-x-hidden px-3 py-3">
            <nav class="text-sm">
                @foreach ($navGroups as $group)
                    @continue(! $group['show'])

                    <div x-show="sidebarOpen" class="px-3 pb-1.5 pt-4 text-[10px] font-semibold uppercase tracking-[0.18em] text-white/50">{{ $group['label'] }}</div>
                    <div x-show="!sidebarOpen" class="mx-auto my-3 h-px w-8 bg-white/20"></div>

                    <div class="space-y-1">
                        @foreach ($group['items'] as $item)
                            <a href="{{ $item['href'] }}"
                               :title="sidebarOpen ? '' : @js($item['text'])"
                               class="group relative flex items-center gap-3 py-2 pl-3 pr-3 transition
                                      {{ $item['active'] ? 'nav-active -mr-3 rounded-l-2xl bg-slate-50 text-red-700' : 'rounded-xl text-white/85 hover:bg-white/10 hover:text-white' }}">
                                <span class="relative inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl
                                             {{ $item['active'] ? 'bg-red-600 text-white shadow-md shadow-red-600/30' : 'bg-white/10 text-white group-hover:bg-white/20' }}">
                                    <i data-lucide="{{ $item['icon'] }}" class="h-5 w-5"></i>
                                    @if ($item['badge'])
                                        <span x-show="!sidebarOpen" class="absolute -right-1 -top-1 h-2.5 w-2.5 rounded-full bg-amber-400 ring-2 {{ $item['active'] ? 'ring-slate-50' : 'ring-red-700' }}"></span>
                                    @endif
                                </span>
                                <span x-show="sidebarOpen" x-transition class="min-w-0 flex-1 truncate font-medium">{{ $item['text'] }}</span>
                                @if ($item['badge'])
                                    <span x-show="sidebarOpen" data-sidebar-badge="{{ $item['key'] }}" data-count="{{ $item['badge'] }}"
                                          class="shrink-0 rounded-full bg-amber-400 px-2 py-0.5 text-[10px] font-bold text-slate-900"
                                          title="Perlu tindakan">{{ $fmtBadge($item['badge']) }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                @endforeach

                {{-- # MASIH PENGEMBANGAN: grup menu "Modul" disembunyikan sementara.
                     # Isi: Checklist Inspeksi, Inspeksi Rambu, Arsip Inventaris, Upload IK & SOP, Peminjaman & Jadwal APD.
                     # Hapus pembungkus komentar ini untuk mengaktifkan kembali.
                <!-- Divider -->
                <div class="pt-2 pb-1" x-show="sidebarOpen" x-transition>
                    <div class="text-[11px] uppercase tracking-wider text-slate-400 px-3">Modul</div>
                </div>

                <!-- Dropdown: Checklist Inspeksi -->
                <div x-data="{ open: {{ $isChecklist ? 'true' : 'false' }} }" class="rounded-xl">
                    <button
                        @click="open=!open"
                        class="w-full group flex items-center gap-3 rounded-xl px-3 py-2.5 transition
                               {{ $isChecklist ? 'bg-red-50 text-red-700 ring-1 ring-red-100' : 'text-slate-700 hover:bg-slate-100' }}"
                    >
                        <span class="inline-flex w-9 h-9 items-center justify-center rounded-xl
                                     {{ $isChecklist ? 'bg-red-100 text-red-700' : 'bg-slate-100 text-slate-600 group-hover:bg-white' }}">
                            <i data-lucide="check-square" class="w-5 h-5"></i>
                        </span>
                        <span x-show="sidebarOpen" x-transition class="flex-1 text-left font-medium">Checklist Inspeksi</span>
                        <i data-lucide="chevron-down"
                           class="w-4 h-4 text-slate-400 transition"
                           :class="open ? 'rotate-180' : ''"
                           x-show="sidebarOpen" x-transition></i>
                    </button>

                    <div x-show="open" x-collapse x-cloak class="mt-1 pl-12 space-y-1">
                        <a href="#"
                           class="block rounded-lg px-3 py-2 text-slate-600 hover:bg-slate-100 hover:text-slate-900">KTA</a>
                        <a href="#"
                           class="block rounded-lg px-3 py-2 text-slate-600 hover:bg-slate-100 hover:text-slate-900">TTA</a>
                        <a href="#"
                           class="block rounded-lg px-3 py-2 text-slate-600 hover:bg-slate-100 hover:text-slate-900">Apar</a>
                        <a href="#"
                           class="block rounded-lg px-3 py-2 text-slate-600 hover:bg-slate-100 hover:text-slate-900">Hydrant</a>
                        <a href="#"
                           class="block rounded-lg px-3 py-2 text-slate-600 hover:bg-slate-100 hover:text-slate-900">P3K</a>
                        <a href="#"
                           class="block rounded-lg px-3 py-2 text-slate-600 hover:bg-slate-100 hover:text-slate-900">Damkar</a>
                        <a href="#"
                           class="block rounded-lg px-3 py-2 text-slate-600 hover:bg-slate-100 hover:text-slate-900">Fire Alarm</a>
                    </div>
                </div>

                <a href="#"
                   class="group flex items-center gap-3 rounded-xl px-3 py-2.5 transition text-slate-700 hover:bg-slate-100">
                    <span class="inline-flex w-9 h-9 items-center justify-center rounded-xl bg-slate-100 text-slate-600 group-hover:bg-white">
                        <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                    </span>
                    <span x-show="sidebarOpen" x-transition class="font-medium">Inspeksi Rambu</span>
                </a>

                <a href="#"
                   class="group flex items-center gap-3 rounded-xl px-3 py-2.5 transition text-slate-700 hover:bg-slate-100">
                    <span class="inline-flex w-9 h-9 items-center justify-center rounded-xl bg-slate-100 text-slate-600 group-hover:bg-white">
                        <i data-lucide="archive" class="w-5 h-5"></i>
                    </span>
                    <span x-show="sidebarOpen" x-transition class="font-medium">Arsip Inventaris</span>
                </a>

                <a href="#"
                   class="group flex items-center gap-3 rounded-xl px-3 py-2.5 transition text-slate-700 hover:bg-slate-100">
                    <span class="inline-flex w-9 h-9 items-center justify-center rounded-xl bg-slate-100 text-slate-600 group-hover:bg-white">
                        <i data-lucide="upload" class="w-5 h-5"></i>
                    </span>
                    <span x-show="sidebarOpen" x-transition class="font-medium">Upload IK & SOP</span>
                </a>

                <!-- Dropdown: APD -->
                <div x-data="{ open: {{ $isApd ? 'true' : 'false' }} }" class="rounded-xl">
                    <button
                        @click="open=!open"
                        class="w-full group flex items-center gap-3 rounded-xl px-3 py-2.5 transition
                               {{ $isApd ? 'bg-red-50 text-red-700 ring-1 ring-red-100' : 'text-slate-700 hover:bg-slate-100' }}"
                    >
                        <span class="inline-flex w-9 h-9 items-center justify-center rounded-xl
                                     {{ $isApd ? 'bg-red-100 text-red-700' : 'bg-slate-100 text-slate-600 group-hover:bg-white' }}">
                            <i data-lucide="hard-hat" class="w-5 h-5"></i>
                        </span>
                        <span x-show="sidebarOpen" x-transition class="flex-1 text-left font-medium">Peminjaman & Jadwal APD</span>
                        <i data-lucide="chevron-down"
                           class="w-4 h-4 text-slate-400 transition"
                           :class="open ? 'rotate-180' : ''"
                           x-show="sidebarOpen" x-transition></i>
                    </button>

                    <div x-show="open" x-collapse x-cloak class="mt-1 pl-12 space-y-1">
                        <a href="#" class="block rounded-lg px-3 py-2 text-slate-600 hover:bg-slate-100 hover:text-slate-900">APD Karyawan</a>
                        <a href="#" class="block rounded-lg px-3 py-2 text-slate-600 hover:bg-slate-100 hover:text-slate-900">APD Tamu</a>
                        <a href="#" class="block rounded-lg px-3 py-2 text-slate-600 hover:bg-slate-100 hover:text-slate-900">APD Ahli Daya</a>
                    </div>
                </div>
                --}}

                @if($isSuperAdmin)
                    <div x-show="sidebarOpen" class="px-3 pb-1.5 pt-4 text-[10px] font-semibold uppercase tracking-[0.18em] text-white/50">Pengaturan</div>
                    <div x-show="!sidebarOpen" class="mx-auto my-3 h-px w-8 bg-white/20"></div>

                    <!-- Dropdown: Master Data -->
                    <div x-data="{ open: {{ $isUserPanel ? 'true' : 'false' }} }">
                        <button
                            @click="if (!sidebarOpen) { sidebarOpen = true; open = true } else { open = !open }"
                            :title="sidebarOpen ? '' : 'Master Data'"
                            class="group flex w-full items-center gap-3 rounded-xl py-2 pl-3 pr-3 transition
                                   {{ $isUserPanel ? 'bg-white/15 text-white' : 'text-white/85 hover:bg-white/10 hover:text-white' }}"
                        >
                            <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-xl
                                         {{ $isUserPanel ? 'bg-white text-red-700' : 'bg-white/10 text-white group-hover:bg-white/20' }}">
                                <i data-lucide="database" class="h-5 w-5"></i>
                            </span>
                            <span x-show="sidebarOpen" x-transition class="flex-1 text-left font-medium">Master Data</span>
                            <i data-lucide="chevron-down"
                               class="h-4 w-4 text-white/60 transition"
                               :class="open ? 'rotate-180' : ''"
                               x-show="sidebarOpen" x-transition></i>
                        </button>

                        <div x-show="open && sidebarOpen" x-collapse x-cloak class="mt-1 space-y-1 border-l border-white/15 pl-3 ml-7">
                            @foreach ($subMenu as $sub)
                                <a href="{{ route($sub['route']) }}"
                                   class="flex items-center gap-2 rounded-lg px-3 py-2 transition
                                          {{ $sub['active'] ? 'bg-white font-semibold text-red-700 shadow-sm' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                                    <i data-lucide="{{ $sub['icon'] }}" class="h-4 w-4 shrink-0"></i>
                                    <span class="truncate">{{ $sub['text'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </nav>
        </div>

        <!-- FOOTER -->
        <div class="border-t border-white/10 p-3">
            <div class="flex items-center gap-3 rounded-xl px-3 py-2 text-xs text-white/70" :class="sidebarOpen ? '' : 'justify-center px-0'">
                <i data-lucide="shield" class="h-4 w-4 shrink-0"></i>
                <span x-show="sidebarOpen" x-transition>Safety First</span>
            </div>
        </div>
    </aside>

    <!-- MAIN WRAPPER -->
    <div class="min-h-screen min-w-0 transition-all duration-300"
         :class="sidebarOpen ? 'lg:pl-[19.5rem]' : 'lg:pl-[6.5rem]'">

        <!-- TOPBAR -->
        <header class="topbar-hero sticky top-0 z-20 shadow-md shadow-slate-900/20">
            <div class="px-4 lg:px-6 py-3 sm:py-4 flex items-center justify-between gap-3">
                <!-- Left: mobile menu button -->
                <div class="flex min-w-0 items-center gap-3">
                    <button
                        @click="toggle()"
                        class="lg:hidden inline-flex items-center justify-center w-10 h-10 rounded-xl hover:bg-white/10 text-white transition"
                        aria-label="Open Menu"
                    >
                        <i data-lucide="menu" class="w-5 h-5"></i>
                    </button>

                    <div class="hidden lg:block">
                        <div class="text-sm text-slate-300">Selamat datang,</div>
                        <div class="font-semibold text-white">{{ Auth::user()->name ?? 'Admin' }}</div>
                    </div>
                </div>

                <!-- Right -->
                <div class="flex shrink-0 items-center gap-2 sm:gap-3">
                    @php
                        $adminNotifications = collect();
                        $adminUnreadNotifications = 0;

                        if (Auth::check() && Auth::user()?->isAdmin()) {
                            try {
                                $adminNotifications = \App\Models\AdminNotification::with('notification.user')
                                    ->where('recipient_id', Auth::id())
                                    ->latest()
                                    ->limit(8)
                                    ->get();

                                $adminUnreadNotifications = \App\Models\AdminNotification::where('recipient_id', Auth::id())
                                    ->whereNull('read_at')
                                    ->count();
                            } catch (\Throwable $e) {
                                $adminNotifications = collect();
                                $adminUnreadNotifications = 0;
                            }
                        }
                    @endphp

                    <!-- Notif -->
                    <div class="relative" x-data="{ open:false }">
                        <button
                            type="button"
                            @click="open=!open"
                            class="relative inline-flex items-center justify-center w-10 h-10 rounded-xl hover:bg-white/10 text-white transition"
                            aria-label="Pemberitahuan"
                        >
                            <i data-lucide="bell" class="w-5 h-5"></i>
                            @if($adminUnreadNotifications > 0)
                                <span class="absolute -top-0.5 -right-0.5 min-w-4 h-4 px-1 rounded-full bg-red-600 text-white text-[9px] leading-none font-bold flex items-center justify-center ring-2 ring-slate-900">
                                    {{ $adminUnreadNotifications > 99 ? '99+' : $adminUnreadNotifications }}
                                </span>
                            @endif
                        </button>

                        <div
                            x-show="open"
                            x-transition.origin.top.right
                            @click.away="open=false"
                            x-cloak
                            class="fixed left-3 right-3 top-16 mt-0 max-h-[calc(100vh-5rem)] bg-white border border-slate-200 rounded-2xl shadow-xl overflow-hidden z-50 sm:absolute sm:left-auto sm:right-0 sm:top-auto sm:mt-2 sm:w-96 sm:max-w-[calc(100vw-2rem)]"
                        >
                            <div class="flex items-center justify-between gap-3 px-4 py-3 border-b border-slate-200">
                                <div>
                                    <div class="text-sm font-bold text-slate-900">Pemberitahuan</div>
                                    <div class="text-[11px] text-slate-500">{{ $adminUnreadNotifications }} belum dibaca</div>
                                </div>

                                @if($adminUnreadNotifications > 0)
                                    <form method="POST" action="{{ route('admin.notifications.readAll') }}">
                                        @csrf
                                        <button type="submit" class="text-[11px] font-semibold text-red-600 hover:text-red-700">
                                            Tandai dibaca
                                        </button>
                                    </form>
                                @endif
                            </div>

                            <div class="max-h-[calc(100vh-10rem)] overflow-y-auto sm:max-h-96">
                                @forelse($adminNotifications as $item)
                                    @php
                                        $source = $item->notification;
                                        $submitter = $source?->user?->name ?? 'User';
                                        $type = strtoupper($source?->type ?? '-');
                                        $number = $source?->number ?? '-';
                                        $job = $source?->description ?? '-';
                                    @endphp
                                    <form method="POST" action="{{ route('admin.notifications.read', $item) }}">
                                        @csrf
                                        <button type="submit"
                                            class="w-full text-left px-4 py-3 border-b border-slate-100 hover:bg-slate-50 transition {{ $item->read_at ? 'bg-white' : 'bg-red-50/60' }}">
                                            <div class="flex items-start gap-3">
                                                <span class="mt-1 inline-flex w-8 h-8 items-center justify-center rounded-xl {{ $item->read_at ? 'bg-slate-100 text-slate-500' : 'bg-red-100 text-red-600' }}">
                                                    <i data-lucide="file-plus-2" class="w-4 h-4"></i>
                                                </span>
                                                <span class="min-w-0 flex-1 overflow-hidden">
                                                    <span class="flex items-center justify-between gap-2">
                                                        <span class="min-w-0 break-words text-sm font-semibold text-slate-900">{{ $item->title }}</span>
                                                        @if(!$item->read_at)
                                                            <span class="w-2 h-2 rounded-full bg-red-500 shrink-0"></span>
                                                        @endif
                                                    </span>
                                                    <span class="mt-1 block break-words text-xs text-slate-600">
                                                        {{ $submitter }} membuat pengajuan {{ $type }} {{ $number }}
                                                    </span>
                                                    <span class="mt-1 block break-words text-xs text-slate-500">
                                                        Pekerjaan: {{ $job }}
                                                    </span>
                                                    <span class="mt-1 block text-[11px] text-slate-400">
                                                        {{ $item->created_at?->diffForHumans() }}
                                                    </span>
                                                </span>
                                            </div>
                                        </button>
                                    </form>
                                @empty
                                    <div class="px-4 py-8 text-center text-sm text-slate-500">
                                        Belum ada pemberitahuan.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- Profile dropdown -->
                    <div class="relative" x-data="{ open:false }">
                        <button
                            @click="open=!open"
                            class="inline-flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-white/10 transition"
                        >
                            <span class="hidden sm:block text-sm font-medium text-white">
                                {{ Auth::user()->name ?? 'Admin' }}
                            </span>
                            <span class="inline-flex w-9 h-9 items-center justify-center rounded-xl bg-white/15 text-white ring-1 ring-white/20">
                                <i data-lucide="user" class="w-5 h-5"></i>
                            </span>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-white/70"></i>
                        </button>

                        <div
                            x-show="open"
                            x-transition.origin.top.right
                            @click.away="open=false"
                            x-cloak
                            class="absolute right-0 mt-2 w-52 bg-white border border-slate-200 rounded-2xl shadow-lg overflow-hidden z-50"
                        >
                            <a href="{{ route('profile.edit') }}"
                               class="flex items-center gap-2 px-4 py-3 text-sm text-slate-700 hover:bg-slate-50">
                                <i data-lucide="settings" class="w-4 h-4"></i> Edit Profile
                            </a>

                            <div class="h-px bg-slate-200"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center gap-2 px-4 py-3 text-sm text-slate-700 hover:bg-slate-50">
                                    <i data-lucide="log-out" class="w-4 h-4"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- CONTENT -->
        @if ($plain)
            {{-- plain: tanpa kartu pembungkus, isi halaman langsung di atas latar --}}
            <main class="min-w-0 overflow-x-hidden p-3 sm:p-4">
                {{ $slot }}
            </main>
        @else
            <main class="min-w-0 overflow-x-hidden p-3 sm:p-4 lg:p-6">
                <div class="min-w-0 overflow-hidden bg-white border border-slate-200 rounded-2xl shadow-sm p-3 sm:p-4 lg:p-6">
                    {{ $slot }}
                </div>
            </main>
        @endif
    </div>
</div>

<script>
    // render lucide icons
    lucide.createIcons();
</script>
</body>
</html>
