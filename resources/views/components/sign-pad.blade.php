<div id="signPadModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-lg" onclick="event.stopPropagation()">
        <h2 class="text-lg font-bold mb-4">Tanda Tangan</h2>
        <canvas id="signaturePad" class="w-full border rounded h-48"></canvas>
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

const signatureSignedButtonClass = 'mt-1 inline-flex items-center justify-center rounded border border-blue-200 bg-blue-50 px-2 py-0.5 text-[10px] font-semibold text-blue-700 hover:bg-blue-100';
const signatureEmptyButtonClass = 'text-blue-600 underline text-xs';

function hasSignatureValue(value) {
    const normalized = String(value || '').trim();
    return normalized !== '' && !['-', '0', 'null', 'undefined'].includes(normalized.toLowerCase());
}

function normalizeSignatureUrl(value) {
    const normalized = String(value || '').trim();
    if (!hasSignatureValue(normalized)) return '';
    if (/^(data:image|https?:\/\/|blob:|\/)/i.test(normalized)) return normalized;
    return '/' + normalized.replace(/^\/+/, '');
}

function isSignatureInput(input) {
    if (!input || input.id === 'currentSignatureField') return false;
    const identity = `${input.id || ''} ${input.name || ''}`.toLowerCase();
    if (identity.includes('clear_all_signatures')) return false;

    return input.type === 'hidden' && /(signature|sign|paraf|ttd)/.test(identity);
}

function buttonCallsSignPad(button) {
    if (button.dataset.signatureAutoButton === 'true') return true;

    return [
        button.getAttribute('onclick'),
        button.getAttribute('@click'),
        button.getAttribute('x-on:click'),
        button.getAttribute('x-on:click.prevent')
    ].some((value) => value && value.includes('openSignPad'));
}

function buttonTargetsInput(button, inputId) {
    return [
        button.getAttribute('onclick'),
        button.getAttribute('@click'),
        button.getAttribute('x-on:click'),
        button.getAttribute('x-on:click.prevent')
    ].some((value) => value && value.includes(inputId));
}

function findSignatureButtonForInput(input) {
    const exact = Array.from(document.querySelectorAll('button')).find((button) => buttonTargetsInput(button, input.id));
    if (exact) return exact;

    const container = input.closest('td, .signature-field, .signature-cell, div') || input.parentElement;
    if (!container) return null;

    const localButtons = Array.from(container.querySelectorAll('button')).filter(buttonCallsSignPad);
    return localButtons.length === 1 ? localButtons[0] : null;
}

function syncSignatureButton(button, hasSignature) {
    if (!button) return;
    const nextClass = hasSignature ? signatureSignedButtonClass : signatureEmptyButtonClass;
    const nextText = hasSignature ? 'Ubah TTD' : 'Tanda Tangan';

    if (button.className !== nextClass) button.className = nextClass;
    if (button.textContent.trim() !== nextText) button.textContent = nextText;
}

function createSignatureButton(input, hasSignature) {
    const button = document.createElement('button');
    button.type = 'button';
    button.dataset.signatureAutoButton = 'true';
    button.addEventListener('click', () => openSignPad(input.id));
    syncSignatureButton(button, hasSignature);
    return button;
}

function syncSignaturePreview(input) {
    const container = input.closest('td, .signature-field, .signature-cell, div') || input.parentElement;
    if (!container || !hasSignatureValue(input.value)) return;

    const source = input.dataset.signatureUrl || normalizeSignatureUrl(input.value);
    if (!source) return;

    let image = container.querySelector('img[data-signature-preview="true"], img[alt*="Tanda Tangan"], img[alt*="TTD"], img');
    if (!image) {
        image = document.createElement('img');
        image.alt = 'Tanda Tangan';
        image.dataset.signaturePreview = 'true';
        image.className = 'h-20 mx-auto mb-1';
        container.insertBefore(image, input);
    }

    image.src = source;
}

function syncSignatureControls(root = document) {
    const inputs = Array.from(root.querySelectorAll('input[type="hidden"][id]')).filter(isSignatureInput);

    inputs.forEach((input) => {
        const hasSignature = hasSignatureValue(input.value);
        if (hasSignature && !input.dataset.signatureUrl) {
            input.dataset.signatureUrl = normalizeSignatureUrl(input.value);
        }

        syncSignaturePreview(input);

        let button = findSignatureButtonForInput(input);
        if (!button) {
            const container = input.closest('td, .signature-field, .signature-cell, div') || input.parentElement;
            const preview = container ? container.querySelector('img[data-signature-preview="true"], img[alt*="Tanda Tangan"], img[alt*="TTD"], img') : null;
            button = createSignatureButton(input, hasSignature);
            if (preview) {
                preview.insertAdjacentElement('afterend', button);
            } else {
                input.insertAdjacentElement('afterend', button);
            }
        } else {
            syncSignatureButton(button, hasSignature);
        }
    });
}

function findSignatureTarget(targetId) {
    const escapedId = window.CSS && CSS.escape ? CSS.escape(targetId) : targetId.replace(/"/g, '\\"');
    const candidates = Array.from(document.querySelectorAll('[id="' + escapedId + '"]'));
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

function openSignPad(targetId) {
    const targetInput = findSignatureTarget(targetId);
    if (!targetInput) {
        console.error('Input target tidak ditemukan:', targetId);
        alert('Input target tanda tangan tidak ditemukan!');
        return;
    }

    document.getElementById('signPadModal').classList.remove('hidden');
    document.getElementById('currentSignatureField').value = targetId;

    const canvas = document.getElementById('signaturePad');
    const ratio = Math.max(window.devicePixelRatio || 1, 1);
    canvas.width = canvas.offsetWidth * ratio;
    canvas.height = canvas.offsetHeight * ratio;
    const ctx = canvas.getContext('2d');
    ctx.scale(ratio, ratio);

    signaturePadInstance = new SignaturePad(canvas);

    const existingSignature = targetInput.value;
    if (hasSignatureValue(existingSignature)) {
        const img = new Image();
        img.src = existingSignature.startsWith('data:image')
            ? existingSignature
            : (targetInput.dataset.signatureUrl || normalizeSignatureUrl(existingSignature));
        img.onload = function () {
            ctx.drawImage(img, 0, 0, canvas.width / ratio, canvas.height / ratio);
        };
    }
}

// FIX: Jadikan fungsi ini global agar bisa dipanggil dari luar
window.openSignPad = openSignPad;
window.closeSignPad = closeSignPad;
window.clearSignature = clearSignature;
window.saveSignature = saveSignature;
function closeSignPad() {
    document.getElementById('signPadModal').classList.add('hidden');
    signaturePadInstance.clear();
}

function clearSignature() {
    signaturePadInstance.clear();
}

function saveSignature() {
    if (signaturePadInstance.isEmpty()) {
        alert('Tanda tangan belum diisi!');
        return;
    }
    const dataURL = signaturePadInstance.toDataURL();
    const targetInput = findSignatureTarget(document.getElementById('currentSignatureField').value);
    if (targetInput) {
        targetInput.value = dataURL;
        targetInput.dataset.signatureUrl = dataURL;
        syncSignaturePreview(targetInput);
        syncSignatureControls(targetInput.closest('td, .signature-field, .signature-cell, div') || document);
        console.log('✅ Signature saved to input:', targetInput.id);
        alert('✅ Tanda tangan berhasil tersimpan!');
    } else {
        console.error('❌ Input target tidak ditemukan!');
        alert('❌ Input target tidak ditemukan!');
    }
    closeSignPad();
}

document.addEventListener('DOMContentLoaded', () => {
    syncSignatureControls();
    setTimeout(syncSignatureControls, 250);
});
</script>
