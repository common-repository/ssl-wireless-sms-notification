<?php 
/*
 * Removes options from database when plugin is deleted.
 *  
 *
 */

# if uninstall not called from WordPress exit

	if (!defined('WP_UNINSTALL_PLUGIN' ))
	    exit();

	global $wpdb, $wp_version;

	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}sslcare_woo_alert" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}sslcare_otp" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}sslcare_otp_login_register_settings" );

	delete_option("sslcare_plugin_version");

	wp_cache_flush();

?>