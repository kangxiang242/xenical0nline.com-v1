<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AccessLogResource\Pages;
use App\Models\AccessLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AccessLogResource extends Resource
{
    protected static ?string $model = AccessLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static ?string $navigationGroup = '系統管理';

    protected static ?int $navigationSort = 21;

    protected static ?string $navigationLabel = '訪問日誌';

    public static function getNavigationLabel(): string
    {
        return '訪問日誌';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('訪問詳情')
                    ->schema([
                        Forms\Components\TextInput::make('url')
                            ->label('網址'),
                        Forms\Components\TextInput::make('method')
                            ->label('請求方法'),
                        Forms\Components\TextInput::make('host')
                            ->label('主機'),
                        Forms\Components\TextInput::make('ip')
                            ->label('IP位址'),
                        Forms\Components\Textarea::make('user_agent')
                            ->label('User Agent'),
                        Forms\Components\TextInput::make('referer')
                            ->label('來源網址'),
                        Forms\Components\TextInput::make('device')
                            ->label('裝置'),
                        Forms\Components\TextInput::make('crawler')
                            ->label('爬蟲'),
                        Forms\Components\TextInput::make('created_at')
                            ->label('訪問時間'),
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
                Tables\Columns\TextColumn::make('url')
                    ->label('網址')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('method')
                    ->label('方法')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'GET' => 'success',
                        'POST' => 'info',
                        'PUT', 'PATCH' => 'warning',
                        'DELETE' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('ip')
                    ->label('IP')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('device')
                    ->label('裝置')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('crawler')
                    ->label('爬蟲')
                    ->toggleable()
                    ->badge()
                    ->color(fn ($state) => $state ? 'warning' : 'gray'),
                Tables\Columns\TextColumn::make('referer')
                    ->label('來源')
                    ->limit(30)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('訪問時間')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('method')
                    ->label('請求方法')
                    ->options([
                        'GET' => 'GET',
                        'POST' => 'POST',
                        'PUT' => 'PUT',
                        'PATCH' => 'PATCH',
                        'DELETE' => 'DELETE',
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
                    ->form([
                        Forms\Components\TextInput::make('url')->label('網址'),
                        Forms\Components\TextInput::make('method')->label('請求方法'),
                        Forms\Components\TextInput::make('host')->label('主機'),
                        Forms\Components\TextInput::make('ip')->label('IP位址'),
                        Forms\Components\Textarea::make('user_agent')->label('User Agent'),
                        Forms\Components\TextInput::make('referer')->label('來源網址'),
                        Forms\Components\TextInput::make('device')->label('裝置'),
                        Forms\Components\TextInput::make('crawler')->label('爬蟲'),
                        Forms\Components\TextInput::make('created_at')->label('訪問時間'),
                    ]),
            ])
            ->bulkActions([])
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
            'index' => Pages\ListAccessLogs::route('/'),
            'view' => Pages\ViewAccessLog::route('/{record}'),
        ];
    }
}
