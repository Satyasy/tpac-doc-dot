<?php

namespace App\Filament\Doctor\Pages;

use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class DoctorDashboard extends Page
{
   protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

   protected string $view = 'filament.doctor.pages.doctor-dashboard';

   protected static ?string $title = 'Dashboard Dokter';

   protected static ?string $navigationLabel = 'Dashboard';

   protected static ?int $navigationSort = -2;

   public function getViewData(): array
   {
      return [
         'user' => auth()->user(),
      ];
   }
}
