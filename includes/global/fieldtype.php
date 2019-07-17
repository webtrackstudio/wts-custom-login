<?php
/**
 * Fieldtype class.
 *
 * @since 1.0.0
 *
 * @package WTS_Custom_Login
 * @author  Test Team
 */

class WTS_Custom_Login_Field_Type {
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
    public static $base;

	/**
	 * Primary class constructor.
	 *
	 * @since 1.0.0
	 */
    public function __construct() {

        // Load the base class object.
        self::$base = WTS_Custom_Login::get_instance();

    }

	public static function get_field( $args ) {

		if ( ! count($args) ) {
			return;
		}

		$type			= $args['type'];
		$id				= $args['id'];
		$name			= $args['name'];
		$value			= $args['value'];
		$class			= isset( $args['class'] ) ? $args['class'] : '';
		$placeholder 	= isset( $args['placeholder'] ) ? $args['placeholder'] : '';
		$required		= isset( $args['required'] ) ? $args['required'] : '';
		$selected		= isset( $args['selected'] ) ? $args['selected'] : '';
		$checked		= isset( $args['checked'] ) ? $args['checked'] : '';
		$date_field 	= isset( $args['date_field'] ) ? $args['date_field'] : '';
		$bind			= isset( $args['bind'] ) ? $args['bind'] : '';
		$disable		= isset( $args['disable'] ) ? $args['disable'] : '';
		$max			= isset( $args['max'] ) ? $args['max'] : '';
		$for			= isset( $args['for'] ) ? $args['for'] : '';
		$extras			= isset( $args['extras'] ) ? $args['extras'] : '';

		

		if ( $required == 'on' ) {
			$class .= ' required';
			$required = 'required="required"';
		} else {
			$required = '';
		}
		if ( $date_field == 'on' ) {
			$class .= ' datepicker';
		}
		if ( $placeholder != '' ) {
			$placeholder = 'placeholder="'.$placeholder.'"';
		}
		if ( $bind != '' ) {
			$bind = 'data-bind="'.$bind.'" data-name="'.$name.'"';
		}
		if ( $disable == 'on' ) {
			$disable = 'disabled="disabled"';
		}
		if ( $max != '' && $max >= 1 ) {
			$max = 'data-max="'.$max.'"';
		}
		if ( $for != '' ) {
			$for = 'data-for="'.$for.'"';
		}

		$field = '';
		$selected_attr = '';

		switch ( $type ) {

			case 'hidden' :

				$field = '<input type="hidden" id="'.$id.'" name="'.$name.'" value="'.$value.'" />';

			break;

			case 'text':
			case 'password':
			case 'search':
			case 'email':
			case 'url':

				$field = '<input type="'.$type.'" id="'.$id.'" name="'.$name.'" value="'.$value.'" class="wts-custom-login-field '.$class.'" '.$bind.' '.$placeholder.' '.$disable.' '.$for.' '.$required.''.$extras.' />';

			break;

			case 'textarea':

				$field = '<textarea id="'.$id.'" name="'.$name.'" class="wts-custom-login-field '.$class.'" '.$bind.' '.$placeholder.' '.$disable.' '.$for.' '.$required.' '.$extras.'>'.$value.'</textarea>';

			break;

			case 'select':

				$field = '<select id="'.$id.'" name="'.$name.'" class="wts-clp-field '.$class.'" style="width:100%;" '.$bind.' '.$disable.' '.$for.' '.$required.'>';
				$field .= '<option value="">Select</option>';
				foreach ( $value as $key=>$label ) {
					if ( $selected && $selected == $key ) {
						$field .= '<option value="'.$key.'" selected="selected">'.$label.'</option>';
					} else {
						$field .= '<option value="'.$key.'">'.$label.'</option>';
					}
				}
				$field .= '</select>';

			break;

			case 'checkbox':
			case 'radio':

				$field = '<input type="'.$type.'" id="'.$id.'" name="'.$name.'" value="'.$value.'" '.$checked.' class="wts-clp-field '.$class.'" '.$bind.' '.$disable.' '.$for.' '.$required.'/>';

			break;

			case 'file':

				$field = '<input type="'.$type.'" id="'.$id.'" name="'.$name.'" value="'.$value.'" class="wts-clp-field '.$class.'" '.$bind.' '.$disable.' '.$for.' '.$required.' />';

			break;

			case 'submit' :

				$field = '<input type="'.$type.'" id="'.$id.'" name="'.$name.'" value="'.$value.'" class="wts-clp-field '.$class.'" '.$bind.' '.$disable.' '.$for.' />';

			break;

		}

		return $field;

	}
	
	/**
     * Returns the singleton instance of the class.
     *
     * @since 1.0.0
     *
     * @return object The WTS_Custom_Login_Field_Type object.
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WTS_Custom_Login_Field_Type ) ) {
            self::$instance = new WTS_Custom_Login_Field_Type();
        }

        return self::$instance;

    }

}

// Load the posttype class.
$wts_clp_fieldtype = WTS_Custom_Login_Field_Type::get_instance();
