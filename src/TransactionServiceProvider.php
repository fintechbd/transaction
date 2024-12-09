<?php

namespace Fintech\Transaction;

use Fintech\Core\Traits\Packages\RegisterPackageTrait;
use Fintech\Transaction\Commands\InstallCommand;
use Fintech\Transaction\Commands\ResetUserBalanceCommand;
use Fintech\Transaction\Providers\RepositoryServiceProvider;
use Illuminate\Support\ServiceProvider;

class TransactionServiceProvider extends ServiceProvider
{
    use RegisterPackageTrait;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->packageCode = 'transaction';

        $this->mergeConfigFrom(
            __DIR__.'/../config/transaction.php', 'fintech.transaction'
        );

        $this->app->register(RepositoryServiceProvider::class);
    }

    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        $this->injectOnConfig();

        $this->publishes([
            __DIR__.'/../config/transaction.php' => config_path('fintech/transaction.php'),
        ]);

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->loadTranslationsFrom(__DIR__.'/../lang', 'transaction');

        $this->publishes([
            __DIR__.'/../lang' => $this->app->langPath('vendor/transaction'),
        ]);

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'transaction');

        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/transaction'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
                ResetUserBalanceCommand::class,
            ]);
        }
    }
}
