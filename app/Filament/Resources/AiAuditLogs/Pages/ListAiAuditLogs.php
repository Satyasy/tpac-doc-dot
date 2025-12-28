<?php

namespace App\Filament\Resources\AiAuditLogs\Pages;

use App\Filament\Resources\AiAuditLogs\AiAuditLogResource;
use Filament\Resources\Pages\ListRecords;

class ListAiAuditLogs extends ListRecords
{
   protected static string $resource = AiAuditLogResource::class;
}
