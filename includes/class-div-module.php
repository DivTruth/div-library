<?php
/**
 * DIV_Module class
 * Abstract class used for creating CPT modules at the site application or plugin level
 *
 * @class 		DIV_Module
 * @version		1.0
 * @package		div_library/Classes
 * @category	Class
 * @uses        DIV_CPT, DIV_Template, DIV_Helper
 */

if( ! defined( 'ABSPATH' ) ) exit;

abstract class DIV_Module {

    public $lables = array();
    public $args = array();
    public $page_templates = array();
    public $single_template;
    public $module;
    public $cpt;
    public $dir;

	/**
	 * CONSTRUCTOR
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
        $this->cpt = get_class($this);;
        $this->dir = $this->get_directory();
        $this->module = $this->register_cpt();

        # if ACF fields are used include them
        if ( class_exists( 'Acf' ) ) $this->register_acf_fields();

        # Default: single-{cpt}.php
        $this->single_template = $this->dir.'/single-'.$this->cpt.'.php';

        # Default: array( page-{plural-cpt}.php => Plural CPT )
        $this->page_templates = array(
            'page-'.DIV_Helper::pluralize($this->cpt).'.php' => DIV_Helper::beautify(DIV_Helper::pluralize($this->cpt)),
        );
    }

    /**
     * INIT
     * To be hooked at 'plugins_loaded'
     * @example add_action( 'plugins_loaded', array($custom, 'init') );
     *
     * @access public
     * @return void
     */
    public function init(){
        # Hooks & Filters
        add_filter( 'single_template', array( $this, 'single_template'), 20 );
        add_filter( 'init', array( $this, 'page_template' ) );
        add_action( 'widgets_init', array( $this, 'include_widgets' ), 20 ); # After Div Library (10)
    }

    /**
     * GET CLASS DIRECTORY
     * Get the child class directory
     *
     * @access public
     * @return string
     */
    public function get_directory(){
        $reflector = new ReflectionClass($this->cpt);
        $fn = $reflector->getFileName();
        return dirname($fn).'/';
    }

    /**
     * REGISTER CPT
     *
     * @uses DIV_CPT
     * @link http://codex.wordpress.org/Function_Reference/register_post_type 
     */
    public function register_cpt(){
        return new DIV_CPT($this->cpt, $this->args, $this->labels);
    }

    /**
     * INCLUDE ALL ACF FIELDS
     * Include field files exported from ACF
     * @example /modules/module/fields-example.php
     *
     */
    function register_acf_fields(){
        foreach( glob($this->dir . '/fields*.php') as $class_path )
            require_once( $class_path );
    }

    /**
     * INCLUDE ALL DEFINED WIDGETS
     * Include field files exported from ACF
     * @example /modules/module/widget-example.php
     *
     */
    function include_widgets(){
        foreach( glob($this->dir . '/widget*.php') as $class_path )
            require_once( $class_path );
    }

    /**
     * SINGLE TEMPLATE FOR CPT
     * Setup single template for this cpt module
     * @example /modules/module/single-custom.php
     *
     */
    public function single_template( $single_template ) {
        if ( is_singular($this->cpt) ){
            $single_template = $this->single_template;
        }
        return $single_template;
    }

    /**
     * PAGE TEMPLATE FOR CPT
     * Setup the page template for this cpt module
     * @example /modules/module/page-custom.php
     *
     */
    public function page_template($templates) {
        $page_templates = new DIV_Template($this->page_templates, $this->dir);
    }

}