<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class DoctorPanelProvider extends PanelProvider
{
   public function panel(Panel $panel): Panel
   {
      return $panel
         ->id('doctor')
         ->path('doctor')
         ->login()
         ->brandName('DocDot Doctor')
         ->brandLogo(asset('images/logo.png'))
         ->brandLogoHeight('3rem')
         ->favicon(asset('favicon.ico'))
         ->colors([
            'primary' => [
               50 => '#f0f9ff',
               100 => '#e0f2fe',
               200 => '#bae6fd',
               300 => '#8DD0FC',
               400 => '#38bdf8',
               500 => '#0ea5e9',
               600 => '#0284c7',
               700 => '#0369a1',
               800 => '#075985',
               900 => '#0c4a6e',
               950 => '#082f49',
            ],
            'success' => Color::Emerald,
            'warning' => Color::Orange,
            'danger' => Color::Rose,
         ])
         ->discoverResources(in: app_path('Filament/Doctor/Resources'), for: 'App\Filament\Doctor\Resources')
         ->discoverPages(in: app_path('Filament/Doctor/Pages'), for: 'App\Filament\Doctor\Pages')
         ->pages([
            \App\Filament\Doctor\Pages\DoctorDashboard::class,
         ])
         ->discoverWidgets(in: app_path('Filament/Doctor/Widgets'), for: 'App\Filament\Doctor\Widgets')
         ->widgets([])
         ->middleware([
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            AuthenticateSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
            DisableBladeIconComponents::class,
            DispatchServingFilamentEvent::class,
         ])
         ->authMiddleware([
            Authenticate::class,
         ])
         ->authGuard('web')
         ->sidebarCollapsibleOnDesktop()
         ->navigationGroups([
            'Dashboard',
            'Konsultasi',
            'Referensi Medis',
         ]);
   }
}
