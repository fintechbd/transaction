<?php

namespace Fintech\Transaction\Http\Controllers;

use Fintech\Core\Enums\Transaction\OrderStatus;
use Fintech\Core\Http\Requests\DropDownRequest;
use Fintech\Core\Http\Resources\DropDownCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class OrderStatusDropdownController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(DropDownRequest $request): DropDownCollection|JsonResponse
    {
        try {
            $entries = collect();

            foreach (OrderStatus::cases() as $status) {
                if ($status == OrderStatus::PaymentPending) {
                    $entries->push(['label' => $status->label('Transaction Pending'), 'attribute' => $status->value]);

                    continue;
                }
                $entries->push(['label' => $status->label(), 'attribute' => $status->value]);
            }

            return new DropDownCollection($entries);

        } catch (\Exception $exception) {
            return response()->failed($exception);
        }
    }
}
