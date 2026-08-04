<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $adminPath = env('ADMIN_PATH', 'ami3-17drt4-6ne634russ');

        return $panel
            ->default()
            ->id($adminPath)
            ->path($adminPath)
            ->login(\App\Filament\Pages\Auth\Login::class)
            ->passwordReset()
            ->brandName(config('app.name'))
            ->colors([
                'primary' => Color::Indigo,
            ])
            ->font('Inter')
            ->favicon(asset('favicon.ico'))
            ->spa()
            ->collapsibleNavigationGroups()
            ->navigationGroups([
                '訂單管理',
                '商品管理',
                '評論管理',
                '內容管理',
                'SEO 管理',
                '客服管理',
                '工具',
                '系統管理',
            ])
            ->renderHook(\Filament\View\PanelsRenderHook::HEAD_START, fn () => view('filament.hooks.custom-styles'))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                \App\Filament\Widgets\RightStatsWidget::class,
                \App\Filament\Widgets\PageAccessRankingWidget::class,
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
