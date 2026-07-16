<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

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
}
