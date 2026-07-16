<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SeoResource\Pages;
use App\Models\Seo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SeoResource extends Resource
{
    protected static ?string $model = Seo::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?string $navigationGroup = '內容管理';

    protected static ?int $navigationSort = 7;

    protected static ?string $navigationLabel = 'SEO管理';

    public static function getNavigationLabel(): string
    {
        return 'SEO管理';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('SEO設定')
                    ->schema([
                        Forms\Components\TextInput::make('path')
                            ->label('路徑')
                            ->required()
                            ->maxLength(255)
                            ->helperText('對應的頁面路徑，例如: /, /about, /product'),
                        Forms\Components\TextInput::make('title')
                            ->label('標題')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('key_word')
                            ->label('關鍵字')
                            ->maxLength(255)
                            ->helperText('關鍵字以逗號分隔'),
                        Forms\Components\Textarea::make('description')
                            ->label('描述')
                            ->maxLength(65535),
                        Forms\Components\Toggle::make('title_tail')
                            ->label('標題後綴')
                            ->default(false)
                            ->helperText('是否在標題後自動添加網站名稱'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('path')
                    ->label('路徑')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('標題')
                    ->searchable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('key_word')
                    ->label('關鍵字')
                    ->limit(30)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('描述')
                    ->limit(40)
                    ->toggleable(),
                Tables\Columns\IconColumn::make('title_tail')
                    ->label('標題後綴')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('建立時間')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                //
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
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListSeos::route('/'),
            'create' => Pages\CreateSeo::route('/create'),
            'edit' => Pages\EditSeo::route('/{record}/edit'),
        ];
    }
}
