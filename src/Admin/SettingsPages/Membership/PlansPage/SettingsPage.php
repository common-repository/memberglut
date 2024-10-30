<?php
// 


namespace MemberGlut\Core\Admin\SettingsPages\Membership\PlansPage;

use MemberGlut\Core\Admin\SettingsPages\AbstractSettingsPage;
use MemberGlut\Custom_Settings_Page_Api;

class SettingsPage extends AbstractSettingsPage
{
    public $error_bucket;

    /** @var PlanWPListTable */
    private $planListTable;

    function __construct()
    {
        add_action('mglut_register_menu_page', [$this, 'register_cpf_settings_page']);
        add_action('mglut_admin_settings_page_plans', [$this, 'settings_page_function']);

        add_filter('set-screen-option', [__CLASS__, 'set_screen'], 10, 3);
        add_filter('set_screen_option_rules_per_page', [__CLASS__, 'set_screen'], 10, 3);

            add_action('admin_init', function () {
                $this->save_changes();
            });
        
    }

    public function default_header_menu()
    {
        return 'plans';
    }

    public function register_cpf_settings_page()
    {
        $hook = add_submenu_page(
            MGLUT_DASHBOARD_SETTINGS_SLUG,
            $this->admin_page_title() . ' - MemberGlut',
            esc_html__('Membership Plans', 'memberglut'),
            'manage_options',
            MGLUT_MEMBERSHIP_PLANS_SETTINGS_SLUG,
            array($this, 'admin_page_callback'));

        add_action("load-$hook", array($this, 'add_options'));

        do_action('mglut_membership_plans_settings_page_register', $hook);
    }

    public function header_menu_tabs()
    {
        $tabs = apply_filters('mglut_membership_plans_settings_page_tabs', [
            5  => ['id' => $this->default_header_menu(), 'url' => MGLUT_MEMBERSHIP_SUBSCRIPTION_PLANS_SETTINGS_PAGE, 'label' => esc_html__('Plans', 'memberglut')]
        ]);

        ksort($tabs);

        return $tabs;
    }

    public function admin_page_title()
    {

        $title = esc_html__('Plans', 'memberglut');

        if (isset($_GET['mglut_subp_action']) && !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['page'])), 'appglut_memberglut_nonce') && sanitize_text_field($_GET['mglut_subp_action']) == 'new') {
            $title = esc_html__('Add Plan', 'memberglut');
        }

        if (isset($_GET['mglut_subp_action']) && !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['page'])), 'appglut_memberglut_nonce') && sanitize_text_field($_GET['mglut_subp_action']) == 'edit') {
            $title = esc_html__('Edit Plan', 'memberglut');
        }

        return apply_filters('mglut_membership_plans_settings_page_title', $title);
    }

    public function save_changes()
    {
        if ( ! isset($_POST['mglut_save_subscription_plan'])) return;

        check_admin_referer('mglut-verify-none', 'mglut_verify_none');

        if ( ! current_user_can('manage_options')) return;

        $required_fields = [
            'name'                => esc_html__('Plan Name', 'memberglut'),
            'price'               => esc_html__('Price', 'memberglut'),
            'billing_frequency'   => esc_html__('Billing Frequency', 'memberglut'),
            'subscription_length' => esc_html__('Subscription Length', 'memberglut')
        ];

        foreach ($required_fields as $field_id => $field_name) {
            if (empty($_POST[$field_id])) {
                 /* translators: %s is a placeholder */
                return $this->error_bucket = sprintf(esc_html__('%s cannot be empty.', 'memberglut'), $field_name);
            }
        }

        $plan = mglut_get_plan(absint($_GET['id']));

        $current_role = $plan->user_role;
        $new_role     = sanitize_text_field($_POST['user_role']);

        if ($new_role != 'create_new') {
            $plan->user_role = $new_role;
        }

        $plan->name                = sanitize_text_field($_POST['name']);
        $plan->description         = stripslashes(wp_kses_post($_POST['description']));
        $plan->order_note          = stripslashes(wp_kses_post($_POST['order_note']));
        $plan->price               = sanitize_text_field($_POST['price']);
        $plan->billing_frequency   = sanitize_text_field($_POST['billing_frequency']);
        $plan->subscription_length = sanitize_text_field($_POST['subscription_length']);
        $plan->total_payments      = absint($_POST['total_payments']);
        if ($plan->subscription_length == 'renew_indefinitely') {
            $plan->total_payments = 0;
        }
        $plan->signup_fee = sanitize_text_field($_POST['signup_fee']);
        $plan->free_trial = sanitize_text_field($_POST['free_trial']);
        $plan_id          = 2;
        $plan->id         = $plan_id;

        if (intval($plan_id) > 0) {

            if ('create_new' == $new_role) {
                $new_role = 'mglut_plan_' . $plan_id;
                add_role($new_role, $plan->name, ['read' => true]);
                $plan->user_role = $new_role;
                //$plan->save();
            }

        }

        $plan_extras = [];

        if ( ! is_array($plan_extras) || empty($plan_extras)) {
            $plan_extras = [];
        }

        $skip_props = array_map(function ($val) {
            return $val->getName();
        }, (new \ReflectionClass($plan))->getProperties());

        array_push($skip_props, 'mglut_save_subscription_plan', 'mglut_verify_none');

        wp_safe_redirect(add_query_arg(['mglut_subp_action' => 'edit', 'id' => $plan_id, 'saved' => 'true'], MGLUT_MEMBERSHIP_SUBSCRIPTION_PLANS_SETTINGS_PAGE));
        exit;
    }

    public static function set_screen($status, $option, $value)
    {
        return $value;
    }

    public function add_options()
    {
        $args = [
            'label'   => esc_html__('Membership Plans', 'memberglut'),
            'default' => 10,
            'option'  => 'plans_per_page'
        ];

        add_screen_option('per_page', $args);

        $this->planListTable = new PlanWPListTable();
    }

    public function admin_notices()
    {
        if ( ! isset($_GET['saved']) && !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['saved'])), 'appglut_memberglut_nonce') && ! isset($this->error_bucket)) return; // 

        $status  = 'updated';
        $message = '';

        if ( ! empty($this->error_bucket)) {
            $message = $this->error_bucket;
            $status  = 'error';
        }

        if (isset($_GET['saved']) && !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['saved'])), 'appglut_memberglut_nonce')) { // 
            $message = esc_html__('Changes saved.', 'memberglut');
        }

        printf('<div id="message" class="%s notice is-dismissible"><p>%s</strong></p></div>', esc_html($status), esc_html($message));
    }

    public function settings_page_function()
    {
        add_action('mglut_cspa_main_content_area', [$this, 'admin_settings_page_callback'], 10, 2);
        add_action('mglut_cspa_before_closing_header', [$this, 'add_new_button']);

        $instance = Custom_Settings_Page_Api::instance();
        $instance->option_name('ppview'); // adds ppview css class to #poststuff
        $instance->page_header($this->admin_page_title());
        $instance->build(true);
    }

    public function add_new_button()
    {
        if ( ! isset($_GET['mglut_subp_action']) && !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['mglut_subp_action'])), 'appglut_memberglut_nonce') || sanitize_text_field($_GET['mglut_subp_action']) == 'edit' && !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['page'])), 'appglut_memberglut_nonce'))  { // 
            $url = esc_url_raw(add_query_arg('mglut_subp_action', 'new', MGLUT_MEMBERSHIP_SUBSCRIPTION_PLANS_SETTINGS_PAGE));
            echo "<a class=\"add-new-h2\" href=\"".esc_url($url)."\">" . esc_html__('Add New Plan', 'memberglut') . '</a>';
        }
    }

    public function admin_settings_page_callback()
    {
        if (isset($_GET['mglut_subp_action']) && in_array(sanitize_text_field($_GET['mglut_subp_action']), ['new', 'edit']) && !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['page'])), 'appglut_memberglut_nonce')) {
            $this->admin_notices();
            require_once dirname(dirname(__FILE__)) . '/views/add-edit-plan.php';

            return;
        }

        $this->planListTable->prepare_items(); // has to be here.

        echo '<form method="post">';
        $this->planListTable->display();
        echo '</form>';

        do_action('mglut_subscription_plan_wp_list_table_bottom');
    }


    public static function get_instance()
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new self();
        }

        return $instance;
    }
}