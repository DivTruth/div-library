<?php
/**
 * Div Starter Taxonomy Class
 * Creates custom taxonomies
 *
 * @class       DIV_Taxonomy
 * @author      Gijs Jorissen
 * @category    Core
 * @package     DivStarter/Classes
 * @uses        DIV_Helper
 * @version     1.0
 */

if( ! defined( 'ABSPATH' ) ) exit;

class DIV_Taxonomy{
    var $taxonomy_name;
    var $taxonomy_labels;
    var $taxonomy_args;
    var $post_type_name;

    /**
     * Constructs the class with important vars and method calls
     * If the taxonomy exists, it will be attached to the post type
     *
     * @param string $name
     * @param string $post_type_name
     * @param array $args
     * @param array $labels
     *
     * @author Gijs Jorissen
     * @since 1.0
     *
     */
    function __construct( $name, $post_type_name = null, $args = array(), $labels = array() ) {
        if( ! empty( $name ) )
        {
            $this->post_type_name = $post_type_name;

            // Taxonomy properties
            $this->taxonomy_name        = DIV_Helper::uglify( $name );
            $this->taxonomy_labels      = $labels;
            $this->taxonomy_args        = $args;

            if( ! taxonomy_exists( $this->taxonomy_name ) )
            {
                add_action( 'init', array( &$this, 'register_taxonomy' ) );
            }
            else
            {
                add_action( 'init', array( &$this, 'register_taxonomy_for_object_type' ) );
            }
        }
    }


    /**
     * Registers the custom taxonomy with the given arguments
     *
     * @author Gijs Jorissen
     * @since 1.0
     *
     */
    function register_taxonomy() {
        $name       = DIV_Helper::beautify( $this->taxonomy_name );
        $plural     = DIV_Helper::pluralize( $name );

        // Default labels, overwrite them with the given labels.
        $labels = array_merge(

            // Default
            array(
                'name'                  => _x( $plural, 'taxonomy general name', 'CUSTOM_TEXTDOMAIN' ),
                'singular_name'         => _x( $name, 'taxonomy singular name', 'CUSTOM_TEXTDOMAIN' ),
                'search_items'          => __( 'Search ' . $plural, 'CUSTOM_TEXTDOMAIN' ),
                'all_items'             => __( 'All ' . $plural, 'CUSTOM_TEXTDOMAIN' ),
                'parent_item'           => __( 'Parent ' . $name, 'CUSTOM_TEXTDOMAIN' ),
                'parent_item_colon'     => __( 'Parent ' . $name . ':', 'CUSTOM_TEXTDOMAIN' ),
                'edit_item'             => __( 'Edit ' . $name, 'CUSTOM_TEXTDOMAIN' ), 
                'update_item'           => __( 'Update ' . $name, 'CUSTOM_TEXTDOMAIN' ),
                'add_new_item'          => __( 'Add New ' . $name, 'CUSTOM_TEXTDOMAIN' ),
                'new_item_name'         => __( 'New ' . $name . ' Name', 'CUSTOM_TEXTDOMAIN' ),
                'menu_name'             => __( $name, 'CUSTOM_TEXTDOMAIN' ),
            ),

            // Given labels
            $this->taxonomy_labels

        );

        // Default arguments, overwitten with the given arguments
        $args = array_merge(

            // Default
            array(
                'label'                 => $plural,
                'labels'                => $labels,
                "hierarchical"          => true,
                'public'                => true,
                'show_ui'               => true,
                'show_in_nav_menus'     => true,
                '_builtin'              => false,
            ),

            // Given
            $this->taxonomy_args

        );

        register_taxonomy( $this->taxonomy_name, $this->post_type_name, $args );
    }


    /**
     * Used to attach the existing taxonomy to the post type
     *
     * @author Gijs Jorissen
     * @since 1.0
     *
     */
    function register_taxonomy_for_object_type() {
        register_taxonomy_for_object_type( $this->taxonomy_name, $this->post_type_name );
    }   
}

?>