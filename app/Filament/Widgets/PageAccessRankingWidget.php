<?php

namespace App\Filament\Widgets;

use App\Models\AccessLog;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class PageAccessRankingWidget extends Widget
{
    protected static string $view = 'filament.widgets.page-access-ranking';

    protected int | string | array $columnSpan = 1;

    public ?string $filter = '7';

    protected function getFilters(): array
    {
        return [
            '7' => '最近7天',
            '15' => '最近15天',
            '30' => '最近1個月',
        ];
    }

    public function getRanking()
    {
        $days = (int) ($this->filter ?: 7);

        return AccessLog::query()
            ->select('url', DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('url')
            ->orderByDesc('count')
            ->limit(10)
            ->get();
    }
}