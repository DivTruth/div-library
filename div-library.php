<?php
/**
 * Plugin Name: Div Library
 * Plugin URI: http://divblend.com/div-library/
 * Description: A powerful, indispensable, library of extendable tools and classes for theme developers who build custom WordPress solutions. Custom Post Type (CPT), Widget, Shortcode, User Role, and other classes making development more effecient. <br/><strong>WARNING:</strong> Deactivating could have negative effects on your site if other active or "Must Use" plugins are using this library.
 * Version: 0.3.0 (beta)
 * Author: Div Blend Team
 * Author URI: http://divblend.com/div-blend-contributors/
 * Requires at least: 3.8
 * Tested up to: 4.6.1
 *
 * Text Domain: divlibrary
 *
 * @package Div Library
 * @category Core
 * @author Div Blend Team
 */

# Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Main div_library Class
 * @class div_library
 */
final class div_library {

	/**
	 * Div Library version
	 * 
	 * @var 	string
	 */
	public $version = '0.3.0';

	/**
	 * Path array for all known directories
	 * 
	 * @var 	array
	 */
	public $path = array();

	/**
	 * The single instance of the class
	 * 
	 * @var div_library
	 */
	protected static $_instance = null;

	/**
	 * Main div_library Instance
	 * 
	 * NOTE: Ensures only one instance of div_library is loaded or can be loaded.
	 *
	 * @static
	 * @see div_library()
	 * @return div_library - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'divlibrary' ), $this->version );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'divlibrary' ), $this->version );
	}

	/**
	 * div_library Constructor
	 */
	public function __construct() {
		# Define path variables
		$this->define_paths();

		# Auto load library classes
		$this->autoload();

		# Library core div functions
		require_once( 'includes/div-core-functions.php' );

		# Hooks
		$this->hooks();

		# Div Library loading complete
		do_action( 'divlibrary_loaded', $this );
	}

	/**
	 * Register auto-loader methods
	 * 
	 * NOTE: Auto-load classes on demand. This effectively creates a queue of autoload 
	 * functions, and runs through each of them in the order they are defined
	 */
	private function autoload(){
		spl_autoload_register( array( $this, 'includes' ) );
		spl_autoload_register( array( $this, 'services' ) );
	}

	/**
	 * Autoload the include classes
	 *
	 * @param      string  $class
	 */
	private function includes( $class ) {
		# Check for div include class type
		if (stripos($class, 'DIV_') === false) return;

		# Convert to proper class file structure
		$class = str_replace('_', '-', strtolower($class));
		
		# Check for div include class structure
		if( is_file($this->path['includes_dir'].'class-'.$class.'.php') )
			require $this->path['includes_dir'].'class-'.$class.'.php';
		else if( is_file($this->path['includes_dir'].'fields/'.$class.'.php') )
			require $this->path['includes_dir'].'fields/'.$class.'.php';
			
	}

	/**
	 * Autoload the services classes
	 *
	 * @param      string  $class
	 */
	private function services( $class ) {
		# Check for div service class type
		if (strpos($class, 'DIV\\services') === false) return;

		# Convert to proper service class file structure
		$class = str_replace('\\', '-', strtolower($class));
		
		# Include service class
		if( is_file($this->path['services_dir'].$class.'.php') )
			require $this->path['services_dir'].$class.'.php';

	}

	/**
	 * Setup action and filter hooks
	 */
	private function hooks(){
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'action_links' ) );
		add_action( 'init', array( $this, 'init' ), 0 );
	}

	/**
	 * Activate the plugin
	 *
	 * @return  void
	 */
	public static function activate() {
		#TODO: Recommended plugins
		#TODO: First time instructions
	}

	/**
	 * Deactivate the plugin
	 *
	 * @return  void
	 */
	public static function deactivate() {
		#TODO: Determine if deactivate() function is necessary
	}
 
	/**
	 * Show action links on the plugin screen
	 *
	 * @since   1.0
	 * @param 	mixed $links
	 * @return 	array
	 */
	public function action_links( $links ) {
		return array_merge( array(
			// '<a href="' . admin_url( 'admin.php?page=div-settings' ) . '">' . __( 'Settings', 'divlibrary' ) . '</a>',
			'<a href="' . esc_url( apply_filters( 'divlibrary_docs_url', 'http://divblend.com/div-library', 'divlibrary' ) ) . '">' . __( 'Documentation', 'divlibrary' ) . '</a>'
		), $links );
	}

	/**
	 * Define Library paths
	 */
	private function define_paths() {
		$this->path['template_path']		= $this->template_path();
		$this->path['plugin_file'] 			= __FILE__;
		$this->path['theme_url'] 			= get_stylesheet_directory_uri().'/';
		$this->path['theme_dir'] 			= get_stylesheet_directory().'/';

		# Assets
		$this->path['assets_dir']		= $this->plugin_path().'/assets/';
		$this->path['css_dir']			= $this->path['assets_dir'].'css/';

			$this->path['assets_url']		= $this->plugins_url().'/assets/';
			$this->path['css_url']			= $this->path['assets_url'].'css/';
		
		# Includes
		$this->path['includes_dir']		= $this->plugin_path().'/includes/';
		$this->path['fields_dir']		= $this->path['includes_dir'].'fields/';
			
			$this->path['includes_url']		= $this->plugins_url().'/includes/';
			$this->path['fields_url']		= $this->path['includes_url'].'fields/';		
		
		# Services
		$this->path['services_dir']		= $this->plugin_path().'/services/';
		$this->path['services_url']		= $this->plugins_url().'/services/';
	}

	/**
	 * Init div_library when WordPress Initialises
	 * 
	 * NOTE: Hooks added for developers to enter during
	 * 		 the library initialization
	 */
	public function init() {
		# Before init action
		do_action( 'before_divlibrary_init' );

		# Init action
		do_action( 'divlibrary_init' );
	}

	/** Helper functions ******************************************************/

	/**
	 * Get the plugin url.
	 *
	 * @return string
	 */
	public function plugins_url() {
		return untrailingslashit( plugins_url( '/', __FILE__ ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

	/**
	 * Get the template path.
	 *
	 * @return string
	 */
	public function template_path() {
		return apply_filters( 'div_template_path', 'library/' );
	}

}

/**
 * Returns the main instance of div_library to prevent the need to use globals.
 *
 * @since  1.0
 * @return div_library
 */
if(class_exists('div_library')){
	# Installation and uninstallation hooks
	register_activation_hook(__FILE__, array('div_library', 'activate'));
	register_deactivation_hook(__FILE__, array('div_library', 'deactivate'));

	return $library = div_library::instance(); #singleton
}