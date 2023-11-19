<?php

namespace Fintech\Transaction\Services;

use Fintech\Business\Facades\Business;
use Fintech\Transaction\Interfaces\OrderDetailRepository;

/**
 * Class OrderDetailService
 */
class OrderDetailService
{
    /**
     * OrderDetailService constructor.
     */
    public function __construct(OrderDetailRepository $orderDetailRepository)
    {
        $this->orderDetailRepository = $orderDetailRepository;
    }

    /**
     * @return mixed
     */
    public function list(array $filters = [])
    {
        return $this->orderDetailRepository->list($filters);

    }

    public function create(array $inputs = [])
    {
        return $this->orderDetailRepository->create($inputs);
    }

    public function find($id, $onlyTrashed = false)
    {
        return $this->orderDetailRepository->find($id, $onlyTrashed);
    }

    public function update($id, array $inputs = [])
    {
        return $this->orderDetailRepository->update($id, $inputs);
    }

    public function destroy($id)
    {
        return $this->orderDetailRepository->delete($id);
    }

    public function restore($id)
    {
        return $this->orderDetailRepository->restore($id);
    }

    public function export(array $filters)
    {
        return $this->orderDetailRepository->list($filters);
    }

    public function import(array $filters)
    {
        return $this->orderDetailRepository->create($filters);
    }

    public function masterUserTransaction(array $data)
    {
        dd($data);
    }
    public function masterUserRefundTransaction(array $data)
    {
        dd($data);
    }
    public function userTransaction($data)
    {
        $data->role_id = $data->user->roles[0]->getKey();
        $serviceStateData['role_id'] = $data->role_id;
        $serviceStateData['service_id'] = $data->service_id;
        $serviceStateData['source_country_id'] = $data->source_country_id;
        $serviceStateData['destination_country_id'] = $data->destination_country_id;
       // print_r($serviceStateData);exit();
        $serviceState = Business::serviceStat()->list($serviceStateData)->first()->toArray();
        //$serviceState['id'] = $serviceState['id'];
        $charge_break_down = Business::chargeBreakDown(['service_state_id' => $serviceState['id']])->list();
        /*$serviceState = json_decode($serviceState['service_state_data'])[0];
        $serviceStateJsonData['charge'] = $serviceState->charge;
        $serviceStateJsonData['discount'] = $serviceState->charge;
        $serviceStateJsonData['commission'] = $serviceState->charge;*/
        //$charge_break_down = Business::chargeBreakDown()->list();
        print_r($charge_break_down);exit();
    }

    public function userRefundTransaction(array $data)
    {
        dd($data);
    }
}
