<?php

namespace MemberGlut\Core\RegisterActivation;


class Base
{
    public static function run_install($networkwide = false)
    {
        if (is_multisite() && $networkwide) {

            $site_ids = get_sites(['fields' => 'ids', 'number' => 0]);

            foreach ($site_ids as $site_id) {
                switch_to_blog($site_id);
                self::mglut_install();
                restore_current_blog();
            }
        } else {
            self::mglut_install();
        }

        flush_rewrite_rules();
    }

    /**
     * Run plugin install / activation action when new blog is created in multisite setup.
     *
     * @param int $blog_id
     */
    public static function multisite_new_blog_install($blog_id)
    {
        if ( ! function_exists('is_plugin_active_for_network')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        if (is_plugin_active_for_network('memberglut/memberglut.php')) {
            switch_to_blog($blog_id);
            self::mglut_install();
            restore_current_blog();
        }
    }

    /**
     * Perform plugin activation / installation.
     */
    public static function mglut_install()
    {
        // set flag for those migrating
        if ( ! get_option('mglut_plugin_activated')) {
            add_option('mglut_is_from_wp_user_avatar', 'true');
        }

        if ( ! current_user_can('activate_plugins') || get_option('mglut_plugin_activated') == 'true') return;

        CreateDBTables::make();

        self::default_settings();

        add_option('mglut_install_date', current_time('mysql'));
        add_option('mglut_plugin_activated', 'true');

        flush_rewrite_rules();
    }

    

    public static function default_settings()
    {
        $settings = [
            'login_username_email_restrict'    => 'both',
            'myac_edit_account_endpoint'       => 'edit-profile',
            'myac_change_password_endpoint'    => 'change-password',
            'set_user_profile_slug'            => 'profile',
            'set_login_redirect'               => 'dashboard',
            'global_site_access'               => 'everyone',
            'global_restricted_access_message' => '<p>' . esc_html__('You are unauthorized to view this page.', 'memberglut') . '</p>',

            'admin_email_addresses' => mglut_admin_email(),
            'email_sender_name'     => mglut_site_title(),
            'email_sender_email'    => 'wordpress@' . mglut_site_url_without_scheme(),
            'email_content_type'    => 'text/html',
            'email_template_type'   => 'default',

            'password_reset_email_enabled' => 'on',
                    /* translators: %s is a placeholder */
            'password_reset_email_subject' => sprintf(__('[%s] Password Reset', 'memberglut'), mglut_site_title()),
            

            'new_user_admin_email_email_enabled' => 'on',
            /* translators: %s is a placeholder */
            'new_user_admin_email_email_subject' => sprintf(__('[%s] New User Registration', 'memberglut'), mglut_site_title()),
        ];

        foreach ($settings as $key => $value) {
            mglut_update_settings($key, $value);
        }

        self::membership_default_settings();
    }

    public static function membership_default_settings()
    {
        $settings = [
            // Business info
            'business_name'               => mglut_site_title(),
            'business_country'            => 'US',
            // Payment settings
            //'payment_currency'            => 'USD', excluded so onboarding checklist can ask user to set currency
            'currency_position'           => 'left',
            'currency_decimal_separator'  => '.',
            'currency_thousand_separator' => ',',
            'currency_decimal_number'     => '2',
            /* translators: %s is a placeholder */
            'terms_agreement_label'       => sprintf(__('I have read and agree to the website %s', 'memberglut'), '[terms]'),
        ];

        foreach ($settings as $key => $value) {
            mglut_update_settings($key, $value);
        }
    }


}