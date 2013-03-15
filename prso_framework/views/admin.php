<?php
/**
 * Theme Admin View File
 *
 * Sets options and creates an admin page for your plugin
 *
 * NOTE:: 	Copy this file and use as a template for every admin page you
 *			require for your plugin
 *
 *
 * @copyright     Pressoholics (http://pressoholics.com)
 * @link          http://pressoholics.com
 * @package       pressoholics theme framework
 * @since         Pressoholics v 0.1
 */

class PrsoThemeAdminView extends PrsoThemeAppController {
	
/**
 * Contents
 *
 * 1. View Slug
 * 2. View Title
 * 3. View Menu Title
 * 4. View Menu Icon
 * 5. View Capability
 * 6. Options form submit button options
 * 7. Add menu page for plugin view
 * 8. Setup option page sections
 * 9. Setup option fields
 * 10. Options form Validation
 * 11. Add HTML before form
 * 12. Add HTML before submit button
 * 13. Add HTML after options form
 * 14. Custom Methods start here :)
 *
 */
 
 
/******************************************************************
 * 1. 	View Slug
 *		Define a unique slug for this options view
 *****************************************************************/ 
 	public $view_slug	= 'main_options';
 
 
/******************************************************************
 * 2. 	View Title
 *		Define a title for this view
 *****************************************************************/ 
 	public $view_title	= 'Prso Plugin Example';
	
	
/******************************************************************
 * 3. 	View Menu Title
 *		Define a title for this view's menu item
 *****************************************************************/ 
 	public $view_menu_title	= 'Prso Plugin Example';
 	

/******************************************************************
 * 4. 	View Menu Icon
 *		Define url to menu icon for this view OR use one of wp defaults
 *		You can use the plugin_dir_url( __FILE__ ) function to get the URL of your 
 *		plugin directory and then add the image filename to it.
 *****************************************************************/ 
 	public $view_icon	= NULL;


/******************************************************************
 * 5. 	View Capability
 *		Define the user capability required to access this view
 *****************************************************************/ 
 	public $capability	= 'administrator';
 	
 	
/******************************************************************
 * 6. 	Options form submit button options
 *		Customize how the submit button is rendered for your options form
 *****************************************************************/ 
 	public $submit_title	= 'Submit';
	
	
/******************************************************************
 * 7. 	Add menu page for plugin view
 *		Set args for either a main menu page or sub menu page for your plugin view
 *		Comment out the array you don't need
 *****************************************************************/
	/**
	* 'position':
	* 	 2 Dashboard
		 4 Separator
		 5 Posts
		 10 Media
		 15 Links
		 20 Pages
		 25 Comments
		 59 Separator
		 60 Appearance
		 65 Plugins
		 70 Users
		 75 Tools
		 80 Settings
		 99 Separator
		 'bottom' Bottom of menu - best choice
	*
	**/
	
	public $main_menu_page = array(
		'position'	=> 	'bottom'
	);
	
	
	/**
	* 'parent_slug':
		For Dashboard: add_submenu_page('index.php',...)
	    For Posts: add_submenu_page('edit.php',...)
	    For Media: add_submenu_page('upload.php',...)
	    For Links: add_submenu_page('link-manager.php',...)
	    For Pages: add_submenu_page('edit.php?post_type=page',...)
	    For Comments: add_submenu_page('edit-comments.php',...)
	    For Custom Post Types: add_submenu_page('edit.php?post_type=your_post_type',...)
	    For Appearance: add_submenu_page('themes.php',...)
	    For Plugins: add_submenu_page('plugins.php',...)
	    For Users: add_submenu_page('users.php',...)
	    For Tools: add_submenu_page('tools.php',...)
	    For Settings: add_submenu_page('options-general.php',...)
	*
	**/
	/*
	public $sub_menu_page = array(
		'parent_slug'	=> 'options-general.php'
	);
	*/
	
/******************************************************************
 * 8. 	Setup option page sections
 *		Register sections for your plugin options page
 *****************************************************************/
 	
 	/**
 	* Add an array for each section you wish to create
 	*
 	* Help:
 	*	$page_sections[] = array(
 	*		'id'	=>	'general_options',
 	*		'title'	=>	'General Options',
 	*		'callback'	=> array($this, 'callback_function') //Change the section description html
 	*	);
 	*
 	**/
 	public $page_sections = array(
 		array(
 			'id'	=>	'general_options',
 			'title'	=>	'General Options'
 		)
 	);
 
 
/******************************************************************
 * 9. 	Setup option fields
 *		Register fields for your options page
 *****************************************************************/
 	
 	/**
 	* Add an array for each field you wish to create
 	*
 	* 'type' is required as this is used by the admin model (in presso plugin) 
 	*   to decide how to output the form element html.
	* 
	* 	'type' Options:
	*					text, hidden, multi-text, text_area, 
	*					wysiwyg, select, checkbox, multi-checkbox, 
	*					radio, media
 	*
 	* Help:
 	*	$page_fields[] = array(
	*			'section' 	=> 'general_options',
	*			'id'		=> 'field_legal_html',
	*			'title'		=> '',
	*			'desc'		=> '',
	*			'type'		=> '',
	*			'default'	=> ''
	*		);
 	*
 	**/
 	public $page_fields = array(
 		array(
 			'section'	=>	'general_options',
 			'id'		=>	'input_test_html',
 			'title'		=>	'Test Input Field',
 			'desc'		=>	'Description for this field',
 			'type'		=>	'text',
 			'default'	=>	'default value'
 		)
 	);

/******************************************************************
 * 10. 	Options form Validation
 *		Customize options form validation feedback
 *****************************************************************/ 
 	public $validation_success_message	= NULL;
 	
 	public $validation_fail_message	= NULL;
 	
 	/**
 	* To validate an option add it's field name into the array.
 	*
 	* 	'type' tells the validation model how to validate the field e.g phone_us, email, url, password
	*	'empty'	NOT REQUIRED will tell the validator that you are happy to let this field be null
	*	'regex'	This will override the 'type' arg and the validator will use the regex provided to 
	*			validate the field
 	*
 	* Example:
 	*	$validate_fields['field_name'] = array( 
	*										'nice_name' => 'Facebook Page Url', 
	*										'type' => 'url', 
	*										'message' => 'Invalid URL.', 
	*										'empty' => true ,
	*										'regex' => null 
 	*	);
 	*
 	**/
 	public $validate_fields = array(
 		'input_test_html'	=>	array(
 			'nice_name'	=>	'Test Field',
 			'type'		=>	'text',
 			'message'	=>	'Invalid Value',
 			'empty'		=>	FALSE
 		)
 	);
	
 
/******************************************************************
 * 11. 	Add HTML before form
 *		Place any HTML you want to appear BEFORE options form in this function
 *****************************************************************/	

	public function render_before_options_form() {
		
		//Init vars
		$output = NULL;
		
		ob_start();
		//ADD YOUR CUSTOM HTML HERE
		?>
		
		<?php
		$output = ob_get_contents();
		ob_end_clean();
		
		echo $output;
	}


/******************************************************************
 * 12. 	Add HTML before submit button
 *		Place any HTML you want to appear BEFORE form submit button in this function
 *****************************************************************/
	
	public function render_before_options_form_submit() {
		
		//Init vars
		$output = NULL;
		
		ob_start();
		//ADD YOUR CUSTOM HTML HERE
		?>
		
		<?php
		$output = ob_get_contents();
		ob_end_clean();
		
		echo $output;
	}
	
	
/******************************************************************
 * 13. 	Add HTML after options form
 *		Place any HTML you want to appear AFTER options form in this function
 *****************************************************************/
	
	public function render_after_options_form() {
		
		//Init vars
		$output = NULL;
		
		ob_start();
		//ADD YOUR CUSTOM HTML HERE
		?>
		
		<?php
		$output = ob_get_contents();
		ob_end_clean();
		
		echo $output;
	}
	
	
/******************************************************************
 * 14. 	Custom Method
 *		Description
 *****************************************************************/

	
	
	
	
	
	
	
	
	
	
/******************************************************************
 * Core functions DO NOT TOUCH
 *		
 *****************************************************************/
	
	function __construct() {
		
		//Ensure vars set in config are available
 		//parent::__construct();
		
 		//Make sure view slug is unique, prepend plug slug to it
 		$this->view_slug = $this->get_slug( $this->view_slug );
		
		//Confirm PrsoCore plugin is loaded
		//$this->prso_core_active();
		
		//Call method to cache plugin options data in plugin's $data array - see app_controller.php
		$this->get_options( $this->view_slug );
		
		//Add main parent page for theme admin section
 		add_action( 'admin_menu', array($this, 'register_plugin_menu') );
		
		//Register sections and define fields
		add_action( 'admin_init', array($this, 'create_settings') );
		
	}
	
	/**
	* register_plugin_menu
	* 
	* Makes use of options set in plugin view file to setup an options view in wordpress
	* 
	* @access 	public
	* @author	Ben Moody
	*/
	public function register_plugin_menu() {
		
		//Init vars
		$plugin_slug = NULL;
		$page_args = array(
			'page_title'	=>	NULL,
			'menu_title'	=>	NULL,
			'capability'	=>	NULL,
			'menu_slug'		=>	NULL,
			'icon_url'		=>	'options-general'	
		);
		
		$plugin_slug  = $this->theme_options_db_slug;
		
		if( is_object($this) ) {
			
			//Cache common args from view object
			if( isset($this->view_title) ) {
				$page_args['page_title'] = $this->view_title;
			}
			
			if( isset($this->view_menu_title) ) {
				$page_args['menu_title'] = $this->view_menu_title;
			}
			
			if( isset($this->capability) ) {
				$page_args['capability'] = $this->capability;
			}
			
			if( isset($this->view_slug) ) {
				$page_args['menu_slug'] = $this->view_slug;
			}
			
			if( isset($this->view_icon) ) {
				$page_args['icon_url'] = $this->view_icon;
			}
			
			//First detect if we are adding a main menu or sub menu
			if( isset($this->main_menu_page['position']) ) {
				
				if( $this->main_menu_page['position'] = 'bottom' ) {
					$this->main_menu_page['position'] = NULL;
				}
				
				//Call wordpress function to create main menu item
				add_menu_page(
					$page_args['page_title'],
					$page_args['menu_title'],
					$page_args['capability'],
					$page_args['menu_slug'],
					array( $this, 'start_plugin_options_page' ),
					$page_args['icon_url'],
					$this->main_menu_page['position']
				);
				
			} elseif( isset($this->sub_menu_page['parent_slug']) ) {
				
				add_submenu_page(
					$this->sub_menu_page['parent_slug'],
					$page_args['page_title'],
					$page_args['menu_title'],
					$page_args['capability'],
					$page_args['menu_slug'],
					array( $this, 'start_plugin_options_page' )
				);
				
			}
			
		}
		
	}
	
	/**
	* start_option_page
	* 
	* Used by wp function add_menu_page to create and parse the admin options page
	* Uses the PrsoCore 'prso_core_render_plugin_view' filter to call the required WP functions
	* and generate the required html to render a WP compliant plugin options page.
	*
	* NOTE you can insert custom html into the view using some filters:
	*	+ Before option page form use $this->render_before_options_form
	*	+ Before options page form submit button use $this->render_before_options_form_submit
	*	+ After options page form use $this->render_after_options_form
	* 
	* @access 	public
	* @author	Ben Moody
	*/
	public function start_plugin_options_page() {
		
		//Init args
		$view_output = NULL;
		$args = array(
			'screen_icon' 	=> $this->view_icon,
			'view_slug'		=> $this->view_slug,
			'view_title'	=> $this->view_title,
			'submit_title'	=> $this->submit_title
		);
		
		//Add custom html before the option page form - see $this->render_before_options_form
		add_filter( 'prso_core_render_plugin_view_before_form', array( $this, 'render_before_options_form' ) );
		
		//Add custom html after the option page form - see $this->render_before_options_form_submit
		add_filter( 'prso_core_render_plugin_view_before_submit', array( $this, 'render_before_options_form_submit' ) );
		
		//Add custom html after the option page form - see $this->render_after_options_form
		add_filter( 'prso_core_render_plugin_view_after_form', array( $this, 'render_after_options_form' ) );
		
		//Render the plugin options view
		echo apply_filters( 'prso_core_render_plugin_view', $view_output, $args );
		
	}
	
	/**
	* create_settings
	* 
	* Register a setting and it's sanitization callback
	*
	* This is part of the Settings API, which lets you automatically generate 
	* wp-admin settings pages by registering your settings and using a 
	* few callbacks to control the output. 
	* 
	* @access 	public
	* @author	Ben Moody
	*/
	public function create_settings() {
		
		//Init vars
		$field_data 	= array();
		$section_slug	= NULL;
		
		//Register santization callback
		register_setting(
			$this->view_slug,
			$this->view_slug,
			array( $this, 'validate' )
		);
		
		//Setup sections
		do_action( 'prso_core_option_page_sections', $this->page_sections, $this->view_slug );
		
		do_action( 'prso_core_option_page_fields', $this->page_fields, $this->view_slug, $this->data );
	}
	
	/**
	* Validate
	* 
	* Makes use of the pressoholics framework validation helper to validate/sanitize data
	* Also uses the flash helper to return a message to the user.
	*
	* HOW TO USE:
	*	You should only have to add the fields you wish to validate into the $validate array
	*
	*	Like this: 
	*	$validate[ $fb_url_slug ] = array( 'nice_name' => 'Facebook Page Url', 'type' => 'url', 'message' => 'Invalid URL.', 'empty' => true ,'regex' => null );
	*
	*	'type' tells the validation model how to validate the field e.g phone_us, email, url, password
	*	'empty'	NOT REQUIRED will tell the validator that you are happy to let this field be null
	*	'regex'	This will override the 'type' arg and the validator will use the regex provided to validate the field
	* 
	* @access 	public
	* @author	Ben Moody
	*/
	public function validate( $data = array() ) {
		
		//Init vars
		$args		= array();
		$validate 	= $this->validate_fields; //An array of fields to validate
		
		//Set options args to override validation messages
		if( isset($this->validation_success_message, $this->validation_fail_message) ) {
			$args = array(
				'success_message' 	=> $this->validation_success_message,
				'fail_message'		=> $this->validation_fail_message
			);
		}
		
		return apply_filters( 'prso_core_validate_plugin_fields', $validate, $data, $args );
		
	}
	
}