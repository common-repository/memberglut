<?php
/**
 * Plugin Name: MemberGlut
 * Plugin URI: https://wordpress.org/plugins/memberglut
 * Description: The Modern WordPress membership plugin.
 * Version: 1.0.0
 * Author: appglut
 * Author URI: https://profiles.wordpress.org/appglut
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: memberglut
 * Domain Path: /languages
 */

defined('ABSPATH') or die("No script kiddies please!");

define('MEMBERGLUT_SYSTEM_FILE_PATH', __FILE__);
define('MGLUT_VERSION_NUMBER', '1.0.0');

require __DIR__ . '/autoloader.php';

add_action('init', function () {
    load_plugin_textdomain('memberglut', false, dirname(plugin_basename(MEMBERGLUT_SYSTEM_FILE_PATH)) . '/languages');
});

MemberGlut\Core\Base::get_instance();