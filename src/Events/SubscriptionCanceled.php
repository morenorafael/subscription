<?php

namespace Morenorafael\Subscription\Events;

use Morenorafael\Subscription\Models\PlanSubscription;
use Illuminate\Queue\SerializesModels;

class SubscriptionCanceled
{
    use SerializesModels;

    public $subscription;

    public function __construct(PlanSubscription $subscription)
    {
        $this->subscription = $subscription;
    }
}
