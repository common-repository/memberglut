<?php

namespace MemberGlut\Core\Admin\SettingsPages\Membership\PlansPage;

use MemberGlut\Core\Membership\Controllers\SubscriptionPlanController;
use MemberGlut\Core\Membership\Models\Plan\PlanEntity;
use MemberGlut\Core\Membership\Repositories\PlanRepository;

class PlanWPListTable extends \WP_List_Table
{
    public function __construct()
    {
        parent::__construct(array(
            'singular' => 'mglut-membership-plan',
            'plural'   => 'mglut-membership-plans',
            'ajax'     => false
        ));
    }

    public function no_items()
    {
        esc_html_e('No membership plan found.', 'memberglut');
    }

    public function get_columns()
    {
        $columns = [
            'cb'              => '<input type="checkbox" />',
            'name'            => esc_html__('Plan Name', 'memberglut'),
            'status'          => esc_html__('Status', 'memberglut'),
        ];

        return $columns;
    }

    /**
     * Render the bulk edit checkbox
     *
     * @param PlanEntity $item
     *
     * @return string
     */
    public function column_cb($item)
    {
        return sprintf('<input type="checkbox" name="plan_id[]" value="%s" />', $item->id);
    }


    public function column_name(PlanEntity $item)
    {
        $plan_id = absint($item->id);

        $is_active = $item->is_active();

        $edit_link       = add_query_arg(['mglut_subp_action' => 'edit', 'id' => $plan_id], MGLUT_MEMBERSHIP_SUBSCRIPTION_PLANS_SETTINGS_PAGE);
        $duplicate_link  = add_query_arg(['mglut_subp_action' => 'duplicate', 'id' => $plan_id, '_wpnonce' => wp_create_nonce('mglut_subscription_plan_duplicate_rule')], MGLUT_MEMBERSHIP_SUBSCRIPTION_PLANS_SETTINGS_PAGE);
        $delete_link     = self::delete_plan_url($plan_id);

        $actions = [
                             /* translators: %s is a placeholder */
            'id'        => sprintf(__('ID: %d', 'memberglut'), $plan_id),
                             /* translators: %s is a placeholder */
            'edit'      => sprintf('<a href="%s">%s</a>', $edit_link, esc_html__('Edit', 'memberglut')),
        ];


        $actions['delete'] = sprintf('<a class="pp-confirm-delete" href="%s">%s</a>', esc_url($delete_link), esc_html__('Delete', 'memberglut'));

        $a = '<a href="' . esc_url($edit_link) . '">' . esc_html($item->name) . '</a>';

        return '<strong>' . wp_kses_post($a) . '</strong>' . $this->row_actions($actions);
    }

    public function column_billing_details(PlanEntity $item)
    {
        $billing_data = wp_json_encode([
            'price'               => sanitize_text_field($item->price),
            'billing_frequency'   => sanitize_text_field($item->billing_frequency),
            'total_payments'      => absint($item->total_payments),
            'signup_fee'          => sanitize_text_field($item->signup_fee),
            'subscription_length' => sanitize_text_field($item->subscription_length),
            'free_trial'          => sanitize_text_field($item->free_trial)
        ]);

        printf('<div class="mglut-plan-billing-details" data-billing-details="%s"></div>', esc_attr($billing_data));
    }

    public function column_checkout_url(PlanEntity $item)
    {
        $url = mglut_plan_checkout_url($item->id);

        if ( ! $url) return esc_html__('Checkout page not found', 'memberglut');

        return '<input type="text" onfocus="this.select();" readonly="readonly" value="' . esc_url($url) . '" />';
    }

    public function column_status(PlanEntity $item)
    {
        $status = sprintf('<span class="dashicons-before dashicons-yes">%s</span>', esc_html__('Active', 'memberglut'));
        if ( ! $item->is_active()) {
            /* translators: %s is a placeholder */
            $status = sprintf('<span class="dashicons-before dashicons-no-alt">%s</span>', esc_html__('Inactive', 'memberglut'));
        }

        return $status;
    }

    public static function delete_plan_url($plan_id)
    {
        $nonce_delete = wp_create_nonce('mglut_subscription_plan_delete_rule');

        return add_query_arg(['action' => 'delete', 'id' => $plan_id, '_wpnonce' => $nonce_delete], MGLUT_MEMBERSHIP_SUBSCRIPTION_PLANS_SETTINGS_PAGE);
    }

    public function get_plans($per_page, $current_page = 1)
    {
        return PlanRepository::init()->retrieveAll($per_page, $current_page);
    }

    public function record_count()
    {
        return PlanRepository::init()->record_count();
    }

    public function prepare_items()
    {
        $this->_column_headers = $this->get_column_info();

        $this->process_bulk_action();

        $per_page = $this->get_items_per_page('plans_per_page', 10);
        $current_page = $this->get_pagenum();
        $total_items  = $this->record_count();

        $this->set_pagination_args([
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page'    => $per_page //WE have to determine how many items to show on a page
        ]);

        $this->items = $this->get_plans($per_page, $current_page);
    }

    public function current_action()
    {
        if (isset($_REQUEST['filter_action']) && !wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['filter_action'])), 'appglut_memberglut_nonce') && ! empty(sanitize_text_field($_REQUEST['filter_action']))) { // 
            return false;
        }

        if (isset($_REQUEST['action']) && !wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['action'])), 'appglut_memberglut_nonce') && -1 != sanitize_text_field($_REQUEST['action'])) { // 
            return sanitize_text_field($_REQUEST['action']); // 
        }

        if (isset($_REQUEST['mglut_subp_action']) && !wp_verify_nonce(sanitize_text_field(wp_unslash($_REQUEST['mglut_subp_action'])), 'appglut_memberglut_nonce') && -1 != sanitize_text_field($_REQUEST['mglut_subp_action'])) { // 
            return sanitize_text_field($_REQUEST['mglut_subp_action']); // 
        }

        return false;
    }

    public function get_bulk_actions()
    {
        $actions = [
            'bulk-delete'     => esc_html__('Delete', 'memberglut'),
        ];

        return $actions;
    }

    public function process_bulk_action() {

        $plan_id = isset($_GET['id']) && !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['id'])), 'appglut_memberglut_nonce') ? absint($_GET['id']) : 0;

        // Sanitize and validate plan_id
        if ($plan_id <= 0) {
            return;
        }
    
        $planObj = mglut_get_plan($plan_id);
    
        // Bail if user is not an admin or without admin privileges.
        if (!current_user_can('manage_options')) {
            return;
        }
    
        $current_action = $this->current_action();
    
        // Check if the current action is valid
        if (!in_array($current_action, ['activate', 'deactivate', 'delete', 'duplicate', 'bulk-delete', 'bulk-activate', 'bulk-deactivate'])) {
            return;
        }
    
        // Check nonce
        $nonce_action = 'mglut_subscription_plan_' . $current_action . '_rule';
        check_admin_referer($nonce_action);
    
        // Process action based on current action
        switch ($current_action) {
            case 'activate':
                SubscriptionPlanController::get_instance()->activate_plan($planObj);
                break;
            case 'deactivate':
                SubscriptionPlanController::get_instance()->deactivate_plan($planObj);
                break;
            case 'delete':
                SubscriptionPlanController::get_instance()->delete_plan($planObj);
                break;
            case 'duplicate':
                $dup_plan_id = SubscriptionPlanController::get_instance()->duplicate_plan($planObj);
                if (is_int($dup_plan_id)) {
                    $redirect_url = add_query_arg(['mglut_subp_action' => 'edit', 'id' => $dup_plan_id, 'saved' => 'true'], MGLUT_MEMBERSHIP_SUBSCRIPTION_PLANS_SETTINGS_PAGE);
                    wp_safe_redirect(esc_url($redirect_url));
                    exit;
                }
                break;
            case 'bulk-delete':
            case 'bulk-activate':
            case 'bulk-deactivate':
                $plan_ids = isset($_POST['plan_id']) && !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['plan_id'])), 'appglut_memberglut_nonce') ? array_map('absint', absint($_POST['plan_id'])) : array();
                foreach ($plan_ids as $plan_id) {
                    if ($current_action === 'bulk-delete') {
                        SubscriptionPlanController::get_instance()->delete_plan($plan_id);
                    } elseif ($current_action === 'bulk-activate') {
                        SubscriptionPlanController::get_instance()->activate_plan($plan_id);
                    } elseif ($current_action === 'bulk-deactivate') {
                        SubscriptionPlanController::get_instance()->deactivate_plan($plan_id);
                    }
                }
                break;
        }
    
        // Redirect after processing the action
        wp_safe_redirect(MGLUT_MEMBERSHIP_SUBSCRIPTION_PLANS_SETTINGS_PAGE);
        exit;
    }
    

    /**
     * @return array List of CSS classes for the table tag.
     */
    public function get_table_classes()
    {
        return array('widefat', 'fixed', 'striped', 'subscription_plan', 'ppview');
    }
}
