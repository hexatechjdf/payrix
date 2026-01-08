<?php

namespace App\Http\Controllers\Location;

use App\Helper\CRM;
use App\Helper\gCache;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\CrmToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class SubaccountController extends Controller
{
    public function index(Request $request)
    {
        return view('location.index');
    }

    public function setToken(Request $request)
    {
        $validSSO = json_decode($request->data,true) ?? [];
        if(@$validSSO['activeLocation'])
        {
            $com = CrmToken::where('user_type', 'Company')->where('company_id', @$validSSO['companyId'])->first();
            CRM::getLocationAccessToken($com->user_id, $validSSO['activeLocation']);
        }

        return response()->json(['success' => true]);
    }

    public function verifySso(Request $request)
    {
        $tokenBy = 'self';
        list($obj,$error) = $this->validateSso($request);

        if($error!='')
        {
            return response()->json(['error' => $error], 401);
        }

        return response()->json(['success' => true, 'validSSO' => $obj,'tokenBy' => $tokenBy]);
    }

    public function validateSso($request)
    {
        $error = '';
        $validSSO = null;

        $ghlSsoToken = $request->key;

        if (!$ghlSsoToken) {
            $error = 'CRM SSO token missing';
        }else{
            $validSSO = CRM::decryptSSO($ghlSsoToken);
            if (!$validSSO || !@$validSSO['activeLocation']) {
                $error = 'Invalid CRM SSO token';
            }
        }

        return [$validSSO,$error];
    }
}
