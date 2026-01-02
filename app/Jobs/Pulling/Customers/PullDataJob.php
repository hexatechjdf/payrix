<?php

namespace App\Jobs\Pulling\Customers;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Services\FieldRouteService;
use Illuminate\Support\Facades\Bus;
use Illuminate\Bus\Batch;
use App\Jobs\Pulling\Customers\ProcessChunkJob;


class PullDataJob implements ShouldQueue
{
    use Queueable;

    public $last_id;
    public $fetched_count;
    public $location_id;
    public $office_id;
    public $param;
    public $is_delay;

    /**
     * Create a new job instance.
     */
    public function __construct(
        $last_id = null,
        $fetched_count = 0,
        $param = [],
        $location_id = null,
        $office_id = null,
        $is_delay = true
    ) {
        $this->last_id   = $last_id;
        $this->fetched_count   = $fetched_count;
        $this->location_id = $location_id;
        $this->office_id  = $office_id;
        $this->param       = $param;
        $this->is_delay    = $is_delay;
    }

    /**
     * Execute the job.
    */
    public function handle(FieldRouteService $fieldService)
    {
        $totalFetched = $this->fetched_count;

        $params = $this->param;
        $id = $this->office_id ?? null;

        list($keys,$existingMappings) = getOfficeParams($this->location_id,$this->office_id);

        $params['officeIDs']=  $keys;

        $lastId = $this->last_id;

        if ($lastId) {
                $params['customerIDs'] = [
                    'operator' => '>',
                    'value'    => [$lastId]
                ];
            }

           $response = $fieldService->getCustomers($params);

           dd($response);

            $count = $response['count'] ?? 0;
            $data = $response['customers'] ?? [];


            if (empty($data)) {
                return;
            }

            $totalFetched += count($data);

            $jobs = collect($data)
                ->chunk(100)
                ->map(function ($chunk) {
                    return new ProcessChunkJob($chunk->toArray());
                })
                ->toArray();

            Bus::batch($jobs)
                ->name('Process Customers Batch')
                ->dispatch();

            $last   = end($data);
            $lastId = $last['customerID'] ?? null;

            if ($totalFetched < $count && $lastId) {
                self::dispatch(
                    $lastId,
                    $totalFetched,
                    $this->param,
                    $this->location_id,
                    $this->office_id,
                    $this->is_delay
                )->delay(
                    now()->addSeconds(2)
                );
            }


    }
}
