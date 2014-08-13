<?php
/**
 * Div Starter Template Hooks
 * Action/filter hooks used for Div Starter functions/templates
 *
 * @version     1.0
 * @package 	DivStarter/Templates
 * @category 	Core
 * @author 		Div Blend Team
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/** 
 * WP Header
 *
 * @see  ds_generator_tag()
 */
add_action( 'get_the_generator_html', 'ds_generator_tag', 10, 2 );
add_action( 'get_the_generator_xhtml', 'ds_generator_tag', 10, 2 );

/**
 * Content Wrappers
 *
 * @see divstarter_output_content_wrapper()
 * @see divstarter_output_content_wrapper_end()
 */
add_action( 'divstarter_before_main_content', 'divstarter_output_content_wrapper', 10 );
add_action( 'divstarter_after_main_content', 'divstarter_output_content_wrapper_end', 10 );

/**
 * Breadcrumbs
 *
 * @see divstarter_breadcrumb()
 */
add_action( 'divstarter_before_main_content', 'divstarter_breadcrumb', 20, 0 );

/**
 * Sidebar
 *
 * @see divstarter_get_sidebar()
 */
add_action( 'divstarter_sidebar', 'divstarter_get_sidebar', 10 );

/**
 * Footer
 *
 * @see  ds_print_js()
 */
add_action( 'wp_footer', 'ds_print_js', 99 );