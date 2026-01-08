<?php

namespace App\Jobs\Pulling\Subscriptions;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Bus;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Batchable;
use Illuminate\Foundation\Bus\Dispatchable;

class CustomeFieldJob implements ShouldQueue
{
    use Queueable, Batchable;

    public $location_id;

    /**
     * Create a new job instance.
     */
    public function __construct($location_id)
    {
        $this->location_id = $location_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $fields = config('mappings.subscription.fields') ?? [];

        $token = getLocationToken($this->location_id,null,1);
        $url =  "locations/{$this->location_id}/customFields";
        $jobs = [];
        for ($i = 1; $i <= 1; $i++) {
            foreach ($fields as $key => $field) {

                $column_key = "subscription_{$i}_{$key}";

                $payload_values = [];

                if (($field['type'] ?? '') === 'SINGLE_OPTIONS' && !empty($field['options'])) {
                    $payload_values = $field['options'];
                }

                $payload = [
                    'name'         => $column_key,
                    'dataType'     => $field['type'],
                    'documentType' => 'field',
                    'placeholder'  => $field['name'],
                    'model'        => 'contact',
                ];

                if (!empty($payload_values)) {
                    $payload['options'] = $payload_values;
                }
                $d = [
                    'payload' => $payload,
                    'location_id' => $this->location_id,
                    'token' => $token,
                    'url' => $url,
                ];

                $jobs[] = new CreateFieldJob($d);
            }

        }

        if (!empty($jobs)) {
            Bus::batch($jobs)
                ->name('Process Customers Batch')
                ->dispatch();
        }


    }
}
