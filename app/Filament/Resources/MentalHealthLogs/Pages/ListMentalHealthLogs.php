<?php

namespace App\Filament\Resources\MentalHealthLogs\Pages;

use App\Filament\Resources\MentalHealthLogs\MentalHealthLogResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMentalHealthLogs extends ListRecords
{
   protected static string $resource = MentalHealthLogResource::class;

   protected function getHeaderActions(): array
   {
      return [
         CreateAction::make(),
      ];
   }
}
