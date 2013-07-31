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

add_filter('get_archives_link', 'archive_count_no_brackets');
add_filter('wp_list_categories', 'archive_count_no_brackets');
function archive_count_no_brackets($links) {
	
	//Wrap post count in span for styling
	$links = str_replace('(', '</a>&nbsp;<span class="prso-post-count">(', $links);
	$links = str_replace(')', ')</span>', $links);

	return $links;
	
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
		$config_path = get_template_directory() . '/prso_framework/config.php';
		
		//Search for config in child theme
		if( file_exists( get_stylesheet_directory() . '/prso_framework/config.php' ) ) {
			$config_path = get_stylesheet_directory() . '/prso_framework/config.php';
		}
		
		if( file_exists($config_path) ) {
			
			include( $config_path );
			
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
	            					<?php echo get_avatar($comment,$size='75',$default='' ); ?>
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