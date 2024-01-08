<?php

namespace Tests\Feature\Controllers\Api;

use App\Models\Fund;
use App\Models\FundManager;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class FundsControllerTest extends TestCase
{
    private FundManager $fundManager;
    private FundManager $secondFundManager;
    private Fund $fund;
    private Fund $secondFund;

    public function setUp(): void
    {
        parent::setUp();
        Event::fake();

        $this->fundManager = FundManager::factory()->create();
        $this->secondFundManager = FundManager::factory()->create();
        $this->fund = Fund::factory()->create([
            'fund_manager_id' => $this->fundManager,
            'year' => 2023
        ]);
        $this->secondFund = Fund::factory()->create([
            'fund_manager_id' => $this->secondFundManager
        ]);
    }

    public function test_it_returns_a_list_of_funds(): void
    {
        $response = $this->json('get', route('funds.index'));

        $response->assertSuccessful();
        $response->assertJson([
            'data' => [
                [
                    'id' => $this->fund->id,
                    'name' => $this->fund->name,
                    'year' => $this->fund->year,
                    'manager' => [
                        'id' => $this->fundManager->id,
                        'name' => $this->fundManager->name,
                    ]
                ],
                [
                    'id' => $this->secondFund->id,
                    'name' => $this->secondFund->name,
                    'year' => $this->secondFund->year,
                    'manager' => [
                        'id' => $this->secondFundManager->id,
                        'name' => $this->secondFundManager->name,
                    ]
                ]
            ]
        ]);

        $jsonResponse = $response->json();
        $this->assertIsIterable($jsonResponse['data'][0]['aliases']);
        $this->assertContains($this->fund->aliases[0], $jsonResponse['data'][0]['aliases']);
    }

    public function test_it_returns_a_list_of_funds_filtered_by_name(): void
    {
        $response = $this->json('get', route('funds.index', ['name' => $this->fund->name]));

        $response->assertSuccessful();
        $response->assertJsonMissing([
            'id' => $this->secondFund->id,
            'name' => $this->secondFund->name,
            'year' => $this->secondFund->year,
        ]);
        $response->assertJson([
            'data' => [
                [
                    'id' => $this->fund->id,
                    'name' => $this->fund->name,
                    'year' => $this->fund->year,
                    'manager' => [
                        'id' => $this->fundManager->id,
                        'name' => $this->fundManager->name,
                    ]
                ]
            ]
        ]);

        $jsonResponse = $response->json();
        $this->assertIsIterable($jsonResponse['data'][0]['aliases']);
        $this->assertContains($this->fund->aliases[0], $jsonResponse['data'][0]['aliases']);
    }

    public function test_it_returns_a_list_of_funds_filtered_by_manager(): void
    {
        $response = $this->json('get', route('funds.index', ['manager_id' => $this->secondFundManager->id]));

        $response->assertSuccessful();
        $response->assertJsonMissing([
            'id' => $this->fund->id,
            'name' => $this->fund->name,
            'year' => $this->fund->year,
        ]);
        $response->assertJson([
            'data' => [
                [
                    'id' => $this->secondFund->id,
                    'name' => $this->secondFund->name,
                    'year' => $this->secondFund->year,
                    'manager' => [
                        'id' => $this->secondFundManager->id,
                        'name' => $this->secondFundManager->name,
                    ]
                ]
            ]
        ]);

        $jsonResponse = $response->json();
        $this->assertIsIterable($jsonResponse['data'][0]['aliases']);
        $this->assertContains($this->secondFund->aliases[0], $jsonResponse['data'][0]['aliases']);
    }

    public function test_it_returns_a_list_of_funds_filtered_by_year(): void
    {
        $response = $this->json('get', route('funds.index', ['year' => $this->fund->year]));

        $response->assertSuccessful();
        $response->assertJsonMissing([
            'id' => $this->secondFund->id,
            'name' => $this->secondFund->name,
            'year' => $this->secondFund->year,
        ]);
        $response->assertJson([
            'data' => [
                [
                    'id' => $this->fund->id,
                    'name' => $this->fund->name,
                    'year' => $this->fund->year,
                    'manager' => [
                        'id' => $this->fundManager->id,
                        'name' => $this->fundManager->name,
                    ]
                ]
            ]
        ]);

        $jsonResponse = $response->json();
        $this->assertIsIterable($jsonResponse['data'][0]['aliases']);
        $this->assertContains($this->fund->aliases[0], $jsonResponse['data'][0]['aliases']);
    }

    public function test_it_updates_a_fund(): void
    {
        $payload = [
            'name' => 'New fund Name',
            'aliases' => [
                'Blue',
                'Red',
                'Yellow',
            ],
            'year' => 2024,
            'fund_manager_id' => $this->secondFundManager->id,
        ];
        $response = $this->json('patch', route('funds.update', $this->fund->id), $payload);

        $response->assertSuccessful();
        $this->assertDatabaseHas('funds', [
            'name' => 'New fund Name',
            'year' => 2024,
            'fund_manager_id' => $this->secondFundManager->id
        ]);
        $response->assertJson([
            'data' => [
                'id' => $this->fund->id,
                'name' => 'New fund Name',
                'year' => 2024,
                'manager' => [
                    'id' => $this->secondFundManager->id,
                    'name' => $this->secondFundManager->name,
                ]
            ]
        ]);
    }

    public function test_it_does_not_updates_a_fund_fund_manager_does_not_exist(): void
    {
        $payload = [
            'name' => 'New fund Name',
            'year' => 2024,
            'fund_manager_id' => 1
        ];
        $response = $this->json('patch', route('funds.update', $this->fund->id), $payload);

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'The selected fund manager id is invalid.'
        ]);
        $this->assertDatabaseMissing('funds', [
            'name' => 'New fund Name',
            'year' => 2024,
        ]);
    }

    public function test_it_returns_a_list_of_duplicated_funds(): void
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

        $response = $this->json('get', route('funds.list_duplicates'));

        $response->assertSuccessful();
        $response->assertJsonMissing([
            'id' => $this->fund->id,
            'name' => $this->fund->name,
            'year' => $this->fund->year,
        ]);
        $response->assertJson([
            'data' => [
                [
                    'id' => $fund->id,
                    'name' => $fund->name,
                    'year' => $fund->year,
                    'manager' => [
                        'id' => $this->fundManager->id,
                        'name' => $this->fundManager->name,
                    ]
                ],
                [
                    'id' => $duplicatedFund->id,
                    'name' => $duplicatedFund->name,
                    'year' => $duplicatedFund->year,
                    'manager' => [
                        'id' => $this->fundManager->id,
                        'name' => $this->fundManager->name,
                    ]
                ]
            ]
        ]);
    }
}
