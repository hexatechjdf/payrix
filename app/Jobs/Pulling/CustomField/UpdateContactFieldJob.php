<?php

namespace App\Jobs\Pulling\CustomField;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Bus\Batchable;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Helper\CRM;

class UpdateContactFieldJob implements ShouldQueue
{
    use Queueable, Batchable;

    public $locationId;
    public $contactId;
    public $options;
    public $isDelay;
    public $key_id;

    /**
     * Create a new job instance.
     */
    public function __construct( $locationId,  $contactId,  $options,$key_id,  $isDelay = false, )
    {
        $this->locationId = $locationId;
        $this->contactId  = $contactId;
        $this->options    = $options;
        $this->isDelay    = $isDelay;
        $this->key_id     = $key_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $token = getLocationToken($this->locationId,null,1);

        $customFields[] = [
            'id' => $this->key_id,
            'field_value' => $this->options,
        ];

        $payload = [
            'customFields' => $customFields,
        ];

        CRM::updateContact($this->contactId, $payload, $this->locationId, $token);
    }
}
