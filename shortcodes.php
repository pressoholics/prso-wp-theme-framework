<?php
/**
* Theme Shortcodes
* 
* Contains all shortcodes used in this theme
* 
* @access 	public
* @author	Ben Moody
*/

#-----------------------------------------------------------------
# Disable wpautop on shortcode content
#-----------------------------------------------------------------
remove_filter( 'the_content', 'do_shortcode', 11 );
add_filter( 'the_content', 'do_shortcode', 13 );

//Be sure all empty p tags are removed from content
add_filter( 'the_content', 'remove_empty_p', 12 );
add_filter( 'widget_text', 'remove_empty_p', 5 );
function remove_empty_p( $content ){

    $content = force_balance_tags($content);
    $content = preg_replace('#<p>\s*+(<br\s*/*>)?\s*</p>#i', '', $content);
    $content = shortcode_unautop( $content );
    
    return preg_replace('#<p>\s*+(<br\s*/*>)?\s*</p>#i', '', $content);
}

/**
* Shortcode Example
* 
* DESCRIPTION
* 
* @param	array	$atts 		- Shortcode attributes
* @param	string	$content 	- Post content
* @var		type	name
* @return	string	$content
* @access 	public
* @author	Ben Moody
*/
/*
add_shortcode( 'tab', 'EXAMPLE' );
function EXAMPLE( $atts, $content ){
	
    extract(shortcode_atts(array(
	), $atts));
	
    if( !empty($content) ) {
	    
	    //Add shortcode actions here
	    
	    
	    //NOTE: You may need to manually apply wpautop to content in your shortcode
	    //		If you want the user to be able to add P and BR with tinymce
	    $content = wpautop( trim($content) );
	    
    }
    
    return $content;
}
*/

#-----------------------------------------------------------------
# Zurb Foundation Shortcodes
#-----------------------------------------------------------------

//Register flex video shortcode
add_shortcode( 'video_wrapper', 'presso_theme_flex_video' );

/**
* presso_theme_flex_video
* 
* Wraps video emebd code in a div to ensure flex size on devices
* 
*/
function presso_theme_flex_video( $attr, $content = null ) {

	if( isset($content) ) {
		
		//Init vars
		$output	= NULL;
		
		//Wrap content in dic with correct zurb founcation class
		$output = "<div class='flex-video widescreen'>$content</div>";
		
		//Return content
		return $output;
		
	}

}

//Register accordion content shortcode
add_shortcode( 'accordion', 'presso_theme_accordion' );

/**
* presso_theme_accordion
* 
* Wraps content in html to make use of foundation accordion
* 
*/
function presso_theme_accordion( $attr, $content = null ) {

	//Wrap content in accordion ul container
	$content = '<ul class="accordion">' . do_shortcode($content) . '</ul>';
	
	return $content;
}

//Register accordion content shortcode
add_shortcode( 'accordion_row', 'presso_theme_accordion_content' );

/**
* presso_theme_accordion_content
* 
* Wraps content in html to make use of foundation accordion
* 
*/
function presso_theme_accordion_content( $attr, $content = null ) {

	if( isset($content) ) {
		
		//Init vars
		$output			= NULL;
		$active_class 	= NULL;
		$defaults		= array(
			'title'		=> NULL,
			'active'	=> 'false'
		);
		
		//Extract args
		$attr = wp_parse_args( $attr, $defaults );
		
		extract( $attr );
		
		//Format content
		$content = do_shortcode( $content );
		$content = wpautop( trim($content) );
		
		//Is this slide active
		if( $active == 'true' ) {
			$active_class = 'active';
		}
		
		ob_start();
		?>
		<li class="<?php echo $active_class; ?>">
			<div class="title">
				<h5><?php esc_attr_e( $title ); ?></h5>
			</div>
			<div class="content">
				<?php echo $content; ?>
			</div>
		</li>
		<?php
		$output = ob_get_contents();
		ob_end_clean();
		
		//Return content
		return $output;
		
	}

}

//Register Panel shortcode
add_shortcode( 'box', 'presso_theme_panel_wrapper' );

/**
* presso_theme_panel_wrapper
* 
* Wraps content in html to make use of foundations panel
* 
*/
function presso_theme_panel_wrapper( $attr, $content = null ) {

	if( isset($content) ) {
		
		//Init vars
		$output		= NULL;
		
		//Format content
		$content = do_shortcode( $content );
		$content = wpautop( trim($content) );
		
		ob_start();
		?>
		<div class="panel">
			<?php echo $content; ?>
		</div>
		<?php
		$output = ob_get_contents();
		ob_end_clean();
		
		//Return content
		return $output;
		
	}

}