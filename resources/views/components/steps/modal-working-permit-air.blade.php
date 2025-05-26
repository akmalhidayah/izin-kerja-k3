<div 
    x-show="activeModal === '{{ $id }}'" 
    x-cloak 
    class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center"
    aria-modal="true" role="dialog"
>
    <div 
        @click.away="activeModal = null" 
        class="bg-white p-6 rounded-lg shadow-lg w-full max-w-5xl overflow-y-auto max-h-[90vh]"
    >
        <h2 class="text-lg font-bold mb-4 text-gray-800 border-b pb-2">Edit Working Permit Air</h2>

        {{-- Include form permit Air --}}
        @include('components.permits.air', [
            'permit' => $permits['air'] ?? null,
            'notification' => $notification,
        ])

        <div class="flex justify-end gap-2 mt-6">
            <button type="button" @click="activeModal = null" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 text-sm">
                Tutup
            </button>
        </div>
    </div>
</div>
