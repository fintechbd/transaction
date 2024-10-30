<?php

namespace Fintech\Transaction\Models;

use Fintech\Core\Abstracts\BaseModel;
use Fintech\Core\Enums\Auth\RiskProfile;
use Fintech\Core\Enums\Transaction\OrderStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Compliance extends BaseModel
{
    use SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    protected $casts = [
        'compliance_data' => 'array',
        'restored_at' => 'datetime',
        'enabled' => 'bool',
        'timestamp' => 'datetime',
        'risk' => RiskProfile::class,
        'priority' => RiskProfile::class,
        'order_status' => OrderStatus::class,
    ];

    protected $hidden = ['creator_id', 'editor_id', 'destroyer_id', 'restorer_id'];

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
        return $this->belongsTo(config('fintech.auth.user_model', Order::class));
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

        $links = [
            'show' => action_link(route('transaction.compliances.show', $primaryKey), __('core::messages.action.show'), 'get'),
            'update' => action_link(route('transaction.compliances.update', $primaryKey), __('core::messages.action.update'), 'put'),
            'destroy' => action_link(route('transaction.compliances.destroy', $primaryKey), __('core::messages.action.destroy'), 'delete'),
            'restore' => action_link(route('transaction.compliances.restore', $primaryKey), __('core::messages.action.restore'), 'post'),
        ];

        if ($this->getAttribute('deleted_at') == null) {
            unset($links['restore']);
        } else {
            unset($links['destroy']);
        }

        return $links;
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
