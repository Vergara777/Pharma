<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Profile;
use App\Filament\Pages\Settings;
use App\Notifications\LowStockNotification;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationGroup;
use Filament\Notifications\Notification;
use Filament\Pages\Dashboard;
use Filament\Support\Assets\Js;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Colors;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Filament\Panel;
use Filament\PanelProvider;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function boot(): void
    {
        // Ejecutar verificación de stock y vencimientos de forma global
        // El método ya tiene su propio cache de 5 min para no saturar
        \Filament\Support\Facades\FilamentView::registerRenderHook(
            'panels::body.start',
            fn() => \App\Notifications\LowStockNotification::send()
        );
    }

    public function panel(Panel $panel): Panel
    {
        // Obtener configuración desde la base de datos
        try {
            $pharmacyName = \App\Models\Setting::get('pharmacy_name', config('app.name'));
        } catch (\Exception $e) {
            $pharmacyName = config('app.name');
        }

        return $panel
            ->default()
            ->id('admin')
            ->brandName($pharmacyName)
            ->brandLogo('/Images/Pharma1.jpeg')
            ->brandLogoHeight('3.5rem')
            ->sidebarCollapsibleOnDesktop()
            ->path('admin')
            ->darkMode(true)  // Habilitar selector de modo oscuro
            ->renderHook(
                'panels::body.end',
                fn() => view('filament.hooks.money-input-format')
            )
            ->renderHook(
                'panels::body.end',
                fn() => view('filament.hooks.avatar-refresh')
            )
            ->renderHook(
                'panels::body.end',
                fn() => view('filament.hooks.money-format')
            )
            ->renderHook(
                'panels::body.end',
                fn() => view('filament.hooks.currency-format')
            )
            ->renderHook(
                'panels::styles.before',
                fn() => view('filament.hooks.custom-styles')
            )
            ->renderHook(
                'panels::user-menu.before',
                fn() => view('filament.hooks.shopping-cart-trigger')
            )
            ->renderHook(
                'panels::body.end',
                fn() => view('filament.hooks.barcode-scanner-listener')
            )
            ->renderHook(
                'panels::body.end',
                fn() => view('filament.hooks.page-expiry-handler')
            )
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->label('Mi Perfil')
                    ->url(fn(): string => Profile::getUrl())
                    ->icon('heroicon-o-user-circle'),
                'settings' => MenuItem::make()
                    ->label('Configuración')
                    ->url(fn(): string => Settings::getUrl())
                    ->icon('heroicon-o-cog-6-tooth'),
            ])
            ->databaseNotifications()
            ->databaseNotificationsPolling('60s')
            ->font('Poppins')
            ->login()
            ->colors([
                'primary' => Color::Amber,
                'danger' => Color::Red,
            ])
            ->sidebarWidth('20rem')
            ->navigationGroups([
                NavigationGroup::make('Configuración')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsible(),
            ])
            ->registration(false)
            ->passwordReset()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->resources([
                \App\Filament\Resources\CashSessionResource::class,
            ])
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
                Profile::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                // Widgets personalizados del dashboard
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
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
