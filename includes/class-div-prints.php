<?php
/**
 * DIV_Prints class.
 * Used to print out settings, shortcodes, enqued scripts & styles, etc.
 *
 * @class 		DIV_Prints
 * @version		1.0
 * @package		DivStarter/Classes
 * @category	Class
 * @author 		Div Truth
 */

if( ! defined( 'ABSPATH' ) ) exit;

class DIV_Prints {

	/**
	 * Constructor.
	 * @access public
	 */
	public function __construct() {
		
	}

	/**
	 * Print all available shortcodes
	 *
	 * @return string
	 */
	public static function shortcodes(){
		global $shortcode_tags;
		echo "<pre>"; print_r($shortcode_tags); echo "</pre>";
	}

}