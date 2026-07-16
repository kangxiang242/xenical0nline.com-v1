<?php

namespace App\Filament\Resources\ArticleCateResource\Pages;

use App\Filament\Resources\ArticleCateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditArticleCate extends EditRecord
{
    protected static string $resource = ArticleCateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
