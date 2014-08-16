<?php
/**
 * DS_Role class.
 * Manage user permissions, roles and other user-based functionality for development
 *
 * @class 		DS_Role
 * @version		1.0
 * @package		DivStarter/Classes
 * @category	Class
 * @uses        DS_Helper
 * @author 		Div Blend Team
 */

if( ! defined( 'ABSPATH' ) ) exit;

class DS_Role {

	public $name;
	public $display_name;
	public $capabilities;

	/**
	 * Constructor
	 * @access public
	 * @param string|array $name
	 * @param string $display_name
	 * @param string|array $capabilities
	 */
	public function __construct($name, $display_name = "", $capabilities = "") {
		if( ! empty( $name ) ) {
            $this->name     		= $name;
            $this->display_name 	= ( $display_name ) ? $display_name : DS_Helper::beautify( $name );
            $this->capabilities 	= $this->setup_capabilities();
    		$this->register_role();
		}
	}

	/**
	 * Register an array of roles
	 * @access public
	 * @return array of user_objects
	 */
	static function register_roles($name){
		if( is_array( $name ) ) {
			foreach ($name as $n => $args) {
	            $name 			= DS_Helper::uglify( $n );
	            $display_name 	= DS_Helper::beautify( $n );
	            $capabilities 	= self::setup_capabilities($args['capabilities']);
				$roles[$name] 	= new DS_Role($name,$display_name,$capabilities);
			}
			return $roles;
		} else {
			return false;
		}
	}

	/**
	 * Register Roles
	 * @access public
	 * @return user_object
	 */
	public function register_role(){
		remove_role($this->name);
		$default_role = apply_filters( 'ds_default_role', get_option( 'default_role', 'subscriber' ) );
        return $user_type = add_role( $this->name, $this->display_name, $this->capabilities);
	}

	/**
	 * Setup the capabilities array
	 * @access public
	 * @param string/array
	 * @return array
	 */
	static function setup_capabilities($c=""){
		// Set default clone permissions in General Settings or with 'ds_default_role' filter
		$default_role = apply_filters( 'ds_default_role', get_option( 'default_role', 'subscriber' ) );

		if ( empty($c) || is_string($c)) {
        	global $wp_roles; if ( ! isset( $wp_roles ) ) $wp_roles = new WP_Roles();
        	$default = ( !empty($c) ) ? $wp_roles->get_role($c) : $wp_roles->get_role($default_role);
        	// $this->cloned = $default->name;
        	$c = $default->capabilities;
        }
       	return $c;
	}

	/**
	 * Setup the capabilities array
	 * @access public
	 * @param string/array
	 * @return array
	 */
	public function add_cap($c){
		$this->role->add_cap();
	}

	/**
	 * Get all custom defined roles
	 * @access public
	 * @param boolean $include_standards
	 */
	static function get_roles($include_standards = false){
		global $wp_roles; if ( ! isset( $wp_roles ) ) $wp_roles = new WP_Roles();
		$all_roles = array();
		$i = 0;
		foreach ($wp_roles->roles as $role => $value) {
			$all_roles[$i] = $role;
			$i++;
		}
		if(!$include_standards){
			$standard_roles = array('administrator','editor','author','contributor','subscriber');
			$roles = array_diff($all_roles, $standard_roles );
		} else {
			$roles = $all_roles;
		}

		return $roles;
	}

	/**
	 * Remove/Reset all custom roles
	 * @access public
	 */
	static function clear_roles(){
		foreach (self::get_roles() as $role) {
			remove_role( $role );
		}
	}

}

$caps = array(
	'read' 		=> 1,
	'level_0'	=> 1,
	'level_1'	=> 1,
);

$roles = array(
	'guest' 		=> array(
		'capabilities'	=> 'subscriber'
	),
	'tech'			=> array(
		'display_name'	=> 'RR Technician',
		'capabilities'	=> $caps
	),
	'web_master' 	=> array(
		'display_name'	=> 'Web Master',
		'capabilities'	=> 'administrator'
	),
);

// $rr_technician = new DS_Role('peasant');
// DS_Print::object($rr_technician);

$rr_roles = DS_Role::register_roles($roles);
// $rr_technician->add_cap('test_cap');
DS_Print::object($rr_roles);


// DS_Role::clear_roles();