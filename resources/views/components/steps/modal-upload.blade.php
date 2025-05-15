{{-- modal-upload.blade.php --}}
<div x-show="activeModal === '{{ $id }}'" x-cloak class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
    <div @click.away="activeModal = null" class="bg-white p-6 rounded-lg shadow-md w-full max-w-md">
        <h2 class="text-lg font-semibold mb-4">{{ $label ?? 'Upload Dokumen' }}</h2>
        <form action="{{ route('upload.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            {{-- Cegah error jika $notification tidak tersedia --}}
            <input type="hidden" name="notification_id" value="{{ $notification->id ?? '' }}">
            <input type="hidden" name="step_name" value="{{ $stepName ?? $label }}">
            <input type="file" name="file" class="mb-4 border rounded p-2 w-full" required>
            <div class="flex justify-end gap-2">
                <button type="button" @click="activeModal = null" class="px-3 py-1 text-sm bg-gray-300 rounded hover:bg-gray-400">Tutup</button>
                <button type="submit" class="px-3 py-1 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">Upload</button>
            </div>
        </form>
    </div>
</div>
