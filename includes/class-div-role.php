<?php
/**
 * DIV_Role class
 * Manage user permissions, roles and other user-based functionality for development
 *
 * @class 		DIV_Role
 * @version		1.0
 * @package		div_library/Classes
 * @category	Class
 * @uses        DIV_Helper
 * @author 		Div Blend Team
 */

if( ! defined( 'ABSPATH' ) ) exit;

class DIV_Role {

	/**
	 * Store all non-standard roles
	 * @var array
	 */
	public $roles;

	/**
	 * Store all non-standard capabilities
	 * @var array
	 */
	public $capabilities;

	/**
	 * Constructor
	 * @access public
	 * @param string|array $name
	 * @param string $display_name
	 * @param string|array $capabilities
	 * @return array $roles
	 */
	public function __construct($name, $display_name = "", $capabilities = "") {
		$this->roles = self::get_roles();
		$this->capabilities = array();

		if( ! empty( $name ) ) {
			if( is_array( $name ) ) {
				$this->register_roles($name);
			} else {
	            $name     		= $name;
	            $display_name 	= ( $display_name ) ? $display_name : DIV\services\helper::beautify( $name );
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
            $role 			= DIV\services\helper::uglify( $n );
            $display_name 	= ( !empty($args['display_name']) ) ? $args['display_name'] : DIV\services\helper::beautify( $n );
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
	 * Setup the capabilities array (includes role inheritance)
	 * NOTE: If no capability array is passed then subscriber or 
	 * default role capabilities will be applied
	 * @access public
	 * @param string/array
	 * @return array
	 */
	public function setup_capabilities($caps=""){
		/* Set default clone permissions in General Settings or with 'div_default_role' filter */
		$default_role = apply_filters( 'div_default_role', get_option( 'default_role', 'subscriber' ) );

		/* If array, look for inherited roles, then setup any custom capabilities in addition */
        if ( is_array($caps) ) {
	        /* Iterate through all passed capabilities */
	        foreach ($caps as $i => $capability) {

		        /* Checking for inherited roles */
				$all_roles = self::get_roles(1);
				if(in_array($capability, $all_roles)){
					
					/* Add role's capabilities */
					$inherited = get_role($all_roles[$i])->capabilities;
					$caps['inherited'] = $i; # Indicate inheritance
					$caps = array_merge($caps, $inherited);
				} else {
					$caps[$capability] = $this->capabilities[$capability] = 1;
				}
				unset($caps[$i]); # remove role from list of capabilities
			}
		}

		/* If not provided or string, then inherit capabilities from default or declared role */
		if ( empty($caps) || is_string($caps)) {
        	$default = ( empty($caps) ) ? get_role($default_role) : get_role($caps);
        	$caps = $default->capabilities;
        	$caps['inherited'] = $default->name; # Indicate inheritance
        }
        
       	return $caps;
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