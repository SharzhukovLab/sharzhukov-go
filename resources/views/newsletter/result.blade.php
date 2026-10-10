<x-layouts.public :title="'Newsletter — Sharzhukov\'s Laboratory'">
    <section class="brutal-grid-bg" style="padding:80px 24px;">
        <div style="max-width:600px;margin:0 auto;text-align:center;">
            @if($status === 'success')
                <span class="brutal-marker" style="background:var(--brutal-accent);color:#fff;">// SUCCESS</span>
                <h1 class="brutal-hero-title" style="margin:24px 0 20px;font-size:clamp(2rem,4vw,3rem);">
                    {{ $message }}
                </h1>
            @else
                <span class="brutal-marker" style="background:#dc2626;color:#fff;">// ERROR</span>
                <h1 class="brutal-hero-title" style="margin:24px 0 20px;font-size:clamp(2rem,4vw,3rem);">
                    {{ $message }}
                </h1>
            @endif

            <div style="margin-top:32px;">
                <a href="https://main.sharzhukov.com" class="lab-btn lab-btn--primary">
                    BACK TO HOME
                </a>
            </div>
        </div>
    </section>
</x-layouts.public>