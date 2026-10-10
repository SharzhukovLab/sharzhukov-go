<?php

namespace App\Filament\Admin\Widgets;

use App\Models\LinkClick;
use App\Models\ShortLink;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GoStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = -10;

    protected function getStats(): array
    {
        $linksTotal    = ShortLink::count();
        $linksActive   = ShortLink::where('is_active', true)->count();
        $linksInactive = ShortLink::where('is_active', false)->count();
        $linksAnon     = ShortLink::whereNull('user_id')->count();

        $clicksTotal   = ShortLink::sum('clicks_count');
        $clicksToday   = LinkClick::whereDate('clicked_at', today())->count();
        $clicksWeek    = LinkClick::where('clicked_at', '>=', now()->subDays(7))->count();

        return [
            Stat::make('Links', $linksTotal)
                ->description($linksActive . ' active · ' . $linksInactive . ' disabled')
                ->descriptionIcon('heroicon-m-link')
                ->color('primary'),

            Stat::make('Total Clicks', number_format($clicksTotal))
                ->description($clicksToday . ' today')
                ->descriptionIcon('heroicon-m-cursor-arrow-rays')
                ->color('success'),

            Stat::make('Last 7 days', number_format($clicksWeek))
                ->description('Clicks in the past week')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('primary'),

            Stat::make('Anonymous', $linksAnon)
                ->description($linksTotal - $linksAnon . ' by signed-in users')
                ->descriptionIcon('heroicon-m-user-circle')
                ->color($linksAnon > 0 ? 'warning' : 'gray'),
        ];
    }
}
