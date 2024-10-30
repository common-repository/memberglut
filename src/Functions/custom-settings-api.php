<?php

namespace MemberGlut;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

ob_start();

class Custom_Settings_Page_Api
{
    /** @var mixed|void database saved data. */
    private $db_options = [];

    /** @var string option name for database saving. */
    private $option_name = '';

    private $form_method = 'post';

    /** @var array config of settings page tabs */
    private $tabs_config = [];

    /** @var array config of main settings page */
    private $main_content_config = [];

    private $remove_white_design = false;
    private $remove_h2_header = false;
    private $header_without_frills = false;

    /** @var array config of settings page sidebar */
    private $sidebar_config = [];

    /** @var string header title of the page */
    private $page_header = '';

    private $view_classes = '';

    private $wrap_classes = 'ppview';

    private $exclude_top_tav_nav = false;

    private $remove_nonce_field = false;

    private $remove_page_form_tag = false;

    protected function __construct($main_content_config = [], $option_name = '', $page_header = '')
    {
        $this->db_options          = get_option($option_name, []);
        $this->db_options          = !is_array($this->db_options) || empty($this->db_options) ? [] : $this->db_options;
        $this->option_name         = $option_name;
        $this->main_content_config = $main_content_config;
        $this->page_header         = $page_header;

        $this->persist_plugin_settings();
    }

    /**
     * set this as late as possible.
     *
     * @param $db_options
     */
    public function set_db_options($db_options)
    {
        $this->db_options          = $db_options;
    }

    public function option_name($val)
    {
        $this->db_options          = get_option($val, []);
        $this->db_options          = empty($this->db_options) ? [] : $this->db_options;
        $this->option_name = $val;
    }

    public function form_method($val)
    {
        $this->form_method = $val;
    }

    public function tab($val)
    {
        $this->tabs_config = $val;
    }

    public function main_content($val)
    {
        $this->main_content_config = $val;
    }

    public function add_view_classes($classes)
    {
        $this->view_classes = $classes;
    }

    public function add_wrap_classes($classes)
    {
        $this->wrap_classes = $classes;
    }

    public function remove_nonce_field()
    {
        $this->remove_nonce_field = true;
    }

    public function remove_page_form_tag()
    {
        $this->remove_page_form_tag = true;
    }

    public function remove_white_design()
    {
        $this->remove_white_design = true;
    }

    public function remove_h2_header()
    {
        $this->remove_h2_header = true;
    }

    public function header_without_frills()
    {
        $this->header_without_frills = true;
    }

    public function sidebar($val)
    {
        $this->sidebar_config = $val;
    }

    public function page_header($val)
    {
        $this->page_header = $val;
    }

    public function settings_page_tab()
    {
        if ($this->exclude_top_tav_nav === true) return;

        $args = $this->tabs_config;

        $html = '';
        if ( ! empty($args)) {
            $html .= '<h2 class="nav-tab-wrapper">';
            foreach ($args as $arg) {
                $url    = esc_url_raw(@$arg['url']);
                $label  = esc_html(@$arg['label']);
                $class  = esc_attr(@$arg['class']);
                $style  = esc_attr(@$arg['style']);
                $remove_args = ['type', 'settings-updated', 'ppsc', 'license', 'mc-audience', 'cm-email-list', 'id', 'contact-info', 'edit'];
                $sanitized_args = array_map('sanitize_text_field', $remove_args);
                $active = (remove_query_arg($sanitized_args) === $url) ? ' nav-tab-active' : null;
                                $html   .= "<a href=\"$url\" class=\"$class nav-tab{$active}\" style='$style'>$label</a>";
            }

            $html .= '</h2>';
        }

        echo wp_kses_post(apply_filters('mglut_cspa_settings_page_tab', $html));

        do_action('mglut_cspa_settings_after_tab');
    }


    public function setting_page_sidebar()
    {
        $custom_sidebar = apply_filters('mglut_cspa_settings_page_sidebar', '');

        if ( ! empty($custom_sidebar)) {
            echo wp_kses_post($custom_sidebar);
            return;
        }

        if ( ! empty($this->sidebar_config)):
        ?>
            <div id="postbox-container-1" class="postbox-container">
            <div class="meta-box-sortables">
                    <?php foreach ($this->sidebar_config as $arg) : ?>
                        <div class="postbox">
                            <div class="postbox-header">
                                <h3 class="hndle is-non-sortable">
                                    <span><?php echo esc_html($arg['section_title']); ?></span>
                                </h3>
                                <div class="handle-actions hide-if-no-js">
                                    <button type="button" class="handlediv" aria-expanded="true">
                                        <span class="toggle-indicator" aria-hidden="true"></span>
                                    </button>
                                </div>
                            </div>

                            <div class="inside">
                                <?php echo esc_html($arg['content']); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
            </div>
        </div>
        <?php endif;
    }

    /**
     * Helper function to recursively sanitize POSTed data.
     *
     * @param $data
     *
     * @return string|array
     */
    public static function sanitize_data($data)
    {
        if (is_string($data)) {
            return esc_html($data);
        }

        $sanitized_data = [];
        foreach ($data as $key => $value) {
            // skip sanitation. useful for fields that expects html
            if (($cValue = apply_filters('mglut_cspa_sanitize_skip', false, $key, $value))) {
                $sanitized_data[$key] = stripslashes($cValue);
                continue;
            }

            if (is_array($data[$key])) {
                $sanitized_data[$key] = self::sanitize_data($data[$key]);
            } else {
                $sanitized_data[$key] = esc_html(stripslashes($data[$key]));
            }
        }

        return $sanitized_data;
    }

    /**
     * Persist the form data to database.
     *
     * @return \WP_Error|Void
     */
    public function persist_plugin_settings()
    {
        add_action('admin_notices', array($this, 'do_settings_errors'));

        if ( ! current_user_can('manage_options')) {
            return;
        }

        if (empty($_POST['save_' . $this->option_name])) {
            return;
        }

        check_admin_referer('mglut-verify-none', 'mglut_verify_none');

        /**
         * Use add_settings_error() to create/generate an errors add_settings_error('wp_csa_notice', '', 'an error');
         * in your validation function/class method hooked to this action.
         */

        $settings_error = get_settings_errors('wp_csa_notice');
        if ( ! empty($settings_error)) {
            return;
        }

        $sanitize_callable = apply_filters('mglut_cspa_sanitize_callback', [self::class, 'sanitize_data']);

        $sanitized_data = apply_filters(
            'mglut_cspa_santized_data',
            call_user_func($sanitize_callable, sanitize_text_field($_POST[$this->option_name])),
            $this->option_name
        );

        // skip unchanged (with asterisk ** in its data) api key/token values.
        foreach ($sanitized_data as $key => $value) {
            if (is_string($value) && strpos($value, '**') !== false) {
                unset($sanitized_data[$key]);
            }
        }

        do_action('mglut_cspa_persist_settings', $sanitized_data, $this->option_name, $this);

        if ( ! apply_filters('mglut_cspa_disable_default_persistence', false)) {

           update_option($this->option_name, $sanitized_data);

            do_action('mglut_cspa_after_persist_settings', $sanitized_data, $this->option_name);

            wp_safe_redirect(esc_url_raw(add_query_arg('settings-updated', 'true')));
            exit;
        }
    }

    /**
     * Do settings page error
     */
    public function do_settings_errors()
    {
        $success_notice = apply_filters('mglut_cspa_success_notice', 'Settings saved.', $this->option_name);
        if (isset($_GET['settings-updated']) && !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['settings-updated'])), 'appglut_memberglut_nonce') && (sanitize_text_field($_GET['settings-updated']) == 'true')) : // ?> 
            <?php add_settings_error('wp_csa_notice', 'wp_csa_settings_updated', $success_notice, 'updated'); ?>
        <?php endif; ?>
        <?php
    }

    

    public function settings_page_heading()
    {
        $style = '';

        if($this->remove_h2_header) {
            $style =' style="display:none;"';
        }

        printf('<h2%s>', esc_html($style));
        echo wp_kses_post($this->page_header);
        do_action('mglut_cspa_before_closing_header');
        echo '</h2>';
        do_action('mglut_cspa_after_header');
    }

    public function nonce_field()
    {
        if($this->remove_nonce_field) return;
        printf('<input id="mglut_verify_none" type="hidden" name="mglut_verify_none" value="%s">', esc_html(wp_create_nonce('mglut-verify-none')));
    }


    /**
     * Main settings page markup.
     *
     * @param bool $return_output
     */
    public function _settings_page_main_content_area($return_output = false)
    {
        $args_arrays = $this->main_content_config;

        // variable declaration
        $html = '';

        if ( ! empty($args_arrays)) {
            foreach ($args_arrays as $args_array) {
                $html .= $this->metax_box_instance($args_array);
            }
        }

        if ($return_output) {
            return $html;
        }

        echo wp_kses_post($html);
    }

    /**
     * @param $args
     *
     * @return string
     */
    public function metax_box_instance($args)
    {
        $db_options = $this->db_options;

        $html = '';

        if ($this->header_without_frills === true) {
            $html .= $this->_header_without_frills($args);
        } else {
            $html .= $this->_header($args);
        }

        $disable_submit_button = isset($args['disable_submit_button']) ? true : false;

        // remove section title from array to make the argument keys be arrays so it can work with foreach loop
        if (isset($args['section_title'])) {
            unset($args['section_title']);
        }

        if (isset($args['disable_submit_button'])) {
            unset($args['disable_submit_button']);
        }

        foreach ($args as $key => $value) {
            $field_type = '_' . $args[$key]['type'];
            $html .= $this->{$field_type}($db_options, $key, $args[$key]);
        }

        if ($disable_submit_button) {
            $html .= $this->_footer_without_button();
        } else {
            $html .= $this->_footer();
        }

        return $html;
    }

    /**
     * @param int $id
     * @param string $name
     * @param string $value
     * @param string $class
     * @param string $placeholder
     * @param string $data_key
     */
    public function _text_field($id, $name, $value, $class = 'regular-text', $placeholder = '', $data_key = '')
    {
        $id = !empty($id) ? $id : '';
        $data_key = !empty($data_key) ? "data-index='$data_key'" : null;
        $value = !empty($value) ? $value : null;

        ?>
        <input type="text" placeholder="<?php echo esc_attr($placeholder); ?>" id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($name); ?>" class="<?php echo esc_attr($class); ?>" value="<?php echo esc_attr($value); ?>" <?php echo esc_attr($data_key); ?>/>
        <?php
do_action('mglut_cspa_field_before_text_field', $id);
    }
    
    public function _color($db_options, $key, $args) {
        $args['class'] = 'mglut-color-picker';
        return $this->_text($db_options, $key, $args);
    }

    /**
     * Renders the text field
     *
     * @param array $db_options addons DB options
     * @param string $key array key of class argument
     * @param array $args class args
     *
     * @return string
     */
    public function _text($db_options, $key, $args)
    {
        $key = mglut_sanitize_key($key);
        $label = esc_attr($args['label']);
        $defvalue = sanitize_text_field(@$args['value']);
        $description = @$args['description'];
        $tr_id = isset($args['tr_id']) ? $args['tr_id'] : "{$key}_row";
        $option_name = $this->option_name;
        $name_attr = $option_name . '[' . $key . ']';
        $value = !empty($db_options[$key]) ? $db_options[$key] : $defvalue;
        $class = 'regular-text ' . esc_attr(mglut_var($args, 'class', ''));
        $placeholder = mglut_var($args, 'placeholder', '');

        if (isset($args['obfuscate_val']) && in_array($args['obfuscate_val'], [true, 'true'])) {
            $value = $this->obfuscate_string($value);
        }

        ob_start();?>
        <tr id="<?php echo esc_attr($tr_id); ?>">
            <th scope="row"><label for="<?php echo esc_attr($key); ?>"><?php echo esc_attr($label); ?></label></th>
            <td>
                <?php do_action('mglut_cspa_before_text_field', $db_options, $option_name, $key, $args);?>
                <?php $this->_text_field($key, $name_attr, $value, $class, $placeholder);?>
                <?php do_action('mglut_cspa_after_text_field', $db_options, $option_name, $key, $args);?>
                <p class="description"><?php echo wp_kses_post($description); ?></p>
            </td>
        </tr>
        <?php
return ob_get_clean();
    }

 


    /**
     * Renders the select dropdown
     *
     * @param array $db_options addons DB options
     * @param string $key array key of class argument
     * @param array $args class args
     *
     * @return string
     */
    public function _select($db_options, $key, $args)
    {
        $key = esc_attr($key);
        $label = esc_attr($args['label']);
        $description = @$args['description'];
        $tr_id = isset($args['tr_id']) ? $args['tr_id'] : "{$key}_row";
        $disabled = isset($args['disabled']) && $args['disabled'] === true ? 'disabled="disabled"' : '';
        $options = $args['options'];
        $default_select_value = @$args['value'];
        $option_name = $this->option_name;
        $attributes = @$args['attributes'];
        $attributes_output = '';
        if (is_array($attributes) && !empty($attributes)) {
            foreach ($attributes as $attr => $val) {
                $attributes_output .= sprintf(' %s = "%s"', $attr, $val);
            }
        }
        ob_start()?>
        <tr id="<?php echo esc_attr($tr_id); ?>">
            <th scope="row"><label for="<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></label></th>
            <td>
                <?php do_action('mglut_cspa_before_select_dropdown', $db_options, $option_name, $key, $args);?>
                <select id="<?php echo esc_attr($key); ?>" name="<?php echo esc_attr($option_name), '[', esc_attr($key), ']'; ?>" <?php echo esc_attr($disabled); ?><?php echo esc_attr($attributes_output); ?>>
                    <?php foreach ($options as $option_key => $option_value): ?>
                        <?php if (is_array($option_value)): ?>
                            <optgroup label="<?php echo esc_attr($option_key) ?>">
                                <?php foreach ($option_value as $key2 => $value2): ?>
                                    <option value="<?php echo esc_attr($key2); ?>" <?php !empty($db_options[$key]) ? selected($db_options[$key], $key2) : selected($key2, $default_select_value);?>><?php echo esc_attr($value2); ?></option>
                                <?php endforeach;?>
                            </optgroup>
                        <?php endif;?>

                        <?php if (!is_array($option_value)): ?>
                            <option value="<?php echo esc_attr($option_key); ?>" <?php !empty($db_options[$key]) ? selected($db_options[$key], $option_key) : selected($option_key, $default_select_value);?>><?php echo esc_attr($option_value); ?></option>
                        <?php endif;?>

                    <?php endforeach;?>
                </select>
                <?php do_action('mglut_cspa_after_select_dropdown', $db_options, $option_name, $key, $args);?>

                <p class="description"><?php echo wp_kses_post($description); ?></p>
            </td>
        </tr>
        <?php
return ob_get_clean();
    }


    /**
     * Section header
     *
     * @param string $section_title
     * @param mixed $args
     *
     * @return string
     */
public function _header($args)
{
    ob_start();
    ?>
    <div class="postbox">
        <?php do_action('mglut_cspa_header', $args, $this->option_name); ?>
        <?php if(!empty($args['section_title'])) : ?>
        <div class="postbox-header">
        <h3 class="hndle is-non-sortable"><span><?php echo esc_html($args['section_title']); ?></span></h3>
        <div class="handle-actions hide-if-no-js">
            <button type="button" class="handlediv" aria-expanded="true">
                <span class="toggle-indicator" aria-hidden="true"></span>
            </button>
        </div>
        </div>
        <?php endif; ?>
        <div class="inside">
            <table class="form-table">
                <?php
                return ob_get_clean();
                }


                /**
                 * Section header without the frills (title and toggle button).
                 *
                 * @return string
                 */
                public function _header_without_frills($args)
                {
                ob_start();
                ?>
                <div class="postbox">
                    <?php do_action('mglut_cspa_header', $args, $this->option_name); ?>
                    <div class="inside">
                        <table class="form-table">
    <?php
    return ob_get_clean();
}

    /**
     * Section footer.
     *
     * @return string
     */
    public function _footer($disable_submit_button = null)
    {
        return '</table>
		<p><input class="button-primary" type="submit" name="save_' . esc_attr($this->option_name) . '" value="'. esc_html__('Save Changes', 'memberglut'). '"></p>
	</div>
</div>';
    }

    /**
     * Section footer without "save changes" button.
     *
     * @return string
     */
    public function _footer_without_button()
    {
        return '</table>
	</div>
</div>';
    }

    /**
     * Build the settings page.
     *
     * @param bool $exclude_sidebar set to true to remove sidebar markup (.column-2)
     *
     * @return mixed|void
     */
    public function build($exclude_sidebar = false, $exclude_top_tav_nav = false)
    {
        $this->persist_plugin_settings();

        $this->exclude_top_tav_nav = $exclude_top_tav_nav;

        $columns2_class = !$exclude_sidebar ? ' columns-2' : null;

        $view_classes = '';
        if (!empty($this->view_classes)) {
            $view_classes = ' ' . $this->view_classes;
        }

        $wrap_classes = '';
        if (!empty($this->wrap_classes)) {
            $wrap_classes = ' ' . $this->wrap_classes;
        }

        ?>
        <div class="wrap<?php echo esc_attr($wrap_classes) ?>">
            <?php $this->settings_page_heading();?>
            <?php $this->do_settings_errors();?>
            <?php settings_errors('wp_csa_notice');?>
            <?php $this->settings_page_tab();?>
            <?php do_action('mglut_cspa_after_settings_tab', $this->option_name);?>
            <div id="poststuff" class="wp_csa_view <?php echo esc_attr($this->option_name); ?><?php echo esc_attr($view_classes); ?>">
                <?php do_action('mglut_cspa_before_metabox_holder_column');?>
                <div id="post-body" class="metabox-holder<?php echo esc_attr($columns2_class); ?>">
                    <div id="post-body-content">
                        <?php do_action('mglut_cspa_before_post_body_content', $this->option_name, $this->db_options);?>
                        <div class="meta-box-sortables ui-sortable">
                        <?php if (!$this->remove_page_form_tag): ?>
                            <form method="<?php echo esc_attr($this->form_method); ?>" <?php do_action('mglut_cspa_form_tag', $this->option_name);?>>
                        <?php endif;?>
                                <?php ob_start();?>
                                <?php $this->_settings_page_main_content_area();?>
                                <?php echo wp_kses_post(apply_filters('mglut_cspa_main_content_area', ob_get_clean(), $this->option_name)); ?>
                                <?php $this->nonce_field();?>
                        <?php if (!$this->remove_page_form_tag): ?>
                            </form>
                        <?php endif;?>
                        </div>
                    </div>
                    <?php $this->setting_page_sidebar();?>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * For building settings page with vertical sidebar tab menus.
     */
    public function build_sidebar_tab_style()
    {
        $settings_args = $this->main_content_config;
        $option_name = $this->option_name;

        do_action('mglut_before_settings_page', $option_name);
        $nav_tabs = '';
        $tab_content_area = '';

        if (!empty($settings_args)) {
            foreach ($settings_args as $key => $settings_arg) {
                $tab_title = @$settings_arg['tab_title'];
                $section_title = @$settings_arg['section_title'];
                $dashicon = isset($settings_arg['dashicon']) ? $settings_arg['dashicon'] : 'dashicons-admin-generic';
                unset($settings_arg['tab_title']);
                unset($settings_arg['section_title']);
                unset($settings_arg['dashicon']);

                $dashicon_html = '';
                if (!empty($dashicon)) {
                    /* translators: %s is a placeholder */
                    $dashicon_html = sprintf('<span class="dashicons %s"></span>', $dashicon);
                }
                /* translators: %s is a placeholder */
                $nav_tabs .= sprintf('<a href="#%1$s" class="nav-tab" id="%1$s-tab">%3$s %2$s</a>', $key, $tab_title, $dashicon_html);

                if (isset($settings_arg[0]['section_title'])) {
                    /* translators: %s is a placeholder */
                    $tab_content_area .= sprintf('<div id="%s" class="pp-group-wrapper">', $key);
                    foreach ($settings_arg as $single_arg) {
                        $tab_content_area .= $this->metax_box_instance($single_arg);
                    }
                    $tab_content_area .= '</div>';
                } else {
                    $settings_arg['section_title'] = $section_title;
                    /* translators: %s is a placeholder */
                    $tab_content_area .= sprintf('<div id="%s" class="pp-group-wrapper">', $key);
                    $tab_content_area .= $this->metax_box_instance($settings_arg);
                    $tab_content_area .= '</div>';
                }
            }

            $this->persist_plugin_settings();

            echo '<div class="wrap ppview">';
            $this->settings_page_heading();
            $this->do_settings_errors();
            settings_errors('wp_csa_notice');
            $this->settings_page_tab();

            echo '<div class="pp-settings-wrap" data-option-name="' . esc_attr($option_name) . '">';
            echo '<h2 class="nav-tab-wrapper">' . wp_kses_post($nav_tabs) . '</h2>';
            echo '<div class="metabox-holder pp-tab-settings">';
            echo '<form method="' . esc_attr($this->form_method) . '">';
            ob_start();
            $this->nonce_field();
           
            $allowed_html = array(
                'div' => array(
                    'class' => true,
                    'id' => true
                ),
                'h3' => array(
                    'class' => true
                ),
                'span' => array(),
                'table' => array(
                    'class' => true,
                ),
                'tbody' => array(),
                'tr' => array(
                    'id' => true,
                ),
                'th' => array(
                    'scope' => true,
                ),
                'td' => array(),
                'label' => array(
                    'for' => true,
                ),
                'input' => array(
                    'type' => true,
                    'placeholder' => true,
                    'id' => true,
                    'name' => true,
                    'class' => true,
                    'value' => true,
                ),
                'p' => array(
                    'class' => true,
                ),
                'select' => array(
                    'id' => true,
                    'name' => true,
                ),
                'option' => array(
                    'value' => true,
                ),
            );
            
            echo wp_kses($tab_content_area, $allowed_html);
            echo wp_kses(apply_filters('mglut_cspa_main_content_area', ob_get_clean(), $this->option_name), $allowed_html);
            echo '</form>';
            echo '</div>';
            echo '</div>';
            echo '</div>';

            do_action('mglut_after_settings_page', $option_name);
        }
    }

    /**
     * Custom_Settings_Page_Api
     *
     * @param array $main_content_config
     * @param string $option_name
     * @param string $page_header
     *
     * @return Custom_Settings_Page_Api
     */
    public static function instance($main_content_config = [], $option_name = '', $page_header = '')
    {
        return new self($main_content_config, $option_name, $page_header);
    }
}