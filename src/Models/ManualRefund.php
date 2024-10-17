<?php

namespace Fintech\Transaction\Models;

use Fintech\Core\Traits\BlameableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class ManualRefund extends Order implements Auditable
{
    use BlameableTrait;
    use \OwenIt\Auditing\Auditable;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

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

        return [
            'show' => action_link(route('transaction.manual-refunds.show', $primaryKey), __('restapi::messages.action.show'), 'get'),
            'update' => action_link(route('transaction.manual-refunds.update', $primaryKey), __('restapi::messages.action.update'), 'put'),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
