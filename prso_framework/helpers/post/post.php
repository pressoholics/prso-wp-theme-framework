<?php
/**
 * Wordpress Post/Page data Helper class file.
 *
 * Simplifies the process of quering wordpress post and page data.
 *
 * CONTENTS:
 *
 *	get_ID_by_slug($page_slug)
 *	get_posts( $args = array() )
 *	loop_category( $parent_cat_slug = null, $args = array() )
 *	loop_category_widget( $cat_args )
 *	get_cat_children( $args = array() )
 * 
 */
class PostHelper {
	
	//Cache parent cat id - Set by method loop_category, Used by method loop_category_widget
	private $loop_cat_parent_id = null;
	
	function __construct() {
	
	}

	/**
	* get_ID_by_slug
	* Shortcut for returning a page ID using it's slug
	*
	*/ 
	public function get_ID_by_slug($page_slug) {
	    $page = get_page_by_path($page_slug);
	    if ($page) {
	        return $page->ID;
	    } else {
	        return null;
	    }
	}
	
	/**
	* get_posts()
	*
	* This automates get_posts by setting some default args as well as
	* allowing you to pass a string for category rather than just cat_id
	* lets you use cat slug too.
	*
	* @param	array	$args - any get_posts args you wish to customize
	* @return	array	$_posts	- posts array returned by wp get_posts
	*/
	public function get_posts( $args = array() ) {
		
		//Init vars
		global $post;
		$_posts		= array();
		$_CatObject = null;
		$_args 		= array(
			'numberposts'     => 5,
		    'offset'          => 0,
		    'category'        => null,
		    'orderby'         => 'post_date',
		    'order'           => 'ASC',
		    'include'         => null,
		    'exclude'         => null,
		    'meta_key'        => null,
		    'meta_value'      => null,
		    'post_type'       => 'post',
		    'post_mime_type'  => null,
		    'post_parent'     => null,
		    'post_status'     => 'publish'
		);
		
		//Combine default arg with args provided
		$args = array_merge( $_args, $args );
		
		//Set category ID in args if cat provided is category slug string
		if( isset($args['category']) && is_string($args['category']) ) {
			
			//Get category id using cat slug provided
			if( $_CatObject = get_category_by_slug($args['category']) ){
				$args['category'] 	= $_CatObject->term_id;
			}else{
				$args['category'] 	= null;
			}
			
		}
		
		//Get posts using args
		$_posts = get_posts( $args );
	
		return $_posts;
	}
	
	/**
	* loop_category
	* 
	* Uses custom wordpress query to prepare a loop for all posts/pages in the requested category. Will fetch all cat parent
	* and cat child posts.
	*
	* HOW TO USE:
	* Note that you can pass an array of std query_posts() args to for example select only pages or posts using post_type = 'page'
	*
	* Instantiate post helper then call function with parent cat slug and args if required, then query posts using query_posts()
	* 
	*	global $PrsoPost;
	*		
	*	//Call helper to set correct category args and call wp_query to fetch posts in requested category
	*	$PrsoPost->loop_category( 'news' );
	
	*	while ( have_posts() ) : the_post();
	*	
	*	IMPORTANT!!!!!!!!
	*   You MUST make sure to end your custom loop by reseting the worpdress query with wp_reset_query();
	*
	* 
	* @param	string	$parent_cat_slug
	* @param	array	args
	* @return	obj		wp_query
	* @access 	public
	* @author	Ben Moody
	*/
	function loop_category( $parent_cat_slug = null, $args = array() ) {
		
		$_CatObject 		= null; //Cache object for categories
		$wp_query			= null;
		$cat_query_str		= null;
		$query_string		= null;
		
		//Default args
		$defaults = array(
			'posts_per_page' 	=> 5,
			'paged'				=> true,
			'post_type'			=> 'post'
		);
		
		$args = wp_parse_args( $args, $defaults );
		
		if( !empty( $parent_cat_slug ) ) {
			
			//Get all child categories of 'news'parent cat
			$child_cats = $this->get_cat_children(
				array(
					'child_of' => $parent_cat_slug
				)
			);
			
			//Loop child_cats and create query sting comprised of all cat id's
			if( !empty($child_cats['children']) ) {
				$i = 0;
				foreach( $child_cats['children'] as $Cat ){
					if( $i > 0 ) {	
						
						$cat_query_str.= ',' . $Cat->term_id;
					} else {
						//First in query sting add parent cat and first child
						$cat_query_str.= $child_cats['args']['child_of'] . ',' . $Cat->term_id;
					}
					
					$i ++;
				}
			} else {
			
				//No child cats found, just add the parent to the query string
				$cat_query_str.= $child_cats['args']['child_of'];
				
			}
			
			//Build query string
			$query_string.= "cat={$cat_query_str}";
			foreach( $args as $key => $arg ) {
				$query_string.= "&{$key}={$arg}";
			}
			
			//Query posts using custom args
			query_posts( $query_string );
			
			//Cache parent cat id
			$this->loop_cat_parent_id = $child_cats['args']['child_of'];
			
			//Call method to filter the wordpress categories widget to display only current category's children
			add_filter('widget_categories_args', array( &$this, 'loop_category_widget' ) );
			
		} else {
			return false;
		}
		
	}

	/**
	 * loop_category_widget
	 *
	 * Called by 'widget_categories_args' filter, setup by $this->loop_category
	 * Filters the wordpress default category widget to show only categories which are children of
	 * the parent category if current loop is a custom loop filtered by category.
	 *
	 */
	function loop_category_widget( $cat_args ) {
		
		if( isset($this->loop_cat_parent_id) ) {
			$cat_args['child_of'] = $this->loop_cat_parent_id;
		}
		
		return $cat_args;
	}
	
	/**
	 * get_cat_children()
	 *
	 * This automates get_categories by setting some default args as well as
	 * allowing you to pass a string for category rather than just cat_id
	 * lets you use cat slug too.
	 *
	 * @param	array	$args - any get_categories args you wish to customize
	 * @return	array	$_children	- Onject array containing all child/grandchild categories
	 */
	public function get_cat_children( $args = array() ) {
		
		//Init vars
		global $post;
		$_children	= array();
		$_CatObject = null;
		$_args 		= array(
			'type'                     => 'post',
			'child_of'                 => 0,
			'parent'                   => '',
			'orderby'                  => 'name',
			'order'                    => 'ASC',
			'hide_empty'               => 1,
			'hierarchical'             => 1,
			'exclude'                  => '',
			'include'                  => '',
			'number'                   => '',
			'taxonomy'                 => 'category',
			'pad_counts'               => false
		);
		
		//Combine default arg with args provided
		$args = array_merge( $_args, $args );
		
		//Check if we should get all children and grandchildren
		if( isset($args['child_of']) && is_string($args['child_of']) ) {
			
			//Get category id using cat slug provided
			if( $_CatObject = get_category_by_slug($args['child_of']) ){
				$args['child_of'] 	= $_CatObject->term_id;
			}
			
		} elseif ( isset($args['parent']) && is_string($args['parent']) ) {
			
			//Get category id using cat slug provided
			if( $_CatObject = get_category_by_slug($args['parent']) ){
				$args['parent'] 	= $_CatObject->term_id;
			}
			
		}
		
		//Get all category children
		$_children['children'] = get_categories( $args );
		
		//Return args too
		$_children['args'] = $args;
		
		return $_children;
	}
	
}