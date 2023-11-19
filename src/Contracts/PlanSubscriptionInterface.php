<?php

namespace Morenorafael\Subscription\Contracts;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

interface PlanSubscriptionInterface
{
    public function subscribable(): MorphTo;

    public function plan();

    public function usage(): HasMany;

    public function getStatusAttribute(): string;

    public function isActive(): bool;

    public function onTrial(): bool;

    public function isCanceled(): bool;

    public function isEnded(): bool;

    public function renew(): self;

    public function cancel(bool $immediately = false);

    public function changePlan($plan): self;
}
