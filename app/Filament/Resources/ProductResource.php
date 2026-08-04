<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationGroup = '商品管理';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = '商品管理';

    public static function getNavigationLabel(): string
    {
        return '商品管理';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('基本資訊')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('商品名稱')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('code')
                            ->label('商品代碼')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('subtitle')
                            ->label('副標題')
                            ->maxLength(255),
                        Forms\Components\FileUpload::make('img')
                            ->label('主圖')
                            ->image()
                            ->directory('products')
                            ->maxSize(2048),
                        Forms\Components\FileUpload::make('m_img')
                            ->label('手機版主圖')
                            ->image()
                            ->directory('products/mobile')
                            ->maxSize(2048),
                        Forms\Components\TextInput::make('price')
                            ->label('價格')
                            ->required()
                            ->numeric()
                            ->prefix('NT$'),
                        Forms\Components\TextInput::make('market_price')
                            ->label('市場價格')
                            ->numeric()
                            ->prefix('NT$'),
                        Forms\Components\TextInput::make('discount_percent')
                            ->label('折扣百分比')
                            ->numeric()
                            ->suffix('%'),
                        Forms\Components\Toggle::make('status')
                            ->label('上架狀態')
                            ->default(true),
                        Forms\Components\Toggle::make('is_stock')
                            ->label('有庫存')
                            ->default(true),
                        Forms\Components\TextInput::make('sort')
                            ->label('排序')
                            ->numeric()
                            ->default(1),
                        Forms\Components\TextInput::make('tags')
                            ->label('標籤')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('collect_code')
                            ->label('收集代碼')
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('商品描述')
                    ->schema([
                        \App\Filament\Components\WangEditor::make('describe')
                            ->label('商品描述')
                            ->columnSpanFull(),
                    ]),
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
                    ->label('商品名稱')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('price')
                    ->label('價格')
                    ->money('TWD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('market_price')
                    ->label('市場價')
                    ->money('TWD')
                    ->sortable(),
                Tables\Columns\IconColumn::make('status')
                    ->label('上架')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_stock')
                    ->label('庫存')
                    ->boolean(),
                Tables\Columns\TextColumn::make('sort')
                    ->label('排序')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('建立時間')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('status')
                    ->label('上架狀態'),
                Tables\Filters\TernaryFilter::make('is_stock')
                    ->label('庫存狀態'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
