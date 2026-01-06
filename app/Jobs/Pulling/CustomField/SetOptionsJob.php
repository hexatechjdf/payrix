<?php

namespace App\Jobs\Pulling\CustomField;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Helper\CRM;
use App\Models\LocationCustomField;

class SetOptionsJob implements ShouldQueue
{
    use Queueable;

    public $is_delay;
    public $data_key;
    public $location_id;
    public $options;

    public function __construct($data_key, $options,$location_id, $is_delay = true)
    {
        $this->data_key        = $data_key;
        $this->location_id     = $location_id;
        $this->options = $options;
        $this->is_delay        = $is_delay;
    }


    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $values = $this->options;
        $payload_options = [];
        $payload_values = [];

        foreach($values as $v)
        {
           $op = $v['typeID'].'_'.$v['description'];
           $payload_options[] = $op;
           $payload_values[] = ['value' => $op];
        }

        $token = getLocationToken($this->location_id,null,1);

        // CRM::getLocationCustomFields($this->location_id, $token);
        // DD(123);
        $this->createOrUpdateCustomField($this->location_id,$token,$payload_options,$this->data_key);

    }

    public function getFieldByDb($locationId, $column_key)
    {
        $l = LocationCustomField::where('location_id' , $locationId)->select($column_key,$column_key.'_options')->first();

        return @$l;
    }

    public function createOrUpdateCustomField($locationId,$token,$payload_values,$key = 'flags')
    {

        $data = getFieldRoutsKeys()[$key];

        $search_key = $data['search_key'];
        $name = $data['search_name'];
        $column_key = $data['column_key'];

        $l = $this->getFieldByDb($locationId, $column_key) ?? CRM::searchLocationCustomFields($locationId, $token,$search_key,$column_key,$name);

        if(!$l)
        {
            $payload = [
                'name' => $name,
                'dataType' => 'MULTIPLE_OPTIONS',
                'documentType' => 'field',
                'placeholder' => 'Select options',
                'model' => 'contact',
                'options' => $payload_values
            ];

            $d = CRM::manageCutomField($payload, $locationId,$token,'POST', "locations/$locationId/customFields",$column_key);

        }
        else{
            $arrayOne = @$l->{$column_key.'_options'} ?? [];
            $arrayTwo = $payload_values ?? [];

            $updatedValues = [];
            $finalArrayOne = [];
            $hasNewValues = false;

            foreach ($arrayOne as $item) {
                [$id, $label] = explode('_', $item, 2);
                $oldMap[$id] = $label;
            }

            foreach ($arrayTwo as $item) {
                [$id, $label] = explode('_', $item, 2);

                if (isset($oldMap[$id])) {

                    if ($oldMap[$id] !== $label) {
                        $updatedValues[] = [
                            'id' => $id,
                            'old' => $oldMap[$id],
                            'new' => $label,
                        ];
                    }

                    $finalArrayOne[] = $id . '_' . $label;

                } else {
                    $hasNewValues = true;
                    $finalArrayOne[] = $id . '_' . $label;
                }
            }

            if (!empty($updatedValues) || $hasNewValues) {

                $payload = [
                    'options' => $finalArrayOne
                ];
                $id = $l->$column_key;
                $d = CRM::manageCutomField($payload, $locationId,$token,'PUT', "locations/$locationId/customFields/$id",$column_key);
            }

        }
    }

}
