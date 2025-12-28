<?php

namespace App\Filament\Resources\ChatSessions\Pages;

use App\Filament\Resources\ChatSessions\ChatSessionResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Placeholder;

class ViewChatSession extends ViewRecord
{
   protected static string $resource = ChatSessionResource::class;

   public function infolist(Schema $schema): Schema
   {
      return $schema
         ->components([
            Section::make('Session Info')
               ->schema([
                  Placeholder::make('id')->label('Session ID')
                     ->content(fn($record) => $record->id),
                  Placeholder::make('user_name')->label('User')
                     ->content(fn($record) => $record->user?->name),
                  Placeholder::make('title')
                     ->content(fn($record) => $record->title),
                  Placeholder::make('created_at')
                     ->content(fn($record) => $record->created_at->format('Y-m-d H:i:s')),
               ])->columns(2),

            Section::make('Messages')
               ->schema([
                  Placeholder::make('messages_list')
                     ->label('')
                     ->content(fn($record) => view('filament.chat-messages', ['messages' => $record->messages])),
               ]),
         ]);
   }
}
