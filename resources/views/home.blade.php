<x-layouts.public title="Sharzhukov Go — Short Links">
    <section class="brutal-grid-bg" style="padding:80px 24px 60px;border-bottom:3px solid var(--brutal-border);">
        <div style="max-width:800px;margin:0 auto;text-align:center;">
            <span class="brutal-marker">// GO</span>
            <h1 class="brutal-hero-title" style="margin:24px 0 20px;">
                Short links.<br>
                <span style="background:var(--brutal-accent);color:#fff;padding:0 .12em;box-shadow:6px 6px 0 var(--brutal-shadow);display:inline-block;transform:rotate(-1deg);">Long reach.</span>
            </h1>
            <p style="color:var(--lab-fg-muted);font-size:17px;max-width:520px;margin:0 auto 40px;line-height:1.6;">
                Fast, private URL shortener. Self-hosted in EU.
            </p>
        </div>
    </section>

    <section class="brutal-grid-bg" style="padding:60px 24px;">
        <div style="max-width:720px;margin:0 auto;">
            @if(session('created'))
                <div class="brutal-card" style="padding:20px;margin-bottom:24px;background:var(--brutal-accent);color:#fff;border-color:var(--brutal-border);">
                    <p style="margin:0;font-family:var(--font-mono);font-weight:700;font-size:13px;">✓ Link created successfully</p>
                </div>
            @endif

            @if($errors->any())
                <div class="brutal-card" style="padding:20px;margin-bottom:24px;background:#fef2f2;border-color:#dc2626;">
                    <ul style="margin:0;padding-left:20px;color:#991b1b;font-size:13px;">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('link.store') }}" class="brutal-card" style="padding:32px;">
                @csrf
                <div style="margin-bottom:20px;">
                    <label style="display:block;font-family:var(--font-mono);font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;margin-bottom:8px;">Long URL *</label>
                    <input type="url" name="target_url" required placeholder="https://example.com/very/long/path" value="{{ old('target_url') }}" style="width:100%;height:48px;padding:0 16px;border:3px solid var(--brutal-border);background:var(--lab-bg);color:var(--lab-fg);font-family:var(--font-mono);font-size:14px;outline:none;">
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
                    <div>
                        <label style="display:block;font-family:var(--font-mono);font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;margin-bottom:8px;">Custom slug (optional)</label>
                        <input type="text" name="slug" placeholder="my-link" value="{{ old('slug') }}" maxlength="64" pattern="[A-Za-z0-9_-]+" style="width:100%;height:48px;padding:0 16px;border:3px solid var(--brutal-border);background:var(--lab-bg);color:var(--lab-fg);font-family:var(--font-mono);font-size:14px;outline:none;">
                    </div>
                    <div>
                        <label style="display:block;font-family:var(--font-mono);font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;margin-bottom:8px;">Description (optional)</label>
                        <input type="text" name="description" placeholder="What's this link for?" value="{{ old('description') }}" maxlength="255" style="width:100%;height:48px;padding:0 16px;border:3px solid var(--brutal-border);background:var(--lab-bg);color:var(--lab-fg);font-family:var(--font-mono);font-size:14px;outline:none;">
                    </div>
                </div>

                <button type="submit" class="lab-btn lab-btn--primary" style="width:100%;height:52px;font-size:14px;letter-spacing:.08em;">
                    SHORTEN →
                </button>
            </form>

            @guest
                <p style="text-align:center;margin-top:20px;font-family:var(--font-mono);font-size:12px;color:var(--lab-fg-muted);">
                    <a href="https://account-main.sharzhukov.com/login?return={{ urlencode(url('/')) }}" style="color:var(--brutal-accent);font-weight:700;">Sign in</a>
                    to save your links and view stats.
                </p>
            @endguest
        </div>
    </section>
</x-layouts.public>
