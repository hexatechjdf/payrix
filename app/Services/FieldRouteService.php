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

            $domain = supersetting('field_subdomain');
            $key    = supersetting('field_api_key');
            $token  = supersetting('field_token');

            if (!$token) {
                throw new \Exception('FieldRoutes credentials missing');
            }

            $method = strtoupper($method);

            // $url = 'https://' . $domain . '.fieldroutes.com/' . $uri;
            $url = 'https://' . $domain . '.pestroutes.com/api/' . $uri;

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

        $offices = Cache::remember($cacheKey, now()->addMinutes(30), function() {
            $payload = ['includeData' => 1];

            $data = $this->request(
                'POST',
                'office/search',
                $payload
            );

            return $data['offices'] ?? [];
        });

        return $offices;
    }
    public function getOfficesIds($params = [])
    {
        $payload = ['includeData' => 1];

        return $this->request(
            'POST',           // must be POST, not GET
            'office/search',
            $payload
        );
    }
}
