<?php 
/**
 * Div Library DIV_Detection
 * Browser & Device Detection Handler
 *
 * @class       DIV_Detection
 * @version     1.0
 * @package     Div_Library/Classes
 * @category    Class
 * @author      Div Blend Team
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class DIV_Detection {

    /** @var array Array of browser/device classes */
    var $classes = array();

    /**
     * Hook into WP body_class filter
     * @access public
     * @return void
     */
    public function __construct() {
        add_filter('body_class', array($this,'add_body_classes') );
    }

    /**
     * Detect browser and device, then add to body class
     * @param  array $classes
     * @return array
     */
    function add_body_classes($classes) {
        global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari,$is_chrome, $is_iphone;
        if($is_lynx) $classes[] = 'lynx';
        elseif($is_gecko) $classes[] = 'gecko';
        elseif($is_opera) $classes[] = 'opera';
        elseif($is_NS4) $classes[] = 'ns4';
        elseif($is_safari) $classes[] = 'safari';
        elseif($is_chrome) $classes[] = 'chrome';
        elseif($is_IE) {
            if(preg_match('/MSIE ([0-9]+)([a-zA-Z0-9.]+)/',$_SERVER['HTTP_USER_AGENT'], $browser_version))
                $classes[] = 'ie ie'.$browser_version[1];
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'Trident/7.0; rv:11.0') !== false)
                $classes[] = 'ie ie11';
        } else $classes[] = 'unknown';
        if($is_iphone) $classes[] = 'iphone';
        if ( stristr( $_SERVER['HTTP_USER_AGENT'],"mac") ) {
             $classes[] = 'osx';
           } elseif ( stristr( $_SERVER['HTTP_USER_AGENT'],"linux") ) {
               $classes[] = 'linux'; //Browser detection and OS detection with body_class
           } elseif ( stristr( $_SERVER['HTTP_USER_AGENT'],"windows") ) {
             $classes[] = 'windows';
           }

        $this->classes = $classes;
        return $classes;
    }

    /**
     * Return Browser/Device classes.
     *
     * @access public
     * @return array
     */
    public function get_classes() {
        return $this->classes;
    }
    
}