<?php
/**
 * Div Library Core Functions
 * General core functions available on both the front-end and admin.
 *
 * @version     1.0
 * @package 	div_library/Functions
 * @category 	Core
 * @author 		Div Blend Team
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/** Templates ******************************************************************/

if ( ! function_exists( 'div_get_template_part' ) ) {
	/**
	 * Get template part (for templates like the shop-loop).
	 *
	 * @access public
	 * @param mixed $slug
	 * @param string $name (default: '')
	 * @return void
	 */
	function div_get_template_part( $slug, $name = '' ) {
		$template = '';

		# First Look: yourtheme/slug-name.php
		# Second Look: yourtheme/starter/slug-name.php
		if ( $name ) {
			$template = locate_template( array( "{$slug}-{$name}.php", $library->template_path() . "{$slug}-{$name}.php" ) );
		}

		# Get default slug-name.php
		if ( ! $template && $name && file_exists( $library->plugin_path() . "/templates/{$slug}-{$name}.php" ) ) {
			$template = $library->plugin_path() . "/templates/{$slug}-{$name}.php";
		}

		# If template file doesn't exist, look in yourtheme/slug.php and yourtheme/divlibrary/slug.php
		if ( ! $template ) {
			$template = locate_template( array( "{$slug}.php", $library->template_path() . "{$slug}.php" ) );
		}

		# Allow 3rd party plugin filter template file from their plugin
		$template = apply_filters( 'div_get_template_part', $template, $slug, $name );

		if ( $template ) {
			load_template( $template, false );
		}
	}
}

if ( ! function_exists( 'div_get_template' ) ) {
	/**
	 * Get other templates passing attributes and including the file.
	 *
	 * @access public
	 * @param string $template_name
	 * @param array $args (default: array())
	 * @param string $template_path (default: '')
	 * @param string $default_path (default: '')
	 * @return void
	 */
	function div_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
		if ( $args && is_array( $args ) ) {
			extract( $args );
		}

		$located = div_locate_template( $template_name, $template_path, $default_path );

		if ( ! file_exists( $located ) ) {
			_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $located ), '2.1' );
			return;
		}

		do_action( 'divlibrary_before_template_part', $template_name, $template_path, $located, $args );

		include( $located );

		do_action( 'divlibrary_after_template_part', $template_name, $template_path, $located, $args );
	}
}

if ( ! function_exists( 'div_locate_template' ) ) {
	/**
	 * Locate a template and return the path for inclusion.
	 *
	 * This is the load order:
	 *
	 *		yourtheme		/	$template_path	/	$template_name
	 *		yourtheme		/	$template_name
	 *		$default_path	/	$template_name
	 *
	 * @access public
	 * @param string $template_name
	 * @param string $template_path (default: '')
	 * @param string $default_path (default: '')
	 * @return string
	 */
	function div_locate_template( $template_name, $template_path = '', $default_path = '' ) {
		if ( ! $default_path ) {
			$default_path = '/templates/';
		}

		# Look within passed path within the theme - this is priority
		$template = locate_template(
			array(
				trailingslashit( $template_path ) . $template_name, # Checks theme/template-path/file-name.php
				$template_name										# Checks theme/file-name.php
			)
		);

		# If no template in theme structure, then fetch from source (application/plugin)
		if ( ! $template ) {
			$template = $default_path . $template_name;
		}

		# Return what we found
		return apply_filters('divlibrary_locate_template', $template, $template_name, $template_path);
	}
}

/** Scripts/Styles ******************************************************************/

if ( ! function_exists( 'div_enqueue_js' ) ) {
	/**
	 * Queue some JavaScript code to be output in the footer.
	 *
	 * @param string $code
	 */
	function div_enqueue_js( $code ) {
		global $div_queued_js;

		if ( empty( $div_queued_js ) ) {
			$div_queued_js = '';
		}

		$div_queued_js .= "\n" . $code . "\n";
	}
}

if ( ! function_exists( 'div_print_js' ) ) {
	/**
	 * Output any queued javascript code in the footer.
	 *
	 * @since   1.0
	 * @global 	$div_queued_js
	 * @usedby 	wp_footer
	 */
	function div_print_js() {
		global $div_queued_js;

		if ( ! empty( $div_queued_js ) ) {

			echo "<!-- Div Library JavaScript -->\n<script type=\"text/javascript\">\njQuery(function($) {";

			// Sanitize
			$div_queued_js = wp_check_invalid_utf8( $div_queued_js );
			$div_queued_js = preg_replace( '/&#(x)?0*(?(1)27|39);?/i', "'", $div_queued_js );
			$div_queued_js = str_replace( "\r", '', $div_queued_js );

			echo $div_queued_js . "});\n</script>\n";

			unset( $div_queued_js );
		}
	}
}
add_action( 'wp_footer', 'div_print_js', 99 );

/** Images ******************************************************************/

if ( ! function_exists( 'div_get_attachment_id' ) ) {

	/**
	 * Pulls an attachment ID from a post, if one exists
	 *
	 * @author Nick Worth
	 * @access public
	 * @subpackage	Media
	 * @param number $num
	 * @return number
	 */
	function div_get_attachment_id($num = 0) {
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

if ( ! function_exists( 'div_get_image' ) ) {

	/**
	 * Pulls an image from the media gallery and returns it
	 *
	 * @author Nick Worth
	 * @access public
	 * @subpackage	Media
	 * @param array $args
	 * @return number
	 */
	function div_get_image($args = array()) {
	  global $post;

	  $defaults = array(
	    'format' => 'html',
	    'size' => 'full',
	    'num' => 0,
	    'attr' => ''
	  );
	  $defaults = apply_filters('div_get_image_default_args', $defaults);

	  $args = wp_parse_args($args, $defaults);

	  // Allow child theme to short-circuit this function
	  $pre = apply_filters('div_pre_get_image', false, $args, $post);
	  if ( false !== $pre ) return $pre;

	  // Check for post image (native WP)
	  if ( has_post_thumbnail() && ($args['num'] === 0) ) {
	    $id = get_post_thumbnail_id();
	    $html = wp_get_attachment_image($id, $args['size'], false, $args['attr']);
	    list($url) = wp_get_attachment_image_src($id, $args['size'], false, $args['attr']);
	  }
	  // else pull the first image attachment
	  else {
	    $id = div_get_attachment_id($args['num']);
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
	  return apply_filters('div_get_image', $output, $args, $id, $html, $url, $src);
	}
}

if ( ! function_exists( 'div_image' ) ) {
	/**
	 * Echo div_get_image();
	 *
	 * @author Nick Worth
	 * @access public
	 * @subpackage	Media
	 * @param array $args
	 * @return number
	 */
	function div_image($args = array()) {
		$image = div_get_image($args);

		if ( $image ) echo $image;
		else return FALSE;
	}
}

if ( ! function_exists( 'div_get_additional_image_sizes' ) ) {
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
	function div_get_additional_image_sizes() {
	  global $_wp_additional_image_sizes;

	  if ( $_wp_additional_image_sizes )
	    return $_wp_additional_image_sizes;

	  return array();
	}
}

if ( ! function_exists( 'div_get_image_sizes' ) ) {
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
	function div_get_image_sizes() {
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

		$additional_sizes = div_get_additional_image_sizes();

		return array_merge( $builtin_sizes, $additional_sizes );
	}
}

if ( ! function_exists( 'div_is_child' ) ) {
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
	function div_is_child($page) { 
		global $post;
		$page_ID = ( is_string($page) ) ? div_get_ID_by_slug($page) : $page;
		if( is_page() && ($post->post_parent==$page_ID) ) {
	        return true;
		} else { 
	        return false; 
		}
	}
}

if ( ! function_exists( 'div_get_ID_by_slug' ) ) {
	/**
	 * GET PAGE ID BY SLUG
	 *
	 * Returns the ID of the page by passing the slug
	 *
	 * @author Nick Worth
	 * @param string $page_slug
	 * @return number
	 */
	function div_get_ID_by_slug($page_slug) { 
		$page = get_page_by_path($page_slug);
	    if ($page) {
	        return $page->ID;
	    } else {
	        return null;
	    }
	}
}