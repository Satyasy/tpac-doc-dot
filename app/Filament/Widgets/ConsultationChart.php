<?php

namespace App\Filament\Widgets;

use App\Models\ChatSession;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class ConsultationChart extends ChartWidget
{
   protected int|string|array $columnSpan = 1;

   public function getHeading(): ?string
   {
      return 'Konsultasi 7 Hari Terakhir';
   }

   public static function getSort(): int
   {
      return 2;
   }

   protected function getData(): array
   {
      $data = [];
      $labels = [];

      for ($i = 6; $i >= 0; $i--) {
         $date = Carbon::now()->subDays($i);
         $labels[] = $date->format('d M');
         $data[] = ChatSession::whereDate('created_at', $date)->count();
      }

      return [
         'datasets' => [
            [
               'label' => 'Sesi Konsultasi',
               'data' => $data,
               'borderColor' => '#8DD0FC',
               'backgroundColor' => 'rgba(141, 208, 252, 0.3)',
               'fill' => true,
            ],
         ],
         'labels' => $labels,
      ];
   }

   protected function getType(): string
   {
      return 'line';
   }
}
