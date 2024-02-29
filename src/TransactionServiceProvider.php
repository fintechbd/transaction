<?php

namespace Fintech\Transaction;

use Fintech\Transaction\Commands\InstallCommand;
use Fintech\Transaction\Commands\TransactionCommand;
use Illuminate\Support\ServiceProvider;

class TransactionServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/transaction.php', 'fintech.transaction'
        );
    }

    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/transaction.php' => config_path('fintech/transaction.php'),
        ]);

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->loadTranslationsFrom(__DIR__.'/../lang', 'transaction');

        $this->publishes([
            __DIR__.'/../lang' => $this->app->langPath('vendor/transaction'),
        ]);

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'transaction');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/transaction'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
                TransactionCommand::class,
            ]);
        }
    }
}
