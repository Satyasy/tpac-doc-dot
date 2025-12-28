<?php

namespace App\Filament\Resources\ChatSessions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables;

class ChatSessionsTable
{
   public static function configure(Table $table): Table
   {
      return $table
         ->columns([
            Tables\Columns\TextColumn::make('id')->label('Session ID')->searchable()->limit(8),
            Tables\Columns\TextColumn::make('user.name')->label('User')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('messages_count')->counts('messages')->label('Messages'),
            Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            Tables\Columns\TextColumn::make('updated_at')->since(),
         ])
         ->defaultSort('created_at', 'desc')
         ->recordActions([
            ViewAction::make(),
         ])
         ->toolbarActions([
            BulkActionGroup::make([
               DeleteBulkAction::make(),
            ]),
         ]);
   }
}
