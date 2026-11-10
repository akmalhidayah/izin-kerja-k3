<x-admin-layout>
    <div class="space-y-4">

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Upload Berita</h1>
                <p class="text-sm text-gray-500">Berita yang aktif tampil di landing page (6 terbaru).</p>
            </div>

            <a href="{{ route('admin.news.create') }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-xl shadow hover:bg-blue-700 text-sm">
                <i class="fas fa-plus mr-2"></i> Tambah Berita
            </a>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-300 text-green-800 p-3 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- SEARCH --}}
        <form method="GET" class="flex items-center gap-2 w-full md:w-auto">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul atau deskripsi..."
                   class="border px-3 py-2 rounded-xl text-sm w-full md:w-72 focus:ring focus:ring-blue-200">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-xl text-sm hover:bg-blue-700">
                <i class="fas fa-search"></i>
            </button>
        </form>

        {{-- TABEL --}}
        <div class="bg-white shadow-sm rounded-2xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-3 text-left">Foto</th>
                            <th class="px-4 py-3 text-left">Berita</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-left">Dibuat</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($items as $item)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 w-28">
                                    <img src="{{ $item->image_url }}" alt="" loading="lazy"
                                         class="w-24 aspect-[16/10] object-cover rounded-lg border border-gray-200 bg-gray-100">
                                </td>
                                <td class="px-4 py-3 max-w-md">
                                    <div class="font-semibold text-gray-800">{{ $item->title }}</div>
                                    <div class="text-xs text-gray-500 line-clamp-2">{{ $item->description }}</div>
                                    <a href="{{ $item->link }}" target="_blank" rel="noopener noreferrer"
                                       class="text-xs text-blue-600 hover:underline break-all">{{ $item->source_label ?: $item->link }}</a>
                                </td>
                                <td class="px-4 py-3">
                                    @if ($item->is_active)
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 border border-green-200">Tampil</span>
                                    @else
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">Disembunyikan</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ $item->created_at?->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('admin.news.edit', $item) }}"
                                           class="bg-blue-100 text-blue-600 px-2 py-1 rounded hover:bg-blue-200 text-xs" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.news.destroy', $item) }}" method="POST"
                                              onsubmit="return confirm('Yakin ingin menghapus berita ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="bg-red-100 text-red-600 px-2 py-1 rounded hover:bg-red-200 text-xs" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-10">
                                    <div class="flex flex-col items-center text-gray-400">
                                        <i class="fas fa-newspaper text-4xl mb-3"></i>
                                        <p class="text-sm">Belum ada berita. Klik "Tambah Berita" untuk membuat yang pertama.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t">
                {{ $items->links() }}
            </div>
        </div>
    </div>
</x-admin-layout>
