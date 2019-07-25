<?php
/*
* Plugin Name: WTS Custom Login
* Description: This plugin allow to customize your login page and lets you easily and safely change the url of the login form page to anything you want without changing core files.
* Author:      webtrackstudio
* Author URI:  http://webtrackstudio.com
* Plugin URI:  https://wordpress.org/plugins/wts-custom-login
* Text Domain: wts-custom-login
* License:     GPLv2 or later
* License URI: http://www.gnu.org/licenses/gpl-2.0.html
* Version:     1.0.3
*/

/*
* Copyright (C)  2017-2020 WebTrackStudio
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License along
* with this program; if not, write to the Free Software Foundation, Inc.,
* 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/


// Make sure this file is only run from within the WordPress context.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'WTS_Custom_Login' ) ) {
	class WTS_Custom_Login {    
		/**
		* Holds the class object.
		*
		* @since 1.0.0
		*
		* @var object
		*/
		public static $instance;	
		/**
		* Plugin version, used for cache-busting of style and script file references.
		*
		* @since 1.0.0
		*
		* @var string
		*/
		public $version = '1.0.3';

		/**
		* The name of the plugin.
		*
		* @since 1.0.0
		*
		* @var string
		*/
		public $plugin_name = 'WTS Custom Login Plugin';

		/**
		* Unique plugin slug identifier.
		*
		* @since 1.0.0
		*
		* @var string
		*/
		public $plugin_slug = 'wts-custom-login';

		/**
		* Plugin file.
		*
		* @since 1.0.0
		*
		* @var string
		*/
		public $file = __FILE__;

		/**
		* Templates path.
		*
		* @since 1.0.0
		*
		* @var string
		*/
		public $template_dir;

		/**
		* Fields class object.
		*
		* @since 1.0.0
		*
		* @var object
		*/
		public $fields;

		/**
		* Primary class constructor.
		*
		* @since 1.0.0
		*/
		public function __construct() {

			// Load the plugin.
			add_action( 'init', array( $this, 'init' ), 0 );
			
			// Load javascript and css files for admin pages, front end and footer
			add_action( 'admin_enqueue_scripts', array( $this, 'wts_clp_load_admin_scripts' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'wts_clp_load_scripts' ) );
			
			add_action( 'admin_enqueue_scripts', array( $this, 'wts_clp_enqueue_color_picker' ) );		
			add_action( 'admin_footer', array( $this, 'wts_clp_footer_script' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'wts_clp_Load_template_scripts' ) );
		}

		/**
		* Loads the plugin into WordPress.
		*
		* @since 1.0.0
		*/
		public function init() {
			
			$this->template_dir = plugin_dir_path( __FILE__ ) . '/templates/';
			
			
			// Load admin only components.
			if ( is_admin() ) {
				$this->require_admin();
			}
			
			// Load global components.
			$this->require_global();
			
			$this->fields = $this->fields();
			
			// Load front-end only components.        
			if ( ! is_admin() ) {
				$this->require_frontend();
			}
		}
		
		/**
		* Loads scripts.
		*
		* @since 1.0.0
		*
		* @global int $id      The current post ID.
		* @global object $post The current post object.
		* @return null         Return early if not on the proper screen.
		*/
		public function wts_clp_load_scripts( $hook ) {
			
			//add bootsrap css file
			wp_register_style( 'bootstrap', plugins_url( 'assets/bootstrap/css/bootstrap.min.css', $this->file ), array(), $this->version );
			wp_enqueue_style( 'bootstrap' );

			//add font-awesome icones
			wp_register_style( 'font-awesome', plugins_url( 'assets/font-awesome/css/font-awesome.min.css', $this->file ), array(), $this->version );
			wp_enqueue_style( 'font-awesome' );
			
			//wp_register_style( 'default-css', plugins_url( 'assets/css/wts-clp-main.css', $this->file ), array(), $this->version );
			//wp_enqueue_style( 'default-css');			
			
			//google recatpcha script.
			wp_register_script( 'wts_recaptcha_script', 'https://www.google.com/recaptcha/api.js', array( 'jquery' ), $this->version,true );		
			wp_enqueue_script( "wts_recaptcha_script" );			

		}	
		
		/**
		* Loads scripts.
		*
		* @since 1.0.0
		*
		* @global int $id      The current post ID.
		* @global object $post The current post object.
		* @return null         Return early if not on the proper screen.
		*/
		public function wts_clp_load_admin_scripts( $hook ) {
			
			
			//this will add this js file plugin's settings page 'wts-custom-login-options'
			// Load only on ?page=wts-custom-login-options        
			if($hook == 'toplevel_page_wts-custom-login-options') {
				//add bootsrap css file
				wp_register_style( 'bootstrap', plugins_url( 'assets/bootstrap/css/bootstrap.min.css', $this->file ), array(), $this->version );
				wp_enqueue_style( 'bootstrap' );

				//add font-awesome icones
				wp_register_style( 'font-awesome', plugins_url( 'assets/font-awesome/css/font-awesome.min.css', $this->file ), array(), $this->version );
				wp_enqueue_style( 'font-awesome' );
				
				wp_register_style( 'wts-admin-skin-css', plugins_url( 'assets/css/admin-skin.css', $this->file ), array(), $this->version );
				wp_enqueue_style( 'wts-admin-skin-css');
				
				
				//settings page custom js for all elements on that page
				wp_register_script( 'wts-clp-settings-page-jquery', plugins_url( 'assets/js/wts-clp-settings-page-custom.js', $this->file ), array( 'jquery' ), $this->version,true );		
				wp_localize_script( 'wts-clp-settings-page-jquery', 'wts_clp', array( 'ajaxurl' => admin_url('admin-ajax.php'), 'scripturl' => plugins_url( 'assets/js/wts-clp-settings-page-custom.js', $this->file ),'pluginDir' => plugin_dir_url( $this->file ) ) );
				
				wp_enqueue_script('wts-clp-settings-page-jquery');
				
				
				//google recatpcha script.
				wp_register_script( 'wts_recaptcha_script', 'https://www.google.com/recaptcha/api.js', array( 'jquery' ), $this->version,true );		
				wp_enqueue_script( "wts_recaptcha_script" );
				
				
				//get selected theme 
				$selected_theme				= get_option('wts_clp_login_selected_theme');

				if(empty(get_option('wts_clp_login_selected_theme')))
				{
					//load content according theme1 by default
					$selected_theme='theme1';
				}	
				
				wp_register_style('wts-admin-theme-css', plugins_url( 'assets/css/' . $selected_theme. '_style.css', $this->file ), array(), $this->version );
				wp_enqueue_style( 'wts-admin-theme-css');
				
			}

			

			

		}	
		
		/**
		* Loads all admin related files into scope.
		*
		* @since 1.0.0
		*/
		public function require_admin() {
			require plugin_dir_path( __FILE__ ) . 'includes/admin/wts_clp-admin-functions.php';		
		}	

		/**
		* Loads all global files into scope.
		*
		* @since 1.0.0
		*/
		public function require_global() {
			//this file has function to add menus and show settings page for plugin
			require plugin_dir_path( __FILE__ ) . 'includes/global/plugin-settings-mainpage.php';

			require plugin_dir_path( __FILE__ ) . 'includes/global/user-functions.php';
			require plugin_dir_path( __FILE__ ) . 'includes/global/functions.php';
			require plugin_dir_path( __FILE__ ) . 'includes/global/fieldtype.php';
		}
		
		/**
		* Loads all front-end files into scope.
		*
		* @since 1.0.0
		*/
		public function require_frontend() {
			//require plugin_dir_path( __FILE__ ) . 'Wts_Clp_PageTemplater.php';
			require plugin_dir_path( __FILE__ ) . 'includes/template.php';
			require plugin_dir_path( __FILE__ ) . 'includes/shortcodes.php';
		}

		/**
		* Custom pages.
		*
		* @since 1.0.0
		*/
		// custom page for login
		public function custom_pages() {

			$pages = array(	 
				'user-login',
			);
			return $pages;
		}

		/**
		* Get fieldtype class.
		* @return WTS_Custom_Login_Field_Type
		*/
		public function fields() {

			return WTS_Custom_Login_Field_Type::get_instance();

		}
		
		/**
		* Returns the singleton instance of the class.
		*
		* @since 1.0.0
		*
		* @return object
		*/
		public static function get_instance() {

			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WTS_Custom_Login ) ) {
				self::$instance = new WTS_Custom_Login();
			}

			return self::$instance;

		}
		
		/**
		 * This function in Custom Login Plugin will be used to add or enqueue color picker js and css	to
		 * add functionality to choose images from wordpress media library and color picker to choose color.	 
		 */
		function wts_clp_enqueue_color_picker() {

			// this will add color picker css by which we can choose color.
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );

			// Add media library.
			wp_enqueue_media();

		}
		
		/**
		 * This function will add JS to site's footer.which enables us to choose media files from
		 * wordpress media library.
		 */
		
		function wts_clp_footer_script() {
			//Core media script
			wp_enqueue_media();
			
			//this will add jQuery which enables to choose media from wordpress media library.
			wp_register_script( 'wp-media-chooser-jquery', plugins_url( 'assets/js/wts-clp-choose-media.js', $this->file ), array( 'jquery' ), $this->version,true );		
			wp_enqueue_script('wp-media-chooser-jquery');
		}
		
		//add_action('wp_enqueue_scripts','wts_clp_Load_template_scripts');
		function wts_clp_Load_template_scripts(){
			if ( is_page_template(plugins_url('templates/pages/wts-clp-login-page-template.php', $this->file )) )
			{
				//echo get_page_template();exit;
				wp_register_script( 'wts-clp-settings-page-jquery', plugins_url( 'assets/js/wts-clp-settings-page-custom.js', $this->file ), array( 'jquery' ), $this->version,true );		
				wp_enqueue_script('wts-clp-settings-page-jquery');
			} 
		}
		
	}

	register_activation_hook( __FILE__, 'wts_custom_login_plugin_activation_hook' );


	/**
	* Fired when the plugin is activated. create a custom login page
	*
	* @since 1.0.0
	*/

	function wts_custom_login_plugin_activation_hook() {
		$instance = WTS_Custom_Login::get_instance();
		
		include_once( 'includes/admin/wts_clp-admin-functions.php' );   
		
		$custom_login_pageId	= wts_clp_create_custom_login_page( 'user-login', 'Login', '[wts_clp_user_login]');
		
		//add custom template to custom login page
		update_post_meta( $custom_login_pageId, '_wp_page_template', "templates/pages/wts-clp-login-page-template.php" );
		
		//save or update in wp_options table this login page id and name
		update_option('wts_clp_login_page_name','user-login', 'yes' );
		update_option('wts_clp_login_page_id',$custom_login_pageId , 'yes' );
		
	}


	register_deactivation_hook( __FILE__, 'wts_custom_login_plugin_deactivation_hook' );

	/**
	* Fired when the plugin is deactivated. delete custom login page
	*
	* @since 1.0.0
	*/

	function wts_custom_login_plugin_deactivation_hook() {
		$custom_login_page_id = get_option( 'wts_clp_login_page_id' );
		
		//delete this custom login page.
		wp_delete_post( $custom_login_page_id, true );	
	}

	/**
	* This will add a template to wordpress templates list while this template
	* is in template folder of plugin which will also be shown to dropdown list of 
	* templates in page editor.At where we can choose out custom template for custom page.
	*/
	require_once( plugin_dir_path( __FILE__ ) . 'wts-clp-class-page-template.php' );
	add_action( 'plugins_loaded', array( 'Wts_Clp_PageTemplater', 'get_instance' ) );

	/**
	* This returns the instance of plugin's class.
	* @since 1.0.0
	*/
	function wts_clp() {

		return WTS_Custom_Login::get_instance();

	}

	// Load the main plugin class.
	$WtsClp = wts_clp();
}