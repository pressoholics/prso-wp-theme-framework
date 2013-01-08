<?php

class PrsoThemeAppController extends PrsoThemeConfig {

	protected $data = array(); //Master store of all data for plugin actions - options data, _GET, Overload data from magic methods
	
	function __construct() {
		 		
	}
	
	/**
	* get_options
	* 
	* Little helper to get data from the wordpress options database table
	* and cache it for later use.
	* 
	* @access 	protected
	* @author	Ben Moody
	*/
	protected function get_options( $option_slug = NULL, $multi_array_slug = NULL ) {
		
		//Init vars
		$option_data = array();
		$temp_data = array();
		
		if(isset( $option_slug )) {
			
			//Call custom wp filter '' to get options data, pass $option_slug, $multi_array_slug
			$option_data = apply_filters( 'prso_core_get_options', $option_data, $option_slug, $multi_array_slug );
			
			$this->data[ $option_slug ] = $option_data;
			return true;
			
		}
		
		return false;
	}
	
	/**
	* get_slug
	* 
	* Little helper to prepend any slug vars with the framework slug constant PRSO_SLUG
	* helps to avoid any name conflicts with options slugs
	* 
	* @access 	protected
	* @author	Ben Moody
	*/
 	protected function get_slug( $var = NULL ) {
 		
 		//Init vars
 		$slug = false;
 		
 		if(isset( $this->theme_slug, $var )) {
 			//Pass wordpress filter 'prso_core_get_slug' plugin_slug, $var, and Current Object $this
 			$slug = apply_filters( 'prso_core_get_slug', $slug, $var, $this->theme_slug, $this  );	
 		}
 		
 		return $slug;
 	}
	
	//Magic methods set and get
	public function __set( $name, $value ) {
		if( isset($this->data) ) {
			$this->data[$name] = $value;
		}
	}
	
	public function __get( $name ) {
		if( isset($this->data) && array_key_exists( $name, $this->data ) ) {
			return $this->data[$name];
		}
		return null;
	}
	
}