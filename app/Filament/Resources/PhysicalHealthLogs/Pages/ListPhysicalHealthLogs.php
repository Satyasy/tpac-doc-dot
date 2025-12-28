<?php

namespace App\Filament\Resources\PhysicalHealthLogs\Pages;

use App\Filament\Resources\PhysicalHealthLogs\PhysicalHealthLogResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPhysicalHealthLogs extends ListRecords
{
   protected static string $resource = PhysicalHealthLogResource::class;

   protected function getHeaderActions(): array
   {
      return [
         CreateAction::make(),
      ];
   }
}
