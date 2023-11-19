<?php

namespace Morenorafael\Subscription\Contracts;

interface PlanInterface
{
    public function features();

    public function subscriptions();

    public function isFree();

    public function hasTrial();
}
