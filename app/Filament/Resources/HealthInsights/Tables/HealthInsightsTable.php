<?php

namespace App\Filament\Resources\HealthInsights\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables;

class HealthInsightsTable
{
   public static function configure(Table $table): Table
   {
      return $table
         ->columns([
            Tables\Columns\TextColumn::make('user.name')->label('User')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('type')->badge()
               ->color(fn(string $state): string => match ($state) {
                  'mental' => 'info',
                  'physical' => 'success',
                  default => 'gray',
               }),
            Tables\Columns\TextColumn::make('summary')->limit(50),
            Tables\Columns\TextColumn::make('risk_level')->badge()
               ->color(fn(string $state): string => match ($state) {
                  'low' => 'success',
                  'medium' => 'warning',
                  'high' => 'danger',
                  default => 'gray',
               }),
            Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
         ])
         ->defaultSort('created_at', 'desc')
         ->filters([
            Tables\Filters\SelectFilter::make('type')
               ->options([
                  'mental' => 'Mental',
                  'physical' => 'Physical',
               ]),
            Tables\Filters\SelectFilter::make('risk_level')
               ->options([
                  'low' => 'Low',
                  'medium' => 'Medium',
                  'high' => 'High',
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
