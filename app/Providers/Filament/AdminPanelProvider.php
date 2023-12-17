<?php

namespace App\Providers\Filament;

use Filament\FontProviders\GoogleFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use pxlrbt\FilamentSpotlight\SpotlightPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin-dashboard')
            ->login()
            ->colors([
                'primary' => Color::Red,
            ])
            ->globalSearchKeyBindings(['ctrl+k'])
            ->font('Poppins')
            ->favicon('img/favicon.png')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
//                Widgets\FilamentInfoWidget::class,
            ])
        //    ->sidebarCollapsibleOnDesktop()
            ->sidebarFullyCollapsibleOnDesktop()
            ->navigationItems([
                NavigationItem::make('Facebook')
                    ->url('https://facebook.com', true)
                    ->icon('heroicon-o-globe-europe-africa')
                    ->group('Company Social Media pages')
                    ->sort(1),

            NavigationItem::make('Instagram')
                ->url('https://www.instagram.com/', true)
                ->icon('heroicon-o-camera')
                ->group('Company Social Media pages')
                ->sort(2)
                ->visible(fn () => ! auth()->user()->is_admin),
            ])
            ->userMenuItems([
                MenuItem::make()
                    ->label('Settings')
                    ->url('')
                    ->icon('heroicon-o-cog-6-tooth'),

                'logout' => MenuItem::make()->label('Log out')
            ])
            ->plugins([
                SpotlightPlugin::make(),
            ])
            ->breadcrumbs(true)
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
            ]);
    }
}
