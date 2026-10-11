<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    /**
     * Полный список уведомлений.
     */
    public function index(Request $request)
    {
        $me = $request->user();
        $notifications = $me->notifications()
            ->orderByDesc('created_at')
            ->paginate(30);

        // Помечаем всё как read при открытии страницы? Нет — по одной кнопке
        return view('notifications.index', [
            'notifications' => $notifications,
        ]);
    }

    /**
     * JSON: последние 5 + счётчик — для navbell dropdown.
     */
    public function recent(Request $request)
    {
        $me = $request->user();
        $items = $me->notifications()
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(fn($n) => [
                'id'         => $n->id,
                'read'       => !is_null($n->read_at),
                'data'       => $n->data,
                'created_at' => $n->created_at->diffForHumans(),
                'ago_short'  => $n->created_at->diffForHumans(null, true, true),
            ]);

        return response()->json([
            'unread' => $me->unreadNotifications()->count(),
            'items'  => $items,
        ]);
    }

    /**
     * Только счётчик — для polling.
     */
    public function unreadCount(Request $request)
    {
        return response()->json([
            'count' => $request->user()->unreadNotifications()->count(),
        ]);
    }

    /**
     * Пометить одно прочитанным.
     */
    public function markRead(Request $request, string $id)
    {
        $n = $request->user()->notifications()->where('id', $id)->firstOrFail();
        $n->markAsRead();
        return response()->json(['ok' => true]);
    }

    /**
     * Пометить все прочитанными.
     */
    public function markAllRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return response()->json(['ok' => true]);
    }

    /**
     * Удалить.
     */
    public function destroy(Request $request, string $id)
    {
        $n = $request->user()->notifications()->where('id', $id)->firstOrFail();
        $n->delete();
        return response()->json(['ok' => true]);
    }
}