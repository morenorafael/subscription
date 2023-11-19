<?php

namespace Morenorafael\Subscription;

use Morenorafael\Subscription\Feature;

class SubscriptionAbility
{
    /**
     * Subscription model instance.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $subscription;

    /**
     * Create a new Subscription instance.
     *
     * @return void
     */
    public function __construct($subscription)
    {
        $this->subscription = $subscription;
    }

    public function canUse(string $feature): bool
    {
        $feature_value = $this->value($feature);

        if (is_null($feature_value)) {
            return false;
        }

        if ($this->enabled($feature) === true) {
            return true;
        }

        if ($feature_value === '0') {
            return false;
        }

        return $this->remainings($feature) > 0;
    }

    public function consumed(string $feature): int
    {
        foreach ($this->subscription->usage as $usage) {
            if ($usage->code === $feature and $usage->isExpired() == false) {
                return $usage->used;
            }
        }

        return 0;
    }

    public function remainings(string $feature): int
    {
        return ((int) $this->value($feature) - (int) $this->consumed($feature));
    }

    public function enabled(string $feature): bool
    {
        $feature_value = $this->value($feature);

        if (is_null($feature_value)) {
            return false;
        }

        if (in_array(strtoupper($feature_value), config('subscription.positive_words'))) {
            return true;
        }

        return false;
    }

    /**
     * Get feature value.
     *
     * @param  string $feature
     * @param  mixed $default
     * @return mixed
     */
    public function value(string $feature, $default = null)
    {
        foreach ($this->subscription->plan->features as $key => $value) {
            if ($feature === $value->code) {
                return $value->value;
            }
        }

        return $default;
    }
}
