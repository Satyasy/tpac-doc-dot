<?php

namespace App\Filament\Resources\HealthInsights;

use App\Filament\Resources\HealthInsights\Pages\CreateHealthInsight;
use App\Filament\Resources\HealthInsights\Pages\EditHealthInsight;
use App\Filament\Resources\HealthInsights\Pages\ListHealthInsights;
use App\Filament\Resources\HealthInsights\Schemas\HealthInsightForm;
use App\Filament\Resources\HealthInsights\Tables\HealthInsightsTable;
use App\Models\HealthInsight;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

use UnitEnum;

class HealthInsightResource extends Resource
{
   protected static ?string $model = HealthInsight::class;

   protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLightBulb;

   protected static string|UnitEnum|null $navigationGroup = 'Kesehatan User';

   protected static ?string $navigationLabel = 'Health Insights';

   protected static ?string $modelLabel = 'Health Insight';

   protected static ?string $pluralModelLabel = 'Health Insights';

   public static function form(Schema $schema): Schema
   {
      return HealthInsightForm::configure($schema);
   }

   public static function table(Table $table): Table
   {
      return HealthInsightsTable::configure($table);
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
         'index' => ListHealthInsights::route('/'),
         'create' => CreateHealthInsight::route('/create'),
         'edit' => EditHealthInsight::route('/{record}/edit'),
      ];
   }
}
