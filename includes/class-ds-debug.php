<?php
/**
 * DS_Debug class
 * General class for debugging during development
 *
 * @class       DS_Debug
 * @version     1.0
 * @package     DivStarter/Classes
 * @category    Class
 * @author      Div Blend Team
 */

if( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'DS_Debug' ) ) :

class DS_Debug{

    /**
     * @var DS_Debug the single instance of the class
     * @since 1.0
     */
    protected static $_instance = null;

    function __construct() {}

    /**
     * Main DS_Debug Instance (singleton)
     *
     * Ensures only one instance of DS_Debug is loaded or can be loaded.
     *
     * @since 1.0
     * @static
     * @see ds_debug()
     * @return DS_Debug
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Print all enqued scripts
     *
     * @return string
     * @since 1.0
     *
     */
    public static function print_scripts($hook='wp_print_scripts') {
        add_action( $hook, function(){
            global $wp_scripts;
            foreach ( $wp_scripts->registered as $registered )
                $script_urls[ $registered->handle ] = $registered->src;

            echo '<ol>';
                foreach( $wp_scripts->queue as $handle ) :
                    echo '<li><strong>'.$handle . '</strong>: <em>'. $script_urls[ $handle ].'</em></li>';
                endforeach;
            echo '</ol>';
        } );
    }


    /**
     * Print all enqued styles
     *
     * @return string
     * @since 1.0
     *
     */
    public static function print_styles($hook='wp_print_scripts') {
        add_action( $hook, function(){
            global $wp_styles;
            foreach ( $wp_styles->registered as $registered )
                $style_urls[ $registered->handle ] = $registered->src;

            echo '<ol>';
                foreach( $wp_styles->queue as $handle ) :
                    echo '<li><strong>'.$handle . '</strong>: <em>'. $style_urls[ $handle ].'</em></li>';
                endforeach;
            echo '</ol>';
        } );
    }

}

endif;

/**
 * Returns the main instance of DS_Debug to prevent the need to use globals.
 *
 * @since  1.0
 * @return DivStarter
 */
function ds_debug() {
    return DS_Debug::instance();
}

ds_debug();
?>