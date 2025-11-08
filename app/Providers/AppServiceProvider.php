<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Category;

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
        
        View::composer('layouts.partial.header', function ($view) {
            $categories = Category::where('active', 1)->orderBy('name', 'asc')->get();
            $view->with('categories', $categories);
        });
    }
}
