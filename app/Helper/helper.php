<?php

use App\Helper\gCache;
use App\Models\OAuthClient;
use App\Models\Setting;
use App\Models\User;
use App\Models\OfficeMapping;
use App\Models\Customer;
use App\Models\Connectivity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Models\LocationCustomField;
use App\Helper\CRM;

function supersetting($key, $default = '')
{
    $setting = gCache::get($key, function () use ($default, $key) {
        if (strpos($key, 'oauth_') !== false) {

            $setting = OAuthClient::where(['type' => str_replace('oauth_', '', $key)])->first();
            if ($setting) {
                $setting = $setting->toArray();
            }
            $value = $setting;
        } else {
            $setting = Setting::where(['key' => $key])->first();
            $value   = $setting->value ?? $default;
        }

        gCache::put($key, $value);
        return $value;
    });

    return $setting;
}

function extractDateAndTime($datetimeStr, $toTimezone)
{
    // Split at 'T' to separate date and time.
    $time = convertTimeIntoTimezone($datetimeStr, $toTimezone);
    \Log::info(['time' => $time, 'org_time' => $datetimeStr]);
    [$datePart, $timeWithOffset] = explode(' ', $time);

                                               // Extract only HH:MM from time part
    $timePart = substr($timeWithOffset, 0, 5); // Gets "15:00"

    return [
        'date' => $datePart, // "2025-07-31"
        'time' => $timePart, // "15:00"
    ];
}

function findUser($user)
{
    if (! ($user instanceof User)) {
        $user = User::find($user);
    }
    return $user;
}

function getAuthUrl($web)
{
    return route('autoauth.check') . '?web=' . $web . '&location_id=' . braceParser('[[location.id]]') . '&sessionkey=' . braceParser('[[user.sessionKey]]'); // . '&user_id=' . braceParser('[[user.id]]')
}

// function getMediaUrl($contactId = null)
// {
//     $routeUrl = route('location.media');

//     if ($contactId) {
//         // $routeUrl .= '?contact_id=' . braceParser('[[contact.contact_id]]');
//     }

//     return $routeUrl;
// }

function isAdmin()
{
    return (loginUser()->role ?? 1) == 1;
}

function braceParser($value)
{
    return str_replace(['[', ']'], ['{', '}'], $value);
}

function loginUser($user = null)
{
    if (auth()->check()) {
        $user = auth()->user();

    } else {
        if (!$user) {
            $user = User::find($user);
        }
    }
    return $user;
}

function getLocationId()
{
    $user       = loginUser();
    $isAdmin    = false;
    $locationId = null;
    if ($user) {
        $isAdmin    = $user->role == 1;
        $locationId = $isAdmin ? null : $user->location_id ?? null;
    }
    return $locationId;
}

function save_settings($key, $value = '')
{
    $setting = Setting::updateOrCreate(
        ['key' => $key],
        [
            'value' => $value,
            'key'   => $key,
        ]
    );
    gCache::del($key);
    gCache::put($key, $value);
    return $setting;
}

function assertRoleUser($validSSO)
{
    return ($validSSO['role'] == 'user' && $validSSO['type'] == 'account');
}

function locationTokenCacheKey($locationId)
{
    return "token_{$locationId}";
}

if (! function_exists('getLocationToken')) {
    function getLocationToken($locationId, $userId = null,$company_id = null)
    {
        $tokenCacheKey = locationTokenCacheKey($locationId);

        return \Cache::remember($tokenCacheKey, 10 * 60, function () use ($locationId,$company_id) {
            return CRM::getTokenByLocation($locationId,$company_id);
        });
    }
}

// function convertTimeIntoTimezone($time, $timeZone = 'UTC', $format = 'Y-m-d H:i:s', $to = "MDT")
// {
//     $convertedTime = Carbon::parse($time, $timeZone)->setTimezone($to)->format($format);

//     return $convertedTime;
// }

function convertTimeIntoTimezone($time, $toTimezone = "MDT", $fromTimezone = 'UTC', $format = 'Y-m-d H:i:s')
{
    $convertedTime = Carbon::parse($time, $fromTimezone)->setTimezone($toTimezone)->format($format);

    return $convertedTime;
}

function currentDateTime($timezone = 'UTC')
{
    return Carbon::now($timezone);
}

function getISODate($date)
{
    return Carbon::parse($date)->toISOString();
}

function convertIntoIsoString(Carbon $dateTime)
{
    return $dateTime->toISOString(); //toIso8601String()  //toISOString()
}

function addDelay($seconds)
{
    return Carbon::now()->addSeconds($seconds);
}

function addMinutes($minutes)
{
    return Carbon::now()->addMinutes($minutes);
}

function subMinutes($minutes)
{
    return Carbon::parse(now(), 'UTC')->subMinutes($minutes);
}

function addHours(int | float $hours): Carbon
{
    return Carbon::now()->addHours($hours);
}

function subHours(int | float $hours): Carbon
{
    return Carbon::parse(now(), 'UTC')->subHours($hours);
}

function subDays(int | float $days): Carbon
{
    return Carbon::now()->subDays($days);
}

function checkError($detail)
{
    if (($detail->error ?? null) || ($detail->statusCode ?? null) == 401) {
        return false;
    }
    return true;
}

function assertErrorinResponse($response)
{
    if (isset($response['error'])) {
        throw new \Exception($response['message']);
    }
    return true;
}

function autoAuth($locationId)
{
    $user = User::where(['location_id' => $locationId])->first();

    if ($user) {
        \Auth::login($user);
        return true;
    }

    return false;

}

function assertLocationUserLogin($ssoLocationId): bool
{
    if (\Auth::check()) {
        $authLocationId = loginUser()->location_id ?? null;

        if ($authLocationId != $ssoLocationId) {
            \Auth::logout();
            sleep(1);
            return autoAuth($ssoLocationId); // Auto-authenticate if not logged in
        }
        return true;
    }

    return autoAuth($ssoLocationId); // Auto-authenticate if not logged in
}

function arrayToObject(array $array)
{
    return json_decode(json_encode($array));
}

function successJsonResponse()
{
    return response()->json([
        'success' => true,
        'message' => 'Webhook received and queued for processing in the backgrond job.',
    ]);
}

function prepareLogData($locationId, $payload, $type, $status, $message)
{
    return [
        'location_id' => $locationId,
        'type'        => $type,
        'status'      => $status,
        'payload'     => $payload,
        'message'     => $message,
    ];
}

function replaceBrWithNewline(string $html): string
{
    return str_ireplace(['<br>', '<br/>', '<br />'], "\n", $html);
}

function findOrCreateUserInDb(string $locationId): array
{
    $user = User::with('token')->firstOrCreate(
        ['location_id' => $locationId],
        [
            'name'  => 'Location User',
            'email' => "{$locationId}@autoauth.net",
            'password' => bcrypt("autoauth_{$locationId}"),
            'ghl_api_key' => '-',
        ]
    );

    // TODO: If API key is required later, we may need to connect OAuth to fetch it.
// Example usage:
// $user->ghl_api_key = $req->token;
// if (! $user->wasRecentlyCreated) {
//     $user->save();
// }

    return [$user->wasRecentlyCreated, $user];
}

function getStripeDataCacheKey($contactId)
{
    return "stripe_data_{$contactId}";
}

function getSubaccountDataCacheKey($email)
{
    return "subaccount_data_{$email}";
}

function isAgencyConnected(): bool
{
    // Check if GHL agency is connected
    // This should check for valid OAuth tokens or connection status

    $authuser = auth::user();
    return optional($authuser->token)->company_id ?? false;

    return true;
}


   function sendMail(string $to, string $subject, string $view, array $data = []): bool
    {
        try {
            Mail::send($view, $data, function ($message) use ($to, $subject) {
                $message->to($to)->subject($subject);
            });
            return true;
        } catch (\Exception $e) {
            \Log::error("Mail sending failed: " . $e->getMessage());
            return false;
        }
    }


function getFieldRoutsKeys()
{
    return [

        'flags' => [
            'id' => 'genericFlagID',
            'value' => 'code',
            'search_id' => 'genericFlagIDs',
            'data' => 'genericFlags',
            'group_by' => 'officeID',
            'function' => 'getFlags',
            'search_key' => 'Flags',
            'search_name' => 'Generic Flags',
            'column_key' => 'generic_flags',
        ],
        'services' => [
            'id' => 'typeID',
            'value' => 'description',
            'search_id' => 'typeIDs',
            'data' => 'serviceTypes',
            'group_by' => 'officeID',
            'function' => 'getSubscriptions',
            'search_key' => 'Subscriptions',
            'search_name' => 'Active Subscriptions',
            'column_key' => 'active_subscriptions',
        ]

    ];
}


function getOfficeParams($location_id = null,$office_id = null)
{
    $existingMappings = [];
    $keys = [];

    if(!$location_id)
    {
        $existingMappings = OfficeMapping::pluck('location_id','office_id')->toArray();
        $keys = array_keys($existingMappings);

    }else{
        $existingMappings[$office_id] = $location_id;
        $keys = [$office_id];
    }

    return [$keys, $existingMappings];
}


function defaultContactFields()
{
    return [
        "id" => 'Contact Id',
        "contactName" => '',
        "locationId" => '',
        "firstName" =>'',
        "lastName" => '',
        "email" => '',
        "timezone" => '',
        "companyName" => '',
        "phone" => '',
        "dnd" => '',
        "dndSettings" => '',
        "type" => '',
        "source" => '',
        "assignedTo" => '',
        "address1" => '',
        "city" => '',
        "state" => '',
        "country" => '',
        "postalCode" => '',
        "dateOfBirth" => '',
    ];
}


function updateOrCreateField($locationId,$id,$options,$column_key)
{
    return LocationCustomField::updateOrCreate(
        ['location_id' => $locationId],
        [$column_key => $id, $column_key.'_options' => $options]
    );
}


function updateOrCreateCustomer($locationId,$contact_id,$data,$office_id,$customer_id)
{
    return Customer::updateOrCreate(
        ['customer_id' => $customer_id],
        ['contact_id' => $contact_id, 'office_id' => $office_id, 'location_id' => $locationId, 'body' => $data]
    );
}


function splitOption($value)
{
    [$id, $label] = explode('_', $value, 2);
    return [
        'id' => $id,
        'label' => $label,
        'full' => $value
    ];
}
