<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArticleResource\Pages;
use App\Models\Article;
use App\Models\ArticleCate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = '內容管理';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = '文章管理';

    public static function getNavigationLabel(): string
    {
        return '文章管理';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('基本資訊')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('標題')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('article_cate_id')
                            ->label('分類')
                            ->relationship('cate', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\FileUpload::make('img')
                            ->label('文章圖片')
                            ->image()
                            ->directory('articles')
                            ->maxSize(2048),
                        Forms\Components\TextInput::make('img_alt')
                            ->label('圖片Alt文字')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('brief')
                            ->label('簡介')
                            ->maxLength(65535),
                        Forms\Components\Toggle::make('status')
                            ->label('狀態')
                            ->default(false),
                        Forms\Components\TextInput::make('sort')
                            ->label('排序')
                            ->numeric()
                            ->default(1),
                        Forms\Components\DateTimePicker::make('release_at')
                            ->label('發佈時間'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('內容')
                    ->schema([
                        \App\Filament\Components\WangEditor::make('content')
                            ->label('文章內容')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('SEO設定')
                    ->schema([
                        Forms\Components\TextInput::make('seo_title')
                            ->label('SEO標題')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('seo_keyword')
                            ->label('SEO關鍵字')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('seo_description')
                            ->label('SEO描述')
                            ->maxLength(65535),
                        Forms\Components\Textarea::make('custom_css')
                            ->label('自訂CSS')
                            ->maxLength(65535),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('統計')
                    ->schema([
                        Forms\Components\TextInput::make('read_num')
                            ->label('閱讀數')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('real_read_num')
                            ->label('真實閱讀數')
                            ->numeric()
                            ->default(0),
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
                    ->size(40),
                Tables\Columns\TextColumn::make('title')
                    ->label('標題')
                    ->searchable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('cate.name')
                    ->label('分類')
                    ->badge()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('status')
                    ->label('狀態')
                    ->boolean(),
                Tables\Columns\TextColumn::make('read_num')
                    ->label('閱讀數')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('sort')
                    ->label('排序')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('建立時間')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('article_cate_id')
                    ->label('分類')
                    ->relationship('cate', 'name'),
                Tables\Filters\TernaryFilter::make('status')
                    ->label('狀態'),
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
            'index' => Pages\ListArticles::route('/'),
            'create' => Pages\CreateArticle::route('/create'),
            'edit' => Pages\EditArticle::route('/{record}/edit'),
        ];
    }
}
