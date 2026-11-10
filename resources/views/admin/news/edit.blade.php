<x-admin-layout>
    <div class="max-w-2xl space-y-4">
        <h1 class="text-2xl font-bold text-gray-800">Edit Berita</h1>
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5">
            @include('admin.news._form', ['action' => route('admin.news.update', $item), 'item' => $item])
        </div>
    </div>
</x-admin-layout>
