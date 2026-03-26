<x-admin-layout>
    <div class="space-y-6">

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <h1 class="text-2xl font-bold text-gray-800">
                Role & Permission Management
            </h1>

            @if (session('success'))
                <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-2 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif
        </div>

        {{-- FILTER + SEARCH --}}
        <form action="{{ route('admin.role_permission.index') }}" method="GET"
            class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">

            {{-- SEARCH --}}
            <div class="flex gap-2 w-full md:w-auto">
                <input type="text" name="search"
                    placeholder="Cari nomor, vendor, deskripsi..."
                    value="{{ request('search') }}"
                    class="border px-3 py-2 rounded-xl text-sm w-full md:w-72 focus:ring focus:ring-blue-200">

                <button class="bg-blue-600 text-white px-4 py-2 rounded-xl text-sm hover:bg-blue-700">
                    <i class="fas fa-search"></i>
                </button>
            </div>

            {{-- FILTER --}}
            <div class="flex flex-wrap gap-2">

                {{-- STATUS --}}
                <select name="status_filter" class="border px-3 py-2 rounded-xl text-sm">
                    <option value="">Semua Status</option>
                    <option value="selesai" {{ request('status_filter') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="belum" {{ request('status_filter') == 'belum' ? 'selected' : '' }}>Belum</option>
                </select>

                {{-- USERTYPE --}}
                <select name="usertype" class="border px-3 py-2 rounded-xl text-sm">
                    <option value="">Semua User</option>
                    <option value="user" {{ request('usertype') == 'user' ? 'selected' : '' }}>Vendor</option>
                    <option value="pgo" {{ request('usertype') == 'pgo' ? 'selected' : '' }}>Karyawan</option>
                    <option value="admin" {{ request('usertype') == 'admin' ? 'selected' : '' }}>Admin K3</option>
                </select>

                {{-- RESET --}}
                @if(request()->hasAny(['search','status_filter','usertype']))
                    <a href="{{ route('admin.role_permission.index') }}"
                        class="text-sm text-red-500 px-2 py-2">
                        Reset
                    </a>
                @endif

            </div>
        </form>

        {{-- RESULT INFO --}}
        @if(request('search'))
            <p class="text-sm text-gray-500">
                Hasil pencarian untuk:
                <span class="font-semibold">{{ request('search') }}</span>
            </p>
        @endif

        {{-- TABLE CARD --}}
        <div class="bg-white shadow-xl rounded-2xl border p-4">

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-left">

                    {{-- HEADER --}}
                    <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3">No</th>
                            <th class="px-4 py-3">Number</th>
                            <th class="px-4 py-3">Type</th>
                            <th class="px-4 py-3">Description</th>
                            <th class="px-4 py-3">User</th>
                            <th class="px-4 py-3">Assigned Admin</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>

                    {{-- BODY --}}
                    <tbody class="divide-y divide-gray-200">

                        @forelse ($notifications as $notification)

                            <tr class="hover:bg-gray-50 transition">

                                {{-- NO --}}
                                <td class="px-4 py-3">
                                    {{ $loop->iteration + ($notifications->currentPage() - 1) * $notifications->perPage() }}
                                </td>

                                {{-- NUMBER --}}
                                <td class="px-4 py-3">
                                    {{ $notification->number ?? '-' }}
                                </td>

                                {{-- TYPE --}}
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium
                                        {{ $notification->type == 'notif' ? 'bg-blue-100 text-blue-700' :
                                           ($notification->type == 'po' ? 'bg-purple-100 text-purple-700' :
                                           'bg-green-100 text-green-700') }}">
                                        {{ strtoupper($notification->type) }}
                                    </span>
                                </td>

                                {{-- DESCRIPTION --}}
                                <td class="px-4 py-3 text-gray-600">
                                    {{ $notification->description ?? '-' }}
                                </td>


                                {{-- USER --}}
                                <td class="px-4 py-3">
                                    <div class="flex flex-col">
                                        <span>{{ $notification->user->name ?? '-' }}</span>
                                    </div>
                                </td>

                                {{-- ADMIN --}}
                                <td class="px-4 py-3">
                                    <form action="{{ route('admin.role_permission.update', $notification) }}" method="POST">
                                        @csrf
                                        @method('PATCH')

                                        <select name="assigned_admin_id"
                                            onchange="this.form.submit()"
                                            class="border px-2 py-1 rounded text-sm w-full">
                                            <option value="">- Pilih Admin -</option>
                                            @foreach ($admins as $admin)
                                                <option value="{{ $admin->id }}"
                                                    {{ $notification->assigned_admin_id == $admin->id ? 'selected' : '' }}>
                                                    {{ $admin->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>

                                {{-- AKSI --}}
                                <td class="px-4 py-3 text-center">
                                    <form action="{{ route('admin.role_permission.destroy', $notification) }}"
                                        method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus data ini?')">

                                        @csrf
                                        @method('DELETE')

                                        <button class="bg-red-100 text-red-600 px-2 py-1 rounded hover:bg-red-200 text-xs">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>

                            </tr>

                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-10 text-gray-400">
                                    <i class="fas fa-folder-open text-3xl mb-2"></i>
                                    <p>Tidak ada data ditemukan</p>
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            <div class="mt-4">
                {{ $notifications->withQueryString()->links() }}
            </div>

        </div>

    </div>
</x-admin-layout>