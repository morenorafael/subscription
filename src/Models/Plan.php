<?php

namespace Morenorafael\Subscription\Models;

use Morenorafael\Subscription\Period;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Morenorafael\Subscription\Contracts\PlanInterface;
use Morenorafael\Subscription\Exceptions\InvalidPlanFeatureException;

class Plan extends Model implements PlanInterface
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'interval',
        'interval_count',
        'trial_period_days',
        'sort_order',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at'
    ];

    /**
     * Boot function for using with User Events.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if (!$model->interval) {
                $model->interval = 'month';
            }

            if (!$model->interval_count) {
                $model->interval_count = 1;
            }
        });
    }

    public function features(): HasMany
    {
        return $this->hasMany(config('subscription.models.plan_feature'));
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(config('subscription.models.plan_subscription'));
    }

    /**
     * Get Interval Name
     *
     * @return mixed string|null
     */
    public function getIntervalNameAttribute()
    {
        $intervals = Period::getAllIntervals();
        return (isset($intervals[$this->interval]) ? $intervals[$this->interval] : null);
    }

    /**
     * Get Interval Description
     *
     * @return string
     */
    public function getIntervalDescriptionAttribute()
    {
        return trans_choice('laraplans::messages.interval_description.' . $this->interval, $this->interval_count);
    }

    public function isFree(): bool
    {
        return ((float) $this->price <= 0.00);
    }

    public function hasTrial(): bool
    {
        return (is_numeric($this->trial_period_days) and $this->trial_period_days > 0);
    }

    /**
     * Returns the demanded feature
     *
     * @param String $code
     * @return PlanFeature
     * @throws InvalidPlanFeatureException
     */
    public function getFeatureByCode($code)
    {
        $feature = $this->features()->getEager()->first(function ($item) use ($code) {
            return $item->code === $code;
        });

        if (is_null($feature)) {
            throw new InvalidPlanFeatureException($code);
        }

        return $feature;
    }
}
