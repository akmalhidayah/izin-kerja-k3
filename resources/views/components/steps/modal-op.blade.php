<div 
    x-show="activeModal === '{{ $id }}'" 
    x-cloak 
    class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center"
>
    <div 
        class="bg-white p-6 rounded-lg shadow-md w-full max-w-md"
        @click.away="activeModal = null"
    >
        <div x-data="{ type: 'po' }">
            <h2 class="text-lg font-semibold mb-4">Input OP/SPK/Notification</h2>

            <form action="{{ route('notification.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Dropdown Pilihan -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Inputan</label>
                    <select x-model="type" name="type" class="w-full border rounded p-2">
                        <option value="po">Order Purchase</option>
                        <option value="spk">SPK</option>
                        <option value="notif">Notification</option>
                    </select>
                </div>

                <!-- Jika Order Purchase -->
                <div x-show="type === 'po'" x-cloak>
                    <input type="text" name="number" placeholder="Masukkan Nomor Purchase Order" class="mb-4 border rounded p-2 w-full">
                    <textarea name="description" placeholder="Deskripsi Pekerjaan" class="mb-4 border rounded p-2 w-full"></textarea>
                </div>

                <!-- Jika SPK -->
                <div x-show="type === 'spk'" x-cloak>
                    <input type="file" name="file" class="mb-4 border rounded p-2 w-full">
                    <textarea name="description" placeholder="Deskripsi Pekerjaan" class="mb-4 border rounded p-2 w-full"></textarea>
                </div>

                <!-- Jika Notification -->
                <div x-show="type === 'notif'" x-cloak>
                    <input type="text" name="number" placeholder="Masukkan Nomor Notification" class="mb-4 border rounded p-2 w-full">
                    <textarea name="description" placeholder="Deskripsi Pekerjaan" class="mb-4 border rounded p-2 w-full"></textarea>
                </div>

                <!-- Tombol Aksi -->
                <div class="flex justify-end gap-2">
                    <button type="button" @click="activeModal = null" class="px-3 py-1 text-sm bg-gray-300 rounded hover:bg-gray-400">Tutup</button>
                    <button type="submit" class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
