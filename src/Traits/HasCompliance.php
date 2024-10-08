<?php

namespace Fintech\Transaction\Traits;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

trait HasCompliance
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

}
