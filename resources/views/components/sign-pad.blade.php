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

function openSignPad(targetId) {
    document.getElementById('signPadModal').classList.remove('hidden');
    document.getElementById('currentSignatureField').value = targetId;

    const canvas = document.getElementById('signaturePad');
    const ratio = Math.max(window.devicePixelRatio || 1, 1);
    canvas.width = canvas.offsetWidth * ratio;
    canvas.height = canvas.offsetHeight * ratio;
    const ctx = canvas.getContext('2d');
    ctx.scale(ratio, ratio);

    signaturePadInstance = new SignaturePad(canvas);

    const existingSignature = document.getElementById(targetId).value;
    if (existingSignature) {
        const img = new Image();
        img.src = existingSignature;
        img.onload = function () {
            ctx.drawImage(img, 0, 0, canvas.width / ratio, canvas.height / ratio);
        };
    }
}

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
    const targetInput = document.getElementById(document.getElementById('currentSignatureField').value);
    if (targetInput) {
        targetInput.value = dataURL;
        console.log('✅ Signature saved to input:', targetInput.id);
        alert('✅ Tanda tangan berhasil tersimpan!');
    } else {
        console.error('❌ Input target tidak ditemukan!');
        alert('❌ Input target tidak ditemukan!');
    }
    closeSignPad();
}
</script>
