<?php

namespace App\Http\Controllers\Location;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helper\CRM;

class MappingController extends Controller
{
    public function fetchUsersMappingData(Request $request)
    {
        $user = loginUser();

        $users = Cache::remember('users', now()->addMinutes(30), function() use($user) {
            return CRM::searchUsers($user->location_id);
        });

        $employees = $this->fieldService->getOffices();

        $existingMappings = UserMapping::pluck('location_id','office_id')->toArray();

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
}
