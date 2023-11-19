<?php

namespace Morenorafael\Subscription\Exceptions;

class InvalidIntervalException extends \Exception
{
    public function __construct($interval)
    {
        $this->message = "Invalid interval \"{$interval}\".";
    }
}
