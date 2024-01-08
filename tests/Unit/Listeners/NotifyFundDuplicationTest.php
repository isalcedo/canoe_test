<?php

namespace Tests\Unit\Listeners;

use App\Events\FundDuplication;
use App\Library\SlackFacade;
use App\Listeners\NotifyFundDuplication;
use App\Models\Fund;
use Tests\TestCase;

class NotifyFundDuplicationTest extends TestCase
{
    protected SlackFacade $slackFacade;

    public function setUp(): void
    {
        parent::setUp();

        $this->slackMessageFacade = \Mockery::mock(SlackFacade::class);
        putenv("FUND_DUPLICATION_CHANNEL=test_slack_channel");
    }

    public function test_it_sends_a_message_to_slack_for_fund_duplication(): void
    {
        $fund = Fund::factory()->create();

        //Mock Assert
        $this->slackMessageFacade->allows('sendMessage')
        ->with('The fund ' . $fund->name . ' is duplicated by name or by alias', 'test_slack_channel')
        ->once();
        app()->instance(SlackFacade::class, $this->slackMessageFacade);

        $event = new FundDuplication($fund);
        /** @var NotifyFundDuplication $listener */
        $listener = app()->make(NotifyFundDuplication::class);

        $listener->handle($event);
    }
}
