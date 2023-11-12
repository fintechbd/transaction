<?php

// config for Fintech/Transaction
return [

    /*
    |--------------------------------------------------------------------------
    | Enable Module APIs
    |--------------------------------------------------------------------------
    | this setting enable the api will be available or not
    */
    'enabled' => env('PACKAGE_TRANSACTION_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Transaction Group Root Prefix
    |--------------------------------------------------------------------------
    |
    | This value will be added to all your routes from this package
    | Example: APP_URL/{root_prefix}/api/transaction/action
    |
    | Note: while adding prefix add closing ending slash '/'
    */

    'root_prefix' => 'test/',

    /*
    |--------------------------------------------------------------------------
    | TransactionForm Model
    |--------------------------------------------------------------------------
    |
    | This value will be used to across system where model is needed
    */
    'transaction_form_model' => \Fintech\Transaction\Models\TransactionForm::class,

    /*
    |--------------------------------------------------------------------------
    | Order Model
    |--------------------------------------------------------------------------
    |
    | This value will be used to across system where model is needed
    */
    'order_model' => \Fintech\Transaction\Models\Order::class,

    /*
    |--------------------------------------------------------------------------
    | OrderDetail Model
    |--------------------------------------------------------------------------
    |
    | This value will be used to across system where model is needed
    */
    'order_detail_model' => \Fintech\Transaction\Models\OrderDetail::class,


    /*
    |--------------------------------------------------------------------------
    | ChartClass Model
    |--------------------------------------------------------------------------
    |
    | This value will be used to across system where model is needed
    */
    'chart_class_model' => \Fintech\Transaction\Models\ChartClass::class,


    /*
    |--------------------------------------------------------------------------
    | ChartType Model
    |--------------------------------------------------------------------------
    |
    | This value will be used to across system where model is needed
    */
    'chart_type_model' => \Fintech\Transaction\Models\ChartType::class,


    /*
    |--------------------------------------------------------------------------
    | ChartEntry Model
    |--------------------------------------------------------------------------
    |
    | This value will be used to across system where model is needed
    */
    'chart_entry_model' => \Fintech\Transaction\Models\ChartEntry::class,

    //** Model Config Point Do not Remove **//

    /*
    |--------------------------------------------------------------------------
    | Repositories
    |--------------------------------------------------------------------------
    |
    | This value will be used across systems where a repositoy instance is needed
    */

    'repositories' => [
        \Fintech\Transaction\Interfaces\TransactionFormRepository::class => \Fintech\Transaction\Repositories\Eloquent\TransactionFormRepository::class,

        \Fintech\Transaction\Interfaces\OrderRepository::class => \Fintech\Transaction\Repositories\Eloquent\OrderRepository::class,

        \Fintech\Transaction\Interfaces\OrderDetailRepository::class => \Fintech\Transaction\Repositories\Eloquent\OrderDetailRepository::class,

        \Fintech\Transaction\Interfaces\ChartClassRepository::class => \Fintech\Transaction\Repositories\Eloquent\ChartClassRepository::class,

        \Fintech\Transaction\Interfaces\ChartTypeRepository::class => \Fintech\Transaction\Repositories\Eloquent\ChartTypeRepository::class,

        \Fintech\Transaction\Interfaces\ChartEntryRepository::class => \Fintech\Transaction\Repositories\Eloquent\ChartEntryRepository::class,

        //** Repository Binding Config Point Do not Remove **//
    ],

];
