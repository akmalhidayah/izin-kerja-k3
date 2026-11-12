{{-- Ikon info kecil: membuka modal riwayat persetujuan (lihat admin/partials/step-history-modal). --}}
@props(['title', 'rows' => [], 'label' => 'Lihat riwayat', 'showStep' => false])
@php
    $count = count($rows);
    $payload = json_encode(['title' => $title, 'rows' => array_values($rows), 'showStep' => (bool) $showStep], JSON_UNESCAPED_UNICODE);
@endphp
<button type="button"
        data-history="{{ $payload }}"
        @click="openHistory($el.dataset.history)"
        {{ $attributes->merge(['class' => 'inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-400 transition hover:border-blue-300 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-200']) }}
        title="{{ $label }}{{ $count ? " ({$count})" : '' }}"
        aria-label="{{ $label }}">
    <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="9" />
        <path d="M12 11v5" />
        <path d="M12 8h.01" />
    </svg>
</button>
