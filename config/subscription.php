<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Positive Words
    |--------------------------------------------------------------------------
    |
    | These words indicates "true" and are used to check if a particular plan
    | feature is enabled.
    |
    */
    'positive_words' => [
        'Y',
        'YES',
        'TRUE',
        'UNLIMITED',
    ],

    /*
    |--------------------------------------------------------------------------
    | Models
    |--------------------------------------------------------------------------
    |
    | If you want to use your own models you will want to update the following
    | array to make sure Laraplans use them.
    |
    */
    'models' => [
        'plan' => Morenorafael\Subscription\Models\Plan::class,
        'plan_feature' => Morenorafael\Subscription\Models\PlanFeature::class,
        'plan_subscription' => Morenorafael\Subscription\Models\PlanSubscription::class,
        'plan_subscription_usage' => Morenorafael\Subscription\Models\PlanSubscriptionUsage::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Features
    |--------------------------------------------------------------------------
    |
    | The heart of this package. Here you will specify all features available
    | for your plans.
    |
    */
    'features' => [
        'SAMPLE_SIMPLE_FEATURE',
        'SAMPLE_DEFINED_FEATURE' => [
            'resettable_interval' => 'month',
            'resettable_count' => 2
        ],
    ],
];
