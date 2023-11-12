<?php

// config for Fintech/Transaction
return [

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

        //** Repository Binding Config Point Do not Remove **//
    ],

];
