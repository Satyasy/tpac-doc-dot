<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms;
use Spatie\Permission\Models\Role;

class UserForm
{
   public static function configure(Schema $schema): Schema
   {
      return $schema
         ->components([
            Forms\Components\TextInput::make('name')
               ->required()
               ->maxLength(255),

            Forms\Components\TextInput::make('email')
               ->email()
               ->required()
               ->unique(ignoreRecord: true)
               ->maxLength(255),

            Forms\Components\TextInput::make('password')
               ->password()
               ->required(fn(string $operation): bool => $operation === 'create')
               ->dehydrated(fn(?string $state) => filled($state))
               ->maxLength(255),

            Forms\Components\Select::make('role_select')
               ->label('Role')
               ->options(function () {
                  return Role::whereIn('name', ['doctor'])
                     ->pluck('name', 'name')
                     ->mapWithKeys(fn($name) => [$name => ucfirst($name)]);
               })
               ->placeholder('User (Default)')
               ->helperText('Kosongkan untuk role User biasa. Admin dikelola via Shield.')
               ->afterStateHydrated(function ($component, $record) {
                  if ($record) {
                     // Get first non-super_admin role
                     $role = $record->roles->whereNotIn('name', ['super_admin'])->first();
                     $component->state($role?->name);
                  }
               })
               ->dehydrated(false)
               ->afterStateUpdated(function ($state, $record) {
                  if ($record) {
                     // Remove old non-admin roles and assign new one
                     $record->roles()->detach(
                        Role::whereIn('name', ['doctor'])->pluck('id')
                     );
                     if ($state) {
                        $record->assignRole($state);
                     }
                  }
               })
               ->live(),

            Forms\Components\DateTimePicker::make('email_verified_at')
               ->label('Email Verified At'),
         ]);
   }
}
