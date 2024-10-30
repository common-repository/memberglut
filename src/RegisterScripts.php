<?php

namespace MemberGlut\Core;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class RegisterScripts
{
    public function __construct()
    {
        add_action('admin_enqueue_scripts', [$this, 'admin_css']);
        
    }

    function admin_css()
    {

        wp_enqueue_style('mglut-admin', MGLUT_ASSETS_URL . '/css/admin/admin.css', [], MGLUT_VERSION_NUMBER);

    }

    /**
     * @return self
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