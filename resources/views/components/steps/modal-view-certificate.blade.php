<div x-data="{ openSIK: false }">
    <button @click="openSIK = true" class="mt-2 bg-red-600 hover:bg-red-700 text-white text-[10px] px-4 py-[5px] rounded-full transition">
        Lihat Sertifikat
    </button>

    <div x-show="openSIK" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" x-cloak>
        <div class="bg-white rounded-lg p-4 w-full max-w-2xl shadow-lg">
            <div class="flex justify-between items-center mb-2">
                <h2 class="text-lg font-semibold">Preview Surat Izin Kerja</h2>
                <button @click="openSIK = false" class="text-gray-500 hover:text-gray-700">&times;</button>
            </div>

            <div class="border p-4 rounded">
                @isset($filePath)
                    <embed src="{{ asset('storage/' . $filePath) }}" type="application/pdf" class="w-full h-96">
                @else
                    <p class="text-center text-sm text-gray-600 italic">File tidak tersedia.</p>
                @endisset
            </div>
        </div>
    </div>
</div>
