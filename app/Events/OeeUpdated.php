<?php

namespace App\Events;

use App\Models\OeeRecord;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OeeUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $oeeRecord;

    /**
     * Create a new event instance.
     *
     * @param OeeRecord $oeeRecord
     * @return void
     */
    public function __construct(OeeRecord $oeeRecord)
    {
        $this->oeeRecord = $oeeRecord;
    }
}