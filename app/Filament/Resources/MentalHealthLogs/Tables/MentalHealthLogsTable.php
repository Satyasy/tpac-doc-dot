<?php

namespace App\Filament\Resources\MentalHealthLogs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables;

class MentalHealthLogsTable
{
   public static function configure(Table $table): Table
   {
      return $table
         ->columns([
            Tables\Columns\TextColumn::make('user.name')->label('User')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('mood')->badge()
               ->color(fn(int $state): string => match ($state) {
                  1 => 'danger',
                  2 => 'warning',
                  3 => 'gray',
                  4 => 'info',
                  5 => 'success',
                  default => 'gray',
               }),
            Tables\Columns\TextColumn::make('stress_level')->badge()
               ->color(fn(int $state): string => match ($state) {
                  1 => 'success',
                  2 => 'info',
                  3 => 'gray',
                  4 => 'warning',
                  5 => 'danger',
                  default => 'gray',
               }),
            Tables\Columns\TextColumn::make('sleep_hours')->suffix(' hrs'),
            Tables\Columns\TextColumn::make('logged_at')->date()->sortable(),
            Tables\Columns\TextColumn::make('created_at')->since(),
         ])
         ->defaultSort('logged_at', 'desc')
         ->filters([
            Tables\Filters\SelectFilter::make('mood')
               ->options([
                  1 => '1 - Very Bad',
                  2 => '2 - Bad',
                  3 => '3 - Neutral',
                  4 => '4 - Good',
                  5 => '5 - Very Good',
               ]),
            Tables\Filters\SelectFilter::make('stress_level')
               ->options([
                  1 => '1 - Very Low',
                  2 => '2 - Low',
                  3 => '3 - Moderate',
                  4 => '4 - High',
                  5 => '5 - Very High',
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
