import QRCode from 'qrcode';

/**
 * Инициализация всех [data-qr] блоков на странице.
 * Генерирует QR в canvas + вешает кнопки скачивания.
 */
export function initQrDownloads() {
    const blocks = document.querySelectorAll('[data-qr]');
    if (!blocks.length) return;

    blocks.forEach(async (block) => {
        const url       = block.dataset.url;
        const filename  = block.dataset.filename || 'qr';
        const canvas    = block.querySelector('.qr-download__canvas');
        if (!url || !canvas) return;

        // Высокое разрешение для скачивания
        const DPR = 3;
        const size = canvas.width;

        try {
            await QRCode.toCanvas(canvas, url, {
                width: size,
                margin: 2,
                errorCorrectionLevel: 'M',
                color: {
                    dark:  '#0a0b12',
                    light: '#ffffff',
                },
            });
        } catch (e) {
            console.warn('[qr] generate failed', e);
            return;
        }

        // PNG download
        const pngBtn = block.querySelector('[data-qr-download="png"]');
        if (pngBtn) {
            pngBtn.addEventListener('click', async () => {
                try {
                    // Генерируем в высоком разрешении через offscreen canvas
                    const tmp = document.createElement('canvas');
                    await QRCode.toCanvas(tmp, url, {
                        width: size * DPR,
                        margin: 2,
                        errorCorrectionLevel: 'M',
                        color: { dark: '#0a0b12', light: '#ffffff' },
                    });

                    tmp.toBlob((blob) => {
                        downloadBlob(blob, `${filename}.png`);
                    }, 'image/png');
                } catch (e) {
                    alert('Не удалось сгенерировать PNG');
                }
            });
        }

        // SVG download
        const svgBtn = block.querySelector('[data-qr-download="svg"]');
        if (svgBtn) {
            svgBtn.addEventListener('click', async () => {
                try {
                    const svg = await QRCode.toString(url, {
                        type: 'svg',
                        margin: 2,
                        errorCorrectionLevel: 'M',
                        color: { dark: '#0a0b12', light: '#ffffff' },
                    });

                    const blob = new Blob([svg], { type: 'image/svg+xml' });
                    downloadBlob(blob, `${filename}.svg`);
                } catch (e) {
                    alert('Не удалось сгенерировать SVG');
                }
            });
        }

        // Copy URL
        const copyBtn = block.querySelector('[data-qr-copy]');
        if (copyBtn) {
            copyBtn.addEventListener('click', async () => {
                try {
                    await navigator.clipboard.writeText(url);
                    const orig = copyBtn.textContent;
                    copyBtn.textContent = '✓ COPIED';
                    setTimeout(() => { copyBtn.textContent = orig; }, 1500);
                } catch (e) {
                    // Fallback
                    const ta = document.createElement('textarea');
                    ta.value = url;
                    document.body.appendChild(ta);
                    ta.select();
                    document.execCommand('copy');
                    document.body.removeChild(ta);
                }
            });
        }
    });
}

function downloadBlob(blob, filename) {
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

// Автоинициализация
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initQrDownloads);
} else {
    initQrDownloads();
}
