<?php

namespace App\Http\Traits;

use App\Models\Fund;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

trait HandlesFunds
{
    public function filterFunds(Request $request, Builder $fundsBuilder): Collection
    {
        if ($request->query('manager_id')) {
            /**
             * Here we can use a Scope but, I prefer, for the sake of the easy code reading,
             * to keep this way as it is easy to catch what's going on here.
             */
            $fundsBuilder->where(['fund_manager_id' => $request->query('manager_id')]);
        }

        if ($request->query('name')) {
            /**
             * @see HandlesFunds:15
             */
            $fundsBuilder->where(['name' => $request->query('name')]);
        }

        if ($request->query('year')) {
            /**
             * @see HandlesFunds:15
             */
            $fundsBuilder->where(['year' => $request->query('year')]);
        }

        return $fundsBuilder->get();
    }
    public function updateFund(Request $request, Fund $fund): Fund
    {
        /**
         * I did not create any logic related to the companies and funds relationship
         * because it is a many-to-many relationship and I think deleting or creating a new
         * company-fund relationship should have specific endpoints.
         */
        $fund->update($request->all());
        $fund->load('manager');

        return $fund;
    }
}
