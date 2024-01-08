<?php

namespace Tests\Unit\Models;

use App\Models\Fund;
use App\Models\FundManager;
use Tests\TestCase;

class FundManagerTest extends TestCase
{
    private FundManager $fundManager;

    public function setUp(): void
    {
        parent::setUp();

        $this->fundManager = FundManager::factory()->create();
    }

    public function test_it_has_related_funds(): void
    {
        /** @var Fund $fund */
        $fund = Fund::factory()->create([
            'fund_manager_id' => $this->fundManager
        ]);

        $funds = $this->fundManager->funds;

        $this->assertIsIterable($funds);
        $this->assertEquals($fund->id, $funds->first()->id);
    }
}
