<?php

namespace App\Jobs\Pulling\Customers;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Services\FieldRouteService;
use App\Models\Customer;
use App\Models\LocationCustomField;
use Illuminate\Support\Facades\Bus;
use Illuminate\Bus\Batch;
use App\Jobs\Pulling\CustomField\UpdateContactFieldJob;

class PullSubscriptionsJob implements ShouldQueue
{
    use Queueable;

    public $data_key;
    public $location_id;
    public $office_ids;
    public $param;
    public $is_delay;

    /**
     * Create a new job instance.
     */
    public function __construct(
        $data_key,
        $param = [],
        $location_id = null,
        $office_ids = null,
        $is_delay = true
    ) {
        $this->data_key   = $data_key;
        $this->location_id = $location_id;
        $this->office_ids  = $office_ids;
        $this->param       = $param;
        $this->is_delay    = $is_delay;
    }

    /**
     * Execute the job.
     */
    public function handle(FieldRouteService $fieldService)
    {
        $params = $this->param;
        $id = $this->office_id ?? null;

        list($keys,$existingMappings) = $this->office_ids ?? getOfficeParams();

        $params['officeIDs']=  $keys;

        Customer::whereIn('office_id', $keys)
        ->select('customer_id', 'contact_id','location_id')
        ->chunk(10, function ($chunk) use($params,$fieldService) {


            $customers = $chunk->pluck('contact_id', 'customer_id')->toArray();
            $locations = $chunk->pluck('location_id', 'customer_id')->toArray();


            $ids = array_keys($customers);

            $params['customerIDs'] = $ids;

            $response = $fieldService->getCustomerSubscriptions($params);


            $data = $response['subscriptions'];

            $groupedData = collect($data)->groupBy('customerID');
            $jobs = [];

            foreach($groupedData as $key => $result)
            {
                $options = $result->pluck('serviceType','serviceID');
                $options = $this->setOptions($options);

                $contact_id = $customers[$key] ?? null;

                $location_id = $locations[$key];

                $field = LocationCustomField::where('location_id',$location_id)->first();

                $jobs[] = new UpdateContactFieldJob($location_id,$contact_id,$options,$field->active_subscriptions,$this->is_delay);

                Bus::batch($jobs)
                ->name('Process Customers Batch')
                ->dispatch();
            }

        });
    }

    public function setOptions($items)
    {
        $keyValueArray = [];
        foreach ($items as $key => $value) {
            if (!is_null($value)) {
                $keyValueArray[] = $key.'_'.$value;
            }
        }

        return $keyValueArray;
    }
}
