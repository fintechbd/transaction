<?php

namespace Fintech\Transaction\Support;

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

    private ?BaseModel $service;

    private OrderType $orderType;

    public function __construct(public $order,
        public $userId = null)
    {
        $this->init();
    }

    public function userId()
    {
        if ($this->userId == null) {
            $this->userId = $this->order->user_id;
        }

        return $this->userId;
    }

    private function init(): void
    {
        $this->stepIndex = 1;
        $this->orderData = $this->order->order_data;
        $this->serviceStatData = $this->orderData['service_stat_data'];
        $this->timeline = $this->orderData['service_stat_data'];
        $this->orderData['previous_amount'] = $this->previousBalance();
        $this->orderType = OrderType::from($this->orderData['order_type']);
        $this->service = $this->order->service ?? null;
    }

    private function isReload(): bool
    {
        return in_array($this->orderType->value, [OrderType::BankDeposit->value, OrderType::CardDeposit->value, OrderType::InteracDeposit->value]);
    }

    private function previousBalance()
    {
        return Transaction::orderDetail([
            'get_order_detail_amount_sum' => true,
            'user_id' => $this - $this->userId(),
            'order_detail_currency' => $this->order->currency,
        ]);

    }

    private function currentBalance()
    {
        return Transaction::orderDetail([
            'get_order_detail_amount_sum' => true,
            'user_id' => $this->userId(),
            'order_detail_currency' => $this->order->currency,
        ]);
    }

    private function transactionAmount()
    {
        return Transaction::orderDetail([
            'get_order_detail_amount_sum' => true,
            'user_id' => $this->userId(),
            'order_id' => $this->order->getKey(),
            'order_detail_currency' => $this->order->currency,
        ]);
    }

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

        $this->debitCharge();

        $this->debitDiscount();

        $this->orderData['current_amount'] = $this->currentBalance();

        return $this->order;
    }

    /**
     * Handle all the order detail transaction entries
     * Calculating user current and after operation balance
     * Updating account balance and updating order status data
     *
     * @param  BaseModel  $order
     */
    public function creditTransaction(array $inputs = []): BaseModel
    {
        $entry = $order->replicate();

        $creditableUserId = $inputs['credit_user_id'] ?? $order->user_id;

        $creditableUserAccount = Transaction::userAccount()->findWhere(['user_id' => $creditableUserId, 'country_id' => $order->destination_country_id]);

        $stepIndex = 1;

        $orderData = $order->order_data;

        $serviceStatData = $orderData['service_stat_data'];

        $timeline = $order->timeline ?? [];

        $updatedBalance['previous_amount'] = Transaction::orderDetail([
            'get_order_detail_amount_sum' => true,
            'user_id' => $order->user_id,
            'order_detail_currency' => $order->currency,
        ]);

        $orderData['previous_amount'] = $updatedBalance['previous_amount'] ?? 0;

        //Receive balance from system
        $balanceFormatted = \currency($entry->converted_amount, $entry->converted_currency);
        $master_user_name = $orderData['master_user_name'];
        $user_name = $orderData['user_name'];
        $entry->order_detail_cause_name = 'cash_deposit';
        $entry->order_detail_number = $orderData['accepted_number'];
        $entry->order_detail_response_id = $orderData['purchase_number'];
        $entry->notes = 'Balance purchases by '.$master_user_name;
        $orderDetailStoreArray = Transaction::orderDetail()->orderDetailsDataArrange($entry);
        $timeline[] = ['message' => "(System) Step {$stepIndex}: {$balanceFormatted} balance purchases from system using ({$master_user_name}) account.", 'flag' => 'info', 'timestamp' => now()];
        $orderDetailStoreArray['step'] = $stepIndex++;
        $orderDetailStoreArray['order_detail_parent_id'] = $entry->order_detail_parent_id ?? null;
        $orderDetailStore = Transaction::orderDetail()->create($orderDetailStoreArray);
        //        $orderDetailStore->order_detail_parent_id = $order->order_detail_parent_id = $orderDetailStore->getKey();
        //        $orderDetailStore->save();

        //Send Balance to given user
        $orderDetailStore->refresh();
        $amount = $order->amount;
        $converted_amount = $order->converted_amount;
        $orderDetailStoreForMaster = $orderDetailStore->replicate();
        $orderDetailStoreForMaster->user_id = $order->sender_receiver_id;
        $orderDetailStoreForMaster->sender_receiver_id = $order->user_id;
        $orderDetailStoreForMaster->order_detail_amount = -$amount;
        $orderDetailStoreForMaster->converted_amount = -$converted_amount;
        $orderDetailStoreForMaster->notes = 'Point Sold to '.$user_name;
        $timeline[] = ['message' => "(System) Step {$stepIndex}: {$balanceFormatted} balance sold to user ({$user_name}).", 'flag' => 'info', 'timestamp' => now()];
        $orderDetailStoreForMaster->step = $stepIndex++;
        $orderDetailStoreForMaster->save();

        //For Charge

        $order->amount = -calculate_flat_percent($amount, $serviceStatData['charge']);
        $order->converted_amount = -calculate_flat_percent($converted_amount, $serviceStatData['charge']);
        $order->order_detail_cause_name = 'charge';
        $order->order_detail_parent_id = $orderDetailStore->getKey();
        $order->notes = 'Deposit Charge Sending to '.$master_user_name;
        $timeline[] = ['message' => "(System) Step {$stepIndex}: Deposit Charge ".\currency($serviceStatData['charge_amount'], $order->converted_currency).' sent to system user ('.$master_user_name.').', 'flag' => 'info', 'timestamp' => now()];
        $order->step = $stepIndex++;
        $order->order_detail_parent_id = $orderDetailStore->getKey();
        $orderDetailStoreForCharge = Transaction::orderDetail()->create(Transaction::orderDetail()->orderDetailsDataArrange($order));

        $orderDetailStoreForChargeForMaster = $orderDetailStoreForCharge->replicate();
        $orderDetailStoreForChargeForMaster->user_id = $order->sender_receiver_id;
        $orderDetailStoreForChargeForMaster->sender_receiver_id = $order->user_id;
        $orderDetailStoreForChargeForMaster->order_detail_amount = calculate_flat_percent($amount, $serviceStatData['charge']);
        $orderDetailStoreForChargeForMaster->converted_amount = calculate_flat_percent($converted_amount, $serviceStatData['charge']);
        $orderDetailStoreForChargeForMaster->order_detail_cause_name = 'charge';
        $orderDetailStoreForChargeForMaster->notes = 'Deposit Charge Receiving from '.$user_name;
        $timeline[] = ['message' => "(System) Step {$stepIndex}: Deposit Charge ".\currency($serviceStatData['charge_amount'], $order->converted_currency).' received from depositor ('.$user_name.').', 'flag' => 'info', 'timestamp' => now()];
        $orderDetailStoreForChargeForMaster->step = $stepIndex++;
        $orderDetailStoreForChargeForMaster->save();

        //For Discount
        if (calculate_flat_percent($amount, $serviceStatData['discount']) > 0) {
            $order->amount = calculate_flat_percent($amount, $serviceStatData['discount']);
            $order->converted_amount = calculate_flat_percent($converted_amount, $serviceStatData['discount']);
            $order->order_detail_cause_name = 'discount';
            $order->notes = 'Deposit Discount form '.$master_user_name;
            $timeline[] = ['message' => '(System) Step 5: Deposit Discount '.\currency($serviceStatData['discount_amount'], $order->converted_currency).' received from system user ('.$master_user_name.').', 'flag' => 'info', 'timestamp' => now()];
            $order->step = $stepIndex++;
            //$data->order_detail_parent_id = $orderDetailStore->getKey();
            //$updateData['order_data']['previous_amount'] = 0;
            $orderDetailStoreForDiscount = Transaction::orderDetail()->create(Transaction::orderDetail()->orderDetailsDataArrange($order));
            $orderDetailStoreForDiscountForMaster = $orderDetailStoreForCharge->replicate();
            $orderDetailStoreForDiscountForMaster->user_id = $order->sender_receiver_id;
            $orderDetailStoreForDiscountForMaster->sender_receiver_id = $order->user_id;
            $orderDetailStoreForDiscountForMaster->order_detail_amount = -calculate_flat_percent($amount, $serviceStatData['discount']);
            $orderDetailStoreForDiscountForMaster->converted_amount = -calculate_flat_percent($converted_amount, $serviceStatData['discount']);
            $orderDetailStoreForDiscountForMaster->order_detail_cause_name = 'discount';
            $orderDetailStoreForDiscountForMaster->notes = 'Deposit Discount to '.$user_name;
            $timeline[] = ['message' => '(System) Step 6: Deposit Discount '.\currency($serviceStatData['discount_amount'], $order->converted_currency).' sent to depositor ('.$user_name.').', 'flag' => 'info', 'timestamp' => now()];

            $orderDetailStoreForDiscountForMaster->step = 6;
            $orderDetailStoreForDiscountForMaster->save();
        }

        $updatedBalance['current_amount'] = Transaction::orderDetail([
            'get_order_detail_amount_sum' => true,
            'user_id' => $order->user_id,
            'order_detail_currency' => $order->currency,
        ]);

        $orderData['current_amount'] = $updatedBalance['current_amount'];

        $updatedBalance['deposit_amount'] = Transaction::orderDetail([
            'get_order_detail_amount_sum' => true,
            'user_id' => $order->user_id,
            'order_id' => $order->getKey(),
            'order_detail_currency' => $order->currency,
        ]);

        array_push($depositArray['timeline'], ...$timeline);

        return $order;
    }

    private function creditBalance(): void
    {
        $this->order->refresh();
        $orderEntry = $this->order;
        $balanceFormatted = \currency($orderEntry->amount, $orderEntry->currency);
        $masterUserName = $this->orderData['master_user_name'] ?? null;
        $userName = $this->orderData['user_name'] ?? null;

        //Receive balance from system
        $this->timeline[] = ['message' => "(System) Step {$this->stepIndex}: {$balanceFormatted} debited for {$this->service->service_name} payment. Received by ({$masterUserName}) system account.", 'flag' => 'info', 'timestamp' => now()];

        $orderEntry->amount = $this->order->amount;
        $orderEntry->converted_amount = $this->order->converted_amount;
        $orderEntry->order_detail_cause_name = $this->orderType->value;
        $orderEntry->order_detail_number = $this->orderData['purchase_number'] ?? null;
        $orderEntry->order_detail_response_id = $this->orderData['purchase_number'] ?? null;
        $orderEntry->notes = ucfirst("{$this->service->service_name} payment sent to system user: {$masterUserName}");
        $orderEntry->step = $this->stepIndex++;
        $orderDetailStore = Transaction::orderDetail()->create(Transaction::orderDetail()->orderDetailsDataArrange($orderEntry));
        $orderDetailStore->order_detail_parent_id = $orderEntry->order_detail_parent_id ?? $orderDetailStore->getKey();
        $orderDetailStore->save();

        //Send balance to system
        $this->timeline[] = ['message' => "(System) Step {$this->stepIndex}: {$balanceFormatted} credited for {$this->service->service_name} payment. Sent By ({$userName}) user account.", 'flag' => 'info', 'timestamp' => now()];

        $orderDetailStore->refresh();
        $orderDetailStoreForMaster = $orderDetailStore->replicate();
        $orderDetailStoreForMaster->user_id = $orderEntry->sender_receiver_id;
        $orderDetailStoreForMaster->sender_receiver_id = $orderEntry->user_id;
        $orderDetailStoreForMaster->order_detail_amount = -$orderEntry->amount;
        $orderDetailStoreForMaster->converted_amount = -$orderEntry->converted_amount;
        $orderDetailStoreForMaster->notes = ucfirst("{$this->service->service_name} payment received from user: {$userName}");
        $orderDetailStoreForMaster->step = $this->stepIndex++;
        $orderDetailStoreForMaster->save();
    }

    private function debitBalance(): void
    {
        $this->order->refresh();
        $orderEntry = $this->order;
        $balanceFormatted = \currency($orderEntry->amount, $orderEntry->currency);
        $masterUserName = $this->orderData['master_user_name'] ?? null;
        $userName = $this->orderData['user_name'] ?? null;

        //Receive balance from user
        $this->timeline[] = ['message' => "(System) Step {$this->stepIndex}: {$balanceFormatted} debited for {$this->service->service_name} payment. Received by ({$masterUserName}) system account.", 'flag' => 'info', 'timestamp' => now()];

        $orderEntry->amount = -$this->order->amount;
        $orderEntry->converted_amount = -$this->order->converted_amount;
        $orderEntry->order_detail_cause_name = $this->orderType->value;
        $orderEntry->order_detail_number = $this->orderData['purchase_number'] ?? null;
        $orderEntry->order_detail_response_id = $this->orderData['purchase_number'] ?? null;
        $orderEntry->notes = ucfirst("{$this->service->service_name} payment sent to system user: {$masterUserName}");
        $orderEntry->step = $this->stepIndex++;
        $orderDetailStore = Transaction::orderDetail()->create(Transaction::orderDetail()->orderDetailsDataArrange($orderEntry));
        $orderDetailStore->order_detail_parent_id = $orderEntry->order_detail_parent_id ?? $orderDetailStore->getKey();
        $orderDetailStore->save();

        //Send balance to system
        $this->timeline[] = ['message' => "(System) Step {$this->stepIndex}: {$balanceFormatted} credited for {$this->service->service_name} payment. Sent By ({$userName}) user account.", 'flag' => 'info', 'timestamp' => now()];

        $orderDetailStore->refresh();
        $orderDetailStoreForMaster = $orderDetailStore->replicate();
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
        $this->order->refresh();

        $orderEntry = $this->order->replicate();

        $balanceFormatted = \currency($this->serviceStatData['charge_amount'] ?? 0, $orderEntry->currency);
        $masterUserName = $this->orderData['master_user_name'] ?? null;
        $userName = $this->orderData['user_name'] ?? null;

        //Receive balance from user
        $orderEntry->amount = -calculate_flat_percent($orderEntry->amount, $this->serviceStatData['charge']);
        $orderEntry->converted_amount = -calculate_flat_percent($converted_amount, $serviceStatData['charge']);
        $orderEntry->order_detail_cause_name = 'charge';
        $orderEntry->order_detail_parent_id = $orderDetailStore->getKey();
        $orderEntry->notes = 'Deposit Charge Sending to '.$master_user_name;
        $timeline[] = ['message' => "(System) Step {$stepIndex}: Deposit Charge ".\currency($serviceStatData['charge_amount'], $orderEntry->converted_currency).' sent to system user ('.$master_user_name.').', 'flag' => 'info', 'timestamp' => now()];
        $orderEntry->step = $stepIndex++;
        $orderEntry->order_detail_parent_id = $orderDetailStore->getKey();
        $orderDetailStoreForCharge = Transaction::orderDetail()->create(Transaction::orderDetail()->orderDetailsDataArrange($orderEntry));

        $orderDetailStoreForChargeForMaster = $orderDetailStoreForCharge->replicate();
        $orderDetailStoreForChargeForMaster->user_id = $orderEntry->sender_receiver_id;
        $orderDetailStoreForChargeForMaster->sender_receiver_id = $orderEntry->user_id;
        $orderDetailStoreForChargeForMaster->order_detail_amount = calculate_flat_percent($amount, $serviceStatData['charge']);
        $orderDetailStoreForChargeForMaster->converted_amount = calculate_flat_percent($converted_amount, $serviceStatData['charge']);
        $orderDetailStoreForChargeForMaster->order_detail_cause_name = 'charge';
        $orderDetailStoreForChargeForMaster->notes = 'Deposit Charge Receiving from '.$user_name;
        $timeline[] = ['message' => "(System) Step {$stepIndex}: Deposit Charge ".\currency($serviceStatData['charge_amount'], $orderEntry->converted_currency).' received from depositor ('.$user_name.').', 'flag' => 'info', 'timestamp' => now()];
        $orderDetailStoreForChargeForMaster->step = $stepIndex++;
        $orderDetailStoreForChargeForMaster->save();
    }

    private function debitDiscount(): void
    {
        //For Discount
        if (calculate_flat_percent($amount, $serviceStatData['discount']) > 0) {
            $order->amount = calculate_flat_percent($amount, $serviceStatData['discount']);
            $order->converted_amount = calculate_flat_percent($converted_amount, $serviceStatData['discount']);
            $order->order_detail_cause_name = 'discount';
            $order->notes = 'Deposit Discount form '.$master_user_name;
            $timeline[] = ['message' => '(System) Step 5: Deposit Discount '.\currency($serviceStatData['discount_amount'], $order->converted_currency).' received from system user ('.$master_user_name.').', 'flag' => 'info', 'timestamp' => now()];
            $order->step = $stepIndex++;
            //$data->order_detail_parent_id = $orderDetailStore->getKey();
            //$updateData['order_data']['previous_amount'] = 0;
            $orderDetailStoreForDiscount = Transaction::orderDetail()->create(Transaction::orderDetail()->orderDetailsDataArrange($order));
            $orderDetailStoreForDiscountForMaster = $orderDetailStoreForCharge->replicate();
            $orderDetailStoreForDiscountForMaster->user_id = $order->sender_receiver_id;
            $orderDetailStoreForDiscountForMaster->sender_receiver_id = $order->user_id;
            $orderDetailStoreForDiscountForMaster->order_detail_amount = -calculate_flat_percent($amount, $serviceStatData['discount']);
            $orderDetailStoreForDiscountForMaster->converted_amount = -calculate_flat_percent($converted_amount, $serviceStatData['discount']);
            $orderDetailStoreForDiscountForMaster->order_detail_cause_name = 'discount';
            $orderDetailStoreForDiscountForMaster->notes = 'Deposit Discount to '.$user_name;
            $timeline[] = ['message' => '(System) Step 6: Deposit Discount '.\currency($serviceStatData['discount_amount'], $order->converted_currency).' sent to depositor ('.$user_name.').', 'flag' => 'info', 'timestamp' => now()];

            $orderDetailStoreForDiscountForMaster->step = 6;
            $orderDetailStoreForDiscountForMaster->save();
        }
    }

    private function creditCommission(): void {}
}
