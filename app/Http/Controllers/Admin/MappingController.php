<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FieldRouteService;
use App\Helper\CRM;
use App\Models\OfficeMapping;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MappingController extends Controller
{

    protected $fieldService;

    public function __construct(FieldRouteService $fieldService)
    {
        $this->fieldService = $fieldService;
    }

    public function offices(Request $request)
    {
          $flags = $this->fieldService->getCustomerFlags();
        //   $flags = $this->fieldService->getSubscriptions(['officeIDs' => [8]]);
        dd($flags);

        // $customers = $this->fieldService->getCustomers(['officeIDs' => [2]]);

        // dd( $customers);



        //  $services = $this->fieldService->getSubscriptions();

        //  dd($services);

        // dd($customers);
        // $services = $this->fieldService->getSubscriptions();

        // $flags = $this->fieldService->getFlags();

        //  $codes = collect($flags)
        // ->pluck('code')
        // ->unique()
        // ->values();

        // dd($codes);
        // dd($flags,$services);

        // $descriptions = collect($services)
        // ->pluck('description')
        // ->unique()
        // ->values();
        // dd($descriptions);
        // $flags = $this->fieldService->getFlags();
        // dd($flags,$services);
        return view('admin.mappings.offices');
    }

    public function fetchOfficeMappingData(Request $request)
    {
        $locations = Cache::remember('locations', now()->addMinutes(30), function() use($request) {
            return CRM::fetchLocations($request);
        });

        $offices = $this->fieldService->getOffices();

        $existingMappings = OfficeMapping::pluck('location_id','office_id')->toArray();

        $data = [];
        foreach($offices as $office) {
            $data[] = [
                'office_id' => @$office['officeID'],
                'office_name' => @$office['officeName'],
                'office_email' =>  @$office['contactEmail'],
                'selected_location_id' => $existingMappings[$office['officeID']] ?? null,
            ];
        }

        return response()->json(['locations' => $locations, 'data' => $data]);
    }

    public function storeOfficeMappingData(Request $request)
    {
        $request->validate([
            'mappings' => 'required|array',
            'mappings.*.office_id' => 'required',
            'mappings.*.location_id' => 'required',
        ]);

        DB::beginTransaction();
        try {

            $incomingOfficeIds = collect($request->mappings)
                ->pluck('office_id')
                ->unique()
                ->toArray();

            OfficeMapping::whereNotIn('office_id', $incomingOfficeIds)->delete();

            foreach ($request->mappings as $map) {
                OfficeMapping::updateOrCreate(
                    ['office_id' => $map['office_id']],
                    ['location_id' => $map['location_id']]
                );
            }

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Office mappings synced successfully',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
