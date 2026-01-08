<?php

namespace App\Jobs\Pulling\Subscriptions;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Bus;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Batchable;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Helper\CRM;


class CreateFieldJob implements ShouldQueue
{
    use Queueable, Batchable;

    public $data;

    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $payload = $this->data['payload'];
        $location_id = $this->data['location_id'];
        $token = $this->data['token'];
        $url = $this->data['url'];

        try{
            CRM::manageCutomField($payload,$location_id,$token,'POST',$url,'none');
        }catch(\Exception $e){
              \Log::info($e);
        }

    }
}
