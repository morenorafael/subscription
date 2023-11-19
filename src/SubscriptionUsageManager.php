<?php

namespace Morenorafael\Subscription;

use Illuminate\Database\Eloquent\Model;
use Morenorafael\Subscription\Models\PlanSubscriptionUsage;

class SubscriptionUsageManager
{
    /**
     * Subscription model instance.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $subscription;

    public function __construct(Model $subscription)
    {
        $this->subscription = $subscription;
    }

    public function record(string $feature, int $uses = 1, bool $incremental = true): PlanSubscriptionUsage
    {
        $feature = new Feature($feature);

        $usage = $this->subscription->usage()->firstOrNew([
            'code' => $feature->getFeatureCode(),
        ]);

        if ($feature->isResettable()) {
            if (is_null($usage->valid_until)) {
                $usage->valid_until = $feature->getResetDate($this->subscription->created_at);
            } elseif ($usage->isExpired() === true) {
                $usage->valid_until = $feature->getResetDate($usage->valid_until);
                $usage->used = 0;
            }
        }

        $usage->used = ($incremental ? $usage->used + $uses : $uses);

        $usage->save();

        return $usage;
    }

    public function reduce(string $feature, int $uses = 1): PlanSubscriptionUsage
    {
        $feature = new Feature($feature);

        $usage = $this->subscription
            ->usage()
            ->byFeatureCode($feature->getFeatureCode())
            ->first();

        if (is_null($usage)) {
            return false;
        }

        $usage->used = max($usage->used - $uses, 0);

        $usage->save();

        return $usage;
    }

    public function clear(): self
    {
        $this->subscription->usage()->delete();

        return $this;
    }
}
