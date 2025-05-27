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
                    <!-- <th class="px-4 py-2">Aksi</th> -->
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
                            <span class="inline-block px-2 py-1 rounded text-xs
                                {{ $notification->status == 'menunggu' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700' }}">
                                {{ ucfirst($notification->status) }}
                            </span>
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
                        <!-- <td class="px-4 py-2 text-center">
                            <form action="{{ route('admin.role_permission.destroy', $notification) }}" method="POST" onsubmit="return confirm('Yakin?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td> -->
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    </div>
</x-admin-layout>
