<?php
/**
 * User login and registration functions.
 *
 * @since 1.0.0
 *
 * @package WTS_Custom_Login_Plugin
 * @author  WTS Team
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
 * Redirect user to correct page after login.
 *
 * @since 1.0.0
 */

function wts_clp_redirect_url( $user_id ) {

	$user = get_userdata( $user_id );

	if ( isset( $user->roles ) && is_array( $user->roles ) ) {
		//check for admins
		if ( in_array( 'administrator', $user->roles ) ) {			
			// redirect them to the default place
			return admin_url();
		}	
	}
}

add_action( 'init', 'wts_clp_login_user' );
/*
 * User login function.
 *
 * @since 1.0.0
 */
function wts_clp_login_user() {

	if( isset( $_POST['username'] ) && wp_verify_nonce( $_POST['wts_clp_login_nonce'], 'wts-clp-login-nonce' ) ) {
		// returns the user ID and other info from the user name.
		$user = get_user_by( 'login', sanitize_user( $_POST['username'],true ) );

		if ( ! $user ) {
			// if the user name doesn't exist.
			wts_clp_user_errors()->add( 'empty_username', __( 'Invalid username', 'wts-custom-login' ) );
			return;
		}

		if ( empty( $_POST['password'] ) ) {
			// if no password was entered.
			wts_clp_user_errors()->add( 'empty_password', __( 'Please enter a password', 'wts-custom-login' ) );
			return;
		}

		// check the user's login with their password.
		if ( ! wp_check_password( $_POST['password'], $user->user_pass, $user->ID ) ) {
			// if the password is incorrect for the specified user.
			wts_clp_user_errors()->add( 'empty_password', __( 'Incorrect password', 'wts-custom-login' ) );
			return;
		}
		
		//reCAPTCHA verification checks captcha was enabled at login screen or not
		if( !empty( get_option( 'wts_clp_login_IsCaptchaRequired' ) ) )
		{
			if ( isset( $_POST['g-recaptcha-response'] ) ) {
				if( !empty( $_POST['g-recaptcha-response'] ) ) {
					$recaptcha_secret = get_option('wts_clp_gcaptcha_privateKey');
					$response = wp_remote_get("https://www.google.com/recaptcha/api/siteverify?secret=". $recaptcha_secret ."&response=". $_POST['g-recaptcha-response']);
					$response = json_decode($response["body"], true);
					
					if (true == $response["success"]) {
					} else {
						wts_clp_user_errors()->add( 'Captcha Invalid', __( "<strong>ERROR</strong>: You are a bot",'wts-custom-login' ) );
					}				
				}
				else {
					wts_clp_user_errors()->add( 'Captcha Invalid', __( "<strong>ERROR</strong>: verify the captcha.",'wts-custom-login' ) );
				}

			} else {
				wts_clp_user_errors()->add( 'Captcha Invalid', __( "<strong>ERROR</strong>: You are a bot. If not then enable JavaScript",'wts-custom-login' ) );				
			}	
		}
		
		// retrieve all error messages.
		$errors = wts_clp_user_errors()->get_error_messages();

		// only log the user in if there are no errors.
		if ( empty( $errors ) ) {			
			wp_set_current_user( $user->ID, sanitize_user( $_POST['username'], true ) );
			wp_set_auth_cookie( $user->ID );
			do_action( 'wp_login', sanitize_user( $_POST['username'],true ) );

			wp_signon( array(
				'user_login' => sanitize_user( $_POST['username'] , true ),
				'user_password' => $_POST['password'],
				'remember' => isset( $_POST['remember_me'] ) ? true : false
			) );
			
			//wp_redirect( wts_clp_redirect_url( $user->ID ) );			
			wp_redirect(get_dashboard_url());
            exit;
		}

	}

}

//attach callback to 'wp_login_failed' hook.
add_action( 'wp_login_failed', 'wts_clp_login_failed' );

/**
* Send to login page if login failed
*
* @since 1.0.0
*/

function wts_clp_login_failed() {

	//get id of custom login page from wp_options table	
	$page_id=get_option('wts_clp_login_page_id');

	$login_page = home_url( '/?page_id='. $page_id. '/' );
	wp_redirect( $login_page . '&login=failed' );
	exit;
}

//add callback to 'authenticate' hook.
add_filter( 'authenticate', 'wts_clp_blank_username_password', 1, 3);

/**
* Send to login page if enetered blank username and password
*
* @since 1.0.0
*/
function wts_clp_blank_username_password( $user, $username, $password ) {

	//get id of custom login page from wp_options table	
	$page_id = get_option( 'wts_clp_login_page_id' );

	$login_page = home_url( '/?page_id='. $page_id. '/' );
	if( $username == "" || $password == "" ) {
		wp_redirect( $login_page . "&logout=blank" );
		exit;
	}
}

//add callback to 'wp_logout' hook. 
add_action('wp_logout', 'wts_clp_logout_page');

/**
* Send to logout screen after successfull logout.
* @since 1.0.0
*/

function wts_clp_logout_page() {
	//get id of custom login page from wp_options table	
	$page_id = get_option('wts_clp_login_page_id');

	$login_page = home_url( '/?page_id='. $page_id. '/' );
	wp_redirect( $login_page . "&login=false" );
	exit;
}

//add redirection callback to 'init' hook.Which redirects user if he is not logged in.
add_action('init','wts_clp_goto_login_page');

/**
* If this plugin is activated and user try to access wp-login or wp-admin
* without login it will send to custom login page
* @since 1.0.0
*/

function wts_clp_goto_login_page() {

$page_id	= get_option('wts_clp_login_page_id');
$login_page	= home_url( '/?page_id='. $page_id. '/' );
$page		= basename($_SERVER['REQUEST_URI']);

	if(!is_user_logged_in())
	{
		if( $page == "wp-login.php" ||$page == "wp-login" ||$page == "wp-admin" && $_SERVER['REQUEST_METHOD'] == 'GET') {
			
			wp_redirect($login_page);
			exit;
		}	
	}
	else
	{
		if( $page == "wp-login.php" ||$page == "wp-login" && $_SERVER['REQUEST_METHOD'] == 'GET') {
			wp_redirect(admin_url());
		    exit;
		  }
	}
}



/*
 * Function for tracking error messages.
 *
 * @since 1.0.0
 */
function wts_clp_user_errors() {

	static $wp_error; // Will hold global variable safely.
	return isset( $wp_error ) ? $wp_error : ( $wp_error = new WP_Error( null, null, null ) );

}

/*
 * Display error messages from form submissions.
 *
 * @since 1.0.0
 */
function wts_clp_show_error_messages() {
	if ( $codes =  wts_clp_user_errors()->get_error_codes() ) {
		echo '<div class="wts_clp_user_errors alert alert-danger">';
		    // Loop error codes and display errors.
		   foreach( $codes as $code ){
		        $message = wts_clp_user_errors()->get_error_message( $code );
		        echo '<span class="error"><strong>' . __('Error') . '</strong>: ' . $message . '</span><br/>';
		    }
		echo '</div>';
	}
}