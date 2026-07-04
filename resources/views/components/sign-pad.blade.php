<style>
@media (max-width: 640px) {
    .wp-signature-button {
        position: relative;
        width: 2rem !important;
        min-width: 2rem !important;
        max-width: 2rem !important;
        height: 2rem !important;
        padding: 0 !important;
        font-size: 0 !important;
        line-height: 0 !important;
        overflow: hidden;
        border-radius: .5rem !important;
    }

    .wp-signature-button::before {
        content: "✎";
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        font-size: 1rem;
        line-height: 1;
    }
}
.sign-pad-modal {
    padding: 1rem;
}

.sign-pad-panel {
    max-height: calc(100dvh - 2rem);
    overflow-y: auto;
}

.sign-pad-canvas {
    height: 12rem;
}

@media (max-width: 640px) {
    .wp-signature-button::before {
        content: "\270E";
    }

    .sign-pad-modal {
        align-items: flex-start !important;
        padding: .75rem;
    }

    .sign-pad-panel {
        width: 100% !important;
        max-width: calc(100vw - 1.5rem) !important;
        max-height: calc(100dvh - 1.5rem);
        padding: .875rem !important;
        margin-top: .25rem;
        border-radius: .75rem !important;
        box-sizing: border-box;
    }

    .sign-pad-title {
        margin-bottom: .5rem !important;
        font-size: .95rem !important;
        line-height: 1.25;
    }

    .sign-pad-canvas {
        height: min(42dvh, 13rem) !important;
        min-height: 9rem;
    }

    .sign-pad-actions {
        margin-top: .75rem !important;
        align-items: center;
        gap: .5rem;
    }

    .sign-pad-action-buttons {
        display: flex;
        gap: .5rem;
    }

    .sign-pad-action-buttons button,
    .sign-pad-clear {
        font-size: .8rem !important;
        line-height: 1.2;
    }

    .sign-pad-action-buttons button {
        padding: .45rem .7rem !important;
    }
}

@media (max-width: 640px) and (max-height: 520px) {
    .sign-pad-canvas {
        height: 8rem !important;
        min-height: 8rem;
    }
}
</style>

<div id="signPadModal" class="sign-pad-modal fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="sign-pad-panel bg-white rounded-lg shadow-lg p-6 w-full max-w-lg" onclick="event.stopPropagation()">
        <h2 class="sign-pad-title text-lg font-bold mb-4">Tanda Tangan</h2>
        <canvas id="signaturePad" class="sign-pad-canvas w-full border rounded cursor-crosshair" style="touch-action: none;"></canvas>
        <input type="hidden" id="currentSignatureField">
        <div class="sign-pad-actions mt-4 flex justify-between">
            <button type="button" onclick="clearSignature()" class="sign-pad-clear text-sm text-red-600 hover:underline">Clear</button>
            <div class="sign-pad-action-buttons space-x-2">
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

function resolveSignatureSource(targetInput, existingSignature) {
    if (!existingSignature) {
        return '';
    }

    if (existingSignature.startsWith('data:image')) {
        return existingSignature;
    }

    if (targetInput.dataset.signatureUrl) {
        return targetInput.dataset.signatureUrl;
    }

    if (existingSignature.startsWith('storage/')) {
        return '/' + existingSignature;
    }

    return existingSignature;
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
            img.src = resolveSignatureSource(targetInput, existingSignature);
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
