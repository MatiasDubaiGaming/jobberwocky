<?php

namespace App\Listeners;

use App\Events\JobCreated;
use App\Mail\JobCreatedMail;
use App\Models\Subscription;
use Illuminate\Support\Facades\Mail;

class SendJobCreatedNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(JobCreated $event): void
    {
        $subscribers = Subscription::all();
        foreach ($subscribers as $subscriber) {
            Mail::to($subscriber->email)->send(new JobCreatedMail($event->jobListing));
        }
    }
}
