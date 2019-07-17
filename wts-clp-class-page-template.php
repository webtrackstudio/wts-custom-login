<?php
/**
* Wts_Clp_PageTemplater class creates custom page templates.
* this template will be shown in dorpdown list of available templates
* of wordpress for our pages.And we can choose these custom templates 
* for our pages. 
*/

class Wts_Clp_PageTemplater {
	
	/**
	 * This is reference to store instance of this class.	
	*/
	
	private static $instance;
	
	/**
	 * This is an array of templates which will be tracked by 
	 * this plugin.
	 */
	
	protected $templates;
	
	/**
	 * This function returns an instance of this class. 
	 * @return Wts_Clp_PageTemplater
	 */
	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new Wts_Clp_PageTemplater();
		} 
		return self::$instance;
	} 
	
	/**
	 * This is constructor of this class.
	 * @return void
	 */
	
	private function __construct() {
		
		$this->templates = array();
		
		// Add a filter to the attributes metabox to inject template into the cache.
		if ( version_compare( floatval( get_bloginfo( 'version' ) ), '4.7', '<' ) ) {
			// for 4.6 and older version of wordpress
			add_filter(
				'page_attributes_dropdown_pages_args',
				array( $this, 'wts_clp_register_project_templates' )
			);
		} else {
			// Add a filter to the wp 4.7 version attributes metabox to add new template.
			add_filter(
				'theme_page_templates', array( $this, 'wts_clp_add_new_template' )
			);
		}
		// Add a filter to the save post or 'wp_insert_post_data' hook to inject out template into the page cache
		add_filter(
			'wp_insert_post_data', 
			array( $this, 'wts_clp_register_project_templates' ) 
		);
		// This will add filter to check that page has assigned our template or return path of that 
		// template assigned and return it's path
		add_filter(
			'template_include', 
			array( $this, 'view_project_template') 
		);
		/**
		* This is array of our custom temlates to be added.So	
		* add your custom templates to this array.
		*/
		$this->templates = array( 
			'templates/pages/wts-clp-login-page-template.php' => 'Wts Clp Login Page',
		);	
	} 
	
	/**
	 * Adds our template to the page dropdown for v4.7+
	 * Means it will add our template in dropdown list of templates 
	 * available for a page.
	 *
	 * @return post templates array.
	 *
	 */
	public function wts_clp_add_new_template( $posts_templates ) {
		$posts_templates = array_merge( $posts_templates, $this->templates );
		return $posts_templates;
	}
	
	/**
	 * This will manupulate wordpress in such a way that wordpress think our custom
	 * template file exists where it doens't really exist.By adding
	 * our template to the pages cache.  
	 *
	 * @return array
	 */
	public function wts_clp_register_project_templates( $atts ) {
		
		//this generates the key used for the themes cache.
		$cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );
		
		//This will generate a template list if this doesn't exist then it
		//will add an array.
		$templates = wp_get_theme()->get_page_templates();
		if ( empty( $templates ) ) {
			$templates = array();
		} 
		// New cache generated, therefore remove the old one
		wp_cache_delete( $cache_key , 'themes');
		
		// Now add our template to the list of templates by merging our templates
		// with the existing templates array from the cache.
		$templates = array_merge( $templates, $this->templates );
		
		// Add the modified cache to allow WordPress to pick it up for listing
		// available templates
		wp_cache_add( $cache_key, $templates, 'themes', 1800 );
		
		return $atts;
	}
	
	/**
	 * This function checks that template is asssigned to page or not.
	 * @return temlate
	 */	 
	public function view_project_template( $template ) {
		
		// Return the search template if we're searching (instead of the template for the first result)
		if ( is_search() ) {
			return $template;
		}
		
		// Get global post
		global $post;
		
		// Return template if post is empty
		if ( ! $post ) {
			return $template;
		}
		
		// Return default template if we don't have a custom one defined
		if ( ! isset( $this->templates[get_post_meta( 
			$post->ID, '_wp_page_template', true 
		)] ) ) {
			return $template;
		}
		
		$file = plugin_dir_path( __FILE__ ). get_post_meta( 
			$post->ID, '_wp_page_template', true
		);

		// Just to be safe, we check if the file exist first
		if ( file_exists( $file ) ) {
			return $file;
		} else {
			echo $file;
		}
		// Return template
		return $template;
	}
} 