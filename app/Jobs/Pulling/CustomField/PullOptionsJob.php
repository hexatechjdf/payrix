<?php

namespace App\Jobs\Pulling\CustomField;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Services\FieldRouteService;
use App\Jobs\Pulling\CustomField\SetOptionsJob;

class PullOptionsJob implements ShouldQueue
{
    use Queueable;

    public $param;
    public $is_delay;
    public $data_key;
    public $existing_record;

    public function __construct($data_key, $existing_record, $param = [], $is_delay = true)
    {
        $this->data_key        = $data_key;
        $this->existing_record = $existing_record;
        $this->param           = $param;
        $this->is_delay        = $is_delay;
    }

    /**
     * Execute the job.
     */
    public function handle(FieldRouteService $fieldService)
    {
        $existingMappings = $this->existing_record;
        $allData      = [];
        $totalFetched  = 0;
        $lastId    = 493 ?? null;

        $params = $this->param;

        $fieldKeys = getFieldRoutsKeys()[$this->data_key];

        $id_key = $fieldKeys['id'];
        $search_key = $fieldKeys['search_id'];
        $data_key = $fieldKeys['data'];
        $groupby_key = $fieldKeys['group_by'];
        $method       = $fieldKeys['function'];
        $value       = $fieldKeys['value'];

        do {
            if ($lastId) {
                $params[$search_key] = [
                    'operator' => '>',
                    'value'    => [$lastId]
                ];
            }

           $response = $fieldService->{$method}($params);

            if (empty($response)) {
                break;
            }

            $count = $response['count'] ?? 0;
            $data = $response[$data_key] ?? [];


            if (empty($data)) {
                break;
            }

            $allData = array_merge($allData, $data);

            $totalFetched += count($data);

            $last   = end($data);
            $lastId = $last[$id_key] ?? null;

        } while ($totalFetched < $count);

        $groupedByOffice = collect($allData)->groupBy($groupby_key);
        foreach($existingMappings as $key => $loc)
        {
            $res = $groupedByOffice[$key];
            SetOptionsJob::dispatchSync($this->data_key,$res,$loc,$this->is_delay);
        }

        dd($groupedByOffice);
    }
}
