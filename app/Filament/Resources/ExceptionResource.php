<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExceptionResource\Pages;
use App\Models\Exception;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ExceptionResource extends Resource
{
    protected static ?string $model = Exception::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static ?string $navigationGroup = '系統管理';

    protected static ?int $navigationSort = 20;

    protected static ?string $navigationLabel = '異常日誌';

    public static function getNavigationLabel(): string
    {
        return '異常日誌';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('異常詳情')
                    ->schema([
                        Forms\Components\TextInput::make('status_code')
                            ->label('狀態碼'),
                        Forms\Components\TextInput::make('message')
                            ->label('異常訊息'),
                        Forms\Components\TextInput::make('uri')
                            ->label('路徑'),
                        Forms\Components\TextInput::make('method')
                            ->label('請求方法'),
                        Forms\Components\TextInput::make('ip')
                            ->label('IP位址'),
                        Forms\Components\TextInput::make('ip_country')
                            ->label('IP地區'),
                        Forms\Components\Textarea::make('user_agent')
                            ->label('User Agent'),
                        Forms\Components\TextInput::make('referer')
                            ->label('來源網址'),
                        Forms\Components\KeyValue::make('parameters')
                            ->label('請求參數'),
                        Forms\Components\KeyValue::make('headers')
                            ->label('請求頭'),
                        Forms\Components\Textarea::make('trace')
                            ->label('堆疊追蹤')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('created_at')
                            ->label('發生時間'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status_code')
                    ->label('狀態碼')
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state >= 500 => 'danger',
                        $state >= 400 => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('message')
                    ->label('異常訊息')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('uri')
                    ->label('路徑')
                    ->searchable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('method')
                    ->label('方法')
                    ->badge()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('ip')
                    ->label('IP')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('發生時間')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_code')
                    ->label('狀態碼')
                    ->options([
                        '400' => '400',
                        '403' => '403',
                        '404' => '404',
                        '500' => '500',
                        '502' => '502',
                        '503' => '503',
                    ]),
                Tables\Filters\Filter::make('created_at')
                    ->label('日期範圍')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('開始日期'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('結束日期'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['created_from'], fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'], fn ($query, $date) => $query->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->form(fn ($record) => static::getFormSchemaForView()),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
    }

    protected static function getFormSchemaForView(): array
    {
        return [
            Forms\Components\TextInput::make('status_code')->label('狀態碼'),
            Forms\Components\TextInput::make('message')->label('異常訊息'),
            Forms\Components\TextInput::make('uri')->label('路徑'),
            Forms\Components\TextInput::make('method')->label('請求方法'),
            Forms\Components\TextInput::make('ip')->label('IP位址'),
            Forms\Components\TextInput::make('ip_country')->label('IP地區'),
            Forms\Components\Textarea::make('user_agent')->label('User Agent'),
            Forms\Components\TextInput::make('referer')->label('來源網址'),
            Forms\Components\KeyValue::make('parameters')->label('請求參數'),
            Forms\Components\KeyValue::make('headers')->label('請求頭'),
            Forms\Components\Textarea::make('trace')->label('堆疊追蹤')->columnSpanFull(),
            Forms\Components\TextInput::make('created_at')->label('發生時間'),
        ];
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
            'index' => Pages\ListExceptions::route('/'),
            'view' => Pages\ViewException::route('/{record}'),
        ];
    }
}
