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

Alpine.start();
