<?php

namespace Fintech\Transaction\Enums;

enum OrderStatus: string
{
    case Processing = 'processing';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
    case Failed = 'failed';
    case FailedAndRefunded = 'failed_and_refunded';
    case Refunded = 'refunded';
    case RefundReceived = 'refund_received';
    case Successful = 'successful';
    case Success = 'success';
    case Cancelled = 'cancelled';
    case AdminVerification = 'admin_verification';
    case Received = 'received';
}
