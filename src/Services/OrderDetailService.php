<?php

namespace Fintech\Transaction\Services;

use Fintech\Auth\Facades\Auth;
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

    /**
     * @param $data
     * @return array
     */
    public function orderDetailsDataArrange($data): array
    {
        $orderData['order_id'] = $data->getKey();
        $orderData['source_country_id'] = $data->source_country_id;
        $orderData['destination_country_id'] = $data->destination_country_id;
        $orderData['order_detail_parent_id'] = $data->order_detail_parent_id ?? null;
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
        $orderData['order_detail_number'] = $data->order_detail_number ?? null;
        $orderData['order_detail_response_id'] = $data->order_detail_response_id ?? null;
        $orderData['step'] = $data->step ?? 1;
        $orderData['risk'] = $data->risk_profile ?? null;
        $orderData['notes'] = $data->notes ?? null;
        $orderData['is_refundable'] = $data->is_refundable ?? null;
        $orderData['order_detail_data'] = $data->order_data;
        $orderData['status'] = 'success';

        return $orderData;
    }
}
