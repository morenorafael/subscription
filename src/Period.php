<?php

namespace Morenorafael\Subscription;

use Carbon\Carbon;
use Morenorafael\Subscription\Exceptions\InvalidIntervalException;

class Period
{
    /**
     * The interval constants.
     */
    const DAY = 'day';
    const WEEK = 'week';
    const MONTH = 'month';
    const YEAR = 'year';

    /**
     * Map Interval to Carbon methods.
     *
     * @var array
     */
    protected static $intervalMapping = [
        self::DAY => 'addDays',
        self::WEEK => 'addWeeks',
        self::MONTH => 'addMonths',
        self::YEAR => 'addYears',
    ];

    /**
     * Starting date of the period.
     *
     * @var Carbon
     */
    protected $start;

    /**
     * Ending date of the period.
     *
     * @var Carbon
     */
    protected $end;

    /**
     * Interval
     *
     * @var string
     */
    protected $interval;

    /**
     * Interval count
     *
     * @var int
     */
    protected $interval_count = 1;

    /**
     * Create a new Period instance.
     *
     * @param  string $interval Interval
     * @param  int $count Interval count
     * @param  string $start Starting point
     * @throws  \Morenorafael\Subscription\Exceptions\InvalidIntervalException
     * @return  void
     */
    public function __construct($interval = 'month', $count = 1, $start = '')
    {
        if (empty($start)) {
            $this->start = new Carbon;
        } elseif (!$start instanceof Carbon) {
            $this->start = new Carbon($start);
        } else {
            $this->start = $start;
        }

        if (!$this::isValidInterval($interval)) {
            throw new InvalidIntervalException($interval);
        }

        $this->interval = $interval;

        if ($count > 0) {
            $this->interval_count = $count;
        }

        $this->calculate();
    }

    public static function getAllIntervals(): array
    {
        $intervals = [];

        foreach (array_keys(self::$intervalMapping) as $interval) {
            $intervals[$interval] = trans('laraplans::messages.' . $interval);
        }

        return $intervals;
    }

    public function getStartDate(): Carbon
    {
        return $this->start;
    }

    public function getEndDate(): Carbon
    {
        return $this->end;
    }

    public function getInterval(): string
    {
        return $this->interval;
    }

    public function getIntervalCount(): int
    {
        return $this->interval_count;
    }

    public static function isValidInterval(string $interval): bool
    {
        return array_key_exists($interval, self::$intervalMapping);
    }

    protected function calculate(): void
    {
        $method = $this->getMethod();
        $start = clone ($this->start);
        $this->end = $start->$method($this->interval_count);
    }

    protected function getMethod(): string
    {
        return self::$intervalMapping[$this->interval];
    }
}
