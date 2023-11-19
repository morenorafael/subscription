<?php

namespace Morenorafael\Subscription\Models;

use Carbon\Carbon;
use LogicException;
use Morenorafael\Subscription\Period;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Morenorafael\Subscription\Models\PlanFeature;
use Morenorafael\Subscription\SubscriptionAbility;
use Morenorafael\Subscription\Traits\BelongsToPlan;
use Morenorafael\Subscription\Contracts\PlanInterface;
use Morenorafael\Subscription\Events\SubscriptionSaved;
use Morenorafael\Subscription\SubscriptionUsageManager;
use Morenorafael\Subscription\Events\SubscriptionSaving;
use Morenorafael\Subscription\Events\SubscriptionCreated;
use Morenorafael\Subscription\Events\SubscriptionRenewed;
use Morenorafael\Subscription\Events\SubscriptionCanceled;
use Morenorafael\Subscription\Events\SubscriptionPlanChanged;
use Morenorafael\Subscription\Contracts\PlanSubscriptionInterface;
use Morenorafael\Subscription\Exceptions\InvalidPlanFeatureException;
use Morenorafael\Subscription\Exceptions\FeatureValueFormatIncompatibleException;

class PlanSubscription extends Model implements PlanSubscriptionInterface
{
    use BelongsToPlan;

    /**
     * Subscription statuses
     */
    const STATUS_ENDED      = 'ended';
    const STATUS_ACTIVE     = 'active';
    const STATUS_CANCELED   = 'canceled';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'plan_id',
        'name',
        'trial_ends_at',
        'starts_at',
        'ends_at',
        'canceled_at'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at',
        'canceled_at', 'trial_ends_at', 'ends_at', 'starts_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'canceled_immediately' => 'boolean',
    ];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => SubscriptionCreated::class,
        'saving' => SubscriptionSaving::class,
        'saved' => SubscriptionSaved::class,
    ];

    /**
     * Subscription Ability Manager instance.
     *
     * @var Morenorafael\Subscription\SubscriptionAbility
     */
    protected $ability;

    public function subscribable(): MorphTo
    {
        return $this->morphTo();
    }

    public function usage(): HasMany
    {
        return $this->hasMany(
            config('subscription.models.plan_subscription_usage'),
            'subscription_id'
        );
    }

    public function getStatusAttribute(): string
    {
        if ($this->isActive()) {
            return self::STATUS_ACTIVE;
        }

        if ($this->isCanceled()) {
            return self::STATUS_CANCELED;
        }

        if ($this->isEnded()) {
            return self::STATUS_ENDED;
        }
    }

    public function isActive(): bool
    {
        if ((!$this->isEnded() or $this->onTrial()) and !$this->isCanceledImmediately()) {
            return true;
        }

        return false;
    }

    public function onTrial(): bool
    {
        if (!is_null($trialEndsAt = $this->trial_ends_at)) {
            return Carbon::now()->lt(Carbon::instance($trialEndsAt));
        }

        return false;
    }

    public function isCanceled(): bool
    {
        return  !is_null($this->canceled_at);
    }

    public function isCanceledImmediately()
    {
        return (!is_null($this->canceled_at) and $this->canceled_immediately === true);
    }

    public function isEnded(): bool
    {
        $endsAt = Carbon::instance($this->ends_at);

        return Carbon::now()->gte($endsAt);
    }

    public function cancel(bool $immediately = false): self
    {
        $this->canceled_at = Carbon::now();

        if ($immediately) {
            $this->canceled_immediately = true;
        }

        if ($this->save()) {
            event(new SubscriptionCanceled($this));

            return $this;
        }

        return false;
    }

    public function changePlan($plan): self
    {
        if (is_numeric($plan)) {
            $plan = App::make(PlanInterface::class)->find($plan);
        }

        if (
            is_null($this->plan) or $this->plan->interval !== $plan->interval or
            $this->plan->interval_count !== $plan->interval_count
        ) {
            $this->setNewPeriod($plan->interval, $plan->interval_count);

            $usageManager = new SubscriptionUsageManager($this);
            $usageManager->clear();
        }

        $this->plan_id = $plan->id;

        return $this;
    }

    public function renew(): self
    {
        if ($this->isEnded() and $this->isCanceled()) {
            throw new LogicException(
                'Unable to renew canceled ended subscription.'
            );
        }

        $subscription = $this;

        DB::transaction(function () use ($subscription) {
            $usageManager = new SubscriptionUsageManager($subscription);
            $usageManager->clear();

            $subscription->setNewPeriod();
            $subscription->canceled_at = null;
            $subscription->save();
        });

        event(new SubscriptionRenewed($this));

        return $this;
    }

    public function ability()
    {
        if (is_null($this->ability)) {
            return new SubscriptionAbility($this);
        }

        return $this->ability;
    }

    public function scopeByUser($query, $subscribable)
    {
        return $query->where('subscribable_id', $subscribable);
    }

    public function scopeFindEndingTrial($query, $dayRange = 3)
    {
        $from = Carbon::now();
        $to = Carbon::now()->addDays($dayRange);

        $query->whereBetween('trial_ends_at', [$from, $to]);
    }

    public function scopeFindEndedTrial($query)
    {
        $query->where('trial_ends_at', '<=', date('Y-m-d H:i:s'));
    }

    public function scopeFindEndingPeriod($query, $dayRange = 3)
    {
        $from = Carbon::now();
        $to = Carbon::now()->addDays($dayRange);

        $query->whereBetween('ends_at', [$from, $to]);
    }

    public function scopeExcludeCanceled($query)
    {
        return $query->whereNull('canceled_at');
    }

    public function scopeExcludeImmediatelyCanceled($query)
    {
        return $query->whereNull('canceled_immediately')
            ->orWhere('canceled_immediately', 0);
    }

    public function scopeFindEndedPeriod($query)
    {
        $query->where('ends_at', '<=', date('Y-m-d H:i:s'));
    }

    public function setNewPeriod($interval = '', $interval_count = '', $start = '')
    {
        if (empty($interval)) {
            $interval = $this->plan->interval;
        }

        if (empty($interval_count)) {
            $interval_count = $this->plan->interval_count;
        }

        $period = new Period($interval, $interval_count, $start);

        $this->starts_at = $period->getStartDate();
        $this->ends_at = $period->getEndDate();

        return $this;
    }
}
