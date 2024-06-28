<?php

namespace CustomLogger;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Http\Kernel;
use CustomLogger\Middleware\LogHttpErrors;

class CustomLoggerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/logging.php', 'logging');
    }

    public function boot()
    {
        // Publish configuration file
        $this->publishes([
            __DIR__.'/../../config/logging.php' => config_path('logging.php'),
        ], 'config');

        // Register middleware
        $this->registerMiddleware(LogHttpErrors::class);

        // Optionally, if you need to publish other assets like views, use:
        // $this->publishes([
        //     __DIR__.'/path/to/assets' => public_path('vendor/yourvendor/yourpackage'),
        // ], 'public');

        // If you need to register any other components like commands or views, do it here
    }

    /**
     * Register middleware to the application's HTTP kernel.
     *
     * @param string $middleware
     * @return void
     */
    protected function registerMiddleware($middleware)
    {
        /** @var Kernel $kernel */
        $kernel = $this->app->make(Kernel::class);
        $kernel->pushMiddleware($middleware);
    }
}
