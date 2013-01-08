<?php
/*
Author: Benjamin Moody
URL: htp://www.BenjaminMoody.com
Version: 2.0
*/

/**
* ADD CUSTOM THEME FUNCTIONS HERE -----
*
*/	

//Combine and Minify all enqueued scripts and styles - comment out to disable auto minifying
add_action( 'wp_print_scripts', 'prso_theme_merge_scripts' );	
add_action( 'wp_print_styles', 'prso_theme_merge_styles' );	

/**
* merge_scripts
* 
* Called during wp_print_scripts to intercept script output from theme and plugins.
* It dequeues all scripts enqueued using wp_enqueue_scripts and calls $this->minify_scripts to merge
* all the scripts into one single app.min.js.
*
* NOTE: To ignore a script add it's enqueue handle to $exceptions array
*
*
* @access 	public
* @author	Ben Moody
*/
if( !function_exists('prso_theme_merge_scripts') ) {

	function prso_theme_merge_scripts() {
		
		$args = array(
			'merged_path' 		=> get_stylesheet_directory() . '/javascripts/app.min.js', //Full path to your new merged script file -REQ
			'merged_url'		=> get_stylesheet_directory_uri() . '/javascripts/app.min.js',
			'depends'			=> array( 'jquery' ), //Array of script handles your merged script depends on
			'handles'			=> array( 'modernizr', 'foundation-reveal', 'foundation-orbit', 'foundation-custom-forms', 'foundation-placeholder', 'foundation-tooltips', 'foundation-off-canvas', 'foundation-app' ), //Declare specific handles to merge
			'enqueue_handle'	=> 'presso-theme-app-min'
		);
		
		$exceptions = array(
			'jquery',
			'admin-bar'
		);
		
		do_action( 'prso_minify_merge_scripts', $args, $exceptions );
		
	}
	
}


/**
* merge_styles
*
* Called during wp_print_styles to intercept style output and merge/minify all enqueued styles
* into one stlyesheet.
*
* Makes use of custom WP Action 'prso_minify_merge_styles' which deenques all styles and enqueues
* the new merged stylessheet. Note it will ignore all WP Core stylesheets and process only those in
* /wp-content/ thus all plugins and theme styles.
*
* @access 	public
* @author	Ben Moody
*/
if( !function_exists('prso_theme_merge_styles') ) {

	function prso_theme_merge_styles() {
		
		$args = array(
			'merged_path' 		=> get_stylesheet_directory() . '/stylesheets/app-min.css', //Full path to your new merged script file -REQ
			'merged_url'		=> get_stylesheet_directory_uri() . '/stylesheets/app-min.css',
			'enqueue_handle'	=> 'presso-theme-app-min'
		);
		
		do_action( 'prso_minify_merge_styles', $args );
		
	}
	
}


//Add a search field to the main nav
add_filter( 'wp_nav_menu_items', 'prso_add_search_to_nav', 10, 2 );
if( !function_exists('prso_add_search_to_nav') ) {
	
	function prso_add_search_to_nav( $items, $args ) {
	
		//Add only to main nav
		if( isset($args->menu) && $args->menu === 'main_nav' ) {
			
			ob_start();
			?>
			<li class="nav-search" >
				<form action="<?php echo home_url( '/' ); ?>" method="get">
			      <div class="twelve columns">
			        <input type="text" id="search" placeholder="Search" name="s" value="<?php the_search_query(); ?>" />
			      </div>
		  		</form>
			</li>
			<?php
			$items.= ob_get_contents();
			ob_end_clean();
			
		}
		
		return $items;
	}
	
}



/**
* PRSO THEME FRAMEWORK -- DO NOT REMOVE!
* Call method to boot core framework
*
*/	
if( file_exists( get_template_directory() . '/prso_framework/bootstrap.php' ) ) {

	if( !class_exists('PrsoThemeBootstrap') ) {
		
		/**
		* Include config file to set core definitions
		*
		*/
		if( file_exists( get_stylesheet_directory() . '/prso_framework/config.php' ) ) {
			
			include( get_stylesheet_directory() . '/prso_framework/config.php' );
			
			if( class_exists('PrsoThemeConfig') ) {
				
				new PrsoThemeConfig();
				
				//Core loaded, load rest of plugin core
				include( get_template_directory() . '/prso_framework/bootstrap.php' );

				//Instantiate bootstrap class
				if( class_exists('PrsoThemeBootstrap') ) {
					new PrsoThemeBootstrap();
				}
				
			}
			
		}
		
	} else {
		
		//If there is a class namespace conflict, deactivate class and error out
		wp_die( wp_sprintf( '%1s: ' . __( 'Sorry, it appears that you already have a Prso Theme active.', 'prso_core' ), __FILE__ ) );
		
	}
	
}

//Pagination function
if( !function_exists('prso_theme_paginate') ) {
	
	function prso_theme_paginate( $menu_html = '', $before = '', $after = '' ) {
		
		global $wpdb, $wp_query;
		
		$request 		= $wp_query->request;
		$posts_per_page = intval(get_query_var('posts_per_page'));
		$paged 			= intval(get_query_var('paged'));
		$numposts 		= $wp_query->found_posts;
		$max_page 		= $wp_query->max_num_pages;
		
		
		if ( $numposts <= $posts_per_page ) { return; }
		if(empty($paged) || $paged == 0) {
			$paged = 1;
		}
		$pages_to_show = 7;
		$pages_to_show_minus_1 = $pages_to_show-1;
		$half_page_start = floor($pages_to_show_minus_1/2);
		$half_page_end = ceil($pages_to_show_minus_1/2);
		$start_page = $paged - $half_page_start;
		if($start_page <= 0) {
			$start_page = 1;
		}
		$end_page = $paged + $half_page_end;
		if(($end_page - $start_page) != $pages_to_show_minus_1) {
			$end_page = $start_page + $pages_to_show_minus_1;
		}
		if($end_page > $max_page) {
			$start_page = $max_page - $pages_to_show_minus_1;
			$end_page = $max_page;
		}
		if($start_page <= 0) {
			$start_page = 1;
		}
			
		echo $before.'<ul class="pagination clearfix">'."";
		if ($paged > 1) {
			$first_page_text = "&laquo";
			echo '<li class="prev"><a href="'.get_pagenum_link().'" title="First">'.$first_page_text.'</a></li>';
		}
			
		echo '<li class="">';
		previous_posts_link('&larr; Previous');
		echo '</li>';
		for($i = $start_page; $i  <= $end_page; $i++) {
			if($i == $paged) {
				echo '<li class="current"><a href="#">'.$i.'</a></li>';
			} else {
				echo '<li><a href="'.get_pagenum_link($i).'">'.$i.'</a></li>';
			}
		}
		echo '<li class="">';
		next_posts_link('Next &rarr;');
		echo '</li>';
		if ($end_page < $max_page) {
			$last_page_text = "&raquo;";
			echo '<li class="next"><a href="'.get_pagenum_link($max_page).'" title="Last">'.$last_page_text.'</a></li>';
		}
		echo '</ul>'.$after."";
		
	}
	
}


// Comment Layout
if( !function_exists('prso_theme_comments') ) {

	function prso_theme_comments($comment, $args, $depth) {
	   $GLOBALS['comment'] = $comment; ?>
		<li <?php comment_class(); ?>>
			<article id="comment-<?php comment_ID(); ?>" class="panel clearfix">
				<div class="comment-author vcard row clearfix">
	                <div class="twelve columns">
	                    <div class="
	                        <?php
	                        $authID = get_the_author_meta('ID');
	                                                    
	                        if($authID == $comment->user_id)
	                            echo "panel callout";
	                        else
	                            echo "panel";
	                        ?>
	                    ">
	                        <div class="row">
	            				<div class="avatar two columns">
	            					<?php echo get_avatar($comment,$size='75',$default='<path_to_url>' ); ?>
	            				</div>
	            				<div class="ten columns">
	            					<?php printf(__('<h4 class="span8">%s</h4>'), get_comment_author_link()) ?>
	            					<time datetime="<?php echo comment_time('Y-m-j'); ?>"><a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>"><?php comment_time('F jS, Y'); ?> </a></time>
	            					
	            					<?php edit_comment_link(__('Edit'),'<span class="edit-comment">', '</span>'); ?>
	                                
	                                <?php if ($comment->comment_approved == '0') : ?>
	                   					<div class="alert-box success">
	                      					<?php _e('Your comment is awaiting moderation.') ?>
	                      				</div>
	            					<?php endif; ?>
	                                
	                                <?php comment_text() ?>
	                                
	                                <!-- removing reply link on each comment since we're not nesting them -->
	            					<?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
	                            </div>
	                        </div>
	                    </div>
	                </div>
				</div>
			</article>
	    <!-- </li> is added by wordpress automatically -->
	<?php
	} // don't remove this bracket!

}