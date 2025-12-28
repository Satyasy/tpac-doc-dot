<?php

namespace App\Filament\Resources\PhysicalHealthLogs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables;

class PhysicalHealthLogsTable
{
   public static function configure(Table $table): Table
   {
      return $table
         ->columns([
            Tables\Columns\TextColumn::make('user.name')->label('User')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('weight_kg')->suffix(' kg')->sortable(),
            Tables\Columns\TextColumn::make('blood_pressure'),
            Tables\Columns\TextColumn::make('activity_minutes')->suffix(' min'),
            Tables\Columns\TextColumn::make('logged_at')->date()->sortable(),
            Tables\Columns\TextColumn::make('created_at')->since(),
         ])
         ->defaultSort('logged_at', 'desc')
         ->recordActions([
            EditAction::make(),
         ])
         ->toolbarActions([
            BulkActionGroup::make([
               DeleteBulkAction::make(),
            ]),
         ]);
   }
}
