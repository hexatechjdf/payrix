<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function listenAppointment(Request $request)
    {
       $appointment = $request->all();
       $appointment =  (object)$appointment;

       $type = $appointment->type;
    }
}
