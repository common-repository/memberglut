<?php

namespace MemberGlut\Core\Membership\Models\Subscription;

class SubscriptionStatus
{
    const ACTIVE = 'active';
    const PENDING = 'pending';
    const CANCELLED = 'cancelled';
    const EXPIRED = 'expired';
    const TRIALLING = 'trialling';
    const COMPLETED = 'completed';

    public static function get_all()
    {
        return apply_filters('mglut_subscription_statuses', [
            self::ACTIVE    => __('Active', 'memberglut'),
            self::PENDING   => __('Pending', 'memberglut'),
            self::EXPIRED   => __('Expired', 'memberglut'),
            self::COMPLETED => __('Completed', 'memberglut'),
            self::TRIALLING => __('Trialling', 'memberglut'),
            self::CANCELLED => __('Cancelled', 'memberglut')
        ]);
    }

    public static function get_label($status)
    {
        return self::get_all()[$status] ?? '';
    }
}