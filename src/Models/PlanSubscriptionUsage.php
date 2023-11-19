<?php

namespace Morenorafael\Subscription\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Morenorafael\Subscription\Contracts\PlanSubscriptionUsageInterface;

class PlanSubscriptionUsage extends Model implements PlanSubscriptionUsageInterface
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subscription_id',
        'code',
        'valid_until',
        'used'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'valid_until',
    ];

    public function feature(): BelongsTo
    {
        return $this->belongsTo(config('subscription.models.plan_feature'));
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(config('subscription.models.plan_subscription'));
    }

    public function scopeByFeatureCode($query, $featureCode): Builder
    {
        return $query->whereCode($featureCode);
    }

    public function isExpired(): bool
    {
        if (is_null($this->valid_until)) {
            return false;
        }

        return Carbon::now()->gte($this->valid_until);
    }
}
