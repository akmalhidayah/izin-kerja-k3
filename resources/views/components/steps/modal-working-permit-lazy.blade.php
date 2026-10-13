@if ($notification ?? null)
@php
    $maxWidth = $maxWidth ?? 'max-w-4xl';
@endphp

<div
    data-form-url="{{ route('working-permit.form', [
        'notification' => $notification->id,
        'type' => $type,
    ]) }}"
    x-data="{
        html: '',
        loading: false,
        loaded: false,
        error: '',
        async loadForm(url) {
            if (this.loaded || this.loading) return;

            this.loading = true;
            this.error = '';

            try {
                const response = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin',
                });

                if (!response.ok) throw new Error('Form tidak dapat dimuat.');

                this.html = await response.text();
                this.loaded = true;
            } catch (error) {
                this.error = error.message || 'Form tidak dapat dimuat.';
            } finally {
                this.loading = false;
            }
        }
    }"
    x-effect="if (activeModal === '{{ $id }}') loadForm($el.dataset.formUrl)"
    x-show="activeModal === '{{ $id }}'"
    x-cloak
    class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center wp-permit-modal"
    aria-modal="true"
    role="dialog"
>
    <div
        @click.away="activeModal = null"
        class="bg-white p-6 rounded-lg shadow-lg wp-permit-form w-full {{ $maxWidth }} overflow-y-auto max-h-[90vh]"
    >
        <h2 class="text-lg font-bold mb-4 text-gray-800 border-b pb-2">{{ $title }}</h2>

        <div x-show="loading" class="py-10 text-center text-sm text-gray-500">Memuat form...</div>
        <div x-show="error" class="rounded bg-red-50 p-3 text-sm text-red-700" x-text="error"></div>
        <div x-html="html"></div>

        <div class="flex justify-end gap-2 mt-6">
            <button type="button" @click="activeModal = null" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 text-sm">
                Tutup
            </button>
        </div>
    </div>
</div>
@endif
