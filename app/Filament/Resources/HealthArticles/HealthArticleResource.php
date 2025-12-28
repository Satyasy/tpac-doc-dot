<?php

namespace App\Filament\Resources\HealthArticles;

use App\Filament\Resources\HealthArticles\Pages\CreateHealthArticle;
use App\Filament\Resources\HealthArticles\Pages\EditHealthArticle;
use App\Filament\Resources\HealthArticles\Pages\ListHealthArticles;
use App\Filament\Resources\HealthArticles\Schemas\HealthArticleForm;
use App\Filament\Resources\HealthArticles\Tables\HealthArticlesTable;
use App\Models\HealthArticle;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

use UnitEnum;

class HealthArticleResource extends Resource
{
    protected static ?string $model = HealthArticle::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|UnitEnum|null $navigationGroup = 'Konten';

    protected static ?string $navigationLabel = 'Artikel Kesehatan';

    protected static ?string $modelLabel = 'Artikel';

    protected static ?string $pluralModelLabel = 'Artikel Kesehatan';

    public static function form(Schema $schema): Schema
    {
        return HealthArticleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HealthArticlesTable::configure($table);
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
            'index' => ListHealthArticles::route('/'),
            'create' => CreateHealthArticle::route('/create'),
            'edit' => EditHealthArticle::route('/{record}/edit'),
        ];
    }
}