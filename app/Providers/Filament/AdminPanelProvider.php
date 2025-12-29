<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
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
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\ConsultationChart;
use App\Filament\Widgets\UserRegistrationChart;
use App\Filament\Widgets\LatestConsultations;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(\App\Filament\Pages\Auth\Login::class)
            ->brandName('DocDot Admin')
            ->brandLogo(asset('images/logo.png'))
            ->brandLogoHeight('3rem')
            ->favicon(asset('favicon.ico'))
            ->colors([
                'primary' => [
                    50 => '#fdf2f8',
                    100 => '#fce7f3',
                    200 => '#fbcfe8',
                    300 => '#f9a8d4',
                    400 => '#F4AFE9',
                    500 => '#ec4899',
                    600 => '#db2777',
                    700 => '#be185d',
                    800 => '#9d174d',
                    900 => '#831843',
                    950 => '#500724',
                ],
                'info' => [
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
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                StatsOverview::class,
                ConsultationChart::class,
                UserRegistrationChart::class,
                LatestConsultations::class,
            ])
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
            ->plugins([
                FilamentShieldPlugin::make()
                    ->gridColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 3,
                    ])
                    ->sectionColumnSpan(1)
                    ->checkboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 4,
                    ])
                    ->resourceCheckboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                    ]),
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->authGuard('web')
            ->sidebarCollapsibleOnDesktop()
            ->navigationGroups([
                'Konsultasi',
                'Konten',
                'Katalog Obat',
                'Kesehatan User',
                'Pengguna',
                'Sistem',
            ]);
    }
}
