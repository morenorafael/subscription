<?php

namespace Morenorafael\Subscription\Exceptions;

class InvalidPlanFeatureException extends \Exception
{
    public function __construct($feature)
    {
        $this->message = "Invalid plan feature: {$feature}";
    }
}
