<?php

namespace App\Filament\Resources\AiAuditLogs\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables;

class AiAuditLogsTable
{
   public static function configure(Table $table): Table
   {
      return $table
         ->columns([
            Tables\Columns\TextColumn::make('user.name')->label('User')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('prompt')->limit(50)->searchable(),
            Tables\Columns\TextColumn::make('model')->badge()->sortable(),
            Tables\Columns\IconColumn::make('blocked')->boolean()
               ->trueColor('danger')
               ->falseColor('success'),
            Tables\Columns\TextColumn::make('reason')->limit(30),
            Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
         ])
         ->defaultSort('created_at', 'desc')
         ->filters([
            Tables\Filters\TernaryFilter::make('blocked'),
            Tables\Filters\SelectFilter::make('model')
               ->options(fn() => \App\Models\AiAuditLog::distinct()->pluck('model', 'model')->toArray()),
         ])
         ->recordActions([
            ViewAction::make(),
         ]);
   }
}
