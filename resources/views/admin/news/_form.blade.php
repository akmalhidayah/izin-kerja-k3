@php
    /** @var \App\Models\News|null $item */
    $item = $item ?? null;
@endphp

<form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="space-y-5">
    @csrf
    @if ($item)
        @method('PUT')
    @endif

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 p-3 rounded-lg text-sm">
            <p class="font-semibold mb-1">Data belum bisa disimpan:</p>
            <ul class="list-disc pl-5 space-y-0.5">
                @foreach ($errors->all() as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- JUDUL --}}
    <div>
        <label for="title" class="block text-sm font-semibold text-gray-700 mb-1">Judul</label>
        <input type="text" id="title" name="title" value="{{ old('title', $item?->title) }}" maxlength="200" required
               class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring focus:ring-blue-200"
               placeholder="Contoh: PT Semen Tonasa Raih Penghargaan K3">
    </div>

    {{-- DESKRIPSI --}}
    <div>
        <label for="description" class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi singkat</label>
        <textarea id="description" name="description" rows="3" maxlength="400" required
                  class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring focus:ring-blue-200"
                  placeholder="Ringkasan 1-2 kalimat yang tampil di kartu berita">{{ old('description', $item?->description) }}</textarea>
        <p class="text-xs text-gray-500 mt-1">Maksimal 400 karakter.</p>
    </div>

    {{-- LINK --}}
    <div>
        <label for="link" class="block text-sm font-semibold text-gray-700 mb-1">Link berita</label>
        <input type="url" id="link" name="link" value="{{ old('link', $item?->link) }}" maxlength="500" required
               class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring focus:ring-blue-200"
               placeholder="https://...">
        <p class="text-xs text-gray-500 mt-1">Alamat lengkap diawali http:// atau https://. Nama sumber di kartu diambil otomatis dari domainnya.</p>
    </div>

    {{-- FOTO --}}
    <div x-data="{ preview: @js($item ? $item->image_url : null) }">
        <label for="image" class="block text-sm font-semibold text-gray-700 mb-1">
            Foto @unless ($item)<span class="text-red-600">*</span>@endunless
        </label>
        <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp"
               @if (!$item) required @endif
               @change="const f = $event.target.files[0]; if (f) preview = URL.createObjectURL(f)"
               class="block w-full text-sm text-gray-600 file:mr-3 file:rounded-lg file:border-0 file:bg-blue-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100">
        <p class="text-xs text-gray-500 mt-1">
            JPG, PNG, atau WebP, maksimal 2 MB. Disarankan rasio 16:10 (contoh 1280x800) agar tidak terpotong.
            @if ($item) Kosongkan bila tidak ingin mengganti foto. @endif
        </p>
        <template x-if="preview">
            <img :src="preview" alt="Pratinjau foto" class="mt-3 w-full max-w-sm aspect-[16/10] object-cover rounded-xl border border-gray-200">
        </template>
    </div>

    {{-- STATUS --}}
    <label class="inline-flex items-center gap-2 text-sm text-gray-700">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300"
               {{ old('is_active', $item?->is_active ?? true) ? 'checked' : '' }}>
        Tampilkan di landing page
    </label>

    <div class="flex items-center gap-2 pt-2">
        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-xl shadow hover:bg-blue-700 text-sm">
            Simpan
        </button>
        <a href="{{ route('admin.news.index') }}" class="px-4 py-2 rounded-xl bg-gray-100 text-gray-700 hover:bg-gray-200 text-sm">Batal</a>
    </div>
</form>
