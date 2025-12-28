<?php

namespace App\Filament\Resources\SystemFlags\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables;

class SystemFlagsTable
{
   public static function configure(Table $table): Table
   {
      return $table
         ->columns([
            Tables\Columns\TextColumn::make('keyword')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('severity')->badge()
               ->color(fn(string $state): string => match ($state) {
                  'low' => 'success',
                  'medium' => 'warning',
                  'high' => 'danger',
                  'critical' => 'danger',
                  default => 'gray',
               }),
            Tables\Columns\TextColumn::make('action'),
         ])
         ->filters([
            Tables\Filters\SelectFilter::make('severity')
               ->options([
                  'low' => 'Low',
                  'medium' => 'Medium',
                  'high' => 'High',
                  'critical' => 'Critical',
               ]),
         ])
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
