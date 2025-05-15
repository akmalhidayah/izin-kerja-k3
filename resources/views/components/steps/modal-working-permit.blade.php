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
        <h2 class="text-lg font-bold mb-4 text-gray-800 border-b pb-2">Form Working Permit</h2>

        <div x-data="{
            selectedPermit: 'umum',
            search: '',
            options: [
                { value: 'umum', label: 'Izin Kerja Umum' },
                { value: 'gaspanas', label: 'Izin Kerja Pekerjaan dengan Material/Gas Panas' },
                { value: 'penggalian', label: 'Izin Kerja Penggalian' },
                { value: 'ruang-tertutup', label: 'Izin Kerja Bekerja di Ruang Tertutup/Terbatas' },
                { value: 'pengangkatan', label: 'Izin Kerja Rencana Pengangkatan' },
                { value: 'beban', label: 'Izin Kerja Mengangkat Beban' },
                { value: 'perancah', label: 'Izin Kerja Perancah' },
                { value: 'ketinggian', label: 'Izin Kerja Bekerja di Ketinggian' },
                { value: 'panas-risiko', label: 'Izin Kerja Pekerjaan Panas Berisiko Tinggi' },
                { value: 'air', label: 'Izin Kerja Bekerja di Air' },
                { value: 'lifesaving', label: 'Life Saving Talk' },
                { value: 'procedures', label: 'Safe Working Procedures' },
            ]
        }">
            <!-- Searchable Dropdown -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Jenis Working Permit</label>
                
                <input 
                    type="text" 
                    x-model="search" 
                    placeholder="Cari jenis permit..." 
                    class="w-full border rounded px-3 py-2 mb-2 text-sm focus:ring focus:ring-blue-200"
                >

                <select x-model="selectedPermit" class="w-full border rounded p-2 text-sm">
                    <template x-for="option in options.filter(o => o.label.toLowerCase().includes(search.toLowerCase()))" :key="option.value">
                        <option :value="option.value" x-text="option.label"></option>
                    </template>
                </select>

                <div class="mt-2 text-sm font-semibold text-gray-600">
                    Dipilih: <span x-text="options.find(o => o.value === selectedPermit)?.label"></span>
                </div>
            </div>

            <!-- Include Form Berdasarkan Pilihan -->
            <div x-show="selectedPermit === 'umum'">
                @include('components.permits.umum')
            </div>
            <div x-show="selectedPermit === 'lifesaving'">
                @include('components.permits.lifesaving')
            </div>
            <div x-show="selectedPermit === 'gaspanas'">
                @include('components.permits.gaspanas')
            </div>
            <div x-show="selectedPermit === 'penggalian'">
                @include('components.permits.penggalian')
            </div>
            <div x-show="selectedPermit === 'ruang-tertutup'">
                @include('components.permits.ruang-tertutup')
            </div>
            <div x-show="selectedPermit === 'pengangkatan'">
                @include('components.permits.pengangkatan')
            </div>
            <div x-show="selectedPermit === 'beban'">
                @include('components.permits.beban')
            </div>
            <div x-show="selectedPermit === 'perancah'">
                @include('components.permits.perancah')
            </div>
            <div x-show="selectedPermit === 'ketinggian'">
                @include('components.permits.ketinggian')
            </div>
            <div x-show="selectedPermit === 'panas-risiko'">
                @include('components.permits.panas-risiko')
            </div>
            <div x-show="selectedPermit === 'air'">
                @include('components.permits.air')
            </div>
            <div x-show="selectedPermit === 'procedures'">
                @include('components.permits.procedures')
            </div>
        </div>

        <!-- Tombol Aksi Modal -->
        <div class="flex justify-end gap-2 mt-6">
            <button type="button" @click="activeModal = null" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 text-sm">Tutup</button>
        </div>
    </div>
</div>
