<?php

namespace Morenorafael\Subscription;

use Carbon\Carbon;
use Morenorafael\Subscription\Period;
use Morenorafael\Subscription\Exceptions\InvalidPlanFeatureException;

class Feature
{
    /**
     * Feature code.
     *
     * @var string
     */
    protected $code;

    /**
     * Feature resettable interval.
     *
     * @var string
     */
    protected $resettableInterval;

    /**
     * Feature resettable count.
     *
     * @var int
     */
    protected $resettableCount;

    public function __construct(string $code)
    {
        if (!self::isValid($code)) {
            throw new InvalidPlanFeatureException($code);
        }

        $this->code = $code;

        $feature = config('subscription.features.' . $code);

        if (is_array($feature)) {
            foreach ($feature as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->$key = $value;
                }
            }
        }
    }

    public static function getAllFeatures(): array
    {
        $features = config('subscription.features');

        if (!$features) {
            return [];
        }

        $codes = [];

        foreach ($features as $key => $value) {
            if (is_string($value)) {
                $codes[] = $value;
            } else {
                $codes[] = $key;
            }
        }

        return $codes;
    }

    public static function isValid(string $code): bool
    {
        $features = config('subscription.features');

        if (array_key_exists($code, $features)) {
            return true;
        }

        if (in_array($code, $features)) {
            return true;
        }

        return false;
    }

    public function getFeatureCode(): string
    {
        return $this->code;
    }

    public function getResettableInterval(): ?string
    {
        return $this->resettableInterval;
    }

    public function getResettableCount(): ?int
    {
        return $this->resettableCount;
    }

    public function setResettableInterval(string $interval): void
    {
        $this->resettableInterval = $interval;
    }

    public function setResettableCount(int $count): void
    {
        $this->resettableCount = $count;
    }

    public function isResettable(): bool
    {
        return is_string($this->resettableInterval);
    }

    public function getResetDate(string $dateFrom = ''): Carbon
    {
        if (empty($dateFrom)) {
            $dateFrom = new Carbon;
        }

        $period = new Period($this->resettableInterval, $this->resettableCount, $dateFrom);

        return $period->getEndDate();
    }
}
