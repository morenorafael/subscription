<?php

namespace Morenorafael\Subscription\Contracts;

use Illuminate\Database\Eloquent\Model;

interface SubscriptionResolverInterface
{
    public function resolve(Model $subscribable, $name);
}
