<?php

namespace App\Jobs\Pulling\Customers;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Bus\Batchable;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Customer;
use App\Helper\CRM;

class ManageContactJob implements ShouldQueue
{
    use Queueable, Batchable;

    public $location_id;
    public $data;
    public $is_delay;

    /**
     * Create a new job instance.
     */
    public function __construct(
        $data = [],
        $location_id = null,
        $is_delay = true
    ) {
        $this->location_id = $location_id;
        $this->data       = $data;
        $this->is_delay    = $is_delay;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $record = $this->data;
        $customer_id = $record['customerID'];
        $office_id = $record['officeID'];

        $payload = $this->mapApiData($record);

        $token = getLocationToken($this->location_id,null,1);


        $this->updateOrCreateContact($payload, $token,$customer_id,$this->location_id,$office_id);


    }

    public function getFieldByDb($customer_id)
    {
        $l = Customer::where('customer_id' , $customer_id)->select('customer_id','contact_id')->first();

        return @$l->contact_id;
    }

    public function updateOrCreateContact($payload, $token,$customer_id,$locationId,$office_id)
    {
        $filters = [
            [
                'group' => 'OR',
                'filters' => [
                    [
                        'field'    => 'email',
                        'operator' => 'eq',
                        'value'    => $payload['email'],
                    ],
                    [
                        'field'    => 'phone',
                        'operator' => 'eq',
                        'value'    => $payload['phone'],
                    ],
                ],
            ],
        ];

        $contact_id = $this->getFieldByDb($customer_id) ?? CRM::searchContact($locationId, $filters, $token);

        if(!$contact_id)
        {
           $contact_id =  CRM::createContact($payload, $locationId, $token);
        }
        else{
            $contact_id = CRM::updateContact($contact_id, $payload, $locationId, $token);
        }

        if($contact_id)
        {
           updateOrCreateCustomer($locationId,$contact_id,$payload,$office_id,$customer_id);
        }

        dd($contact_id);
    }

    public function mapApiData(array $apiData): array
    {

        $mapping = config('mappings.contact');
        $result = [];
        $result['custom_fields'] = [];

        foreach ($mapping as $sourceKey => $targetKey) {

            if ($sourceKey === 'custom_fields') {
                foreach ($targetKey as $customKey => $customTarget) {
                    if (array_key_exists($customKey, $apiData)) {
                        $result['custom_fields'][$customTarget] = $apiData[$customKey];
                    }
                }
                continue;
            }

            if (array_key_exists($sourceKey, $apiData)) {
                $result[$targetKey] = $apiData[$sourceKey];
            }
        }

        if (empty($result['custom_fields'])) {
            unset($result['custom_fields']);
        }

        return $result;
    }
}
