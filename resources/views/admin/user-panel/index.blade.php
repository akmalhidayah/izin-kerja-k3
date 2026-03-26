<x-admin-layout>
    <div class="space-y-6">

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <h1 class="text-2xl font-bold text-gray-800">Manajemen User</h1>

            <a href="{{ route('admin.userpanel.create') }}"
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-xl shadow hover:bg-blue-700 text-sm">
                <i class="fas fa-plus mr-2"></i> Tambah User
            </a>
        </div>

        {{-- ALERT --}}
        @if (session('success'))
            <div class="bg-green-100 border border-green-300 text-green-800 p-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        {{-- SEARCH + FILTER --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">

            {{-- SEARCH --}}
            <form method="GET" class="flex items-center gap-2 w-full md:w-auto">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari nama atau email..."
                    class="border px-3 py-2 rounded-xl text-sm w-full md:w-64 focus:ring focus:ring-blue-200">

                <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded-xl text-sm hover:bg-blue-700">
                    <i class="fas fa-search"></i>
                </button>
            </form>

            {{-- FILTER --}}
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.userpanel.index') }}"
                    class="px-3 py-1 rounded-full text-sm {{ request('usertype') == null ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">
                    Semua
                </a>
                <a href="?usertype=user"
                    class="px-3 py-1 rounded-full text-sm {{ request('usertype') == 'user' ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">
                    Vendor
                </a>
                <a href="?usertype=pgo"
                    class="px-3 py-1 rounded-full text-sm {{ request('usertype') == 'pgo' ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">
                    Karyawan
                </a>
                <a href="?usertype=admin"
                    class="px-3 py-1 rounded-full text-sm {{ request('usertype') == 'admin' ? 'bg-blue-600 text-white' : 'bg-gray-200' }}">
                    K3
                </a>
            </div>

        </div>

        {{-- TABLE CARD --}}
        <div class="bg-white shadow-xl rounded-2xl border overflow-hidden">

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">

                    {{-- HEADER --}}
                    <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3 text-left">No</th>
                            <th class="px-4 py-3 text-left">User</th>
                            <th class="px-4 py-3 text-left">Email</th>
                            <th class="px-4 py-3 text-left">Usertype</th>
                            <th class="px-4 py-3 text-left">Role</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>

                    {{-- BODY --}}
                    <tbody class="divide-y divide-gray-200">

                        @forelse ($users as $user)
                            @php
                                $usertypeClass = match ($user->usertype) {
                                    'admin' => 'bg-red-100 text-red-600 border border-red-200',
                                    'pgo' => 'bg-yellow-100 text-yellow-700 border border-yellow-200',
                                    default => 'bg-green-100 text-green-600 border border-green-200',
                                };
                            @endphp

                            <tr class="hover:bg-gray-50 transition">

                                {{-- NO --}}
                                <td class="px-4 py-3">
                                    {{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}
                                </td>

                                {{-- USER --}}
                                <td class="px-4 py-3">
                                    <div class="flex flex-col">
                                        <span class="font-semibold text-gray-800">{{ $user->name }}</span>
                                    </div>
                                </td>

                                {{-- EMAIL --}}
                                <td class="px-4 py-3 text-gray-600">
                                    {{ $user->email }}
                                </td>

                                {{-- USERTYPE --}}
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $usertypeClass }}">
                                        @switch($user->usertype)
                                            @case('admin') K3 @break
                                            @case('pgo') Karyawan @break
                                            @case('user') Vendor @break
                                            @default {{ ucfirst($user->usertype) }}
                                        @endswitch
                                    </span>
                                </td>

                                {{-- ROLE --}}
                                <td class="px-4 py-3 text-gray-700">
                                    {{ $user->role->name ?? '-' }}
                                </td>

                                {{-- AKSI --}}
                                <td class="px-4 py-3 text-center">
                                    <div class="flex justify-center gap-2">

                                        <a href="{{ route('admin.userpanel.edit', $user) }}"
                                            class="bg-blue-100 text-blue-600 px-2 py-1 rounded hover:bg-blue-200 text-xs">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <form action="{{ route('admin.userpanel.destroy', $user) }}" method="POST"
                                            onsubmit="return confirm('Yakin ingin hapus user ini?')">
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                class="bg-red-100 text-red-600 px-2 py-1 rounded hover:bg-red-200 text-xs">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>

                                    </div>
                                </td>

                            </tr>

                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-10">
                                    <div class="flex flex-col items-center text-gray-400">
                                        <i class="fas fa-users text-4xl mb-3"></i>
                                        <p class="text-sm">Tidak ada data user</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            <div class="p-4 border-t">
                {{ $users->withQueryString()->links() }}
            </div>

        </div>

    </div>
</x-admin-layout>