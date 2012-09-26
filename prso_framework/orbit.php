<?php
/**
* Orbit.php
* 
* Handles all function required to setup the Zurb Foundation Orbit banner.
*
* What this does:
*	1.Setup custom post type 'prso_orbit_banner'
*	2.Setup custom taxonomy for 'prso_orbit_banner' called 'banner-gallery'
*	3.Adds a custom field to WP post edit page allowing page/post to be linked to a banner-gallery
*	4.Adds a custom WP Action called 'prso_orbit_banner' to output Orbit in a page template
* 
* @author	Ben Moody
*/


/**
* prso_orbit_post_type
* 
* Setup custom post type 'prso_orbit_banner'
* 
* @access 	public
* @author	Ben Moody
*/
function prso_orbit_post_type() { 
	// creating (registering) the custom type 
	register_post_type( 'prso_orbit_banner', /* (http://codex.wordpress.org/Function_Reference/register_post_type) */
	 	// let's now add all the options for this post type
		array('labels' => array(
			'name' => __('Banners', 'post type general name'), /* This is the Title of the Group */
			'singular_name' => __('Banner', 'post type singular name'), /* This is the individual type */
			'add_new' => __('Add New', 'custom post type item'), /* The add new menu item */
			'add_new_item' => __('Add New Banner'), /* Add New Display Title */
			'edit' => __( 'Edit' ), /* Edit Dialog */
			'edit_item' => __('Edit Banners'), /* Edit Display Title */
			'new_item' => __('New Post Type'), /* New Display Title */
			'view_item' => __('View Post Type'), /* View Display Title */
			'search_items' => __('Search Post Type'), /* Search Custom Type Title */ 
			'not_found' =>  __('No banners found, add a new one.'), /* This displays if there are no entries yet */ 
			'not_found_in_trash' => __('Nothing found in Trash'), /* This displays if there is nothing in the trash */
			'parent_item_colon' => ''
			), /* end of arrays */
			'description' => __( 'This is the example custom post type' ), /* Custom Type Description */
			'public' => true,
			'publicly_queryable' => false,
			'exclude_from_search' => true,
			'show_ui' => true,
			'query_var' => true,
			'menu_position' => NULL, /* this is what order you want it to appear in on the left hand side menu */ 
			'menu_icon' => get_stylesheet_directory_uri() . '/prso_framework/images/custom-post-icon.png', /* the icon for the custom post type menu */
			'rewrite' => true,
			'capability_type' => 'post',
			'hierarchical' => false,
			/* the next one is important, it tells what's enabled in the post editor */
			'supports' => array( 'title', 'editor', 'thumbnail', 'prso-orbit-banner', 'excerpt' )
	 	) /* end of options */
	); /* end of register post type */
	
} 
// adding the function to the Wordpress init
add_action( 'init', 'prso_orbit_post_type');
	
	
/**
* prso_orbit_banner_gallery
* 
* Setup custom taxonomy for orbit called 'prso_orbit_banner'
* 
* @access 	public
* @author	Ben Moody
*/
register_taxonomy( 'prso_orbit_banner_gallery', 
	array('prso_orbit_banner'), /* if you change the name of register_post_type( 'custom_type', then you have to change this */
	array('hierarchical' => true,     /* if this is true it acts like categories */             
		'labels' => array(
			'name' => __( 'Banner Galleries' ), /* name of the custom taxonomy */
			'singular_name' => __( 'Gallery' ), /* single taxonomy name */
			'search_items' =>  __( 'Search Banner Galleries' ), /* search title for taxomony */
			'all_items' => __( 'All Banner Galleries' ), /* all title for taxonomies */
			'parent_item' => __( 'Parent Gallery' ), /* parent title for taxonomy */
			'parent_item_colon' => __( 'Parent Gallery:' ), /* parent taxonomy title */
			'edit_item' => __( 'Edit Gallery' ), /* edit custom taxonomy title */
			'update_item' => __( 'Update Gallery' ), /* update title for taxonomy */
			'add_new_item' => __( 'Add New Gallery' ), /* add new title for taxonomy */
			'new_item_name' => __( 'New Banner Gallery' ) /* name title for taxonomy */
		),
		'show_ui' => true,
		'query_var' => true,
	)
);

/**
* prso_orbit_meta_box
* 
* Adds a custom field to WP post edit page allowing page/post to be linked to a banner-gallery
* 
* @access 	public
* @author	Ben Moody
*/
function prso_orbit_meta_box() {
    add_meta_box(
        'prso-orbit-banner',
        __('Banner Gallery', 'prso_textdomain' ), 
        'prso_orbit_banner_option',
        'page',
        'side'
    );
    add_meta_box(
        'prso-orbit-banner',
        __('Banner Gallery', 'prso_textdomain' ), 
        'prso_orbit_banner_option',
        'post',
        'side'
    );
}
add_action( 'add_meta_boxes', 'prso_orbit_meta_box' );

/**
* prso_orbit_banner_option()
*
* Collects all Orbit page banner categories set in the system and outputs
* an html select option for each Orbit banner category found
*
*/
function prso_orbit_banner_option( $post ) {
	
	// Use nonce for verification
	wp_nonce_field( plugin_basename( __FILE__ ), 'prso_noncename' );
	
	// Get a list of all page-banners category children
	$tax_args = array(
		'hide_empty' 	=> true,
		'hierarchical'	=> false,
		'parent'		=> 0
	);
	$banner_cats = get_terms('prso_orbit_banner_gallery', $tax_args);
	
	//Cache name and term_id for each banner cat
	$banners = array();
	foreach( $banner_cats as $key => $cat ) {
		$banners[$cat->name] = $cat->term_id;
	}
	
	//Get page banner meta for this page
	$current_banner = get_post_meta( $post->ID, 'prso_orbit_banner_gallery', true );
	
	// The actual fields for data entry
	?>
		<label for="orbit_page_banner_cat">
		<?php _e("Select Banner Gallery", 'prso_textdomain' ); ?>
		</label>
		<?php if( !empty($banners) ): ?>
			<select id="orbit_page_banner_cat" name="prso_orbit_banner_gallery">
				<option value="">None</option>
				
				<?php foreach( $banners as $name => $ID ): ?>
					<?php if( !empty($current_banner) && is_numeric($current_banner) && $ID === $current_banner ): ?>
					<option value="<?php echo $ID; ?>" selected="selected"><?php echo $name; ?></option>
					<?php else: ?>
					<option value="<?php echo $ID; ?>"><?php echo $name; ?></option>
					<?php endif; ?>
				<?php endforeach; ?>
				
			</select>
		<?php else: ?>
			<p>You don't have any Banners setup yet.</p>
		<?php endif; ?>
	<?php
}

/**
* prso_orbit_save_options()
*
* Called by save_post action, saves custom post data relating to the selection
* of Orbit page banner categories for specific page.
*
*/
function prso_orbit_save_options( $post_id ) {
	
	if( isset($_POST['prso_noncename']) ) {
		// verify if this is an auto save routine. 
		// If it is our form has not been submitted, so we dont want to do anything
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
		  return;
		
		// verify this came from the our screen and with proper authorization,
		// because save_post can be triggered at other times
		
		if ( !wp_verify_nonce( $_POST['prso_noncename'], plugin_basename( __FILE__ ) ) )
		  return;
		
		
		// Check permissions
		if ( 'page' == $_POST['post_type'] ) {
			if ( !current_user_can( 'edit_page', $post_id ) )
			    return;
		} else {
			if ( !current_user_can( 'edit_post', $post_id ) )
			    return;
		}
		
		// OK, we're authenticated: we need to find and save the data
		$data = array();
		//Cache page banner cat data
		if( isset($_POST['prso_orbit_banner_gallery']) ) {
			$data['prso_orbit_banner_gallery'] = $_POST['prso_orbit_banner_gallery'];
		}
			
		// Loop data array and save post meta
		if( !empty($data) && isset($post_id) ){
			
			foreach( $data as $meta_key => $meta_value ) {
				//Type cast value as int
				$meta_value = (int) $meta_value;
				
				if( update_post_meta( $post_id, $meta_key, $meta_value ) ) {
					add_post_meta( $post_id, $meta_key, $meta_value );
				}
			
			}
			
		}
	}
	
}
add_action( 'save_post', 'prso_orbit_save_options' );

/**
* prso_orbit_banner_shortcode
*
* Allows users to manually add a banner gallery into a post using a shortcode
*
*/
add_shortcode( 'prso_banner_gallery', 'prso_orbit_banner_shortcode' );
function prso_orbit_banner_shortcode( $atts, $content = NULL ) {
	
	//Init vars
	$banner_args = array();
	$output = NULL;
	$defaults = array(
		'name' => NULL
	);
	
	$atts = shortcode_atts( $defaults, $atts );
	
	extract( $atts );
	
	//Sanitize vars
	$name = esc_attr( $name );
	
	if( isset($name) ) {
		
		//Call custom action to return banner html
		$banner_args = array(
			'gallery_name' 	=> $name,
			'container_id'	=> 'featured-shortcode',
			'echo'			=> false
		);
		$output = prso_orbit_banner_output( $banner_args );
		
	}
	
	return $output;
}

//Add custom tinymce button for orbit shortcode
function prso_orbit_add_tinymce_button() {
	$args = array(
		'plugin_slug' 		=> 'prso_banner_gallery',
		'title'				=> 'Banner Gallery',
		'image'				=> '/prso-orbit-button.png',
		'content'			=> array(),
		'shortcode_args'	=> array(
			'name'	=> array(
				'slug' 		=> 'name',
				'prompt'	=> 'Gallery Name',
				'default'	=> ' '
			)
		),
		'plugin_info'		=> array(
								'longname' 	=> 'Pressoholics Orbit Banner Gallery Plugin'
							),
		'file_path'			=> get_template_directory() . '/javascripts/tinymce-plugins/prso-orbit.js',
		'file_url'			=> get_template_directory_uri() . '/javascripts/tinymce-plugins/prso-orbit.js'
	);
	
	//Call custom action to create plugin javascript file
	do_action( 'prso_core_create_tiny_mce_plugin', $args );
	
}
add_action( 'admin_init', 'prso_orbit_add_tinymce_button' );

/**
* prso_orbit_banner_output()
*
* Called using custom WP Action 'prso_orbit_banner'
*
*/
add_action( 'prso_orbit_banner', 'prso_orbit_banner_output', 10, 1 );
function prso_orbit_banner_output( $args = array() ) {
	
	//Init vars
	global $post;
	$banner_gallery_ID 		= NULL;
	$banner_gallery_data	= NULL;
	$banners 				= array();
	$output 				= NULL;
	$defaults = array(
		'show_all' 			=> false, //Output all banners regardless of Gallery taxonomy
		'gallery_name'		=> NULL, //Output all banners for a specific gallery
		'image_fallback'	=> true, //Fallback to post featured image if one is set
		'echo'				=> true //Whether to return or echo the html output
	);
	
	$args = wp_parse_args( $args, $defaults );
	
	extract( $args );
	
	//First let's see if this page has a banner gallery assigned to it
	$banner_gallery_ID = get_post_meta( esc_attr( $post->ID ), 'prso_orbit_banner_gallery', true );
	if( !empty($banner_gallery_ID) ) {
		$banner_gallery_data = get_term_by( 'id', esc_attr( $banner_gallery_ID ), 'prso_orbit_banner_gallery', 'ARRAY_A' );
	}
	

	if( !empty($banner_gallery_data) && isset($banner_gallery_data['slug']) ) {
		
		//Lets get any banners in this gallery
		$post_args = array(
			'post_type' 				=> 'prso_orbit_banner',
			'prso_orbit_banner_gallery'	=> esc_attr( $banner_gallery_data['slug'] )
		);
		$banners = get_posts( $post_args );
				
	} else {
		
		//No banner galleries set so let's see if we should show all banners
		if( isset($show_all) && $show_all === true ) {
			
			//Lets get all banners regardless of gallery
			$post_args = array(
				'post_type' 				=> 'prso_orbit_banner'
			);
			$banners = get_posts( $post_args );
			
		}
		
		//OR has a gallery name been provided in args
		if( isset($gallery_name) && is_string($gallery_name) ) {
			
			//Lets get any banners in this gallery
			$post_args = array(
				'post_type' 				=> 'prso_orbit_banner',
				'prso_orbit_banner_gallery'	=> esc_attr( $gallery_name )
			);
			$banners = get_posts( $post_args );
			
		}
		
	}
	
	//OK, do we have any banners to output
	if( !empty($banners) ) {
		//Call function to return banner html and scripts
		$output = prso_orbit_banner_content( $banners, $args );
	}
	
	//Fallback, does the post have a featured image set
	if( $image_fallback && empty($banners) && has_post_thumbnail( $post->ID ) ) {
		ob_start();
		?>
		<div class="post-featured-image">
			<?php echo get_the_post_thumbnail( $post->ID, 'prso-orbit' ); ?>
		</div>
		<?php
		$output = ob_get_contents();
		ob_end_clean();
	}
	
	//Detect the requested output type - return or echo
	if( isset($echo) && $echo === false ) {
		return $output;
	} else {
		echo $output;
	}
	
}

function prso_orbit_banner_content( $banners = array(), $args = array() ) {
	
	//Init vars
	$banner_content = NULL;
	$start_html 	= NULL;
	$end_html		= '</div>';
	$html_array		= array();
	$output 		= NULL;
	
	$defaults = array(
		'container_id' 		=> 'featured',
		'container_class'	=> NULL
	);
	
	$args = wp_parse_args( $args, $defaults );
	
	//Cache the html required to open the gallery div
	$container_id 		= $args['container_id'];
	$container_class	= $args['container_class'];
	$start_html = "<div id='{$container_id}' class='featured-banner {$container_class}'>";
	
	if( !empty($banners) ) {
		
		//Start banner html
		$output = $start_html;
		
		//Loop each banner
		foreach( $banners as $banner ) {
			
			if( isset($banner->ID, $banner->post_content) ) {
				
				//Init vars
				$has_thumbnail	= false;
				$has_content	= false;
				$has_caption	= false;
				
				//Check for a post thumbnail
				$has_thumbnail = has_post_thumbnail( $banner->ID );
				
				//Check if banner has custom content
				if( !empty($banner->post_content) ) {
					$has_content = true;
				} 
				
				//Check for post excerpt
				if( !empty($banner->post_excerpt) ) {
					$has_caption = true;
				}
				
				//Now we have assesed the banner content we need to build the html - returns array of ['banner'] and ['caption']
				$html_array[] = prso_orbit_banner_html( $banner, $has_thumbnail, $has_content, $has_caption );
				
			}
			
		}
		
		//Loop html_array and banners html to output
		if( !empty($html_array) ) {
			foreach( $html_array as $html_data ) {
				
				if( isset($html_data['banner']) ) {
					$output.= $html_data['banner'];
				}
				
			}
		}
		
		//Close banner html
		$output.= $end_html;
		
		//Loop html_array and captions html to output
		$output.= '<!-- Captions for Orbit !-->';
		if( !empty($html_array) ) {
			foreach( $html_array as $html_data ) {
				
				if( isset($html_data['caption']) ) {
					$output.= $html_data['caption'];
				}
				
			}
		}
		
	}
	
	return $output;
}

function prso_orbit_banner_html( $banner = array(), $has_thumbnail = false, $has_content = false, $has_caption = false) {
	
	//Init vars
	$data_caption 	= NULL;
	$caption_html	= NULL;
	$image_src		= array();
	$output 		= NULL;
	
	if( !empty($banner) ) {
		
		//Do we need to output a caption
		if( $has_caption ) {
			$data_caption = "#caption" . strtolower( $banner->post_name );
			
			//Output banner caption
			ob_start();
			?>
			<span class="orbit-caption" id="<?php echo "caption" . strtolower( esc_attr($banner->post_name) ); ?>"><?php echo esc_attr($banner->post_excerpt); ?></span>
			<?php
			$output['caption'] = ob_get_contents();
			ob_end_clean();
		}
		
		//There are four banner variations - image only, image + caption, image + content
		if( $has_thumbnail && !$has_content ) {
			
			//Get image src
			$image_src = wp_get_attachment_image_src( get_post_thumbnail_id( $banner->ID ), 'prso-orbit' );
			
			//Output banner image
			ob_start();
			?>
			<img src="<?php echo $image_src[0]; ?>" alt="<?php echo esc_attr($banner->post_title); ?>" data-caption="<?php echo $data_caption; ?>" />
			<?php
			$output['banner'] = ob_get_contents();
			ob_end_clean();
			
		} elseif( !$has_thumbnail && $has_content ) {
			
			//Output banner content div
			ob_start();
			?>
			<div class="content">
				<?php echo $banner->post_content; ?>
			</div>
			<?php
			$output['banner'] = ob_get_contents();
			ob_end_clean();
			
		} elseif( $has_thumbnail && $has_content ) {
			
			//Get image src
			$image_src = wp_get_attachment_image_src( get_post_thumbnail_id( $banner->ID ), 'prso-orbit' );
			
			//Output banner content div with background set to thumb src
			ob_start();
			?>
			<div class="content" style="background: url('<?php echo $image_src[0]; ?>') no-repeat center center #ffffff;-webkit-background-size: cover;-moz-background-size: cover;-o-background-size: cover;background-size: cover;">
				<?php echo $banner->post_content; ?>
			</div>
			<?php
			$output['banner'] = ob_get_contents();
			ob_end_clean();
			
		}
		
		
	}
	
	return $output;
}