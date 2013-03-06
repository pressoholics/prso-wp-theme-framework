<?php
/**
 * Html Helper class file.
 *
 * Simplifies the construction of HTML elements.
 *
 * CONTENTS:
 *
 * 1. center_thumbnail		- filter 'prso_center_thumbnail'
 * 2. get_the_excerpt		- action 'prso_get_the_excerpt'
 * 
 */
 
class HtmlHelper {
 	
 	function __construct() {
 		
 		//Add custom action hooks for post helpers
 		$this->custom_action_hooks();
 		
 	}
 	
 	/**
	* custom_action_hooks
	* 
	* Create any custom WP Action Hooks here for post helpers
	* 
	* @access 	private
	* @author	Ben Moody
	*/
 	private function custom_action_hooks() {
 		
 		/**
 		* 1. center_thumbnail
 		* 	 Returns contents for style attr for img
 		*/
 		$this->add_filter( 'prso_center_thumbnail', 'center_thumbnail', 10, 3 );
 		
 		/**
 		* 2. get_the_excerpt
 		* 	 Detects if post content is using 'more' tag and echos the_content OR
 		*	 if 'more' tag is not used then defaults to calling the_excerpt()
 		*/
 		$this->add_action( 'prso_get_the_excerpt', 'get_the_excerpt', 10 );
 		
 	}
 	
	/**
	* center_thumbnail()
	*
	* Calculates dom width, height and padding required to center a thumbnail based on
	* the confines of the dom element provided in $dom_size array.
	*
	* E.g We want to dynamically output multiple post thumbnails in a grid pattern but
	* each thumbnail is a different size. We are going to use the WP the_post_thumbnail()
	* to output the thumnail within the dom dimensions but they will not all be centered.
	* To center each image we will need to dynamically change the dom styles based on the
	* resulting thumnail returned by the_post_thumbnail().
	*
	* Usage: Call function and pass it the post obj and declare the dimensions of your img dom
	* cache the resulting style string and echo it into style="" of your dom.
	*
	* @param	array	$args - any get_posts args you wish to customize
	* @return	array	$_posts	- posts array returned by wp get_posts
	*/
	public function center_thumbnail( $Post = null, $dom_size = array(), $unit = 'px' ) {
	
		//Init vars
		$_dom_style		= null;
		$_img_data 		= array();
		$_img_height 	= null;
		$_img_width		= null;
		
		if( isset($Post) && !empty($dom_size) ) {
			if( isset($dom_size['width']) && isset($dom_size['height']) ) {
				
				//Setup post data from post obj
				setup_postdata($Post);
				
				//Get thumbnail image data based on dom height and width provided
				$_img_data 	= wp_get_attachment_image_src( 
					get_post_thumbnail_id(), 
					array($dom_size['width'], $dom_size['height']) 
				);
				
				//Cache thumbnail height and width
				$_img_height = $_img_data[2];
				$_img_width  = $_img_data[1];
				
				//Calculate amount of top and left padding req to center thumbnail in dom object
				$_dom_padding_top	= 0;
				$_dom_padding_left	= 0;
				//Calc top padding
				if( $_img_height !== $dom_size['height'] ){
					$_dom_padding_top = ($dom_size['height'] - $_img_height)/2;
				
					//Recalculate dom height based on padding
					$dom_size['height'] = $dom_size['height'] - $_dom_padding_top;
				}
				
				//Calc left padding
				if( $_img_width !== $dom_size['width'] ){
					$_dom_padding_left = ($dom_size['width'] - $_img_width)/2;
				
					//Recalculate div min width based on padding
					$dom_size['width'] = $dom_size['width'] - $_dom_padding_left;
				}
				
				//Create dom style data string
				ob_start();
				
				echo 'min-height:' . $dom_size['height'] . $unit .';';
				echo 'max-height:' . $dom_size['height'] . $unit .';';
				echo 'min-width:' . $dom_size['width'] . $unit .';';
				echo 'max-width:' . $dom_size['width'] . $unit .';';
				echo 'padding:' . $_dom_padding_top . $unit .' 0 0 ' . $_dom_padding_left . $unit .';';
				echo 'overflow:hidden;';
				
				$_dom_style = ob_get_contents();
				
				ob_end_clean();
			}
		}
		
		return $_dom_style;
	}
	
	
	public function get_the_excerpt() {
		global $post;
		
		//Detect if current post content contains a more tag if not then use excerpt
		if( isset($post->post_content) && preg_match('/<!--more(.*?)?-->/', $post->post_content) ) {
			the_content('', TRUE);
		} else {
			the_excerpt();
		}
		
	}

	
	
	/**
	* add_action
	* 
	* Helper to deal with Wordpress add_action requests. Checks to make sure that the action is not
	* duplicated if a class is instantiated multiple times.
	* 
	* @access 	protected
	* @author	Ben Moody
	*/
	private function add_action( $tag = NULL, $method = NULL, $priority = 10, $accepted_args = NULL ) {
		
		if( isset($tag,$method) ) {
			//Check that action has not already been added
			if( !has_action($tag) ) {
				add_action( $tag, array($this, $method), $priority, $accepted_args );
			}
		}
		
	}
	
	/**
	* add_filter
	* 
	* Helper to deal with Wordpress add_filter requests. Checks to make sure that the filter is not
	* duplicated if a class is instantiated multiple times.
	* 
	* @access 	protected
	* @author	Ben Moody
	*/
	private function add_filter( $tag = NULL, $method = NULL, $priority = 10, $accepted_args = NULL ) {
		
		if( isset($tag,$method) ) {
			//Check that action has not already been added
			if( !has_filter($tag) ) {
				add_filter( $tag, array($this, $method), $priority, $accepted_args );
			}
		}
		
	}
}