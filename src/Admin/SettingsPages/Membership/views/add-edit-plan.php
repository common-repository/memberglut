<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use MemberGlut\Core\Admin\SettingsPages\Membership\PlanIntegrationsMetabox;
use MemberGlut\Core\Admin\SettingsPages\Membership\SettingsFieldsParser;
use MemberGlut\Core\Membership\Models\Subscription\SubscriptionBillingFrequency;
use MemberGlut\Core\Membership\Models\Subscription\SubscriptionTrialPeriod;

$plan_data   = [''];
$plan_extras = [''];


$append = ['create_new' => esc_html__('Create user role for this membership plan', 'memberglut')];

if ( ! is_null(get_role('mglut_plan_' . $plan_data->id))) {
    $append = [];
}

$user_roles = $append + (function () {
        $core_roles = mglut_wp_roles_key_value();
        unset($core_roles['administrator']);
        unset($core_roles['editor']);
        unset($core_roles['author']);
        unset($core_roles['contributor']);
        unset($core_roles['subscriber']);

        return $core_roles;
    })();

$plan_details = [
    [
        'id'    => 'name',
        'type'  => 'text',
        'label' => esc_html__('Plan Name', 'memberglut')
    ],
    [
        'id'          => 'description',
        'type'        => 'wp_editor',
        'label'       => esc_html__('Plan Description', 'memberglut'),
        'description' => esc_html__('A description of this plan.', 'memberglut')
    ],
    [
        'id'          => 'order_note',
        'type'        => 'textarea',
        'label'       => esc_html__('Plan Note', 'memberglut'),
        'description' => esc_html__('Enter an optional note.', 'memberglut')
    ],
    [
        'id'          => 'user_role',
        'type'        => 'select',
        'options'     => $user_roles,
        'label'       => esc_html__('User Role', 'memberglut'),
        'description' => esc_html__('Select the user role to associate with this membership plan.', 'memberglut')
    ],
    [
        'id'          => 'price',
        'type'        => 'price',
        'label'       => esc_html__('Price', 'memberglut') . sprintf(' (%s)', mglut_get_currency_symbol()),
        'description' => esc_html__('The price of this membership plan.', 'memberglut')
    ]
];

$subscription_settings = [
    [
        'id'      => 'billing_frequency',
        'type'    => 'select',
        'label'   => esc_html__('Billing Period', 'memberglut'),
        'options' => SubscriptionBillingFrequency::get_all()
    ],
    [
        'id'      => 'subscription_length',
        'type'    => 'select',
        'label'   => esc_html__('Subscription Options', 'memberglut'),
        'options' => [
            'renew_indefinitely' => esc_html__('Renew indefinitely until member cancels', 'memberglut'),
            'fixed'              => esc_html__('Fixed number of payments', 'memberglut')
        ]
    ],
    [
        'id'          => 'total_payments',
        'type'        => 'number',
        'label'       => esc_html__('Payments', 'memberglut'),
        'description' => esc_html__('The total number of recurring billing cycles including the trial period (if applicable).', 'memberglut')
    ],
    [
        'id'          => 'signup_fee',
        'type'        => 'price',
        'label'       => esc_html__('Initial Fee', 'memberglut') . sprintf(' (%s)', mglut_get_currency_symbol()),
        'description' => esc_html__('Optional signup fee.', 'memberglut')
    ],
    [
        'id'          => 'free_trial',
        'type'        => 'select',
        'options'     => SubscriptionTrialPeriod::get_all(),
        'label'       => esc_html__('Free Trial', 'memberglut'),
        'description' => esc_html__('Duration of time before charging them.', 'memberglut')
    ]
];


add_action('add_meta_boxes', function () use ($subscription_settings, $plan_details, $plan_data, $plan_extras, $meta_box_settings) {
    add_meta_box(
        'mglut-membership-plan-content',
        esc_html__('Plan Details', 'memberglut'),
        function () use ($plan_details, $plan_data) {
            echo '<div class="mglut-membership-plan-details">';
            (new SettingsFieldsParser($plan_details, $plan_data))->build();
            echo '</div>';
        },
        'ppmembershipplan'
    );

    add_meta_box(
        'mglut-subscription-plan-settings',
        esc_html__('Subscription Settings', 'memberglut'),
        function () use ($subscription_settings, $plan_data) {
            echo '<div class="mglut-subscription-plan-settings">';
            (new SettingsFieldsParser($subscription_settings, $plan_data))->build();
            echo '</div>';
        },
        'ppmembershipplan'
    );


    add_meta_box(
        'submitdiv',
        __('Publish', 'memberglut'),
        function () {
            require dirname(__FILE__) . '/plans-page-sidebar.php';
        },
        'ppmembershipplan',
        'sidebar'
    );

});

do_action('add_meta_boxes', 'ppmembershipplan', new WP_Post(new stdClass()));
?>
    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
            <div id="postbox-container-1" class="postbox-container">
                <?php do_meta_boxes('ppmembershipplan', 'sidebar', ''); ?>
            </div>
            <div id="postbox-container-2" class="postbox-container">
                <?php do_meta_boxes('ppmembershipplan', 'advanced', ''); ?>
            </div>
        </div>
        <br class="clear">
    </div>

