<?php

namespace Morenorafael\Subscription\Contracts;

use Morenorafael\Subscription\Models\PlanSubscription;

interface SubscriptionBuilderInterface
{
    public function trialDays(int $trialDays): self;

    public function skipTrial(): self;

    public function create(array $attributes = []): PlanSubscription;
}
