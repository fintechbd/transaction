<?php

namespace Fintech\Transaction\Support;

use ErrorException;
use Exception;
use Fintech\Auth\Facades\Auth;
use Fintech\Auth\Models\User;
use Fintech\Business\Models\Service;
use Fintech\Core\Abstracts\BaseModel;
use Fintech\Core\Enums\Auth\SystemRole;
use Fintech\Core\Enums\Transaction\OrderType;
use Fintech\Core\Exceptions\Transaction\MasterCurrencyUnavailableException;
use Fintech\Core\Exceptions\UpdateOperationException;
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

    /**
     * @var BaseModel|Service
     */
    private $service;

    private OrderType $orderType;

    /**
     * @var BaseModel|User
     */
    private $systemUser;

    public function __construct(public $order,
                                public $userId = null)
    {
        $this->__init();
    }

    private function __init(): void
    {
        $this->setStepIndex();
        $this->orderData = $this->order->order_data;
        $this->serviceStatData = $this->orderData['service_stat_data'];
        $this->timeline = $this->order->timeline ?? [];
        $this->orderData['previous_amount'] = $this->previousBalance();
        $this->orderType = OrderType::from($this->orderData['order_type']);
        $this->service = $this->order->service ?? null;
    }

    /***************************************************************/

    /**
     * Handle all the order detail transaction entries
     * Calculating user current and after operation balance
     * Updating account balance and updating order status data
     *
     * @throws UpdateOperationException
     * @throws MasterCurrencyUnavailableException
     * @throws Exception
     */
    public function debitTransaction(array $parameters = []): BaseModel
    {
        try {

            $this->setSystemUser();

            ($this->isReload())
                ? $this->creditBalance()
                : $this->debitBalance();

            $this->order->refresh();

            $this->debitCharge();

            $this->order->refresh();

            $this->debitDiscount();

            $this->orderData['current_amount'] = $this->currentBalance();
            $this->orderData['transaction_amount'] = $this->transactionAmount();

            $transactionAmountFormatted = \currency($this->orderData['transaction_amount'], $this->order->currency);

            $message = ($this->isReload())
                ? "Total {$transactionAmountFormatted} credited for {$this->service->service_name} deposit."
                : "Total {$transactionAmountFormatted} debited for {$this->service->service_name} payment.";

            $this->logTimeline($message);

            if (!Transaction::order()->update($this->order->getKey(), ['order_data' => $this->orderData, 'timeline' => array_values($this->timeline)])) {
                throw (new UpdateOperationException)->setModel(config('fintech.transaction.order_model'), $this->order->getKey());
            }

            $this->order->refresh();

            return $this->order;

        } catch (Exception $exception) {
            throw new ErrorException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * Handle all the order detail transaction entries
     * Calculating user current and after operation balance
     * Updating account balance and updating order status data
     *
     * @throws Exception
     */
    public function creditTransaction(array $parameters = []): BaseModel
    {
        try {
            $this->setSystemUser('credit');

            $this->creditBalance();

            $this->order->refresh();

            $this->creditCommission();

            $this->orderData['current_amount'] = $this->currentBalance();
            $this->orderData['transaction_amount'] = $this->transactionAmount(true);

            $transactionAmountFormatted = \currency($this->orderData['transaction_amount'], $this->order->converted_currency);

            $this->logTimeline("Total {$transactionAmountFormatted} credited for {$this->service->service_name} deposit.");

            if (!Transaction::order()->update($this->order->getKey(), ['order_data' => $this->orderData, 'timeline' => array_values($this->timeline)])) {
                throw (new UpdateOperationException)->setModel(config('fintech.transaction.order_model'), $this->order->getKey());
            }

            $this->order->refresh();

            return $this->order;

        } catch (Exception $exception) {
            throw new ErrorException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    /**
     * @throws UpdateOperationException
     */
    public function creditBalanceToUserAccount(array $parameters = []): void
    {
        $userAccount = Transaction::userAccount()->findWhere(['user_id' => $this->userId(), 'country_id' => $this->order->destination_country_id]);
        $userAccountData = $userAccount->user_account_data ?? [];
        $userAccountData['deposit_amount'] += $this->orderData['transaction_amount'];
        $userAccountData['available_amount'] = $this->orderData['current_amount'];

        if (!Transaction::userAccount()->update($userAccount->getKey(), ['user_account_data' => $userAccountData])) {
            throw (new UpdateOperationException)->setModel(config('fintech.transaction.user_account_model'), $userAccount->getKey());
        }
    }

    /**
     * @throws UpdateOperationException
     */
    public function debitBalanceFromUserAccount(array $parameters = []): void
    {
        $userAccount = Transaction::userAccount()->findWhere(['user_id' => $this->userId(), 'country_id' => $this->order->source_country_id]);
        $userAccountData = $userAccount->user_account_data ?? [];
        $userAccountData['spent_amount'] -= $this->orderData['transaction_amount'];
        $userAccountData['available_amount'] = $this->orderData['current_amount'];
        if (!Transaction::userAccount()->update($userAccount->getKey(), ['user_account_data' => $userAccountData])) {
            throw (new UpdateOperationException)->setModel(config('fintech.transaction.user_account_model'), $userAccount->getKey());
        }
    }

    public function setStepIndex(int $startAt = 1): self
    {
        $this->stepIndex = $startAt;

        return $this;
    }

    public function currentIndex(): int
    {
        return $this->stepIndex;
    }

    /***************************************************************/
    /**
     * @throws MasterCurrencyUnavailableException
     */
    private function setSystemUser(string $transactionType = 'debit'): void
    {
        $filters = [
            'role_name' => SystemRole::MasterUser->value,
            'country_id' => $this->order->source_country_id,
        ];

        if ($transactionType === 'credit') {
            $filters['country_id'] = $this->order->destination_country_id;
        }

        $this->systemUser = Auth::user()->findWhere($filters);

        if (!$this->systemUser) {
            throw new MasterCurrencyUnavailableException($filters['country_id']);
        }

        $this->orderData['master_user_name'] = $this->systemUser->name ?? 'System';
    }

    private function logTimeline(string $message, string $flag = 'info'): void
    {
        $message = ucfirst($message);

        $this->timeline[] = entry_timeline("(System) Step {$this->stepIndex}: {$message}", $flag);
    }

    private function creditBalance(): void
    {
        $balanceOrderDetail = $this->order;
        $balanceFormatted = \currency($balanceOrderDetail->converted_amount, $balanceOrderDetail->converted_currency);
        $userName = $this->orderData['user_name'] ?? null;

        // Receive balance from system
        $this->logTimeline("balance {$balanceFormatted} added for {$this->service->service_name} to ({$userName}) user account.");

        $balanceOrderDetail->amount = $this->order->amount;
        $balanceOrderDetail->sender_receiver_id = $this->systemUser->getKey();
        $balanceOrderDetail->converted_amount = $this->order->converted_amount;
        $balanceOrderDetail->order_detail_cause_name = $this->orderType->value;
        $balanceOrderDetail->order_detail_number = $this->orderDetailNumber();
        $balanceOrderDetail->order_detail_response_id = $this->orderData['purchase_number'] ?? null;
        $balanceOrderDetail->notes = ucfirst("{$this->service->service_name} balance sent to  user: {$userName}");
        $balanceOrderDetail->step = $this->stepIndex++;
        $balanceOrderDetailStore = Transaction::orderDetail()->create(Transaction::orderDetail()->orderDetailsDataArrange($balanceOrderDetail));
        $balanceOrderDetailStore->order_detail_parent_id = $balanceOrderDetail->order_detail_parent_id ?? $balanceOrderDetailStore->getKey();
        $this->orderDetailParentId($balanceOrderDetailStore->order_detail_parent_id);
        $balanceOrderDetailStore->save();

        // Send balance to system
        $this->logTimeline("balance -{$balanceFormatted} deducted for {$this->service->service_name} from ({$this->systemUser->name}) system account.");

        $balanceOrderDetailStore->refresh();
        $balanceOrderDetailForMaster = $balanceOrderDetailStore->replicate();
        $balanceOrderDetailForMaster->order_detail_parent_id = $this->orderDetailParentId();
        $balanceOrderDetailForMaster->sender_receiver_id = $balanceOrderDetail->user_id;
        $balanceOrderDetailForMaster->user_id = $balanceOrderDetail->sender_receiver_id;
        $balanceOrderDetailForMaster->order_detail_amount = -$balanceOrderDetail->amount;
        $balanceOrderDetailForMaster->converted_amount = -$balanceOrderDetail->converted_amount;
        $balanceOrderDetailForMaster->notes = ucfirst("{$this->service->service_name} balance received from user: {$this->systemUser->name}");
        $balanceOrderDetailForMaster->step = $this->stepIndex++;
        $balanceOrderDetailForMaster->save();
    }

    private function debitBalance(): void
    {
        $orderEntry = $this->order;
        $balanceFormatted = \currency($orderEntry->amount, $orderEntry->currency);
        $userName = $this->orderData['user_name'] ?? null;

        // Deducting Balance From User
        $this->logTimeline("balance -{$balanceFormatted} deducted for {$this->service->service_name} form ({$userName}) user account.");

        $orderEntry->amount = -$this->order->amount;
        $orderEntry->converted_amount = -$this->order->converted_amount;
        $orderEntry->order_detail_cause_name = $this->orderType->value;
        $orderEntry->order_detail_number = $this->orderDetailNumber();
        $orderEntry->order_detail_response_id = $this->orderData['purchase_number'] ?? null;
        $orderEntry->notes = ucfirst("{$this->service->service_name} payment sent to system user: {$this->systemUser->name}");
        $orderEntry->step = $this->stepIndex++;
        $orderEntry->sender_receiver_id = $this->systemUser->getKey();
        $orderDetailStore = Transaction::orderDetail()->create(Transaction::orderDetail()->orderDetailsDataArrange($orderEntry));
        $orderDetailStore->order_detail_parent_id = $orderEntry->order_detail_parent_id ?? $orderDetailStore->getKey();
        $this->orderDetailParentId($orderDetailStore->order_detail_parent_id);
        $orderDetailStore->save();

        // Adding Balance To System
        $this->logTimeline("balance {$balanceFormatted} added for {$this->service->service_name} to ({$this->systemUser->name}) system account.");

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
        $userName = $this->orderData['user_name'] ?? null;

        $chargeAmount = calculate_flat_percent($chargeOrderDetail->amount, $this->serviceStatData['charge']);
        $convertedChargeAmount = calculate_flat_percent($chargeOrderDetail->converted_amount, $this->serviceStatData['charge']);
        $balanceFormatted = \currency($chargeAmount, $chargeOrderDetail->currency);

        if ($chargeAmount > 0) {

            // Receive charge for system
            $this->logTimeline("charge -{$balanceFormatted} deducted for {$this->service->service_name} from ({$userName}) user account.");

            $chargeOrderDetail->amount = -$chargeAmount;
            $chargeOrderDetail->converted_amount = -$convertedChargeAmount;
            $chargeOrderDetail->order_detail_cause_name = 'charge';
            $chargeOrderDetail->sender_receiver_id = $this->systemUser->getKey();
            $chargeOrderDetail->order_detail_parent_id = $this->orderDetailParentId();
            $chargeOrderDetail->order_detail_number = $this->orderDetailNumber();
            $chargeOrderDetail->order_detail_response_id = $this->orderData['purchase_number'] ?? null;
            $chargeOrderDetail->notes = ucfirst("{$this->service->service_name} charge received from user: {$userName}");
            $chargeOrderDetail->step = $this->stepIndex++;
            $chargeOrderDetailStore = Transaction::orderDetail()->create(Transaction::orderDetail()->orderDetailsDataArrange($chargeOrderDetail));
            $chargeOrderDetailStore->save();

            // Send balance to system
            $this->logTimeline("charge {$balanceFormatted} added for {$this->service->service_name} to ({$this->systemUser->name}) system account.");

            $chargeOrderDetailStore->refresh();
            $chargeOrderDetailForMaster = $chargeOrderDetailStore->replicate();
            $chargeOrderDetailForMaster->user_id = $chargeOrderDetail->sender_receiver_id;
            $chargeOrderDetailForMaster->sender_receiver_id = $chargeOrderDetail->user_id;
            $chargeOrderDetailForMaster->order_detail_amount = $chargeAmount;
            $chargeOrderDetailForMaster->converted_amount = $convertedChargeAmount;
            $chargeOrderDetailForMaster->notes = ucfirst("{$this->service->service_name} charge sent to system user: {$this->systemUser->name}");
            $chargeOrderDetailForMaster->step = $this->stepIndex++;
            $chargeOrderDetailForMaster->save();
        }
    }

    private function debitDiscount(): void
    {
        $discountOrderDetail = $this->order;
        $userName = $this->orderData['user_name'] ?? null;

        $discountAmount = calculate_flat_percent($discountOrderDetail->amount, $this->serviceStatData['discount']);
        $convertedDiscountAmount = calculate_flat_percent($discountOrderDetail->converted_amount, $this->serviceStatData['discount']);
        $balanceFormatted = \currency($discountAmount, $discountOrderDetail->currency);

        if ($discountAmount > 0) {

            // Receive charge for system
            $this->logTimeline("discount {$balanceFormatted} added for {$this->service->service_name} to ({$userName}) user account.");

            $discountOrderDetail->amount = $discountAmount;
            $discountOrderDetail->converted_amount = $convertedDiscountAmount;
            $discountOrderDetail->order_detail_cause_name = 'discount';
            $discountOrderDetail->order_detail_parent_id = $this->orderDetailParentId();
            $discountOrderDetail->order_detail_number = $this->orderDetailNumber();
            $discountOrderDetail->order_detail_response_id = $this->orderData['purchase_number'] ?? null;
            $discountOrderDetail->notes = ucfirst("{$this->service->service_name} charge sent to system user: {$this->systemUser->name}");
            $discountOrderDetail->step = $this->stepIndex++;
            $discountOrderDetailStore = Transaction::orderDetail()->create(Transaction::orderDetail()->orderDetailsDataArrange($discountOrderDetail));
            $discountOrderDetailStore->save();

            // Send balance to system
            $this->logTimeline("discount -{$balanceFormatted} deducted for {$this->service->service_name} from ({$this->systemUser->name}) system account.");

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
        $userName = $this->orderData['user_name'] ?? null;

        $commissionAmount = calculate_flat_percent($commissionOrderDetail->amount, $this->serviceStatData['discount']);
        $convertedCommissionAmount = calculate_flat_percent($commissionOrderDetail->converted_amount, $this->serviceStatData['discount']);
        $balanceFormatted = \currency($commissionAmount, $commissionOrderDetail->currency);

        if ($commissionAmount > 0) {

            // Receive charge for system
            $this->logTimeline("commission {$balanceFormatted} added for {$this->service->service_name} to ({$userName}) user account.");

            $commissionOrderDetail->amount = $commissionAmount;
            $commissionOrderDetail->converted_amount = $convertedCommissionAmount;
            $commissionOrderDetail->order_detail_cause_name = 'discount';
            $commissionOrderDetail->order_detail_parent_id = $this->orderDetailParentId();
            $commissionOrderDetail->order_detail_number = $this->orderDetailNumber();
            $commissionOrderDetail->order_detail_response_id = $this->orderData['purchase_number'] ?? null;
            $commissionOrderDetail->notes = ucfirst("{$this->service->service_name} charge sent to system user: {$this->systemUser->name}");
            $commissionOrderDetail->step = $this->stepIndex++;
            $discountOrderDetailStore = Transaction::orderDetail()->create(Transaction::orderDetail()->orderDetailsDataArrange($commissionOrderDetail));
            $discountOrderDetailStore->save();

            // Send balance to system
            $this->logTimeline("discount -{$balanceFormatted} deducted for {$this->service->service_name} from ({$this->systemUser->name}) system account.");

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
