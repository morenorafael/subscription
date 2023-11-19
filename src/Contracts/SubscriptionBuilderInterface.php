<?php

namespace Morenorafael\Subscription\Contracts;

interface SubscriptionBuilderInterface
{
    public function trialDays($trialDays);

    public function skipTrial();

    public function create(array $attributes = []);
}
