<?php

namespace App\Filament\Widgets;

use App\Models\ChatSession;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestConsultations extends BaseWidget
{
   protected int|string|array $columnSpan = 'full';

   public function getHeading(): ?string
   {
      return 'Konsultasi Terbaru';
   }

   public static function getSort(): int
   {
      return 4;
   }

   public function table(Table $table): Table
   {
      return $table
         ->query(
            ChatSession::query()
               ->with(['user', 'messages'])
               ->latest()
               ->limit(5)
         )
         ->columns([
            Tables\Columns\TextColumn::make('user.name')
               ->label('Pengguna')
               ->searchable(),
            Tables\Columns\TextColumn::make('title')
               ->label('Topik')
               ->limit(40),
            Tables\Columns\TextColumn::make('messages_count')
               ->label('Pesan')
               ->counts('messages'),
            Tables\Columns\TextColumn::make('created_at')
               ->label('Waktu')
               ->dateTime('d M Y, H:i')
               ->sortable(),
         ])
         ->paginated(false);
   }
}
