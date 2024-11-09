<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\NewProductMessage;
use App\Models\User;
use App\Notifications\NewProductMessageNotification;

class SendNewProductMessageNotification
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
    public function handle(NewProductMessage $event)
    {
        $receiver = User::find($event->message->receiver_id);
        if ($receiver) {
            $receiver->notify(new NewProductMessageNotification($event->message));
        }
    }
}
