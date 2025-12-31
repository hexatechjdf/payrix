<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MappingController extends Controller
{
    public function offices(Request $request)
    {
        return view('admin.mappings.offices')
    }
}
