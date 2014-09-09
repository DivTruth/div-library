<?php
/**
 * DIV_Role class
 * Manage user permissions, roles and other user-based functionality for development
 *
 * @class 		DIV_Role
 * @version		1.0
 * @package		DivStarter/Classes
 * @category	Class
 * @uses        DIV_Helper
 * @author 		Div Blend Team
 */

if( ! defined( 'ABSPATH' ) ) exit;

class DIV_Role {

	#store all non-standard roles
	public $roles;

	/**
	 * Constructor
	 * @access public
	 * @param string|array $name
	 * @param string $display_name
	 * @param string|array $capabilities
	 * @return array $roles
	 */
	public function __construct($name, $display_name = "", $capabilities = "") {
		$roles = $this->get_roles();

		if( ! empty( $name ) ) {
			if( is_array( $name ) ) {
				$this->register_roles($name);
			} else {
	            $name     		= $name;
	            $display_name 	= ( $display_name ) ? $display_name : DIV_Helper::beautify( $name );
	            $capabilities 	= self::setup_capabilities($capabilities);
	    		$this->register_role($name,$display_name,$capabilities);
	    	}
		}
	}

	/**
	 * Register an array of roles
	 * @access public
	 * @return array of user_objects
	 */
	function register_roles($name){
		foreach ($name as $n => $args) {
            $role 			= DIV_Helper::uglify( $n );
            $display_name 	= ( !empty($args['display_name']) ) ? $args['display_name'] : DIV_Helper::beautify( $n );
            $capabilities 	= self::setup_capabilities($args['capabilities']);
			$this->register_role($role,$display_name,$capabilities);
		}
	}

	/**
	 * Register Roles
	 * @access public
	 * @return user_object
	 */
	public function register_role($role,$display_name,$capabilities){
		remove_role($role);
        $this->roles[$role] = add_role( $role,$display_name,$capabilities);
	}

	/**
	 * Setup the capabilities array
	 * @access public
	 * @param string/array
	 * @return array
	 */
	static function setup_capabilities($c=""){
		// Set default clone permissions in General Settings or with 'div_default_role' filter
		$default_role = apply_filters( 'div_default_role', get_option( 'default_role', 'subscriber' ) );

		// If array, look for inherited roles, and setup any custom capabilities
        if (  is_array($c) ) {
	        // Check for inherited roles
	        foreach ($c as $capability => $v) {
				$roles = self::get_roles(1);
				$admin_role = get_role('administrator');

				if(in_array($capability, $roles)){
					unset($c[$capability]); //remove role
					
					// Add role's capabilities
					$inherited = get_role($capability)->capabilities;
					$c['inherited'] = $capability;
					$c = array_merge($c, $inherited);
				} else {
					if(!array_key_exists ( $capability, $admin_role->capabilities)){
						$admin_role->add_cap($capability);
					}
				}
			}
		}

		// If not provided or string, then inherit capabilities from default or declared role
		if ( empty($c) || is_string($c)) {
        	$default = ( empty($c) ) ? get_role($default_role) : get_role($c);
        	$c = $default->capabilities;
        	$c['inherited'] = $default->name;
        }
        
       	return $c;
	}

	/**
	 * Add the capabilities to defined roles
	 * @access public
	 * @param string/array
	 */
	public function add_cap($c){
		$admin_role = get_role('administrator');
		$admin_role->add_cap($c);
		foreach ($this->roles as $name => $role) {
			$role->add_cap($c);
		}
	}

	/**
	 * Remove capabilities from defined roles
	 * @access public
	 * @param string/array
	 */
	public function remove_cap($c){
		foreach ($this->roles as $name => $role) {
			$role->remove_cap($c);
		}
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

	//TODO: clean_roles() to remove all custom capabilities added to a WP standard role

}