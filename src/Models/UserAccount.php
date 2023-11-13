<?php

namespace Fintech\Transaction\Models;

use Fintech\Core\Traits\AuditableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAccount extends Model
{
    use AuditableTrait;
    use SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    protected $appends = ['links'];

    protected $casts = ['user_account_data' => 'array', 'restored_at' => 'datetime', 'enabled' => 'bool'];

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
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('fintech.auth.user_model', \Fintech\Auth\Models\User::class));
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(config('fintech.metadata.country_model', \Fintech\MetaData\Models\Country::class));
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
            'show' => action_link(route('transaction.user-account.show', $primaryKey), __('core::messages.action.show'), 'get'),
            'update' => action_link(route('transaction.user-account.update', $primaryKey), __('core::messages.action.update'), 'put'),
            'destroy' => action_link(route('transaction.user-account.destroy', $primaryKey), __('core::messages.action.destroy'), 'delete'),
            'restore' => action_link(route('transaction.user-account.restore', $primaryKey), __('core::messages.action.restore'), 'post'),
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
