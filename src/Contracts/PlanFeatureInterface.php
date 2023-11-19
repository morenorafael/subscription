<?php

namespace Morenorafael\Subscription\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

interface PlanFeatureInterface
{
    public function plan(): BelongsTo;

    public function usage(): HasMany;
}
