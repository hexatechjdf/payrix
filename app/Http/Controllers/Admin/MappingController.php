<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\FieldRouteService;
use App\Helper\CRM;
use App\Models\OfficeMapping;
use Illuminate\Support\Facades\Cache;

class MappingController extends Controller
{

    protected $fieldService;

    public function __construct(FieldRouteService $fieldService)
    {
        $this->fieldService = $fieldService;
    }

    public function offices(Request $request)
    {
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
                'office_email' => @$office['contactEmail'],
                'selected_location_id' => $existingMappings[$office['officeID']] ?? null,
            ];
        }

        return response()->json(['locations' => $locations, 'data' => $data]);
    }
}
