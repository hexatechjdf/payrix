<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocationCustomField extends Model
{
     protected $guarded = [];

     protected $casts = [
        'active_subscriptions_options' => 'array',
        'generic_flags_options' => 'array',
    ];
}
