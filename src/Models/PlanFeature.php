<?php

namespace Morenorafael\Subscription\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Morenorafael\Subscription\Traits\BelongsToPlan;
use Morenorafael\Subscription\Contracts\PlanFeatureInterface;

class PlanFeature extends Model implements PlanFeatureInterface
{
    use BelongsToPlan;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'plan_id',
        'code',
        'value',
        'sort_order'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at'
    ];

    public function usage(): HasMany
    {
        return $this->hasMany(config('subscription.models.plan_subscription_usage'));
    }
}
