<?php

namespace Fintech\Transaction\Services;

use Carbon\Carbon;
use Fintech\Business\Facades\Business;
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
    public function __construct(private readonly OrderRepository $orderRepository)
    {
    }

    /**
     * @return mixed
     */
    public function list(array $filters = [])
    {
        return $this->orderRepository->list($filters);

    }

    public function create(array $inputs = [])
    {
        return $this->orderRepository->create($inputs);
    }

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

    public function import(array $filters)
    {
        return $this->orderRepository->create($filters);
    }

    public function transactionDelayCheck($data)
    {
        $delayCheck = config('fintech.transaction.delay_time');
        if($delayCheck > 0){
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
            $service_type_parent_id = Business::service()->find($input['service_id']);
            if((isset($service_type_parent_id->serviceType->service_type_slug)?$service_type_parent_id->serviceType->service_type_slug:null) == 'fund_deposit'){
                $input['status'] = array('processing');
                unset($input['created_at_start_date_time'], $input['created_at_end_date_time']);
            }
            $orderCheck = Transaction::order()->list($input);
                //$this->ShowAllPreTransaction($data)->orderBy('pre_transactions.transaction_date', 'DESC');
        }
        print_r($orderCheck);exit();

    }
}
