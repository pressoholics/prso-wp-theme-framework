<?php
/**
* shortcodes
* 
* Contains all prso theme framework default shortcodes, any custom shortcodes
* should be added to shortcodes.php in theme root.
* 
* @access 	public
* @author	Ben Moody
*/
class PrsoThemeShortcodes {
	
	function __construct() {
		
		//Add shortcodes
		$this->add_shortcodes();
		
	}
	
	private function add_shortcodes() {
		
		//Add shorcodes for gallery
		remove_shortcode('gallery', array($this, 'gallery_shortcode') );
		add_shortcode('gallery', array($this, 'gallery_shortcode_tbs') );
		
		//Add shortcodes for Buttons
		add_shortcode('button', array($this, 'buttons') );
		
		//Add shortcodes for Alerts
		add_shortcode('alert', array($this, 'alerts') );
		
		//Add shortcodes for Panels
		add_shortcode('panel', array($this, 'panels') );
		
	}
	
	/**
	* Gallery Shortcodes
	* 
	*/
	public function gallery_shortcode_tbs($attr) {
		global $post, $wp_locale;
	
		$args = array( 'post_type' => 'attachment', 'numberposts' => -1, 'post_status' => null, 'post_parent' => $post->ID ); 
		$attachments = get_posts($args);
		if ($attachments) {
			$output = '<ul class="block-grid four-up">';
			foreach ( $attachments as $attachment ) {
				$output .= '<li>';
				$att_title = apply_filters( 'the_title' , $attachment->post_title );
				$output .= wp_get_attachment_link( $attachment->ID , 'thumbnail', true );
				$output .= '</li>';
			}
			$output .= '</ul>';
		}
	
		return $output;
	}
	
	
	
	/**
	* Button Shortcodes
	* 
	*/
	public function buttons( $atts, $content = null ) {
		extract( shortcode_atts( array(
		'type' => 'radius', /* radius, round */
		'size' => 'medium', /* small, medium, large */
		'color' => 'blue',
		'nice' => 'false',
		'url'  => '',
		'text' => '', 
		), $atts ) );
		
		$output = '<a href="' . $url . '" class="button '. $type . ' ' . $size . ' ' . $color;
		if( $nice == 'true' ){ $output .= ' nice';}
		$output .= '">';
		$output .= $text;
		$output .= '</a>';
		
		return $output;
	} 
	
	/**
	* Alert Shortcodes
	* 
	*/
	public function alerts( $atts, $content = null ) {
		extract( shortcode_atts( array(
		'type' => '	', /* warning, success, error */
		'close' => 'false', /* display close link */
		'text' => '', 
		), $atts ) );
		
		$output = '<div class="fade in alert-box '. $type . '">';
		
		$output .= $text;
		if($close == 'true') {
			$output .= '<a class="close" href="#">?</a></div>';
		}
		
		return $output;
	}
	
	/**
	* Panel Shortcodes
	* 
	*/
	public function panels( $atts, $content = null ) {
		extract( shortcode_atts( array(
		'type' => '	', /* warning, success, error */
		'close' => 'false', /* display close link */
		'text' => '', 
		), $atts ) );
		
		$output = '<div class="panel">';
		$output .= $text;
		$output .= '</div>';
		
		return $output;
	}
	
}