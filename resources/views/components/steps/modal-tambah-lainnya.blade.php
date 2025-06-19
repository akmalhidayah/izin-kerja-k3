<div 
    x-show="activeModal === '{{ $id }}'" 
    x-cloak 
    class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center"
    aria-modal="true" role="dialog"
>
    <div 
        @click.away="activeModal = null" 
        class="bg-white p-6 rounded-lg shadow-lg w-full max-w-4xl overflow-y-auto max-h-[90vh]"
    >
        <h2 class="text-lg font-bold mb-4 text-gray-800 border-b pb-2">Tambah Jenis Working Permit Lain</h2>

        <div x-data="{
            selectedPermit: '',
            search: '',
            options: [
                { value: 'gaspanas', label: 'Izin Kerja Gas Panas' },
                { value: 'air', label: 'Izin Kerja di Air' },
                { value: 'ketinggian', label: 'Izin Kerja di Ketinggian' },
                { value: 'pengangkatan', label: 'Izin Pengangkatan Beban' },
                { value: 'penggalian', label: 'Izin Penggalian' },
                { value: 'beban', label: 'Izin Beban Berat' },
                { value: 'panas-risiko', label: 'Izin Risiko Panas' },
                { value: 'ruang-tertutup', label: 'Izin Ruang Tertutup' },
                { value: 'lifesaving', label: 'Izin Life Saving Appliance' },
                { value: 'perancah', label: 'Izin Perancah' },
                { value: 'procedures', label: 'Izin Prosedur Khusus' }
            ]
        }">
            <!-- Dropdown -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Jenis Working Permit</label>
                
                <input 
                    type="text" 
                    x-model="search" 
                    placeholder="Cari permit..." 
                    class="w-full border rounded px-3 py-2 mb-2 text-sm focus:ring focus:ring-blue-200"
                >

                <select x-model="selectedPermit" class="w-full border rounded p-2 text-sm">
                    <template x-for="option in options.filter(o => o.label.toLowerCase().includes(search.toLowerCase()))" :key="option.value">
                        <option :value="option.value" x-text="option.label"></option>
                    </template>
                </select>

                <div class="mt-2 text-sm font-semibold text-gray-600">
                    Dipilih: <span x-text="options.find(o => o.value === selectedPermit)?.label || '-'"></span>
                </div>
            </div>

            <!-- Dynamic Includes -->
<!-- Dynamic Includes -->
<div x-show="selectedPermit === 'gaspanas'" x-cloak>
    @include('components.permits.gaspanas', ['permit' => $permits['gaspanas'] ?? null])
</div>
<div x-show="selectedPermit === 'air'" x-cloak>
    @include('components.permits.air', ['permit' => $permits['air'] ?? null])
</div>
<div x-show="selectedPermit === 'ketinggian'" x-cloak>
    @include('components.permits.ketinggian', ['permit' => $permits['ketinggian'] ?? null])
</div>
<div x-show="selectedPermit === 'pengangkatan'" x-cloak>
    @include('components.permits.pengangkatan', ['permit' => $permits['pengangkatan'] ?? null])
</div>
<div x-show="selectedPermit === 'penggalian'" x-cloak>
    @include('components.permits.penggalian', ['permit' => $permits['penggalian'] ?? null])
</div>
<div x-show="selectedPermit === 'beban'" x-cloak>
    @include('components.permits.beban', ['permit' => $permits['beban'] ?? null])
</div>
<div x-show="selectedPermit === 'panas-risiko'" x-cloak>
    @include('components.permits.risiko-panas', ['permit' => $permits['panas-risiko'] ?? null])
</div>
<div x-show="selectedPermit === 'ruang-tertutup'" x-cloak>
    @include('components.permits.ruang-tertutup', ['permit' => $permits['ruang-tertutup'] ?? null])
</div>
<div x-show="selectedPermit === 'lifesaving'" x-cloak>
    @include('components.permits.lifesaving', ['permit' => $permits['lifesaving'] ?? null])
</div>
<div x-show="selectedPermit === 'perancah'" x-cloak>
    @include('components.permits.perancah', ['permit' => $permits['perancah'] ?? null])
</div>
<div x-show="selectedPermit === 'procedures'" x-cloak>
    @include('components.permits.procedures', ['permit' => $permits['procedures'] ?? null])
</div>
        </div>
        <!-- Tombol -->
        <div class="flex justify-end gap-2 mt-6">
            <button type="button" @click="activeModal = null" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 text-sm">
                Tutup
            </button>
        </div>
    </div>
</div>
