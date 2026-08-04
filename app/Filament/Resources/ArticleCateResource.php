<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleCateResource\Pages;
use App\Models\ArticleCate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ArticleCateResource extends Resource
{
    protected static ?string $model = ArticleCate::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    protected static ?string $navigationGroup = '內容管理';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = '文章分類';

    public static function getNavigationLabel(): string
    {
        return '文章分類';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('分類資訊')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('分類名稱')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('uri')
                            ->label('URI')
                            ->maxLength(255)
                            ->helperText('自訂網址路徑'),
                        Forms\Components\Textarea::make('desc')
                            ->label('描述')
                            ->maxLength(65535),
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
                    ->label('分類名稱')
                    ->searchable(),
                Tables\Columns\TextColumn::make('uri')
                    ->label('URI')
                    ->searchable()
                    ->toggleable(),
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
            'index' => Pages\ListArticleCates::route('/'),
            'create' => Pages\CreateArticleCate::route('/create'),
            'edit' => Pages\EditArticleCate::route('/{record}/edit'),
        ];
    }
}
