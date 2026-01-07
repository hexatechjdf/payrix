<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Jobs\Pulling\CustomField\ManageFiltersJob;
use App\Jobs\Pulling\Customers\PullDataJob;
use App\Jobs\Pulling\Customers\PullFlagsJob;
use App\Jobs\Pulling\Customers\PullSubscriptionsJob;

class PullingController extends Controller
{
    public function genericFlags(Request $request)
    {
        ManageFiltersJob::dispatchSync('flags',[],$request->location_id, $request->office_id);

        return response()->json(['success' => true]);
    }

    public function serviceTypes(Request $request)
    {
        ManageFiltersJob::dispatchSync('services',[],$request->location_id, $request->office_id);

        return response()->json(['success' => true]);
    }

    public function customers(Request $request)
    {
        PullDataJob::dispatchSync();
        // PullSubscriptionsJob::dispatchSync('flags');
        // PullFlagsJob::dispatchSync('flags');
    }
}
