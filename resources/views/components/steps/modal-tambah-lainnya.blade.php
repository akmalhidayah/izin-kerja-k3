@if ($notification ?? null)
<div 
    x-show="activeModal === '{{ $id }}'" 
    x-cloak 
    class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center wp-permit-modal"
    aria-modal="true" role="dialog"
>
    <div 
        @click.away="activeModal = null" 
        class="bg-white p-6 rounded-lg shadow-lg wp-permit-form w-full max-w-4xl overflow-y-auto max-h-[90vh]"
    >
        <h2 class="text-lg font-bold mb-4 text-gray-800 border-b pb-2">Tambah Jenis Working Permit Lain</h2>

        <div
            data-form-url-template="{{ route('working-permit.form', [
                'notification' => $notification->id,
                'type' => '__permit_type__',
            ]) }}"
            x-data="{
            selectedPermit: '',
            search: '',
            forms: {},
            loadedTypes: [],
            pending: {},
            errors: {},
            async loadPermit(type, urlTemplate) {
                if (!type) return;

                if (this.forms[type] || this.pending[type]) return;

                this.errors = { ...this.errors, [type]: '' };
                this.pending = { ...this.pending, [type]: true };

                try {
                    const separator = urlTemplate.includes('?') ? '&' : '?';
                    const url = urlTemplate.replace('__permit_type__', encodeURIComponent(type)) + separator + 'context=add';
                    const response = await fetch(url, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        credentials: 'same-origin',
                    });

                    if (!response.ok) throw new Error('Form tidak dapat dimuat.');

                    const html = await response.text();
                    this.forms = { ...this.forms, [type]: html };
                    this.loadedTypes.push(type);
                } catch (error) {
                    this.errors = {
                        ...this.errors,
                        [type]: error.message || 'Form tidak dapat dimuat.',
                    };
                } finally {
                    this.pending = { ...this.pending, [type]: false };
                }
            },
            options: [
                { value: 'gaspanas', label: 'Izin Kerja Gas Panas' },
                { value: 'air', label: 'Izin Kerja di Air' },
                { value: 'ketinggian', label: 'Izin Kerja di Ketinggian' },
                { value: 'pengangkatan', label: 'Izin Pengangkatan Beban' },
                { value: 'penggalian', label: 'Izin Penggalian' },
                { value: 'beban', label: 'Izin Beban Berat' },
                { value: 'panas-risiko', label: 'Izin Risiko Panas' },
                { value: 'ruang-tertutup', label: 'Izin Ruang Tertutup' },
                <!-- { value: 'lifesaving', label: 'Izin Life Saving Appliance' }, -->
                { value: 'perancah', label: 'Izin Perancah' },
                @if ($permits['umum'] ?? null)
                    { value: 'listrik-tegangan-tinggi', label: 'Izin Listrik Tegangan Tinggi' },
                @endif
                <!-- { value: 'procedures', label: 'Izin Prosedur Khusus' } -->
            ]
        }"
        >
            <!-- Dropdown -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Jenis Working Permit</label>
                
                <input 
                    type="text" 
                    x-model="search" 
                    placeholder="Cari permit..." 
                    class="w-full border rounded px-3 py-2 mb-2 text-sm focus:ring focus:ring-blue-200"
                >

                <select x-model="selectedPermit" @change="loadPermit(selectedPermit, $root.dataset.formUrlTemplate)" class="w-full border rounded p-2 text-sm">
                    <template x-for="option in options.filter(o => o.label.toLowerCase().includes(search.toLowerCase()))" :key="option.value">
                        <option :value="option.value" x-text="option.label"></option>
                    </template>
                </select>

                <div class="mt-2 text-sm font-semibold text-gray-600">
                    Dipilih: <span x-text="options.find(o => o.value === selectedPermit)?.label || '-'"></span>
                </div>
            </div>

            <!-- Dynamic Includes -->
@if ($eager ?? false)
<div x-show="selectedPermit === 'gaspanas'" x-cloak>
    @include('components.permits.gaspanas', [
        'permit' => $permits['gaspanas'] ?? null,
        'detail' => ($permits['gaspanas'] ?? null)?->detail,
        'closure' => ($permits['gaspanas'] ?? null)?->closure,
    ])
</div>
<div x-show="selectedPermit === 'air'" x-cloak>
    @include('components.permits.air', [
        'permit' => $permits['air'] ?? null,
        'detail' => ($permits['air'] ?? null)?->detail,
        'closure' => ($permits['air'] ?? null)?->closure,
    ])
</div>
<div x-show="selectedPermit === 'ketinggian'" x-cloak>
    @include('components.permits.ketinggian', [
        'permit' => $permits['ketinggian'] ?? null,
        'detail' => ($permits['ketinggian'] ?? null)?->detail,
        'closure' => ($permits['ketinggian'] ?? null)?->closure,
    ])
</div>
<div x-show="selectedPermit === 'pengangkatan'" x-cloak>
    @include('components.permits.pengangkatan', ['permit' => $permits['pengangkatan'] ?? null])
</div>
<div x-show="selectedPermit === 'listrik-tegangan-tinggi'" x-cloak>
    @include('components.permits.listrik-tegangan-tinggi', [
        'permit' => $permits['listrik-tegangan-tinggi'] ?? null,
        'signaturePrefix' => 'add-listrik-tegangan-tinggi',
    ])
</div>
<div x-show="selectedPermit === 'penggalian'" x-cloak>
    @include('components.permits.penggalian', [
        'permit' => $permits['penggalian'] ?? null,
        'detail' => ($permits['penggalian'] ?? null)?->detail,
        'closure' => ($permits['penggalian'] ?? null)?->closure,
    ])
</div>
<div x-show="selectedPermit === 'beban'" x-cloak>
    @include('components.permits.beban', [
        'permit' => $permits['beban'] ?? null,
        'detail' => ($permits['beban'] ?? null)?->detail,
        'closure' => ($permits['beban'] ?? null)?->closure,
    ])
</div>
<div x-show="selectedPermit === 'panas-risiko'" x-cloak>
    @include('components.permits.risiko-panas', [
        'permit' => $permits['risiko-panas'] ?? null,
        'detail' => ($permits['risiko-panas'] ?? null)?->detail,
        'closure' => ($permits['risiko-panas'] ?? null)?->closure,
    ])
</div>
<div x-show="selectedPermit === 'ruang-tertutup'" x-cloak>
    @include('components.permits.ruang-tertutup', [
        'permit' => $permits['ruang-tertutup'] ?? null,
        'detail' => ($permits['ruang-tertutup'] ?? null)?->detail,
        'closure' => ($permits['ruang-tertutup'] ?? null)?->closure,
    ])
</div>
<div x-show="selectedPermit === 'lifesaving'" x-cloak>
    @include('components.permits.lifesaving', ['permit' => $permits['lifesaving'] ?? null])
</div>
<div x-show="selectedPermit === 'perancah'" x-cloak>
    @include('components.permits.perancah', [
        'permit' => $permits['perancah'] ?? null,
        'detail' => ($permits['perancah'] ?? null)?->detail,
        'closure' => ($permits['perancah'] ?? null)?->closure,
    ])
</div>
<div x-show="selectedPermit === 'procedures'" x-cloak>
    @include('components.permits.procedures', ['permit' => $permits['procedures'] ?? null])
</div>
@else
<div x-show="pending[selectedPermit]" class="py-10 text-center text-sm text-gray-500">Memuat form...</div>
<div x-show="errors[selectedPermit]" class="rounded bg-red-50 p-3 text-sm text-red-700" x-text="errors[selectedPermit]"></div>
<template x-for="type in loadedTypes" :key="type">
    <div x-show="selectedPermit === type" x-html="forms[type]"></div>
</template>
@endif
        </div>
        <!-- Tombol -->
        <div class="flex justify-end gap-2 mt-6">
            <button type="button" @click="activeModal = null" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 text-sm">
                Tutup
            </button>
        </div>
    </div>
</div>
@endif
