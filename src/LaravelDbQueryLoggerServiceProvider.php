<?php

namespace EyadBereh\LaravelDbQueryLogger;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use EyadBereh\LaravelDbQueryLogger\Commands\LaravelDbQueryLoggerCommand;
use EyadBereh\LaravelDbQueryLogger\Drivers\AbstractDriver;
use EyadBereh\LaravelDbQueryLogger\Providers\EventServiceProvider;

class LaravelDbQueryLoggerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-db-query-logger')
            ->hasConfigFile("db-query-logger")
            ->hasCommand(LaravelDbQueryLoggerCommand::class);

        $this->app->register(EventServiceProvider::class);

        $driver = config("db-query-logger.driver");
        $driver_info = config("db-query-logger.drivers.$driver");
        $concrete    = $driver_info["concrete"];

        $reflectionClass = new \ReflectionClass($concrete);

        if ($reflectionClass->isAbstract()) {
            throw new \Exception("The specified logging driver [$concrete] must not be abstract");
        }

        if (!$reflectionClass->isInstantiable()) {
            throw new \Exception("The specified logging driver [$concrete] must be instantiatable");
        }

        if (!$reflectionClass->isSubclassOf(AbstractDriver::class)) {
            throw new \Exception("The specified logging driver [$concrete] must extend the class [EyadBereh\\LaravelDbQueryLogger\\Drivers\\AbstractDriver]");
        }

        $this->app->bind(AbstractDriver::class, $concrete);
    }
}
