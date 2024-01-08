<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FundResource;
use App\Http\Traits\HandlesFunds;
use App\Models\Fund;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

class FundsController extends Controller
{
    use HandlesFunds;

    /**
     * Retrieve all funds and their corresponding managers.
     *
     * @return AnonymousResourceCollection The collection of funds and their managers.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $funds = Fund::with('manager');
        $funds = $this->filterFunds($request, $funds);

        return FundResource::collection($funds);
    }

    public function update(Request $request, Fund $fund): FundResource
    {
        $request->validate([
            'name' => 'sometimes|required|string',
            'aliases' => 'sometimes|required|array',
            'year' => 'sometimes|required|integer',
            'fund_manager_id' => [
                'sometimes',
                'required',
                'integer',
                Rule::exists('fund_managers', 'id')
            ]
        ]);

        $this->updateFund($request, $fund);

        return new FundResource($fund);
    }
}
