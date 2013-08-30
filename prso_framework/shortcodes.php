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
		
		#-----------------------------------------------------------------
		# Disable wpautop on shortcode content -
		# Filter Order --> 'do_shortcode', 'wpautop', 'remove_empty_p'
		#-----------------------------------------------------------------
		
		remove_filter( 'the_content', 'wpautop' );
		add_filter( 'the_content', 'wpautop', 13 );
		
		remove_filter( 'the_content', 'do_shortcode', 11 );
		add_filter( 'the_content', 'do_shortcode', 12 );
		
		//Be sure all empty p tags are removed from content
		add_filter( 'the_content', array($this, 'remove_empty_p'), 14 );
		add_filter( 'widget_text', array($this, 'remove_empty_p'), 5 );
		
		//Add shortcodes
		$this->add_shortcodes();
		
		//Add TinyMCE buttons
		$this->add_tinymce_buttons();
		
	}
	
	public function remove_empty_p( $content ){

	    return apply_filters( 'prso_remove_p', $content );
	    
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
		
		//Add shortcodes for Flex Videos (youtube/vimeo)
		add_shortcode('video', array($this, 'flex_video') );
		
		//Add shortcodes for Accordion
		add_shortcode('accordion', array($this, 'accordion') );
		add_shortcode('accordion_row', array($this, 'accordion_content') );
		
		//Add shortcode for two column content layout
		add_shortcode('two_columns', array($this, 'two_column_content') );
		
	}
	
	private function add_tinymce_buttons() {
		
		//Two column content button
		add_action( 'admin_init', array($this, 'two_column_shortcode_add_tinymce_button') );
		
		//Panel content button
		add_action( 'admin_init', array($this, 'panel_shortcode_add_tinymce_button') );
		
		//Accordion content button
		add_action( 'admin_init', array($this, 'accordion_shortcode_add_tinymce_button') );
		
		//Accordion content button
		add_action( 'admin_init', array($this, 'button_shortcode_add_tinymce_button') );
		
		//Video button
		add_action( 'admin_init', array($this, 'video_shortcode_add_tinymce_button') );
		
	}
	
	/**
	* Gallery Shortcodes
	*
	* Uses Zurb Foundation Clearing Feature
	* 
	*/
	public function gallery_shortcode_tbs($attr) {
	
		$post = get_post();

		static $instance = 0;
		$instance++;
	
		if ( ! empty( $attr['ids'] ) ) {
			// 'ids' is explicitly ordered, unless you specify otherwise.
			if ( empty( $attr['orderby'] ) )
				$attr['orderby'] = 'post__in';
			$attr['include'] = $attr['ids'];
		}
	
		// Allow plugins/themes to override the default gallery template.
		$output = apply_filters('post_gallery', '', $attr);
		if ( $output != '' )
			return $output;
	
		// We're trusting author input, so let's at least make sure it looks like a valid orderby statement
		if ( isset( $attr['orderby'] ) ) {
			$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
			if ( !$attr['orderby'] )
				unset( $attr['orderby'] );
		}
		
		$gallery_defaults = array(
			'order'      => 'ASC',
			'orderby'    => 'menu_order ID',
			'id'         => $post ? $post->ID : 0,
			'itemtag'    => 'dl',
			'icontag'    => 'dt',
			'captiontag' => 'dd',
			'columns'    => 4,
			'size'       => 'thumbnail',
			'include'    => '',
			'exclude'    => ''
		);
		
		//Filter gallery deafults
		$gallery_defaults = apply_filters( 'prso_gallery_shortcode_args', $gallery_defaults );
		
		extract(shortcode_atts($gallery_defaults, $attr, 'gallery'));
		
		$id = intval($id);
		if ( 'RAND' == $order )
			$orderby = 'none';
	
		if ( !empty($include) ) {
			$_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
	
			$attachments = array();
			foreach ( $_attachments as $key => $val ) {
				$attachments[$val->ID] = $_attachments[$key];
			}
		} elseif ( !empty($exclude) ) {
			$attachments = get_children( array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
		} else {
			$attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
		}
	
		if ( empty($attachments) )
			return '';
	
		if ( is_feed() ) {
			$output = "\n";
			foreach ( $attachments as $att_id => $attachment )
				$output .= wp_get_attachment_link($att_id, $size, true) . "\n";
			return $output;
		}
	
		$itemtag = tag_escape($itemtag);
		$captiontag = tag_escape($captiontag);
		$icontag = tag_escape($icontag);
		$valid_tags = wp_kses_allowed_html( 'post' );
		if ( ! isset( $valid_tags[ $itemtag ] ) )
			$itemtag = 'dl';
		if ( ! isset( $valid_tags[ $captiontag ] ) )
			$captiontag = 'dd';
		if ( ! isset( $valid_tags[ $icontag ] ) )
			$icontag = 'dt';
	
		$columns = intval($columns);
		
		//Set bloch grid class based on columns
		switch( $columns ) {
			case 1:
				$block_class = "one-up mobile-three-up";
				break;
			case 2:
				$block_class = "two-up mobile-three-up";
				break;
			case 3:
				$block_class = "three-up mobile-three-up";
				break;
			case 4:
				$block_class = "four-up mobile-three-up";
				break;
			case 5:
				$block_class = "five-up mobile-three-up";
				break;
			case 6:
				$block_class = "six-up mobile-three-up";
				break;
			default:
				$block_class = "four-up mobile-three-up";
				break;
		}
		
		$gallery_container = "<div class='row'><div class='twelve columns'><ul class='gallery galleryid-{$id} block-grid {$block_class}' data-clearing>";
		
		$output = apply_filters( 'gallery_style', $gallery_container );
	
		$i = 0;
		foreach ( $attachments as $id => $attachment ) {
			if ( ! empty( $attr['link'] ) && 'file' === $attr['link'] )
				$image_output = wp_get_attachment_link( $id, $size, false, false );
			elseif ( ! empty( $attr['link'] ) && 'none' === $attr['link'] )
				$image_output = wp_get_attachment_image( $id, $size, false );
			else
				$image_output = wp_get_attachment_link( $id, $size, true, false );
				
			$image_output = wp_get_attachment_link( $id, $size, false, false );	
			
			$image_meta  = wp_get_attachment_metadata( $id );
			
			//Cache image caption
			if ( trim($attachment->post_excerpt) ) {
				$caption_text = wptexturize($attachment->post_excerpt);
			}
			
			//Add caption to img tag
			$image_output = str_replace('<img', "<img data-caption='{$caption_text}'", $image_output);
			
			ob_start();
			?>
			<li>
				<?php echo $image_output; ?>
			</li>
			<?php
			$output.= ob_get_contents();
			ob_end_clean();
			
		}
	
		$output .= "</ul></div></div>";
		
		return $output;
	}
	
	
	
	/**
	* Button Shortcodes
	* 
	*/
	public function buttons( $atts, $content = null ) {
		extract( shortcode_atts( array(
		'type' => 'default', /* default, secondary, success, alert */
		'size' => 'medium', /* tiny, small, medium, large */
		'color' => '',
		'style' => 'square', /* square, radius, round */
		'url'  => '' 
		), $atts ) );
		
		$output = '<a href="' . esc_url($url) . '" class="button '. esc_attr($type) . ' ' . esc_attr($size) . ' ' . esc_attr($color);
		
		$output .= '">';
		$output .= esc_attr($content);
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
		'type' 		=> 'default', /* warning, success, error */
		'radius' 	=> 'default', 
		), $atts ) );
		
		//Detect radius option
		if( $radius == 'round' ) {
			$radius = 'radius';
		}
		
		//Format content
		$content = apply_filters( 'prso_format_shortcode_content', $content );
		
		$output = "<div class='panel {$radius} {$type}'>";
		$output .= $content;
		$output .= '</div>';
		
		return $output;
	}
	
	/**
	* flex_video
	* 
	* Wraps video emebd code in a div to ensure flex size on devices
	* 
	*/
	public function flex_video( $atts, $content = null ) {
		
		//Init vars
		$embed_url 	= "";
		$output			= NULL;
		
		extract( shortcode_atts( array(
		'service' 		=> 'youtube', 	/* youtube/vimeo */
		'video_id' 		=> $content, 		
		'aspect' 		=> 'widescreen',
		'params'		=> array(),
		'width'			=> 560,
		'height'		=> 315
		), $atts ) );
		
		if( !empty($video_id) ) {
			
			//Detect service and form embed code
			if( $service == 'youtube' ) {
				
				//Setup embed url
				$embed_url = "//www.youtube.com/embed/" . esc_attr($video_id) . "?rel=0";
				
				//Append params to url
				$embed_url = add_query_arg( $params, $embed_url );
				
				ob_start();
				?>
				<iframe width="<?php esc_attr_e($width); ?>" height="<?php esc_attr_e($height); ?>" src="<?php echo esc_url($embed_url); ?>" frameborder="0" allowfullscreen></iframe>
				<?php
				$output = ob_get_contents();
				ob_end_clean();
				
			} else {
				
				//Setup embed url
				$embed_url = "//player.vimeo.com/video/" . esc_attr($video_id);
				
				//Append params to url
				$embed_url = add_query_arg( $params, $embed_url );
				
				ob_start();
				?>
				<iframe src="<?php echo esc_url($embed_url); ?>" width="<?php esc_attr_e($width); ?>" height="<?php esc_attr_e($height); ?>" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
				<?php
				$output = ob_get_contents();
				ob_end_clean();
				
			}
						
			//Wrap output in correct flex-video div
			$output = "<div class='flex-video ". esc_attr($aspect) ."'>{$output}</div>";
			
		} else {
			$output = "[video]Missing Video ID[/video]";
		}
		
		return $output;
	}
	
	/**
	* presso_theme_accordion
	* 
	* Wraps content in html to make use of foundation accordion
	* 
	*/
	public function accordion( $attr, $content = null ) {
	
		//Wrap content in accordion ul container
		$content = '<ul class="accordion">' . do_shortcode($content) . '</ul>';
		
		return $content;
	}
	
	/**
	* presso_theme_accordion_content
	* 
	* Wraps content in html to make use of foundation accordion
	* 
	*/
	public function accordion_content( $attr, $content = null ) {
		
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
		   $content = apply_filters( 'prso_format_shortcode_content', $content );
			
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
	
	/**
	* two_column_content
	* 
	* Wraps content in six column founation classes to create a 2 column layout
	*
	* NOTE:: makes use of global $cja_shortcode_column_count var to count each column
	*        This is so that the function can wrap the two columns in a row class to ensure
	*        correct formatting using the zurb foundation
	* 
	* @param	array	$atts 		- Shortcode attributes
	* @param	string	$content 	- Post content
	* @var		global	$cja_shortcode_column_count - used to count columns processed, used to wrap cols in row div
	* @return	string	$content
	* @access 	public
	* @author	Ben Moody
	*/
	function two_column_content( $atts, $content ){
		
		//Init vars
		global $cja_shortcode_column_count;
		$first_column = FALSE;
		$output = NULL;
		
		/* No atts for this shortcode :)
	    extract(shortcode_atts(array(
		), $atts));
		*/
		
		//Detect if we need to set the global column counter to default
		if( !isset($cja_shortcode_column_count) ) {
			$cja_shortcode_column_count = 1;
		}
		
		//Detect if this is the first column of two
		if( $cja_shortcode_column_count === 1 ) {
			$first_column = TRUE;
		}
		
	    if( !empty($content) ) {
		    
		    //If is first row start foundation row div
		    if( $first_column === TRUE ) {
			    $output = '<div class="row">';
			    //Also increment global column counter to second column
			    $cja_shortcode_column_count = 2;
		    }
		    
		    //Format content
		   $content = apply_filters( 'prso_format_shortcode_content', $content );
		    
		    //Wrap content around 2 col foudnation div
		    ob_start();
		    ?>
		    <div class="six mobile-two columns">
		    	<?php echo apply_filters( 'prso_remove_p', wpautop(trim($content)) ); ?>
		    </div>
		    <?php
		    $output.= ob_get_contents();
		    ob_end_clean();
		    
		    //If is last row END row div
		    if( $first_column === FALSE ) {
			    $output.= '</div>';
			    //Reset global column counter to default
			    $cja_shortcode_column_count = 1;
		    }
		    
		    $content = $output;
		    
	    }
	    
	    return $content;
	}
	
	
	
	
	
	
	#-----------------------------------------------------------------
	# Shortcode TinyMCE Buttons
	#-----------------------------------------------------------------
	
	/**
	* two_column_shortcode_add_tinymce_button
	* 
	* Harnesses prso core plugin wp action to output script required to
	* add a custom tinymce button to output two_columns shortcode into tinymce
	* 
	* @access 	public
	* @author	Ben Moody
	*/
	function two_column_shortcode_add_tinymce_button() {
		$args = array(
			'plugin_slug' 		=> 'two_columns',
			'title'				=> 'Add Content Column',
			'image'				=> '/prso-two-column-shortcode.png',
			'content'			=> array(
					'prompt' 	=> 'Column Content - Click ok to add tags',
					'default'	=> 'Add content between column tags'
			),
			'shortcode_args'	=> array(),
			'plugin_info'		=> array(
									'longname' 	=> 'Prso Two Column Content Shortcode'
								),
			'file_path'			=> get_stylesheet_directory() . '/javascripts/tinymce-plugins/prso-two-column-shortcode.js',
			'file_url'			=> get_stylesheet_directory_uri() . '/javascripts/tinymce-plugins/prso-two-column-shortcode.js'
		);
		
		//Call custom action to create plugin javascript file
		do_action( 'prso_core_create_tiny_mce_plugin', $args );
		
	}
	
	/**
	* panel_shortcode_add_tinymce_button
	* 
	* Harnesses prso core plugin wp action to output script required to
	* add a custom tinymce button to output panel shortcode into tinymce
	* 
	* @access 	public
	* @author	Ben Moody
	*/
	function panel_shortcode_add_tinymce_button() {
		$args = array(
			'plugin_slug' 		=> 'panel',
			'title'				=> 'Add content panel',
			'image'				=> '/prso-panel-shortcode.png',
			'content'			=> array(
					'prompt' 	=> 'Panel Content - Click ok to add tags',
					'default'	=> 'Add content between panel tags'
			),
			'shortcode_args'	=> array(
				'type'	=> array(
					'slug' 		=> 'type',
					'prompt'	=> 'Type: (default, callout)',
					'default'	=> 'default'
				),
				'radius'	=> array(
					'slug' 		=> 'radius',
					'prompt'	=> 'Type: (default, round)',
					'default'	=> 'default'
				)
			),
			'plugin_info'		=> array(
									'longname' 	=> 'Prso Content Box Shortcode'
								),
			'file_path'			=> get_stylesheet_directory() . '/javascripts/tinymce-plugins/prso-panel-shortcode.js',
			'file_url'			=> get_stylesheet_directory_uri() . '/javascripts/tinymce-plugins/prso-panel-shortcode.js'
		);
		
		//Call custom action to create plugin javascript file
		do_action( 'prso_core_create_tiny_mce_plugin', $args );
		
	}
	
	/**
	* accordion_shortcode_add_tinymce_button
	* 
	* Harnesses prso core plugin wp action to output script required to
	* add a custom tinymce button to output accordion shortcode into tinymce
	* 
	* @access 	public
	* @author	Ben Moody
	*/
	function accordion_shortcode_add_tinymce_button() {
		$args = array(
			'plugin_slug' 		=> 'accordion',
			'title'				=> 'Add Accordion Content',
			'image'				=> '/prso-accordion-shortcode.png',
			'content'			=> array(
					'prompt' 	=> 'Accordion - Click ok to add tags',
					'default'	=> "[accordion_row title='' active='']Add content between row tags. Keep rows within Accordion parent tag[/accordion_row]"
			),
			'shortcode_args'	=> array(
			),
			'plugin_info'		=> array(
									'longname' 	=> 'Prso Accordion Content Shortcode'
								),
			'file_path'			=> get_stylesheet_directory() . '/javascripts/tinymce-plugins/prso-accordion-shortcode.js',
			'file_url'			=> get_stylesheet_directory_uri() . '/javascripts/tinymce-plugins/prso-accordion-shortcode.js'
		);
		
		//Call custom action to create plugin javascript file
		do_action( 'prso_core_create_tiny_mce_plugin', $args );
		
	}
	
	/**
	* button_shortcode_add_tinymce_button
	* 
	* Harnesses prso core plugin wp action to output script required to
	* add a custom tinymce button to output button shortcode into tinymce
	* 
	* @access 	public
	* @author	Ben Moody
	*/
	function button_shortcode_add_tinymce_button() {
		$args = array(
			'plugin_slug' 		=> 'button',
			'title'				=> 'Add Button',
			'image'				=> '/prso-button-shortcode.png',
			'content'			=> array(
					'prompt' 	=> 'Button - Click ok to add tags',
					'default'	=> "Button Text Here"
			),
			'shortcode_args'	=> array(
				'type'	=> array(
					'slug' 		=> 'type',
					'prompt'	=> 'Type: (default, secondary, success, alert)',
					'default'	=> 'default'
				),
				'size'	=> array(
					'slug' 		=> 'size',
					'prompt'	=> 'Type: (tiny, small, medium, large)',
					'default'	=> 'medium'
				),
				'style'	=> array(
					'slug' 		=> 'style',
					'prompt'	=> 'Type: (square, radius, round)',
					'default'	=> 'square'
				),
				'url'	=> array(
					'slug' 		=> 'url',
					'prompt'	=> 'Link URL',
					'default'	=> 'http://'
				)
			),
			'plugin_info'		=> array(
									'longname' 	=> 'Prso Call to Action Button Shortcode'
								),
			'file_path'			=> get_stylesheet_directory() . '/javascripts/tinymce-plugins/prso-button-shortcode.js',
			'file_url'			=> get_stylesheet_directory_uri() . '/javascripts/tinymce-plugins/prso-button-shortcode.js'
		);
		
		//Call custom action to create plugin javascript file
		do_action( 'prso_core_create_tiny_mce_plugin', $args );
		
	}
	
	/**
	* video_shortcode_add_tinymce_button
	* 
	* Harnesses prso core plugin wp action to output script required to
	* add a custom tinymce button to output video shortcode into tinymce
	* 
	* @access 	public
	* @author	Ben Moody
	*/
	function video_shortcode_add_tinymce_button() {
		$args = array(
			'plugin_slug' 		=> 'video',
			'title'				=> 'Add Video',
			'image'				=> '/prso-video-shortcode.png',
			'content'			=> array(
					'prompt' 	=> 'Video - Click ok to add tags',
					'default'	=> "Video ID Here"
			),
			'shortcode_args'	=> array(
				'service'	=> array(
					'slug' 		=> 'service',
					'prompt'	=> 'Type: (youtube, vimeo)',
					'default'	=> 'youtube'
				),
				'aspect'	=> array(
					'slug' 		=> 'aspect',
					'prompt'	=> 'Type: (widescreen, normal)',
					'default'	=> 'widescreen'
				),
				'width'	=> array(
					'slug' 		=> 'width',
					'prompt'	=> 'Video Width',
					'default'	=> '560'
				),
				'height'	=> array(
					'slug' 		=> 'height',
					'prompt'	=> 'Video Height',
					'default'	=> '315'
				)
			),
			'plugin_info'		=> array(
									'longname' 	=> 'Prso Video Shortcode'
								),
			'file_path'			=> get_stylesheet_directory() . '/javascripts/tinymce-plugins/prso-video-shortcode.js',
			'file_url'			=> get_stylesheet_directory_uri() . '/javascripts/tinymce-plugins/prso-video-shortcode.js'
		);
		
		//Call custom action to create plugin javascript file
		do_action( 'prso_core_create_tiny_mce_plugin', $args );
		
	}
	
}