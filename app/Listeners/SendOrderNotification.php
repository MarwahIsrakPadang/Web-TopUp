<?php

namespace App\Listeners;

use App\Events\OrderStatusUpdated;
use App\Notifications\OrderStatusNotification;

class SendOrderNotification
{
    public function handle(OrderStatusUpdated $event): void
    {
        $order = $event->order;

        if ($order->user_id) {
            $order->user->notify(new OrderStatusNotification(
                $order,
                $event->oldStatus,
                $event->newStatus,
            ));
        }
    }
}
