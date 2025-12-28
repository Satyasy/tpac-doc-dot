<?php

namespace App\Filament\Resources\SystemFlags\Pages;

use App\Filament\Resources\SystemFlags\SystemFlagResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSystemFlag extends EditRecord
{
   protected static string $resource = SystemFlagResource::class;

   protected function getHeaderActions(): array
   {
      return [
         DeleteAction::make(),
      ];
   }
}
