<?php

namespace MemberGlut\Core;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


class DBTables
{
    

    public static function subscription_plans_db_table()
    {
        global $wpdb;

        return $wpdb->prefix . 'mglut_plans';
    }

}