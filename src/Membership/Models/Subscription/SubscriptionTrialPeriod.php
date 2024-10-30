<?php

namespace MemberGlut\Core\Membership\Models\Subscription;

class SubscriptionTrialPeriod
{
    const DISABLED = 'disabled';
    const THREE_DAYS = '3_day';
    const FIVE_DAYS = '5_day';
    const ONE_WEEK = '1_week';
    const TWO_WEEKS = '2_week';
    const THREE_WEEKS = '3_week';
    const ONE_MONTH = '1_month';

    public static function get_all()
    {
        return apply_filters('mglut_subscription_trial_periods', [
            self::DISABLED    => __('Disabled', 'memberglut'),
            self::THREE_DAYS  => __('3 Days', 'memberglut'),
            self::FIVE_DAYS   => __('5 Days', 'memberglut'),
            self::ONE_WEEK    => __('One Week', 'memberglut'),
            self::TWO_WEEKS   => __('Two Weeks', 'memberglut'),
            self::THREE_WEEKS => __('Three Weeks', 'memberglut'),
            self::ONE_MONTH   => __('One Month', 'memberglut')
        ]);
    }

    public static function get_label($status)
    {
        return self::get_all()[$status] ?? '';
    }
}