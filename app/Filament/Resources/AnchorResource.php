<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnchorResource\Pages;
use App\Models\Anchor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AnchorResource extends Resource
{
    protected static ?string $model = Anchor::class;

    protected static ?string $navigationIcon = 'heroicon-o-link';

    protected static ?string $navigationGroup = '內容管理';

    protected static ?int $navigationSort = 11;

    protected static ?string $navigationLabel = '錨點管理';

    public static function getNavigationLabel(): string
    {
        return '錨點管理';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('錨點資訊')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('名稱')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('url')
                            ->label('網址')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('keyword')
                            ->label('關鍵字')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('uri')
                            ->label('URI')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('sort')
                            ->label('排序')
                            ->numeric()
                            ->default(1),
                        Forms\Components\Toggle::make('status')
                            ->label('狀態')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('名稱')
                    ->searchable(),
                Tables\Columns\TextColumn::make('keyword')
                    ->label('關鍵字')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('uri')
                    ->label('URI')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('url')
                    ->label('網址')
                    ->limit(40)
                    ->copyable(),
                Tables\Columns\IconColumn::make('status')
                    ->label('狀態')
                    ->boolean(),
                Tables\Columns\TextColumn::make('sort')
                    ->label('排序')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('建立時間')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('status')
                    ->label('狀態'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAnchors::route('/'),
            'create' => Pages\CreateAnchor::route('/create'),
            'edit' => Pages\EditAnchor::route('/{record}/edit'),
        ];
    }
}
