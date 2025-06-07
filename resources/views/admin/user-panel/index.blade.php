<x-admin-layout>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Manajemen User</h1>
        <!-- <a href="{{ route('admin.userpanel.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            <i class="fas fa-plus mr-2"></i> Tambah User
        </a> -->
    </div>

    @if (session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="flex items-center space-x-3 mb-4">
        <span class="font-semibold">Filter Usertype:</span>
        <a href="{{ route('admin.userpanel.index') }}" class="px-3 py-1 rounded-full text-sm {{ request('usertype') == null ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">
            Semua
        </a>
        <a href="{{ route('admin.userpanel.index', ['usertype' => 'user']) }}" class="px-3 py-1 rounded-full text-sm {{ request('usertype') == 'user' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">
            Vendor
        </a>
        <a href="{{ route('admin.userpanel.index', ['usertype' => 'admin']) }}" class="px-3 py-1 rounded-full text-sm {{ request('usertype') == 'admin' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">
            Admin
        </a>
    </div>

    <div class="bg-white shadow-lg rounded-xl overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-4 py-2 text-left">No</th>
                    <th class="px-4 py-2 text-left">Nama</th>
                    <th class="px-4 py-2 text-left">Email</th>
                    <th class="px-4 py-2 text-left">Usertype</th>
                    <th class="px-4 py-2 text-left">Role</th>
                    <th class="px-4 py-2 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                        <td class="px-4 py-2">{{ $user->name }}</td>
                        <td class="px-4 py-2">{{ $user->email }}</td>
                        <td class="px-4 py-2">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $user->usertype == 'admin' ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600' }}">
                                {{ ucfirst($user->usertype) }}
                            </span>
                        </td>
                        <td class="px-4 py-2">{{ $user->role->name ?? '-' }}</td>
                        <td class="px-4 py-2 text-center">
                            <div class="flex justify-center items-center space-x-2">
                                <a href="{{ route('admin.userpanel.edit', $user) }}" class="text-blue-600 hover:text-blue-800" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.userpanel.destroy', $user) }}" method="POST" onsubmit="return confirm('Yakin?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-gray-500 py-4">Tidak ada user.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4">
            {{ $users->withQueryString()->links() }}
        </div>
    </div>
</x-admin-layout>
