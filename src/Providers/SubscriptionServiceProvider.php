<?php

namespace Morenorafael\Subscription\Providers;

use Illuminate\Support\ServiceProvider;
use Morenorafael\Subscription\Contracts\PlanInterface;
use Morenorafael\Subscription\Contracts\PlanFeatureInterface;
use Morenorafael\Subscription\Contracts\PlanSubscriptionInterface;
use Morenorafael\Subscription\Contracts\SubscriptionBuilderInterface;
use Morenorafael\Subscription\Contracts\SubscriptionResolverInterface;
use Morenorafael\Subscription\Contracts\PlanSubscriptionUsageInterface;
use Illuminate\Support\Facades\Event;

class SubscriptionServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/subscription.php', 'subscription');

        $this->app->bind(PlanInterface::class, config('subscription.models.plan'));
        $this->app->bind(PlanFeatureInterface::class, config('subscription.models.plan_feature'));
        $this->app->bind(PlanSubscriptionInterface::class, config('subscription.models.plan_subscription'));
        $this->app->bind(PlanSubscriptionUsageInterface::class, config('subscription.models.plan_subscription_usage'));
        $this->app->bind(SubscriptionBuilderInterface::class, SubscriptionBuilder::class);
        $this->app->bind(SubscriptionResolverInterface::class, SubscriptionResolver::class);

        Event::listen(
            \Morenorafael\Subscription\Events\SubscriptionSaving::class,
            \Morenorafael\Subscription\Listeners\PlanSubscription\SetPeriodWhenEmpty::class
        );

        Event::listen(
            \Morenorafael\Subscription\Events\SubscriptionSaving::class,
            \Morenorafael\Subscription\Listeners\PlanSubscription\DispatchEventWhenPlanChanges::class
        );
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../database/migrations/' => database_path('migrations')
        ], 'subscription-migrations');

        $this->publishes([
            __DIR__ . '/../config/subscription.php' => config_path('subscription.php')
        ], 'subscription-config');
    }
}
