<?php

namespace App\Jobs\Pulling\CustomField;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Jobs\Pulling\CustomField\ManageGenericFlagsJob;

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
        $params = $this->param;
        $id = $this->office_id ?? null;

        list($keys,$existingMappings) = getOfficeParams($this->location_id,$this->office_id);

        $params['officeIDs']=  $keys;

        PullOptionsJob::dispatchSync($this->data_key,$existingMappings,$params,$this->is_delay);
    }
}
