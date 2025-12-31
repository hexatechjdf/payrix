<?php

// app/Http/Controllers/OAuthController.php

namespace App\Http\Controllers;

use App\Helper\CRM;
use App\Models\OAuthToken;
use Illuminate\Http\Request;

class OAuthController extends Controller
{

    protected $viewPath;
    protected $accessTokenKey;

    public function __construct()
    {
        $this->accessTokenKey = 'access_token';
        $this->viewPath       = 'location.integrations.';
    }

    public function crmCallback(Request $request)
    {
        $code = $request->code ?? null;

        if ($code) {
            $user_id   = auth()->user()->id;
            $code      = CRM::crm_token($code, '');
            $code      = json_decode($code);
            $user_type = $code->userType ?? null;
            $main      = route('admin.settings'); // old-route: without-s


            //dd($user_id, $code, $user_type);

            if ($user_type) {
                $token = $user->crmtoken ?? null;

                list($connected, $con) = CRM::go_and_get_token($code, '', $user_id, $token);

                if ($connected) {
                    return redirect($main)->with('success', 'Connected Successfully');
                }
                return redirect($main)->with('error', json_encode($code));
            }
        }
        return response()->json(['message' => 'Not allowed to connect']);
    }

    public function getRedirectUri($request, $authorizationUrl)
    {
        if ($request->ajax()) {
            return response()->json(['url' => $authorizationUrl]);
        }
        return redirect($authorizationUrl);
    }

    public function OAuthConnected($data = [])
    {
        return view($this->viewPath . '.connected', $data);
    }
}
