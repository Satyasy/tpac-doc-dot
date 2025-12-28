<?php

namespace App\Filament\Resources\Drugs;

use App\Filament\Resources\Drugs\Pages\CreateDrug;
use App\Filament\Resources\Drugs\Pages\EditDrug;
use App\Filament\Resources\Drugs\Pages\ListDrugs;
use App\Filament\Resources\Drugs\RelationManagers\InteractionsRelationManager;
use App\Filament\Resources\Drugs\RelationManagers\PricesRelationManager;
use App\Filament\Resources\Drugs\Schemas\DrugForm;
use App\Filament\Resources\Drugs\Tables\DrugsTable;
use App\Models\Drug;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

use UnitEnum;

class DrugResource extends Resource
{
    protected static ?string $model = Drug::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBeaker;

    protected static string|UnitEnum|null $navigationGroup = 'Katalog Obat';

    protected static ?string $navigationLabel = 'Obat';

    protected static ?string $modelLabel = 'Obat';

    protected static ?string $pluralModelLabel = 'Katalog Obat';

    public static function form(Schema $schema): Schema
    {
        return DrugForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DrugsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            PricesRelationManager::class,
            InteractionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDrugs::route('/'),
            'create' => CreateDrug::route('/create'),
            'edit' => EditDrug::route('/{record}/edit'),
        ];
    }
}