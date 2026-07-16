<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FaqResource\Pages;
use App\Models\Faq;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static ?string $navigationGroup = '內容管理';

    protected static ?int $navigationSort = 8;

    protected static ?string $navigationLabel = 'FAQ管理';

    public static function getNavigationLabel(): string
    {
        return 'FAQ管理';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('FAQ內容')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('問題')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('uri')
                            ->label('所屬頁面')
                            ->options(Faq::getUriLabel())
                            ->searchable(),
                        Forms\Components\Textarea::make('content')
                            ->label('答案')
                            ->required()
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('sort')
                            ->label('排序')
                            ->numeric()
                            ->default(1),
                        Forms\Components\Toggle::make('status')
                            ->label('狀態')
                            ->default(true),
                        Forms\Components\Textarea::make('questions_gb')
                            ->label('簡體問題')
                            ->maxLength(65535),
                        Forms\Components\Textarea::make('answers_gb')
                            ->label('簡體答案')
                            ->maxLength(65535),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('問題')
                    ->searchable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('uri')
                    ->label('所屬頁面')
                    ->formatStateUsing(fn ($state) => Faq::getUriLabel()->get($state) ?? $state ?? '全部')
                    ->badge()
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
                Tables\Filters\SelectFilter::make('uri')
                    ->label('所屬頁面')
                    ->options(Faq::getUriLabel()),
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
            'index' => Pages\ListFaqs::route('/'),
            'create' => Pages\CreateFaq::route('/create'),
            'edit' => Pages\EditFaq::route('/{record}/edit'),
        ];
    }
}
