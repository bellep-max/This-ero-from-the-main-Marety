<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use LucasDotVin\Soulbscription\Enums\PeriodicityType;
use App\Models\Subscriptions\Plan;
use App\Models\Subscriptions\Feature;
use App\Constants\SubscriptionPlanConstants;

class SubscriptionPlansSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $free_plans = [
            // Anonymous
            'visitor' => [
                'name' => 'Visitor',
                'periodicity_type' => null,
                'periodicity' => null,
                'price' => SubscriptionPlanConstants::PRICE_MONTHLY_VISITOR,
                'currency' => SubscriptionPlanConstants::PRICING_CURRENCY,
                'is_subscribable' => 0,
                'is_cancelable' => 0,
                'is_visible' => 1,
                'description' => 'For those just looking around.'
            ],
            // Listener
            'listener' => [
                'name' => 'Listener',
                'periodicity_type' => null,
                'periodicity' => null,
                'price' => SubscriptionPlanConstants::PRICE_MONTHLY_LISTENER,
                'currency' => SubscriptionPlanConstants::PRICING_CURRENCY,
                'is_subscribable' => 1,
                'is_cancelable' => 0,
                'is_visible' => 1,
                'description' => 'For those who just want to listen for a while.'
            ],
            // Creator
            'creator' => [
                'name' => 'Creator',
                'periodicity_type' => null,
                'periodicity' => null,
                'price' => SubscriptionPlanConstants::PRICE_MONTHLY_CREATOR,
                'currency' => SubscriptionPlanConstants::PRICING_CURRENCY,
                'is_subscribable' => 1,
                'is_cancelable' => 0,
                'is_visible' => 1,
                'description' => 'The plan for casual creators.'
            ],
        ];

        $paid_monthly_plans = [
            // Listener Pro
            'listener_pro_monthly' => [
                'name' => 'Listener Pro',
                'periodicity_type' => PeriodicityType::Month,
                'periodicity' => 1,
                'price' => SubscriptionPlanConstants::PRICE_MONTHLY_LISTENER_PRO,
                'currency' => SubscriptionPlanConstants::PRICING_CURRENCY,
                'grace_days' => SubscriptionPlanConstants::GRACE_DAYS,
                'is_subscribable' => 1,
                'is_cancelable' => 1,
                'is_visible' => 1,
                'description' => 'For distinguished connoisseurs.'
            ],
            // Creator Premium
            'creator_premium_monthly' => [
                'name' => 'Creator Premium',
                'periodicity_type' => PeriodicityType::Month,
                'periodicity' => 1,
                'price' => SubscriptionPlanConstants::PRICE_MONTHLY_CREATOR_PREMIUM,
                'currency' => SubscriptionPlanConstants::PRICING_CURRENCY,
                'grace_days' => SubscriptionPlanConstants::GRACE_DAYS,
                'is_subscribable' => 1,
                'is_cancelable' => 1,
                'is_visible' => 1,
                'description' => 'Creators who have more to show.'
            ],
            // Creator Pro
            'creator_pro_monthly' => [
                'name' => 'Creator Pro',
                'periodicity_type' => PeriodicityType::Month,
                'periodicity' => 1,
                'price' => SubscriptionPlanConstants::PRICE_MONTHLY_CREATOR_PRO,
                'currency' => SubscriptionPlanConstants::PRICING_CURRENCY,
                'grace_days' => SubscriptionPlanConstants::GRACE_DAYS,
                'is_subscribable' => 1,
                'is_cancelable' => 1,
                'is_visible' => 1,
                'description' => 'For our most prolific creators.'
            ],
        ];

        $paid_annual_plans = [];

        foreach ($paid_monthly_plans as $k => $mp) {
            $annual_plan = $mp;

            $paid_monthly_plans[$k]['name'] .= ' Monthly';

            $annual_plan['name'] .= ' Annual';
            $annual_plan['price'] = SubscriptionPlanConstants::resolveAnnualPrice($annual_plan['price']);
            $annual_plan['periodicity_type'] = PeriodicityType::Year;

            $ak = preg_replace('/_monthly$/', '_annual', $k);

            $paid_annual_plans[$ak] = $annual_plan;
        }

        $all_plans = array_merge($free_plans, $paid_monthly_plans, $paid_annual_plans);

        //dump('####### PLANS #######');

        $made_plans = [];

        foreach ($all_plans as $plan_name => $plan) {
            $made_plans[strtolower($plan_name)] = Plan::create($plan);

            //dump($made_plans[strtolower($plan_name)]->id . ': ' . $made_plans[strtolower($plan_name)]->name);
        }

        $features = [
            'play_tracks' => [
                'consumable' => false,
                'periodicity_type' => null,
                'periodicity' => null,
                'quota' => false,
                'postpaid' => false,
                'label' => 'Play Tracks',
                'plan_adds' => [
                    'visitor' => [
                        'charges' => true
                    ],
                    'listener' => [
                        'charges' => true
                    ],
                    'creator' => [
                        'charges' => true
                    ],
                    'listener_pro' => [
                        'charges' => true
                    ],
                    'creator_premium' => [
                        'charges' => true
                    ],
                    'creator_pro' => [
                        'charges' => true
                    ],
                ],
            ],
            'hd_streaming' => [
                'consumable' => false,
                'periodicity_type' => null,
                'periodicity' => null,
                'quota' => false,
                'postpaid' => false,
                'label' => 'HD Audio Streaming',
                'plan_adds' => [
                    'listener_pro' => [
                        'charges' => true
                    ],
                    'creator_premium' => [
                        'charges' => true
                    ],
                    'creator_pro' => [
                        'charges' => true
                    ],
                ],
            ],
            'full_age_verification' => [
                'consumable' => false,
                'periodicity_type' => null,
                'periodicity' => null,
                'quota' => false,
                'postpaid' => false,
                'label' => 'Age Verification',
                'plan_adds' => [
                    'listener_pro' => [
                        'charges' => true
                    ],
                    'creator_premium' => [
                        'charges' => true
                    ],
                    'creator_pro' => [
                        'charges' => true
                    ],
                ],
            ],
            'audio_streaming' => [
                'consumable' => false,
                'periodicity_type' => null,
                'periodicity' => null,
                'quota' => false,
                'postpaid' => false,
                'label' => 'Audio Streaming',
                'plan_adds' => [
                    'visitor' => [
                        'charges' => true
                    ],
                    'listener' => [
                        'charges' => true
                    ],
                    'creator' => [
                        'charges' => true
                    ],
                    'listener_pro' => [
                        'charges' => true
                    ],
                    'creator_premium' => [
                        'charges' => true
                    ],
                    'creator_pro' => [
                        'charges' => true
                    ],
                ],
            ],
            'audio_downloads' => [
                'consumable' => false,
                'periodicity_type' => null,
                'periodicity' => null,
                'quota' => false,
                'postpaid' => false,
                'label' => 'Audio Downloads',
                'plan_adds' => [
                    'listener_pro' => [
                        'charges' => true
                    ],
                    'creator_premium' => [
                        'charges' => true
                    ],
                    'creator_pro' => [
                        'charges' => true
                    ],
                ],
            ],
            'ad_free' => [
                'consumable' => false,
                'periodicity_type' => null,
                'periodicity' => null,
                'quota' => false,
                'postpaid' => false,
                'label' => 'Ad-Free Experience',
                'plan_adds' => [
                    'listener_pro' => [
                        'charges' => true
                    ],
                    'creator_premium' => [
                        'charges' => true
                    ],
                    'creator_pro' => [
                        'charges' => true
                    ],
                ],
            ],
            'fandoms' => [
                'consumable' => false,
                'periodicity_type' => null,
                'periodicity' => null,
                'quota' => false,
                'postpaid' => false,
                'label' => 'Creator Fandoms',
                'plan_adds' => [
                    'creator' => [
                        'charges' => true
                    ],
                    'creator_premium' => [
                        'charges' => true
                    ],
                    'creator_pro' => [
                        'charges' => true
                    ],
                ],
            ],
            'tippable' => [
                'consumable' => false,
                'periodicity_type' => null,
                'periodicity' => null,
                'quota' => false,
                'postpaid' => false,
                'label' => 'Creator Tipping',
                'plan_adds' => [
                    'creator_premium' => [
                        'charges' => true
                    ],
                    'creator_pro' => [
                        'charges' => true
                    ],
                ],
            ],
            'concurrent_uploads' => [
                'consumable' => true,
                'periodicity_type' => null,
                'periodicity' => null,
                'quota' => true,
                'postpaid' => false,
                'label' => 'Concurrent Uploads',
                'plan_adds' => [
                    'creator' => [
                        'charges' => 1
                    ],
                    'creator_premium' => [
                        'charges' => 3
                    ],
                    'creator_pro' => [
                        'charges' => 10
                    ],
                ],
            ],
            'revenue_sharing' => [
                'consumable' => false,
                'periodicity_type' => null,
                'periodicity' => null,
                'quota' => false,
                'postpaid' => false,
                'label' => 'Revenue Sharing',
                'plan_adds' => [
                    'creator_premium' => [
                        'charges' => true
                    ],
                    'creator_pro' => [
                        'charges' => true
                    ],
                ],
            ],
            'track_hours_editable' => [
                'consumable' => false,
                'periodicity_type' => null,
                'periodicity' => null,
                'quota' => true,
                'postpaid' => false,
                'label' => 'Track Hours Editable',
                'plan_adds' => [
                    'creator' => [
                        'charges' => 1
                    ],
                    'creator_premium' => [
                        'charges' => 3
                    ],
                    'creator_pro' => [
                        'charges' => NULL
                    ],
                ],
            ],
            'track_images' => [
                'consumable' => true,
                'periodicity_type' => null,
                'periodicity' => null,
                'quota' => true,
                'postpaid' => false,
                'label' => 'Track Images',
                'plan_adds' => [
                    'creator' => [
                        'charges' => 1
                    ],
                    'creator_premium' => [
                        'charges' => 3
                    ],
                    'creator_pro' => [
                        'charges' => NULL
                    ],
                ],
            ],
            'track_library_limit' => [
                'consumable' => true,
                'periodicity_type' => null,
                'periodicity' => null,
                'quota' => true,
                'postpaid' => false,
                'label' => 'Track Library Limit',
                'plan_adds' => [
                    'creator' => [
                        'charges' => NULL
                    ],
                    'creator_premium' => [
                        'charges' => NULL
                    ],
                    'creator_pro' => [
                        'charges' => NULL
                    ],
                ],
            ],
            'track_max_price' => [
                'consumable' => false,
                'periodicity_type' => null,
                'periodicity' => null,
                'quota' => false,
                'postpaid' => false,
                'label' => 'Track Maximum Price',
                'plan_adds' => [
                    'creator_premium' => [
                        'charges' => 20
                    ],
                    'creator_pro' => [
                        'charges' => NULL
                    ],
                ],
            ],
            'track_min_price' => [
                'consumable' => false,
                'periodicity_type' => null,
                'periodicity' => null,
                'quota' => false,
                'postpaid' => false,
                'label' => 'Track Minimum Price',
                'plan_adds' => [
                    'creator_premium' => [
                        'charges' => 0.99
                    ],
                    'creator_pro' => [
                        'charges' => 0.99
                    ],
                ],
            ],
            'track_sales_artist_cut' => [
                'consumable' => false,
                'periodicity_type' => null,
                'periodicity' => null,
                'quota' => false,
                'postpaid' => false,
                'label' => 'Track Sales Artist Cut',
                'plan_adds' => [
                    'creator_premium' => [
                        'charges' => 0.5
                    ],
                    'creator_pro' => [
                        'charges' => 0.6
                    ],
                ],
            ],
            'track_stream_pay_rate' => [
                'consumable' => false,
                'periodicity_type' => null,
                'periodicity' => null,
                'quota' => false,
                'postpaid' => false,
                'label' => 'Track Stream Pay Rate',
                'plan_adds' => [
                    'creator_premium' => [
                        'charges' => 0.007825
                    ],
                    'creator_pro' => [
                        'charges' => 0.0085
                    ],
                ],
            ],
            'track_uploads' => [
                'consumable' => true,
                'periodicity_type' => PeriodicityType::Week,
                'periodicity' => 1,
                'quota' => true,
                'postpaid' => false,
                'label' => 'Track Uploads/week',
                'plan_adds' => [
                    'creator' => [
                        'charges' => 7
                    ],
                    'creator_premium' => [
                        'charges' => 21
                    ],
                    'creator_pro' => [
                        'charges' => null
                    ],
                ],
            ],
            'gallery_hours_editable' => [
                'consumable' => true,
                'periodicity_type' => null,
                'periodicity' => null,
                'quota' => true,
                'postpaid' => false,
                'label' => 'Gallery Hours Editable',
                'plan_adds' => [
                    'creator' => [
                        'charges' => 24
                    ],
                    'creator_premium' => [
                        'charges' => NULL
                    ],
                    'creator_pro' => [
                        'charges' => NULL
                    ],
                ],
            ],
            'gallery_images_limit' => [
                'consumable' => true,
                'periodicity_type' => null,
                'periodicity' => null,
                'quota' => true,
                'postpaid' => false,
                'label' => 'Gallery Images Limit',
                'plan_adds' => [
                    'creator' => [
                        'charges' => 1
                    ],
                    'creator_premium' => [
                        'charges' => 5
                    ],
                    'creator_pro' => [
                        'charges' => 20
                    ],
                ],
            ],
            'gallery_limit' => [
                'consumable' => true,
                'periodicity_type' => null,
                'periodicity' => null,
                'quota' => true,
                'postpaid' => false,
                'label' => 'Gallery Limit',
                'plan_adds' => [
                    'creator' => [
                        'charges' => 3
                    ],
                    'creator_premium' => [
                        'charges' => 20
                    ],
                    'creator_pro' => [
                        'charges' => NULL
                    ],
                ],
            ],
            'gallery_maximum_price' => [
                'consumable' => false,
                'periodicity_type' => null,
                'periodicity' => null,
                'quota' => false,
                'postpaid' => false,
                'label' => 'Gallery Maximum Price',
                'plan_adds' => [
                    'creator_premium' => [
                        'charges' => 29.99
                    ],
                    'creator_pro' => [
                        'charges' => NULL
                    ],
                ],
            ],
            'gallery_minimum_price' => [
                'consumable' => false,
                'periodicity_type' => null,
                'periodicity' => null,
                'quota' => false,
                'postpaid' => false,
                'label' => 'Gallery Minimum Price',
                'plan_adds' => [
                    'creator_premium' => [
                        'charges' => 0.99
                    ],
                    'creator_pro' => [
                        'charges' => 2.99
                    ],
                ],
            ],
            'gallery_sales_artist_cut' => [
                'consumable' => false,
                'periodicity_type' => null,
                'periodicity' => null,
                'quota' => false,
                'postpaid' => false,
                'label' => 'Gallery Sales Artist Cut',
                'plan_adds' => [
                    'creator_premium' => [
                        'charges' => 0.5
                    ],
                    'creator_pro' => [
                        'charges' => 0.5
                    ],
                ],
            ],
            'gallery_tracks_limit' => [
                'consumable' => true,
                'periodicity_type' => null,
                'periodicity' => null,
                'quota' => true,
                'postpaid' => false,
                'label' => 'Gallery Tracks Limit',
                'plan_adds' => [
                    'creator' => [
                        'charges' => 6
                    ],
                    'creator_premium' => [
                        'charges' => 12
                    ],
                    'creator_pro' => [
                        'charges' => NULL
                    ],
                ],
            ],
            'playlist_collaborators' => [
                'consumable' => true,
                'periodicity_type' => null,
                'periodicity' => null,
                'quota' => true,
                'postpaid' => false,
                'label' => 'Playlist Collaborators',
                'plan_adds' => [
                    'listener' => [
                        'charges' => 3
                    ],
                    'creator' => [
                        'charges' => 5
                    ],
                    'listener_pro' => [
                        'charges' => 3
                    ],
                    'creator_premium' => [
                        'charges' => NULL
                    ],
                    'creator_pro' => [
                        'charges' => NULL
                    ],
                ],
            ],
            'playlist_images_limit' => [
                'consumable' => true,
                'periodicity_type' => null,
                'periodicity' => null,
                'quota' => true,
                'postpaid' => false,
                'label' => 'Playlist Images Limit',
                'plan_adds' => [
                    'listener' => [
                        'charges' => 1
                    ],
                    'creator' => [
                        'charges' => 5
                    ],
                    'listener_pro' => [
                        'charges' => 1
                    ],
                    'creator_premium' => [
                        'charges' => 10
                    ],
                    'creator_pro' => [
                        'charges' => 20
                    ],
                ],
            ],
            'playlist_limit' => [
                'consumable' => true,
                'periodicity_type' => null,
                'periodicity' => null,
                'quota' => true,
                'postpaid' => false,
                'label' => 'Playlist Limit',
                'plan_adds' => [
                    'listener' => [
                        'charges' => 5
                    ],
                    'creator' => [
                        'charges' => 24
                    ],
                    'listener_pro' => [
                        'charges' => NULL
                    ],
                    'creator_premium' => [
                        'charges' => NULL
                    ],
                    'creator_pro' => [
                        'charges' => NULL
                    ],
                ],
            ],
            'playlist_tracks_limit' => [
                'consumable' => true,
                'periodicity_type' => null,
                'periodicity' => null,
                'quota' => true,
                'postpaid' => false,
                'label' => 'Playlist Tracks Limit',
                'plan_adds' => [
                    'listener' => [
                        'charges' => 5
                    ],
                    'creator' => [
                        'charges' => 10
                    ],
                    'listener_pro' => [
                        'charges' => 25
                    ],
                    'creator_premium' => [
                        'charges' => 100
                    ],
                    'creator_pro' => [
                        'charges' => NULL
                    ],
                ],
            ],
            'comment' => [
                'consumable' => false,
                'periodicity_type' => null,
                'periodicity' => null,
                'quota' => false,
                'postpaid' => false,
                'label' => 'Comments',
                'plan_adds' => [
                    'listener' => [
                        'charges' => true
                    ],
                    'creator' => [
                        'charges' => true
                    ],
                    'listener_pro' => [
                        'charges' => true
                    ],
                    'creator_premium' => [
                        'charges' => true
                    ],
                    'creator_pro' => [
                        'charges' => true
                    ],
                ],
            ],
            'like' => [
                'consumable' => false,
                'periodicity_type' => null,
                'periodicity' => null,
                'quota' => false,
                'postpaid' => false,
                'label' => 'Like',
                'plan_adds' => [
                    'listener' => [
                        'charges' => true
                    ],
                    'creator' => [
                        'charges' => true
                    ],
                    'listener_pro' => [
                        'charges' => true
                    ],
                    'creator_premium' => [
                        'charges' => true
                    ],
                    'creator_pro' => [
                        'charges' => true
                    ],
                ],
            ],
            'rate' => [
                'consumable' => false,
                'periodicity_type' => null,
                'periodicity' => null,
                'quota' => false,
                'postpaid' => false,
                'label' => 'Rate',
                'plan_adds' => [
                    'listener' => [
                        'charges' => true
                    ],
                    'creator' => [
                        'charges' => true
                    ],
                    'listener_pro' => [
                        'charges' => true
                    ],
                    'creator_premium' => [
                        'charges' => true
                    ],
                    'creator_pro' => [
                        'charges' => true
                    ],
                ],
            ],
            'react' => [
                'consumable' => false,
                'periodicity_type' => null,
                'periodicity' => null,
                'quota' => false,
                'postpaid' => false,
                'label' => 'React',
                'plan_adds' => [
                    'listener' => [
                        'charges' => true
                    ],
                    'creator' => [
                        'charges' => true
                    ],
                    'listener_pro' => [
                        'charges' => true
                    ],
                    'creator_premium' => [
                        'charges' => true
                    ],
                    'creator_pro' => [
                        'charges' => true
                    ],
                ],
            ],
            'favorite' => [
                'consumable' => false,
                'periodicity_type' => null,
                'periodicity' => null,
                'quota' => false,
                'postpaid' => false,
                'label' => 'Favorite',
                'plan_adds' => [
                    'listener' => [
                        'charges' => true
                    ],
                    'creator' => [
                        'charges' => true
                    ],
                    'listener_pro' => [
                        'charges' => true
                    ],
                    'creator_premium' => [
                        'charges' => true
                    ],
                    'creator_pro' => [
                        'charges' => true
                    ],
                ],
            ],
            'follow' => [
                'consumable' => false,
                'periodicity_type' => null,
                'periodicity' => null,
                'quota' => false,
                'postpaid' => false,
                'label' => 'Follow',
                'plan_adds' => [
                    'listener' => [
                        'charges' => true
                    ],
                    'creator' => [
                        'charges' => true
                    ],
                    'listener_pro' => [
                        'charges' => true
                    ],
                    'creator_premium' => [
                        'charges' => true
                    ],
                    'creator_pro' => [
                        'charges' => true
                    ],
                ],
            ],
            'tag' => [
                'consumable' => false,
                'periodicity_type' => null,
                'periodicity' => null,
                'quota' => false,
                'postpaid' => false,
                'label' => 'Tag',
                'plan_adds' => [
                    'listener' => [
                        'charges' => true
                    ],
                    'creator' => [
                        'charges' => true
                    ],
                    'listener_pro' => [
                        'charges' => true
                    ],
                    'creator_premium' => [
                        'charges' => true
                    ],
                    'creator_pro' => [
                        'charges' => true
                    ],
                ],
            ],
            'share' => [
                'consumable' => false,
                'periodicity_type' => null,
                'periodicity' => null,
                'quota' => false,
                'postpaid' => false,
                'label' => 'Share',
                'plan_adds' => [
                    'listener' => [
                        'charges' => true
                    ],
                    'creator' => [
                        'charges' => true
                    ],
                    'listener_pro' => [
                        'charges' => true
                    ],
                    'creator_premium' => [
                        'charges' => true
                    ],
                    'creator_pro' => [
                        'charges' => true
                    ],
                ],
            ],
            'review' => [
                'consumable' => false,
                'periodicity_type' => null,
                'periodicity' => null,
                'quota' => false,
                'postpaid' => false,
                'label' => 'Review',
                'plan_adds' => [
                    'listener' => [
                        'charges' => true
                    ],
                    'creator' => [
                        'charges' => true
                    ],
                    'listener_pro' => [
                        'charges' => true
                    ],
                    'creator_premium' => [
                        'charges' => true
                    ],
                    'creator_pro' => [
                        'charges' => true
                    ],
                ],
            ],
            'report' => [
                'consumable' => false,
                'periodicity_type' => null,
                'periodicity' => null,
                'quota' => false,
                'postpaid' => false,
                'label' => 'Report',
                'plan_adds' => [
                    'listener' => [
                        'charges' => true
                    ],
                    'creator' => [
                        'charges' => true
                    ],
                    'listener_pro' => [
                        'charges' => true
                    ],
                    'creator_premium' => [
                        'charges' => true
                    ],
                    'creator_pro' => [
                        'charges' => true
                    ],
                ],
            ],
            'featured_placement' => [
                'consumable' => false,
                'periodicity_type' => null,
                'periodicity' => null,
                'quota' => false,
                'postpaid' => false,
                'label' => 'Featured Placement',
                'plan_adds' => [
                    'creator_premium' => [
                        'charges' => true
                    ],
                    'creator_pro' => [
                        'charges' => true
                    ],
                ],
            ],
            'expanded_profile' => [
                'consumable' => false,
                'periodicity_type' => null,
                'periodicity' => null,
                'quota' => false,
                'postpaid' => false,
                'label' => 'Expanded Profile',
                'plan_adds' => [
                    'creator_premium' => [
                        'charges' => true
                    ],
                    'creator_pro' => [
                        'charges' => true
                    ],
                ],
            ],
            'social_media_links' => [
                'consumable' => false,
                'periodicity_type' => null,
                'periodicity' => null,
                'quota' => false,
                'postpaid' => false,
                'label' => 'Social Media Links',
                'plan_adds' => [
                    'creator' => [
                        'charges' => true
                    ],
                    'creator_premium' => [
                        'charges' => true
                    ],
                    'creator_pro' => [
                        'charges' => true
                    ],
                ],
            ],
            'social_media_marketing' => [
                'consumable' => false,
                'periodicity_type' => null,
                'periodicity' => null,
                'quota' => false,
                'postpaid' => false,
                'label' => 'Social Media Marketing',
                'plan_adds' => [
                    'creator_premium' => [
                        'charges' => true
                    ],
                    'creator_pro' => [
                        'charges' => true
                    ],
                ],
            ],
            'adfree_owned_content' => [
                'consumable' => false,
                'periodicity_type' => null,
                'periodicity' => null,
                'quota' => false,
                'postpaid' => false,
                'label' => 'Ad-free Owned Content',
                'plan_adds' => [
                    'creator_premium' => [
                        'charges' => true
                    ],
                    'creator_pro' => [
                        'charges' => true
                    ],
                ],
            ],
            'transcode_queue_priority' => [
                'consumable' => false,
                'periodicity_type' => null,
                'periodicity' => null,
                'quota' => false,
                'postpaid' => false,
                'label' => 'Transcode Queue Priority',
                'plan_adds' => [
                    'creator' => [
                        'charges' => 3
                    ],
                    'creator_premium' => [
                        'charges' => 6
                    ],
                    'creator_pro' => [
                        'charges' => 9
                    ],
                ],
            ],
            'transcode_queue_limit' => [
                'consumable' => true,
                'periodicity_type' => null,
                'periodicity' => null,
                'quota' => true,
                'postpaid' => false,
                'label' => 'Transcode Queue Limit',
                'plan_adds' => [
                    'creator' => [
                        'charges' => 1
                    ],
                    'creator_premium' => [
                        'charges' => 3
                    ],
                    'creator_pro' => [
                        'charges' => 10
                    ],
                ],
            ],
        ];

        $all_features = [];

        //dump('####### FEATURES #######');

        foreach ($features as $feature_name => $f) {
            $f['name'] = $feature_name;
            $f['description'] = '';

            unset($f['plan_adds']);

            $all_features[$feature_name] = Feature::create($f);

            //dump($all_features[$feature_name]->id . ': ' . $all_features[$feature_name]->name);
        }

        //dump('####### PLAN FEATURES #######');

        foreach ($features as $feature_name => $f) {
            if (!array_key_exists('plan_adds', $f) || count($f['plan_adds']) == 0) {
                dump($feature_name . ' is added to no plans!');

                continue;
            }

            foreach ($f['plan_adds'] as $p => $v) {
                $pt = [$p, $p . '_annual', $p . '_monthly'];

                foreach ($pt as $plan_var) {
                    if (array_key_exists($plan_var, $made_plans)) {
                        if (get_class($made_plans[$plan_var]) == 'App\Models\Subscriptions\Plan') {
                            //if ($v === true) {
                            //    $made_plans[$plan_var]->features()->attach($all_features[$feature_name]);
                            //}
                            if ($v['charges'] === true || is_null($v['charges'])) {
                                $made_plans[$plan_var]->features()->attach($all_features[$feature_name], ['charges' => null]);
                            }
                            else {
                                $made_plans[$plan_var]->features()->attach($all_features[$feature_name], ['charges' => $v['charges']]);
                            }

                            //dump($plan_var . ' attached feature ' . $feature_name . ' = ' . (is_null($v['charges']) ? 'null' : $v['charges']));
                        }
                        //else {
                        //    dump($plan_var . ' is not a plan? ' . (isset($made_plans[$plan_var]) ? 'T' : 'F') . ' ; ' . get_class($made_plans[$plan_var]));
                        //}
                    }
                    //else {
                    //    //dump('made_plans[' . $plan_var . '] is not set?');
                    //}
                }
            }
        }
    }
}
