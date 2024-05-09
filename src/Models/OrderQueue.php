<?php

namespace Fintech\Transaction\Models;

use Fintech\Auth\Models\User;
use Fintech\Core\Abstracts\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderQueue extends BaseModel
{
    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    protected $appends = ['links'];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function order(): BelongsTo
    {
        return $this->belongsTo(config('fintech.transaction.order_model', Order::class));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('fintech.auth.user_model', User::class));
    }
    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /**
     * @return array
     */
    public function getLinksAttribute()
    {
        $primaryKey = $this->getKey();

        return [
            'show' => action_link(route('transaction.order-queues.show', $primaryKey), __('restapi::messages.action.show'), 'get'),
            'update' => action_link(route('transaction.order-queues.update', $primaryKey), __('restapi::messages.action.update'), 'put'),
            'destroy' => action_link(route('transaction.order-queues.destroy', $primaryKey), __('restapi::messages.action.destroy'), 'delete'),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
