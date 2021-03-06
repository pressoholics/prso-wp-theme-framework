<?php
/**
* description_walker
* 
* Add the 'has-flyout' class to any li's that have children and add the arrows to li's with children
* 
* @access 	public
* @author	Ben Moody
*/
if( !class_exists('main_nav_walker') ) {
	
	class main_nav_walker extends Walker_Nav_Menu {
	  
	      function start_el(&$output, $item, $depth = 0, $args = array(), $current_object_id = 0) {
	            global $wp_query;
	            $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
	            
	            $class_names = $value = '';
	            
	            // If the item has children, add the dropdown class for foundation
	            if ( $args->has_children && ($depth == 0) ) {
	                $class_names = "has-flyout ";
	            }
	            
	            $classes = empty( $item->classes ) ? array() : (array) $item->classes;
	            
	            $class_names .= join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
	            $class_names = ' class="'. esc_attr( $class_names ) . '"';
	           
	            $output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';
	
	            $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
	            $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
	            $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
	            $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
	            // if the item has children add these two attributes to the anchor tag
	            // if ( $args->has_children ) {
	            //     $attributes .= 'class="dropdown-toggle" data-toggle="dropdown"';
	            // }
	
	            $item_output = $args->before;
	            $item_output .= '<a'. $attributes .'>';
	            $item_output .= $args->link_before .apply_filters( 'the_title', $item->title, $item->ID );
	            $item_output .= $args->link_after;
	            // if the item has children add the caret just before closing the anchor tag
	            if ( $args->has_children && ($depth == 0) ) {
	                $item_output .= '</a><a href="#" class="flyout-toggle"><span> </span></a>';
	            }
	            else{
	                $item_output .= '</a>';
	            }
	            $item_output .= $args->after;
	
	            $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	        }
	            
	        function start_lvl(&$output, $depth = 0, $args = array()) {
	            $indent = str_repeat("\t", $depth);
	            $output .= "\n$indent<ul class=\"flyout\">\n";
	        }
	            
	        function display_element( $element, &$children_elements, $max_depth, $depth=0, $args, &$output ) {
	            $id_field = $this->db_fields['id'];
	            if ( is_object( $args[0] ) ) {
	                $args[0]->has_children = ! empty( $children_elements[$element->$id_field] );
	            }
	            return parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
	        } 
	              
	}
	
}

/**
* footer_links_walker
* 
* Walker class to customize footer links
* 
* @access 	public
* @author	Ben Moody
*/
if( !class_exists('footer_links_walker') ) {
	
	class footer_links_walker extends Walker_Nav_Menu {
	      function start_el(&$output, $item, $depth = 0, $args = array(), $current_object_id = 0)
	      {
	            global $wp_query;
	            $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
	            
	            $class_names = $value = '';
	            
	            $classes = empty( $item->classes ) ? array() : (array) $item->classes;
	            
	            $class_names .= join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
	            $class_names = ' class="'. esc_attr( $class_names ) . '"';
	           
	            $output .= $indent . '<li ' . $value . $class_names .'>';
	
	            $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
	            $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
	            $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
	            $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
	
	            $item_output = $args->before;
	            $item_output .= '<a'. $attributes .'>';
	            $item_output .= $args->link_before .apply_filters( 'the_title', $item->title, $item->ID );
	            $item_output .= $args->link_after;
	            
	            $item_output .= '</a>';
	            $item_output .= $args->after;
	
	            $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	            }
	            
	        function start_lvl(&$output, $depth = 0, $args = array()) {
	            $indent = str_repeat("\t", $depth);
	            $output .= "\n$indent<ul class=\"flyout\">\n";
	        }
	            
	        function display_element( $element, &$children_elements, $max_depth, $depth=0, $args, &$output )
	            {
	                $id_field = $this->db_fields['id'];
	                if ( is_object( $args[0] ) ) {
	                    $args[0]->has_children = ! empty( $children_elements[$element->$id_field] );
	                }
	                return parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
	            }       
	}
	
}
