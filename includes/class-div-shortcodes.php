<?php
/**
 * DIV_Shortcodes class.
 * Shortcodes will be added here by their respectice classes
 *
 * @class 		DIV_Shortcodes
 * @version		1.0
 * @package		div_library/Classes
 * @category	Class
 * @author 		Div Blend Team
 */

if( ! defined( 'ABSPATH' ) ) exit;

class DIV_Shortcodes {

	/**
	 * Shortcode Wrapper
	 *
	 * @param mixed $function
	 * @param array $atts (default: array())
	 * @return string
	 */
	public static function shortcode_wrapper(
		$function,
		$atts    = array(),
		$wrapper = array(
			'class'  => 'divlibrary shortcode',
			'before' => null,
			'after'  => null
		)
	) {
		ob_start();

		$before 	= empty( $wrapper['before'] ) ? '<div class="' . esc_attr( $wrapper['class'] ) . '">' : $wrapper['before'];
		$after 		= empty( $wrapper['after'] ) ? '</div>' : $wrapper['after'];

		echo $before;
		call_user_func( $function, $atts );
		echo $after;

		return ob_get_clean();
	}

}