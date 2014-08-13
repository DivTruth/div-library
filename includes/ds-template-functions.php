<?php
/**
 * Div Starter Template Functions
 * Functions for the templating system.
 *
 * @version     1.0
 * @package 	DivStarter/Functions
 * @category 	Core
 * @author 		Div Blend Team
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Output generator tag to aid debugging.
 *
 * @access public
 * @return void
 */
function ds_generator_tag( $gen, $type ) {
	switch ( $type ) {
		case 'html':
			$gen .= "\n" . '<meta name="generator" content="DivStarter ' . esc_attr( DS_VERSION ) . '">';
			break;
		case 'xhtml':
			$gen .= "\n" . '<meta name="generator" content="DivStarter ' . esc_attr( DS_VERSION ) . '" />';
			break;
	}
	return $gen;
}

/** Global ****************************************************************/

if ( ! function_exists( 'divstarter_output_content_wrapper' ) ) {

	/**
	 * Output the start of the page wrapper.
	 *
	 * @access public
	 * @return void
	 */
	function divstarter_output_content_wrapper() {
		wc_get_template( 'global/wrapper-start.php' );
	}
}

if ( ! function_exists( 'divstarter_output_content_wrapper_end' ) ) {

	/**
	 * Output the end of the page wrapper.
	 *
	 * @access public
	 * @return void
	 */
	function divstarter_output_content_wrapper_end() {
		wc_get_template( 'global/wrapper-end.php' );
	}
}

if ( ! function_exists( 'divstarter_get_sidebar' ) ) {

	/**
	 * Get the shop sidebar template.
	 *
	 * @access public
	 * @return void
	 */
	function divstarter_get_sidebar() {
		ds_get_template( 'global/sidebar.php' );
	}
}


/** Template Functions********************************************************/

if ( ! function_exists( 'ds_header_logo' ) ) {
	/**
	 * Handle redirects before content is output - hooked into template_redirect so is_page works.
	 * TODO: This will grab a theme settings option for the header logo img
	 *
	 * @author Nick Worth
	 * @param string $title
	 * @param string $image
	 * @return void
	 */
	function ds_header_logo($title,$image="/images/header-logo.png"){
	  echo '<div class="header-logo">
	    <img style="float:left;margin:5px;" src="'.get_stylesheet_directory_uri().$images.'"/>
	    <h1 style="float:left;margin-top: 27px;color:#fff;">'.$title.'</h1>
	  </div>';
	}
	add_action( 'ds_header_logo', 'ds_header_logo'); 
}

if ( ! function_exists( 'ds_copyright' ) ) {
	/**
	 * SITE COPYRIGHT
	 * TODO: This will grab a theme settings option for the copyright text
	 *
	 * @author Nick Worth
	 * @return string $copyright
	 */
	function ds_copyright(){
	  $copyright = '&copy; '.date('Y').' '.get_bloginfo('name').' All Rights Reserved. <br>';
	  $copyright .= 'Site designed and developed by <a href="http://www.divtruth.com" target="_blank">Div Truth LLC</a>.</p>';
	  return $copyright;
	}
	add_action('ds_copyright','ds_copyright');
}

/** Images ******************************************************************/

if ( ! function_exists( 'ds_get_attachment_id' ) ) {

	/**
	 * Pulls an attachment ID from a post, if one exists
	 *
	 * @author Nick Worth
	 * @access public
	 * @subpackage	Media
	 * @param number $num
	 * @return number
	 */
	function ds_get_attachment_id($num = 0) {
	  global $post;

	  $image_ids = array_keys(
	    get_children(
	      array(
	        'post_parent' => $post->ID,
	        'post_type' => 'attachment',
	        'post_mime_type' => 'image',
	        'orderby' => 'menu_order',
	        'order' => 'ASC'
	      )
	    )
	  );

	  if ( isset($image_ids[$num]) )
	    return $image_ids[$num];

	  return false;
	}
}

if ( ! function_exists( 'ds_get_image' ) ) {

	/**
	 * Pulls an image from the media gallery and returns it
	 *
	 * @author Nick Worth
	 * @access public
	 * @subpackage	Media
	 * @param array $args
	 * @return number
	 */
	function ds_get_image($args = array()) {
	  global $post;

	  $defaults = array(
	    'format' => 'html',
	    'size' => 'full',
	    'num' => 0,
	    'attr' => ''
	  );
	  $defaults = apply_filters('ds_get_image_default_args', $defaults);

	  $args = wp_parse_args($args, $defaults);

	  // Allow child theme to short-circuit this function
	  $pre = apply_filters('ds_pre_get_image', false, $args, $post);
	  if ( false !== $pre ) return $pre;

	  // Check for post image (native WP)
	  if ( has_post_thumbnail() && ($args['num'] === 0) ) {
	    $id = get_post_thumbnail_id();
	    $html = wp_get_attachment_image($id, $args['size'], false, $args['attr']);
	    list($url) = wp_get_attachment_image_src($id, $args['size'], false, $args['attr']);
	  }
	  // else pull the first image attachment
	  else {
	    $id = ds_get_attachment_id($args['num']);
	    $html = wp_get_attachment_image($id, $args['size'], false, $args['attr']);
	    list($url) = wp_get_attachment_image_src($id, $args['size'], false, $args['attr']);
	  }

	  // source path, relative to the root
	  $src = str_replace( home_url(), '', $url );

	  // determine output
	  if ( strtolower($args['format']) == 'html' )
	    $output = $html;
	  elseif ( strtolower($args['format']) == 'url' )
	    $output = $url;
	  else
	    $output = $src;

	  // return FALSE if $url is blank
	  if ( empty($url) ) $output = FALSE;

	  // return FALSE if $src is invalid (file doesn't exist)
	  if ( !file_exists(ABSPATH . $src) ) $output = FALSE;

	  // return data, filtered
	  return apply_filters('ds_get_image', $output, $args, $id, $html, $url, $src);
	}
}

if ( ! function_exists( 'ds_image' ) ) {
	/**
	 * Echo ds_get_image();
	 *
	 * @author Nick Worth
	 * @access public
	 * @subpackage	Media
	 * @param array $args
	 * @return number
	 */
	function ds_image($args = array()) {
		$image = ds_get_image($args);

		if ( $image ) echo $image;
		else return FALSE;
	}
}

if ( ! function_exists( 'ds_get_additional_image_sizes' ) ) {
	/**
	 * GET ADDITIONAL IMAGE SIZES
	 *
	 * Returns a two-dimensional array of just the additionally registered image
	 * sizes, with width, height and crop sub-keys.
	 *
	 * @author Nick Worth
	 * @global array $_wp_additional_image_sizes Additionally registered image sizes
	 * @return array Two-dimensional, with width, height and crop sub-keys
	 */
	function ds_get_additional_image_sizes() {
	  global $_wp_additional_image_sizes;

	  if ( $_wp_additional_image_sizes )
	    return $_wp_additional_image_sizes;

	  return array();
	}
}

if ( ! function_exists( 'ds_get_image_sizes' ) ) {
	/**
	 * GET ALL IMAGE SIZES
	 *
	 * Returns a two-dimensional array of ALL registered image
	 * sizes, with width, height and crop sub-keys.
	 *
	 * @author Nick Worth
	 * @global array $_wp_additional_image_sizes
	 * @return array
	 */
	function ds_get_image_sizes() {
		$builtin_sizes = array(
			'large'   => array(
				'width' => get_option('large_size_w'),
				'height' => get_option('large_size_h')
			),
			'medium'  => array(
		  		'width' => get_option('medium_size_w'),
		  		'height' => get_option('medium_size_h')
			),
			'thumbnail' => array(
		  		'width' => get_option('thumbnail_size_w'),
		  		'height' => get_option('thumbnail_size_h')
			)
		);

		$additional_sizes = ds_get_additional_image_sizes();

		return array_merge( $builtin_sizes, $additional_sizes );
	}
}

/** Loop ******************************************************************/

	// Loop function to go here

/** Single Product ********************************************************/

	// Single function to go here

/** Login *****************************************************************/

if ( ! function_exists( 'ds_login_form' ) ) {

	/**
	 * Output the DivStarter Login Form
	 *
	 * @access public
	 * @subpackage	Forms
	 * @return void
	 */
	function ds_login_form( $args = array() ) {

		$defaults = array(
			'message'  => '',
			'redirect' => '',
			'hidden'   => false
		);

		$args = wp_parse_args( $args, $defaults  );

		ds_get_template( 'global/form-login.php', $args );
	}
}

/** Forms ****************************************************************/

	// Form functions to go here