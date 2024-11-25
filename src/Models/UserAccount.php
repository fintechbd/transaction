<?php

namespace Fintech\Transaction\Models;

use Fintech\Core\Abstracts\BaseModel;
use Fintech\Core\Traits\Audits\BlameableTrait;
use Fintech\Transaction\Traits\AuthRelations;
use Fintech\Transaction\Traits\MetaDataRelations;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class UserAccount extends BaseModel implements Auditable
{
    use AuthRelations;
    use BlameableTrait;
    use MetaDataRelations;
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $primaryKey = 'id';

    protected $guarded = ['id'];

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
            'show' => action_link(route('transaction.user-accounts.show', $primaryKey), __('restapi::messages.action.show'), 'get'),
            'destroy' => action_link(route('transaction.user-accounts.destroy', $primaryKey), __('restapi::messages.action.destroy'), 'delete'),
            'restore' => action_link(route('transaction.user-accounts.restore', $primaryKey), __('restapi::messages.action.restore'), 'post'),
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
