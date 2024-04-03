<?php

namespace App\Listeners;

use App\Events\RequestTerminated;
use App\Models\RequestHistory as RequestHistoryModel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class StoreRequest implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(RequestTerminated $event): void
    {
        RequestHistoryModel::create($event->data());
    }
}
