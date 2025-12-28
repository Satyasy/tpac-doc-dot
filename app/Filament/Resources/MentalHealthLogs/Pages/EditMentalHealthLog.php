<?php

namespace App\Filament\Resources\MentalHealthLogs\Pages;

use App\Filament\Resources\MentalHealthLogs\MentalHealthLogResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMentalHealthLog extends EditRecord
{
   protected static string $resource = MentalHealthLogResource::class;

   protected function getHeaderActions(): array
   {
      return [
         DeleteAction::make(),
      ];
   }
}
