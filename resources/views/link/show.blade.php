<x-layouts.public :title="$link->slug . ' — Sharzhukov Go'">
    <section class="brutal-grid-bg" style="padding:60px 24px;">
        <div style="max-width:720px;margin:0 auto;">
            <span class="brutal-marker">// LINK CREATED</span>

            <div class="brutal-card" style="padding:32px;margin-top:24px;">
                <p style="font-family:var(--font-mono);font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--lab-fg-muted);margin:0 0 8px;">Short URL</p>
                <p style="font-family:var(--font-mono);font-size:22px;font-weight:800;color:var(--brutal-accent);margin:0 0 24px;word-break:break-all;">
                    {{ $link->short_url }}
                </p>

                <p style="font-family:var(--font-mono);font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--lab-fg-muted);margin:0 0 8px;">Target</p>
                <p style="font-size:13px;color:var(--lab-fg);margin:0 0 24px;word-break:break-all;font-family:var(--font-mono);">
                    {{ $link->target_url }}
                </p>

                <div style="display:flex;gap:12px;flex-wrap:wrap;">
                    <a href="{{ $link->short_url }}" target="_blank" class="lab-btn lab-btn--primary">TEST LINK →</a>
                    <button type="button" onclick="navigator.clipboard.writeText('{{ $link->short_url }}'); this.textContent='COPIED ✓';" class="lab-btn lab-btn--secondary">COPY URL</button>
                </div>
            </div>

            <div class="brutal-card" style="padding:24px;margin-top:20px;text-align:center;">
                <p style="font-family:var(--font-mono);font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--lab-fg-muted);margin:0 0 16px;">QR Code</p>
                <img src="{{ $link->qr_url }}" alt="QR code" style="width:220px;height:220px;border:3px solid var(--brutal-border);margin:0 auto;display:block;">
            </div>

            <div style="text-align:center;margin-top:24px;">
                <a href="{{ route('home') }}" class="brutal-link-arrow">← Create another link</a>
            </div>
        </div>
    </section>
</x-layouts.public>