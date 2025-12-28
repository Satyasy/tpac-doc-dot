<?php

namespace App\Filament\Resources\MentalHealthLogs\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms;

class MentalHealthLogForm
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

            Forms\Components\Select::make('mood')
               ->options([
                  1 => '1 - Very Bad',
                  2 => '2 - Bad',
                  3 => '3 - Neutral',
                  4 => '4 - Good',
                  5 => '5 - Very Good',
               ])
               ->required(),

            Forms\Components\Select::make('stress_level')
               ->options([
                  1 => '1 - Very Low',
                  2 => '2 - Low',
                  3 => '3 - Moderate',
                  4 => '4 - High',
                  5 => '5 - Very High',
               ])
               ->required(),

            Forms\Components\TextInput::make('sleep_hours')
               ->numeric()
               ->step(0.5)
               ->minValue(0)
               ->maxValue(24),

            Forms\Components\Textarea::make('note')
               ->columnSpanFull(),

            Forms\Components\DatePicker::make('logged_at')
               ->required()
               ->default(now()),
         ]);
   }
}
