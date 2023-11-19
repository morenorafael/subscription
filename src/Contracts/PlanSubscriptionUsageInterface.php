<?php

namespace Morenorafael\Subscription\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface PlanSubscriptionUsageInterface
{
    public function feature(): BelongsTo;

    public function subscription(): BelongsTo;

    public function scopeByFeatureCode($query, $featureCode): Builder;

    public function isExpired(): bool;
}
