<?php

namespace MemberGlut\Core\Admin\SettingsPages;

use MemberGlut\Custom_Settings_Page_Api;

class GeneralSettings extends AbstractSettingsPage
{
    public $settingsPageInstance;

    public function __construct()
    {
        // registers the global MemberGlut dashboard menu
        add_action('admin_menu', array($this, 'register_core_menu'));

        add_action('mglut_register_menu_page_general_general', function () {
            $this->settingsPageInstance = Custom_Settings_Page_Api::instance([], MGLUT_SETTINGS_DB_OPTION_NAME, esc_html__('General', 'memberglut'));
        });

        add_action('mglut_register_menu_page', array($this, 'register_menu_page'));

        add_action('mglut_admin_settings_submenu_page_general_general', [$this, 'settings_admin_page_callback']);

        // flush rewrite rule on save/persistence
        add_action('mglut_cspa_persist_settings', function () {
            flush_rewrite_rules();
        });

        $this->custom_sanitize();
    }

    public function register_menu_page()
    {
        $hook = add_submenu_page(
            MGLUT_DASHBOARD_SETTINGS_SLUG,
            'MemberGlut ' . apply_filters('mglut_general_settings_admin_page_title', esc_html__('Settings', 'memberglut')),
            esc_html__('Settings', 'memberglut'),
            'manage_options',
            MGLUT_SETTINGS_SLUG,
            array($this, 'admin_page_callback')
        );

        add_action("load-$hook", [$this, 'screen_option']);
    }

    public function default_header_menu()
    {
        return 'general';
    }

    public function header_menu_tabs()
    {
        $tabs = apply_filters('mglut_settings_page_tabs', [
            20 => ['id' => 'general', 'url' => MGLUT_SETTINGS_SETTING_PAGE, 'label' => esc_html__('General', 'memberglut')],
        ]);

        ksort($tabs);

        return $tabs;
    }

    public function integrations_submenu_tabs()
    {
        return apply_filters('mglut_integrations_submenu_tabs', []);
    }

    public function header_submenu_tabs()
    {
        $tabs = apply_filters('mglut_settings_page_submenus_tabs', [
            0     => ['parent' => 'general', 'id' => 'general', 'label' => esc_html__('General', 'memberglut')],
        ]);

        $tabs = $tabs + $this->integrations_submenu_tabs();

        ksort($tabs);

        return $tabs;
    }

    public function screen_option()
    {
        do_action('mglut_settings_page_screen_option');
    }

    public function settings_admin_page_callback()
    {
        $custom_page = apply_filters('mglut_general_settings_admin_page_short_circuit', false);

        if (false !== $custom_page) return $custom_page;

        
        $login_redirect_page_dropdown_args = [
            ['key' => 'current_page', 'label' => esc_html__('Currently viewed page', 'memberglut')],
            ['key' => 'none', 'label' => esc_html__('Previous/Referrer page (Pro feature)', 'memberglut'), 'disabled' => true],
            ['key' => 'dashboard', 'label' => esc_html__('WordPress Dashboard', 'memberglut')]
        ];

        $fix_db_url = wp_nonce_url(
            add_query_arg('mglut-install-missing-db', 'true', MGLUT_SETTINGS_SETTING_GENERAL_PAGE),
            'mglut_install_missing_db_tables'
        );

        $args = [
           /** Set default values on register activation */
            'business_info'             => apply_filters('mglut_business_info_settings_page', [
                'tab_title' => esc_html__('General Info', 'memberglut'),
                'dashicon'  => 'dashicons-info',
                [
                    'section_title'        => esc_html__('General Information', 'memberglut'),
                    'business_name'        => [
                        'type'        => 'text',
                        'label'       => esc_html__('Business Name', 'memberglut'),
                        'description' => esc_html__('The official (legal) name of your store. Defaults to Site Title if empty.', 'memberglut'),
                    ],
                    'business_address'     => [
                        'type'        => 'text',
                        'label'       => esc_html__('Address', 'memberglut'),
                        'description' => esc_html__('The street address where your business is registered and located.', 'memberglut'),
                    ],
                    
                    'business_postal_code' => [
                        'type'        => 'text',
                        'label'       => esc_html__('ZIP / Postal Code', 'memberglut'),
                        'description' => esc_html__('The country in which your business is located.', 'memberglut'),
                    ],
                    
                ],
            ]),
        ];

        if ( ! $this->is_core_page_missing()) {
            unset($args['global_pages'][0]['create_required_pages_notice']);
        }

        $business_country = mglut_business_country();

        if ( ! empty($business_country) && ! empty(mglut_array_of_world_states($business_country))) {
            $args['business_info'][0]['business_state']['type']    = 'select';
            $args['business_info'][0]['business_state']['options'] = ['' => '&mdash;&mdash;&mdash;'] + mglut_array_of_world_states($business_country);
        }


        $this->settingsPageInstance->main_content(apply_filters('mglut_settings_page_args', $args));
        $this->settingsPageInstance->build_sidebar_tab_style();
    }

    private function is_core_page_missing()
    {
        $required_pages = [
            'set_login_url',
            'set_registration_url',
            'set_lost_password_url',
            'edit_user_profile_url',
            'set_user_profile_shortcode',
            'checkout_page_id',
            'payment_success_page_id',
            'payment_failure_page_id'
        ];

        $result = false;

        foreach ($required_pages as $required_page) {

            if (empty(mglut_settings_by_key($required_page, ''))) {
                $result = true;
                break;
            }
        }

        return $result;
    }


    public function custom_sanitize()
    {
        $config = apply_filters('mglut_settings_custom_sanitize', [
            'global_restricted_access_message' => function ($val) {
                return wp_kses_post($val);
            },
            'bank_transfer_account_details'    => function ($val) {
                return wp_kses_post($val);
            },
            'uec_unactivated_error'            => function ($val) {
                return wp_kses_post($val);
            },
            'uec_invalid_error'                => function ($val) {
                return wp_kses_post($val);
            },
            'uec_success_message'              => function ($val) {
                return wp_kses_post($val);
            },
            'uec_activation_resent'            => function ($val) {
                return wp_kses_post($val);
            },
            'uec_already_confirm_message'      => function ($val) {
                return wp_kses_post($val);
            }
        ]);

        foreach ($config as $fieldKey => $callback) {
            add_filter('mglut_cspa_sanitize_skip', function ($return, $key, $value) use ($fieldKey, $callback) {
                if ($key == $fieldKey) {
                    return call_user_func($callback, $value);
                }

                return $return;
            }, 10, 3);
        }
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