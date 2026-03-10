<?php

return [
    'database' => [
        'cancel_migrations_autoloading' => true,
    ],

    'feature_tickets' => env('SOULBSCRIPTION_FEATURE_TICKETS', false),

    'models' => [
        'feature' => \App\Models\Subscriptions\Feature::class,
        'feature_consumption' => \App\Models\Subscriptions\FeatureConsumption::class,
        'feature_ticket' => \App\Models\Subscriptions\FeatureTicket::class,
        'feature_plan' => \App\Models\Subscriptions\FeaturePlan::class,
        'plan' => \App\Models\Subscriptions\Plan::class,
        'subscriber' => [
            'uses_uuid' => env('SOULBSCRIPTION_SUBSCRIBER_USES_UUID', false),
        ],
        'subscription' => \App\Models\Subscriptions\Subscription::class,
        'subscription_renewal' => \App\Models\Subscriptions\SubscriptionRenewal::class,
    ],
];
