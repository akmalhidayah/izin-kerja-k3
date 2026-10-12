<div
    x-show="activeModal === '{{ $id }}'"
    x-cloak
    class="wp-permit-modal fixed inset-0 z-50 flex items-center justify-center bg-black/50"
    aria-modal="true"
    role="dialog"
>
    <div @click.away="activeModal = null" class="wp-permit-form max-h-[90vh] w-full max-w-5xl overflow-y-auto rounded-lg bg-white p-6 shadow-lg">
        <h2 class="mb-4 border-b pb-2 text-lg font-bold text-gray-800">{{ $label ?? 'Edit Permit Listrik Tegangan Tinggi' }}</h2>

        @include('components.permits.listrik-tegangan-tinggi', [
            'permit' => $permitListrikTeganganTinggi ?? ($permits['listrik-tegangan-tinggi'] ?? null),
            'signaturePrefix' => $id,
        ])

        <div class="mt-6 flex justify-end">
            <button type="button" @click="activeModal = null" class="rounded bg-gray-300 px-4 py-2 text-sm hover:bg-gray-400">Tutup</button>
        </div>
    </div>
</div>
