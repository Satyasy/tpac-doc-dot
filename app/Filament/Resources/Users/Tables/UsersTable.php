<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables;

class UsersTable
{
   public static function configure(Table $table): Table
   {
      return $table
         ->columns([
            Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('email')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('roles.name')
               ->label('Role')
               ->badge()
               ->formatStateUsing(fn(?string $state): string => $state ? ucfirst($state) : 'User')
               ->color(fn(?string $state): string => match ($state) {
                  'super_admin' => 'danger',
                  'doctor' => 'info',
                  default => 'success',
               }),
            Tables\Columns\TextColumn::make('email_verified_at')->dateTime()->sortable(),
            Tables\Columns\TextColumn::make('created_at')->since(),
         ])
         ->filters([
            Tables\Filters\SelectFilter::make('roles')
               ->relationship('roles', 'name')
               ->label('Role')
               ->options([
                  'doctor' => 'Doctor',
                  'super_admin' => 'Super Admin',
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
