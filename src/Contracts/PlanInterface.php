<?php

namespace Morenorafael\Subscription\Contracts;

use Illuminate\Database\Eloquent\Relations\HasMany;

interface PlanInterface
{
    public function features(): HasMany;

    public function subscriptions(): HasMany;

    public function isFree(): bool;

    public function hasTrial(): bool;
}
