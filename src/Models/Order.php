<?php

namespace Fintech\Transaction\Models;

use Fintech\Auth\Models\User;
use Fintech\Business\Models\Service;
use Fintech\Business\Models\ServiceVendor;
use Fintech\Core\Abstracts\BaseModel;
use Fintech\Core\Enums\Auth\RiskProfile;
use Fintech\Core\Enums\Transaction\OrderStatus;
use Fintech\Core\Enums\Transaction\OrderType;
use Fintech\Core\Traits\Audits\BlameableTrait;
use Fintech\Transaction\Traits\AuthRelations;
use Fintech\Transaction\Traits\HasOrderAttributes;
use Fintech\Transaction\Traits\MetaDataRelations;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property \Fintech\Core\Enums\Reload\DepositStatus $status
 * @property float $transaction_amount
 */
class Order extends BaseModel
{
    use AuthRelations;
    use BlameableTrait;
    use HasOrderAttributes;
    use MetaDataRelations;
    use SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $primaryKey = 'id';

    protected $table = 'orders';

    protected $guarded = ['id'];

    protected $casts = [
        'order_data' => 'array',
        'timeline' => 'array',
        'restored_at' => 'datetime',
        'ordered_at' => 'datetime',
        'status' => OrderStatus::class,
        'risk_profile' => RiskProfile::class,
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

    public function parentOrder(): BelongsTo
    {
        return $this->belongsTo(config('fintech.transaction.order_model', Order::class), 'parent_id');
    }

    public function transactionForm(): BelongsTo
    {
        return $this->belongsTo(config('fintech.transaction.transaction_form_model', TransactionForm::class));
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(config('fintech.auth.user_model', User::class));
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(config('fintech.business.service_model', Service::class));
    }

    public function serviceVendor(): BelongsTo
    {
        return $this->belongsTo(config('fintech.business.service_vendor_model', ServiceVendor::class));
    }

    public function orderDetails(): HasMany
    {
        return $this->hasMany(config('fintech.transaction.order_detail_model', OrderDetail::class));
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
            'show' => action_link(route('transaction.orders.show', $primaryKey), __('core::messages.action.show'), 'get'),
            'update' => action_link(route('transaction.orders.update', $primaryKey), __('core::messages.action.update'), 'put'),
            'destroy' => action_link(route('transaction.orders.destroy', $primaryKey), __('core::messages.action.destroy'), 'delete'),
            'restore' => action_link(route('transaction.orders.restore', $primaryKey), __('core::messages.action.restore'), 'post'),
        ];

        if ($this->getAttribute('deleted_at') == null) {
            unset($links['restore']);
        } else {
            unset($links['destroy']);
        }

        return $links;
    }

    public function getTransactionAmountAttribute()
    {
        return $this->order_data['transaction_amount'] ?? 0;
    }

    public function orderType(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->order_data['order_type'] ? OrderType::tryFrom($this->order_data['order_type']) : null,
        );
    }

    public function ref_number(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->order_data['beneficiary_data']['reference_no'] ?? $this->order_data['purchase_number'],
        );
    }
    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
