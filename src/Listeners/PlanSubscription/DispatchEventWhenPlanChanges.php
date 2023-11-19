<?php

namespace Morenorafael\Subscription\Listeners\PlanSubscription;

use Morenorafael\Subscription\Events\SubscriptionSaving;
use Morenorafael\Subscription\Events\SubscriptionPlanChanged;

class DispatchEventWhenPlanChanges
{
    public function handle(SubscriptionSaving $event)
    {
        $planId = $event->subscription->getOriginal('plan_id');

        if ($planId && $planId !== $event->subscription->plan_id) {
            event(new SubscriptionPlanChanged($event->subscription));
        }
    }
}
