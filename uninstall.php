<?php
/**
 * WTS Custom Login Plugin Uninstall.php 
 *
 * Uninstalling WTS Custom Login Plugin deletes user pages and options.
 *
 * @author      webtrackstudio
 * @category    Core
 * @package     wts_clp/Uninstaller
 * @version     1.0.2
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

//get global wordpress and version
global $wpdb, $wp_version;
if( is_admin())
{
	// This will add post or pages to trash.
	wp_trash_post( get_option( 'wts_clp_login_page_id' ) );

	// This query deletes all options associated with this plugin and used plugin prefix
	// to identify this plugin's options.
	$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'wts_clp\_%';" );


	// Clear any cached data that has been removed
	wp_cache_flush();
}

