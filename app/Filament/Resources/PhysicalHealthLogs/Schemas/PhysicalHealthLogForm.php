<?php

namespace App\Filament\Resources\PhysicalHealthLogs\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms;

class PhysicalHealthLogForm
{
   public static function configure(Schema $schema): Schema
   {
      return $schema
         ->components([
            Forms\Components\Select::make('user_id')
               ->relationship('user', 'name')
               ->searchable()
               ->preload()
               ->required(),

            Forms\Components\TextInput::make('weight_kg')
               ->label('Weight (kg)')
               ->numeric()
               ->step(0.1)
               ->minValue(0),

            Forms\Components\TextInput::make('blood_pressure')
               ->label('Blood Pressure')
               ->placeholder('e.g. 120/80'),

            Forms\Components\TextInput::make('activity_minutes')
               ->label('Activity (minutes)')
               ->numeric()
               ->minValue(0),

            Forms\Components\DatePicker::make('logged_at')
               ->required()
               ->default(now()),
         ]);
   }
}
