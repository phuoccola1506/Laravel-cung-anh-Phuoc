<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use App\Models\Category;
use App\Models\Setting;

class AppServiceProvider extends ServiceProvider
{
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
        // Set default pagination view to Bootstrap
        \Illuminate\Pagination\Paginator::useBootstrapFive();
        
        // Share categories with header
        View::composer('layouts.partial.header', function ($view) {
            $categories = Category::where('active', 1)->orderBy('name', 'asc')->get();
            $view->with('categories', $categories);
        });

        // Share currency with all views
        View::composer('*', function ($view) {
            $currency = Setting::get('currency', 'VND');
            $view->with('currency', $currency);
        });

        // Blade directive để format giá
        Blade::directive('price', function ($expression) {
            return "<?php echo number_format($expression, 0, ',', '.') . ' ' . \$currency; ?>";
        });
    }
}
