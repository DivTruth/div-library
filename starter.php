<?php
/**
 * Plugin Name: Div Starter
 * Plugin URI: http://divblend.com/div-starter/
 * Description: A power, indispensable tool for theme developers who build custom solutions.
 * Version: 0.1 (alpha)
 * Author: Div Blend Team
 * Author URI: http://divblend.com/div-blend-contributors/
 * Requires at least: 3.8
 * Tested up to: 3.9
 *
 * Text Domain: divstarter
 * Domain Path: /i18n/languages/
 *
 * @package Div Starter
 * @category Core
 * @author Div Blend Team
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; # Exit if accessed directly
}

if ( ! class_exists( 'DivStarter' ) ) :

/**
 * Main DivStarter Class
 *
 * @class DivStarter
 * @version	1.0
 */
final class DivStarter {

	/**
	 * @var string
	 */
	public $version = '0.1';

	/**
	 * @var DivStarter The single instance of the class
	 * @since 1.0
	 */
	protected static $_instance = null;

	/**
	 * @var DS_Detection $user_agent
	 */
	public $user_agent = null;

	/**
	 * Main DivStarter Instance (singleton)
	 *
	 * Ensures only one instance of DivStarter is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 * @see DS()
	 * @return DivStarter - Main instance
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
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'divstarter' ), '2.1' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'divstarter' ), '2.1' );
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
	 * DivStarter Constructor.
	 * @access public
	 * @return DivStarter
	 */
	public function __construct() {
		// Auto-load classes on demand. This effectively creates a queue of autoload functions, and runs through each of them in the order they are defined.
		if ( function_exists( "__autoload" ) ) {
			spl_autoload_register( "__autoload" );
		}

		spl_autoload_register( array( $this, 'autoload' ) );

		// Define constants
		$this->define_constants();

		// Include required files
		$this->includes();

		// Hooks
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'action_links' ) );
		add_action( 'plugins_loaded', array( $this, 'include_fields' ) );
		add_action( 'widgets_init', array( $this, 'include_widgets' ) );
		add_action( 'init', array( $this, 'init' ), 0 );
		add_action( 'init', array( $this, 'include_template_functions' ) );
		add_action( 'init', array( 'DS_Shortcodes', 'init' ) );
		add_filter( 'admin_footer_text', array( $this, 'ds_admin_footer'), 9999 );
		
		// Setup any theme environment settings
		add_action( 'after_setup_theme', array( $this, 'setup_environment' ) );

		// Loaded action
		do_action( 'divstarter_loaded' );
	}

	/**
	 * Show action links on the plugin screen
	 *
	 * @param mixed $links
	 * @return array
	 */
	public function action_links( $links ) {
		$plugin_name = 'advanced-custom-fields';
		$acf_link = '<a href="' . esc_url( network_admin_url('plugin-install.php?tab=plugin-information&plugin=' . $plugin_name . '&TB_iframe=true&width=600&height=550' ) ) . '" class="thickbox" title="More info about ACF">Install ACF</a>';
		return array_merge( array(
			'<a href="' . admin_url( 'admin.php?page=ds-settings' ) . '">' . __( 'Settings', 'divstarter' ) . '</a>',
			'<a href="' . esc_url( apply_filters( 'divstarter_docs_url', 'http://divstarter.com/', 'divstarter' ) ) . '">' . __( 'Documentation', 'divstarter' ) . '</a>',
			'<br/>'.$acf_link
		), $links );
	}

	/**
	 * Auto-load DS classes on demand to reduce memory consumption.
	 *
	 * @param mixed $class
	 * @return void
	 */
	public function autoload( $class ) {
		$path  = null;
		$class = strtolower( $class );
		$file = 'class-' . str_replace( '_', '-', $class ) . '.php';

		if ( strpos( $class, 'ds_shortcode_' ) === 0 ) {
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
	 * Define DS Constants
	 */
	private function define_constants() {
		define( 'WP_VERSION', get_bloginfo('version') );
		define( 'DS_VERSION', $this->version );

		/* **************  THEME Paths definition *********************** */
		define( 'THEME_URL', get_stylesheet_directory_uri().'/' );
		define( 'THEME_DIR', get_stylesheet_directory().'/' );
		
		/* **************  DS STARTER Paths definition *********************** */
		define( 'DS_PLUGIN_FILE', 		__FILE__ );

		define( 'DS_ASSETS_DIR', 		$this->plugin_path().'/assets/' );
		define( 'DS_CSS_DIR', 			DS_ASSETS_DIR.'css/' );
		define( 'DS_IMAGES_DIR', 		DS_ASSETS_DIR.'images/' );
		define( 'DS_JS_DIR', 			DS_ASSETS_DIR.'js/' );

		define( 'DS_ASSETS_URL', 		$this->plugins_url().'/assets/' );
		define( 'DS_CSS_URL', 			DS_ASSETS_URL.'css/' );
		define( 'DS_IMAGES_URL', 		DS_ASSETS_URL.'images/' );
		define( 'DS_JS_URL', 			DS_ASSETS_URL.'js/' );

		define( 'DS_INCLUDES_DIR', 	$this->plugin_path().'/includes/' );
		define( 'DS_ABSTRACTS_DIR', 	DS_INCLUDES_DIR.'abstracts/' );
		define( 'DS_SHORTCODES_DIR', 	DS_INCLUDES_DIR.'shortcodes/' );
		define( 'DS_WIDGETS_DIR', 		DS_INCLUDES_DIR.'widgets/' );
		
		define( 'DS_INCLUDES_URL', 	$this->plugins_url().'/includes/' );
		define( 'DS_ABSTRACTS_URL', 	DS_INCLUDES_URL.'abstracts/' );
		define( 'DS_SHORTCODES_URL', 	DS_INCLUDES_URL.'shortcodes/' );
		define( 'DS_WIDGETS_URL', 		DS_INCLUDES_URL.'widgets/' );

		if ( ! defined( 'DS_TEMPLATE_PATH' ) ) {
			define( 'DS_TEMPLATE_PATH', $this->template_path() );
		}

	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	private function includes() {
		include_once( 'includes/ds-core-functions.php' );			# Core div functions
		include_once( 'includes/class-ds-prints.php' );				# Printout settings class
		include_once( 'includes/class-ds-detection.php' );			# Browser/Device Detection class
		include_once( 'includes/class-ds-shortcodes.php' );			# Shortcodes class

		if ( is_admin() ) {
			// include_once( 'includes/admin/class-wc-admin.php' );
		}

		if ( defined( 'DOING_AJAX' ) ) {
			// $this->ajax_includes();
		}

		if ( ! is_admin() || defined( 'DOING_AJAX' ) ) {
			$this->frontend_includes();
		}

		// Classes (used on all pages)
		include_once( 'includes/class-ds-helper.php' );			# Power tools for data manipluation
		include_once( 'includes/class-ds-taxonomy.php' );		# For creating Custom taxonomies
		include_once( 'includes/class-ds-cpt.php' );			# For creating Custom Post Types
		include_once( 'includes/class-ds-roles.php' );			# For creating Custom User Types

		// Include template hooks in time for themes to remove/modify them
		include_once( 'includes/ds-template-hooks.php' );
	}

	/**
	 * Include required ajax files.
	 */
	public function ajax_includes() {
		// include_once( 'includes/class-ds-ajax.php' );					# Ajax functions for admin and the front-end
	}

	/**
	 * Include required frontend files.
	 */
	public function frontend_includes() {
		// include_once( 'includes/class-ds-template-loader.php' );		# Template Loader
		// include_once( 'includes/class-ds-frontend-scripts.php' );	# Frontend Scripts
	}

	/**
	 * Function used to Init DivStarter Template Functions - This makes them pluggable by plugins and themes.
	 */
	public function include_template_functions() {
		include_once( 'includes/ds-template-functions.php' );
	}
	
	/**
	 * Include core fields
	 */
	public function include_fields() {
		// Pco Image Widget Field - by: PeytzCo, Compute, jamesbonham
		include_once( 'includes/fields/image-widget-field/pco-image-widget-field.php' );
	}

	/**
	 * Include core widgets
	 */
	public function include_widgets() {
		include_once( 'includes/abstracts/abstract-ds-widget.php' );
		include_once( 'includes/widgets/class-ds-widget-banner.php' );
		include_once( 'includes/widgets/class-ds-widget-text-image.php' );
	}

	/**
	 * Init DivStarter when WordPress Initialises.
	 */
	public function init() {
		// Before init action
		do_action( 'before_divstarter_init' );

		// Load Class instances
		$this->user_agent = new DS_Detection();		# Detection class

		// Init action
		do_action( 'divstarter_init' );
	}

	/**
	 * Ensure theme and server variable compatibility and setup image sizes..
	 */
	public function setup_environment() {

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
		return apply_filters( 'DS_TEMPLATE_PATH', 'starter/' );
	}

	/**
	 * Custom Backend Footer
	 *
	 * @param string
	 */
	# 
	public function ds_admin_footer($s) {
	    $ds_footer = apply_filters( 'ds_admin_footer', '<span id="footer-thankyou">'.$s.'<br/>
	    	Developed with <a href="http://www.divblend.com/div-starter" target="_blank">Div Starter</a> (version '.DS_VERSION.')
	    </span>');
	    _e($ds_footer, 'div-starter');
	}

	/**
	 * Get Ajax URL.
	 *
	 * @return string
	 */
	public function ajax_url() {
		return admin_url( 'admin-ajax.php', 'relative' );
	}

}

endif;

/**
 * Returns the main instance of DS to prevent the need to use globals.
 *
 * @since  1.0
 * @return DivStarter
 */
function DS() {
	return DivStarter::instance();
}

DS();