<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Share hotel settings with ALL views (layouts, sidebar, etc.)
        View::composer('*', function ($view) {
            try {
                if (Schema::hasTable('settings')) {
                    $hotelName    = Setting::get('hotel_name',    'Merahkie PMS Lite');
                    $hotelPhone   = Setting::get('hotel_phone',   '');
                    $hotelEmail   = Setting::get('hotel_email',   '');
                    $hotelAddress = Setting::get('hotel_address', '');
                    $hotelWebsite = Setting::get('hotel_website', '');
                    $currency     = Setting::get('currency',      'USD');
                } else {
                    $hotelName    = 'Merahkie PMS Lite';
                    $hotelPhone   = $hotelEmail = $hotelAddress = $hotelWebsite = $currency = '';
                }
            } catch (\Exception $e) {
                $hotelName    = 'Merahkie PMS Lite';
                $hotelPhone   = $hotelEmail = $hotelAddress = $hotelWebsite = $currency = '';
            }

            $view->with([
                'hotelName'    => $hotelName,
                'hotelPhone'   => $hotelPhone,
                'hotelEmail'   => $hotelEmail,
                'hotelAddress' => $hotelAddress,
                'hotelWebsite' => $hotelWebsite,
                'currency'     => $currency,
            ]);
        });
    }
}
