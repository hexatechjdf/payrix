<?php

namespace App\Jobs\Pulling\Customers;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Jobs\Pulling\Customers\ManageContactJob;
use Illuminate\Support\Facades\Bus;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Batchable;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessChunkJob implements ShouldQueue
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

    public function handle(): void
    {
        $records = $this->data;

        $jobs = [];

        foreach ($records as $record) {
            $jobs[] = new ManageContactJob(
                $record,
                $this->location_id,
                $this->is_delay
            );
        }

        if (!empty($jobs)) {
            Bus::batch($jobs)
                ->name('Process Customers Batch')
                ->dispatch();
        }

        // $customerIds = $records->pluck('customerID');
    }


}
