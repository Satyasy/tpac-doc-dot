<?php

namespace App\Filament\Resources\HealthInsights\Pages;

use App\Filament\Resources\HealthInsights\HealthInsightResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHealthInsight extends EditRecord
{
   protected static string $resource = HealthInsightResource::class;

   protected function getHeaderActions(): array
   {
      return [
         DeleteAction::make(),
      ];
   }
}
