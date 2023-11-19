<?php

namespace Morenorafael\Subscription;

use Illuminate\Database\Eloquent\Model;
use Morenorafael\Subscription\Contracts\SubscriptionResolverInterface;

class SubscriptionResolver implements SubscriptionResolverInterface
{
    public function resolve(Model $subscribable, $name)
    {
        $subscriptions = $subscribable->subscriptions->sortByDesc(function ($value) {
            return $value->created_at->getTimestamp();
        });

        foreach ($subscriptions as $subscription) {
            if ($subscription->name === $name) {
                return $subscription;
            }
        }
    }
}
