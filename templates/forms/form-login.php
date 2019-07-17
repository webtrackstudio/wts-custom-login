<?php
/**
 * The form template for login form.
 *
 * @since 1.0.0
 *
 * @package WTS_Custom_Login_Plugin
 * @author  WTS Team
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<?php 
//Login area logo 
$loginArea_logo				= get_option('wts_clp_logo');
if(empty(get_option('wts_clp_logo')))
{
	//set defult logo
	$loginArea_logo	= plugin_dir_url( wts_clp()->file ) .'assets/images/wts-clp-logo.png';
}
else {
	//set custom logo url
	$loginArea_logo = get_option( 'wts_clp_logo' );
}

?>
<div class="error">
<?php wts_clp_show_error_messages(); ?>
</div>	
<div class="wapper">
	<div class="container wts-clp-container">	
		<div class="row">
			<div class="col-sm-3 ">&nbsp </div>
			<div class="col-sm-6 wts-clp-login">
				<!--<h1>
					<a href="<?php echo esc_url( 'https://www.webtrackstudio.com/' ); ?>" title="<?php echo esc_html( 'Powered by WebTrackStudio' ); ?>" tabindex="-1">
						<img src="<?php echo esc_url( $loginArea_logo ); ?>" >
						</img>
					</a>
				</h1>-->
			</div>
			<div class="col-sm-3 ">&nbsp </div>
		</div>
		<div class="row">
				<div class="col-sm-3 loginleft_colm">&nbsp </div>
				<div class="col-sm-6 main"> 
				   <div class="main_box">
				    <div class="row wts-clp-login logo_img">
						<?php $blog_title = get_bloginfo( 'name' ); ?>
						<a class="site_logo" href="<?php echo esc_url( site_url() ); ?>" title="<?php echo esc_html( $blog_title ); ?>" tabindex="-1">
							<img src="<?php echo esc_url( $loginArea_logo ); ?>" >
						</a>
					</div>
					<div class="row bg">
						<?php do_action( 'wts_custom_login_before_user_login_form' ); ?>					
						<form id="wts_clp_login_form" name="wts_clp_login_form" class="wts-clp-login-form" method="POST" role="form" autocomplete="on" enctype="multipart/form-data">					
						<div class="input-group margin-bottom-sm">
							  <?php $userIconUrl = plugin_dir_url( wts_clp()->file ) .'assets/images/user_icon.png'; ?>
							  <span class="input-group-addon">
							  <i class="fa fa-user" aria-hidden="true"></i></span>
							  <?php /* ?><img src="<?php echo esc_url( $userIconUrl ); ?>"><?php */ ?>
							  </span>
							 <!-- <input class="form-control" type="text" placeholder="Login">-->
									<input type="hidden" name="wts_clp_login_nonce" value="<?php echo wp_create_nonce( 'wts-clp-login-nonce' ); ?>" /> 		
									<?php
										echo wts_clp()->fields->get_field( array(
											'type'		=> 'text',
											'id'		=> 'wts_clp_login_username',
											'name'		=> 'username',
											'value'		=> isset( $_POST['username'] ) ? sanitize_user( $_POST['username'] ) : '',
											'placeholder'=>'User Name',
											'class'		=>'form-control',	
											'required'	=> 'on'
										) );
									?>	
						</div>
						<div class="input-group paswrd_g">
							  <?php $passwrdKeyImg = plugin_dir_url( wts_clp()->file ) .'/assets/images/Key_password.png'; ?>
							  <span class="input-group-addon pasimg">
								<?php /* ?><img src="<?php echo esc_url( $passwrdKeyImg ); ?>"><?php */ ?>
								<i class="fa fa-key fa-fw"></i>
							  </span>
							  <!--<input class="form-control" type="password" placeholder="Password">-->
							  <?php
								echo wts_clp()->fields->get_field( array(
									'type'		=> 'password',
									'id'		=> 'wts_clp_login_password',
									'name'		=> 'password',
									'value'		=> '',
									'placeholder'=>'Password',
									'class'		=>'form-control',									
									'required'	=> 'on',
									'extras'	=> 'autocomplete="on"'
								) );
							  ?>							  
						</div>
						<?php  if(get_option('wts_clp_login_IsCaptchaRequired') == 1)//captcha to be shown
						{ 
						?>
							<div class="input-group wts-clp-captcha-container">
							<?php 
								//this will renders google recatpcha field with given public key
								$publickey = get_option( 'wts_clp_gcaptcha_publicKey' );		
								//echo recaptcha_get_html($publickey);	
								echo '<div class="g-recaptcha" data-sitekey="'.$publickey.'"></div>'; 
							?>
							</div>
						<?php 
						}						
						?>
						<div class="second submitbn">
							  <!--<input type="submit" value=""><i class="fa fa-caret-right" aria-hidden="true"></i></input>-->
								<!--<i class="fa fa-sign-in" aria-hidden="true">-->
								 	
									<?php
										echo wts_clp()->fields->get_field( array(
											'type'		=> 'submit',
											'id'		=> 'wts_clp_login_submit',
											'name'		=> 'submit',
											'value'		=> __( 'Login', 'wts-custom-login' ),											
											'required'	=> 'on'
										) );
									?>	
								</i>
						</div>
						<div class="row forget_rember">
						<!--<div class="col-sm-6 input-group remeber_pas">
							<span class="remember-user">
							<?php
							
								echo wts_clp()->fields->get_field( array(
									'type'		=> 'checkbox',
									'id'		=> 'wts_clp_login_remember',
									'name'		=> 'remember_me',
									'value'		=> 1,
									'required'	=> 'off'
								) );
								
							?>
								<label for="wts_clp_login_remember" ><?php _e( 'Remember Me?', 'wts-custom-login' ); ?></label>
							</span>	
						</div>-->
						<div class="col-sm-12 forgot forget_pass">
							<li class="forgot">
								<a href="<?php echo esc_url( wp_lostpassword_url( get_permalink() ) ); ?>"><?php _e( 'Forgot Password', 'wts-custom-login' ); ?></a>
							</li>					
						</div>
                        </div>
						</form>
						<?php do_action( 'wts_custom_login_after_user_login_form' ); ?>					
						
					</div><!--row bg ends here-->
				</div>
				</div>
				<div class="col-sm-3 loginright_colm">&nbsp </div>
		</div>
	</div>
</div>
