<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\FundResource;
use App\Http\Traits\HandlesFunds;
use App\Models\Fund;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
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

    public function listDuplicates(): AnonymousResourceCollection
    {
        /**
         * I must admit, in this one I googled a lot and also used a few AIs :D
         * This was my first time trying to solve something like this.
         * I think, production ready, this can be the best query without using a lot of PHP or Database power
         * using my approach of alises as a JSON column. I don't know right now if a many-to-many relationship
         * was better to aliases and funds. In any case, this works and is tested. Yai!!
         */
        $funds = Fund::join(DB::raw('(SELECT name, fund_manager_id
                    FROM funds
                    GROUP BY name, fund_manager_id
                    HAVING COUNT(*) > 1) duplicados'),
            static function ($join) {
                $join->on('funds.name', '=', 'duplicados.name')
                    ->on('funds.fund_manager_id', '=', 'duplicados.fund_manager_id');
            }
        )->with('manager')->get();


        return FundResource::collection($funds);
    }
}
