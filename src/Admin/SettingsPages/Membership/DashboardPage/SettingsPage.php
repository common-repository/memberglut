<?php

namespace MemberGlut\Core\Admin\SettingsPages\Membership\DashboardPage;

use MemberGlut\Core\Admin\SettingsPages\AbstractSettingsPage;
use MemberGlut\Custom_Settings_Page_Api;

class SettingsPage extends AbstractSettingsPage
{
    function __construct()
    {
        add_action('mglut_register_menu_page', [$this, 'register_cpf_settings_page']);
        add_action('mglut_admin_settings_page_dashboard', [$this, 'reports_admin_page']);
    }

    public function admin_page_title()
    {
        return apply_filters(
            'mglut_membership_dashboard_settings_page_title',
            esc_html__('Dashboard', 'memberglut')
        );
    }

    public function register_cpf_settings_page()
    {
        $hook = add_submenu_page(
            MGLUT_DASHBOARD_SETTINGS_SLUG,
            $this->admin_page_title() . ' - MemberGlut',
            esc_html__('Dashboard', 'memberglut'),
            'manage_options',
            MGLUT_DASHBOARD_SETTINGS_SLUG,
            array($this, 'admin_page_callback'));

        do_action('mglut_membership_reports_settings_page_register', $hook);
    }


    public function default_header_menu()
    {
        return 'dashboard';
    }

    public function reports_admin_page()
    {
        add_action('mglut_cspa_main_content_area', [$this, 'admin_settings_page_callback'], 10, 2);

        $instance = Custom_Settings_Page_Api::instance();
        $instance->option_name('ppview'); // adds ppview css class to #poststuff
        $instance->form_method('get');
        $instance->page_header($this->admin_page_title());
        $instance->build(true);
    }

  

    public function admin_settings_page_callback()
    {
        echo '<input type="hidden" name="page" value="' .esc_url(MGLUT_DASHBOARD_SETTINGS_SLUG) . '" />';
        echo '<div class="mglut-report-charts-container">';
        $this->top_cards();
        $this->mglut_dashboard_welcome();
        echo '</div>';
    }

 /**
     * @return void
     */
    public function top_cards()
    {
        ?>
        <div class="mglut-report-chart-top-cards-wrap">
            <dl class="mglut-report-chart-top-card-item-wrap">
                <div class="mglut-report-chart-top-card-item">
                    <dt class="mglut-report-chart-top-card-item-header"><?php esc_html_e('Total Members', 'memberglut')?></dt>
                    <dd class="mglut-report-chart-top-card-item-content-wrap">
                        <div class="mglut-report-chart-top-card-item-content"><?php esc_html_e('0', 'memberglut') ?></div>
                    </dd>
                </div>

                <div class="mglut-report-chart-top-card-item">
                    <dt class="mglut-report-chart-top-card-item-header"><?php esc_html_e('Total Members', 'memberglut')?></dt>
                    <dd class="mglut-report-chart-top-card-item-content-wrap">
                        <div class="mglut-report-chart-top-card-item-content"><?php esc_html_e('0', 'memberglut') ?></div>
                    </dd>
                </div>

                <div class="mglut-report-chart-top-card-item">
                    <dt class="mglut-report-chart-top-card-item-header"><?php esc_html_e('Total Orders', 'memberglut')?></dt>
                    <dd class="mglut-report-chart-top-card-item-content-wrap">
                        <div class="mglut-report-chart-top-card-item-content"><?php esc_html_e('0', 'memberglut') ?></div>
                    </dd>
                </div>

            </dl>
        </div>
        <?php
}

    /**
     * Callback function for mglut_dashboard_welcome meta box.
     */
    public function mglut_dashboard_welcome()
    {?>
<div class="mglut-dashboard-welcome-columns">
    <div class="mglut-dashboard-welcome-column">
        <?php global $mglut_level_ready, $mglut_gateway_ready, $mglut_pages_ready;?>
        <h3><?php esc_html_e('Getting Started', 'memberglut');?></h3>
        <ul>
        <li>
    	<a href="<?php $add_new_plan = esc_url_raw(add_query_arg('mglut_subp_action', 'new', MGLUT_MEMBERSHIP_SUBSCRIPTION_PLANS_SETTINGS_PAGE));
        echo esc_html($add_new_plan); ?>"><i class="dashicons dashicons-shield"></i> <?php esc_html_e('Create a Membership Plan', 'memberglut');?></a>
    	</li>

    </div> <!-- end mglut-dashboard-welcome-column -->
    <div class="mglut-dashboard-welcome-column">
        <h3><?php esc_html_e('Explore MemberGlut', 'memberglut');?></h3>
        <ul>
            <li><a href="https://www.appglut.com/memberglut/docs" target="_blank"><i class="dashicons dashicons-admin-page"></i> <?php esc_html_e('Visit the User Guide', 'memberglut');?></a></li>
            <li><a href="https://www.youtube.com" target="_blank"><i class="dashicons dashicons-video-alt3"></i> <?php esc_html_e('Check the Video Documentation', 'memberglut');?></a></li>
        </ul>
        <hr />
        <p class="text-center">
            <?php echo esc_html(__('MemberGlut is Ultimate Membership Plugin that will help you manage your plans as your way.', 'memberglut')); ?>
        </p>
    </div> <!-- end mglut-dashboard-welcome-column -->
    <div class="mglut-dashboard-welcome-column">
        <h3><?php esc_html_e('Become a Member', 'memberglut');?></h3>
        <p><?php esc_html_e('You can help-support through our these medias', 'memberglut');?></p>
        <ul>
            <li><a href="https://wordpress.org/plugins/memberglut/#reviews" target="_blank"><i class="dashicons dashicons-wordpress"></i> <?php esc_html_e('Give us a review at WordPress.org.', 'memberglut');?></a></li>
        </ul>
        <hr />
        <p><?php esc_html_e('Help translate MemberGlut into your language.', 'memberglut');?> <a href="https://translate.wordpress.org/projects/wp-plugins/memberglut" target="_blank"><?php esc_html_e('Translation Dashboard', 'memberglut');?></a></p>
    </div> <!-- end mglut-dashboard-welcome-column -->
</div> <!-- end mglut-dashboard-welcome-columns -->
<?php
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