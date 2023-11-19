<?php

namespace Morenorafael\Subscription\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToPlan
{
    public function plan(): BelongsTo
    {
        return $this->belongsTo(config('subscription.models.plan'));
    }

    /**
     * Scope by plan id.
     *
     * @param  \Illuminate\Database\Eloquent\Builder
     * @param  int $plan_id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByPlan($query, $plan_id)
    {
        return $query->where('plan_id', $plan_id);
    }
}
