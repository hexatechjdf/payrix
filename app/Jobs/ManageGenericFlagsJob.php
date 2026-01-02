<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Services\FieldRouteService;
use App\Models\OfficeMapping;

class ManageGenericFlagsJob implements ShouldQueue
{
    use Queueable;

    public $location_id;
    public $office_ids;

    /**
     * Create a new job instance.
     */
    public function __construct($location_id = null, $office_ids = [])
    {
       $location_id = $this->location_id;
       $office_ids = $this->office_ids;
    }

    /**
     * Execute the job.
     */
    public function handle(FieldRouteService $fieldService)
    {
        $existingMappings = [];
        $id = $this->office_id ?? null;
        if(!$this->location_id)
        {
            $existingMappings = OfficeMapping::pluck('location_id','office_id')->toArray();
            $keys = array_keys($existingMappings);

        }else{
            $existingMappings[$id] = $this->location_id;
        }


        $params = ['officeIDs' => $keys];

        // dd($params);


        $allFlags      = [];
        $totalFetched  = 0;
        $lastFlagId    = null;


        do {
            if ($lastFlagId) {
                $params['genericFlagID'] = [
                    'operator' => '>',
                    'value'    => $lastFlagId
                ];
            }

            $response = $fieldService->getFlags($params);

            if (empty($response)) {
                break;
            }

            $count = $response['count'] ?? 0;
            $flags = $response['genericFlags'] ?? [];

            if (empty($flags)) {
                break;
            }

            $allFlags = array_merge($allFlags, $flags);

            $totalFetched += count($flags);

            $lastFlag   = end($flags);
            $lastFlagId = $lastFlag['genericFlagID'] ?? null;

        } while ($totalFetched < $count);

        $groupedByOffice = collect($allFlags)->groupBy('officeID');

        foreach($existingMappings as $key => $loc)
        {
            $res = $groupedByOffice[$key];

            dd($res);
        }

        dd($groupedByOffice);




    }



    // do {
    //         if ($lastFlagId) {
    //             $params['genericFlagID'] = [
    //                 'operator' => '>',
    //                 'value'    => $lastFlagId
    //             ];
    //         }

    //         $response = $fieldService->getFlags($params);

    //         if (empty($response)) {
    //             break;
    //         }

    //         $count = $response['count'] ?? 0;
    //         $flags = $response['genericFlags'] ?? [];

    //         if (empty($flags)) {
    //             break;
    //         }

    //         // total fetched counter
    //         $totalFetched += count($flags);

    //         // collect($flags)
    //         //     ->chunk(100)
    //         //     ->each(function ($chunk) {
    //         //         ProcessFlagsBatchJob::dispatch($chunk->toArray());
    //         //     });

    //         $lastFlag     = end($flags);
    //         $lastFlagId   = $lastFlag['genericFlagID'] ?? null;

    //     } while ($totalFetched < $count);
}
