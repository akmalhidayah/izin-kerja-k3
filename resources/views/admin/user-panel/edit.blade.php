<x-admin-layout>
    <h1 class="text-2xl font-bold mb-4">Edit User</h1>

    <form action="{{ route('admin.userpanel.update', $user) }}" method="POST" class="space-y-4">

        @csrf
        @method('PATCH')

        <div>
            <label class="block">Nama</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full border p-2 rounded" required>
        </div>
        <div>
            <label class="block">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full border p-2 rounded" required>
        </div>
        <div>
            <label class="block">Usertype</label>
            <select name="usertype" class="w-full border p-2 rounded" required>
                <option value="user" {{ old('usertype', $user->usertype) == 'user' ? 'selected' : '' }}>Vendor</option>
                <option value="pgo" {{ old('usertype', $user->usertype) == 'pgo' ? 'selected' : '' }}>User Karyawan</option>
                <option value="admin" {{ old('usertype', $user->usertype) == 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
        </div>
        <div>
            <label class="block">Role</label>
            <select name="role_id" class="w-full border p-2 rounded">
                <option value="">- Pilih Role -</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <button class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
    </form>
</x-admin-layout>
