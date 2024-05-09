<?php

// config for Fintech/Transaction
use Fintech\Transaction\Models\ChartClass;
use Fintech\Transaction\Models\ChartEntry;
use Fintech\Transaction\Models\ChartType;
use Fintech\Transaction\Models\ManualRefund;
use Fintech\Transaction\Models\Order;
use Fintech\Transaction\Models\OrderDetail;
use Fintech\Transaction\Models\OrderQueue;
use Fintech\Transaction\Models\RedeemPoint;
use Fintech\Transaction\Models\RewardPoint;
use Fintech\Transaction\Models\TransactionForm;
use Fintech\Transaction\Models\UserAccount;
use Fintech\Transaction\Repositories\Eloquent\ChartClassRepository;
use Fintech\Transaction\Repositories\Eloquent\ChartEntryRepository;
use Fintech\Transaction\Repositories\Eloquent\ChartTypeRepository;
use Fintech\Transaction\Repositories\Eloquent\ManualRefundRepository;
use Fintech\Transaction\Repositories\Eloquent\OrderDetailRepository;
use Fintech\Transaction\Repositories\Eloquent\OrderQueueRepository;
use Fintech\Transaction\Repositories\Eloquent\OrderRepository;
use Fintech\Transaction\Repositories\Eloquent\RedeemPointRepository;
use Fintech\Transaction\Repositories\Eloquent\RewardPointRepository;
use Fintech\Transaction\Repositories\Eloquent\TransactionFormRepository;
use Fintech\Transaction\Repositories\Eloquent\UserAccountRepository;

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

    /*
    |--------------------------------------------------------------------------
    | Transaction Retry Delay
    |--------------------------------------------------------------------------
    |
    | Note: duration in minutes
    */

    'delay_time' => 15,
    'minimum_balance' => 0,
    /*
    |--------------------------------------------------------------------------
    | TransactionForm Model
    |--------------------------------------------------------------------------
    |
    | This value will be used to across system where model is needed
    */
    'transaction_form_model' => TransactionForm::class,

    /*
    |--------------------------------------------------------------------------
    | Order Model
    |--------------------------------------------------------------------------
    |
    | This value will be used to across system where model is needed
    */
    'order_model' => Order::class,

    /*
    |--------------------------------------------------------------------------
    | OrderDetail Model
    |--------------------------------------------------------------------------
    |
    | This value will be used to across system where model is needed
    */
    'order_detail_model' => OrderDetail::class,

    /*
    |--------------------------------------------------------------------------
    | ChartClass Model
    |--------------------------------------------------------------------------
    |
    | This value will be used to across system where model is needed
    */
    'chart_class_model' => ChartClass::class,

    /*
    |--------------------------------------------------------------------------
    | ChartType Model
    |--------------------------------------------------------------------------
    |
    | This value will be used to across system where model is needed
    */
    'chart_type_model' => ChartType::class,

    /*
    |--------------------------------------------------------------------------
    | ChartEntry Model
    |--------------------------------------------------------------------------
    |
    | This value will be used to across system where model is needed
    */
    'chart_entry_model' => ChartEntry::class,

    /*
    |--------------------------------------------------------------------------
    | UserAccount Model
    |--------------------------------------------------------------------------
    |
    | This value will be used to across system where model is needed
    */
    'user_account_model' => UserAccount::class,

    /*
    |--------------------------------------------------------------------------
    | OrderQueue Model
    |--------------------------------------------------------------------------
    |
    | This value will be used to across system where model is needed
    */
    'order_queue_model' => OrderQueue::class,

    /*
    |--------------------------------------------------------------------------
    | ManualRefund Model
    |--------------------------------------------------------------------------
    |
    | This value will be used to across system where model is needed
    */
    'manual_refund_model' => ManualRefund::class,

    /*
    |--------------------------------------------------------------------------
    | RewardPoint Model
    |--------------------------------------------------------------------------
    |
    | This value will be used to across system where model is needed
    */
    'reward_point_model' => RewardPoint::class,

    /*
    |--------------------------------------------------------------------------
    | RedeemPoint Model
    |--------------------------------------------------------------------------
    |
    | This value will be used to across system where model is needed
    */
    'redeem_point_model' => RedeemPoint::class,

    //** Model Config Point Do not Remove **//

    /*
    |--------------------------------------------------------------------------
    | Repositories
    |--------------------------------------------------------------------------
    |
    | This value will be used across systems where a repositoy instance is needed
    */

    'repositories' => [
        \Fintech\Transaction\Interfaces\TransactionFormRepository::class => TransactionFormRepository::class,

        \Fintech\Transaction\Interfaces\OrderRepository::class => OrderRepository::class,

        \Fintech\Transaction\Interfaces\OrderDetailRepository::class => OrderDetailRepository::class,

        \Fintech\Transaction\Interfaces\ChartClassRepository::class => ChartClassRepository::class,

        \Fintech\Transaction\Interfaces\ChartTypeRepository::class => ChartTypeRepository::class,

        \Fintech\Transaction\Interfaces\ChartEntryRepository::class => ChartEntryRepository::class,

        \Fintech\Transaction\Interfaces\UserAccountRepository::class => UserAccountRepository::class,

        \Fintech\Transaction\Interfaces\OrderQueueRepository::class => OrderQueueRepository::class,

        \Fintech\Transaction\Interfaces\ManualRefundRepository::class => ManualRefundRepository::class,

        \Fintech\Transaction\Interfaces\RewardPointRepository::class => RewardPointRepository::class,

        \Fintech\Transaction\Interfaces\RedeemPointRepository::class => RedeemPointRepository::class,

        //** Repository Binding Config Point Do not Remove **//
    ],

];
