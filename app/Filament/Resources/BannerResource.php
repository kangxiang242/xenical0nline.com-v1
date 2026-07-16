<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BannerResource\Pages;
use App\Models\Banner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BannerResource extends Resource
{
    protected static ?string $model = Banner::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = '內容管理';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = '橫幅管理';

    public static function getNavigationLabel(): string
    {
        return '橫幅管理';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('橫幅設定')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('名稱')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('description')
                            ->label('描述')
                            ->maxLength(255),
                        Forms\Components\FileUpload::make('img')
                            ->label('圖片')
                            ->image()
                            ->multiple()
                            ->directory('banners')
                            ->maxSize(2048)
                            ->helperText('可上傳多張圖片'),
                        Forms\Components\FileUpload::make('m_img')
                            ->label('手機版圖片')
                            ->image()
                            ->directory('banners/mobile')
                            ->maxSize(2048),
                        Forms\Components\TextInput::make('url')
                            ->label('連結網址')
                            ->url()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('href')
                            ->label('連結(Href)')
                            ->url()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('page')
                            ->label('頁面')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('alt')
                            ->label('Alt文字')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('sort')
                            ->label('排序')
                            ->numeric()
                            ->default(1),
                        Forms\Components\Toggle::make('status')
                            ->label('狀態')
                            ->default(true),
                        Forms\Components\TextInput::make('type')
                            ->label('類型')
                            ->maxLength(255),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('img')
                    ->label('圖片')
                    ->circular()
                    ->size(50),
                Tables\Columns\TextColumn::make('name')
                    ->label('名稱')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('url')
                    ->label('連結網址')
                    ->limit(30)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('page')
                    ->label('頁面')
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
            'index' => Pages\ListBanners::route('/'),
            'create' => Pages\CreateBanner::route('/create'),
            'edit' => Pages\EditBanner::route('/{record}/edit'),
        ];
    }
}
