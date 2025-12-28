<?php

namespace App\Filament\Resources\SystemFlags;

use App\Filament\Resources\SystemFlags\Pages\CreateSystemFlag;
use App\Filament\Resources\SystemFlags\Pages\EditSystemFlag;
use App\Filament\Resources\SystemFlags\Pages\ListSystemFlags;
use App\Filament\Resources\SystemFlags\Schemas\SystemFlagForm;
use App\Filament\Resources\SystemFlags\Tables\SystemFlagsTable;
use App\Models\SystemFlag;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

use UnitEnum;

class SystemFlagResource extends Resource
{
   protected static ?string $model = SystemFlag::class;

   protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFlag;

   protected static string|UnitEnum|null $navigationGroup = 'Sistem';

   protected static ?string $navigationLabel = 'System Flags';

   protected static ?string $modelLabel = 'System Flag';

   protected static ?string $pluralModelLabel = 'System Flags';

   public static function form(Schema $schema): Schema
   {
      return SystemFlagForm::configure($schema);
   }

   public static function table(Table $table): Table
   {
      return SystemFlagsTable::configure($table);
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
         'index' => ListSystemFlags::route('/'),
         'create' => CreateSystemFlag::route('/create'),
         'edit' => EditSystemFlag::route('/{record}/edit'),
      ];
   }
}
