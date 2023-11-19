<?php

namespace Morenorafael\Subscription\Contracts;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Morenorafael\Subscription\Models\PlanSubscription;
use Morenorafael\Subscription\SubscriptionUsageManager;

interface PlanSubscriberInterface
{
    public function subscription(string $name = 'default'): ?PlanSubscription;

    public function subscriptions(): HasOne;

    public function subscribed(string $subscription = 'default', ?int $planId = null): bool;

    public function newSubscription(string $subscription, $plan): PlanSubscription;

    public function subscriptionUsage(string $subscription = 'default'): SubscriptionUsageManager;
}
