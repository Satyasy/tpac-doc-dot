<?php

namespace App\Filament\Resources\PhysicalHealthLogs\Pages;

use App\Filament\Resources\PhysicalHealthLogs\PhysicalHealthLogResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPhysicalHealthLog extends EditRecord
{
   protected static string $resource = PhysicalHealthLogResource::class;

   protected function getHeaderActions(): array
   {
      return [
         DeleteAction::make(),
      ];
   }
}
