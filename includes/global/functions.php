<?php
/**
 * Global functions.
 *
 * @since 1.0.0
 *
 * @package WTS_Custom_Login
 * @author  WTS Team
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
* This function loads condition based custom template.
* @since 1.0.0
*/
function wts_clp_load_template( $name, $data = array() ) {

	if ( $data && is_array( $data ) ) {
		extract( $data );
	}

	$template = locate_template( $name . '.php' );

	if ( $overridden_template = $template ) {
		// locate_template() returns path to file
		// if either the child theme or the parent theme have overridden the template.
		include( $overridden_template );
	} else {
		// If neither the child nor parent theme have overridden the template,
		// we load the template from the 'templates' directory
		include( wts_clp()->template_dir . $name . '.php' );
	}

}

/**
* This adds admin bar logo.
* @since 1.0.0
*/
function wts_clp_add_adminbar_logo_inlinestyles() {
	
	if( !empty( get_option( 'wts_clp_adminbar_logo' ) ) )
	{
		//set custom logo url
		$adminbar_logo = get_option( 'wts_clp_adminbar_logo' );	
		
		wp_enqueue_style(
			'wts-custom-style',
			plugin_dir_url( wts_clp()->file ) . 'wcl-options/assets/css/wts-options.css'
		);
		$custom_css = "
			#wpadminbar #wp-admin-bar-wp-logo > .ab-item .ab-icon:before {
				background-image: url( ". $adminbar_logo .") !important;
				background-position: 0 0;
				color: rgba(0, 0, 0, 0);
				background-repeat: no-repeat;
				background-size: 100% 100%;
			}";
		
		wp_add_inline_style( 'wts-custom-style', $custom_css );		
	}
}

//hook into the administrative header output
add_action('wp_before_admin_bar_render', 'wts_clp_add_adminbar_logo_inlinestyles');

/**
* Add google captcha script to front or custom template.
*/
function wts_clp_load_gacaptcha_script_front() { 
	if(get_option('wts_clp_login_IsCaptchaRequired') == 1) {
		wp_register_script( 'wts_recaptcha_script_front', 'https://www.google.com/recaptcha/api.js' );
		wp_enqueue_script("wts_recaptcha_script_front"); 
	}
}
//add callback
add_action("wp_enqueue_scripts", "wts_clp_load_gacaptcha_script_front");

/**
* Add Custom login page styles.
*/
function wts_clp_custom_login_page_inline_styles_load() {
	$custoLoginPageId	= get_option('wts_clp_login_page_id' );
	$custoLoginPageSlug	= get_post( $custoLoginPageId )->post_name;
	if( is_page( $custoLoginPageSlug ) ){
		/**
		* To add css for body part of this template first check any custom
		* theme of our plugin has been choosen or not according to that add css.
		*/
		$body_backround_img='';
		$body_background_color='';
		
		//when no theme selected for login page from settings page of our plugin.	
		if(empty(get_option('wts_clp_login_selected_theme')))
		{	
			//even background image not selected yet.
			if(empty(get_option('wts_clp_background_img')))
			{
				//background color is not set yet.
				if(empty(get_option('wts_clp_page_bgcolor')))
				{
					//thus this is default condition
					$body_backround_img		= plugin_dir_url( wts_clp()->file ) . 'assets/images/theme1-bodybackground.jpeg';
					$body_background_color	= '';
				}
				else
				{
					//body background color set by settings page.
					$body_backround_img		= '';
					$body_background_color	= get_option('wts_clp_page_bgcolor');
				}
			}
			//when body background image set by settings page.
			else
			{
				$body_backround_img		= get_option('wts_clp_background_img');
				$body_background_color	= '';
			}	
		}
		//when theme has been choosen then use as per theme.
		else
		{
			if(empty(get_option('wts_clp_background_img')))
			{
				if(empty(get_option('wts_clp_page_bgcolor')))
				{
					$theme					= get_option('wts_clp_login_selected_theme');
					$body_backround_img		= plugin_dir_url( wts_clp()->file ) . 'assets/images/'.$theme.'-bodybackground.jpeg';
					$body_background_color	= '';
				}
				else
				{
					$body_backround_img='';
					$body_background_color=get_option('wts_clp_page_bgcolor');
				}
			}
			else
			{
				$body_backround_img=get_option('wts_clp_background_img');
				$body_background_color='';
			}		
		}	
		
		//login page styles 
		wp_enqueue_style(
			'wts-custom-login-page-styles',
			plugin_dir_url( wts_clp()->file ) . 'wcl-options/assets/css/wts-options.css'
		);
		
		$custom_css = "
		.wts-clp-login-page_body {
			background-image: url($body_backround_img);
			background-color:$body_background_color;		
		}";	
	
		//add this custom css as inline css
		wp_add_inline_style( 'wts-custom-login-page-styles', $custom_css );		
	}
}

/**
* This will load front end or login page styles as per selected theme.
*/
function wts_clp_custom_login_page_styles_load(  ) {
	$custoLoginPageId	= get_option( 'wts_clp_login_page_id' );
	$custoLoginPageSlug	= get_post( $custoLoginPageId )->post_name;
	if( !is_page( $custoLoginPageSlug ) ){
		return;	
	}
	
	//selected theme 
	$selected_theme		= get_option('wts_clp_login_selected_theme');
	if(empty(get_option('wts_clp_login_selected_theme')))
	{
		//load content according theme1 by default
		$selected_theme='theme1';
	}
	
	$slctdThemeCssUrl	= plugin_dir_url( wts_clp()->file ) .'assets/css/' . $selected_theme.'_style.css';
	//echo $slctdThemeCssUrl;exit;
	wp_register_style( 'wts_clp_login_page_style', $slctdThemeCssUrl, false, rand() );
	wp_enqueue_style( 'wts_clp_login_page_style' );	

}

//add callback
add_action("wp_enqueue_scripts", "wts_clp_custom_login_page_inline_styles_load");
add_action("wp_enqueue_scripts", "wts_clp_custom_login_page_styles_load");

/**
* Admin area plugin settings page inline css.
*/

function wts_clp_loginarea_inline_style_load( $hook ) {	
	$loginArea_backround_img	= get_option('wts_clp_login_background_img');
	$loginArea_bgcolor			= get_option('wts_clp_login_bgcolor');
	$body_text_color			= get_option('wts_clp_color');
	
	/**
	* To add css for body part of this template first check any custom
	* theme of our plugin has been choosen or not according to that add css.
	*/
	$body_backround_img='';
	$body_background_color='';

	//when no theme selected for login page from settings page of our plugin.	
	if(empty(get_option('wts_clp_login_selected_theme')))
	{	
		//even background image not selected yet.
		if(empty(get_option('wts_clp_background_img')))
		{
			//background color is not set yet.
			if(empty(get_option('wts_clp_page_bgcolor')))
			{
				//thus this is default condition
				$body_backround_img		= plugin_dir_url( wts_clp()->file ) . 'assets/images/theme1-bodybackground.jpeg';
				$body_background_color	= '';
			}
			else
			{
				//body background color set by settings page.
				$body_backround_img='';
				$body_background_color=get_option('wts_clp_page_bgcolor');
			}
		}
		//when body background image set by settings page.
		else
		{
			$body_backround_img=get_option('wts_clp_background_img');
			$body_background_color='';
		}	
	}
	//when theme has been choosen then use as per theme.
	else
	{
		if(empty(get_option('wts_clp_background_img')))
		{
			if(empty(get_option('wts_clp_page_bgcolor')))
			{
				$theme					= get_option('wts_clp_login_selected_theme');
				$body_backround_img		= plugin_dir_url( wts_clp()->file ) . 'assets/images/'.$theme.'-bodybackground.jpeg';
				$body_background_color	= '';
			}
			else
			{
				$body_backround_img='';
				$body_background_color=get_option('wts_clp_page_bgcolor');
			}
		}
		else
		{
			$body_backround_img=get_option('wts_clp_background_img');
			$body_background_color='';
		}		
	}
	
	if(empty(get_option('wts_clp_login_background_img')))
	{
		if(empty(get_option('wts_clp_login_bgcolor')))
		{
			$loginArea_backround_img='';
			$loginArea_bgcolor='';
		}
		else
		{
			$loginArea_backround_img='';
			$loginArea_bgcolor=get_option('wts_clp_login_bgcolor');
		}

	}
	//login page styles 
	wp_enqueue_style(
		'wts-plugin-settings-pageinline-styles',
		plugin_dir_url( wts_clp()->file ) . 'wcl-options/assets/css/wts-options.css'
	);	

	if ( 'toplevel_page_wts-custom-login-options' == $hook ) {
		$customCssAdminPage = "
			.settings-preview-container {
				background-image: url(". $body_backround_img. ");
				background-color:". $body_background_color .";
			}

			.settings-preview-login-screen
			{
				background-image: url(". $loginArea_backround_img .");
				background-color:". $loginArea_bgcolor ." !important;
				color:". $body_text_color. " !important;
			}
			.submitbn input#wts_clp_login_submit {color:". $body_text_color. " !important;border: 1px solid ". $body_text_color. "; }
			.remember-user label,.forget_pass a,.forgot_themeopt a {
				color:". $body_text_color. " !important;
			}
			span.input-group-addon {
				color:". $body_text_color. " !important;
			}
		";	
		
		//add themewise css to be added to this 
		$themeWiseCss = wtsClpGetThemeWiseInlineStyle(); 
		if( !empty( $themeWiseCss ) ){
			$customCssAdminPage .= $themeWiseCss;
		}
		
		//echo $customCssAdminPage;exit;		
		//add this custom css as inline css
		wp_add_inline_style( 'wts-plugin-settings-pageinline-styles', $customCssAdminPage );	
    }
	
	//add custom inline css for front pages 
	$custoLoginPageId	= get_option( 'wts_clp_login_page_id' );
	$pp_obj=get_post( $custoLoginPageId );
	$custoLoginPageSlug	= $pp_obj->post_name;
	if( is_page( $custoLoginPageSlug ) ){
		$customCssFrontPage = "
			input:-webkit-autofill {
				border: 1px solid #ccc;
				-webkit-box-shadow: inset 0 0 0px 9999px white;
			}

			input:focus,
			input:-webkit-autofill:focus {
				border-color: #66afe9;
				-webkit-box-shadow: inset 0 0 0px 9999px white,
									0 0 8px rgba(102, 175, 233, 0.6);
			}
			.settings-preview-login-screen {
				background-image: url('". $loginArea_backround_img. "');
				background-color:". $loginArea_bgcolor ." ;
				color:". $body_text_color. "!important;				
			}
			.submitbn input#wts_clp_login_submit {color:". $body_text_color. " !important;border: 1px solid ". $body_text_color. "; }
			.remember-user label,.forget_pass a,.forgot_themeopt a {
				color:". $body_text_color. " !important;
			}
			span.input-group-addon {
				color:". $body_text_color. " !important;
			}
		";	
		
		//echo $customCssFrontPage;exit;
		//add this custom css as inline css
		wp_add_inline_style( 'wts-plugin-settings-pageinline-styles', $customCssFrontPage );	
	}
}


/**
* returns themewise inline css to be added.
*/
function wtsClpGetThemeWiseInlineStyle() {
	
	$themeWiseCss = '';
	//selected theme 
	$selected_theme		= get_option('wts_clp_login_selected_theme');
	if(empty(get_option('wts_clp_login_selected_theme')))
	{
		//load content according theme1 by default
		$selected_theme='theme1';
	}
	
	$custoLoginPageId	= get_option( 'wts_clp_login_page_id' );
	$custoLoginPageSlug	= get_post( $custoLoginPageId )->post_name;
	if( is_page( $custoLoginPageSlug ) ){
		$themeWiseCss = '';
	}	

	
	return $themeWiseCss; 
	
}
/**
* This loads admin area plugin settings page scripts 
*/
function wts_clp_plugin_settingPage_scripts_load( $hook ) {
	if ( 'toplevel_page_wts-custom-login-options' != $hook ) {
        return;
    }
	
	//get selected theme 
	$selected_theme				= get_option('wts_clp_login_selected_theme');

	if(empty(get_option('wts_clp_login_selected_theme')))
	{
		//load content according theme1 by default
		$selected_theme='theme1';
	}	
	
	//add theme wise admin page css or styles.
	$adminPageStyleUrl = plugin_dir_url( wts_clp()->file ) . 'assets/themes/' . $selected_theme. '/css/' . 'admin-page-style.css'; 
	//echo $adminPageStyleUrl;exit;
	//wp_register_style( 'wts_clp_admin_page_style', $adminPageStyleUrl, false, rand() );
	//wp_enqueue_style( 'wts_clp_admin_page_style' );
}
//load plugin settings page styles and scripts 
add_action( 'admin_enqueue_scripts', 'wts_clp_loginarea_inline_style_load' );
add_action( 'wp_enqueue_scripts', 'wts_clp_loginarea_inline_style_load' );
add_action( 'admin_enqueue_scripts', 'wts_clp_plugin_settingPage_scripts_load' );

/**
* Checkbox sanitization function
*/
function wts_clp_sanitize_checkbox( $input ){
	//returns true if checkbox is checked
	return ( isset( $input ) ? true : false );
}

/**
* callback function to remove core updates.For theme,plugin and core updates.
*/
function wts_clp_remove_all_core_updates() {
	global $wp_version;return(object) array('last_checked'=> time(),'version_checked'=> $wp_version,);
}

/**
* This will disable core updates for wordpress installation for e.g 
*  WordPress 3.9.1 is available! Please update now, from the all users dashboard, admin dashboard & from Updates page as well.
*/
function wts_clp_remove_core_updates()
{
	 if(! current_user_can('update_core')){return;}
	 add_action('init', create_function('$a',"remove_action( 'init', 'wp_version_check' );"),2);
	 add_filter('pre_option_update_core','__return_null');
	 add_filter('pre_site_transient_update_core','__return_null');
}

$allUpdateOption	= get_option( 'wts_clp_all_updates_enable_disable' );
$pluginUpdateOption = get_option( 'wts_clp_plugin_updates_enable_disable' );
$themeUpdateOption	= get_option( 'wts_clp_theme_updates_enable_disable' );

if( $allUpdateOption == 'disable' ) {
	
	/*
	//disables notification for theme,plugin,core updates (all updates)
	add_filter('pre_site_transient_update_core','wts_clp_remove_all_core_updates');
	add_filter('pre_site_transient_update_plugins','wts_clp_remove_all_core_updates');
	add_filter('pre_site_transient_update_themes','wts_clp_remove_all_core_updates');
	*/
	
	//to disable wordpress core updates.
	add_action('after_setup_theme','wts_clp_remove_core_updates');		
}

//handle plugin updates
if( $pluginUpdateOption == 'disable' ) {
	remove_action('load-update-core.php','wp_update_plugins');
	add_filter('pre_site_transient_update_plugins','__return_null');	
}

//handle theme updates 
if( $pluginUpdateOption == 'disable' ) {
	remove_action('load-update-core.php','wp_update_plugins');
	add_filter('pre_site_transient_update_themes','__return_null');	
}