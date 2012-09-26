<?php
/**
 * Theme Core Functions
 *
 * Contains methods required by the Prso Theme Framework.
 * 
 * Output for some of these methods can be edited via vars set in the config class
 *
 * Use the Wordpress API call's within __construct to call your methods:
 *	//Action hook example
 *	add_action( 'init', array( &$this, 'test' ) ); 
 *
 *
 */
class PrsoThemeFunctions extends PrsoThemeAppController {
	
	function __construct() {
		
		//Ensure vars set in config are available
 		parent::__construct();
 		
 		//Prepare theme
 		$this->theme_setup();
 		
 		//Add custom action hooks for theme framework
 		$this->custom_action_hooks();

 	}
 	
 	/**
	* theme_setup
	* 
	* All core methods used to setup the theme framework are called here.
	* Either directly or via WP Actions and Filters 
	* 
	* @access 	private
	* @author	Ben Moody
	*/
 	private function theme_setup() {
 	
 		//Setup translation options
 		
 		//Cleanup Wordpress Head
 		add_action( 'init', array($this, 'wp_head_cleanup') );
 		
 		//Remove WP version from RSS
 		add_filter( 'the_generator', array($this, 'remove_rss_version') );
 		
 		//Load scripts for non-admin (front end) pages
 		add_action( 'wp_enqueue_scripts', array($this, 'enqueue_front_end_scripts') );
 		
 		//Load styles for front end pages
 		add_action( 'wp_enqueue_scripts', array($this, 'enqueue_theme_styles') );
 		
 		//Set Prso Theme support
 		$this->add_theme_support();
 		
 		//Add extra form fields to WP User profile contact methods
		add_filter( 'user_contactmethods', array($this, 'add_user_contact_methods'), 10, 1 );
 		
 		//Register Prso Theme sidebars
 		add_action( 'widgets_init', array($this, 'register_sidebars') );
 		
 		//Remove <p> tag from around imgs (http://css-tricks.com/snippets/wordpress/remove-paragraph-tags-from-around-images/)
 		add_filter( 'the_content', array($this, 'remove_p_tag_from_images') );
 		
 		//Hack to enable rel='' attr for links - thanks to yoast
 		if( !function_exists('yoast_allow_rel') ) {
 			add_action( 'wp_loaded', array($this, 'yoast_allow_rel') );
 		}
 		
 		//Admin specific Actions and Filters
 		$this->admin_area_actions();
 		
 		//Add Zurb Foundation grid classes to comments
 		add_filter( 'comment_class', array($this, 'add_comments_classes') );
 		
 		//Overwrite WP default post password form
 		add_filter( 'the_password_form', array($this, 'custom_post_password_form') );
 		
 		//Update WP tag cloud to improve style
 		$this->update_wp_tag_cloud();
 		
 		//Enable shortcodes in widgets
 		add_filter('widget_text', 'do_shortcode');
 		
 		//Disable page jump in 'read more' links
 		add_filter( 'the_content_more_link', array($this, 'remove_more_jump_link') );
 		
 		// Remove height/width attributes on images so they can be responsive
 		add_filter( 'post_thumbnail_html', array($this, 'remove_thumbnail_dimensions'), 10 );
 		add_filter( 'image_send_to_editor', array($this, 'remove_thumbnail_dimensions'), 10 );
 		
 		//Deletes all CSS classes and id's, except for those listed in the array
 		add_filter( 'nav_menu_css_class', array($this, 'custom_wp_nav_menu') );
 		add_filter( 'nav_menu_item_id', array($this, 'custom_wp_nav_menu') );
 		add_filter( 'page_css_class', array($this, 'custom_wp_nav_menu') );
 		
 		//change the standard class that wordpress puts on the active menu item in the nav bar
 		add_filter( 'wp_nav_menu', array($this, 'current_to_active') );
 		
 		//Deletes empty classes and removes the sub menu class_exists
 		add_filter( 'wp_nav_menu', array($this, 'strip_empty_classes') );
 		
 	}
 	
 	/**
	* custom_action_hooks
	* 
	* Create any custom WP Action Hooks here
	* 
	* @access 	private
	* @author	Ben Moody
	*/
 	private function custom_action_hooks() {
 		
 		//Add custom WP Action to output related posts
 		add_action( 'prso_theme_related_posts', array($this, 'get_related_posts') );
 		
 	}
 	
 	/**
	* wp_head_cleanup
	* 
	* Call actions to remove elements from wp_head()
	* 
	* @access 	public
	* @author	Ben Moody
	*/
 	public function wp_head_cleanup() {
 		
 		//Remove header links
 		remove_action( 'wp_head', 'feed_links_extra', 3 );                    // Category Feeds
		remove_action( 'wp_head', 'feed_links', 2 );                          // Post and Comment Feeds
		remove_action( 'wp_head', 'rsd_link' );                               // EditURI link
		remove_action( 'wp_head', 'wlwmanifest_link' );                       // Windows Live Writer
		remove_action( 'wp_head', 'index_rel_link' );                         // index link
		remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );            // previous link
		remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );             // start link
		remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 ); // Links for Adjacent Posts
		remove_action( 'wp_head', 'wp_generator' );                           // WP version
		
		if ( !is_admin() ) {
			wp_deregister_script('jquery');                                   // De-Register jQuery
			wp_register_script('jquery', '', '', '', true);                   // It's already in the Header
		}
 		
 	}
 	
 	/**
	* remove_rss_version
	* 
	* Returns NULL to WP Filter to remove the rss version as a security measure
	* 
	* @access 	public
	* @author	Ben Moody
	*/
 	public function remove_rss_version() {
 		return '';
 	}
 	
 	/**
	* enqueue_front_end_scripts
	* 
	* If you need to add any scripts for front end user call them here.
	*
	* Remember to register the script with WP if not a core WP script
	* 
	* @access 	public
	* @author	Ben Moody
	*/
 	public function enqueue_front_end_scripts() {
 		
 		//Init vars
 		$google_jquery_url = NULL;
 		
 		//Ensure scripts are loaded for front end only
 		if( !is_admin() ) {
 			
 			//Load Wordpress commments jQuery for single pages only
 			$this->enqueue_comments_script();
 			
 			//Load Google jQuery, if not fall back to WP jQuery
	 		if( isset($this->theme_google_jquery_url) ) {
	 			$google_jquery_url = @fopen( $this->theme_google_jquery_url, 'r' ); //Test google jquery file
	 		
		 		if( $google_jquery_url !== false ) {
		 			$this->load_google_jquery();
		 		} else {
		 			$this->load_wp_jquery();
		 		}
	 		}
	 		
	 		//Load Modernizr script from Zurb Foundation
 			wp_register_script( 'modernizr', get_template_directory_uri() . '/javascripts/foundation/modernizr.foundation.js' ); 
    		wp_enqueue_script( 'modernizr' );
 			
 			//Load Zurb Foundation scripts
 			$this->enqueue_zurb_foundation_scripts();
 			
 		}
 		
 	}
 	
 	/**
	* enqueue_zurb_foundation_scripts
	* 
	* Register and Enqueue all scripts required by the Zurb Foundation framework
	*
	* Called directly in enqueue_front_end_scripts()
	* 
	* @access 	private
	* @author	Ben Moody
	*/
 	private function enqueue_zurb_foundation_scripts() {
 		
 		wp_register_script( 'foundation-reveal', get_template_directory_uri() . '/javascripts/foundation/jquery.reveal.js', 'jQuery', '1.1', true ); 
	    wp_enqueue_script( 'foundation-reveal' );
	    
	    wp_register_script( 'foundation-orbit', get_template_directory_uri() . '/javascripts/foundation/jquery.orbit-1.4.0.js', 'jQuery', '1.4.0', true ); 
	    wp_enqueue_script( 'foundation-orbit' );
	    
	    wp_register_script( 'foundation-custom-forms', get_template_directory_uri() . '/javascripts/foundation/jquery.customforms.js', 'jQuery', '1.0', true ); 
	    wp_enqueue_script( 'foundation-custom-forms' );
	    
	    wp_register_script( 'foundation-placeholder', get_template_directory_uri() . '/javascripts/foundation/jquery.placeholder.min.js', 'jQuery', '2.0.7', true ); 
	    wp_enqueue_script( 'foundation-placeholder' );
	    
	    wp_register_script( 'foundation-tooltips', get_template_directory_uri() . '/javascripts/foundation/jquery.tooltips.js', 'jQuery', '2.0.1', true ); 
	    wp_enqueue_script( 'foundation-tooltips' );
	    
	    wp_register_script( 'foundation-off-canvas', get_template_directory_uri() . '/javascripts/foundation/off-canvas.js', 'jQuery', '1.0', true ); 
	    wp_enqueue_script( 'foundation-off-canvas' );
	    
	    //NOTE if detected Child-Theme app.js will override Parent app.js
	    if( file_exists( get_stylesheet_directory() . '/javascripts/app.js' ) ) {
	    	wp_register_script( 'foundation-app', get_stylesheet_directory_uri() . '/javascripts/app.js', 'jQuery', '1.0', true ); 
	    } else {
	    	wp_register_script( 'foundation-app', get_template_directory_uri() . '/javascripts/app.js', 'jQuery', '1.0', true ); 
	    }
	    wp_enqueue_script( 'foundation-app' );
 		
 	}
 	
 	/**
	* enqueue_comments_script
	* 
	* Enqueues WP comments script only on single pages where comments are open.
	* 
	* Called directly in enqueue_front_end_scripts()
	*
	* @access 	private
	* @author	Ben Moody
	*/
 	private function enqueue_comments_script() {
 	
 		if( is_singular() && comments_open() ) {
 			wp_enqueue_script( 'comment-reply' );
 		}
 		
 	}
 	
 	/**
	* load_google_jquery
	* 
	* Registers Google's jQuery as master jquery script in WP.
	* NOTE: this is only called for front end users, back end still uses WP jquery
	* 
	* @access 	private
	* @author	Ben Moody
	*/
 	private function load_google_jquery() {
 	
 		if( isset($this->theme_google_jquery_url) ) {
 			wp_deregister_script( 'jquery' ); // deregisters the default WordPress jQuery  
        	wp_register_script('jquery', $this->theme_google_jquery_url); // register the external file  
        	wp_enqueue_script('jquery'); // enqueue the external file
 		}
 		
 	}
 	
 	/**
	* load_wp_jquery
	* 
	* Ensures that the WP jQuery library is enqueued, usualy a fallback for load_google_jquery()
	* 
	* @access 	private
	* @author	Ben Moody
	*/
 	private function load_wp_jquery() {
        wp_enqueue_script('jquery'); // enqueue the local file
  	}
 	
 	/**
	* enqueue_theme_styles
	* 
	* Enqueues the style sheet for theme -> '/stylesheets/app.css'
	* 
	* @access 	public
	* @author	Ben Moody
	*/
 	public function enqueue_theme_styles() {
 		
 		//Register Zurb Foundation Full CSS
    	//wp_register_style( 'foundation-app', get_template_directory_uri() . '/stylesheets/foundation.css', array(), '3.0', 'all' );
    	
    	//Register Zurb Foundation Min CSS
    	wp_register_style( 'foundation-app', get_template_directory_uri() . '/stylesheets/foundation.min.css', array(), '3.0', 'all' );
   		
   		//Register Theme Stylsheet - req by wordpress, use app.css for custom styles
 		wp_register_style( 'presso-theme-base', get_stylesheet_directory_uri() . '/style.css', array( 'foundation-app' ), filemtime( get_stylesheet_directory() . '/style.css' ), 'all' );
   		
   		//Register Wordpress Specific Stylsheet
 		wp_register_style( 'presso-theme-wp', get_template_directory_uri() . '/stylesheets/app-wordpress.css', array( 'presso-theme-base' ), filemtime( get_template_directory() . '/stylesheets/app-wordpress.css' ), 'all' );
 		
 		//Register the App's specific stylesheet - NOTE if child theme is used will try to find app.css in child dir
	    if( file_exists( get_stylesheet_directory() . '/stylesheets/app.css' ) ) {
	    	wp_register_style( 'presso-theme-app', get_stylesheet_directory_uri() . '/stylesheets/app.css', array( 'presso-theme-wp' ), filemtime( get_stylesheet_directory() . '/stylesheets/app.css' ), 'all' );
    	} else {
    		wp_register_style( 'presso-theme-app', get_template_directory_uri() . '/stylesheets/app.css', array( 'presso-theme-wp' ), filemtime( get_template_directory() . '/stylesheets/app.css' ), 'all' );
    	}
    	
    	//Enqueue App's specific stylesheet - will enqueue all required styles as well :)
    	wp_enqueue_style( 'presso-theme-app' );
 		
 	}
 	
 	/**
	* add_theme_support
	* 
	* Register any WP theme support elements here.
	*
	* Some of these settings can be altered via the config class vars
	* 
	* @access 	public
	* @author	Ben Moody
	*/
 	public function add_theme_support() {
 		
 		//Init vars
 		$nav_menu_args	= array( 
			'main_nav' => 'The Main Menu',   // main nav in header
			'footer_links' => 'Footer Links' // secondary nav in footer
		);
		$post_format_args = array();
		$custom_background_args = array(
			'default-color' => 'ffffff'
		);
 		
 		//Get nav menu options from config class
 		if( isset($this->theme_nav_menus) ) {
 			$nav_menu_args = wp_parse_args( $this->theme_nav_menus, $nav_menu_args );
 		}
 		
 		//Get post format options from config class
 		if( isset($this->theme_post_formats) ) {
 			$post_format_args = wp_parse_args( $this->theme_post_formats, $post_format_args );
 		}
 		
 		//Get custom background options from config class
 		if( isset($this->theme_custom_background) ) {
 			$custom_background_args = wp_parse_args( $this->theme_custom_background, $custom_background_args );
 		}
 		
 		if ( function_exists( 'add_theme_support' ) ) {
 		
	 		//Post Thumbnails
	 		add_theme_support( 'post-thumbnails' );
			//Set thumbnail sizes
			$this->add_custom_thumbnails();
			
			//WP custom background
			add_theme_support( 'custom-background', $custom_background_args );
			
			//RSS thingy
			add_theme_support( 'automatic-feed-links' );
			
			//to add header image support go here: http://themble.com/support/adding-header-background-image-support/
			
			//Post format support
			add_theme_support( 'post-formats', $post_format_args );	
			
			//Add custom Nav Menu support
			add_theme_support( 'menus' );            // wp menus
			//Register navs set in config class - edit $theme_nav_menus array in config class
			register_nav_menus( $nav_menu_args );	 // wp3+ menus
			
		}
 		
 	}
 	
 	/**
	* register_sidebars
	* 
	* Registers the theme sidebars.
	*
	* NOTE: to add or remove sidebars from the theme edit the $theme_sidebar_settings in config class
	* 
	* @access 	public
	* @author	Ben Moody
	*/
	private function add_custom_thumbnails() {
		
		//Init vars
		$defaults = array(
			'width' 	=> NULL,
			'height'	=> NULL,
			'crop'		=> false
		);
		
		//Check settings from config class
		if( isset($this->theme_thumbnail_settings) && is_array($this->theme_thumbnail_settings) ) {
			
			//Loop thumbnail sizes
			foreach( $this->theme_thumbnail_settings as $name => $args ) {
				
				$args = wp_parse_args( $args, $defaults );
				
				extract($args);
				
				if( isset( $width, $height, $crop ) ) {
					
					//Check for requests to update WP default thumbnails
					switch( $name ) {
						case 'default';
							//Add default thumb size
							set_post_thumbnail_size( $width, $height, $crop );   // default thumb size
						case 'medium':
							update_option('medium_size_w', $width);
							update_option('medium_size_h', $height);
							update_option('medium_crop', $crop);
							break;
						case 'large':
							update_option('large_size_w', $width);
							update_option('large_size_h', $height);
							update_option('large_crop', $crop);
							break;
						default:
							//Add custom thumbnail image size to wordpress
							add_image_size( $name, $width, $height, $crop );
							break;
					}
					
				}
				
			}
			
		}
		
	}
	
	/**
	* register_sidebars
	* 
	* Registers the theme sidebars.
	*
	* NOTE: to add or remove sidebars from the theme edit the $theme_sidebar_settings in config class
	* 
	* @access 	public
	* @author	Ben Moody
	*/
	public function register_sidebars() {
		
		//Init vars
		$sidebar_defaults = array(
			'name'          => 'Sidebar',
			'id'            => 'sidebar',
			'description'   => '',
		    'class'         => '',
			'before_widget' => '<li id="%1$s" class="widget %2$s">',
			'after_widget'  => '</li>',
			'before_title'  => '<h2 class="widgettitle">',
			'after_title'   => '</h2>' 
		);
		
		if( isset($this->theme_sidebar_settings) && is_array($this->theme_sidebar_settings) ) {
			
			//Loop each sidebar setting from config class and call WP register_sidebar() function
			foreach( $this->theme_sidebar_settings as $sidebar_ID => $sidebar_args ) {
				
				$sidebar_args = wp_parse_args( $sidebar_args, $sidebar_defaults );
				
				register_sidebar(
					array(
						'name'          => $sidebar_args['name'],
						'id'            => $sidebar_args['id'],
						'description'   => $sidebar_args['description'],
					    'class'         => $sidebar_args['class'],
						'before_widget' => $sidebar_args['before_widget'],
						'after_widget'  => $sidebar_args['after_widget'],
						'before_title'  => $sidebar_args['before_widget'],
						'after_title'   => $sidebar_args['after_title']
					)
				);
				
			}
			
		}
		
	}
	
	/**
	* remove_p_tag_from_images
	* 
	* Filters out <p> tags wrapped around <img> tags by WP
	* 
	* @access 	public
	* @author	Ben Moody
	*/
	public function remove_p_tag_from_images( $content ) {
		
		return preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
		
	}
	
	/**
	* get_related_posts
	* 
	* Custom method which returns an ul list of related posts.
	*
	* Use in theme via custom Action Hook 'prso_theme_related_posts'
	*
	* E.G. do_action('prso_theme_related_posts');
	* 
	* @param	type	name
	* @var		type	name
	* @return	type	name
	* @access 	public
	* @author	Ben Moody
	*/
	public function get_related_posts() {
		
		//Init vars
		global $post;

		echo '<ul id="bones-related-posts">';
		
		$tags = wp_get_post_tags($post->ID);
		
		if($tags) {
			foreach($tags as $tag) { $tag_arr .= $tag->slug . ','; }
	        $args = array(
	        	'tag' => $tag_arr,
	        	'numberposts' => 5, /* you can change this to show more */
	        	'post__not_in' => array($post->ID)
	     	);
	        $related_posts = get_posts($args);
	        if($related_posts) {
	        	foreach ($related_posts as $post) : setup_postdata($post); ?>
		           	<li class="related_post"><a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></li>
		        <?php endforeach; } 
		    else { ?>
	            <li class="no_related_post">No Related Posts Yet!</li>
			<?php }
		}
		
		wp_reset_query();
		
		echo '</ul>';
		
	}
	
	/**
	* yoast_allow_rel
	* 
	* Adds rel='' to links
	* 
	* @access 	public
	* @author	Ben Moody
	*/
	public function yoast_allow_rel() {
		global $allowedtags;
		$allowedtags['a']['rel'] = array ();
	}
	
	/**
	* add_user_contact_methods
	* 
	* Used to add extra user contact fields to user profile view.
	*
	* NOTE: to edit the list of fields see $admin_user_contact_methods array in config class
	* 
	* @access 	public
	* @author	Ben Moody
	*/
	public function add_user_contact_methods( $contact_methods ) {
		
		//Check if options have been set in config class
		if( isset($this->admin_user_contact_methods) && is_array($this->admin_user_contact_methods) ) {
			
			//Merge arrays
			$content_methods = wp_parse_args( $contact_methods, $this->admin_user_contact_methods );
			
		}
		
		return $content_methods;
	}
	
	/**
	* admin_area_actions
	* 
	* Call methods to effect WP admin area here, eg. add/remove dashboard widgets
	* 
	* @access 	private
	* @author	Ben Moody
	*/
	private function admin_area_actions() {
		
		if( is_admin() ) {
		
			//Disable dashboard widgets
			add_action( 'admin_menu', array($this, 'disable_dashboard_widgets') );
		
		}
		
	}
	
	/**
	* disable_dashboard_widgets
	* 
	* Disables admin dashboard widgets.
	*
	* NOTE: to edit the list of dashboard see $admin_disable_dashboard_widgets array in config class
	* 
	* @access 	public
	* @author	Ben Moody
	*/
	public function disable_dashboard_widgets() {
		
		//Init vars
		$defaults = array(
			'id' 		=> NULL,
			'page'		=> 'dashboard',
			'context'	=> NULL
		);
		
		//Check if disable_dashboard_widgets array isset in config class
		if( isset($this->admin_disable_dashboard_widgets) && is_array($this->admin_disable_dashboard_widgets) ) {
			
			//Loop each request and call WP remove_meta_box for each
			foreach( $this->admin_disable_dashboard_widgets as $method_args ) {
				
				$method_args = wp_parse_args( $method_args, $defaults );
				
				if( isset( $method_args['id'], $method_args['page'], $method_args['context'] ) ) {
					remove_meta_box( $method_args['id'], $method_args['page'], $method_args['context'] );
				}
				
			}
			
		}
		
	}
	
	/**
	* add_comments_classes
	* 
	* Adds new classes to comments html wrapper
	* 
	* @access 	public
	* @author	Ben Moody
	*/
	public function add_comments_classes( $classes ) {
		array_push($classes, "twelve", "columns");
    	return $classes;
	}
	
	/**
	* custom_post_password_form
	* 
	* Overwrite the WP standard password form used on password protected posts/pages
	* 
	* @access 	public
	* @author	Ben Moody
	*/
	public function custom_post_password_form() {
		
		global $post;
		$label = 'pwbox-'.( empty( $post->ID ) ? rand() : $post->ID );
		$o = '<div class="clearfix"><form action="' . get_option('siteurl') . '/wp-pass.php" method="post">
		' . __( "<p>This post is password protected. To view it please enter your password below:</p>" ) . '
		<div class="row collapse">
	        <div class="twelve columns"><label for="' . $label . '">' . __( "Password:" ) . ' </label></div>
	        <div class="eight columns">
	            <input name="post_password" id="' . $label . '" type="password" size="20" class="input-text" />
	        </div>
	        <div class="four columns">
	            <input type="submit" name="Submit" class="postfix button nice blue radius" value="' . esc_attr__( "Submit" ) . '" />
	        </div>
		</div>
	    </form></div>
		';
		return $o;
		
	}
	
	/**
	* update_wp_tag_cloud
	* 
	* Add actions/Filter calls to edit the WP tag cloud here
	* 
	* @access 	private
	* @author	Ben Moody
	*/
	private function update_wp_tag_cloud() {
		
		//filter tag clould output so that it can be styled by CSS
		add_action( 'wp_tag_cloud', array($this, 'add_tag_class') );
		
		//Tweak tag cloud args
		add_filter( 'widget_tag_cloud_args', array($this, 'my_widget_tag_cloud_args') );
		
		//Wrap tag cloud output
		add_filter( 'wp_tag_cloud', array($this, 'wp_tag_cloud_filter') );
		
		//Alter the link (<a>) tag html
		add_filter( 'the_tags', array($this, 'add_class_the_tags') );
		
	}
	
	/**
	* add_tag_class
	* 
	* @access 	public
	* @author	Ben Moody
	*/
	public function add_tag_class( $taglinks ) {
	    $tags = explode('</a>', $taglinks);
	    $regex = "#(.*tag-link[-])(.*)(' title.*)#e";
	        foreach( $tags as $tag ) {
	            $tagn[] = preg_replace($regex, "('$1$2 label radius tag-'.get_tag($2)->slug.'$3')", $tag );
	        }
	    $taglinks = implode('</a>', $tagn);
	    return $taglinks;
	}
	
	/**
	* my_widget_tag_cloud_args
	* 
	* Override the WP tag cloud args
	* 
	* @access 	public
	* @author	Ben Moody
	*/
	public function my_widget_tag_cloud_args( $args ) {
		$args['number'] = 20; // show less tags
		$args['largest'] = 9.75; // make largest and smallest the same - i don't like the varying font-size look
		$args['smallest'] = 9.75;
		$args['unit'] = 'px';
		return $args;
	} 
	
	/**
	* wp_tag_cloud_filter
	* 
	* Wrap the WP tag cloud in custom html.
	* 
	* @access 	public
	* @author	Ben Moody
	*/
	public function wp_tag_cloud_filter($return, $args) {
	  return '<div id="tag-cloud"><p>'.$return.'</p></div>';
	}
	
	/**
	* add_class_the_tags
	* 
	* Add custom classes to tag <a> links
	* 
	* @access 	public
	* @author	Ben Moody
	*/
	public function add_class_the_tags($html){
	    $postid = get_the_ID();
	    $html = str_replace('<a','<a class="label success radius"',$html);
	    return $html;
	}
	
	/**
	* remove_more_jump_link
	* 
	* Remove the html page jump (#DOM_ID) from more links
	* 
	* @access 	public
	* @author	Ben Moody
	*/
	public function remove_more_jump_link($link) {
		$offset = strpos($link, '#more-');
		if ($offset) {
			$end = strpos($link, '"',$offset);
		}
		if ($end) {
			$link = substr_replace($link, '', $offset, $end-$offset);
		}
		return $link;
	}
	
	/**
	* remove_thumbnail_dimensions
	* 
	* Remove height/width dimensions from thumbnail images to ensure they are dynamic and fluid
	* 
	* @access 	public
	* @author	Ben Moody
	*/
	public function remove_thumbnail_dimensions( $html ) {
	    $html = preg_replace( '/(width|height)=\"\d*\"\s/', "", $html );
	    return $html;
	}
	
	/**
	* custom_wp_nav_menu
	* 
	* Override the list of allowed classes to output for WP Nav Menus
	* 
	* @access 	public
	* @author	Ben Moody
	*/
	public function custom_wp_nav_menu($var) {
        return is_array($var) ? array_intersect($var, array(
                //List of allowed menu classes
                'current_page_item',
                'current_page_parent',
                'current_page_ancestor',
                'first',
                'last',
                'vertical',
                'horizontal'
                )
        ) : '';
	}
	
	/**
	* current_to_active
	* 
	* Change the class used to indicate an active page in the WP Nav Menu
	* 
	* @access 	public
	* @author	Ben Moody
	*/
	public function current_to_active($text){
        $replace = array(
                //List of menu item classes that should be changed to "active"
                'current_page_item' => 'active',
                'current_page_parent' => 'active',
                'current_page_ancestor' => 'active',
        );
        $text = str_replace(array_keys($replace), $replace, $text);
        
        return $text;
    }
    
    /**
	* strip_empty_classes
	* 
	* @access 	public
	* @author	Ben Moody
	*/
    public function strip_empty_classes($menu) {
	    $menu = preg_replace('/ class=""| class="sub-menu"/','',$menu);
	    return $menu;
	}

}