<?php

namespace App\Services;

use App\Models\CrmToken;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;


class FieldRouteService
{
   public function request($method, $uri, $data = [], $show_complete = false)
    {
        try {

            $data['includeData'] = 1;

            $domain = supersetting('field_subdomain');
            $key    = supersetting('field_api_key');
            $token  = supersetting('field_token');

            if (!$token) {
                throw new \Exception('FieldRoutes credentials missing');
            }

            $method = strtoupper($method);

            // $url = 'https://' . $domain . '.fieldroutes.com/' . $uri;
            $url = 'https://' . $domain . '.pestroutes.com/api/' . $uri;

            // dd($url, $key , $token);

            if (in_array($method, ['GET', 'DELETE']) && !empty($data)) {
                $url .= '?' . http_build_query($data);
            }

            $ch = curl_init();

            curl_setopt_array($ch, [
                CURLOPT_URL            => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 20,
                CURLOPT_CUSTOMREQUEST  => $method,
                CURLOPT_HTTPHEADER     => [
                    'authenticationKey: ' . $key,
                    'authenticationToken: ' . $token,
                    'Content-Type: application/json',
                ],
            ]);

            if (!in_array($method, ['GET', 'DELETE'])) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }

            $responseBody = curl_exec($ch);
            $httpCode     = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if (curl_errno($ch)) {
                throw new \Exception(curl_error($ch));
            }

            curl_close($ch);

            $body = json_decode($responseBody, true);

            if ($show_complete) {
                return [
                    'status' => $httpCode,
                    'body'   => $body,
                ];
            }

            return $body;

        } catch (\Exception $e) {

            \Log::error('FieldRoutes API Error', [
                'uri'   => $uri,
                'data'  => $data,
                'error' => $e->getMessage(),
            ]);

            return [
                'error'   => true,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get Offices
     */
    public function getOffices($params = [])
    {
        $cacheKey = 'offices_list';

        $offices = Cache::remember($cacheKey, now()->addMinutes(30), function() use($params) {
            $data = $this->request(
                'POST',
                'office/search',
                $params
            );

            return @$data['offices'] ?? [];
        });

        return $offices;
    }

    public function getSubscriptions($params = [])
    {
        $cacheKey = 'service_types';

        $res = $this->request(
            'POST',
            'serviceType/search',
            $params
        );

        return $res;
    }

    public function getFlags($params = [])
    {
        $cacheKey = 'generic_flags';

        $res = $this->request(
            'POST',
            'genericFlag/search',
             $params
        );

        return $res;
    }

    public function getCustomers($params = [])
    {
        $data = $this->request(
            'POST',
            'customer/search',
            $params
        );

        return $data ?? [];
    }

    public function getEmployees($params = [])
    {
        $data = $this->request(
            'POST',
            'employee/search',
            $params
        );

        return $data ?? [];
    }

    public function getCustomerFlags($params = [])
    {
        $data = $this->request(
            'POST',
            'customerFlag/search',
            $params
        );

        return $data ?? [];
    }


    public function getCustomerSubscriptions($params = [])
    {
        $data = $this->request(
            'POST',
            'subscription/search',
            $params
        );

        return $data ?? [];
    }

}
