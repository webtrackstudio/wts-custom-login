<?php
/**
 * Admin hooks and functions.
 *
 * @since 1.0.0
 *
 * @package WTS_Custom_Login Plugin
 * @author  Test Team
 */
 
//create page by function 
function wts_clp_create_custom_login_page($slug, $title = '', $content = '', $parent = 0)
{
	global $wpdb;

	$page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type = 'page' AND post_name = %s LIMIT 1;", $slug ) );

	if ( $page_found ) {
		$page_id = $page_found;
		$page_args = array(
			'ID'			=> $page_id,
			'post_status'	=> 'publish'
		);
		wp_update_post( $page_args );
	} else {
		$page_args = array(
			'post_status'    => 'publish',
			'post_type'      => 'page',
			'post_author'    => 1,
			'post_name'      => $slug,
			'post_title'     => $title,
			'post_content'   => $content,
			'post_parent'    => $parent,
			'comment_status' => 'closed'
		);
		$page_id = wp_insert_post( $page_args );
	}
	return $page_id;
}