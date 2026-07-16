<?php

namespace App\Filament\Resources\ArticleCateResource\Pages;

use App\Filament\Resources\ArticleCateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListArticleCates extends ListRecords
{
    protected static string $resource = ArticleCateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
