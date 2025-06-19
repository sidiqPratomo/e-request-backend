<?php

namespace App\Providers;

use App\BC\Storage\StorageContext;
use App\Observers\FileUploadObserver;
use App\Observers\OwnersObserver;
use App\Observers\TrancientObserver;
use App\Observers\UsersObserver;
use App\Services\Impl\JsonResponseService;
use App\Services\Report\ReportServiceFactory;
use App\Services\Report\ReportServiceInterface;
use App\Services\ResponseService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $segment = request()->segment(1);

        if (file_exists(app_path('Models/' . Str::studly($segment)) . '.php')) {
            $model = app("App\Models\\" . Str::studly($segment));

            if ($segment == 'user') {
                $model::observe(UsersObserver::class);
            } else {
                $model::observe([
                    OwnersObserver::class,
                    FileUploadObserver::class,
                    TrancientObserver::class,
                ]);
            }
        }

        (new StorageContext())->init($this->app);

        $this->app->bind(ResponseService::class, function () {
            return new JsonResponseService();
        });

        $this->app->bind(ReportServiceInterface::class, function () {
            return new ReportServiceFactory();
        });
    }
}
