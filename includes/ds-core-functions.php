<?php
/**
 * Div Starter Core Functions
 * General core functions available on both the front-end and admin.
 *
 * @version     1.0
 * @package 	DivStarter/Functions
 * @category 	Core
 * @author 		Div Blend Team
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

# Include core functions
// include( 'ds-scope-functions.php' );

# Filters on data used in admin and frontend
// add_filter( 'ds_custom_filter', 'sanitize_text_field' );


/**
 * Get template part (for templates like the shop-loop).
 *
 * @access public
 * @param mixed $slug
 * @param string $name (default: '')
 * @return void
 */
function ds_get_template_part( $slug, $name = '' ) {
	$template = '';

	# First Look: yourtheme/slug-name.php
	# Second Look: yourtheme/starter/slug-name.php
	if ( $name ) {
		$template = locate_template( array( "{$slug}-{$name}.php", DS()->template_path() . "{$slug}-{$name}.php" ) );
	}

	# Get default slug-name.php
	if ( ! $template && $name && file_exists( DS()->plugin_path() . "/templates/{$slug}-{$name}.php" ) ) {
		$template = DS()->plugin_path() . "/templates/{$slug}-{$name}.php";
	}

	# If template file doesn't exist, look in yourtheme/slug.php and yourtheme/divstarter/slug.php
	if ( ! $template ) {
		$template = locate_template( array( "{$slug}.php", DS()->template_path() . "{$slug}.php" ) );
	}

	# Allow 3rd party plugin filter template file from their plugin
	$template = apply_filters( 'ds_get_template_part', $template, $slug, $name );

	if ( $template ) {
		load_template( $template, false );
	}
}

/**
 * Get other templates (e.g. product attributes) passing attributes and including the file.
 *
 * @access public
 * @param string $template_name
 * @param array $args (default: array())
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 * @return void
 */
function ds_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	if ( $args && is_array( $args ) ) {
		extract( $args );
	}

	$located = ds_locate_template( $template_name, $template_path, $default_path );

	if ( ! file_exists( $located ) ) {
		_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $located ), '2.1' );
		return;
	}

	do_action( 'divstarter_before_template_part', $template_name, $template_path, $located, $args );

	include( $located );

	do_action( 'divstarter_after_template_part', $template_name, $template_path, $located, $args );
}

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
function ds_locate_template( $template_name, $template_path = '', $default_path = '' ) {
	if ( ! $template_path ) {
		$template_path = DS()->template_path();
	}

	if ( ! $default_path ) {
		$default_path = DS()->plugin_path() . '/templates/';
	}

	# Look within passed path within the theme - this is priority
	$template = locate_template(
		array(
			trailingslashit( $template_path ) . $template_name,
			$template_name
		)
	);

	# Get default template
	if ( ! $template ) {
		$template = $default_path . $template_name;
	}

	# Return what we found
	return apply_filters('divstarter_locate_template', $template, $template_name, $template_path);
}

/**
 * Queue some JavaScript code to be output in the footer.
 *
 * @param string $code
 */
function ds_enqueue_js( $code ) {
	global $ds_queued_js;

	if ( empty( $ds_queued_js ) ) {
		$ds_queued_js = '';
	}

	$ds_queued_js .= "\n" . $code . "\n";
}

/**
 * Output any queued javascript code in the footer.
 *
 * @global $ds_queued_js
 * @usedby wp_footer
 */
function ds_print_js() {
	global $ds_queued_js;

	if ( ! empty( $ds_queued_js ) ) {

		echo "<!-- DivStarter JavaScript -->\n<script type=\"text/javascript\">\njQuery(function($) {";

		// Sanitize
		$ds_queued_js = wp_check_invalid_utf8( $ds_queued_js );
		$ds_queued_js = preg_replace( '/&#(x)?0*(?(1)27|39);?/i', "'", $ds_queued_js );
		$ds_queued_js = str_replace( "\r", '', $ds_queued_js );

		echo $ds_queued_js . "});\n</script>\n";

		unset( $ds_queued_js );
	}
}
