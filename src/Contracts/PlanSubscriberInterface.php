<?php

namespace Morenorafael\Subscription\Contracts;

interface PlanSubscriberInterface
{
    public function subscription($name = 'default');

    public function subscriptions();

    public function subscribed($subscription = 'default');

    public function newSubscription($name, $plan);

    public function subscriptionUsage($subscription = 'default');
}
