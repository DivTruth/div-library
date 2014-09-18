<?php
/**
 * DIV_Module class
 * Abstract class used for creating CPT modules at the site application or plugin level
 *
 * @class       DIV_Module
 * @version     1.0
 * @package     div_library/Classes
 * @category    Class
 * @uses        DIV_CPT, DIV_Template, DIV_Helper
 */

if( ! defined( 'ABSPATH' ) ) exit;

abstract class DIV_Module {

    public $lables = array();
    public $args = array();
    public $column_filters = array();
    public $column_styles = array();
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
        
        add_filter( "manage_".$this->cpt."_posts_columns", array( $this, 'columns') );
        add_action( "manage_".$this->cpt."_posts_custom_column", array( $this, 'column_filters'), 10, 2 );
        add_action( 'admin_head-edit.php', array( $this, 'column_styles' ) );
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
     * @link http://divblend.com/div-library/class-div_cpt/
     */
    public function register_cpt(){
        return new DIV_CPT($this->cpt, $this->args, $this->labels);
    }

    /**
     * FILTER: COLUMNS
     * 
     * @param array $cols
     * @return array $cols
     */
    function columns($cols){
        return $cols = ($this->columns) ? $this->columns : $cols;
    }

    /**
     * ADD COLUMN FILTER
     * @example $this->add_column_filter('featured_image', function($post_id){} );
     * 
     * @param string $column
     * @param string $function
     * @return void
     */
    function add_column_filter($column, $function){
        $this->column_filters[$column] = $function;        
    }

    /**
     * ADD COLUMN STYLES
     * @example $this->add_column_styles('featured_image', 'width: 120px; text-align: center' );
     * 
     * @param string $column
     * @param string $styles
     * @return void
     */
    function add_column_styles($column, $styles){
        $this->column_styles[$column] = $styles;
    }

    /**
     * ACTION: COLUMN STYLES
     * Adds styles to admin head for post panel columns
     * 
     */
    function column_styles(){
        _e('<style type="text/css">');
            foreach ($this->column_styles as $column => $styles) {
                _e('.widefat .column-'.$column.' { '.$styles.' }');
            }
        _e('</style>');
    }

    /**
     * ACTION: CUSTOM COLUMN FILTERS
     * 
     * @param array $cols
     * @param array $post_id
     * @return array $cols
     */
    public function column_filters( $columns, $post_id ) {
        foreach ($this->column_filters as $column => $function) {
            switch ( $columns ) {
                case $column:
                    $function($post_id);
                    break;
            }
        }
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
     * FILTER: SINGLE TEMPLATE FOR CPT
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
     * FILTER: PAGE TEMPLATE FOR CPT
     * Setup the page template for this cpt module
     * @example /modules/module/page-custom.php
     *
     */
    public function page_template($templates) {
        $page_templates = new DIV_Template($this->page_templates, $this->dir);
    }

}