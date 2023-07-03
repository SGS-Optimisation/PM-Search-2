<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;
use Laravel\Nova\Panel;
use Outl1ne\NovaSettings\NovaSettings;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        NovaSettings::addSettingsFields([
            Panel::make('Matomo Tracking', [
                Boolean::make('Enable', 'matomo_tracking_enabled')
                    ->required()->default(false),
                Text::make('Matomo Host', 'matomo_host')
                    ->default('https://ma.sgsco.com'),
                Text::make('Matomo Site ID', 'matomo_site_id'),
            ])
        ], [], 'features');

        NovaSettings::addSettingsFields([
            Text::make('Api Auth Path', 'photon_api_auth_path')->required(),
            Text::make('Api Base Path', 'photon_api_base_path')->required(),
            Text::make('Api Version', 'photon_api_version')->required(),
            Text::make('Api App Id', 'photon_api_app_id')->required(),
            Text::make('Api App Key', 'photon_api_app_key')->required(),
            Text::make('Subscription Key', 'photon_subscription_key')->required(),

            Number::make('Cache Duration', 'photon_api_cache_duration')
                ->min(0)
                ->default(5)
                ->help('In minutes'),

        ], [], 'photon-api');
    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        Nova::routes()
            ->withAuthenticationRoutes()
            ->withPasswordResetRoutes()
            ->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewNova', function ($user) {
            return Str::endsWith(Str::lower($user->email), '@sgsco.com');
        });
    }

    /**
     * Get the dashboards that should be listed in the Nova sidebar.
     *
     * @return array
     */
    protected function dashboards()
    {
        return [
            new \App\Nova\Dashboards\Main,
        ];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        return [
            \Vyuldashev\NovaPermission\NovaPermissionTool::make(),
            new \Outl1ne\NovaSettings\NovaSettings
        ];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
