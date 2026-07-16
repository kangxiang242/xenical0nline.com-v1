<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = '訂單管理';

    public static function getNavigationLabel(): string
    {
        return '訂單管理';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('訂單資訊')
                    ->schema([
                        Forms\Components\TextInput::make('no')
                            ->label('訂單編號')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('inside_no')
                            ->label('內部單號')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('name')
                            ->label('收件人姓名')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label('收件人電話')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('total_price')
                            ->label('總價')
                            ->required()
                            ->numeric()
                            ->prefix('NT$'),
                        Forms\Components\TextInput::make('product_price')
                            ->label('商品總價')
                            ->numeric()
                            ->prefix('NT$'),
                        Forms\Components\TextInput::make('freight')
                            ->label('運費')
                            ->numeric()
                            ->prefix('NT$'),
                        Forms\Components\Select::make('delivery_type')
                            ->label('配送方式')
                            ->options(Order::DELIVERY_TYPE_TXT)
                            ->default(0),
                        Forms\Components\Select::make('delivery_time')
                            ->label('配送時段')
                            ->options(Order::DELIVERY_TIME),
                        Forms\Components\Select::make('payment_type')
                            ->label('付款方式')
                            ->options([
                                '0' => '貨到付款',
                            ]),
                        Forms\Components\Select::make('status')
                            ->label('訂單狀態')
                            ->options(Order::STATUS_TXT)
                            ->default(0),
                        Forms\Components\Textarea::make('remarks')
                            ->label('備註')
                            ->maxLength(65535),
                        Forms\Components\TextInput::make('country')
                            ->label('國家')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('province')
                            ->label('省份')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('city')
                            ->label('城市')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('county')
                            ->label('區/縣')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('street')
                            ->label('街道')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('address')
                            ->label('詳細地址')
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('商店資訊')
                    ->schema([
                        Forms\Components\TextInput::make('shop_name')
                            ->label('商店名稱')
                            ->maxLength(255),
                        Forms\Components\Select::make('shop_type')
                            ->label('商店類型')
                            ->options(Order::SHOP_TYPE_TXT),
                        Forms\Components\TextInput::make('shop_no')
                            ->label('商店編號')
                            ->maxLength(255),
                        Forms\Components\KeyValue::make('shop_data')
                            ->label('商店資料'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('技術資訊')
                    ->schema([
                        Forms\Components\TextInput::make('ip')
                            ->label('IP位址')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('ipcountry')
                            ->label('IP地區')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('user_agent')
                            ->label('User Agent')
                            ->maxLength(65535),
                        Forms\Components\TextInput::make('is_test')
                            ->label('是否測試')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('release_token')
                            ->label('釋放Token')
                            ->maxLength(255),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no')
                    ->label('訂單編號')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('收件人')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('電話')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->label('總價')
                    ->money('TWD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('delivery_type')
                    ->label('配送方式')
                    ->formatStateUsing(fn ($state) => Order::DELIVERY_TYPE_TXT[$state] ?? '未知'),
                Tables\Columns\TextColumn::make('status')
                    ->label('狀態')
                    ->formatStateUsing(fn ($state) => Order::STATUS_TXT[$state] ?? '未知')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        '-1' => 'danger',
                        '0' => 'warning',
                        '1', '2', '3' => 'info',
                        '4', '5' => 'gray',
                        '10' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('建立時間')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('訂單狀態')
                    ->options(Order::STATUS_TXT),
                Tables\Filters\SelectFilter::make('delivery_type')
                    ->label('配送方式')
                    ->options(Order::DELIVERY_TYPE_TXT),
                Tables\Filters\Filter::make('created_at')
                    ->label('建立日期')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('開始日期'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('結束日期'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
