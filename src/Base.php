<?php

namespace MemberGlut\Core;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


use MemberGlut\Core\Admin\SettingsPages\Membership\PlansPage\SettingsPage as PlansSettingsPage;

use MemberGlut\Core\Admin\SettingsPages\GeneralSettings;
use MemberGlut\Core\Classes\AdminNotices;


define('MEMBERGLUT_SRC', plugin_dir_path(MEMBERGLUT_SYSTEM_FILE_PATH) . 'src/');
define('MGLUT_ADMIN_SETTINGS_PAGE_FOLDER', MEMBERGLUT_SRC . 'Admin/SettingsPages/');

define('MGLUT_ERROR_LOG_FOLDER', WP_CONTENT_DIR . "/uploads/memberglut-logs/");

define('MGLUT_SETTINGS_SLUG', 'mglut-config');
define('MGLUT_FORMS_SETTINGS_SLUG', 'mglut-forms');
define('MGLUT_MEMBER_DIRECTORIES_SLUG', 'mglut-directories');

define('MGLUT_CONTENT_PROTECTION_SETTINGS_SLUG', 'mglut-content-protection');
define('MGLUT_DASHBOARD_SETTINGS_SLUG', 'mglut-dashboard');
define('MGLUT_MEMBERSHIP_PLANS_SETTINGS_SLUG', 'mglut-plans');
define('MGLUT_MEMBERSHIP_ORDERS_SETTINGS_SLUG', 'mglut-orders');
define('MGLUT_MEMBERSHIP_CUSTOMERS_SETTINGS_SLUG', 'mglut-customers');
define('MGLUT_MEMBERSHIP_SUBSCRIPTIONS_SETTINGS_SLUG', 'mglut-subscriptions');
define('MGLUT_EXTENSIONS_SETTINGS_SLUG', 'mglut-extensions');

define('MGLUT_SETTINGS_SETTING_PAGE', admin_url('admin.php?page=' . MGLUT_SETTINGS_SLUG));
define('MGLUT_SETTINGS_SETTING_GENERAL_PAGE', add_query_arg(['section' => 'general'], admin_url('admin.php?page=' . MGLUT_SETTINGS_SLUG)));
define('MGLUT_FORMS_SETTINGS_PAGE', admin_url('admin.php?page=' . MGLUT_FORMS_SETTINGS_SLUG));
define('MGLUT_USER_PROFILES_SETTINGS_PAGE', add_query_arg('form-type', 'user-profile', MGLUT_FORMS_SETTINGS_PAGE));
define('MGLUT_DASHBOARD_SETTINGS_PAGE', admin_url('admin.php?page=' . MGLUT_DASHBOARD_SETTINGS_SLUG));
define('MGLUT_MEMBERSHIP_SUBSCRIPTION_PLANS_SETTINGS_PAGE', admin_url('admin.php?page=' . MGLUT_MEMBERSHIP_PLANS_SETTINGS_SLUG));

define('MGLUT_SETTINGS_DB_OPTION_NAME', 'mglut_settings_data');
define('MGLUT_CONTACT_INFO_OPTION_NAME', 'mglut_contact_info');
define('MGLUT_PAYMENT_METHODS_OPTION_NAME', 'mglut_payment_methods');
define('MGLUT_FILE_DOWNLOADS_OPTION_NAME', 'mglut_file_downloads');
define('MGLUT_TAXES_OPTION_NAME', 'mglut_taxes');

define('MGLUT_ASSETS_URL', plugin_dir_url(MEMBERGLUT_SYSTEM_FILE_PATH) . 'assets');


class Base extends DBTables
{


    public function __construct()
    {
        register_activation_hook(MEMBERGLUT_SYSTEM_FILE_PATH, ['MemberGlut\Core\RegisterActivation\Base', 'run_install']);

        if (version_compare(get_bloginfo('version'), '5.1', '<')) {
            add_action('wpmu_new_blog', ['MemberGlut\Core\RegisterActivation\Base', 'multisite_new_blog_install']);
        } else {
            add_action('wp_insert_site', function (\WP_Site $new_site) {
                RegisterActivation\Base::multisite_new_blog_install($new_site->blog_id);
            });
        }


        // handles edge case where register activation isn't triggered especially when migrating from wp user avatar.
        add_action('admin_init', function () {
            if (get_option('mglut_plugin_activated') != 'true') {
                RegisterActivation\Base::run_install();
            }
        });

        do_action('mglut_before_loaded');

      
        RegisterScripts::get_instance();
       

        $this->admin_hooks();

        do_action('mglut_loaded');

    }


    public function admin_hooks()
    {
        if ( ! is_admin()) {
            return;
        }

        do_action('mglut_admin_before_hooks');


        Admin\SettingsPages\Membership\DashboardPage\SettingsPage::get_instance();
     
        PlansSettingsPage::get_instance();
    

        GeneralSettings::get_instance();
      

        AdminNotices::get_instance();
      

        do_action('mglut_admin_hooks');
    }

    /**
     * Singleton.
     *
     * @return Base
     */
    public static function get_instance()
    {

        static $instance = null;

        if (is_null($instance)) {
            $instance = new self();
        }

        return $instance;
    }
}
