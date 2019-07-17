<?php
/**
 * Template class.
 *
 * @since 1.0.0
 *
 * @package Wts_Custom_Login
 * @author  WTS Team
 */

class Wts_Custom_Login_Posttype_Templates {
	/**
     * Holds the class object.
     *
     * @since 1.0.0
     *
     * @var object
     */
    public static $instance;

    /**
     * Path to the file.
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $file = __FILE__;

    /**
     * Holds the base class object.
     *
     * @since 1.0.0
     *
     * @var object
     */
    public $base;

    /**
     * Holds post types.
     *
     * @since 1.0.0
     *
     * @var post types
     */
    public $types;

	/**
     * Primary class constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {

        // Load the base class object.
        $this->base = WTS_Custom_Login::get_instance();
       	add_action( 'template_redirect', array( $this, 'template_redirect' ) );
		add_filter( 'template_include', array( $this, 'load_template' ) );
	}

	/**
	 * Handle redirects before content is output - hooked into template_redirect so is_page works.
	 *
	 * @since 1.0.0
	 */
	public function template_redirect() {
		global $post;
		
		$custom_login_pageid = get_option('wts_clp_login_page_id');
		$custom_login_page_slug = get_post( $custom_login_pageid )->post_name;
		if( is_page( $custom_login_pageid ) ) {
			if( is_user_logged_in() ) {
				//logged in user tried to access custom login page.so send him to wp-admin page.
				wp_redirect(get_dashboard_url());
				//wp_redirect(admin_url());
				exit;				
			}
		}
	}

	/**
	 * Load template based on different conditions.
	 * @since 1.0.0
	 * @return respective template.
	 */	 
	public function load_template( $template ) {
		
		//load custom template for custom login page.
		if ( is_page(get_option('wts_clp_login_page_id')) ) { 
			$template = locate_template( array( 'templates/pages/wts-clp-login-page-template.php' ) );
			if ( '' == $template ) {
				$template = wts_clp()->template_dir . '/pages/wts-clp-login-page-template.php';
			}
		}
		
		
        return $template;
		
	}


	/**
     * Returns the singleton instance of the class.
     *
     * @since 1.0.0
     *
     * @return object The Wts_Custom_Login_Posttype_Templates object.
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Wts_Custom_Login_Posttype_Templates ) ) {
            self::$instance = new Wts_Custom_Login_Posttype_Templates();
        }

        return self::$instance;

    }
}

// Load the posttype admin class.
$wts_clp_posttype_templates = Wts_Custom_Login_Posttype_Templates::get_instance();
