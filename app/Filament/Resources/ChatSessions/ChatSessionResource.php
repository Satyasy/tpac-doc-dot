<?php

namespace App\Filament\Resources\ChatSessions;

use App\Filament\Resources\ChatSessions\Pages\ListChatSessions;
use App\Filament\Resources\ChatSessions\Pages\ViewChatSession;
use App\Filament\Resources\ChatSessions\Tables\ChatSessionsTable;
use App\Models\ChatSession;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

use UnitEnum;

class ChatSessionResource extends Resource
{
   protected static ?string $model = ChatSession::class;

   protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

   protected static string|UnitEnum|null $navigationGroup = 'Konsultasi';

   protected static ?string $navigationLabel = 'Sesi Chat';

   protected static ?string $modelLabel = 'Sesi Chat';

   protected static ?string $pluralModelLabel = 'Sesi Chat';

   public static function table(Table $table): Table
   {
      return ChatSessionsTable::configure($table);
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
         'index' => ListChatSessions::route('/'),
         'view' => ViewChatSession::route('/{record}'),
      ];
   }
}
