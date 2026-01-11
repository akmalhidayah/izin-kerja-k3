<x-admin-layout>
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Role & Permission Management</h1>
        @if (session('success'))
            <div class="bg-green-100 text-green-800 p-3 rounded mb-4 w-full">
                {{ session('success') }}
            </div>
        @endif
    </div>

    <div class="bg-white shadow rounded p-4 overflow-x-auto">
<form action="{{ route('admin.role_permission.index') }}" method="GET" class="mb-4 flex gap-2">
    <input type="text" name="search" placeholder="Cari nomor, deskripsi, vendor..." value="{{ request('search') }}"
        class="border rounded px-3 py-2 w-64 text-sm" />

    {{-- Filter status --}}
    <select name="status_filter" class="border rounded px-2 py-2 text-sm">
        <option value="">Semua Status</option>
        <option value="selesai" {{ request('status_filter') == 'selesai' ? 'selected' : '' }}>Selesai</option>
        <option value="belum" {{ request('status_filter') == 'belum' ? 'selected' : '' }}>Belum Selesai</option>
    </select>

    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded text-sm">Cari</button>

    @if(request('search') || request('status_filter'))
        <a href="{{ route('admin.role_permission.index') }}" class="text-sm text-red-500 self-center ml-2">Reset</a>
    @endif
</form>


@if(request('search'))
    <p class="text-sm text-gray-500 mb-2">
        Menampilkan hasil untuk pencarian: <strong>{{ request('search') }}</strong>
    </p>
@endif

        <table class="min-w-full text-sm text-left">
            <thead class="bg-gray-100 text-gray-700 uppercase tracking-wider">
                <tr>
                    <th class="px-4 py-2">NO</th>
                    <th class="px-4 py-2">Number</th>
                    <th class="px-4 py-2">Type</th>
                    <th class="px-4 py-2">Description</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Nama Vendor</th>
                    <th class="px-4 py-2">Ditangani Oleh</th>
                    <th class="px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($notifications as $notification)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $loop->iteration + ($notifications->currentPage() - 1) * $notifications->perPage() }}</td>
                        <td class="px-4 py-2">{{ $notification->number ?? '-' }}</td>
                        <td class="px-4 py-2">
                            <span class="inline-block px-2 py-1 rounded text-xs
                                {{ $notification->type == 'notif' ? 'bg-blue-100 text-blue-700' : ($notification->type == 'po' ? 'bg-purple-100 text-purple-700' : 'bg-green-100 text-green-700') }}">
                                {{ strtoupper($notification->type) }}
                            </span>
                        </td>
                        <td class="px-4 py-2">{{ $notification->description ?? '-' }}</td>
                      <td class="px-4 py-2">
    <div class="w-full bg-gray-200 rounded-full h-2">
        <div class="h-2 rounded-full {{ $notification->progress == 100 ? 'bg-green-500' : 'bg-yellow-500' }}" style="width: {{ $notification->progress }}%"></div>
    </div>
    <div class="text-xs text-gray-500 mt-1">{{ $notification->current_step }}/13</div>
</td>

                        <td class="px-4 py-2">
                            {{ $notification->user->name ?? '-' }}
                        </td>
                        <td class="px-4 py-2">
                            <form action="{{ route('admin.role_permission.update', $notification) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <div class="flex items-center space-x-2">
                                    <select name="assigned_admin_id" onchange="this.form.submit()" class="border rounded px-2 py-1 text-sm">
                                        <option value="">- Pilih Admin -</option>
                                        @foreach ($admins as $admin)
                                            <option value="{{ $admin->id }}" {{ $notification->assigned_admin_id == $admin->id ? 'selected' : '' }}>
                                                {{ $admin->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-save"></i>
                                    </button>
                                </div>
                            </form>
                        </td>
                      <td class="px-4 py-2 text-center">
    <form action="{{ route('admin.role_permission.destroy', $notification) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus notifikasi ini beserta seluruh data izin kerja yang terkait?')" class="inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="text-red-600 hover:text-red-800" title="Hapus Notifikasi">
            <i class="fas fa-trash-alt"></i> {{-- ikon fontawesome --}}
        </button>
    </form>
</td>

                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    </div>
</x-admin-layout>
