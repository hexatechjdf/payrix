<?php

namespace App\Jobs\Pulling\CustomField;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Jobs\Pulling\CustomField\ManageGenericFlagsJob;
use App\Helper\CRM;

class ManageFiltersJob implements ShouldQueue
{
    use Queueable;

    public $location_id;
    public $office_id;
    public $param;
    public $data_key;
    public $is_delay;

    /**
     * Create a new job instance.
     */
    public function __construct(
        $data_key,
        $param = [],
        $location_id = null,
        $office_id = null,

        $is_delay = true
    ) {
        $this->data_key   = $data_key;
        $this->location_id = $location_id;
        $this->office_id  = $office_id;
        $this->param       = $param;
        $this->is_delay    = $is_delay;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
            $t = getLocationToken('JoqQ51Bl3LEmR42l6LrG',null,1);
        $t = CRM::getContactFields('JoqQ51Bl3LEmR42l6LrG',1,$t);
        dd($t);


        $params = $this->param;
        $id = $this->office_id ?? null;

        list($keys,$existingMappings) = getOfficeParams($this->location_id,$this->office_id);

        $params['officeIDs']=  $keys;

        PullOptionsJob::dispatchSync($this->data_key,$existingMappings,$params,$this->is_delay);
    }

    public function getFieldByDb($locationId, $column_key)
    {
        $l = LocationCustomField::where('location_id' , $locationId)->select($column_key);

        return @$l;
    }

    public function createOrUpdateCustomField($locationId,$token,$key = 'flags')
    {

        $data = getFieldRoutsKeys()[$key];

        $search_key = 'Invoice' ?? $data['search_key'];
        $name = 'Invoice Date' ?? $data['search_name'];
        $column_key = 'generic_flags' ?? $data['column_key'];

        $l = $this->getFieldByDb($locationId, $column_key) ?? CRM::searchLocationCustomFields($locationId, $token,$key);

        if(!$l)
        {

        }






        $customFields = self::getLocationCustomFields($locationId, $token,$key);

         return $customFields;
        $contactFields = defaultContactFields();
        $cacheKey = "contactFields";

        $data = Cache::remember($cacheKey, 3 * 3, function () use ($contactFields, $locationId) {
            $customFields = self::getLocationCustomFields($locationId);
            $dataa = [];
            if(count($customFields) > 0)
            {
                CustomFields::updateOrCreate(['key' => $locationId],[ 'content' => json_encode($customFields)]);
                foreach($customFields as $k => $f)
                {
                    $dataa[$f['fieldKey']] = $f['name'];
                }
            }
            $mergedFields = array_merge($contactFields, $dataa);
            return $mergedFields;
        });
        $array = [];
        if ($is_values) {
            foreach ($data as $key => $field) {
                $keyy = $field && !empty($field) ? $field : $key;
                $array[$keyy] = '{{' . $key . '}}';
            }

            return $array;
        }
        return $data;
    }


}
