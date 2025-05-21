<?php

namespace BeMyKad\Laravel;

use BeMyKad\BeMyKad;
use Illuminate\Support\ServiceProvider; // Assuming BeMyKad class is in the global namespace or autoloaded correctly.
use InvalidArgumentException; // To use the InvalidArgumentException class.

class BeMyKadServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('bemykad', function ($app, $params) {
            if (! isset($params['mykadNumber'])) {
                throw new InvalidArgumentException('mykadNumber parameter is required.');
            }

            return new BeMyKad($params['mykadNumber']);
        });

        // Also, register a direct class binding for type-hinting if preferred
        $this->app->bind(BeMyKad::class, function ($app, $params) {
            if (! isset($params['mykadNumber'])) {
                throw new InvalidArgumentException('mykadNumber parameter is required.');
            }

            return new BeMyKad($params['mykadNumber']);
        });
    }
}
