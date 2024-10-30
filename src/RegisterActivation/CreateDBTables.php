<?php

namespace MemberGlut\Core\RegisterActivation;

use MemberGlut\Core\Base as CoreBase;

class CreateDBTables
{
    public static function make()
    {

        self::membership_db_make();
    }

    public static function membership_db_make()
    {
        global $wpdb;

        $collate = '';
        if ($wpdb->has_cap('collation')) {
            $collate = $wpdb->get_charset_collate();
        }

        $plans_table         = CoreBase::subscription_plans_db_table();
    

        $sqls[] = "CREATE TABLE IF NOT EXISTS $plans_table (
                      id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                      name varchar(255) NOT NULL,
                      description text,
                      price decimal(26,3) NOT NULL DEFAULT '0.00',
                      user_role varchar(50) NOT NULL,
                      billing_frequency varchar(50) NOT NULL,
                      subscription_length varchar(50) NOT NULL,
                      order_note varchar(50) NOT NULL,
                      total_payments int(11) DEFAULT NULL,
                      signup_fee decimal(26,3) DEFAULT '0.00',
                      free_trial varchar(50) DEFAULT NULL,
                      status enum('true','false') NOT NULL DEFAULT 'true',
                      meta_data longtext,
                      PRIMARY KEY (id)
                    ) $collate;
				";
      

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        foreach ($sqls as $sql) {
            dbDelta($sql);
        }
    }
}