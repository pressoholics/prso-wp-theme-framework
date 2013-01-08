<?php
/**
 * Config
 *
 * Sets all constant definitions for the Pressoholics theme framework
 *
 * PHP versions 4 and 5
 *
 * @copyright     Pressoholics (http://pressoholics.com)
 * @link          http://pressoholics.com
 * @package       pressoholics theme framework
 * @since         Pressoholics v 0.1
 */
class PrsoThemeConfig {
	
	
	//***** CHANGE THEME ADMIN OPTIONS HERE *****//
	
	/**
	* VERY IMPORTANT
	*
	* Define a unique slug to prepend to all wordpress database keys to ensure
	* there are no conflicts
	*
	* Effects Class Names and Keys for saved options
	*
	* Be sure to Prepend all Class names with this slug (convert to CamelCase - E.G. foo_bar_ => FooBar)
	*
	* If you need a string to be unique say with an option key call $this->get_slug('your_string'), it will return
	* your_string with the plugin slug prepended to it.
	*
	*/
	protected $theme_slug = 'prso_theme_';
	
	/**
 	* Admin page setting vars: Admin Parent Page Settings...
 	*
 	*/
 	protected $page_title_parent 	= 'Pressoholics Theme Options'; //Cache parent page title string
 	protected $menu_title_parent 	= 'Presso Theme'; //Cache parent menu title string
 	protected $capability_parent	= 'administrator'; //Cache parent user capability
 	protected $menu_slug_parent		= 'prso_theme_admin'; //Cache parent menu slug - prepend prso unqiue slug key
 	protected $icon_url_parent		= NULL; //Cache parent menu icon url
 	protected $position_parent		= NULL; //Cache parent menu postition
 	
 	//Store theme options under this slug - will be a serialized array under this slug
	protected $theme_options_db_slug 	= 'prso_theme_data'; //The unique slug used to identify this plugin - also used to store and indentify plugin option data
 	
 	
 	
 	
 	
 	//***** CHANGE THEME SETUP OPTIONS *****//
 	
 	
 	/**
	* $this->theme_thumbnail_settings
	* 
	* Register/Change theme thumbnails
	* 
	* $theme_thumbnail_settings[{thumbnail-name}] = array(
	  		'width' 	=> '',
	  		'height'	=> '',
	  		'crop'		=> false
	  )
	*/
 	protected $theme_thumbnail_settings = array(
 		'default' => array(
 			'width' 	=> 125,
 			'height'	=> 125,
 			'crop'		=> true
 		),
	 	'prso-orbit' => array(
	 			'width' 	=> 970,
	 			'height'	=> 364,
	 			'crop'		=> true
	 	),
	 	'prso-orbit-thumbnail' => array(
	 			'width' 	=> 100,
	 			'height'	=> 75,
	 			'crop'		=> true
	 	),
	 	'prso-thumb-600' => array(
	 			'width' 	=> 600,
	 			'height'	=> 150,
	 			'crop'		=> false
	 	),
	 	'prso-thumb-300' => array(
	 			'width' 	=> 300,
	 			'height'	=> 100,
	 			'crop'		=> true
	 	)
 	);
 	
 	/**
	* $this->theme_custom_background
	* 
	* Set options for theme custom-background support
	* 
	* array(
	  	'default-color'          => '',
		'default-image'          => '',
		'wp-head-callback'       => '_custom_background_cb',
		'admin-head-callback'    => '',
		'admin-preview-callback' => ''
	  )
	*/
 	protected $theme_custom_background = array(
		'default-color'          => 'ffffff'
	);
 	
 	/**
	* $this->theme_nav_menus
	* 
	* Register theme nav menus
	* 
	* array(
	  		'nav_slug' => 'Nav Title',
	  )
	*/
 	protected $theme_nav_menus = array( 
		'main_nav' => 'The Main Menu',   // main nav in header
		'footer_links' => 'Footer Links' // secondary nav in footer
	);
	
	/**
	* $this->theme_sidebar_settings
	* 
	* Register theme sidebars
	* 
	* $theme_sidebar_settings[{sidebar_slug}] = array(
	  		'id' => 'sidebar1',
	    	'name' => 'Main Sidebar',
	    	'description' => 'Used on every page BUT the homepage page template.',
	    	'before_widget' => '<div id="%1$s" class="widget %2$s">',
	    	'after_widget' => '</div>',
	    	'before_title' => '<h4 class="widgettitle">',
	    	'after_title' => '</h4>'
	  )
	*/
	protected $theme_sidebar_settings = array(
		'sidebar_main' => array(
	    	'id' => 'sidebar_main',
	    	'name' => 'Main Sidebar',
	    	'description' => 'Used on every page BUT the homepage page template.',
	    	'before_widget' => '<div id="%1$s" class="widget %2$s">',
	    	'after_widget' => '</div>',
	    	'before_title' => '<h4 class="widgettitle">',
	    	'after_title' => '</h4>',
	    ),
	    'sidebar_home' => array(
	    	'id' => 'sidebar_home',
	    	'name' => 'Homepage Sidebar',
	    	'description' => 'Used only on the homepage page template.',
	    	'before_widget' => '<div id="%1$s" class="widget %2$s">',
	    	'after_widget' => '</div>',
	    	'before_title' => '<h4 class="widgettitle">',
	    	'after_title' => '</h4>',
	    ),
	    'sidebar_blog_home' => array(
	    	'id' => 'sidebar_blog_home',
	    	'name' => 'Blog Home Sidebar',
	    	'description' => 'Used only on the blog home page template. NOTE, will overide Blog Sidebar on this specific page.',
	    	'before_widget' => '<div id="%1$s" class="widget %2$s">',
	    	'after_widget' => '</div>',
	    	'before_title' => '<h4 class="widgettitle">',
	    	'after_title' => '</h4>',
	    ),
	    'sidebar_blog' => array(
	    	'id' => 'sidebar_blog',
	    	'name' => 'Blog Sidebar',
	    	'description' => 'Used only on the blog page template.',
	    	'before_widget' => '<div id="%1$s" class="widget %2$s">',
	    	'after_widget' => '</div>',
	    	'before_title' => '<h4 class="widgettitle">',
	    	'after_title' => '</h4>',
	    ),
	    'sidebar_search' => array(
	    	'id' => 'sidebar_search',
	    	'name' => 'Search Sidebar',
	    	'description' => 'Used only on the search results archive template.',
	    	'before_widget' => '<div id="%1$s" class="widget %2$s">',
	    	'after_widget' => '</div>',
	    	'before_title' => '<h4 class="widgettitle">',
	    	'after_title' => '</h4>',
	    )
	);
	
	/**
	* $this->theme_post_formats
	* 
	* Setup theme post-formats support
	* 
	* array(
	*		'aside',   // title less blurb
	*		'gallery', // gallery of images
	*		'link',    // quick link to other site
	*		'image',   // an image
	*		'quote',   // a quick quote
	*		'status',  // a Facebook like status update
	*		'video',   // video 
	*		'audio',   // audio
	*		'chat'     // chat transcript 
	*	);
	*/
	protected $theme_post_formats = array(
			'aside',   // title less blurb
			'gallery', // gallery of images
			'link',    // quick link to other site
			'image',   // an image
			'quote',   // a quick quote
			'status',  // a Facebook like status update
			'video',   // video 
			'audio',   // audio
			'chat'     // chat transcript 
	);
	
	/**
	* $this->theme_google_jquery_url
	* 
	* The url for Google jQuery library, used in front end only
	*/
	protected $theme_google_jquery_url = 'http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js';
	
	
	
	
	
	//***** CHANGE THEME ADMIN VIEW OPTIONS *****//
	
	
	/**
	* $this->admin_user_contact_methods
	* 
	* Add more contact fields to user profiles
	* 
	* array(
	  		'field_slug' => 'Field Name',
	  )
	*/
	protected $admin_user_contact_methods = array(
		'user_fb' 			=> 'Facebook',
		'user_tw'			=> 'Twitter',
		'google_profile'	=> 'Google Profile URL'
	);
	
	/**
	* $this->theme_sidebar_settings
	* 
	* Remove admin dashboard widgets
	* 
	* $admin_disable_dashboard_widgets[] = array(
	  		'id' 		=> '',
			'context'	=> ''
	  )
	*/
	protected $admin_disable_dashboard_widgets = array(
		array(
			'id' 		=> 'dashboard_recent_comments',
			'context'	=> 'core'
		),
		array(
			'id' 		=> 'dashboard_incoming_links',
			'context'	=> 'core'
		),
		array(
			'id' 		=> 'dashboard_recent_drafts',
			'context'	=> 'core'
		),
		array(
			'id' 		=> 'dashboard_primary',
			'context'	=> 'core'
		),
		array(
			'id' 		=> 'dashboard_secondary',
			'context'	=> 'core'
		),
		array(
			'id' 		=> 'dashboard_plugins',
			'context'	=> 'core'
		)
	);
 	
	//***** END -- THEME OPTIONS - DON'T EDIT PASSED HERE!! *****//

	
}