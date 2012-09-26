<?php
/**
 * Html Helper class file.
 *
 * Simplifies the construction of HTML elements.
 *
 * CONTENTS:
 *
 * center_thumbnail( $Post = null, $dom_size = array(), $unit = 'px' )
 * trim_text($input, $length, $ellipses = true, $strip_html = true)
 * subpages_menu( $post = null )
 * 
 */
 
class HtmlHelper {
 	
 	function __construct() {
 		
 	}
 	
 	function test() {
 		echo 'HTMLTEST';
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
	
	/**
	* trims text to a space then adds ellipses if desired
	* @param string $input text to trim
	* @param int $length in characters to trim to
	* @param bool $ellipses if ellipses (...) are to be added
	* @param bool $strip_html if html tags are to be stripped
	* @return string 
	*/
	public function trim_text($input, $length, $ellipses = true, $strip_html = true) {
	    //strip tags, if desired
	    if ($strip_html) {
	        $input = strip_tags($input);
	    }
	  
	    //no need to trim, already shorter than trim length
	    if (strlen($input) <= $length) {
	        return $input;
	    }
	  
	    //find last space within length
	    $last_space = strrpos(substr($input, 0, $length), ' ');
	    $trimmed_text = substr($input, 0, $last_space);
	  
	    //add ellipses (...)
	    if ($ellipses) {
	        $trimmed_text .= '...';
	    }
	  
	    return $trimmed_text;
	}
 
	/**
	* subpages_menu
	* 
	* Return a list of subpages for the page provided
	* 
	* @param	obj		$post
	* @return	string	output
	* @access 	public
	* @author	Ben Moody
	*/
	public function subpages_menu( $post = null ) {
	
		//Init vars
		$page_list 		= null; //Cache html string if subpages found
		$current_page	= null;
		$output			= null;
		
		if( isset($post) ) {
			
			//Check if this page is a parent or child
			if( !empty($post->post_parent) ) {
				$post_id = $post->post_parent;
			} else {
				$post_id = $post->ID;
				//Cache class for current page as this is parent page
				$current_page = 'current_page_item';
			}
			
			//Get list of subpages
			$page_list = wp_list_pages(
				array(
					'sort_column'	=> 'menu_order',
					'title_li'		=> null,
					'child_of' 		=> $post_id,
					'echo'			=> false
				)
			);
			
			//Check if string is empty
			if( !empty($page_list) ) {
				?>
				<div class="row">
					<div class="twelve columns">
						<ul id="page-submenu" class="submenu">
							<li class="page_item page-item-<?php echo $post_id; ?> <?php echo $current_page; ?>">
								<a href="<?php echo get_permalink( $post_id ); ?>"><?php echo get_the_title( $post_id ); ?></a>
							</li>
							<?php echo $page_list; ?>
							<div class="clear"></div>
						</ul>
					</div>
				</div>
				<?php
			}
		}
	}
	
	/**
	* get_page_title
	* 
	* Returns the current page/post title wrapped in requested header tag and/or link if requested
	* 
	* @access 	public
	* @author	Ben Moody
	*/
	public function get_page_title( $args = array() ) {
		
		//Init vars
		global $post;
		$post_id	= null;
		$permalink	= null;
		$title		= null;
		$output 	= null;
		
		//Set arg defaults
		$defaults 	= array(
			'id' 	=> null,
			'class'	=> null,
			'type'	=> 'h1',
			'link'	=> false	
		);
		
		$args = wp_parse_args( $args, $defaults );
		
		extract( $args );

		//Get current post title data
		if( isset($post->ID) ) {
			$post_id = $post->ID;
			
			$permalink 	= get_permalink( $post_id );
			$title		= get_the_title( $post_id );
			
			//Form title html
			if( $link ) {
				ob_start();
				?>
				<<?php echo $type; ?> id="<?php echo $id; ?>" class="<?php echo $class; ?>">
					<a href="<?php echo $permalink; ?>" rel="bookmark" title="Permanent Link to <?php echo $title; ?>"><?php echo $title; ?></a>
				</<?php echo $type; ?>>
				<?php
				$output = ob_get_contents();
				ob_end_clean();
			} else {
				ob_start();
				?>
				<<?php echo $type; ?> id="<?php echo $id; ?>" class="<?php echo $class; ?>"><?php echo $title; ?></<?php echo $type; ?>>
				<?php
				$output = ob_get_contents();
				ob_end_clean();
			}
			
		}
		
		return $output;
	}
	
	/**
	* orbit_banner
	* 
	* Output post featured image or orbit content
	*
	* Detect if post has a page banner category id set, if not then see if there is a featured image
	* 
	* @param	int		post_id
	* @access 	public
	* @author	Ben Moody
	*/
	function orbit_banner( $post_id = null, $cat_slug_exclude = array() ) {
		
		global $post;
		
		//Init vars
		$banner_tax_slug 	= 'orbit_page_banner_cat';
		$banner_thumb_slug	= 'orbit_banner-thumb';
		
		$banner_cat_id 	= null;
		$thumbnail		= null;
		$post_cat_slug	= null;
		$exclude		= false;
		$output 		= null;
		
		//Check if this post should have an orbit banner
		if( !is_home() && !is_category() && !is_tag() && !is_tax() && !is_archive() && !is_search() ) {
			
			//Cache current post cat id
			$post_cat_slug = get_the_category( $post->ID );
			$post_cat_slug = $post_cat_slug[0]->slug;
			
			//Loop the cat slug exclude array and see if the current post should be excluded
			if( !empty( $cat_slug_exclude ) ) {
				foreach( $cat_slug_exclude as $slug ) {
					if( $post_cat_slug === $slug ) {
						$exclude = true;
						break;
					}
				}
			}
			
		} else {
			
			//Current page is a type or template we need to exclude
			$exclude = true;
			
		}
		
		if( !empty($post_id) && !$exclude ) {
			
			//Check if this post has a page banner category
			$banner_cat_id = get_post_meta( $post_id, $banner_tax_slug, true );
			if( !empty($banner_cat_id) && is_numeric($banner_cat_id) ) {
				
				//Post has a orbit page banner so let's get it
				ob_start();
				?>
				<!-- Desktop Orbit !-->
				<div class="row hide-on-phones">
					<div class="tweleve columns" id="orbit">
						<div id="featured-desktop">
							<?php echo SliderContent( array( 'cat' => $banner_cat_id, 'banner_slug' => $banner_thumb_slug ) ); ?>
						</div>
					</div>
				</div>
				<!-- Phone Orbit (reduced height) !-->
				<div class="row show-on-phones">
					<div class="tweleve columns" id="orbit">
						<div id="featured-phone">
							<?php echo SliderContent( array( 'cat' => $banner_cat_id, 'banner_slug' => $banner_thumb_slug ) ); ?>
						</div>
					</div>
				</div>
				<?php
				$output = ob_get_contents();
				ob_end_clean();
				
				echo $output;
				
			} else {
			
				//Output post thumbnail
				$thumbnail = get_the_post_thumbnail( $post_id, 'full' );
				if( !empty($thumbnail) ) {
					
					ob_start();
					?>
					<div class="row">
						<div class="tweleve columns" id="orbit">
						<?php echo $thumbnail; ?>
						</div>
					</div>
					<?php
					$output = ob_get_contents();
					ob_end_clean();
					
					echo $output;
					
				}
			
			}
			
		}
		
	}
		
}