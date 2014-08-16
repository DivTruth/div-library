<?php
/**
 * DS_Print class
 * Used to print out settings, shortcodes, enqued scripts & styles, etc.
 *
 * @class       DS_Print
 * @version     1.0
 * @package     DivStarter/Classes
 * @category    Class
 * @author      Div Blend Team
 */

if( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'DS_Print' ) ) :

class DS_Print{

    function __construct() {}

    /**
     * Print all registered or enqued scripts
     *
     * @param boolean (enqueued)
     * @return string
     * @since 1.0
     *
     */
    public static function scripts($enqueued=false) {
        global $wp_scripts;
        $status = ($enqueued) ? "queue" : "registered";
        echo "<pre>"; print_r($wp_scripts->$status); echo "</pre>";
    }

    /**
     * Print all registered or enqued styles
     *
     * @param boolean (enqueued)
     * @return string
     * @since 1.0
     *
     */
    public static function styles($enqueued=false) {
        global $wp_styles;
        $status = ($enqueued) ? "queue" : "registered";
        echo "<pre>"; print_r($wp_styles->$status); echo "</pre>";
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

    /**
     * Print object
     *
     * @param object or array
     * @return string
     */
    public static function object($array){
        echo "<pre>"; print_r($array); echo "</pre>";
    }

}

endif;

?>