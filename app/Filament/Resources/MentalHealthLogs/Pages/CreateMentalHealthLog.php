<?php

namespace App\Filament\Resources\MentalHealthLogs\Pages;

use App\Filament\Resources\MentalHealthLogs\MentalHealthLogResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMentalHealthLog extends CreateRecord
{
   protected static string $resource = MentalHealthLogResource::class;
}
