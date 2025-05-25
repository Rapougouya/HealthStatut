<?php

namespace App\Observers;

use App\Models\Alerte;

class AlertObserver
{
    /**
     * Handle the Alerte "created" event.
     */
    public function created(Alerte $alerte): void
    {
        //
    }

    /**
     * Handle the Alerte "updated" event.
     */
    public function updated(Alerte $alerte): void
    {
        //
    }

    /**
     * Handle the Alerte "deleted" event.
     */
    public function deleted(Alerte $alerte): void
    {
        //
    }

    /**
     * Handle the Alerte "restored" event.
     */
    public function restored(Alerte $alerte): void
    {
        //
    }

    /**
     * Handle the Alerte "force deleted" event.
     */
    public function forceDeleted(Alerte $alerte): void
    {
        //
    }
}
