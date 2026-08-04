<?php

namespace App\Filament\Widgets;

use App\Models\AccessLog;
use App\Models\Message;
use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RightStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('新訂單', Order::whereDate('created_at', today())->count())
                ->description('今日新增訂單')
                ->descriptionIcon('heroicon-o-shopping-cart')
                ->color('primary'),
            Stat::make('新留言', Message::whereDate('created_at', today())->count())
                ->description('今日新增留言')
                ->descriptionIcon('heroicon-o-chat-bubble-left-ellipsis')
                ->color('warning'),
            Stat::make('新設備', AccessLog::whereDate('created_at', today())->count())
                ->description('今日訪問次數')
                ->descriptionIcon('heroicon-o-computer-desktop')
                ->color('success'),
        ];
    }

    protected int | string | array $columnSpan = 1;
}