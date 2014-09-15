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
	 * Init shortcodes
	 */
	public static function init() {
		global $shortcode_tags;

		// Define shortcodes
		$shortcodes = array(
			'chart'		=> __CLASS__ . '::chart',
			'download'	=> __CLASS__ . '::download',
			'pdf'		=> __CLASS__ . '::pdf',
		);

		foreach ( $shortcodes as $shortcode => $function ) {
			if ( !array_key_exists( $shortcode, $shortcode_tags ) )
			add_shortcode( apply_filters( "{$shortcode}_shortcode_tag", $shortcode ), $function );
		}
	}

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