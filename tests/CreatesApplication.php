<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Artisan;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();
        $app->loadEnvironmentFrom('.env.testing');
        // Migrate the database
        Artisan::call('migrate', [
            '--env' => 'testing', // Specify the environment explicitly
        ]);

        return $app;
    }
}
