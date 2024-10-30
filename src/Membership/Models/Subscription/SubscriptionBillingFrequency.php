<?php

namespace MemberGlut\Core\Membership\Models\Subscription;

class SubscriptionBillingFrequency
{
    const MONTHLY = 'monthly';
    const WEEKLY = 'weekly';
    const DAILY = 'daily';
    const QUARTERLY = '3_month';
    const EVERY_6_MONTHS = '6_month';
    const YEARLY = '1_year';
    const ONE_TIME = 'lifetime';
    const LIFETIME = 'lifetime';

    public static function get_all()
    {
        return apply_filters('mglut_subscription_billing_frequency', [
            self::MONTHLY        => __('Monthly', 'memberglut'),
            self::WEEKLY         => __('Weekly', 'memberglut'),
            self::DAILY          => __('Daily', 'memberglut'),
            self::QUARTERLY      => __('Quarterly (every 3 months)', 'memberglut'),
            self::EVERY_6_MONTHS => __('Every 6 months', 'memberglut'),
            self::YEARLY         => __('Yearly', 'memberglut'),
            self::ONE_TIME       => __('One-time purchase', 'memberglut')
        ]);
    }

    public static function get_label($status)
    {
        return self::get_all()[$status] ?? '';
    }
}