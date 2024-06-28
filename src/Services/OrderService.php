<?php

namespace Fintech\Transaction\Services;

use Carbon\Carbon;
use Fintech\Business\Facades\Business;
use Fintech\MetaData\Facades\MetaData;
use Fintech\Transaction\Facades\Transaction;
use Fintech\Transaction\Interfaces\OrderRepository;

/**
 * Class OrderService
 */
class OrderService
{
    /**
     * OrderService constructor.
     */
    public function __construct(private readonly OrderRepository $orderRepository) {}

    public function find($id, $onlyTrashed = false)
    {
        return $this->orderRepository->find($id, $onlyTrashed);
    }

    public function update($id, array $inputs = [])
    {
        return $this->orderRepository->update($id, $inputs);
    }

    public function destroy($id)
    {
        return $this->orderRepository->delete($id);
    }

    public function restore($id)
    {
        return $this->orderRepository->restore($id);
    }

    public function export(array $filters)
    {
        return $this->orderRepository->list($filters);
    }

    /**
     * @return mixed
     */
    public function list(array $filters = [])
    {
        return $this->orderRepository->list($filters);

    }

    public function import(array $filters)
    {
        return $this->orderRepository->create($filters);
    }

    public function create(array $inputs = [])
    {
        return $this->orderRepository->create($inputs);
    }

    public function transactionDelayCheck($data): array
    {
        $delayCheck = config('fintech.transaction.delay_time');
        if ($delayCheck > 0) {
            $input['user_id'] = $data['user_id'];
            $input['service_id'] = $data['service_id'];
            $input['amount'] = $data['amount'];
            $input['currency'] = $data['currency'];
            $input['source_country_id'] = $data['source_country_id'];
            $input['destination_country_id'] = $data['destination_country_id'];
            $created_at = Carbon::now()->subMinute($delayCheck)->format('Y-m-d H:i:s');
            $input['created_at_start_date_time'] = $created_at;
            $input['created_at_end_date_time'] = Carbon::now()->format('Y-m-d H:i:s');
            $input['service_delay'] = 'yes';

            $service_type_parent = Business::service()->list(['service_id' => $input['service_id'], 'service_delay' => $input['service_delay']])->first();
            if ($service_type_parent) {
                if (isset($data['order_data']['account_number']) && $data['order_data']['account_number'] != null) {
                    if ((isset($service_type_parent->serviceType->service_type_slug) ? $service_type_parent->serviceType->service_type_slug : null) == 'wallet_transfer') {
                        $country = MetaData::country()->list(['country_id' => $input['destination_country_id']])->first();
                        $input['account_number'] = $country->phone_code + $data['order_data']['account_number'];
                    } else {
                        $input['account_number'] = $data['order_data']['account_number'];
                        $input['bank_id'] = isset($data['order_data']['bank_id']) ? $data['order_data']['bank_id'] : null;
                        $input['bank_branch_id'] = isset($data['order_data']['bank_branch_id']) ? $data['order_data']['bank_branch_id'] : null;
                    }
                } elseif ((isset($service_type_parent->serviceType->service_type_slug) ? $service_type_parent->serviceType->service_type_slug : null) == 'fund_deposit') {
                    $input['status'] = ['processing'];
                    unset($input['created_at_start_date_time'], $input['created_at_end_date_time']);
                } elseif ((isset($service_type_parent->serviceType->service_type_slug) ? $service_type_parent->serviceType->service_type_slug : null) == 'currency_swap') {
                    $input['status'] = ['processing'];
                    unset($input['created_at_start_date_time'], $input['created_at_end_date_time']);
                }
                $input['sort'] = 'orders.id';
                $input['dir'] = 'asc';

                $orderCheck = Transaction::order()->list($input);
                if ($orderCheck->first()) {
                    $returnValue['countValue'] = $orderCheck->count();
                    $remainingTime = strtotime($created_at) - strtotime($orderCheck->first()->order_at);
                    $returnValue['remainingTime'] = $delayCheck - (int) ($remainingTime / 60);
                    $returnValue['delayTime'] = $delayCheck;
                } else {
                    $returnValue['countValue'] = 0;
                    $returnValue['remainingTime'] = 0;
                    $returnValue['delayTime'] = 0;
                }
            } else {
                $returnValue['countValue'] = 0;
                $returnValue['remainingTime'] = 0;
                $returnValue['delayTime'] = 0;
            }
        } else {
            $returnValue['countValue'] = 0;
            $returnValue['remainingTime'] = 0;
            $returnValue['delayTime'] = 0;
        }

        return $returnValue;
    }
}
