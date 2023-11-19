<?php

namespace Morenorafael\Subscription\Traits;

use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\App;
use Morenorafael\Subscription\SubscriptionUsageManager;
use Morenorafael\Subscription\Contracts\SubscriptionBuilderInterface;
use Morenorafael\Subscription\Contracts\SubscriptionResolverInterface;
use Morenorafael\Subscription\Models\PlanSubscription;

trait PlanSubscriber
{
    public function subscription(string $name = 'default'): ?PlanSubscription
    {
        return App::make(SubscriptionResolverInterface::class)->resolve($this, $name);
    }

    public function subscriptions(): HasOne
    {
        return $this->morphMany(config('subscription.models.plan_subscription'), 'subscribable');
    }

    public function subscribed(string $subscription = 'default', ?int $planId = null): bool
    {
        $subscription = $this->subscription($subscription);

        if (is_null($subscription)) {
            return false;
        }

        if (is_null($planId)) {
            return $subscription->isActive();
        }

        if ($planId == $subscription->plan_id and $subscription->isActive()) {
            return true;
        }

        return false;
    }

    public function newSubscription(string $subscription, $plan): PlanSubscription
    {
        $container = Container::getInstance();

        if (method_exists($container, 'makeWith')) {
            return $container->makeWith(SubscriptionBuilderInterface::class, [
                'user' => $this, 'name' => $subscription, 'plan' => $plan
            ]);
        }

        return $container->make(SubscriptionBuilderInterface::class, [$this, $subscription, $plan]);
    }

    public function subscriptionUsage(string $subscription = 'default'): SubscriptionUsageManager
    {
        return new SubscriptionUsageManager($this->subscription($subscription));
    }
}
