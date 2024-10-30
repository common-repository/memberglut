<?php
// 


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


use MemberGlut\Core\Base;


/** Plugin DB settings data */
function mglut_db_data()
{
    return get_option(MGLUT_SETTINGS_DB_OPTION_NAME, []);
}

function mglut_update_settings($key, $value)
{
    $data       = mglut_db_data();
    $data[$key] = $value;
    update_option(MGLUT_SETTINGS_DB_OPTION_NAME, $data);
}

/**
 * Array of WooCommerce billing fields.
 *
 * @return array
 */
function mglut_woocommerce_billing_fields()
{
    return array(
        'billing_first_name',
        'billing_last_name',
        'billing_company',
        'billing_address_1',
        'billing_address_2',
        'billing_city',
        'billing_postcode',
        'billing_country',
        'billing_state',
        'billing_phone',
        'billing_email'
    );
}

/**
 * Array of WooCommerce billing fields.
 *
 * @return array
 */
function mglut_woocommerce_shipping_fields()
{
    return array(
        'shipping_first_name',
        'shipping_last_name',
        'shipping_company',
        'shipping_address_1',
        'shipping_address_2',
        'shipping_city',
        'shipping_postcode',
        'shipping_country',
        'shipping_state'
    );
}

/**
 * Array of WooCommerce billing and shipping fields.
 *
 * @return array
 */
function mglut_woocommerce_billing_shipping_fields()
{
    return array_merge(mglut_woocommerce_billing_fields(), mglut_woocommerce_shipping_fields());
}

/**
 * @param string $key
 * @param bool $default
 * @param bool $is_empty set to true to return the default if value is empty
 *
 * @return mixed
 */
function mglut_settings_by_key($key = '', $default = false, $is_empty = false)
{
    $data = mglut_db_data();

    if ($is_empty === true) {
        return isset($data[$key]) && ( ! empty($data[$key]) || mglut_is_boolean($data[$key])) ? $data[$key] : $default;
    }

    return isset($data[$key]) ? $data[$key] : $default;
}

function mglut_get_setting($key = '', $default = false, $is_empty = false)
{
    return mglut_settings_by_key($key, $default, $is_empty);
}



/**
 * Return the url to redirect to after successful reset / change of password.
 *
 * @return bool|string
 */
function mglut_password_reset_redirect()
{
    $password_reset_redirect            = mglut_get_setting('set_password_reset_redirect');
    $custom_url_password_reset_redirect = mglut_get_setting('custom_url_password_reset_redirect');

    if ( ! empty($custom_url_password_reset_redirect)) {
        $redirect = $custom_url_password_reset_redirect;
    } elseif ( ! empty($password_reset_redirect)) {
        $redirect = get_permalink($password_reset_redirect);
        if ($password_reset_redirect == 'no_redirect') {
            $redirect = mglut_password_reset_url() . '?password=changed';
        }
    } else {
        $redirect = mglut_password_reset_url() . '?password=changed';
    }

    return apply_filters('mglut_do_password_reset_redirect', esc_url_raw($redirect));
}

/**
 * Return the url to frontend myprofile page.
 *
 * @return bool|string
 */
function mglut_profile_url()
{
    $url = admin_url('profile.php');

    $page_id = mglut_get_setting('set_user_profile_shortcode');

    if ( ! empty($page_id)) {
        $url = get_permalink($page_id);
    }

    return apply_filters('mglut_profile_url', $url);
}

function mglut_get_frontend_profile_url($username_or_id)
{
    if (is_numeric($username_or_id)) {
        $username_or_id = mglut_get_username_by_id($username_or_id);
    }

    return home_url(mglut_get_profile_slug() . '/' . rawurlencode($username_or_id));
}

/**
 * Return MemberGlut edit profile page URL or WP default profile URL as fallback
 *
 * @return bool|string
 */
function mglut_edit_profile_url()
{
    return apply_filters('mglut_edit_profile_url', mglut_my_account_url());
}

function mglut_my_account_url()
{
    $url = get_edit_profile_url();

    $page_id = mglut_settings_by_key('edit_user_profile_url');

    if ( ! empty($page_id) && get_post_status($page_id)) {
        $url = get_permalink($page_id);
    }

    return apply_filters('mglut_my_account_url', $url);
}

/**
 * Return MemberGlut password reset url.
 *
 * @return string
 */
function mglut_password_reset_url()
{
    $url = wp_lostpassword_url();

    $page_id = mglut_get_setting('set_lost_password_url');

    if ( ! empty($page_id) && get_post_status($page_id)) {
        $url = get_permalink($page_id);
    }

    return apply_filters('mglut_password_reset_url', $url);
}


/**
 * Get MemberGlut login page URL or WP default login url if it isn't set.
 *
 * @param $redirect
 *
 * @return string
 */
function mglut_login_url($redirect = '')
{
    $login_url = wp_login_url();

    $login_page_id = mglut_get_setting('set_login_url');

    if ( ! empty($login_page_id) && get_post_status($login_page_id)) {
        $login_url = get_permalink($login_page_id);
    }

    if ( ! empty($redirect)) {
        $login_url = add_query_arg('redirect_to', rawurlencode(wp_validate_redirect($redirect)), $login_url);
    }

    return apply_filters('mglut_login_url', $login_url);
}

/**
 * Get MemberGlut login page URL or WP default login url if it isn't set.
 */
function mglut_registration_url()
{
    $page_id = mglut_get_setting('set_registration_url');

    if ( ! empty($page_id) && get_post_status($page_id)) {
        $reg_url = get_permalink($page_id);
    } else {
        $reg_url = wp_registration_url();
    }

    return apply_filters('mglut_registration_url', $reg_url);
}

/**
 * Return the URL of the currently view page.
 *
 * @return string
 */
function mglut_get_current_url()
{
    global $wp;

    return home_url(add_query_arg(array(), $wp->request));
}





/**
 * @return string blog URL without scheme
 */
function mglut_site_url_without_scheme()
{
    $parsed_url = wp_parse_url(home_url());

    return $parsed_url['host'];
}

/**
 * Append an option to a select dropdown
 *
 * @param string $option option to add
 * @param string $select select dropdown
 *
 * @return string
 */
function mglut_append_option_to_select($option, $select)
{
    $regex = "/<select ([^<]*)>/";

    preg_match($regex, $select, $matches);
    $select_attr = mglut_var($matches, 1);

    $a = preg_split($regex, $select);

    $join = '<select ' . $select_attr . '>' . "\r\n";
    $join .= $option . mglut_var($a, 1, '');

    return $join;
}

/**
 * Blog name or domain name if name doesn't exist
 *
 * @return string
 */
function mglut_site_title()
{
    $blog_name = get_option('blogname');

    return ! empty($blog_name) ? wp_specialchars_decode($blog_name, ENT_QUOTES) : str_replace(
        array(
            'http://',
            'https://',
        ),
        '',
        site_url()
    );
}


/**
 * Check if an admin settings page is MemberGlut'
 *
 * @return bool
 */
function mglut_is_admin_page() {
    $mglut_builder_pages = array(
        MGLUT_SETTINGS_SLUG,
        MGLUT_MEMBERSHIP_ORDERS_SETTINGS_SLUG,
        MGLUT_MEMBERSHIP_SUBSCRIPTIONS_SETTINGS_SLUG,
        MGLUT_MEMBERSHIP_PLANS_SETTINGS_SLUG,
        MGLUT_MEMBERSHIP_CUSTOMERS_SETTINGS_SLUG,
        MGLUT_FORMS_SETTINGS_SLUG,
        MGLUT_MEMBER_DIRECTORIES_SLUG,
        MGLUT_CONTENT_PROTECTION_SETTINGS_SLUG,
        MGLUT_EXTENSIONS_SETTINGS_SLUG,
        MGLUT_DASHBOARD_SETTINGS_SLUG
    );

    // Sanitize and validate $_GET['page'] and $_GET['tab'] against the allowed values
    $page = isset($_GET['page']) && !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['page'])), 'appglut_memberglut_nonce') ? sanitize_key($_GET['page'])  : '';
    $tab = isset($_GET['tab']) && !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['tab'])), 'appglut_memberglut_nonce') ? sanitize_key($_GET['tab'])  : '';

    return (in_array($page, $mglut_builder_pages)) || ($tab === 'mglut_extensions');
}



/**
 * Return admin email
 *
 * @return string
 */
function mglut_admin_email()
{
    return get_option('admin_email');
}

/**
 * Checks whether the given user ID exists.
 *
 * @param string $user_id ID of user
 *
 * @return null|int The user's ID on success, and null on failure.
 */
function mglut_user_id_exist($user_id)
{
    if ($user = get_user_by('id', $user_id)) {
        return $user->ID;
    }

    return null;
}

/**
 * Get a user's username by their ID
 *
 * @param int $user_id
 *
 * @return bool|string
 */
function mglut_get_username_by_id($user_id)
{
    return mglut_var_obj(get_user_by('id', $user_id), 'user_login');
}

/**
 * front-end profile slug.
 *
 * @return string
 */
function mglut_get_profile_slug()
{
    return apply_filters('mglut_profile_slug', mglut_get_setting('set_user_profile_slug', 'profile', true));
}

/**
 * Filter form field attributes for unofficial attributes.
 *
 * @param array $atts supplied shortcode attributes
 *
 * @return mixed
 *
 */
function mglut_other_field_atts($atts)
{
    if ( ! is_array($atts)) return $atts;

    $official_atts = array('name', 'class', 'id', 'value', 'title', 'required', 'placeholder', 'key', 'field_key', 'limit', 'options', 'checkbox_text', 'processing_label');

    $other_atts = array();

    foreach ($atts as $key => $value) {
        if ( ! in_array($key, $official_atts) && strpos($key, 'on') !== 0) {
            $other_atts[esc_attr($key)] = esc_attr($value);
        }
    }

    $other_atts_html = '';

    if (is_array($other_atts) && ! empty($other_atts)) {

        foreach ($other_atts as $key => $value) {

            if ( ! empty($value)) {
                $other_atts_html = sprintf('%s="%s" ', esc_attr($key), esc_attr($value));
            }
        }
    }

    return $other_atts_html;
}



/**
 * Get front-end do password reset form url.
 *
 * @param string $user_login
 * @param string $key
 *
 * @return string
 */
function mglut_get_do_password_reset_url($user_login, $key)
{
    $url = network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login');

    $page_id = mglut_get_setting('set_lost_password_url');

    if (apply_filters('mglut_front_end_do_password_reset', true) && ! empty($page_id)) {

        $url = add_query_arg(
            array(
                'key'   => $key,
                'login' => rawurlencode($user_login)
            ),
            mglut_password_reset_url()
        );
    }

    return $url;
}

/**
 * Return true if a field key exist/is multi selectable dropdown.
 *
 * @param $field_key
 *
 * @return bool
 */
function mglut_is_select_field_multi_selectable($field_key)
{
    $data = get_option('mglut_cpf_select_multi_selectable', array());

    return array_key_exists($field_key, $data);
}


/**
 * Return username/username of a user using the user's nicename to do the DB search.
 *
 * @param string $slug
 *
 * @return bool|null|string
 */
function mglut_is_slug_nice_name($slug)
{
    global $wpdb;

$response = wp_cache_get('user_login_' . $slug, 'user_logins');

// If not found in cache, fetch from the database
if (false === $response) {
    // Use WordPress functions to interact with the database
    $user = get_user_by('slug', $slug);

    if ($user) {
        $response = $user->user_login;

        // Cache the result
        wp_cache_set('user_login_' . $slug, $response, 'user_logins');
    }
}
    

    // if response isn't null, the username/user_login is returned.
    return is_null($response) ? false : $response;
}

/**
 * Return array of editable roles.
 *
 * @param $remove_admin
 *
 * @return mixed
 */
function mglut_get_editable_roles($remove_admin = true)
{
    $all_roles = wp_roles()->roles;

    if (true == $remove_admin) {
        unset($all_roles['administrator']);
    }

    return $all_roles;
}

function mglut_wp_roles_key_value($remove_admin = true)
{
    $wp_roles = mglut_get_editable_roles($remove_admin);

    return array_reduce(array_keys($wp_roles), function ($carry, $item) use ($wp_roles) {
        $carry[$item] = $wp_roles[$item]['name'];

        return $carry;
    }, []);
}




/**
 * Generate url to reset user's password.
 *
 * @param string $user_login
 *
 * @return string
 */
function mglut_generate_password_reset_url($user_login)
{
    $user = get_user_by('login', $user_login);

    $key = get_password_reset_key($user);

    if (is_wp_error($key)) {
        mglut_log_error($key->get_error_message());

        return '';
    }

    return mglut_get_do_password_reset_url($user_login, $key);
}

function mglut_nonce_action_string()
{
    return 'mglut_plugin_nonce';
}

/**
 * Return array of countries.
 *
 * @param string $country_code
 *
 * @return mixed|string
 */
function mglut_array_of_world_countries($country_code = '')
{
    $list = apply_filters('mglut_countries_list', include(MEMBERGLUT_SRC . 'Functions/data/countries.php'));

    if ( ! empty($country_code)) {
        return mglut_var($list, $country_code);
    }

    return $list;
}

/**
 * @param $country
 *
 * @return mixed
 */
function mglut_array_of_world_states($country = '')
{
    $states = apply_filters('mglut_countries_states_list', include(MEMBERGLUT_SRC . 'Functions/data/states.php'));

    if ( ! empty($country)) {
        return mglut_var($states, $country, []);
    }

    return $states;
}

function mglut_get_country_title($country)
{
    if ( ! empty($country)) {

        $val = mglut_array_of_world_countries($country);

        if ( ! empty($val)) return $val;
    }

    return $country;
}

function mglut_get_country_state_title($state, $country)
{
    return mglut_var(mglut_array_of_world_states($country), $state, $state, true);
}

function mglut_create_nonce()
{
    return wp_create_nonce(mglut_nonce_action_string());
}

function mglut_nonce_field()
{
    return wp_nonce_field(mglut_nonce_action_string(), '_wpnonce', true, false);
}

function mglut_verify_nonce()
{
    return check_admin_referer(mglut_nonce_action_string());
}

function mglut_verify_ajax_nonce()
{
    return check_ajax_referer(mglut_nonce_action_string());
}

/**
 * Returns a more compact md5 hashing.
 *
 * @param $string
 *
 * @return false|string
 */
function mglut_md5($string)
{
    return substr(base_convert(md5($string), 16, 32), 0, 12);
}

/**
 * Generate unique ID
 *
 * @param int $length
 *
 * @return string
 */
function mglut_generate_unique_id($length = 10)
{
    $characters       = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString     = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[wp_rand(0, $charactersLength - 1)];
    }

    return mglut_md5(time() . $randomString);
}

function mglut_minify_css($buffer)
{
    $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
    $buffer = str_replace(': ', ':', $buffer);
    $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);

    return $buffer;
}

function mglut_minify_js($code)
{
    // make it into one long line
    $code = str_replace(array("\n", "\r"), '', $code);
    // replace all multiple spaces by one space
    $code = preg_replace('!\s+!', ' ', $code);
    // replace some unneeded spaces, modify as needed
    $code = str_replace(array(' {', ' }', '{ ', '; '), array('{', '}', '{', ';'), $code);

    return $code;
}

function mglut_minify_html($html)
{
    $lines = explode(PHP_EOL, $html);
    array_walk($lines, function (&$line) {
        $line = trim($line);
    });

    $lines = array_filter($lines, function ($line) {
        return $line !== '';
    });

    return implode(PHP_EOL, $lines);
}


/**
 * Admin email address to receive admin notification.
 *
 * @return mixed
 */
function mglut_get_admin_notification_emails()
{
    return mglut_get_setting('admin_email_addresses', mglut_admin_email(), true);
}

/**
 * @return WP_Filesystem_Base|false
 */
function mglut_file_system()
{
    global $wp_filesystem;

    require_once ABSPATH . 'wp-admin/includes/file.php';

    // If for some reason the include doesn't work as expected just return false.
    if ( ! function_exists('\WP_Filesystem')) {
        return false;
    }

    $writable = WP_Filesystem(false, '', true);

    // We consider the directory as writable if it uses the direct transport,
    // otherwise credentials would be needed.
    return ($writable && 'direct' === $wp_filesystem->method) ? $wp_filesystem : false;
}

function mglut_get_file($file)
{
    $content = '';

    $fs = mglut_file_system();

    if ($fs && $fs->exists($file)) {
        $content = $fs->get_contents($file);
    }

    return $content;
}

function mglut_get_error_log($type = 'debug')
{
    $file_token = get_option('mglut_debug_log_token');

    $log_file = MGLUT_ERROR_LOG_FOLDER . "{$type}-{$file_token}.log";

    $file_contents = '';

    if (file_exists($log_file)) {
        $file_contents = @wp_remote_get($log_file);
    }

    return $file_contents;
}


function mglut_var($bucket, $key, $default = false, $empty = false)
{
    if ($empty) {
        return isset($bucket[$key]) && ( ! empty($bucket[$key]) || mglut_is_boolean($bucket[$key])) ? sanitize_text_field($bucket[$key]) : $default;
    }

    return isset($bucket[$key]) ? sanitize_text_field($bucket[$key]) : $default;
}

function mglut_var_obj($bucket, $key, $default = false, $empty = false)
{
    if ($empty) {
        return isset($bucket->$key) && ( ! empty($bucket->$key) || mglut_is_boolean($bucket->$key)) ? sanitize_text_field($bucket->$key) : $default;
    }

    return isset($bucket->$key) ? sanitize_text_field($bucket->$key) : $default;
}


/**
 * Normalize unamed shortcode
 *
 * @param array $atts
 *
 * @return mixed
 */
function mglut_normalize_attributes($atts)
{
    if (is_array($atts)) {
        foreach ($atts as $key => $value) {
            if (is_int($key)) {
                $atts[$value] = true;
                unset($atts[$key]);
            }
        }
    }

    return $atts;
}

function mglut_dnd_field_key_description()
{
    return esc_html__('It must be unique for each field, not a reserve text, in lowercase letters only with an underscore ( _ ) separating words e.g job_title', 'memberglut');
}

function mglut_reserved_field_keys()
{
    return [
        'ID', 'id', 'user_pass', 'user_login', 'user_nicename', 'user_url', 'user_email', 'display_name', 'nickname',
        'first_name', 'last_name', 'description', 'rich_editing', 'syntax_highlighting', 'comment_shortcuts', 'admin_color',
        'use_ssl', 'user_registered', 'user_activation_key', 'spam', 'show_admin_bar_front', 'role', 'locale', 'deleted', 'user_level',
        'user_status', 'user_description'
    ];
}

function mglut_is_boolean($maybe_bool)
{
    if (is_bool($maybe_bool)) {
        return true;
    }

    if (is_string($maybe_bool)) {
        $maybe_bool = strtolower($maybe_bool);

        $valid_boolean_values = array(
            'false',
            'true',
            '0',
            '1',
        );

        return in_array($maybe_bool, $valid_boolean_values, true);
    }

    if (is_int($maybe_bool)) {
        return in_array($maybe_bool, array(0, 1), true);
    }

    return false;
}

function mglut_filter_empty_array($values)
{
    if ( ! is_array($values)) return $values;

    return array_filter($values, function ($value) {
        return mglut_is_boolean($value) || is_int($value) || ! empty($value);
    });
}

/**
 * Check if HTTP status code is successful.
 *
 * @param int $code
 *
 * @return bool
 */
function mglut_is_http_code_success($code)
{
    $code = absint($code);

    return $code >= 200 && $code <= 299;
}

/**
 * Converts date/time which should be in UTC to timestamp.
 *
 * strtotime uses the default timezone set in PHP which may or may not be UTC.
 *
 * @param $time
 *
 * @return false|int
 */
function mglut_strtotime_utc($time)
{
    return strtotime($time . ' UTC');
}

function mglut_array_flatten($array)
{
    if ( ! is_array($array)) {
        return false;
    }
    $result = array();
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            // we are not doing array_merge here because we wanna keep array keys.
            // PS: The + operator is not an addition, it's a union. If the keys don't overlap then all is good.
            $result = $result + mglut_array_flatten($value);
        } else {
            $result[$key] = $value;
        }
    }

    return $result;
}

/**
 * Sanitizes a string key.
 *
 * Keys are used as internal identifiers. Lowercase alphanumeric characters and underscores are allowed.
 *
 * @param string $key String key
 *
 * @return string Sanitized key
 */
function mglut_sanitize_key($key)
{
    return str_replace('-', '_', sanitize_key($key));
}




function mglut_standard_fields_key_value_pair($remove_default = false)
{
    $fields = [];
    if ($remove_default === false) {
        $fields[''] = esc_html__('Select...', 'memberglut');
    }

    return array_merge($fields, [
        'first_last_names'  => esc_html__('First and Last Names', 'memberglut'),
        'last_first_names'  => esc_html__('Last and First Names', 'memberglut'),
        'username'          => esc_html__('Username', 'memberglut'),
        'first-name'        => esc_html__('First Name', 'memberglut'),
        'last-name'         => esc_html__('Last Name', 'memberglut'),
        'nickname'          => esc_html__('Nickname', 'memberglut'),
        'display-name'      => esc_html__('Display Name', 'memberglut'),
        'email'             => esc_html__('Email Address', 'memberglut'),
        'bio'               => esc_html__('Biography', 'memberglut'),
        'registration_date' => esc_html__('Registration Date', 'memberglut'),
    ]);
}

function mglut_standard_custom_fields_key_value_pair($remove_default = false)
{
    $fields = [];

    if ($remove_default === false) {
        $fields[''] = esc_html__('Select...', 'memberglut');
    }

    $fields[esc_html__('Standard Fields', 'memberglut')] = mglut_standard_fields_key_value_pair(true);

    

    return $fields;
}

/**
 * @param int|bool $user_id
 *
 * @return bool
 */
function mglut_user_has_cover_image($user_id = false)
{
    $user_id = ! $user_id ? get_current_user_id() : $user_id;

    $cover = get_user_meta($user_id, 'mglut_profile_cover_image', true);

    return ! empty($cover);
}


/**
 * @param int|bool $user_id
 *
 * @return string|bool
 */
function mglut_get_cover_image_url($user_id = false)
{
    $user_id = ! $user_id ? get_current_user_id() : $user_id;

    $slug = get_user_meta($user_id, 'mglut_profile_cover_image', true);

    if ( ! empty($slug)) {
        return MGLUT_COVER_IMAGE_UPLOAD_URL . "$slug";
    }

    return get_option('wp_user_cover_default_image_url');
}

function mglut_is_my_own_profile()
{
    global $mglut_frontend_profile_user_obj;

    return mglut_var_obj($mglut_frontend_profile_user_obj, 'ID') == get_current_user_id();
}

function mglut_is_my_account_page()
{
    return mglut_post_content_has_shortcode('memberglut-my-account');
}

function mglut_social_network_fields()
{
    return apply_filters('mglut_core_contact_info_fields', [
        Base::cif_facebook  => 'Facebook',
        Base::cif_twitter   => 'Twitter',
        Base::cif_linkedin  => 'LinkedIn',
        Base::cif_vk        => 'VK',
        Base::cif_youtube   => 'YouTube',
        Base::cif_instagram => 'Instagram',
        Base::cif_github    => 'GitHub',
        Base::cif_pinterest => 'Pinterest',
    ]);
}

function mglut_social_login_networks()
{
    return apply_filters('mglut_social_login_networks', [
        'facebook'     => 'Facebook',
        'twitter'      => 'Twitter',
        'google'       => 'Google',
        'linkedin'     => 'LinkedIn',
        'microsoft'    => 'Microsoft',
        'yahoo'        => 'Yahoo',
        'amazon'       => 'Amazon',
        'github'       => 'GitHub',
        'wordpresscom' => 'WordPress.com',
        'vk'           => 'VK.com'
    ]);
}

function mglut_mb_function($function_names, $args)
{
    $mb_function_name = $function_names[0];
    $function_name    = $function_names[1];
    if (function_exists($mb_function_name)) {
        $function_name = $mb_function_name;
    }

    return call_user_func_array($function_name, $args);
}

function mglut_recursive_trim($item)
{
    if (is_array($item)) {

        $sanitized_data = [];
        foreach ($item as $key => $value) {
            $sanitized_data[$key] = mglut_recursive_trim($value);
        }

        return $sanitized_data;
    }

    return trim($item);
}

function mglut_check_type_and_ext($file, $accepted_mime_types = [], $accepted_file_ext = [])
{

    if (empty($file_name)) {
        $file_name = $file['name'];
    }

    $tmp_name = $file['tmp_name'];

    $wp_filetype = wp_check_filetype_and_ext($tmp_name, $file_name);

    $ext             = $wp_filetype['ext'];
    $type            = $wp_filetype['type'];
    $proper_filename = $wp_filetype['proper_filename'];

    // When a proper_filename value exists, it could be a security issue if it's different than the original file name.
    if ($proper_filename && strtolower($proper_filename) !== strtolower($file_name)) {
        return new WP_Error('invalid_file', esc_html__('There was an problem while verifying your file.', 'memberglut'));
    }

    // If either $ext or $type are empty, WordPress doesn't like this file and we should bail.
    if ( ! $ext) {
        return new WP_Error('illegal_extension', esc_html__('Sorry, this file extension is not permitted for security reasons.', 'memberglut'));
    }

    if ( ! $type) {
        return new WP_Error('illegal_type', esc_html__('Sorry, this file type is not permitted for security reasons.', 'memberglut'));
    }

    if ( ! empty($accepted_mime_types) && ! in_array($type, $accepted_mime_types)) {
        return new WP_Error('illegal_type', esc_html__('Error: The file you uploaded is not accepted on our website.', 'memberglut'));
    }

    if ( ! empty($accepted_file_ext) && ! in_array($ext, $accepted_file_ext)) {
        return new WP_Error('illegal_type', esc_html__('Error: The file you uploaded is not accepted on our website.', 'memberglut'));
    }

    return true;
}

function mglut_decode_html_strip_tags($val)
{
    return wp_strip_all_tags(html_entity_decode($val));
}


function mglut_is_json($str)
{
    $json = json_decode($str);

    return $json && $str != $json;
}

function mglut_clean($var, $callback = 'sanitize_textarea_field')
{
    if (is_array($var)) {
        return array_map('mglut_clean', $var);
    } else {
        return is_scalar($var) ? call_user_func($callback, $var) : $var;
    }
}


/**
 * @param int $plan_id Plan ID or Subscription ID if change plan URL
 * @param bool $is_change_plan set to true to return checkout url to change plan
 *
 * @return false|string
 */
function mglut_plan_checkout_url($plan_id, $is_change_plan = false)
{
    $page_id = mglut_settings_by_key('checkout_page_id');

    if ( ! empty($page_id)) {

        $cid = $is_change_plan ? 'change_plan' : 'plan';

        return add_query_arg($cid, absint($plan_id), get_permalink($page_id));
    }

    return false;
}

/**
 * Generate unique ID for each optin form.
 *
 * @param int $length
 *
 * @return string
 */
function mglut_generateUniqueId($length = 10)
{
    $characters       = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString     = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[wp_rand(0, $charactersLength - 1)];
    }

    return mglut_md5(time() . $randomString);
}

function mglut_render_view($template, $vars = [], $parentDir = '')
{
    if (empty($parentDir)) $parentDir = dirname(__FILE__, 2) . '/templates/';

    $path = $parentDir . $template . '.php';

    extract($vars);
    ob_start();
    require apply_filters('mglut_render_view', $path, $vars, $template, $parentDir);
    echo wp_kses_post(apply_filters('mglut_render_view_output', ob_get_clean(), $template, $vars, $parentDir));
}

function mglut_post_content_has_shortcode($tag = '', $post = null)
{
    if (is_null($post)) {
        global $post;
    }

    return is_singular() && is_a($post, 'WP_Post') && has_shortcode($post->post_content, $tag);
}

function mglut_maybe_define_constant($name, $value)
{
    if ( ! defined($name)) {
        define($name, $value);
    }
}

function mglut_upgrade_urls_affilify($url)
{
    return apply_filters('mglut_pro_upgrade_url', $url);
}

function mglut_cache_transform($cache_key, $callback)
{
    static $mglut_cache_transform_bucket = [];

    $result = mglut_var($mglut_cache_transform_bucket, $cache_key, false);

    if ( ! $result) {

        $result = $callback();

        $mglut_cache_transform_bucket[$cache_key] = $result;
    }

    return $result;
}

