<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Admin Panel') }}</title>

    <!-- Fonts & Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
    
    
    <!-- Tailwind & Alpine -->
    <link rel="stylesheet" href="{{ asset('build/assets/app-Dc2lHwHR.css') }}">
    <script src="{{ asset('build/assets/app-BWimq3f3.js') }}"></script> 
    <script src="https://cdn.jsdelivr.net/npm/alpinejs" defer></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>
<body class="bg-gray-100 font-sans text-gray-800">
    <div x-data="{ sidebarOpen: true }" class="flex h-screen">
        <!-- Sidebar -->
        <aside 
    :class="sidebarOpen ? 'w-64' : 'w-16'" 
    class="bg-white shadow-md transition-all duration-300 border-r border-gray-200 fixed h-full z-20 flex flex-col">

    <!-- Logo & Search sticky -->
    <div class="sticky top-0 bg-white z-30 p-4 border-b">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-2">
                <template x-if="sidebarOpen">
                    <img src="{{ asset('images/logo-st.png') }}" class="w-10 h-10" alt="Logo">
                </template>
                <span x-show="sidebarOpen" class="font-bold text-lg">K3 Admin</span>
            </div>
            <button @click="sidebarOpen = !sidebarOpen" class="text-gray-600 focus:outline-none">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        <div x-show="sidebarOpen">
            <input type="text" placeholder="Cari menu..." class="w-full px-3 py-2 text-sm border rounded bg-gray-100 focus:outline-none">
        </div>
    </div>

    <!-- Scrollable menu -->
    <div class="overflow-y-auto flex-1 p-4">
        <nav class="space-y-2 text-sm">
    <!-- Dashboard -->
    <a href="{{ route('admin.dashboard') }}"
       class="flex items-center px-3 py-2 rounded transition-all duration-200
              hover:bg-red-100 {{ request()->routeIs('admin.dashboard') ? 'bg-red-200 font-semibold text-red-700' : 'text-gray-800' }}">
        <i class="fas fa-home w-5 text-center"></i>
        <span x-show="sidebarOpen" class="ml-3">Dashboard</span>
    </a>

    <!-- Permintaan Izin Kerja -->
    <a href="{{ route('admin.permintaansik') }}"
       class="flex items-center px-3 py-2 rounded transition-all duration-200
              hover:bg-red-100 {{ request()->routeIs('admin.permintaansik') ? 'bg-red-200 font-semibold text-red-700' : 'text-gray-800' }}">
        <i class="fas fa-clipboard-list w-5 text-center"></i>
        <span x-show="sidebarOpen" class="ml-3">Permintaan Izin Kerja</span>
    </a>

    <!-- Checklist Inspeksi -->
    <div x-data="{ open: false }">
        <button @click="open = !open"
            class="flex items-center px-3 py-2 w-full rounded transition-all duration-200 hover:bg-red-100 text-gray-800 focus:outline-none">
            <i class="fas fa-check-square w-5 text-center"></i>
            <span x-show="sidebarOpen" class="ml-3 flex-1 text-left">Checklist Inspeksi</span>
            <i :class="open ? 'fas fa-chevron-up' : 'fas fa-chevron-down'" class="text-xs ml-auto"></i>
        </button>
        <div x-show="open" x-cloak class="pl-8 space-y-1">
            <a href="#" class="block px-3 py-2 rounded hover:bg-red-100">KTA</a>
            <a href="#" class="block px-3 py-2 rounded hover:bg-red-100">TTA</a>
            <a href="#" class="block px-3 py-2 rounded hover:bg-red-100">Apar</a>
            <a href="#" class="block px-3 py-2 rounded hover:bg-red-100">Hydrant</a>
            <a href="#" class="block px-3 py-2 rounded hover:bg-red-100">P3K</a>
            <a href="#" class="block px-3 py-2 rounded hover:bg-red-100">Damkar</a>
            <a href="#" class="block px-3 py-2 rounded hover:bg-red-100">Fire Alarm</a>
        </div>
    </div>

    <!-- Inspeksi Rambu -->
    <a href="#"
       class="flex items-center px-3 py-2 rounded transition-all duration-200 hover:bg-red-100 text-gray-800">
        <i class="fas fa-exclamation-triangle w-5 text-center"></i>
        <span x-show="sidebarOpen" class="ml-3">Inspeksi Rambu</span>
    </a>

    <!-- Arsip Inventaris -->
    <a href="#"
       class="flex items-center px-3 py-2 rounded transition-all duration-200 hover:bg-red-100 text-gray-800">
        <i class="fas fa-archive w-5 text-center"></i>
        <span x-show="sidebarOpen" class="ml-3">Arsip Inventaris</span>
    </a>

    <!-- Upload IK & SOP -->
    <a href="#"
       class="flex items-center px-3 py-2 rounded transition-all duration-200 hover:bg-red-100 text-gray-800">
        <i class="fas fa-upload w-5 text-center"></i>
        <span x-show="sidebarOpen" class="ml-3">Upload IK & SOP</span>
    </a>

    <!-- Peminjaman & Jadwal Pemberian APD -->
    <div x-data="{ open: false }">
        <button @click="open = !open"
            class="flex items-center px-3 py-2 w-full rounded transition-all duration-200 hover:bg-red-100 text-gray-800 focus:outline-none">
            <i class="fas fa-hard-hat w-5 text-center"></i>
            <span x-show="sidebarOpen" class="ml-3 flex-1 text-left">Peminjaman & Jadwal APD</span>
            <i :class="open ? 'fas fa-chevron-up' : 'fas fa-chevron-down'" class="text-xs ml-auto"></i>
        </button>
        <div x-show="open" x-cloak class="pl-8 space-y-1">
            <a href="#" class="block px-3 py-2 rounded hover:bg-red-100">APD Karyawan</a>
            <a href="#" class="block px-3 py-2 rounded hover:bg-red-100">APD Tamu</a>
            <a href="#" class="block px-3 py-2 rounded hover:bg-red-100">APD Ahli Daya</a>
        </div>
    </div>

    <!-- User Panel (Paling Bawah) -->
    <div x-data="{ open: false }">
        <button @click="open = !open"
            class="flex items-center px-3 py-2 w-full rounded transition-all duration-200 hover:bg-red-100 text-gray-800 focus:outline-none">
            <i class="fas fa-user-cog w-5 text-center"></i>
            <span x-show="sidebarOpen" class="ml-3 flex-1 text-left">User Panel</span>
            <i :class="open ? 'fas fa-chevron-up' : 'fas fa-chevron-down'" class="text-xs ml-auto"></i>
        </button>
        <div x-show="open" x-cloak class="pl-8 space-y-1">
            <a href="#" class="block px-3 py-2 rounded hover:bg-red-100">Manajemen User</a>
            <a href="#" class="block px-3 py-2 rounded hover:bg-red-100">Role & Permission</a>
        </div>
    </div>
</nav>
</aside>
<!-- Content -->
<div :class="sidebarOpen ? 'ml-64' : 'ml-16'" class="flex-1 flex flex-col transition-all duration-300">
  <!-- Topbar (Sticky + Notifikasi di kanan) -->
<header class="bg-white shadow px-6 py-4 flex items-center justify-between border-b sticky top-0 z-30">
    <!-- Left: Kosongin biar rata kanan -->
    <div></div>

    <!-- Right: Notifikasi & Profile -->
    <div class="flex items-center gap-4 ml-4">
        <!-- Icon Notifikasi -->
        <button class="relative text-gray-600 hover:text-red-600 focus:outline-none">
            <i class="fas fa-bell text-xl"></i>
            <!-- Optional badge -->
            {{-- <span class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">!</span> --}}
        </button>

        <!-- Nama & Dropdown Profil -->
        <span class="text-sm font-medium text-gray-800">{{ Auth::user()->name ?? 'Admin' }}</span>
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="text-gray-600 hover:text-red-600 focus:outline-none">
                <i class="fas fa-user-circle text-2xl"></i>
            </button>
            <div
                x-show="open"
                @click.away="open = false"
                class="absolute right-0 mt-2 w-40 bg-white shadow-md rounded-md overflow-hidden z-50"
            >
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="w-full px-4 py-2 text-left text-sm hover:bg-red-50 text-gray-800 hover:text-red-600"
                    >
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>

            <!-- Slot content -->
            <main class="flex-1 p-6 bg-gray-50">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
