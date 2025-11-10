<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App as LaravelApp;

class AppServiceProvider extends ServiceProvider
{
    public const HOME = '/redirect'; // ✅ Class constant should go here

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set application locale from session (if set) so views use translations
        try {
            $locale = session('locale', config('app.locale'));
            if ($locale) {
                LaravelApp::setLocale($locale);
            }
        } catch (\Exception $e) {
            // session() may not be available in some CLI contexts; ignore silently
        }
    }
}
