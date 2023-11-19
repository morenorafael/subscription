<?php

namespace Morenorafael\Subscription\Contracts;

interface PlanSubscriptionUsageInterface
{
    public function feature();

    public function subscription();

    public function scopeByFeatureCode($query, $feature_code);

    public function isExpired();
}
