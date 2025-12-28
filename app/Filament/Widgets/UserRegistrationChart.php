<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class UserRegistrationChart extends ChartWidget
{
   protected int|string|array $columnSpan = 1;

   public function getHeading(): ?string
   {
      return 'Registrasi User 7 Hari Terakhir';
   }

   public static function getSort(): int
   {
      return 3;
   }

   protected function getData(): array
   {
      $data = [];
      $labels = [];

      for ($i = 6; $i >= 0; $i--) {
         $date = Carbon::now()->subDays($i);
         $labels[] = $date->format('d M');
         $data[] = User::whereDate('created_at', $date)->count();
      }

      return [
         'datasets' => [
            [
               'label' => 'User Baru',
               'data' => $data,
               'borderColor' => '#F4AFE9',
               'backgroundColor' => 'rgba(244, 175, 233, 0.3)',
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
