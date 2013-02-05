<?php
/**
 * PrsoThemeBootstrap
 *
 * Instantiates all required classes for the Pressoholics theme framework
 *
 * E.G Instantiates helpers, views
 *
 * PHP versions 4 and 5
 *
 * @copyright     Pressoholics (http://pressoholics.com)
 * @link          http://pressoholics.com
 * @package       pressoholics theme framework
 * @since         Pressoholics v 0.1
 */
 
 class PrsoThemeBootstrap extends PrsoThemeConfig {
 	
 	private $helpers_scan 	= array(); //Cache all helpers in helpers dir
 	private $views_scan		= array(); //Cache all views in views dir 
 	
 	/**
	* The full path to the directory which holds "presso_framework", WITHOUT a trailing DS.
	*
	*/
	protected $theme_root = NULL;
	protected $child_theme_root = NULL; //Path to child theme's framework folder if child theme is active
	
	/**
	* The full path to the directory which holds "helpers", WITHOUT a trailing DS.
	*
	*/
	protected $theme_helpers = NULL;
	
	/**
	* The full path to the directory which holds "views", WITHOUT a trailing DS.
	*
	*/
	protected $theme_views = NULL;
	protected $child_theme_views = NULL; //Path to child theme framework views folder if child theme is active
	
	/**
	* Unique slug prepended to all class names
	*
	*/
	protected $theme_class_slug = NULL;
 	
 	
 	
 	function __construct( $args = array() ) {
 		
 		//Set framework root (Parent Theme path)
		$this->theme_root 		= get_template_directory() . '/prso_framework';
		$this->child_theme_root = get_stylesheet_directory() . '/prso_framework';
		
		//Set framework helpers dir
		$this->theme_helpers = $this->theme_root . '/helpers';
		
		//Set framework views folder
		$this->theme_views 			= $this->theme_root . '/views';
		$this->child_theme_views	= $this->child_theme_root . '/views';
		
		//Set plugin Class slug to be prepended to class names making them unique
		$this->theme_class_slug = 'PrsoTheme';
		//REMOVE - $this->theme_class_slug = str_replace(' ', '', ucwords(str_replace('_', ' ', $this->theme_slug)));
 		
 		//Boot plugin
 		add_action( 'after_setup_theme', array( $this, 'boot' ) );
 	}
 	
 	/**
	* boot
	* 
	* Calls methods to scan helpers dir and load instances of all valid helpers found
	* 
	*/
 	public function boot( $args = array() ) {
 		
 		//Load app controller
 		if( $this->load_app_controller() ) {
 			
 			//Scan the helpers dir
	 		$this->helpers_scan = $this->scan_helpers();
	 		
	 		//Instantiate helpers
	 		$this->load_helpers();
	 		
	 		//Scan the views dir
	 		$this->views_scan = $this->scan_views();
	 		
	 		//If user is admin load admin views
	 		$this->load_admin_views();
	 		
	 		//Load general app functions
	 		$this->load_app_functions();
	 		
	 		//Load framework shortcodes
	 		$this->load_shortcodes();
	 		
	 		//Load framework custom WP Walkers
 			$this->load_walkers();
 			
 			//Load Orbit banner system
 			$this->load_orbit_banner();
 			
 		} else {
		
			//Error loading app controller
			wp_die( wp_sprintf( '%1s: ' . __( 'Sorry, this theme requires the Pressoholics framework plugin to work.', 'prso_core' ), __FILE__ ) );
			
		}
 		
 	}
 	
 	/**
	* load_app_controller
	* 
	* Loads the app_controller class, which contains common methods shared by all presso plugins
	* 
	*/
 	private function load_app_controller() {
		
		//Init vars
		$result = false;
		$args	= array(
			'plugin_root_dir' 	=> $this->theme_root,
			'plugin_class_slug'	=> $this->theme_class_slug
		);
		
		$result = apply_filters( 'prso_core_load_plugin_app_controller', $result, $args );
 		
 		return $result;
 	}
 	
 	/**
	* scan_helpers
	* 
	* Scans theme framework helpers dir, caches and dir found in
	* $this->helpers_scan array.
	*
	* Returns false on error
	* 
	*/
 	private function scan_helpers() {
 			
 		//Init vars
 		$result = false;
 		$scan	= null; //Cache result of dir scan
 		
 		if( isset($this->theme_helpers) ) {
 			$scan = scandir( $this->theme_helpers );
 			
 			//Loop scandir result and store any found dirs in $result
 			foreach( $scan as $dir ) {
 				//Ignore any root designations
 				if( !empty($dir) && $dir != '.' && $dir != '..' ) {
 					if( is_string($dir) ) {
 						$result[] = $dir;
 					}
 				}
 			}
 		}
 		
 		return $result;
 	}
 	
 	/**
	* load_helpers
	* 
	* Checks to see if any valid helpers where found in $this->helpers_scan
	* If the helper file exsists an instance is created and the helper object is
	* stored in a global var which matches the following convension:
	*
	* 'Prso' . Helpername(uppercase)  e.g  PrsoHtml
	*
	* Call helper methods in wordpress template by:
	* Global PrsoHelpername;
	* $PrsoHelpername->method();
	* 
	*/
 	private function load_helpers() {
 		
 		if( $this->helpers_scan && is_array( $this->helpers_scan ) ) {
 			
 			//Loop the result of the helpers dir scan and try to instantiate each helper class
 			foreach( $this->helpers_scan as $helper ) {
 				
 				//Check if helper file exsists
 				if( file_exists( $this->theme_helpers . '/' . $helper . '/' . $helper . '.php' ) ) {
 					
 					//Include the helper file
 					include_once( $this->theme_helpers . '/' . $helper . '/' . $helper . '.php' );
					
 					//Ucase first letter of helper name to fit convension
 					$helper			= ucfirst($helper);
 					$helper_class 	= $helper . 'Helper';
 					$helper_global	= 'Prso' . ucfirst($helper); //Add a unique var name to avoid conflicts
 					
 					//Instantiate class
 					if( class_exists( $helper_class ) ) {
 						new $helper_class;
 					}
 				}
 			}
 			
 		}
 		
 	}
 	
 	/**
	* scan_views
	* 
	* Scans theme framework views dir, caches any files found in
	* $this->views_scan array.
	*
	* Returns false on error
	* 
	*/
 	private function scan_views() {
 			
 		//Init vars
 		$result = false;
 		$args	= array(
			'plugin_views_dir' 			=> $this->theme_views,
			'plugin_child_views_dir'	=> $this->child_theme_views
		);
 		 		
 		$result = apply_filters( 'prso_core_scan_plugin_views', $result, $args );
 		
 		return $result;
 	}
 	
 	/**
	* load_admin
	* 
	* Detects if user is logged in, if so then it detects the theme framework admin view
	* file in $this->theme_views dir and creates an instance of the class.
	* 
	*/
 	private function load_admin_views() {
 		
 		$args = array(
			'views_scan' 				=> $this->views_scan,
			'plugin_class_slug'			=> $this->theme_class_slug,
			'plugin_views_dir'			=> $this->theme_views,
			'plugin_child_views_dir'	=> $this->child_theme_views
		);
 		
 		do_action( 'prso_core_load_plugin_views', $args );
 		
 	}
 	
 	/**
	* load_app_functions
	* 
	* Loads the app_functions class, which contains all custom methods for this app
	* 
	*/
 	private function load_app_functions() {
 		
 		//Init vars
 		$args 	= array(
			'plugin_root_dir' 	=> $this->theme_root,
			'plugin_class_slug'	=> $this->theme_class_slug
		);
 		
 		do_action( 'prso_core_load_plugin_functions', $args );
 		
 	}
 	
 	private function load_shortcodes() {
 		
 		//Init vars
 		$file_path 	= $this->theme_root . '/shortcodes.php';
 		$class		= $this->theme_class_slug . 'Shortcodes';
 		
 		//Check file exsists in framework
 		if( file_exists($file_path) ) {
 			
 			include_once($file_path);
 			
 			if( class_exists($class) ) {
 				new $class();
 			}
 			
 		}
 		
 	}
 	
 	private function load_walkers() {
 		
 		//Init vars
 		$file_path 	= $this->theme_root . '/walkers.php';
 		$child_theme_file_path = $this->child_theme_root . '/walkers.php';
 		
 		//If a child theme is being used check to see if it has any walkers to load
 		if( is_child_theme() ) {
	 		//Check file exsists in framework
	 		if( file_exists($child_theme_file_path) ) {
	 			
	 			include_once($child_theme_file_path);
	 			
	 		}
 		}
 		
 		//Check file exsists in framework
 		if( file_exists($file_path) ) {
 			
 			include_once($file_path);
 			
 		}
 		
 	}
 	
 	private function load_orbit_banner() {
 		
 		//Init vars
 		$file_path 	= $this->theme_root . '/orbit.php';
 		
 		//Check file exsists in framework
 		if( file_exists($file_path) ) {
 			
 			include_once($file_path);
 			
 		}
 		
 	}
 	
 }