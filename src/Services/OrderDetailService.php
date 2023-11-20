<?php

namespace Fintech\Transaction\Services;

use Fintech\Business\Facades\Business;
use Fintech\Transaction\Facades\Transaction;
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
        $serviceStateData['amount'] = $data->amount;
        $serviceStateData['enable'] = true;
        $serviceState = Business::serviceStat()->list($serviceStateData)->first()->toArray();

        $serviceStateData['service_stat_id'] = $serviceState['id'];
        $charge_break_down = Business::chargeBreakDown()->list($serviceStateData)->first();
        $serviceState = $serviceState['service_stat_data'][0];
        if ($charge_break_down) {
            $serviceStateJsonData['charge'] = $charge_break_down->charge_break_down_charge;
            $serviceStateJsonData['discount'] = $charge_break_down->charge_break_down_discount;
            $serviceStateJsonData['commission'] = $charge_break_down->charge_break_down_commission;
            $serviceStateJsonData['charge_break_down'] = $charge_break_down->getKey();
        } else {
            $serviceStateJsonData['charge'] = $serviceState['charge'];
            $serviceStateJsonData['discount'] = $serviceState['discount'];
            $serviceStateJsonData['commission'] = $serviceState['commission'];
            $serviceStateJsonData['service_stat_id'] = $serviceStateData['service_stat_id'];
        }

        //TODO
        $charge_amount = 0;
        $discount_amount = 0;
        $commission_amount = 0;

        $data->order_detail_cause_name = 'cash_deposit';
        $orderDetailStore = Transaction::orderDetail()->create(self::orderDetailsDataArrange($data));
        $data->amount = -$charge_amount;
        $data->converted_amount = -$charge_amount;
        $data->order_detail_cause_name = 'charge';
        //$data->notes = "charge";
        $data->step = 2;
        $data->order_detail_parent_id = $orderDetailStore->getKey();
        $orderDetailStoreForCharge = Transaction::orderDetail()->create(self::orderDetailsDataArrange($data));
        $data->amount = -$discount_amount;
        $data->converted_amount = -$discount_amount;
        $data->order_detail_cause_name = 'discount';
        //$data->notes = "charge";
        $data->step = 3;
        $data->order_detail_parent_id = $orderDetailStore->getKey();
        $updateData['order_data']['previous_amount'] = 0;
        $orderDetailStoreForDiscount = Transaction::orderDetail()->create(self::orderDetailsDataArrange($data));
        print_r($orderDetailStoreForDiscount);
        exit();
    }

    public function userRefundTransaction(array $data)
    {
        dd($data);
    }

    public function chargeStore($data)
    {
        $orderData['order_detail_amount'] = $data['commission_amount'];
        $orderData['converted_amount'] = $data['commission_amount'];
        $orderData['order_detail_cause_name'] = 'charge';
        $orderData['order_detail_parent_id'] = 0;
        $orderData['step'] = 0;
        dd($data);
    }

    public function discountStore($data)
    {
        $orderData['order_detail_amount'] = $data['commission_amount'];
        $orderData['converted_amount'] = $data['commission_amount'];
        $orderData['order_detail_cause_name'] = 'discount';
        $orderData['order_detail_parent_id'] = 0;
        $orderData['step'] = 0;
        dd($data);
    }

    public function commissionStore($data)
    {
        $orderData['order_detail_amount'] = $data['commission_amount'];
        $orderData['converted_amount'] = $data['commission_amount'];
        $orderData['order_detail_cause_name'] = 'commission';
        $orderData['order_detail_parent_id'] = 0;
        $orderData['step'] = 0;
        dd($data);
    }

    private function orderDetailsDataArrange($data): array
    {
        $orderData['order_id'] = $data->getKey();
        $orderData['source_country_id'] = $data->source_country_id;
        $orderData['destination_country_id'] = $data->destination_country_id;
        $orderData['sender_receiver_id'] = $data->sender_receiver_id;
        $orderData['user_id'] = $data->user_id;
        $orderData['service_id'] = $data->service_id;
        $orderData['transaction_form_id'] = $data->transaction_form_id;
        $orderData['order_detail_date'] = $data->ordered_at;
        $orderData['order_detail_cause_name'] = $data->order_detail_cause_name;
        $orderData['order_detail_amount'] = $data->amount;
        $orderData['order_detail_currency'] = $data->currency;
        $orderData['converted_amount'] = $data->converted_amount;
        $orderData['converted_currency'] = $data->converted_currency;
        $orderData['order_detail_number'] = 0;
        $orderData['order_detail_response_id'] = $data->order_number;
        $orderData['step'] = 1;
        $orderData['risk'] = $data->risk;
        $orderData['notes'] = 0;
        $orderData['is_refundable'] = $data->is_refundable;
        $orderData['order_detail_data'] = $data->order_data;
        $orderData['status'] = 'success';

        return $orderData;

    }
}
