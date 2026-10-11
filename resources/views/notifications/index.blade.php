<x-layouts.public :title="'Notifications — ' . config('app.name')">
    <section class="brutal-grid-bg" style="padding:48px 24px 32px;border-bottom:3px solid var(--brutal-border);">
        <div style="max-width:820px;margin:0 auto;display:flex;justify-content:space-between;align-items:flex-end;flex-wrap:wrap;gap:16px;">
            <div>
                <span class="brutal-marker">// NOTIFICATIONS</span>
                <h1 class="brutal-hero-title" style="margin:14px 0 0;font-size:clamp(1.75rem,4vw,2.5rem);">Inbox</h1>
            </div>
            @if($notifications->total() > 0)
                <button type="button" onclick="labMarkAllRead(this)" class="lab-btn lab-btn--secondary" style="font-size:11px;">MARK ALL READ</button>
            @endif
        </div>
    </section>

    <section style="max-width:820px;margin:0 auto;padding:32px 24px 80px;">
        @if($notifications->isEmpty())
            <div class="brutal-card" style="padding:48px;text-align:center;">
                <p style="color:var(--lab-fg-muted);margin:0;font-size:15px;">No notifications yet.</p>
            </div>
        @else
            <div style="display:flex;flex-direction:column;gap:12px;" id="notif-list">
                @foreach($notifications as $n)
                    @php $data = $n->data; $unread = is_null($n->read_at); @endphp
                    <div class="brutal-card" data-notif-id="{{ $n->id }}" style="padding:18px;display:flex;gap:14px;align-items:flex-start;{{ $unread ? 'border-left:6px solid var(--brutal-accent);' : 'opacity:.72;' }}">
                        <div style="flex:1;min-width:0;">
                            <div style="display:flex;justify-content:space-between;align-items:baseline;gap:10px;margin-bottom:6px;flex-wrap:wrap;">
                                <div style="font-family:var(--font-sans);font-weight:900;font-size:15px;">
                                    {{ $data['title'] ?? 'Notification' }}
                                </div>
                                <div style="font-family:var(--font-mono);font-size:10px;color:var(--lab-fg-muted);">
                                    {{ $n->created_at->diffForHumans() }}
                                </div>
                            </div>
                            @if(!empty($data['message']))
                                <p style="margin:0 0 10px;color:var(--lab-fg-muted);font-size:14px;line-height:1.55;">
                                    {{ $data['message'] }}
                                </p>
                            @endif
                            @if(!empty($data['url']))
                                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                                    <a href="{{ $data['url'] }}" class="lab-btn lab-btn--primary" style="font-size:11px;padding:6px 12px;">
                                        Open →
                                    </a>
                                    @if($unread)
                                        <button type="button" class="lab-btn lab-btn--secondary" style="font-size:11px;padding:6px 12px;" onclick="labNotifMarkRead('{{ $n->id }}', this)">
                                            Mark read
                                        </button>
                                    @endif
                                    <button type="button" class="lab-btn lab-btn--ghost" style="font-size:11px;padding:6px 12px;" onclick="labNotifDelete('{{ $n->id }}', this)">
                                        ×
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            @if($notifications->hasPages())
                <div style="margin-top:24px;display:flex;justify-content:center;">
                    {{ $notifications->links() }}
                </div>
            @endif
        @endif
    </section>

    <script>
    function labNotifMarkRead(id, btn) {
        fetch('/api/notifications/' + id + '/read', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' },
            credentials: 'same-origin',
        }).then(() => {
            var card = document.querySelector('[data-notif-id="' + id + '"]');
            if (card) { card.style.opacity = '.72'; card.style.borderLeft = 'none'; }
            if (btn) btn.remove();
        });
    }
    function labNotifDelete(id, btn) {
        fetch('/api/notifications/' + id, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' },
            credentials: 'same-origin',
        }).then(() => {
            var card = document.querySelector('[data-notif-id="' + id + '"]');
            if (card) card.remove();
        });
    }
    function labMarkAllRead(btn) {
        fetch('/api/notifications/mark-all-read', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' },
            credentials: 'same-origin',
        }).then(() => location.reload());
    }
    </script>
</x-layouts.public>