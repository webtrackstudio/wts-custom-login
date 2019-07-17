<?php
/**
 * Shortcodes class.
 *
 * @since 1.0.0
 *
 * @package WTS_Custom_Login
 * @author  WTS Team
 */

class WTS_Custom_Login_Shortcodes {
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
     * Primary class constructor.
     *
     * @since 1.0.0
     */
    public function __construct() {

        // Load the base class object.
        $this->base = WTS_Custom_Login::get_instance();		
		add_shortcode( 'wts_clp_user_login', array( $this, 'wts_clp_user_login' ) );
	}
	
	/**
     * This used to add login form in content.
     *
     * @since 1.0.0
     */
	 
	public function wts_clp_user_login( $atts ) {
		if ( ! is_user_logged_in() ) {
			ob_start();
		?>
        <div class="container-fluid">
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
					<?php
						//get selected theme 
						$selected_theme	= get_option('wts_clp_login_selected_theme');
						if(empty(get_option('wts_clp_login_selected_theme')))
						{
							//load content according theme1 by default
							$selected_theme='theme1';
						}
						
						//load theme wise form 
						wts_clp_load_template( 'forms/form-login-'.$selected_theme );
					?>
				</div>
			</div>
        </div>
		<?php
			return ob_get_clean();
		}

	}
	
	/**
     * Returns the singleton instance of the class.
     *
     * @since 1.0.0
     *
     * @return object The WTS_Custom_Login_Shortcodes object.
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WTS_Custom_Login_Shortcodes ) ) {
            self::$instance = new WTS_Custom_Login_Shortcodes();
        }

        return self::$instance;

    }	
}
// Load the posttype admin class.
$wts_clp_shortcodes = WTS_Custom_Login_Shortcodes::get_instance();