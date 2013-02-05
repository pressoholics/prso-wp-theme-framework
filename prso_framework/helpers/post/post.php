<?php
/**
 * Wordpress Post/Page data Helper class file.
 *
 * Simplifies the process of quering wordpress post and page data.
 *
 * CONTENTS:
 *
 *	1. do_action('prso_get_related_posts', $args); 
 *	2. apply_filters('prso_get_page_id_by_slug', NULL, $page_slug, $post_type);
 *  3. apply_filters('prso_get_page_content', NULL, $page_id_slug);
 *  4. do_action('prso_query_posts_by_category', $cat_slug, $args);
 *  5. do_action('prso_prev_next_permalink', $args);
 * 
 */
class PostHelper {
	
	//Cache parent cat id - Set by method loop_category, Used by method loop_category_widget
	private $loop_cat_parent_id = null;
	
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
 		* 1. prso_get_related_posts
 		* 	 Returns/Echos a ul list of related posts.
 		*/
 		$this->add_action( 'prso_get_related_posts', 'get_related_posts', 10, 1 );
 		
 		/**
 		* 2. prso_get_page_id_by_slug
 		* 	 Shortcut for returning a page ID using it's slug.
 		*/
 		$this->add_filter( 'prso_get_page_id_by_slug', 'get_ID_by_slug', 1, 3 );
 		
 		/**
 		* 3. prso_get_page_content
 		* 	 Shortcut for returning a pages content by ID or Slug
 		*/
 		$this->add_filter( 'prso_get_page_content', 'get_page_content', 1, 2 );
 		
 		/**
 		* 4. prso_query_posts_by_category
 		* 	 Alters the main loop to return posts in a specific category
 		*/
 		$this->add_action( 'prso_query_posts_by_category', 'loop_category', 10, 2 );
 		
 		/**
 		* 5. prso_prev_next_permalink
 		* 	 Echo permalink to next/prev post in a loop of pages
 		*/
 		$this->add_action( 'prso_prev_next_permalink', 'prev_next_pagination', 10, 1 );
 		
 	}
	
	/**
	* get_related_posts
	* 
	* do_action('prso_get_related_posts', $args);
	*
	* Custom method which returns an ul list of related posts.
	*
	* Use in theme via custom Action Hook 'prso_get_related_posts'
	* 
	* Use WP_Query args to adjust std query elements such as numberposts.
	* Also accepts a number of custom args:
	*
	* 1. 'relation' - category/tag/category__and__tag/category__or__tag
	*		Use this to adjust the relationship between tags and categories when
	*		querying the post relationship, so you want tag/cat to be independent
	*		or do you define related post as posts which share both cat and tag
	*		or just on or the other.
	*
	* 2. 'no_posts_msg'	- string or NULL
	*		If you dont need a msg to output when no related posts found, set to NULL
	*
	*
	* @param	array	$args (see above comments)
	* @access 	public
	* @author	Ben Moody
	*/
	public function get_related_posts( $args = array() ) {
		
		//Init vars
		global $post;
		$tags		= array();
		$tag_ids	= array();
		$cats		= array();
		$cat_ids	= array();
		$query 		= array();
		$get_posts	= NULL;
		$related_posts = NULL;
		$output		= NULL;
		$defaults = array(
			'numberposts' => 5, 'offset' => 0,
			'category' => 0, 'orderby' => 'post_date',
			'order' => 'DESC', 'include' => array(),
			'exclude' => array(), 'meta_key' => '',
			'meta_value' =>'', 'post_type' => 'post',
			'suppress_filters' => true,
			'no_posts_msg'	=> 'No related posts',
			'relation'		=> 'category__and__tag'
		);
		
		//Parse args
		$query = wp_parse_args( $args, $defaults );
		
		//First check the requested relationship between tag and catagories and set the wp_query tax_query arg
		switch( $query['relation'] ) {
			case 'category__and__tag':
				$query['tax_query']['relation'] = 'AND';
				break;
			case 'category__or__tag':
				$query['tax_query']['relation'] = 'OR';
				break;
		}
		
		//Check if we need categories
		if( $query['relation'] === 'tag' || $query['relation'] === 'category__and__tag' || $query['relation'] === 'category__or__tag' ) {
			
			//Get all post tag id's
			$tags = wp_get_post_tags($post->ID);
			
			//Loop post tags and cache in query var
			if( !empty($tags) ) {
				foreach( $tags as $tag ) {
					if( isset($tag->term_id) ) {
						$tag_ids[] = $tag->term_id;
					}
				}
				
				//Cache a wp_query tax_query arg
				$query['tax_query'][] =	array(
					'taxonomy'	=> 'post_tag',
					'field'		=> 'id',
					'terms'		=> $tag_ids
				);
			}
			
		}		
		
		
		//Check if we need categories
		if( $query['relation'] === 'category' || $query['relation'] === 'category__and__tag' || $query['relation'] === 'category__or__tag' ) {
			
			//Get all post cat id's
			$cats = wp_get_post_categories( $post->ID );
			
			//Loop post categories and cache in query var
			if( !empty($cats) ) {
				foreach( $cats as $cat ) {
					//Get term data on current cat in loop
					$term = get_category( $cat );
					//Cache the terms term_id
					if( isset($term->term_id) ) {
						$cat_ids[] = $term->term_id;
					}
				}
				
				//Cache a wp_query tax_query arg
				$query['tax_query'][] =	array(
					'taxonomy' 			=> 'category',
					'terms' 			=> $cat_ids,
					'field' 			=> 'term_id',
					'include_children' 	=> false
				);
				
				//Do not relate posts via the 'uncategorized' category
				$query['tax_query'][] = array(
					'taxonomy'	=> 'category',
					'terms'		=> array( 1 ),
					'field'		=> 'term_id',
					'operator'	=> 'NOT IN'
				);
			}
			
		}
		
		//Lets get all related posts using our new query
		$query['post__not_in'] 	= array( $post->ID );
		$get_posts 				= new WP_Query;
		$related_posts 			= $get_posts->query( $query );
		
		//Start the html to output unordered list
		$output = '<ul id="prso-related-posts">';
		
		//Loop any posts found and add a list item for each to output html
		if( !empty($related_posts) ) {
			
			foreach( $related_posts as $post ) {
				//Prepare global post data
				setup_postdata( $post );
				//Cache html
				ob_start();
				?>
				<li class="related_post">
					<a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
				</li>
				<?php
				$output.= ob_get_contents();
				ob_end_clean();
			}
			
		} else {
			
			//No related posts found
			if( isset($query['no_posts_msg']) ) {
				ob_start();
				?>
				<li class="related_post">
					<?php esc_attr_e( $query['no_posts_msg'] ); ?>
				</li>
				<?php
				$output.= ob_get_contents();
				ob_end_clean();
			}
			
		}
		
		//Reset wp query
		wp_reset_query();
		//Reset wp global post
		wp_reset_postdata();
		
		//Close output html
		$output.= '</ul>';
		
		if( !empty($output) ) {
			echo $output;
		}
		
	}
	
	/**
	* get_ID_by_slug
	*
	* apply_filters('prso_get_page_id_by_slug', NULL, $page_slug, $post_type);
	*
	* Shortcut for returning a page ID using it's slug
	*
	* @param	string	$page_slug - page slug, can be take from url :)
	* @param	string	$post_type - 'page', 'post', 'custom post type'
	*/ 
	public function get_ID_by_slug( $output, $page_slug, $post_type = 'page' ) {
	    
	    $page = get_page_by_path( $page_slug, 'OBJECT', $post_type );
	    
	    if ( isset($page->ID) ) {
	        $output = $page->ID;
	    } else {
	        $output = null;
	    }
	    
	    return $output;
	}
	
	/**
	* get_page_content
	* 
	* apply_filters('prso_get_page_content', NULL, $page_id_slug);
	* 
	* Returns the content of any page by either it's ID or slug
	*
	* @param	mixed	ID or slug of page content you wish to return
	* @access 	public
	* @author	Ben Moody
	*/
	public function get_page_content( $content, $page_id_slug = NULL ) {
		
		//Init vars
		$page_data 	= NULL;
		
		$page_id_slug = esc_attr( $page_id_slug );
		
		if( isset($page_id_slug) ) {
			
			//Detect if this is a page ID or Slug
			if( is_string($page_id_slug) ) {
				//Convert slug into page ID
				$page_id_slug = apply_filters( 'prso_get_page_id_by_slug', NULL, $page_id_slug );
			}
			
			//Get page data
			if( isset($page_id_slug) ) {
				$page_data = get_page( $page_id_slug );
				
				//Get page content and run wordpress content filters on it
				$content = apply_filters( 'the_content', $page_data->post_content );
			}
			
		}
		
		return $content;
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
	public function loop_category( $parent_cat_slug = null, $args = array() ) {
		
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
	public function loop_category_widget( $cat_args ) {
		
		if( isset($this->loop_cat_parent_id) ) {
			$cat_args['child_of'] = $this->loop_cat_parent_id;
		}
		
		return $cat_args;
	}
	
	/**
	 * prev_next_pagination()
	 *
	 * This helper will return the post permalink for next or previous posts of similar post type and/or
	 * custom taxonomy.
	 *
	 * The helper uses wp_query to query the posts so simply add you wp_query args into $args and the helper
	 * will automatically create a 1 post/page pagination loop allowing you to use prev/next post buttons for users
	 * to loop through a set of posts.
	 *
	 * Be sure to declare whether you want the 'next' or 'previous' post usnig the 'direction' key in args array
	 *
	 * @param	array	$args - any get_categories args you wish to customize
	 * @return	array	$_children	- Onject array containing all child/grandchild categories
	 */
	public function prev_next_pagination( $args = array() ) {
		
		//Init vats
		global $post;
		$paged				= NULL;
		$total_posts		= NULL;
		$post_terms 		= NULL;
		$post_term_slug		= NULL;
		$taxonomy 			= NULL;
		$portfolio_posts	= NULL;	
		$PostData			= NULL;
		$permalink			= NULL;
		$defaults = array(
			'direction' 		=> NULL,
			'post_type'			=> 'page',
			'posts_per_page' 	=> 1,
			'tax_query'			=> array(
				array(
					'taxonomy' 	=> NULL,
					'field'		=> 'slug',
					'terms'		=> NULL
				)
			)
		);
		
		$args = wp_parse_args($args, $defaults);
		
		extract($args);
		
		//Cache the page var
		$paged = get_query_var('page');
		
		//First get the current portfolio items taxonomy
		if( isset($post->ID) ) {
			$post_terms = wp_get_post_terms( $post->ID, $taxonomy );
			
			//Cache the first term as artist cat
			if( isset($post_terms[0]->slug) ) {
				$post_term_slug = $post_terms[0]->slug;
			}
			
			//Get all portfolio post for this artist
			$args['tax_query'][0]['terms'] = $post_term_slug;
			
			
			//this is the first page and we must find where this sits in relation to other posts	
			$paged = NULL;
			
			//We need to find which page the landing page sits in relation to wp_query pagination
			//So get all pages and loop until we find the page we are on
			$args['posts_per_page'] = -1;
			$portfolio_posts = new WP_Query( $args );
			
			wp_reset_query();
			
			//Loop through all posts and find out where our current post is in the array this will be the page var
			if( isset($portfolio_posts->posts) && !empty($portfolio_posts->posts) ) {
				$post_page_count = 1;
				
				foreach( $portfolio_posts->posts as $key => $post_obj ){
					//If we have found the landing post, cache it's array position as the $paged var
					if( $post_obj->ID === $post->ID ) {
						$paged = $post_page_count;
						break;
					}
					
					$post_page_count++;
				}
				
				//Now run the query again this time find either the page previous or next and get the hyperlink
				if( isset($paged) && is_int($paged) ) {
					
					//First cache the total number of posts found in the pagination
					$total_posts = count( $portfolio_posts->posts );
			
					//As we only have 1 post per page $total_posts is also the total number of pages too!
					$total_pages = $total_posts;
					
					//Based on $direction var set the page to get
					switch( $direction ) {
						case 'previous':
							//Add page arg to url
							$new_page = $paged - 1;
							
							//Make sure we don't go negative
							if( $new_page < 1 ) {
								$new_page = $total_pages;
							}
							
							break;
						case 'next':
							//Add page arg to url
							$new_page = $paged + 1;
							
							//Make sure we don't go over total pages
							if( $new_page > $total_pages ) {
								$new_page = 1;
							}
							
							break;
					}
					
					$args['paged'] = $new_page;
					
					$args['posts_per_page'] = 1;
					$portfolio_posts = new WP_Query( $args );
					
					if( isset($portfolio_posts->posts) ) {
						$PostData = $portfolio_posts;
					}
					
					wp_reset_query();
					
				}
				
			}
			
			//So if we have found the post data lets create the hyperlink for next/previous page
			if( isset($PostData->posts[0]->ID) ){
				
				//Get the post permalink
				$permalink = get_post_permalink( $PostData->posts[0]->ID );
					
			}
			
		}
		
		if( !empty($permalink) ) {
			echo esc_url( $permalink );
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