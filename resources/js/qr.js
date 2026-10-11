import QRCode from 'qrcode';

const QR_OPTIONS = {
    margin: 2,
    errorCorrectionLevel: 'H', // High — 30% redundancy для лого в центре
    color: { dark: '#0a0b12', light: '#ffffff' },
};

const LOGO_RATIO = 0.22;    // лого занимает 22% от QR
const LOGO_PAD   = 0.15;    // padding внутри оранжевой плашки (от размера лого)
const LOGO_COLOR = '#ff5c1f';
const BORDER_COLOR = '#0a0b12';

/* ═══ Инициализация всех [data-qr] ═══ */
export function initQrDownloads() {
    document.querySelectorAll('[data-qr]').forEach((block) => {
        if (block.dataset.qrInitialized === '1') return;
        renderBlock(block);
        block.dataset.qrInitialized = '1';
    });
}

async function renderBlock(block) {
    const url = block.dataset.url;
    const filename = block.dataset.filename || 'qr';
    const withLogo = block.dataset.logo === '1';
    const logoUrl = block.dataset.logoUrl || '/img/branding/core-white.png';
    const canvas = block.querySelector('.qr-download__canvas');
    if (!url || !canvas) return;

    try {
        await QRCode.toCanvas(canvas, url, { width: canvas.width, ...QR_OPTIONS });
        if (withLogo) {
            const logo = await loadLogo(logoUrl);
            if (logo) drawLogoOnCanvas(canvas, logo);
        }
    } catch (e) {
        console.warn('[qr] render failed', e);
        return;
    }

    bindDownload(block.querySelector('[data-qr-download="png"]'), url, filename, 'png', withLogo, logoUrl);
    bindDownload(block.querySelector('[data-qr-download="svg"]'), url, filename, 'svg', withLogo, logoUrl);
    bindCopy(block.querySelector('[data-qr-copy]'), url);
}

/* ═══ Logo loading (cache) ═══ */
const logoCache = {};
function loadLogo(url) {
    if (logoCache[url]) return logoCache[url];
    logoCache[url] = new Promise((resolve) => {
        const img = new Image();
        img.crossOrigin = 'anonymous';
        img.onload = () => resolve(img);
        img.onerror = () => resolve(null);
        img.src = url;
    });
    return logoCache[url];
}

function imageToDataUrl(img) {
    return new Promise((resolve) => {
        try {
            const c = document.createElement('canvas');
            c.width = img.naturalWidth || img.width;
            c.height = img.naturalHeight || img.height;
            c.getContext('2d').drawImage(img, 0, 0);
            resolve(c.toDataURL('image/png'));
        } catch (e) {
            resolve('');
        }
    });
}

/* ═══ Logo overlay on canvas ═══ */
function drawLogoOnCanvas(canvas, logo) {
    const ctx = canvas.getContext('2d');
    const size = canvas.width;
    const logoSize = Math.round(size * LOGO_RATIO);
    const pad = Math.round(logoSize * LOGO_PAD);
    const boxSize = logoSize + pad * 2;
    const boxX = Math.round((size - boxSize) / 2);
    const boxY = Math.round((size - boxSize) / 2);
    const logoX = Math.round((size - logoSize) / 2);
    const logoY = Math.round((size - logoSize) / 2);
    const borderW = Math.max(3, Math.round(size * 0.012));

    // Оранжевая плашка
    ctx.fillStyle = LOGO_COLOR;
    ctx.fillRect(boxX, boxY, boxSize, boxSize);

    // Чёрная обводка
    ctx.strokeStyle = BORDER_COLOR;
    ctx.lineWidth = borderW;
    ctx.strokeRect(boxX + borderW / 2, boxY + borderW / 2, boxSize - borderW, boxSize - borderW);

    // Логотип
    ctx.drawImage(logo, logoX, logoY, logoSize, logoSize);
}

/* ═══ Downloads ═══ */
function bindDownload(btn, url, filename, type, withLogo, logoUrl) {
    if (!btn || btn.dataset.qrBound === '1') return;
    btn.dataset.qrBound = '1';

    btn.addEventListener('click', async () => {
        try {
            if (type === 'svg') {
                const svg = await makeSvg(url, withLogo, logoUrl);
                download(new Blob([svg], { type: 'image/svg+xml' }), `${filename}.svg`);
            } else {
                const canvas = await makeCanvas(url, 900, withLogo, logoUrl);
                canvas.toBlob((blob) => download(blob, `${filename}.png`), 'image/png');
            }
        } catch (e) {
            console.error('[qr] download failed', e);
            alert('Не удалось сгенерировать ' + type.toUpperCase());
        }
    });
}

async function makeCanvas(url, size, withLogo, logoUrl) {
    const c = document.createElement('canvas');
    await QRCode.toCanvas(c, url, { width: size, ...QR_OPTIONS });
    if (withLogo) {
        const logo = await loadLogo(logoUrl);
        if (logo) drawLogoOnCanvas(c, logo);
    }
    return c;
}

async function makeSvg(url, withLogo, logoUrl) {
    const svg = await QRCode.toString(url, { type: 'svg', width: 600, ...QR_OPTIONS });
    if (!withLogo) return svg;

    const logo = await loadLogo(logoUrl);
    if (!logo) return svg;

    const logoData = await imageToDataUrl(logo);
    if (!logoData) return svg;

    // Парсим viewBox из svg (qrcode.js даёт viewBox="0 0 N N")
    const m = svg.match(/viewBox="0 0 (\d+(?:\.\d+)?) (\d+(?:\.\d+)?)"/);
    if (!m) return svg;
    const size = parseFloat(m[1]);

    const logoSize = size * LOGO_RATIO;
    const pad = logoSize * LOGO_PAD;
    const boxSize = logoSize + pad * 2;
    const boxX = (size - boxSize) / 2;
    const boxY = (size - boxSize) / 2;
    const logoX = (size - logoSize) / 2;
    const logoY = (size - logoSize) / 2;
    const borderW = Math.max(3, size * 0.012);

    const overlay = `
    <rect x="${boxX}" y="${boxY}" width="${boxSize}" height="${boxSize}" fill="${LOGO_COLOR}"/>
    <rect x="${boxX + borderW/2}" y="${boxY + borderW/2}" width="${boxSize - borderW}" height="${boxSize - borderW}" fill="none" stroke="${BORDER_COLOR}" stroke-width="${borderW}"/>
    <image href="${logoData}" xlink:href="${logoData}" x="${logoX}" y="${logoY}" width="${logoSize}" height="${logoSize}" preserveAspectRatio="xMidYMid meet"/>
    `;

    return svg.replace('</svg>', overlay + '</svg>');
}

/* ═══ Copy URL ═══ */
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

/* ═══ Download helper ═══ */
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

    const block = modalEl.querySelector('[data-qr]');
    const canvas = block.querySelector('.qr-download__canvas');

    // Reset
    canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
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
            <div class="qr-download" data-qr data-url="" data-filename="qr" data-logo="1" data-logo-url="/img/branding/core-white.png"
                 style="display:inline-flex;flex-direction:column;align-items:center;gap:16px;">
                <div style="padding:14px;background:#fff;border:3px solid var(--brutal-border);box-shadow:6px 6px 0 var(--brutal-shadow);line-height:0;">
                    <canvas class="qr-download__canvas" width="280" height="280" style="display:block;"></canvas>
                </div>
                <div style="display:flex;gap:8px;flex-wrap:wrap;justify-content:center;">
                    <button type="button" class="lab-btn lab-btn--primary lab-btn--sm" data-qr-download="png">PNG</button>
                    <button type="button" class="lab-btn lab-btn--secondary lab-btn--sm" data-qr-download="svg">SVG</button>
                    <button type="button" class="lab-btn lab-btn--secondary lab-btn--sm" data-qr-copy>COPY URL</button>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modalEl);
    modalEl.addEventListener('click', (e) => { if (e.target === modalEl) window.closeQrModal(); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') window.closeQrModal(); });
}

/* ═══ Auto-init ═══ */
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initQrDownloads);
} else {
    initQrDownloads();
}
