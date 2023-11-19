<?php

namespace Morenorafael\Subscription\Listeners\PlanSubscription;

use Morenorafael\Subscription\Events\SubscriptionSaving;

class SetPeriodWhenEmpty
{
    public function handle(SubscriptionSaving $event)
    {
        if (!$event->subscription->ends_at) {
            $event->subscription->setNewPeriod();
        }
    }
}
