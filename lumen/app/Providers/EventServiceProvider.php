<?php

namespace App\Providers;

use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

/**
 * @codeCoverageIgnore
 */
class EventServiceProvider extends ServiceProvider
{
    /** {@inheritdoc} */
    protected $listen = [
        /* \App\Events\ExampleEvent::class => [
            \App\Listeners\ExampleListener::class,
        ], */
    ];

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
