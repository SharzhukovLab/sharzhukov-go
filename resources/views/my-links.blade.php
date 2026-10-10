<x-layouts.public title="My links — Sharzhukov Go">
    <section class="brutal-grid-bg" style="padding:60px 24px;">
        <div style="max-width:900px;margin:0 auto;">
            <span class="brutal-marker">// MY LINKS</span>
            <h1 class="brutal-hero-title" style="margin:16px 0 32px;font-size:clamp(2rem,4vw,3rem);">Your links</h1>

            @if($links->isEmpty())
                <div class="brutal-card" style="text-align:center;padding:60px;">
                    <p style="color:var(--lab-fg-muted);margin-bottom:20px;">No links yet.</p>
                    <a href="{{ route('home') }}" class="lab-btn lab-btn--primary">CREATE LINK →</a>
                </div>
            @else
                <div style="display:flex;flex-direction:column;gap:10px;">
                    @foreach($links as $link)
                        <div class="brutal-card" style="padding:20px 24px;">
                            <div style="display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;">
                                <div style="flex:1;min-width:0;">
                                    <div style="font-family:var(--font-mono);font-weight:800;font-size:15px;color:var(--brutal-accent);margin-bottom:4px;">
                                        /s/{{ $link->slug }}
                                    </div>
                                    <div style="font-size:12px;color:var(--lab-fg-muted);font-family:var(--font-mono);word-break:break-all;">
                                        → {{ \Illuminate\Support\Str::limit($link->target_url, 60) }}
                                    </div>
                                </div>
                                <div style="display:flex;gap:8px;align-items:center;">
                                    <span class="brutal-badge">{{ $link->clicks_count }} clicks</span>
                                    <a href="{{ $link->short_url }}" target="_blank" class="lab-btn lab-btn--secondary" style="height:34px;padding:0 12px;font-size:11px;">OPEN</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div style="margin-top:24px;">{{ $links->links() }}</div>
            @endif
        </div>
    </section>
</x-layouts.public>