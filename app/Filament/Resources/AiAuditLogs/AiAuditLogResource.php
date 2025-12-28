<?php

namespace App\Filament\Resources\AiAuditLogs;

use App\Filament\Resources\AiAuditLogs\Pages\ListAiAuditLogs;
use App\Filament\Resources\AiAuditLogs\Pages\ViewAiAuditLog;
use App\Filament\Resources\AiAuditLogs\Tables\AiAuditLogsTable;
use App\Models\AiAuditLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

use UnitEnum;

class AiAuditLogResource extends Resource
{
   protected static ?string $model = AiAuditLog::class;

   protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

   protected static string|UnitEnum|null $navigationGroup = 'Sistem';

   protected static ?string $navigationLabel = 'AI Audit Log';

   protected static ?string $modelLabel = 'AI Audit Log';

   protected static ?string $pluralModelLabel = 'AI Audit Logs';

   public static function table(Table $table): Table
   {
      return AiAuditLogsTable::configure($table);
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
         'index' => ListAiAuditLogs::route('/'),
         'view' => ViewAiAuditLog::route('/{record}'),
      ];
   }
}
