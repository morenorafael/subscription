
# Subscription

## Installation

```sh
composer  require  morenorafael/subscription
```

### Service Provider

```php
'providers' => [
/**
* Third Party Service Providers...
*/
Morelorafael\Subscription\SubscriptionServiceProvider::class,
...
```

### Config File and Migrations

Publish package config file and migrations with the following command:

```sh
php  artisan  vendor:publish  --provider="Morelorafael\Subscription\SubscriptionServiceProvider"
```

```sh
php  artisan  migrate
```

### Traits and Contracts

Add `Morelorafael\Subscription\Traits\PlanSubscriber` trait and `Morelorafael\Subscription\Contracts\PlanSubscriberInterface` contract to your `User` model.

See the following example:

```php
namespace  App\Models;

use Illuminate\Foundation\Auth\User  as  Authenticatable;
use Morenorafael\Subscription\Contracts\PlanSubscriberInterface;
use Morenorafael\Subscription\Traits\PlanSubscriber;

class  User  extends  Authenticatable  implements  PlanSubscriberInterface
{
	use  PlanSubscriber;

	...
```



## Usage

### Create a Plan

```php

use Morenorafael\Subscription\Models\Plan;
use Morenorafael\Subscription\Models\PlanFeature;

$plan  =  Plan::create([
	'name' => 'Pro',
	'description' => 'Pro plan',
	'price' => 9.99,
	'interval' => 'month',
	'interval_count' => 1,
	'trial_period_days' => 15,
	'sort_order' => 1,
]);

$plan->features()->saveMany([
	new  PlanFeature(['code' => 'listings', 'value' => 50, 'sort_order' => 1]),
	new  PlanFeature(['code' => 'pictures_per_listing', 'value' => 10, 'sort_order' => 5]),
	new  PlanFeature(['code' => 'listing_duration_days', 'value' => 30, 'sort_order' => 10]),
	new  PlanFeature(['code' => 'listing_title_bold', 'value' => 'Y', 'sort_order' => 15])
]);

...
```

### Accessing Plan Features

In some cases you need to access a particular feature in a particular plan, you can accomplish this by using the `getFeatureByCode` method available in the `Plan` model.

Example:

```php
$feature  =  $plan->getFeatureByCode('pictures_per_listing');
$feature->value  // Get the feature's value
```



### Create a Subscription

First, retrieve an instance of your subscriber model, which typically will be your user model and an instance of the plan your user is subscribing to. Once you have retrieved the model instance, you may use the `newSubscription` method (available in `PlanSubscriber` trait) to create the model’s subscription.

```php
use  Auth;
use Morenorafael\Subscription\Models\Plan;

$user  =  Auth::user();
$plan  =  Plan::find(1);

$user->newSubscription('main', $plan)->create();
```

The first argument passed to `newSubscription` method should be the name of the subscription. If your application offer a single subscription, you might call this `main` or `primary`. Subscription’s name is not the Plan’s name, it is an unique subscription identifier. The second argument is the plan instance your user is subscribing to.

### Subscription resolving

When you use the `subscription()` method (i.e., `$user->subscription('main')`) in the subscribable model to retrieve a subscription, you will receive the latest subscription created of the subscribable and the subscription name. For example, if you subscribe Jane Doe to Free plan, and later to Pro plan, Laraplans will return the subscription with the Pro plan because it is the newest subscription available. If you have a different requirement you may use your own subscription resolver by binding an implementation of `Morenorafael\Subscription\Contracts\SubscriptionResolverInterface` to the [service container](https://laravel.com/docs/10.x); like so:

```php
/**
 * Register the application services.
 *
 * @return void
 */
public function register()
{
    $this->app->bind(SubscriptionResolverInterface::class, CustomSubscriptionResolver::class);
}
```

### Subscription’s Ability

There are multiple ways to determine the usage and ability of a particular feature in the user’s subscription, the most common one is `canUse`:

The  `canUse`  method returns  `true`  or  `false`  depending on multiple factors:

-   Feature  _is enabled_
-   Feature value isn’t  `0`.
-   Or feature has remaining uses available

```php
$user->subscription('main')->ability()->canUse('listings');
```

**There are other ways to determine the ability of a subscription:**

-   `enabled`: returns  `true`  when the value of the feature is a  _positive word_  listed in the config file.
-   `consumed`: returns how many times the user has used a particular feature.
-   `remainings`: returns available uses for a particular feature.
-   `value`: returns the feature value.

All methods share the same signature:  `$user->subscription('main')->ability()->consumed('listings');`.

### Record Feature Usage

In order to efectively use the ability methods you will need to keep track of every usage of usage based features. You may use the  `record`  method available through the user  `subscriptionUsage()`  method:

The  `record`  method accepts 3 parameters: the first one is the feature’s code, the second one is the quantity of uses to add (default is  `1`), and the third one indicates if the usage should be incremented (`true`: default behavior) or overriden (`false`).

See the following example:

```php
// Increment by 2
$user->subscriptionUsage('main')->record('listings', 2);

// Override with 9
$user->subscriptionUsage('main')->record('listings', 9, false);
```

### Reduce Feature Usage

Reducing the feature usage is _almost_ the same as incrementing it. In this case we only _substract_ a given quantity (default is `1`) to the actual usage:

```php
// Reduce by 1
$user->subscriptionUsage('main')->reduce('listings');

// Reduce by 2
$user->subscriptionUsage('main')->reduce('listings', 2);
```

### Clear The Subscription Usage Data

In some cases you will need to clear all usages in a particular user subscription, you can accomplish this by using the `clear` method:

```php
$user->subscriptionUsage('main')->clear();
```

### Check Subscription Status

For a subscription to be considered **active** the subscription must have an active trial or subscription’s `ends_at` is in the future.

```php
$user->subscribed('main');
$user->subscribed('main', $planId); // Check if subscription is active AND using a particular plan
```

Alternatively, you can use the following methods available in the subscription model:

```php
$user->subscription('main')->isActive();
$user->subscription('main')->isCanceled();
$user->subscription('main')->isCanceledImmediately();
$user->subscription('main')->isEnded();
$user->subscription('main')->onTrial();
```

> Caution
> **Canceled** subscriptions **with** an active trial or `ends_at` in the future are considered active.

### Renew a Subscription

To renew a subscription you may use the `renew` method available in the subscription model. This will set a new `ends_at` date based on the selected plan and **will clear the usage data** of the subscription.

```php
$user->subscription('main')->renew();
```

> Caution
> Canceled subscriptions with an ended period can’t be renewed.

`Morenorafael\Subscription\Events\SubscriptionRenewed` event is fired when a subscription is renewed using the `renew` method.

### Cancel a Subscription

To cancel a subscription, simply use the  `cancel`  method on the user’s subscription:

```php
$user->subscription('main')->cancel();
```

By default, the subscription will remain active until the period ends. Pass  `true`  to  _immediately_  cancel a subscription.

```php
$user->subscription('main')->cancel(true);
```

## Events

The following are the events fired by the package:

-   `Morenorafael\Subscription\Events\SubscriptionCreated`: Fired when a subscription is created.
-   `Morenorafael\Subscription\Events\SubscriptionRenewed`: Fired when a subscription is renewed using the  `renew()`  method.
-   `Morenorafael\Subscription\Events\SubscriptionCanceled`: Fired when a subscription is canceled using the  `cancel()`  method.
-   `Morenorafael\Subscription\Events\SubscriptionPlanChanged`: Fired when a subscription’s plan is changed; it will be fired once the  `PlanSubscription`  model is saved. Plan change is determined by comparing the original and current value of  `plan_id`.

## Eloquent Scopes

```php
use Morenorafael\Subscription\Models\PlanSubscription;

// Get subscriptions by plan:
$subscriptions = PlanSubscription::byPlan($plan_id)->get();

// Get subscription by user:
$subscription = PlanSubscription::byUser($user_id)->first();

// Get subscriptions with trial ending in 3 days:
$subscriptions = PlanSubscription::findEndingTrial(3)->get();

// Get subscriptions with ended trial:
$subscriptions = PlanSubscription::findEndedTrial()->get();

// Get subscriptions with period ending in 3 days:
$subscriptions = PlanSubscription::findEndingPeriod(3)->get();

// Get subscriptions with ended period:
$subscriptions = PlanSubscription::findEndedPeriod()->get();

// Exclude subscriptions which are canceled:
$subscriptions = PlanSubscription::excludeCanceled()->get();

// Exclude subscriptions which are immediately canceled:
$subscriptions = PlanSubscription::scopeExcludeImmediatelyCanceled()->get();
```