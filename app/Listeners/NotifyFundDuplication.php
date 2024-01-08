<?php

namespace App\Listeners;

use App\Events\FundDuplication;
use App\Library\SlackFacade;

/**
 * The test ask for "Write a process to Consume the duplicate_fund_warning event".
 * For me and my experience there is customer support people or something like that
 * looking for Slack messages in a specific channel or emails, etc.
 *
 * I'm simulating that this listener will use a SlackFacade to send a message to Slack
 * with the duplicated Fund.
 */
class NotifyFundDuplication
{
    /**
     * Create the event listener.
     */
    public function __construct(private readonly SlackFacade $slackFacade)
    {
    }

    /**
     * Handle the event.
     */
    public function handle(FundDuplication $event): void
    {
        $fund = $event->fund;
        $message = 'The fund ' . $fund->name . ' is duplicated by name or by alias';

        $this->slackFacade->sendMessage(
            $message, env('FUND_DUPLICATION_CHANNEL')
        );
    }
}
