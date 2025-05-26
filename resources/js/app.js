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


Alpine.start();
