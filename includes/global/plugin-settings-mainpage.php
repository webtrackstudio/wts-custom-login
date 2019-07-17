<?php
/**
* This register plugin settings as per option group or callback functions.
* @since 1.0.0
*/
function wts_clp_plugin_register_settings() {
	register_setting( 'wts_clp_options_group', 'wts_clp_login_page_name_bySettingsPage', 'change_wts_clp_loginPageName_callback' );   
	register_setting( 'wts_clp_options_group', 'wts_clp_login_IsCaptchaRequired', 'wts_clp_IsCaptchaRequired_callback' );
	register_setting( 'wts_clp_options_group', 'wts_clp_gcaptcha_publicKey', 'myplugin_callback' );
	register_setting( 'wts_clp_options_group', 'wts_clp_gcaptcha_privateKey', 'myplugin_callback' );

	register_setting( 'wts_clp_options_group', 'wts_clp_logo', 'myplugin_callback' );
	register_setting( 'wts_clp_options_group', 'wts_clp_adminbar_logo', 'myplugin_callback' );
	register_setting( 'wts_clp_options_group', 'wts_clp_background_img', 'myplugin_callback' );
	register_setting( 'wts_clp_options_group', 'wts_clp_page_bgcolor', 'myplugin_callback' );
	register_setting( 'wts_clp_options_group', 'wts_clp_login_background_img', 'myplugin_callback' );
	register_setting( 'wts_clp_options_group', 'wts_clp_login_bgcolor', 'myplugin_callback' );
	register_setting( 'wts_clp_options_group', 'wts_clp_color', 'myplugin_callback' );
	register_setting( 'wts_clp_options_group', 'wts_clp_custom_css', 'myplugin_callback' );   
	register_setting( 'wts_clp_options_group', 'wts_clp_login_selected_theme', 'myplugin_callback' );
	
	
	//register settings regarding wordpress updates.
	register_setting( 'wts_clp_options_group', 'wts_clp_all_updates_enable_disable', 'wts_clp_enable_disable_all_updates' );
	register_setting( 'wts_clp_options_group', 'wts_clp_plugin_updates_enable_disable', 'wts_clp_enable_disable_plugin_updates' );
	register_setting( 'wts_clp_options_group', 'wts_clp_theme_updates_enable_disable', 'wts_clp_enable_disable_theme_updates' );
}
//add plugin register settings function to 'admin_init' hook.
add_action( 'admin_init', 'wts_clp_plugin_register_settings' );


/**
* This is a callback function which triggers when settings from settings page sent to save 
* by submit button on that page
* This function updates custom login page slug and it's value in default option 'wts_clp_login_page_name'
*/

function change_wts_clp_loginPageName_callback()
{
	
	//update slug of login page and it's slug value in database
	$new_login_page_name = sanitize_text_field( $_POST['wts_clp_login_page_name_bySettingsPage'] );	
	$my_post = array(
	  'ID'           => get_option('wts_clp_login_page_id'),
	  'post_name' => $new_login_page_name,
	  'post_title' => $new_login_page_name,
	);

	// Update the post into the database
	wp_update_post( $my_post );
	//now update this in default login page option also
	update_option('wts_clp_login_page_name',$new_login_page_name, 'yes' );
}

/**
* @since 1.0.0
* Check for public and private keys for google captcha.And 
* show respective notices.
*/
function wts_clp_IsCaptchaRequired_callback()
{
	$isCaptchaRequired = wts_clp_sanitize_checkbox( $_POST['wts_clp_login_IsCaptchaRequired'] );
	if( $isCaptchaRequired )
	{
		$gcaptchaPublicKey = sanitize_text_field( $_POST['wts_clp_gcaptcha_publicKey'] );
		$gcaptchaPrivateKey = sanitize_text_field( $_POST['wts_clp_gcaptcha_privateKey'] );
		if( empty( $gcaptchaPublicKey ) )
		{
			add_settings_error(
				'Do You Want To Show Google Captcha On Login Screen?',
				esc_attr( 'settings_updated' ),
				'please enter public key for google captcha',
				error
			);
			return false;	
		}
		if( empty( $gcaptchaPrivateKey ) )
		{
			add_settings_error(
				'Do You Want To Show Google Captcha On Login Screen?',
				esc_attr( 'settings_updated' ),
				'please enter private key for google captcha',
				error
			);
			return false;		
		}
		
		return true;
	}
	else
	{
		return false;
	}
	
}

/*
* This is callback function which saves selected theme in options table
*/
function wts_clp_login_selected_theme_callback()
{
	//theme selection callback
}


add_action('admin_menu', 'wts_custom_login_plugin_menu');

/**
* Step 1.
* Adds custom login plugin menus.
* @since 1.0.0
*/
function wts_custom_login_plugin_menu(){
    //add_menu_page('WTS Custom Login Plugin', 'WTS CustomLogin Menu', 'manage_options', 'my-menu', 'my_menu_output' );
    add_menu_page( 'WTS Custom Login', 'WTS Custom Login','manage_options','wts-custom-login-options','WtsCustomLogin_Plugin_MainPage','',8 );
}

/**
* @since 1.0.0
* Load plugin settings main page with settings form.
*/
function WtsCustomLogin_Plugin_MainPage()
{
	?>
	<div class="container-fluid">
		<div class="row">
			<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<?php wts_clp_load_template( 'forms/settings-page-form' ); ?>
			</div>
		</div>
	</div>
<?php
}


/*---------enable or disable wordpress updates all,theme or plugin updates.---------*/

//enable or disable all wordpress updates
function wts_clp_enable_disable_all_updates() {
	
	if( !empty( sanitize_text_field( $_POST[ 'wts_clp_all_updates_enable_disable' ] ) ) ) {
		
		$allUpdatesEnableDisable = sanitize_text_field( $_POST[ 'wts_clp_all_updates_enable_disable' ] );
		
		if( $allUpdatesEnableDisable == 'enable' ) {		
			//enable all wordpress core or otherwise updates
			add_filter( 'automatic_updater_disabled', '__return_false' );			
			return 'enable';
		}else if( $allUpdatesEnableDisable == 'disable' ){		
			//disable all wordpress updates
			add_filter( 'automatic_updater_disabled', '__return_true' );
			return 'disable';
		}
		else {
			//by default
			//enable all wordpress core or otherwise updates
			add_filter( 'automatic_updater_disabled', '__return_false' );		
			
			return 'enable';
		}		
	}
	else {
		return 'enable';
	}
}

/**
* @since 1.0.0
* Enable or disable plugin updates
*/
function wts_clp_enable_disable_plugin_updates() {
	
	if( !empty( sanitize_text_field( $_POST[ 'wts_clp_plugin_updates_enable_disable' ] ) )) {
		$plugin_updates_en_dis = sanitize_text_field( $_POST[ 'wts_clp_plugin_updates_enable_disable' ] );	
		if( $plugin_updates_en_dis == 'enable' ) {		
			//enable all plugin updates
			add_filter( 'auto_update_plugin', '__return_true' );
			return 'enable';
		}else if( $plugin_updates_en_dis == 'disable' ){		
			//disable all plugin updates
			add_filter( 'auto_update_plugin', '__return_false' );
			return 'disable';
		}
		else {
			//by default
			//enable all plugin updates
			add_filter( 'auto_update_plugin', '__return_true' );		
			return 'enable';
		}		
	}
	else {
		return 'enable';
	}	
}

/**
* @since 1.0.0
* Enable or disable theme updates
*/
function wts_clp_enable_disable_theme_updates() {
	$ret_value = '';
	$theme_updates_en_dis = '';
	
	if( !empty( sanitize_text_field($_POST[ 'wts_clp_theme_updates_enable_disable' ] ) )) {
		$theme_updates_en_dis = sanitize_text_field( $_POST[ 'wts_clp_theme_updates_enable_disable' ]);
		
		if( $theme_updates_en_dis == 'enable' ) { 			
			//enable all theme updates
			add_filter( 'auto_update_theme', '__return_true' );
			$ret_value = 'enable';
		}else if( $theme_updates_en_dis == 'disable' ){			
			//disable all theme updates
			add_filter( 'auto_update_theme', '__return_false' );
			$ret_value = 'disable';
		}
		else {
			//by default enable all theme updates
			add_filter( 'auto_update_theme', '__return_true' );		
			$ret_value = 'enable';
		}
		
	}
	else{
		$ret_value = 'enable';
	}
	return $ret_value;
}
