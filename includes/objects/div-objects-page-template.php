<?php
/**
 * DIV page_template class
 * Register page template to WordPress
 *
 * @package     div-library/objects
 * @author      Harri Bell-Thomas (modified by Div Blend Team)
 * @link 		http://www.wpexplorer.com/wordpress-page-templates-plugin/
 */
namespace DIV\objects{

    if( ! defined( 'ABSPATH' ) ) exit;
    
    if ( ! class_exists( 'page_template' ) ) :
    
        class page_template{

        	/**
             * A Unique Identifier
             */
        	protected $plugin_slug;

            /**
             * The array of templates that this plugin tracks.
             */
            protected $templates;

            /**
             * The directory used for scanning templates files.
             */    
            protected $dir;

        	/**
        	 * Constructor
        	 * Initializes the plugin by setting filters and administration functions.
        	 *
        	 * @access private
        	 * @return void
        	 */
        	public function __construct($templates, $dir) {
                $this->dir = $dir;
                $this->templates = $templates;

                # Add a filter to the attributes metabox to inject template into the cache.
                add_filter(
        			'page_attributes_dropdown_pages_args',
        			array( $this, 'register_project_templates' ) 
        		);

                # Add a filter to the save post to inject out template into the page cache
                add_filter(
        			'wp_insert_post_data', 
        			array( $this, 'register_project_templates' ) 
        		);

                # Add a filter to the template include to determine if the page has our template assigned and return it's path
                add_filter(
        			'template_include', 
        			array( $this, 'view_project_template') 
        		);

                
            }

            /**
        	 * Register Project Templates
        	 *
        	 * @access public
        	 * @param array
        	 * @return array
        	 */
            public function register_project_templates( $atts ) {
                # Create the key used for the themes cache
                $cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

                # Retrieve the cache list. 
        		# If it doesn't exist, or it's empty prepare an array
                $templates = wp_get_theme()->get_page_templates();
                if ( empty( $templates ) ) {
                        $templates = array();
                } 

                # New cache, therefore remove the old one
                wp_cache_delete( $cache_key , 'themes');

                # Now add our template to the list of templates by merging our templates
                # with the existing templates array from the cache.
                $templates = array_merge( $templates, $this->templates );

                # Add the modified cache to allow WordPress to pick it up for listing
                # available templates
                wp_cache_add( $cache_key, $templates, 'themes', 1800 );

                return $atts;

            }

            /**
        	 * Register Project Templates
        	 * Checks if the template is assigned to the page
        	 *
        	 * @access public
        	 * @param string
        	 * @return string
        	 */
            public function view_project_template( $template ) {
                global $post;
                $id = ( isset( $post->ID ) ? get_the_ID() : NULL );
                
                if (!isset($this->templates[get_post_meta(
        			$id, '_wp_page_template', true 
        		)] ) ) {
                    return $template;
                } 

                $file = $this->dir.'\\'.get_post_meta( 
        		$id, '_wp_page_template', true 
        	);
        		
                # Just to be safe, we check if the file exist first
                if( file_exists( $file ) ) { return $file;
                } else { echo '<strong>Template not found</strong>: '.$file; }

                return $template;
            }

        }
    
    endif;

}
