<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Domain\Import\Events\ImportStarted;
use App\Domain\Import\Events\ImportSucceeded;
use App\Domain\Import\Events\ImportFailed;
use App\Domain\Export\Events\ExportStarted;
use App\Domain\Export\Events\ExportCompleted;
use App\Application\Import\Listeners\ImportStartedListener;
use App\Application\Import\Listeners\ImportSucceededListener;
use App\Application\Import\Listeners\ImportFailedListener;
use App\Application\Export\Listeners\ExportStartedListener;
use App\Application\Export\Listeners\ExportCompletedListener;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        // Import Domain Events
        ImportStarted::class => [
            ImportStartedListener::class,
        ],
        ImportSucceeded::class => [
            ImportSucceededListener::class,
        ],
        ImportFailed::class => [
            ImportFailedListener::class,
        ],

        // Export Domain Events
        ExportStarted::class => [
            ExportStartedListener::class,
        ],
        ExportCompleted::class => [
            ExportCompletedListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
