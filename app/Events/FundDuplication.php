<?php

namespace App\Events;

use App\Listeners\NotifyFundDuplication;
use App\Models\Fund;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * This class represents an event that is triggered when a fund is duplicated.
 *
 * @see NotifyFundDuplication
 */
class FundDuplication
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public readonly Fund $fund)
    {
        //
    }
}
