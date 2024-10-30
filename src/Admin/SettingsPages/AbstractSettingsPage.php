<?php
// 


namespace MemberGlut\Core\Admin\SettingsPages;

use MemberGlut\Custom_Settings_Page_Api;

if ( ! defined('ABSPATH')) {
    exit;
}

abstract class AbstractSettingsPage
{
    protected $option_name;

    public static $parent_menu_url_map = [];

    private function getMenuIcon()
    {
        return 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB3aWR0aD0iNDAiIHpvb21BbmRQYW49Im1hZ25pZnkiIHZpZXdCb3g9IjAgMCAzMCAzMC4wMDAwMDEiIGhlaWdodD0iNDAiIHByZXNlcnZlQXNwZWN0UmF0aW89InhNaWRZTWlkIG1lZXQiIHZlcnNpb249IjEuMCI+PGRlZnM+PGNsaXBQYXRoIGlkPSIyZjc3ZGIyNjNlIj48cGF0aCBkPSJNIDYgMTMgTCAyNCAxMyBMIDI0IDI4LjMxNjQwNiBMIDYgMjguMzE2NDA2IFogTSA2IDEzICIgY2xpcC1ydWxlPSJub256ZXJvIi8+PC9jbGlwUGF0aD48Y2xpcFBhdGggaWQ9IjBlYmQ0MzhkY2QiPjxwYXRoIGQ9Ik0gMTUgMSBMIDI5LjA2MjUgMSBMIDI5LjA2MjUgMjEgTCAxNSAyMSBaIE0gMTUgMSAiIGNsaXAtcnVsZT0ibm9uemVybyIvPjwvY2xpcFBhdGg+PGNsaXBQYXRoIGlkPSI2NWRkMDcyY2Q2Ij48cGF0aCBkPSJNIDAuNzU3ODEyIDEgTCAxOSAxIEwgMTkgMjEgTCAwLjc1NzgxMiAyMSBaIE0gMC43NTc4MTIgMSAiIGNsaXAtcnVsZT0ibm9uemVybyIvPjwvY2xpcFBhdGg+PC9kZWZzPjxnIGNsaXAtcGF0aD0idXJsKCMyZjc3ZGIyNjNlKSI+PHBhdGggZmlsbD0iI2ZmZmZmZiIgZD0iTSA2LjI2NTYyNSAyMy4xOTUzMTIgTCA2LjMzNTkzOCAxMy4xNTYyNSBMIDguNTU0Njg4IDE0LjQ2MDkzOCBMIDguNTAzOTA2IDIxLjkyNTc4MSBMIDEyLjI1IDI0LjEyNSBDIDExLjYxNzE4OCAyMi43NTc4MTIgMTIuMTI1IDIxLjExMzI4MSAxMy40NTcwMzEgMjAuMzU5Mzc1IEMgMTQuODU5Mzc1IDE5LjU2MjUgMTYuNjQ0NTMxIDIwLjA1MDc4MSAxNy40NDE0MDYgMjEuNDUzMTI1IEMgMTcuOTUzMTI1IDIyLjM1MTU2MiAxNy45MzM1OTQgMjMuNDA2MjUgMTcuNDg0Mzc1IDI0LjI1NzgxMiBMIDIxLjQzMzU5NCAyMi4wMTU2MjUgTCAyMS40ODQzNzUgMTQuNTUwNzgxIEwgMjMuNzIyNjU2IDEzLjI3NzM0NCBMIDIzLjY1MjM0NCAyMy4zMTY0MDYgTCAxNC45MjU3ODEgMjguMjc3MzQ0IEwgNi4yNjU2MjUgMjMuMTk1MzEyICIgZmlsbC1vcGFjaXR5PSIxIiBmaWxsLXJ1bGU9Im5vbnplcm8iLz48L2c+PGcgY2xpcC1wYXRoPSJ1cmwoIzBlYmQ0MzhkY2QpIj48cGF0aCBmaWxsPSIjZmZmZmZmIiBkPSJNIDIyLjU1NDY4OCA2LjEwNTQ2OSBDIDIzLjQyMTg3NSA1LjE5NTMxMiAyMy42MzI4MTIgMy43ODUxNTYgMjIuOTc2NTYyIDIuNjM2NzE5IEMgMjIuMTc1NzgxIDEuMjM0Mzc1IDIwLjM5MDYyNSAwLjc1IDE4Ljk4ODI4MSAxLjU1MDc4MSBDIDE3LjU4OTg0NCAyLjM1MTU2MiAxNy4xMDE1NjIgNC4xMzY3MTkgMTcuOTA2MjUgNS41MzkwNjIgQyAxOC4wNDI5NjkgNS43NzczNDQgMTguMjEwOTM4IDUuOTkyMTg4IDE4LjM5ODQzOCA2LjE3NTc4MSBMIDE1LjQ0NTMxMiA3Ljg2NzE4OCBMIDE3LjY2Nzk2OSA5LjE2NDA2MiBMIDIwLjU0Njg3NSA3LjUxNTYyNSBMIDI2Ljk5NjA5NCAxMS4yNzM0MzggTCAyNi45Njg3NSAxOC43MzgyODEgTCAyOS4xOTUzMTIgMjAuMDM1MTU2IEwgMjkuMjMwNDY5IDkuOTk2MDk0IEwgMjIuNTU0Njg4IDYuMTA1NDY5ICIgZmlsbC1vcGFjaXR5PSIxIiBmaWxsLXJ1bGU9Im5vbnplcm8iLz48L2c+PHBhdGggZmlsbD0iI2ZmZmZmZiIgZD0iTSAxMi4yODkwNjIgOS42NzE4NzUgTCAxMS44NDM3NSA5LjkyOTY4OCBMIDExLjgwNDY4OCAxOS45Njg3NSBMIDE0LjAzOTA2MiAxOC42ODc1IEwgMTQuMDY2NDA2IDExLjIyMjY1NiBMIDE0LjUxMTcxOSAxMC45Njg3NSBMIDEyLjI4OTA2MiA5LjY3MTg3NSAiIGZpbGwtb3BhY2l0eT0iMSIgZmlsbC1ydWxlPSJub256ZXJvIi8+PGcgY2xpcC1wYXRoPSJ1cmwoIzY1ZGQwNzJjZDYpIj48cGF0aCBmaWxsPSIjZmZmZmZmIiBkPSJNIDExLjkyOTY4OCAyLjYzNjcxOSBDIDEyLjU4NTkzOCAzLjc4NTE1NiAxMi4zNzUgNS4xOTUzMTIgMTEuNTA3ODEyIDYuMTA1NDY5IEwgMTguMTgzNTk0IDkuOTk2MDk0IEwgMTguMTQ0NTMxIDIwLjAzNTE1NiBMIDE1LjkyMTg3NSAxOC43MzgyODEgTCAxNS45NDkyMTkgMTEuMjczNDM4IEwgOS41IDcuNTE1NjI1IEwgMy4wMTk1MzEgMTEuMjIyNjU2IEwgMi45OTIxODggMTguNjg3NSBMIDAuNzU3ODEyIDE5Ljk2ODc1IEwgMC43OTY4NzUgOS45Mjk2ODggTCA3LjM1MTU2MiA2LjE3NTc4MSBDIDcuMTY0MDYyIDUuOTkyMTg4IDYuOTk2MDk0IDUuNzc3MzQ0IDYuODU5Mzc1IDUuNTM5MDYyIEMgNi4wNTQ2ODggNC4xMzY3MTkgNi41NDI5NjkgMi4zNTE1NjIgNy45NDE0MDYgMS41NTA3ODEgQyA5LjM0Mzc1IDAuNzUgMTEuMTI4OTA2IDEuMjM0Mzc1IDExLjkyOTY4OCAyLjYzNjcxOSAiIGZpbGwtb3BhY2l0eT0iMSIgZmlsbC1ydWxlPSJub256ZXJvIi8+PC9nPjwvc3ZnPg==';
    }

    public function register_core_menu()
    {
        add_menu_page(
            esc_html__('MemberGlut - Wordpress Membership Plugin', 'memberglut'),
            'MemberGlut',
            'manage_options',
            MGLUT_DASHBOARD_SETTINGS_SLUG,
            '',
            $this->getMenuIcon(),
            '50.0015'
        );

        do_action('mglut_register_menu_page_' . $this->active_menu_tab() . '_' . $this->active_submenu_tab());

        do_action('mglut_register_menu_page');

        add_filter('list_pages', function ($title, $page) {
            return sprintf('%s (ID: %s)', $title, $page->ID);
        }, 10, 2);
    }

    public function header_menu_tabs()
    {
        return [];
    }

    public function header_submenu_tabs()
    {
        return [];
    }

    public function settings_page_header($active_menu, $active_submenu)
    {
        $logo_url       = MGLUT_ASSETS_URL . '/images/memberglut-logo.svg';
        $submenus_count = count($this->header_menu_tabs());
        ?>
        <div class="mglut-admin-wrap">
            <div class="mglut-admin-banner mglut-not-pro">
                <div class="mglut-admin-banner__logo">
                    <img src="<?php echo esc_url($logo_url); ?>" alt="">
                </div>
                <div class="mglut-admin-banner__helplinks">
                   
                    <span><a rel="noopener" href="https://appglut.com/memberglut/docs/" target="_blank">
                    <span class="dashicons dashicons-book"></span> <?php echo esc_html__('Documentation', 'memberglut'); ?>
                </a></span>
                </div>
                <div class="clear"></div>
                <?php $this->settings_page_header_menus($active_menu); ?>
            </div>
            <?php

            $submenus = $this->header_submenu_tabs();

            if ( ! empty($submenus) && count($submenus) > 1) {
                $this->settings_page_header_sub_menus($active_menu, $active_submenu);
            }

            ?>
        </div>
        <?php
        do_action('mglut_settings_page_header', $active_menu, $active_submenu);
    }

    public function settings_page_header_menus($active_menu)
    {
        $menus = $this->header_menu_tabs();

        if (count($menus) < 2) return;
    }

    public function settings_page_header_sub_menus($active_menu, $active_submenu)
    {
        $submenus = $this->header_submenu_tabs();

        if (count($submenus) < 2) return;

        $active_menu_url = self::$parent_menu_url_map[$active_menu];

        $submenus = wp_list_filter($submenus, ['parent' => $active_menu]);

        echo '<ul class="subsubsub">';

        foreach ($submenus as $submenu) {

            printf(
                '<li><a href="%s"%s>%s</a></li>',
                esc_url(add_query_arg('section', $submenu['id'], $active_menu_url)),
                $active_submenu == $submenu['id'] ? ' class="mglut-current"' : '',
                esc_html($submenu['label'])
            );
        }
        echo '</ul>';
    }

    public function active_menu_tab()
    {
        if (isset($_GET['page']) && !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['page'])), 'appglut_memberglut_nonce') && strpos(sanitize_text_field($_GET['page']), 'mglut') !== false) {
            return $this->default_header_menu();
        }        

        return false;
    }

    public function active_submenu_tab()
    {
        if (isset($_GET['page']) && !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['page'])), 'appglut_memberglut_nonce') && strpos(sanitize_text_field($_GET['page']), 'mglut') !== false) {

            $active_menu = $this->active_menu_tab();

            $submenu_tabs      = wp_list_filter($this->header_submenu_tabs(), ['parent' => $active_menu]);
            $first_submenu_tab = '';
            if ( ! empty($submenu_tabs)) {
                $first_submenu_tab = array_values($submenu_tabs)[0]['id'];
            }

            return (isset($_GET['section']) && !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['page'])), 'appglut_memberglut_nonce') && isset($_GET['view']) && sanitize_text_field($_GET['view']) === 'general' && $active_menu === 'general') ? esc_attr(sanitize_text_field($_GET['section'])) : esc_attr($first_submenu_tab);

            
        }

        return false;
    }

    public function admin_page_callback()
    {
        $active_menu = $this->active_menu_tab();

        $active_submenu = $this->active_submenu_tab();

        $this->settings_page_header($active_menu, $active_submenu);

        do_action('mglut_admin_settings_page_' . $active_menu);

        do_action('mglut_admin_settings_submenu_page_' . $active_menu . '_' . $active_submenu);
    }
    /** --------------------------------------------------------------- */

    /**
     * Register core settings.
     *
     * @param Custom_Settings_Page_Api $instance
     * @param bool $remove_sidebar
     */
    public static function register_core_settings(Custom_Settings_Page_Api $instance, $remove_sidebar = false)
    {
        if ( ! $remove_sidebar) {
            $instance->sidebar(self::sidebar_args());
        }
    }

    public static function sidebar_args()
    {
        $sidebar_args = [

        ];


        return $sidebar_args;
    }


    public static function page_dropdown($id, $appends = [], $args = ['skip_append_default_select' => false])
    {
        $default_args = [
            'name'             => MGLUT_SETTINGS_DB_OPTION_NAME . "[$id]",
            'show_option_none' => esc_html__('Select...', 'memberglut'),
            'selected'         => mglut_get_setting($id, ''),
            'echo'             => false
        ];

        if ( ! empty($appends)) {
            unset($default_args['show_option_none']);
        }

        $html = wp_dropdown_pages(
            wp_kses_post(array_replace($default_args, $args))
        );

        if ( ! empty($appends)) {
            $addition = '';

            if (mglut_var($args, 'skip_append_default_select') === false) {
                $addition .= '<option value="">' . esc_html__('Select...', 'memberglut') . '</option>';
            }

            foreach ($appends as $append) {
                $key           = $append['key'];
                $label         = $append['label'];
                $disabled_attr = mglut_var($append, 'disabled') === true ? ' disabled' : '';
                $addition      .= "<option value=\"$key\"" . selected(mglut_get_setting($id), $key, false) . $disabled_attr . '>' . $label . '</option>';
            }

            $html = mglut_append_option_to_select($addition, $html);
        }

        return $html;
    }

    protected function custom_text_input($id, $placeholder = '')
    {
        $placeholder = ! empty($placeholder) ? $placeholder : esc_html__('Custom URL Here', 'memberglut');
        $value       = mglut_get_setting($id, '');

        return "<input placeholder=\"$placeholder\" name=\"" . MGLUT_SETTINGS_DB_OPTION_NAME . "[$id]\" type=\"text\" class=\"regular-text code\" value=\"$value\">";
    }
}