<?php

namespace Fintech\Transaction\Support;

use Fintech\Business\Facades\Business;
use Fintech\Core\Abstracts\BaseModel;
use Fintech\Core\Enums\Transaction\OrderType;
use Fintech\Transaction\Facades\Transaction;

/**
 * Class ChartClassService
 *
 * @property BaseModel|\Illuminate\Database\Eloquent\Model $order
 * @property string|int|null $userId
 */
class Accounting
{
    public array $timeline;
    public array $serviceStatData;
    public array $orderData;
    private int $stepIndex;
    private ?int $orderDetailParentId = null;
    private ?BaseModel $service;
    private OrderType $orderType;

    public function __construct(public $order,
                                public $userId = null)
    {
        $this->__init();
    }

    private function __init(): void
    {
        $this->stepIndex = 1;
        $this->orderData = $this->order->order_data;
        $this->serviceStatData = $this->orderData['service_stat_data'];
        $this->timeline = $this->orderData['service_stat_data'];
        $this->orderData['previous_amount'] = $this->previousBalance();
        $this->orderType = OrderType::from($this->orderData['order_type']);
        $this->service = $this->order->service ?? null;
    }

    /***************************************************************/

    /**
     * Handle all the order detail transaction entries
     * Calculating user current and after operation balance
     * Updating account balance and updating order status data
     */
    public function debitTransaction(array $parameters = []): BaseModel
    {
        ($this->isReload())
            ? $this->creditBalance()
            : $this->debitBalance();

        $this->order->refresh();
        $this->debitCharge();

        $this->order->refresh();
        $this->debitDiscount();

        $transactionAmount = $this->transactionAmount();

        $this->orderData['current_amount'] = $this->currentBalance();

        return $this->order;
    }

    /**
     * Handle all the order detail transaction entries
     * Calculating user current and after operation balance
     * Updating account balance and updating order status data
     *
     * @param BaseModel $order
     * @return BaseModel
     */
    public function creditTransaction(array $parameters = []): BaseModel
    {
            $this->creditBalance();

        $this->order->refresh();
        $this->creditCommission();

        $transactionAmount = $this->transactionAmount(true);

        $this->orderData['current_amount'] = $this->currentBalance();

        return $this->order;

    }

    /***************************************************************/

    private function creditBalance(): void
    {
        $balanceOrderDetail = $this->order;
        $balanceFormatted = \currency($balanceOrderDetail->amount, $balanceOrderDetail->currency);
        $masterUserName = $this->orderData['master_user_name'] ?? null;
        $userName = $this->orderData['user_name'] ?? null;

        //Receive balance from system
        $this->timeline[] = ['message' => "(System) Step {$this->stepIndex}: {$balanceFormatted} debited for {$this->service->service_name} payment. Received by ({$masterUserName}) system account.", 'flag' => 'info', 'timestamp' => now()];

        $balanceOrderDetail->amount = $this->order->amount;
        $balanceOrderDetail->converted_amount = $this->order->converted_amount;
        $balanceOrderDetail->order_detail_cause_name = $this->orderType->value;
        $balanceOrderDetail->order_detail_number = $this->orderDetailNumber();
        $balanceOrderDetail->order_detail_response_id = $this->orderData['purchase_number'] ?? null;
        $balanceOrderDetail->notes = ucfirst("{$this->service->service_name} payment sent to system user: {$masterUserName}");
        $balanceOrderDetail->step = $this->stepIndex++;
        $balanceOrderDetailStore = Transaction::orderDetail()->create(Transaction::orderDetail()->orderDetailsDataArrange($balanceOrderDetail));
        $balanceOrderDetailStore->order_detail_parent_id = $balanceOrderDetail->order_detail_parent_id ?? $balanceOrderDetailStore->getKey();
        $this->orderDetailParentId($balanceOrderDetailStore->order_detail_parent_id);
        $balanceOrderDetailStore->save();

        //Send balance to system
        $this->timeline[] = ['message' => "(System) Step {$this->stepIndex}: {$balanceFormatted} credited for {$this->service->service_name} payment. Sent By ({$userName}) user account.", 'flag' => 'info', 'timestamp' => now()];

        $balanceOrderDetailStore->refresh();
        $balanceOrderDetailForMaster = $balanceOrderDetailStore->replicate();
        $balanceOrderDetailForMaster->order_detail_parent_id = $this->orderDetailParentId();
        $balanceOrderDetailForMaster->user_id = $balanceOrderDetail->sender_receiver_id;
        $balanceOrderDetailForMaster->sender_receiver_id = $balanceOrderDetail->user_id;
        $balanceOrderDetailForMaster->order_detail_amount = -$balanceOrderDetail->amount;
        $balanceOrderDetailForMaster->converted_amount = -$balanceOrderDetail->converted_amount;
        $balanceOrderDetailForMaster->notes = ucfirst("{$this->service->service_name} payment received from user: {$userName}");
        $balanceOrderDetailForMaster->step = $this->stepIndex++;
        $balanceOrderDetailForMaster->save();
    }
    private function debitBalance(): void
    {
        $orderEntry = $this->order;
        $balanceFormatted = \currency($orderEntry->amount, $orderEntry->currency);
        $masterUserName = $this->orderData['master_user_name'] ?? null;
        $userName = $this->orderData['user_name'] ?? null;

        //Receive balance from user
        $this->timeline[] = ['message' => "(System) Step {$this->stepIndex}: {$balanceFormatted} debited for {$this->service->service_name} payment. Received by ({$masterUserName}) system account.", 'flag' => 'info', 'timestamp' => now()];

        $orderEntry->amount = -$this->order->amount;
        $orderEntry->converted_amount = -$this->order->converted_amount;
        $orderEntry->order_detail_cause_name = $this->orderType->value;
        $orderEntry->order_detail_number = $this->orderDetailNumber();
        $orderEntry->order_detail_response_id = $this->orderData['purchase_number'] ?? null;
        $orderEntry->notes = ucfirst("{$this->service->service_name} payment sent to system user: {$masterUserName}");
        $orderEntry->step = $this->stepIndex++;
        $orderDetailStore = Transaction::orderDetail()->create(Transaction::orderDetail()->orderDetailsDataArrange($orderEntry));
        $orderDetailStore->order_detail_parent_id = $orderEntry->order_detail_parent_id ?? $orderDetailStore->getKey();
        $this->orderDetailParentId($orderDetailStore->order_detail_parent_id);
        $orderDetailStore->save();

        //Send balance to system
        $this->timeline[] = ['message' => "(System) Step {$this->stepIndex}: {$balanceFormatted} credited for {$this->service->service_name} payment. Sent By ({$userName}) user account.", 'flag' => 'info', 'timestamp' => now()];

        $orderDetailStore->refresh();
        $orderDetailStoreForMaster = $orderDetailStore->replicate();
        $orderDetailStoreForMaster->order_detail_parent_id = $this->orderDetailParentId();
        $orderDetailStoreForMaster->user_id = $orderEntry->sender_receiver_id;
        $orderDetailStoreForMaster->sender_receiver_id = $orderEntry->user_id;
        $orderDetailStoreForMaster->order_detail_amount = $orderEntry->amount;
        $orderDetailStoreForMaster->converted_amount = $orderEntry->converted_amount;
        $orderDetailStoreForMaster->notes = ucfirst("{$this->service->service_name} payment received from user: {$userName}");
        $orderDetailStoreForMaster->step = $this->stepIndex++;
        $orderDetailStoreForMaster->save();
    }
    private function debitCharge(): void
    {
        $chargeOrderDetail = $this->order;
        $masterUserName = $this->orderData['master_user_name'] ?? null;
        $userName = $this->orderData['user_name'] ?? null;

        $chargeAmount = calculate_flat_percent($chargeOrderDetail->amount, $this->serviceStatData['charge']);
        $convertedChargeAmount = calculate_flat_percent($chargeOrderDetail->converted_amount, $this->serviceStatData['charge']);
        $balanceFormatted = \currency($chargeAmount, $chargeOrderDetail->currency);

        if ($chargeAmount > 0) {

            //Receive charge for system
            $this->timeline[] = ['message' => "(System) Step {$this->stepIndex}: {$balanceFormatted} charge debited for {$this->service->service_name}. Received by ({$masterUserName}) system account.", 'flag' => 'info', 'timestamp' => now()];

            $chargeOrderDetail->amount = -$chargeAmount;
            $chargeOrderDetail->converted_amount = -$convertedChargeAmount;
            $chargeOrderDetail->order_detail_cause_name = 'charge';
            $chargeOrderDetail->order_detail_parent_id = $this->orderDetailParentId();
            $chargeOrderDetail->order_detail_number = $this->orderDetailNumber();
            $chargeOrderDetail->order_detail_response_id = $this->orderData['purchase_number'] ?? null;
            $chargeOrderDetail->notes = ucfirst("{$this->service->service_name} charge sent to system user: {$masterUserName}");
            $chargeOrderDetail->step = $this->stepIndex++;
            $chargeOrderDetailStore = Transaction::orderDetail()->create(Transaction::orderDetail()->orderDetailsDataArrange($chargeOrderDetail));
            $chargeOrderDetailStore->save();

            //Send balance to system
            $this->timeline[] = ['message' => "(System) Step {$this->stepIndex}: {$balanceFormatted} charge credited for {$this->service->service_name}. Sent By ({$userName}) user account.", 'flag' => 'info', 'timestamp' => now()];

            $chargeOrderDetailStore->refresh();
            $chargeOrderDetailForMaster = $chargeOrderDetailStore->replicate();
            $chargeOrderDetailForMaster->user_id = $chargeOrderDetail->sender_receiver_id;
            $chargeOrderDetailForMaster->sender_receiver_id = $chargeOrderDetail->user_id;
            $chargeOrderDetailForMaster->order_detail_amount = $chargeAmount;
            $chargeOrderDetailForMaster->converted_amount = $convertedChargeAmount;
            $chargeOrderDetailForMaster->notes = ucfirst("{$this->service->service_name} payment received from user: {$userName}");
            $chargeOrderDetailForMaster->step = $this->stepIndex++;
            $chargeOrderDetailForMaster->save();
        }
    }
    private function debitDiscount(): void
    {
        $discountOrderDetail = $this->order;
        $masterUserName = $this->orderData['master_user_name'] ?? null;
        $userName = $this->orderData['user_name'] ?? null;

        $discountAmount = calculate_flat_percent($discountOrderDetail->amount, $this->serviceStatData['discount']);
        $convertedDiscountAmount = calculate_flat_percent($discountOrderDetail->converted_amount, $this->serviceStatData['discount']);
        $balanceFormatted = \currency($discountAmount, $discountOrderDetail->currency);

        if ($discountAmount > 0) {

            //Receive charge for system
            $this->timeline[] = ['message' => "(System) Step {$this->stepIndex}: {$balanceFormatted} discount credited for {$this->service->service_name}. Received by ({$masterUserName}) system account.", 'flag' => 'info', 'timestamp' => now()];

            $discountOrderDetail->amount = $discountAmount;
            $discountOrderDetail->converted_amount = $convertedDiscountAmount;
            $discountOrderDetail->order_detail_cause_name = 'discount';
            $discountOrderDetail->order_detail_parent_id = $this->orderDetailParentId();
            $discountOrderDetail->order_detail_number = $this->orderDetailNumber();
            $discountOrderDetail->order_detail_response_id = $this->orderData['purchase_number'] ?? null;
            $discountOrderDetail->notes = ucfirst("{$this->service->service_name} charge sent to system user: {$masterUserName}");
            $discountOrderDetail->step = $this->stepIndex++;
            $discountOrderDetailStore = Transaction::orderDetail()->create(Transaction::orderDetail()->orderDetailsDataArrange($discountOrderDetail));
            $discountOrderDetailStore->save();

            //Send balance to system
            $this->timeline[] = ['message' => "(System) Step {$this->stepIndex}: {$balanceFormatted} discount debited for {$this->service->service_name}. Sent By ({$userName}) user account.", 'flag' => 'info', 'timestamp' => now()];

            $discountOrderDetailStore->refresh();
            $discountOrderDetailForMaster = $discountOrderDetailStore->replicate();
            $discountOrderDetailForMaster->user_id = $discountOrderDetail->sender_receiver_id;
            $discountOrderDetailForMaster->sender_receiver_id = $discountOrderDetail->user_id;
            $discountOrderDetailForMaster->order_detail_amount = -$discountAmount;
            $discountOrderDetailForMaster->converted_amount = -$convertedDiscountAmount;
            $discountOrderDetailForMaster->notes = ucfirst("{$this->service->service_name} payment received from user: {$userName}");
            $discountOrderDetailForMaster->step = $this->stepIndex++;
            $discountOrderDetailForMaster->save();
        }
    }
    private function creditCommission(): void
    {
        $commissionOrderDetail = $this->order;
        $masterUserName = $this->orderData['master_user_name'] ?? null;
        $userName = $this->orderData['user_name'] ?? null;

        $commissionAmount = calculate_flat_percent($commissionOrderDetail->amount, $this->serviceStatData['discount']);
        $convertedCommissionAmount = calculate_flat_percent($commissionOrderDetail->converted_amount, $this->serviceStatData['discount']);
        $balanceFormatted = \currency($commissionAmount, $commissionOrderDetail->currency);

        if ($commissionAmount > 0) {

            //Receive charge for system
            $this->timeline[] = ['message' => "(System) Step {$this->stepIndex}: {$balanceFormatted} commission credited for {$this->service->service_name}. Received by ({$masterUserName}) system account.", 'flag' => 'info', 'timestamp' => now()];

            $commissionOrderDetail->amount = $commissionAmount;
            $commissionOrderDetail->converted_amount = $convertedCommissionAmount;
            $commissionOrderDetail->order_detail_cause_name = 'discount';
            $commissionOrderDetail->order_detail_parent_id = $this->orderDetailParentId();
            $commissionOrderDetail->order_detail_number = $this->orderDetailNumber();
            $commissionOrderDetail->order_detail_response_id = $this->orderData['purchase_number'] ?? null;
            $commissionOrderDetail->notes = ucfirst("{$this->service->service_name} charge sent to system user: {$masterUserName}");
            $commissionOrderDetail->step = $this->stepIndex++;
            $discountOrderDetailStore = Transaction::orderDetail()->create(Transaction::orderDetail()->orderDetailsDataArrange($commissionOrderDetail));
            $discountOrderDetailStore->save();

            //Send balance to system
            $this->timeline[] = ['message' => "(System) Step {$this->stepIndex}: {$balanceFormatted} commission debited for {$this->service->service_name}. Sent By ({$userName}) user account.", 'flag' => 'info', 'timestamp' => now()];

            $discountOrderDetailStore->refresh();
            $discountOrderDetailForMaster = $discountOrderDetailStore->replicate();
            $discountOrderDetailForMaster->user_id = $commissionOrderDetail->sender_receiver_id;
            $discountOrderDetailForMaster->sender_receiver_id = $commissionOrderDetail->user_id;
            $discountOrderDetailForMaster->order_detail_amount = -$commissionAmount;
            $discountOrderDetailForMaster->converted_amount = -$convertedCommissionAmount;
            $discountOrderDetailForMaster->notes = ucfirst("{$this->service->service_name} payment received from user: {$userName}");
            $discountOrderDetailForMaster->step = $this->stepIndex++;
            $discountOrderDetailForMaster->save();
        }
    }

    /***************************************************************/
    public function userId()
    {
        if ($this->userId == null) {
            $this->userId = $this->order->user_id;
        }

        return $this->userId;
    }
    private function isReload(): bool
    {
        return in_array($this->orderType->value, [OrderType::BankDeposit->value, OrderType::CardDeposit->value, OrderType::InteracDeposit->value]);
    }
    private function previousBalance(): float
    {
        return (float)Transaction::orderDetail([
            'get_order_detail_amount_sum' => true,
            'user_id' => $this->userId(),
            'order_detail_currency' => $this->order->currency,
        ]);
    }
    private function currentBalance(): float
    {
        return (float)Transaction::orderDetail([
            'get_order_detail_amount_sum' => true,
            'user_id' => $this->userId(),
            'order_detail_currency' => $this->order->currency,
        ]);
    }
    private function transactionAmount(bool $convertedAmount = false): float
    {
        $parameters = [
            'get_order_detail_amount_sum' => true,
            'user_id' => $this->userId(),
            'order_id' => $this->order->getKey(),
            'order_detail_currency' => $this->order->currency,
        ];

        if ($convertedAmount) {
            unset($parameters['get_order_detail_amount_sum'], $parameters['order_detail_currency']);
            $parameters['get_converted_amount_sum'] = true;
            $parameters['converted_currency'] = $this->order->converted_currency;
        }

        return (float)Transaction::orderDetail($parameters);
    }
    private function orderDetailParentId($orderDetailParentId = null): ?int
    {
        if ($orderDetailParentId !== null) {
            $this->orderDetailParentId = $orderDetailParentId;
        }

        return $this->orderDetailParentId;
    }
    private function orderDetailNumber(): ?string
    {
        if ($this->isReload()) {
            return $this->orderData['accepted_number'] ?? null;
        }

        return $this->orderData['accept_number'] ?? null;
    }
}