<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Models\EmployeeMapping;
use App\Models\CalendarMapping;
use App\Models\Customer;
use App\Models\Appointment;
use App\Services\FieldRouteService;

class WebhookController extends Controller
{
    protected $fieldService;

    public function __construct(FieldRouteService $fieldService)
    {
        $this->fieldService = $fieldService;
    }

    public function listenAppointment(Request $request)
    {
        $appointment = $request->all();

        if(empty($appointment['type']) || empty($appointment['appointment'])){
            return response()->json(['status' => false, 'message' => 'Invalid payload'], 400);
        }

        $type = $appointment['type'];
        $payload = $appointment['appointment'];

        $mapping = config("mappings.appointment", []);

        $newArray = [];

        foreach ($mapping as $payloadKey => $newKey) {
            $value = Arr::get($payload, $payloadKey);

            if($payloadKey === 'calendarId'){
                $value = $this->getServiceByCalendar($value);
            }
            if($payloadKey === 'assignedUserId'){
                $value = $this->getEmployeeByUser($value);
            }
            if($payloadKey === 'contactId'){
                $value = $this->getCustomerByContact($value);
            }

            $newArray[$newKey] = $value;
        }

        if($type === 'AppointmentCreate'){
            $url = 'appointment/create';
        }
        elseif($type === 'AppointmentUpdate'){
            $newArray['appointmentID'] = $this->getAppointment($payload['calendarId']);
            $url = 'appointment/update';
        } else {
            return response()->json(['status' => false, 'message' => 'Unsupported type'], 400);
        }

        $res = $this->fieldService->request('POST', $url, $newArray);

        $appointment_id = $res['result'] ?? null;

        if($appointment_id){
            $this->manageAppointment($newArray, $payload, $appointment_id, $appointment['locationId']);
        }

        return response()->json(['status' => true, 'message' => 'Appointment processed']);
    }

    public function getCustomerByContact($contactId)
    {
        return Customer::where('contact_id', $contactId)->value('customer_id') ?? '';
    }

    public function getEmployeeByUser($userId)
    {
        return EmployeeMapping::where('user_id', $userId)->value('employee_id') ?? null;
    }

    public function getServiceByCalendar($calendarId)
    {
        return CalendarMapping::where('calendar_id', $calendarId)->value('service_id') ?? '';
    }

    public function getAppointment($calendarId)
    {
        return Appointment::where('calendar_id', $calendarId)->value('appointment_id') ?? '';
    }

    public function manageAppointment($data, $payload, $id, $locationId)
    {
        Appointment::updateOrCreate(
            ['appointment_id' => $id],
            [
                'calendar_id' => $payload['calendarId'],
                'customer_id' => $data['customerID'] ?? null,
                'employee_id' => $data['employeeID'] ?? null,
                'contact_id'  => $payload['contactId'] ?? null,
                'location_id' => $locationId ?? null,
                'body'  => $data,
            ]
        );
    }
}
