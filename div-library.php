<?php
/**
 * Plugin Name: Div Library
 * Plugin URI: http://divblend.com/div-library/
 * Description: A powerful, indispensable, library of extendable tools and classes for theme developers who build custom WordPress solutions. Custom Post Type (CPT), Widget, Shortcode, User Role, and other classes making development more effecient. <strong>WARNING:</strong> Deactivating could have negative effects on your site if other active or "Must Use" plugins are using this library.
 * Version: 0.2.1 (alpha)
 * Author: Div Blend Team
 * Author URI: http://divblend.com/div-blend-contributors/
 * Requires at least: 3.8
 * Tested up to: 3.9
 *
 * Text Domain: divlibrary
 *
 * @package Div Library
 * @category Core
 * @author Div Blend Team
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; # Exit if accessed directly
}

/**
 * Main Div_Library Class
 * @class Div_Library
 */
final class Div_Library {

	/**
	 * @var 	string
	 * @since   1.0
	 */
	public $version = '0.1';

	/**
	 * Path Definitions
	 * @var 	array
	 * @since   1.0
	 */
	public $path = array();

	/**
	 * @var 	DIV_Detection
	 * @since   1.0
	 */
	public $user_agent = null;

	/**
	 * @var Div_Library The single instance of the class
	 * @since 1.0
	 */
	protected static $_instance = null;

	/**
	 * Main Div_Library Instance
	 *
	 * Ensures only one instance of Div_Library is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 * @see DIV()
	 * @return Div_Library - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'divlibrary' ), $this->version );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'divlibrary' ), $this->version );
	}

	/**
	 * Auto-load in-accessible properties on demand.
	 *
	 * @param mixed $key
	 * @return mixed
	 */
	public function __get( $key ) {
		if ( method_exists( $this, $key ) ) {
			return $this->$key();
		}
	}

	/**
	 * Div_Library Constructor.
	 * @since   1.0
	 * @access public
	 * @return Div_Library
	 */
	public function __construct() {
		// Auto-load classes on demand. This effectively creates a queue of autoload functions, and runs through each of them in the order they are defined.
		if ( function_exists( "__autoload" ) ) {
			spl_autoload_register( "__autoload" );
		}

		spl_autoload_register( array( $this, 'autoload' ) );

		// Define path variables
		$this->define_paths();

		// Include required files
		$this->includes();

		// Hooks
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'action_links' ) );
		add_action( 'init', array( $this, 'init' ), 0 );
		// add_action( 'init', array( 'DIV_Shortcodes', 'init' ), 10 );

		// Div Library loading complete
		do_action( 'divlibrary_loaded', $this );
	}

	/**
	 * Activate the plugin
	 *
	 * @since   1.0
	 * @return  void
	 */
	public static function activate() {
		#TODO: Recommended plugins
		#TODO: First time instructions
	}

	/**
	 * Deactivate the plugin
	 *
	 * @since   1.0
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
			'<a href="' . admin_url( 'admin.php?page=div-settings' ) . '">' . __( 'Settings', 'divlibrary' ) . '</a>',
			'<a href="' . esc_url( apply_filters( 'divlibrary_docs_url', 'http://www.divblend.com/div-library', 'divlibrary' ) ) . '">' . __( 'Documentation', 'divlibrary' ) . '</a>'
		), $links );
	}

	/**
	 * Auto-load Div_Library classes on demand to reduce memory consumption.
	 * TODO: Determine if autoload is necessary/possible in the library
	 * @since   1.0
	 * @param 	mixed $class
	 * @return 	void
	 */
	public function autoload( $class ) {
		$path  = null;
		$class = strtolower( $class );
		$file = 'class-' . str_replace( '_', '-', $class ) . '.php';

		if ( strpos( $class, 'div_shortcode_' ) === 0 ) {
			$path = $this->plugin_path() . '/includes/shortcodes/';
		} 

		if ( $path && is_readable( $path . $file ) ) {
			include_once( $path . $file );
			return;
		}

		if ( $path && is_readable( $path . $file ) ) {
			include_once( $path . $file );
			return;
		}
	}

	/**
	 * Define Library paths
	 */
	private function define_paths() {
		$this->path['template_path']		= $this->template_path();
		$this->path['plugin_file'] 			= __FILE__;
		$this->path['theme_url'] 			= get_stylesheet_directory_uri().'/';
		$this->path['theme_dir'] 			= get_stylesheet_directory().'/';

		#assets
		$this->path['assets_dir']		= $this->plugin_path().'/assets/';
		$this->path['css_dir']			= $this->path['assets_dir'].'css/';

			$this->path['assets_url']		= $this->plugins_url().'/assets/';
			$this->path['css_url']			= $this->path['assets_url'].'css/';
		
		#includes
		$this->path['includes_dir']		= $this->plugin_path().'/includes/';
		$this->path['fields_dir']		= $this->path['includes_dir'].'fields/';
			
			$this->path['includes_url']		= $this->plugins_url().'/includes/';
			$this->path['fields_url']		= $this->path['includes_url'].'fields/';
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	private function includes() {
		include_once( 'includes/div-core-functions.php' );		# Core div functions
		include_once( 'includes/class-div-prints.php' );		# Printout settings class
		include_once( 'includes/class-div-detection.php' );		# Browser/Device Detection class
		include_once( 'includes/class-div-shortcodes.php' );	# Shortcodes class

		// PCO Image Widget Field - by: PeytzCo, Compute, jamesbonham
		include_once( 'includes/fields/image-widget-field/pco-image-widget-field.php' );
		include_once( 'includes/class-div-widgets.php' );		# For creating custom widgets

		if ( is_admin() ) {
			#TODO: include_once( 'includes/admin/class-wc-admin.php' );
		}

		#TODO: Consider/Setup autoloading options
		// Classes (used on all pages)
		include_once( 'includes/class-div-helper.php' );		# Power tools for data manipluation
		include_once( 'includes/class-div-taxonomy.php' );		# For creating Custom taxonomies
		include_once( 'includes/class-div-cpt.php' );			# For creating Custom Post Types
		include_once( 'includes/class-div-roles.php' );			# For creating Custom User Types

	}

	/**
	 * Init Div_Library when WordPress Initialises.
	 */
	public function init() {
		// Before init action
		do_action( 'before_divlibrary_init' );

		// Init action
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
 * Returns the main instance of DIV to prevent the need to use globals.
 *
 * @since  1.0
 * @return Div_Library
 */
if(class_exists('Div_Library')){
	// Installation and uninstallation hooks
	register_activation_hook(__FILE__, array('Div_Library', 'activate'));
	register_deactivation_hook(__FILE__, array('Div_Library', 'deactivate'));

	Div_Library::instance(); #singleton
}