<?php

namespace App\Filament\Resources\HealthInsights\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms;

class HealthInsightForm
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

            Forms\Components\Select::make('type')
               ->options([
                  'mental' => 'Mental',
                  'physical' => 'Physical',
               ])
               ->required(),

            Forms\Components\Textarea::make('summary')
               ->required()
               ->columnSpanFull(),

            Forms\Components\Select::make('risk_level')
               ->options([
                  'low' => 'Low',
                  'medium' => 'Medium',
                  'high' => 'High',
               ])
               ->required(),
         ]);
   }
}
