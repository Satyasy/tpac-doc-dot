<?php

namespace App\Filament\Resources\AiAuditLogs\Pages;

use App\Filament\Resources\AiAuditLogs\AiAuditLogResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;

class ViewAiAuditLog extends ViewRecord
{
   protected static string $resource = AiAuditLogResource::class;

   public function infolist(Schema $schema): Schema
   {
      return $schema
         ->components([
            Section::make('Log Details')
               ->schema([
                  Placeholder::make('user_name')->label('User')
                     ->content(fn($record) => $record->user?->name),
                  Placeholder::make('model')
                     ->content(fn($record) => $record->model),
                  Placeholder::make('blocked')
                     ->content(fn($record) => $record->blocked ? 'Blocked' : 'Allowed'),
                  Placeholder::make('reason')
                     ->content(fn($record) => $record->reason),
                  Placeholder::make('created_at')
                     ->content(fn($record) => $record->created_at->format('Y-m-d H:i:s')),
               ])->columns(2),

            Section::make('Prompt')
               ->schema([
                  Placeholder::make('prompt_content')
                     ->label('')
                     ->content(fn($record) => new HtmlString(nl2br(e($record->prompt)))),
               ]),

            Section::make('Retrieved Sources')
               ->schema([
                  Placeholder::make('retrieved_source_content')
                     ->label('')
                     ->content(fn($record) => new HtmlString('<pre>' . e(json_encode($record->retrieved_source, JSON_PRETTY_PRINT)) . '</pre>')),
               ]),
         ]);
   }
}
