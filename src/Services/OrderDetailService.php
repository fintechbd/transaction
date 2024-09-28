<?php

namespace Fintech\Transaction\Services;

use Fintech\Core\Abstracts\BaseModel;
use Fintech\Transaction\Interfaces\OrderDetailRepository;

/**
 * Class OrderDetailService
 */
class OrderDetailService
{
    use \Fintech\Core\Traits\HasFindWhereSearch;

    /**
     * OrderDetailService constructor.
     */
    public function __construct(private readonly OrderDetailRepository $orderDetailRepository) {}

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

    /**
     * @return mixed
     */
    public function list(array $filters = [])
    {
        return $this->orderDetailRepository->list($filters);

    }

    public function import(array $filters)
    {
        return $this->orderDetailRepository->create($filters);
    }

    public function create(array $inputs = [])
    {
        return $this->orderDetailRepository->create($inputs);
    }

    public function orderDetailsDataArrange(BaseModel $order): array
    {
        $orderData['order_id'] = $order->getKey();
        $orderData['source_country_id'] = $order->source_country_id;
        $orderData['destination_country_id'] = $order->destination_country_id;
        $orderData['order_detail_parent_id'] = $order->order_detail_parent_id ?? null;
        $orderData['sender_receiver_id'] = $order->sender_receiver_id;
        $orderData['user_id'] = $order->user_id;
        $orderData['service_id'] = $order->service_id;
        $orderData['transaction_form_id'] = $order->transaction_form_id;
        $orderData['order_detail_date'] = $order->ordered_at;
        $orderData['order_detail_cause_name'] = $order->order_detail_cause_name;
        $orderData['order_detail_amount'] = $order->amount;
        $orderData['order_detail_currency'] = $order->currency;
        $orderData['converted_amount'] = $order->converted_amount;
        $orderData['converted_currency'] = $order->converted_currency;
        $orderData['order_detail_number'] = $order->order_detail_number ?? null;
        $orderData['order_detail_response_id'] = $order->order_detail_response_id ?? null;
        $orderData['step'] = $order->step ?? 1;
        $orderData['risk'] = $order->risk_profile ?? null;
        $orderData['notes'] = $order->notes ?? null;
        $orderData['is_refundable'] = $order->is_refundable ?? null;
        $orderData['order_detail_data'] = $order->order_data;
        $orderData['status'] = 'success';

        return $orderData;
    }
}
