<div id="signPadModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-lg" onclick="event.stopPropagation()">
        <h2 class="text-lg font-bold mb-4">Tanda Tangan</h2>
        <canvas id="signaturePad" class="w-full border rounded h-48 cursor-crosshair" style="touch-action: none;"></canvas>
        <input type="hidden" id="currentSignatureField">
        <div class="mt-4 flex justify-between">
            <button type="button" onclick="clearSignature()" class="text-sm text-red-600 hover:underline">Clear</button>
            <div class="space-x-2">
                <button type="button" onclick="closeSignPad()" class="px-3 py-1 bg-gray-300 text-gray-800 rounded">Batal</button>
                <button type="button" onclick="saveSignature()" class="px-4 py-1 bg-blue-600 text-white rounded">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script>
let signaturePadInstance;

function findSignatureTarget(targetId) {
    const candidates = Array.from(document.querySelectorAll('[id]')).filter((element) => element.id === targetId);
    return candidates.find((element) => {
        let current = element.parentElement;
        while (current && current !== document.body) {
            const style = window.getComputedStyle(current);
            if (style.display === 'none' || style.visibility === 'hidden') {
                return false;
            }
            current = current.parentElement;
        }
        return true;
    }) || candidates[0] || null;
}

function resizeSignatureCanvas(canvas) {
    const ratio = Math.max(window.devicePixelRatio || 1, 1);
    const rect = canvas.getBoundingClientRect();
    const width = rect.width || canvas.offsetWidth || 1;
    const height = rect.height || canvas.offsetHeight || 192;

    canvas.width = width * ratio;
    canvas.height = height * ratio;

    const ctx = canvas.getContext('2d');
    ctx.setTransform(1, 0, 0, 1, 0, 0);
    ctx.scale(ratio, ratio);

    return { ctx, width, height };
}

function showSignatureNotice(type, title, text) {
    if (window.Swal) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: type,
            title: title,
            text: text,
            showConfirmButton: false,
            timer: 2800,
            timerProgressBar: true,
        });
        return;
    }

    const existingToast = document.getElementById('signatureToast');
    if (existingToast) {
        existingToast.remove();
    }

    const toast = document.createElement('div');
    toast.id = 'signatureToast';
    toast.style.position = 'fixed';
    toast.style.top = '16px';
    toast.style.right = '16px';
    toast.style.zIndex = '2147483647';
    toast.style.maxWidth = '360px';
    toast.style.borderRadius = '8px';
    toast.style.border = '1px solid ' + (type === 'success' ? '#bbf7d0' : '#fecaca');
    toast.style.background = type === 'success' ? '#f0fdf4' : '#fef2f2';
    toast.style.color = type === 'success' ? '#166534' : '#991b1b';
    toast.style.padding = '12px 16px';
    toast.style.fontSize = '14px';
    toast.style.lineHeight = '1.4';
    toast.style.boxShadow = '0 10px 24px rgba(15, 23, 42, 0.18)';
    toast.innerHTML = '<div style="font-weight: 700;">' + title + '</div><div style="margin-top: 4px;">' + text + '</div>';

    document.body.appendChild(toast);

    window.setTimeout(() => {
        toast.remove();
    }, 2800);
}

function openSignPad(targetId) {
    const targetInput = findSignatureTarget(targetId);
    if (!targetInput) {
        console.error('Input target tidak ditemukan:', targetId);
        alert('Input target tanda tangan tidak ditemukan!');
        return;
    }

    if (typeof SignaturePad === 'undefined') {
        console.error('SignaturePad library belum termuat.');
        alert('Komponen tanda tangan belum siap. Refresh halaman lalu coba lagi.');
        return;
    }

    const modal = document.getElementById('signPadModal');
    modal.classList.remove('hidden');
    document.getElementById('currentSignatureField').value = targetId;

    window.requestAnimationFrame(() => {
        const canvas = document.getElementById('signaturePad');

        if (signaturePadInstance && typeof signaturePadInstance.off === 'function') {
            signaturePadInstance.off();
        }

        const { ctx, width, height } = resizeSignatureCanvas(canvas);
        signaturePadInstance = new SignaturePad(canvas);

        const existingSignature = targetInput.value;
        if (existingSignature) {
            const img = new Image();
            img.src = existingSignature.startsWith('data:image')
                ? existingSignature
                : (targetInput.dataset.signatureUrl || existingSignature);
            img.onload = function () {
                ctx.drawImage(img, 0, 0, width, height);
            };
        }
    });
}

// FIX: Jadikan fungsi ini global agar bisa dipanggil dari luar
window.openSignPad = openSignPad;
window.closeSignPad = closeSignPad;
window.clearSignature = clearSignature;
window.saveSignature = saveSignature;
function closeSignPad() {
    document.getElementById('signPadModal').classList.add('hidden');
    if (signaturePadInstance) {
        signaturePadInstance.clear();
    }
}

function clearSignature() {
    if (signaturePadInstance) {
        signaturePadInstance.clear();
    }
}

function saveSignature() {
    if (!signaturePadInstance) {
        alert('Canvas tanda tangan belum siap.');
        return;
    }

    if (signaturePadInstance.isEmpty()) {
        alert('Tanda tangan belum diisi!');
        return;
    }
    const dataURL = signaturePadInstance.toDataURL();
    const targetInput = findSignatureTarget(document.getElementById('currentSignatureField').value);
    if (targetInput) {
        targetInput.value = dataURL;
        targetInput.dataset.signatureUrl = dataURL;
        targetInput.dispatchEvent(new Event('input', { bubbles: true }));
        targetInput.dispatchEvent(new Event('change', { bubbles: true }));
        console.log('Signature saved to input:', targetInput.id);
        showSignatureNotice(
            'success',
            'TTD tersimpan sementara',
            'Klik Simpan form untuk menyimpan permanen.'
        );
    } else {
        console.error('Input target tidak ditemukan!');
        alert('Input target tidak ditemukan!');
    }
    closeSignPad();
}
</script>
