{{--
    Modal riwayat persetujuan. Sertakan sekali di dalam pembungkus
    <div x-data="stepHistoryModal()"> bersama tombol <x-admin.history-button>.
--}}
<div x-show="hist" x-cloak
     class="fixed inset-0 z-[80] flex items-end justify-center bg-slate-900/50 p-0 sm:items-center sm:p-4"
     @keydown.escape.window="hist = null">
    <div class="w-full max-w-2xl rounded-t-2xl bg-white shadow-2xl sm:rounded-2xl" @click.outside="hist = null">
        <div class="flex items-start justify-between gap-3 border-b border-gray-100 px-5 py-4">
            <div class="min-w-0">
                <h4 class="text-sm font-bold text-gray-900">Riwayat Persetujuan</h4>
                <p class="text-[11px] text-gray-500 truncate" x-text="hist ? hist.title : ''"></p>
            </div>
            <button type="button" @click="hist = null"
                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 hover:text-gray-700"
                    aria-label="Tutup">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6L6 18" /></svg>
            </button>
        </div>

        <div class="max-h-[70vh] overflow-y-auto px-5 py-4">
            <template x-if="hist && hist.rows.length === 0">
                <p class="py-6 text-center text-[11px] text-gray-500">Belum ada perubahan status pada step ini.</p>
            </template>

            <ol class="relative space-y-4 border-l border-gray-200 pl-5">
                <template x-for="(row, i) in (hist ? hist.rows : [])" :key="i">
                    <li class="relative">
                        <span class="absolute -left-[27px] top-1 h-3 w-3 rounded-full border-2 border-white"
                              :class="{
                                  'bg-green-500': row.ke_status === 'disetujui',
                                  'bg-yellow-500': row.ke_status === 'revisi',
                                  'bg-amber-500': row.ke_status === 'perlu_disetujui',
                                  'bg-gray-400': !['disetujui','revisi','perlu_disetujui'].includes(row.ke_status)
                              }"></span>
                        <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-[11px]">
                            <span class="font-semibold text-gray-800" x-text="row.waktu"></span>
                            <span class="text-gray-400">•</span>
                            <span class="text-gray-700" x-text="row.aktor"></span>
                            <template x-if="hist && hist.showStep">
                                <span class="rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-semibold text-gray-600" x-text="row.step_title"></span>
                            </template>
                        </div>
                        <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[11px]">
                            <span class="text-gray-500" x-text="row.aksi"></span>
                            <span class="text-gray-400">:</span>
                            <span class="rounded-full bg-gray-100 px-2 py-0.5 font-semibold text-gray-600" x-text="row.dari"></span>
                            <svg viewBox="0 0 24 24" class="h-3 w-3 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 6l6 6-6 6" /></svg>
                            <span class="rounded-full px-2 py-0.5 font-semibold"
                                  :class="{
                                      'bg-green-100 text-green-700': row.ke_status === 'disetujui',
                                      'bg-yellow-100 text-yellow-700': row.ke_status === 'revisi',
                                      'bg-amber-100 text-amber-700': row.ke_status === 'perlu_disetujui',
                                      'bg-gray-100 text-gray-700': !['disetujui','revisi','perlu_disetujui'].includes(row.ke_status)
                                  }" x-text="row.ke"></span>
                        </div>
                        <template x-if="row.catatan">
                            <p class="mt-1 rounded-lg border border-gray-100 bg-gray-50 px-3 py-1.5 text-[11px] text-gray-600" x-text="row.catatan"></p>
                        </template>
                    </li>
                </template>
            </ol>
        </div>
    </div>
</div>

<script>
    function stepHistoryModal() {
        return {
            hist: null,
            openHistory(json) {
                try {
                    const data = JSON.parse(json);
                    this.hist = { title: data.title || '', rows: data.rows || [], showStep: !!data.showStep };
                } catch (e) {
                    this.hist = { title: '', rows: [], showStep: false };
                }
            }
        };
    }
</script>
