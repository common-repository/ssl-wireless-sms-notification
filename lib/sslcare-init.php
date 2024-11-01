<?php
    namespace Sslcare\Admin\Init;

    class Sslcare_Init
    {
        public static function install_sslcare() {

            global $wpdb;

            $sslcare_table_name = $wpdb->prefix . "sslcare_woo_alert";
            $sslcare_otp_table_name = $wpdb->prefix . "sslcare_otp";
            $sslcare_otp_login_register_settings_table_name = $wpdb->prefix . "sslcare_otp_login_register_settings";

            $charset_collate = '';

            if ( ! empty( $wpdb->charset ) ) {
              $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
            }

            if ( ! empty( $wpdb->collate ) ) {
                $charset_collate .= " COLLATE {$wpdb->collate}";
            }
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            
            if ($wpdb->get_var("SHOW TABLES LIKE '$sslcare_table_name'") != $sslcare_table_name) {
            $sql = "CREATE TABLE $sslcare_table_name (
                id mediumint(15) UNSIGNED NOT NULL AUTO_INCREMENT,
                customer_name varchar(300) NULL,
                phone_no varchar(20) NULL,
                sending_time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                sms_type varchar(100) NULL,
                api_response varchar(4000),
                UNIQUE KEY id (id)
                ) $charset_collate;";
                dbDelta( $sql );       
            }

            if ($wpdb->get_var("SHOW TABLES LIKE '$sslcare_otp_table_name'") != $sslcare_otp_table_name) {
            $sql = "CREATE TABLE $sslcare_otp_table_name (
                id mediumint(15) UNSIGNED NOT NULL AUTO_INCREMENT,
                phone varchar(20) NULL,
                otp int(255) NULL,
                cap_data varchar(100) NULL,
                quantity int(255),
                ip_address varchar(20) NULL,
                sending_time timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY id (id)
                ) $charset_collate;";
                dbDelta( $sql );       
            }

            if ($wpdb->get_var("SHOW TABLES LIKE '$sslcare_otp_login_register_settings_table_name'") != $sslcare_otp_login_register_settings_table_name) {
            $sql = "CREATE TABLE $sslcare_otp_login_register_settings_table_name (
                id mediumint(15) UNSIGNED NOT NULL AUTO_INCREMENT,
                enable_otp varchar(20) NULL,
                page_id bigint(20) NULL,
                delete_otp_data_after bigint(20) NULL,
                UNIQUE KEY id (id)
                ) $charset_collate;";
                dbDelta( $sql );       
            }
        }
    }