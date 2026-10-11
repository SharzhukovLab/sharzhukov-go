import QRCode from 'qrcode';

const QR_OPTIONS = {
    margin: 2,
    errorCorrectionLevel: 'M',
    color: { dark: '#0a0b12', light: '#ffffff' },
};

/* ═══ Init all [data-qr] blocks ═══ */
export function initQrDownloads() {
    document.querySelectorAll('[data-qr]').forEach((block) => {
        if (block.dataset.qrInitialized === '1') return;
        renderBlock(block);
        block.dataset.qrInitialized = '1';
    });
}

async function renderBlock(block) {
    const url      = block.dataset.url;
    const filename = block.dataset.filename || 'qr';
    const canvas   = block.querySelector('.qr-download__canvas');
    if (!url || !canvas) return;

    try {
        await QRCode.toCanvas(canvas, url, { width: canvas.width, ...QR_OPTIONS });
    } catch (e) {
        console.warn('[qr] render failed', e);
        return;
    }

    bindDownload(block.querySelector('[data-qr-download="png"]'), url, filename, 'png');
    bindDownload(block.querySelector('[data-qr-download="svg"]'), url, filename, 'svg');
    bindCopy(block.querySelector('[data-qr-copy]'), url);
}

function bindDownload(btn, url, filename, type) {
    if (!btn || btn.dataset.qrBound === '1') return;
    btn.dataset.qrBound = '1';
    btn.addEventListener('click', async () => {
        try {
            if (type === 'svg') {
                const svg = await QRCode.toString(url, { type: 'svg', width: 600, ...QR_OPTIONS });
                download(new Blob([svg], { type: 'image/svg+xml' }), `${filename}.svg`);
            } else {
                const tmp = document.createElement('canvas');
                await QRCode.toCanvas(tmp, url, { width: 900, ...QR_OPTIONS });
                tmp.toBlob((blob) => download(blob, `${filename}.png`), 'image/png');
            }
        } catch (e) {
            alert('Не удалось сгенерировать ' + type.toUpperCase());
        }
    });
}

function bindCopy(btn, url) {
    if (!btn || btn.dataset.qrBound === '1') return;
    btn.dataset.qrBound = '1';
    btn.addEventListener('click', async () => {
        try {
            await navigator.clipboard.writeText(url);
        } catch (e) {
            const ta = document.createElement('textarea');
            ta.value = url;
            document.body.appendChild(ta);
            ta.select();
            document.execCommand('copy');
            document.body.removeChild(ta);
        }
        const orig = btn.textContent;
        btn.textContent = '✓ COPIED';
        setTimeout(() => { btn.textContent = orig; }, 1500);
    });
}

function download(blob, filename) {
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    setTimeout(() => {
        URL.revokeObjectURL(a.href);
        document.body.removeChild(a);
    }, 100);
}

/* ═══ Modal ═══ */
let modalEl = null;

window.openQrModal = function (url, filename) {
    if (!modalEl) createModal();

    const block  = modalEl.querySelector('[data-qr]');
    const canvas = block.querySelector('.qr-download__canvas');

    // Reset canvas
    canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);

    // Reset bindings
    block.querySelectorAll('[data-qr-download], [data-qr-copy]').forEach((b) => {
        const clone = b.cloneNode(true);
        clone.dataset.qrBound = '';
        b.parentNode.replaceChild(clone, b);
    });

    block.dataset.url = url;
    block.dataset.filename = filename;
    block.dataset.qrInitialized = '';

    modalEl.style.display = 'flex';
    renderBlock(block);
    block.dataset.qrInitialized = '1';
};

window.closeQrModal = function () {
    if (modalEl) modalEl.style.display = 'none';
};

function createModal() {
    modalEl = document.createElement('div');
    modalEl.id = 'qr-modal';
    modalEl.style.cssText = 'display:none;position:fixed;inset:0;background:rgba(10,11,18,0.85);z-index:100;align-items:center;justify-content:center;padding:20px;';
    modalEl.innerHTML = `
        <div style="background:var(--lab-bg);border:3px solid var(--brutal-border);box-shadow:8px 8px 0 var(--brutal-shadow);padding:28px;position:relative;max-width:100%;">
            <button type="button" onclick="window.closeQrModal()" style="position:absolute;top:-16px;right:-16px;background:var(--brutal-accent);color:#fff;border:3px solid var(--brutal-border);width:36px;height:36px;cursor:pointer;font-weight:900;font-size:18px;box-shadow:3px 3px 0 var(--brutal-shadow);">✕</button>
            <div class="qr-download" data-qr data-url="" data-filename="qr">
                <div style="padding:12px;background:#fff;border:2px solid var(--brutal-border);box-shadow:4px 4px 0 var(--brutal-shadow);">
                    <canvas class="qr-download__canvas" width="280" height="280" style="display:block;"></canvas>
                </div>
                <div style="display:flex;gap:8px;margin-top:16px;justify-content:center;">
                    <button type="button" class="lab-btn lab-btn--primary lab-btn--sm" data-qr-download="png">PNG</button>
                    <button type="button" class="lab-btn lab-btn--secondary lab-btn--sm" data-qr-download="svg">SVG</button>
                    <button type="button" class="lab-btn lab-btn--secondary lab-btn--sm" data-qr-copy>COPY URL</button>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modalEl);

    modalEl.addEventListener('click', (e) => {
        if (e.target === modalEl) window.closeQrModal();
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') window.closeQrModal();
    });
}

/* ═══ Auto-init ═══ */
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initQrDownloads);
} else {
    initQrDownloads();
}
