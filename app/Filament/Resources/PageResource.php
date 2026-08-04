<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Models\Config;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PageResource extends Resource
{
    protected static ?string $model = Config::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    protected static ?string $navigationGroup = '內容管理';

    protected static ?int $navigationSort = 8;

    protected static ?string $navigationLabel = '單頁管理';

    public static function getNavigationLabel(): string
    {
        return '單頁管理';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('頁面設定')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('識別名稱')
                            ->required()
                            ->maxLength(255)
                            ->helperText('用於程式識別的唯讀鍵值，例如: about_us, privacy_policy'),
                        Forms\Components\TextInput::make('type')
                            ->label('類型')
                            ->maxLength(255)
                            ->default('page'),
                        \App\Filament\Components\WangEditor::make('content')
                            ->label('內容')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('識別名稱')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('類型')
                    ->badge()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('content')
                    ->label('內容預覽')
                    ->limit(50)
                    ->html(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('建立時間')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('更新時間')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('類型')
                    ->options([
                        'page' => '頁面',
                        'text' => '文字',
                        'html' => 'HTML',
                    ]),
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
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
