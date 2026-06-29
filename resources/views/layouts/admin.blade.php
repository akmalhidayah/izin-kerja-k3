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
    </style>
</head>

<body class="bg-slate-50 text-slate-800 overflow-x-hidden">
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
    class="min-h-screen overflow-x-hidden"
>

    <!-- MOBILE OVERLAY -->
    <div
        x-show="mobileOpen"
        x-transition.opacity
        class="fixed inset-0 bg-black/40 z-30 lg:hidden"
        @click="closeMobile()"
        x-cloak
    ></div>

    <!-- SIDEBAR -->
    <aside
        class="fixed inset-y-0 left-0 z-40 bg-white border-r border-slate-200 shadow-sm flex flex-col
               transition-all duration-300"
        :class="[
            (mobileOpen ? 'translate-x-0' : '-translate-x-full') + ' lg:translate-x-0',
            (sidebarOpen ? 'lg:w-72' : 'lg:w-20'),
            'w-72'
        ]"
    >
        <!-- TOP: Brand -->
        <div class="sticky top-0 z-10 bg-white border-b border-slate-200">
            <div class="flex items-center justify-between gap-3 px-4 py-4">
                <div class="flex items-center gap-3 min-w-0">
                    <img src="{{ asset('images/logo-st.png') }}" class="w-10 h-10 rounded-xl object-contain bg-white" alt="Logo">
                    <div class="min-w-0" x-show="sidebarOpen" x-transition>
                        <div class="font-extrabold tracking-tight text-slate-900 leading-none">K3 Admin</div>
                        <div class="text-xs text-slate-500 truncate">PT. Semen Tonasa</div>
                    </div>
                </div>

                <!-- toggle button -->
                <button
                    @click="toggle()"
                    class="inline-flex items-center justify-center w-10 h-10 rounded-xl
                           hover:bg-slate-100 text-slate-600 active:scale-[0.98] transition"
                    aria-label="Toggle Sidebar"
                >
                    <i data-lucide="panel-left" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Search -->
            <div class="px-4 pb-4" x-show="sidebarOpen" x-transition>
                <div class="relative">
                    <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input
                        type="text"
                        placeholder="Cari menu..."
                        class="w-full pl-9 pr-3 py-2 text-sm rounded-xl border border-slate-200 bg-slate-50
                               focus:outline-none focus:ring-2 focus:ring-red-200 focus:border-red-300"
                    >
                </div>
            </div>
        </div>

        <!-- MENU -->
        <div class="flex-1 overflow-y-auto px-3 py-4">
            <nav class="space-y-1 text-sm">

                @php
                    $isSuperAdmin = Auth::user()
                        && Auth::user()?->isAdmin()
                        && in_array(Auth::user()->role->name ?? '', ['Super Admin']);

                    $isChecklist = request()->routeIs('admin.checklist.*');
                    $isApd       = request()->routeIs('admin.apd.*');
                    $isUserPanel = request()->routeIs('admin.userpanel.*') || request()->routeIs('admin.role_permission.*');
                @endphp

                <!-- Reusable classes via tailwind (inline) -->
                <!-- Item -->
                <a href="{{ route('admin.dashboard') }}"
                   class="group flex items-center gap-3 rounded-xl px-3 py-2.5 transition
                          {{ request()->routeIs('admin.dashboard') ? 'bg-red-50 text-red-700 ring-1 ring-red-100' : 'text-slate-700 hover:bg-slate-100' }}">
                    <span class="inline-flex w-9 h-9 items-center justify-center rounded-xl
                                 {{ request()->routeIs('admin.dashboard') ? 'bg-red-100 text-red-700' : 'bg-slate-100 text-slate-600 group-hover:bg-white' }}">
                        <i data-lucide="home" class="w-5 h-5"></i>
                    </span>
                    <span x-show="sidebarOpen" x-transition class="font-medium">Dashboard</span>
                </a>

                <a href="{{ route('admin.permintaansik') }}"
                   class="group flex items-center gap-3 rounded-xl px-3 py-2.5 transition
                          {{ request()->routeIs('admin.permintaansik') ? 'bg-red-50 text-red-700 ring-1 ring-red-100' : 'text-slate-700 hover:bg-slate-100' }}">
                    <span class="inline-flex w-9 h-9 items-center justify-center rounded-xl
                                 {{ request()->routeIs('admin.permintaansik') ? 'bg-red-100 text-red-700' : 'bg-slate-100 text-slate-600 group-hover:bg-white' }}">
                        <i data-lucide="clipboard-list" class="w-5 h-5"></i>
                    </span>
                    <span x-show="sidebarOpen" x-transition class="font-medium">Permintaan SIK Vendor</span>
                </a>

                <a href="{{ url('admin/permintaansikpgo') }}"
                   class="group flex items-center gap-3 rounded-xl px-3 py-2.5 transition
                          {{ request()->is('admin/permintaansikpgo*') ? 'bg-amber-50 text-amber-700 ring-1 ring-amber-100' : 'text-slate-700 hover:bg-slate-100' }}">
                    <span class="inline-flex w-9 h-9 items-center justify-center rounded-xl
                                 {{ request()->is('admin/permintaansikpgo*') ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-600 group-hover:bg-white' }}">
                        <i data-lucide="clipboard-check" class="w-5 h-5"></i>
                    </span>
                    <span x-show="sidebarOpen" x-transition class="font-medium">Permintaan SIK Karyawan</span>
                </a>

                @if($isSuperAdmin)
                    <a href="{{ route('admin.approvesik.index') }}"
                       class="group flex items-center gap-3 rounded-xl px-3 py-2.5 transition
                              {{ request()->routeIs('admin.approvesik.index') ? 'bg-red-50 text-red-700 ring-1 ring-red-100' : 'text-slate-700 hover:bg-slate-100' }}">
                        <span class="inline-flex w-9 h-9 items-center justify-center rounded-xl
                                     {{ request()->routeIs('admin.approvesik.index') ? 'bg-red-100 text-red-700' : 'bg-slate-100 text-slate-600 group-hover:bg-white' }}">
                            <i data-lucide="file-signature" class="w-5 h-5"></i>
                        </span>
                        <span x-show="sidebarOpen" x-transition class="font-medium">Approve SIK</span>
                    </a>
                @endif

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

                @if($isSuperAdmin)
                    <!-- Dropdown: User Panel -->
                    <div x-data="{ open: {{ $isUserPanel ? 'true' : 'false' }} }" class="rounded-xl">
                        <button
                            @click="open=!open"
                            class="w-full group flex items-center gap-3 rounded-xl px-3 py-2.5 transition
                                   {{ $isUserPanel ? 'bg-red-50 text-red-700 ring-1 ring-red-100' : 'text-slate-700 hover:bg-slate-100' }}"
                        >
                            <span class="inline-flex w-9 h-9 items-center justify-center rounded-xl
                                         {{ $isUserPanel ? 'bg-red-100 text-red-700' : 'bg-slate-100 text-slate-600 group-hover:bg-white' }}">
                                <i data-lucide="users" class="w-5 h-5"></i>
                            </span>
                            <span x-show="sidebarOpen" x-transition class="flex-1 text-left font-medium">User Panel</span>
                            <i data-lucide="chevron-down"
                               class="w-4 h-4 text-slate-400 transition"
                               :class="open ? 'rotate-180' : ''"
                               x-show="sidebarOpen" x-transition></i>
                        </button>

                        <div x-show="open" x-collapse x-cloak class="mt-1 pl-12 space-y-1">
                            <a href="{{ route('admin.userpanel.index') }}"
                               class="block rounded-lg px-3 py-2 text-slate-600 hover:bg-slate-100 hover:text-slate-900">
                                Manajemen User
                            </a>
                            <a href="{{ route('admin.role_permission.index') }}"
                               class="block rounded-lg px-3 py-2 text-slate-600 hover:bg-slate-100 hover:text-slate-900">
                                Role & Permission
                            </a>
                        </div>
                    </div>
                @endif
            </nav>
        </div>

        <!-- FOOTER (optional) -->
        <div class="border-t border-slate-200 p-3">
            <div class="flex items-center gap-3 rounded-xl px-3 py-2 text-xs text-slate-500">
                <i data-lucide="shield" class="w-4 h-4"></i>
                <span x-show="sidebarOpen" x-transition>Safety First</span>
            </div>
        </div>
    </aside>

    <!-- MAIN WRAPPER -->
    <div class="min-h-screen min-w-0 transition-all duration-300"
         :class="sidebarOpen ? 'lg:pl-72' : 'lg:pl-20'">

        <!-- TOPBAR -->
        <header class="sticky top-0 z-20 bg-white/80 backdrop-blur border-b border-slate-200">
            <div class="px-4 lg:px-6 py-3 sm:py-4 flex items-center justify-between gap-3">
                <!-- Left: mobile menu button -->
                <div class="flex min-w-0 items-center gap-3">
                    <button
                        @click="toggle()"
                        class="lg:hidden inline-flex items-center justify-center w-10 h-10 rounded-xl hover:bg-slate-100 text-slate-700 transition"
                        aria-label="Open Menu"
                    >
                        <i data-lucide="menu" class="w-5 h-5"></i>
                    </button>

                    <div class="hidden lg:block">
                        <div class="text-sm text-slate-500">Selamat datang,</div>
                        <div class="font-semibold text-slate-900">{{ Auth::user()->name ?? 'Admin' }}</div>
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
                            class="relative inline-flex items-center justify-center w-10 h-10 rounded-xl hover:bg-slate-100 text-slate-700 transition"
                            aria-label="Pemberitahuan"
                        >
                            <i data-lucide="bell" class="w-5 h-5"></i>
                            @if($adminUnreadNotifications > 0)
                                <span class="absolute -top-0.5 -right-0.5 min-w-4 h-4 px-1 rounded-full bg-red-600 text-white text-[9px] leading-none font-bold flex items-center justify-center ring-2 ring-white">
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
                            class="inline-flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-slate-100 transition"
                        >
                            <span class="hidden sm:block text-sm font-medium text-slate-800">
                                {{ Auth::user()->name ?? 'Admin' }}
                            </span>
                            <span class="inline-flex w-9 h-9 items-center justify-center rounded-xl bg-slate-100 text-slate-700">
                                <i data-lucide="user" class="w-5 h-5"></i>
                            </span>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400"></i>
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
        <main class="min-w-0 overflow-x-hidden p-3 sm:p-4 lg:p-6">
            <div class="min-w-0 overflow-hidden bg-white border border-slate-200 rounded-2xl shadow-sm p-3 sm:p-4 lg:p-6">
                {{ $slot }}
            </div>
        </main>
    </div>
</div>

<script>
    // render lucide icons
    lucide.createIcons();
</script>
</body>
</html>
