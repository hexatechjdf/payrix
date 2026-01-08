<?php

namespace App\Http\Controllers\Location;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helper\CRM;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Services\FieldRouteService;
use App\Models\EmployeeMapping;
use App\Models\CalendarMapping;

class MappingController extends Controller
{
    protected $fieldService;

    public function __construct(FieldRouteService $fieldService)
    {
        $this->fieldService = $fieldService;
    }


    public function employees(Request $request)
    {
        return view('location.mappings.employees');
    }

    public function calendars(Request $request)
    {
        return view('location.mappings.calendar');
    }

    public function fetchEmployeeMappingData(Request $request)
    {
        $user = loginUser();

        $locationId = $request->location_id ?? $user->location_id;
        $office_id =  $request->office_id ?? getOfficeParams($locationId , null,'office');

        $users = Cache::remember('users', now()->addMinutes(30), function() use($locationId) {
            return CRM::searchUsers($locationId);
        });


        $employees = $this->fieldService->getEmployees(['officeIDs' => [$office_id]]);
        $employees = $employees['employees'];

        $existingMappings = EmployeeMapping::pluck('user_id', 'employee_id')->toArray();

        $data = [];

        foreach ($employees as $employee) {
            $data[] = [
                'employee_id'   => $employee['employeeID'] ?? null,
                'employee_name'=> $employee['fname'] .' '. $employee['lname'],
                'employee_email'=> $employee['email'] ?? null,
                'selected_user_id' => $existingMappings[$employee['employeeID']] ?? null,
            ];
        }

        return response()->json([
            'users' => $users,
            'data'  => $data,
        ]);
    }
   public function fetchCalendarMappingData(Request $request)
    {
        $user = loginUser();

        $locationId = $request->location_id ?? $user->location_id;
        $office_id =  $request->office_id ?? getOfficeParams($locationId , null,'office');

        $calendars = Cache::remember(
            'calendars_' . $locationId,
            now()->addMinutes(30),
            function () use ($locationId) {
                return CRM::getLocationCalenders($locationId);
            }
        );

        $servicesResponse = $this->fieldService->getSubscriptions(['officeIDs' => [$office_id]]);
        $services = $servicesResponse['serviceTypes'] ?? [];

        $existingMappings = CalendarMapping::pluck('calendar_id', 'service_id')->toArray();

        $data = [];

        foreach ($services as $service) {
            $data[] = [
                'service_id'   => $service['typeID'] ?? null,
                'service_name' => $service['description'] ?? null,
                'selected_calendar_id' => $existingMappings[$service['typeID']] ?? null,
            ];
        }

        return response()->json([
            'calendars' => $calendars,
            'data'      => $data,
        ]);
    }


    public function storeEmployeeMappingData(Request $request)
    {
        $request->validate([
            'mappings' => 'required|array',
            'mappings.*.employee_id' => 'required',
            'mappings.*.user_id' => 'required',
        ]);

        DB::beginTransaction();

        try {

            $incomingEmployeeIds = collect($request->mappings)
                ->pluck('employee_id')
                ->unique()
                ->toArray();

            EmployeeMapping::whereNotIn('employee_id', $incomingEmployeeIds)->delete();

            foreach ($request->mappings as $map) {
                EmployeeMapping::updateOrCreate(
                    ['employee_id' => $map['employee_id']],
                    ['user_id' => $map['user_id']]
                );
            }

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Employee mappings synced successfully',
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

    public function storeCalendarMappingData(Request $request)
    {
        $request->validate([
            'mappings' => 'required|array',
            'mappings.*.calendar_id' => 'required',
            'mappings.*.service_id'  => 'required',
        ]);

        DB::beginTransaction();

        try {

            $incomingKeys = collect($request->mappings)->map(function ($item) {
                return $item['calendar_id'].'_'.$item['service_id'];
            })->toArray();

            CalendarMapping::get()
                ->reject(function ($row) use ($incomingKeys) {
                    return in_array($row->calendar_id.'_'.$row->service_id, $incomingKeys);
                })
                ->each
                ->delete();

            foreach ($request->mappings as $map) {
                CalendarMapping::updateOrCreate(
                    [
                        'calendar_id' => $map['calendar_id'],
                        'service_id'  => $map['service_id'],
                    ],
                    []
                );
            }

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Calendar & Service mappings saved successfully',
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
