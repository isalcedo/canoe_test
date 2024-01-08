<?php

namespace Tests\Unit\Models;

use App\Events\FundDuplication;
use App\Models\Company;
use App\Models\Fund;
use App\Models\FundManager;
use Tests\TestCase;
use Illuminate\Support\Facades\Event;

class FundTest extends TestCase
{
    private FundManager $fundManager;
    private Fund $fund;

    public function setUp(): void
    {
        parent::setUp();
        Event::fake([FundDuplication::class]);

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

        Event::assertNotDispatched(FundDuplication::class);
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

        Event::assertNotDispatched(FundDuplication::class);
    }

    public function test_it_fires_the_fund_duplication_event_by_same_name(): void
    {
        /** @var Fund $fund */
        $fund = Fund::factory()->create([
            'name' => 'Same Name',
            'fund_manager_id' => $this->fundManager
        ]);
        $duplicatedFund = Fund::factory()->create([
            'name' => 'Same Name',
            'fund_manager_id' => $this->fundManager
        ]);

        Event::assertDispatched(FundDuplication::class);

        $this->assertDatabaseHas('funds', [
            'id'=> $fund->id,
        ]);
        $this->assertDatabaseHas('funds', [
            'id'=> $duplicatedFund->id,
        ]);
    }

    public function test_it_fires_the_fund_duplication_event_by_same_alias(): void
    {
        /** @var Fund $fund */
        $fund = Fund::factory()->create([
            'aliases' => ['same_alias'],
            'fund_manager_id' => $this->fundManager
        ]);
        $duplicatedFund = Fund::factory()->create([
            'aliases' => ['same_alias'],
            'fund_manager_id' => $this->fundManager
        ]);

        Event::assertDispatched(FundDuplication::class);

        $this->assertDatabaseHas('funds', [
            'id'=> $fund->id,
        ]);
        $this->assertDatabaseHas('funds', [
            'id'=> $duplicatedFund->id,
        ]);
    }
}
