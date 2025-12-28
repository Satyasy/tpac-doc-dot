<?php

namespace App\Filament\Resources\HealthInsights\Pages;

use App\Filament\Resources\HealthInsights\HealthInsightResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHealthInsights extends ListRecords
{
   protected static string $resource = HealthInsightResource::class;

   protected function getHeaderActions(): array
   {
      return [
         CreateAction::make(),
      ];
   }
}
