import './bootstrap';
import Alpine from 'alpinejs';
import SignaturePad from 'signature_pad'; // ⬅️ ini penting!

window.Alpine = Alpine;
window.SignaturePad = SignaturePad; // ⬅️ supaya bisa dipakai di komponen modal

// ⬇️ Tambah Alpine store di sini
Alpine.store('signatureModal', {
    show(role) {
        const modal = document.querySelector('[data-modal="signature"]');
        if (modal && modal.__x) {
            modal.__x.$data.openModal(role);
        } else {
            console.warn('Modal tidak ditemukan atau belum siap.');
        }
    }
});
window.formJSA = function (existing = []) {
    return {
        langkahKerja: existing.length ? existing : [{ langkah: '', bahaya: '', pengendalian: '' }],
        tambahRow() {
            this.langkahKerja.push({ langkah: '', bahaya: '', pengendalian: '' });
        },
        hapusRow(index) {
            this.langkahKerja.splice(index, 1);
        },
        serializeLangkah() {
            document.getElementById('langkah_kerja').value = JSON.stringify(this.langkahKerja);
        }
    };
};
window.isolasiData = function (existingListrik = [], existingNonListrik = []) {
    let listrik = Array.isArray(existingListrik) ? existingListrik : (existingListrik ? JSON.parse(existingListrik) : []);
    let nonListrik = Array.isArray(existingNonListrik) ? existingNonListrik : (existingNonListrik ? JSON.parse(existingNonListrik) : []);

    return {
        listrik: listrik.length ? listrik : [{ peralatan: '', nomor: '', tempat: '', locked: '', tested: '', signature: '' }],
        nonListrik: nonListrik.length ? nonListrik : [{ peralatan: '', jenis: '', tempat: '', locked: '', tested: '', signature: '' }],
        addListrik() {
            this.listrik.push({ peralatan: '', nomor: '', tempat: '', locked: '', tested: '', signature: '' });
        },
        addNonListrik() {
            this.nonListrik.push({ peralatan: '', jenis: '', tempat: '', locked: '', tested: '', signature: '' });
        }
    };
};

window.pekerjaData = function (existing = []) {
    let data = Array.isArray(existing) ? existing : (existing ? JSON.parse(existing) : []);
    return {
        pekerja: data.length ? data : [{ nama: '', signature: '' }],
        addBaris() {
            this.pekerja.push({ nama: '', signature: '' });
        }
    };
};
window.formKontraktor = function(tenagaKerjaInit = [], peralatanKerjaInit = [], apdInit = []) {
    return {
        tenagaKerja: tenagaKerjaInit && tenagaKerjaInit.length ? tenagaKerjaInit : [],
        peralatanKerja: peralatanKerjaInit && peralatanKerjaInit.length ? peralatanKerjaInit : [],
        apd: apdInit && apdInit.length ? apdInit : [],
        tambahTenagaKerja() { this.tenagaKerja.push({ nama: '', jumlah: '', satuan: '' }); },
        hapusTenagaKerja(index) { this.tenagaKerja.splice(index, 1); },
        tambahPeralatan() { this.peralatanKerja.push({ nama: '', jumlah: '', satuan: '' }); },
        hapusPeralatan(index) { this.peralatanKerja.splice(index, 1); },
        tambahApd() { this.apd.push({ nama: '', jumlah: '', satuan: '' }); },
        hapusApd(index) { this.apd.splice(index, 1); },
        serializeData() {
            document.getElementById('tenaga_kerja').value = JSON.stringify(this.tenagaKerja);
            document.getElementById('peralatan_kerja').value = JSON.stringify(this.peralatanKerja);
            document.getElementById('apd').value = JSON.stringify(this.apd);
        }
    }
}



Alpine.start();
