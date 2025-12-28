<?php

namespace App\Filament\Resources\PhysicalHealthLogs;

use App\Filament\Resources\PhysicalHealthLogs\Pages\CreatePhysicalHealthLog;
use App\Filament\Resources\PhysicalHealthLogs\Pages\EditPhysicalHealthLog;
use App\Filament\Resources\PhysicalHealthLogs\Pages\ListPhysicalHealthLogs;
use App\Filament\Resources\PhysicalHealthLogs\Schemas\PhysicalHealthLogForm;
use App\Filament\Resources\PhysicalHealthLogs\Tables\PhysicalHealthLogsTable;
use App\Models\PhysicalHealthLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

use UnitEnum;

class PhysicalHealthLogResource extends Resource
{
   protected static ?string $model = PhysicalHealthLog::class;

   protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBeaker;

   protected static string|UnitEnum|null $navigationGroup = 'Kesehatan User';

   protected static ?string $navigationLabel = 'Log Fisik';

   protected static ?string $modelLabel = 'Log Kesehatan Fisik';

   protected static ?string $pluralModelLabel = 'Log Kesehatan Fisik';

   public static function form(Schema $schema): Schema
   {
      return PhysicalHealthLogForm::configure($schema);
   }

   public static function table(Table $table): Table
   {
      return PhysicalHealthLogsTable::configure($table);
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
         'index' => ListPhysicalHealthLogs::route('/'),
         'create' => CreatePhysicalHealthLog::route('/create'),
         'edit' => EditPhysicalHealthLog::route('/{record}/edit'),
      ];
   }
}
