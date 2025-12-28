<?php

namespace App\Filament\Resources\SystemFlags\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms;

class SystemFlagForm
{
   public static function configure(Schema $schema): Schema
   {
      return $schema
         ->components([
            Forms\Components\TextInput::make('keyword')
               ->required()
               ->maxLength(255),

            Forms\Components\Select::make('severity')
               ->options([
                  'low' => 'Low',
                  'medium' => 'Medium',
                  'high' => 'High',
                  'critical' => 'Critical',
               ])
               ->required(),

            Forms\Components\TextInput::make('action')
               ->required()
               ->maxLength(255),
         ]);
   }
}
