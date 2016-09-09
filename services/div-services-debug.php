<?php
/**
 * DIV debug class
 * Used to print out settings, shortcodes, enqued scripts & styles, etc.
 *
 * @class       debug
 * @version     1.0
 * @package     div-library/services
 * @author      Div Blend Team
 */
namespace DIV\services{

    if( ! defined( 'ABSPATH' ) ) exit;
    
    if ( ! class_exists( 'debug' ) ) :
    
        class debug{

            /**
             * Print all registered or enqued scripts
             * @example DIV\services\debug::styles(true);
             *
             * @param      boolean  $enqueued
             */
            public static function scripts($enqueued=false) {
                global $wp_scripts;
                $status = ($enqueued) ? "queue" : "registered";
                if(!IS_NULL($wp_scripts)) self::obj($wp_scripts->$status);
            }

            /**
             * Print all registered or enqued styles
             * @example DIV\services\debug::styles(true);
             *
             * @param      boolean  $enqueued
             */
            public static function styles($enqueued=false) {
                global $wp_styles;
                $status = ($enqueued) ? "queue" : "registered";
                if(!IS_NULL($wp_styles)) self::obj($wp_styles->$status);
            }

            /**
             * Print all available shortcodes
             * @example DIV\services\debug::shortcodes(true);
             */
            public static function shortcodes(){
                global $shortcode_tags;
                self::obj($shortcode_tags);
            }

            /**
             * Print object or array
             * @example DIV\services\debug::obj($array);
             *
             * @param      array|object  $array
             */
            public static function obj($array){
                echo "<pre>"; print_r($array); echo "</pre>";
            }

        }

    endif;

}


?>