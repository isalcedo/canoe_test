<?php

namespace Tests\Unit\Models;

use App\Models\Company;
use App\Models\Fund;
use App\Models\FundManager;
use Tests\TestCase;

class FundTest extends TestCase
{
    private FundManager $fundManager;
    private Fund $fund;

    public function setUp(): void
    {
        parent::setUp();


        $this->fundManager = FundManager::factory()->create();
    }

    public function test_it_has_related_managers(): void
    {
        /** @var Fund $fund */
        $fund = Fund::factory()->create([
            'fund_manager_id' => $this->fundManager
        ]);

        $this->assertInstanceOf(FundManager::class, $fund->manager);
        $this->assertEquals($this->fundManager->id, $fund->manager->id);
    }

    public function test_it_has_related_companies(): void
    {
        /** @var Fund $fund */
        $fund = Fund::factory()->create([
            'fund_manager_id' => $this->fundManager
        ]);
        $company = Company::factory()->create();

        $fund->companies()->attach($company);


        $this->assertIsIterable($fund->companies);
        $this->assertEquals($company->id, $fund->companies->first()->id);
        $this->assertDatabaseHas('company_fund', [
           'company_id' => $company->id,
           'fund_id' => $fund->id
        ]);
    }
}
