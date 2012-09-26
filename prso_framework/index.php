<?php
/**
 * Index
 *
 * The Front Controller for loading the Pressoholics theme framwork
 *
 * PHP versions 4 and 5
 *
 * @copyright     Pressoholics (http://pressoholics.com)
 * @link          http://pressoholics.com
 * @package       pressoholics theme framework
 * @since         Pressoholics v 0.1
 */
	
/**
* Include config file to set core definitions
*
*/
if( file_exists( dirname(__FILE__) . '/config.php' ) ) {
	include( dirname(__FILE__) . '/config.php' );
}

/**
* Call method to boot framework
*
*/
if( file_exists( dirname(__FILE__) . '/bootstrap.php' ) ) {
	
	include( dirname(__FILE__) . '/bootstrap.php' );
	
	//Instantiate bootstrap class
	if( class_exists('PrsoThemeBootstrap') ) {
		$PrsoBoot = new PrsoThemeBootstrap();
		$PrsoBoot->boot();
	}
	
}