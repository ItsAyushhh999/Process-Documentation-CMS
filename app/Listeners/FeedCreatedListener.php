<?php

namespace App\Listeners;

use App\Events\GenerateFeedEvent;

class FeedCreatedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param  GenerateFeedEvent  $event
     * @return void
     */
    public function handle(GenerateFeedEvent $event)
    {

    }
}
