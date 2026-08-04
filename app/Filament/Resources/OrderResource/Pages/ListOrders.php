<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Exports\OrdersExport;
use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('refresh')
                ->label('重新整理')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action(fn () => $this->resetTable()),
        ];
    }

    public function exportAll()
    {
        if (! $this->acquireExportLock()) {
            Notification::make()->warning()
                ->title('匯出進行中')->body('請稍候，避免連續點擊')->send();
            return;
        }
        set_time_limit(0);
        $data = OrderResource::buildExportData(
            Order::with('products')->orderBy('created_at', 'desc')->lazy()
        );
        return Excel::download(new OrdersExport($data), '訂單匯出-' . date('YmdHis') . '.xlsx');
    }

    public function exportSelected()
    {
        if (! $this->acquireExportLock()) {
            Notification::make()->warning()
                ->title('匯出進行中')->body('請稍候，避免連續點擊')->send();
            return;
        }
        $records = $this->getSelectedTableRecords();
        $records->loadMissing('products');
        $data = OrderResource::buildExportData($records);
        return Excel::download(new OrdersExport($data), '訂單匯出-' . date('YmdHis') . '.xlsx');
    }

    protected function acquireExportLock(): bool
    {
        $key = 'order_export_lock_' . (auth()->id() ?? 'guest');
        return Cache::add($key, true, 8);
    }
}
