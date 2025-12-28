<?php

namespace App\Filament\Resources\SystemFlags\Pages;

use App\Filament\Resources\SystemFlags\SystemFlagResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSystemFlags extends ListRecords
{
   protected static string $resource = SystemFlagResource::class;

   protected function getHeaderActions(): array
   {
      return [
         CreateAction::make(),
      ];
   }
}
