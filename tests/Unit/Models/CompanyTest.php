<?php

namespace Tests\Unit\Models;

use App\Models\Company;
use App\Models\Fund;
use App\Models\FundManager;
use Tests\TestCase;

class CompanyTest extends TestCase
{
    private Company $company;

    public function setUp(): void
    {
        parent::setUp();

        $this->company = Company::factory()->create();
    }

    public function test_it_has_related_funds(): void
    {
     $fundManager = FundManager::factory()->create();
        /** @var Fund $fund */
        $fund = Fund::factory()->create([
            'fund_manager_id' => $fundManager
        ]);
        $this->company->funds()->attach($fund);

        $this->assertIsIterable($this->company->funds);
        $this->assertEquals($fund->id, $this->company->funds->first()->id);
        $this->assertDatabaseHas('company_fund', [
            'company_id' => $this->company->id,
            'fund_id' => $fund->id
        ]);
    }
}
