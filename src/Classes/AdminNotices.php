<?php

namespace MemberGlut\Core\Classes;

use MemberGlut\Core\Membership\PaymentMethods\AbstractPaymentMethod;

class AdminNotices
{
    public function __construct()
    {
        add_action('admin_init', function () {

            if (mglut_is_admin_page()) {
                remove_all_actions('admin_notices');
            }

            add_action('admin_notices', [$this, 'admin_notices_bucket']);

            add_filter('removable_query_args', [$this, 'removable_query_args']);
        });

      
        add_action('admin_init', array($this, 'act_on_request'));

        add_filter('admin_body_class', [$this, 'add_admin_body_class']);
    }

    public function add_admin_body_class($classes)
    {
        $current_screen = get_current_screen();

        if (empty ($current_screen)) return $classes;

        if (false !== strpos($current_screen->id, 'mglut-')) {
            // Leave space on both sides so other plugins do not conflict.
            $classes .= ' mglut-admin ';
        }

        return $classes;
    }

    public function admin_notices_bucket()
    {
        do_action('mglut_admin_notices');

    }

    public function act_on_request()
    {
        if ( ! empty($_GET['mglut_admin_action']) && !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['mglut_admin_action'])), 'appglut_memberglut_nonce')) { // 

            if (sanitize_text_field($_GET['mglut_admin_action']) == 'dismiss_leave_review_forever' && !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['mglut_admin_action'])), 'appglut_memberglut_nonce')) { // 
                update_option('mglut_dismiss_leave_review_forever', true);
            }

            wp_safe_redirect(esc_url_raw(remove_query_arg('mglut_admin_action')));
            exit;
        }
    }

    public function test_mode_notice()
    {
        if (mglut_is_test_mode() && current_user_can('manage_options')) {
            $link = add_query_arg(
                ['view' => 'payments', 'section' => 'payment-methods'],
                MGLUT_SETTINGS_SETTING_PAGE
            );
           /* translators: %s is a placeholder */
            $notice = sprintf(__('<strong>Important:</strong> No real payment is being processed because MemberGlut is in test mode. Go to <a href="%s">Payment method settings</a> to disable test mode.', 'memberglut'), $link);
            ?>
            <div class="notice notice-warning">
                <p><?php echo esc_html($notice); ?></p>
            </div>
            <?php
        }
    }

    /**
     * Display one-time admin notice to review plugin at least 7 days after installation
     */
    public function review_plugin_notice()
    {
        if ( ! current_user_can('manage_options')) return;

        if (get_option('mglut_dismiss_leave_review_forever', false)) return;

        $install_date = get_option('mglut_install_date', '');

        if (empty($install_date)) return;

        $diff = round((time() - strtotime($install_date)) / 24 / 60 / 60);

        if ($diff < 7) return;

        $review_url = 'https://wordpress.org/support/plugin/memberglut/reviews/?filter=5#new-post';

        $dismiss_url = esc_url(add_query_arg('mglut_admin_action', 'dismiss_leave_review_forever'));
        /* translators: %s is a placeholder */
        $notice = sprintf(
                    /* translators: %s is a placeholder */
            __('Hey, I noticed you have been using MemberGlut for at least 7 days now - that\'s awesome! Could you please do us a BIG favor and give it a %1$s5-star rating on WordPress?%2$s This will help us spread the word and boost our motivation - thanks!', 'memberglut'),
            '<a href="' . esc_url($review_url) . '" target="_blank">',
            '</a>'
        );
        $label  = __('Sure! I\'d love to give a review', 'memberglut');

        $dismiss_label = __('Dismiss Forever', 'memberglut');

        $notice .= "<div style=\"margin:10px 0 0;\"><a href=\"$review_url\" target='_blank' class=\"button-primary\">$label</a></div>";
        $notice .= "<div style=\"margin:10px 0 0;\"><a href=\"$dismiss_url\">$dismiss_label</a></div>";

        echo '<div data-dismissible="mglut-review-plugin-notice-forever" class="update-nag notice notice-warning is-dismissible">';
        echo "<p>".wp_kses_post($notice)."</p>";
        echo '</div>';
    }

    public function seo_friendly_permalink_not_set()
    {

        if (is_admin() && current_user_can('administrator') && ! get_option('permalink_structure')) {

                    /* translators: %s is a placeholder */
            $change_permalink_button = sprintf(
                '<a class="button" href="%s">%s</a>',
                admin_url('options-permalink.php'),
                __('Change Permalink Structure', 'memberglut')
            );
            /* translators: %s is a placeholder */
            $notice = sprintf(
            /* translators: %s is a placeholder */
                __("Your site permalink structure is currently set to <code>Plain</code>. This setting is not compatible with MemberGlut. Change your permalink structure to any other setting to avoid issues. We recommend <code>Post name</code>.</p><p>%s", 'memberglut'),
                esc_url($change_permalink_button)
            );

            echo '<div data-dismissible="mglut_seo_friendly_permalink_not_set-2" class="update-nag notice notice-warning is-dismissible">';
            echo "<p>".wp_kses_post($notice)."</p>";
            echo '</div>';
        }
    }


    /**
     * Notice when user registration is disabled.
     */
    function registration_disabled_notice()
    {
        if ( ! current_user_can('manage_options')) return;

        if (get_option('users_can_register') || apply_filters('mglut_remove_registration_disabled_notice', false)) {
            return;
        }

        $url = is_multisite() ? network_admin_url('settings.php') : admin_url('options-general.php');

        ?>
        <div data-dismissible="pp-registration-disabled-notice-forever" id="message" class="updated notice is-dismissible">
            <p>
                
                <?php 
                /* translators: %s is a placeholder */
                printf(esc_html__('User registration currently disabled. To enable, Go to <a href="%1$s">Settings -> General</a>, and under Membership, check "Anyone can register"', 'memberglut'), esc_url($url)); ?>
                . </p>
        </div>
        <?php
    }

    public function removable_query_args($args = [])
    {
        $args[] = 'settings-updated';
        $args[] = 'rule-updated';
        $args[] = 'settings-added';
        $args[] = 'field-edited';
        $args[] = 'field-added';
        $args[] = 'updated-contact-info';
        $args[] = 'form-added';
        $args[] = 'form-edited';
        $args[] = 'user-profile-added';
        $args[] = 'user-profile-edited';
        $args[] = 'melange-edited';
        $args[] = 'melange-added';
        $args[] = 'license';

        return $args;
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