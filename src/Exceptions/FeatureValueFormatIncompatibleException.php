<?php

namespace Morenorafael\Subscription\Exceptions;

class FeatureValueFormatIncompatibleException extends \Exception
{
    public function __construct($value)
    {
        $this->message = "Feature value format is incompatible: {$value}.";
    }
}
