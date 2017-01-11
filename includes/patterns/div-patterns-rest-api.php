<?php
/**
 * Div Library API application Class
 * 
 * 				When creating an application that relies on RESTful calls
 * 				to an API, this pattern can be used to streamline the process.
 * 				Simply extend the class like so:
 * 				
 * 				class ExampleApplication extends DIV\patterns\API{
 * 				
 * 					# The string that defines the ajax
 * 					# action your api is bound
 *           		protected $action;
 *
 * @version 	1.0
 * @package 	div_library/Classes
 * @category 	Class
 * @author 		Div Blend Team
 */
namespace DIV\patterns{

	if( ! defined( 'ABSPATH' ) ) exit;

	if ( ! class_exists( 'REST_API' ) ) :

		abstract class REST_API {

			/**
             * The string that defines the ajax action
             * your api is bound
             */
            protected $action;

            /**
		     * Using a Div Rest object class, the REST 
             * handler must be configured in extended class
             * to make RESTful calls to an API
		     */
		    protected $rest;

		    /**
		     * Optional property when an addon app wants REST_API to check for
		     * expired session, both variables need to be defined
		     * 
		     * NOTE: OAuth provider class must have a refresh_session()
		     * 		defined in order to enable this feature
		     */
		    protected $provider;		# i.e. - 'salesforce'
		    protected $expired_param;	# i.e. - 'errorCode'
		    protected $expired_code;	# i.e. - 'INVALID_SESSION_ID'

		    /**
		     * Used to configure the $rest object prior to
		     * initiating an action. Child class must define
		     * this method and effectively setup $rest
		     */
		    abstract protected function config_rest();

			/**
			 * Constructor
			 */
			public function __construct() {
				add_action( 'wp_ajax_'.$this->action, array( $this, 'ajax_request') );
        		add_action( 'wp_ajax_nopriv_'.$this->action, array( $this, 'ajax_request') );
			}

			/**
		     * Initialize the application
		     */
		    public static function init(){
		    	$class = get_called_class();
		        new $class();
		    }

			/**
		     * AJAX response handler which routes the function in the request
			 * to a method defined in the API class
		     */
		    public function ajax_request() {
		        # The $_REQUEST contains all the data sent via ajax the function requested
		         
		        # Check for class parameter
		        if ( ISSET($_REQUEST['class']) ) {
		        	# Check for function parameter
			        if ( isset($_REQUEST['function']) ) {
		        		# Verify the referenced class exist
			        	if ( class_exists( $_REQUEST['class'] ) ) {
			        		# Create an instance of the class
				        	$application = new $_REQUEST['class']();

				        	# Verify rest can be configured
				        	if( method_exists($application, 'config_rest') ){
				        		$application->config_rest();
				        	}
				        	# Verify the class method exist
				        	if( method_exists($application, $_REQUEST['function']) ){
					            # Execute action defined by the application
					            $response = $application->$_REQUEST['function']();
					            
					            # Before providing the response, make sure it didn't fail
					            # from an expired session
					            if( $application->expired_param!= NULL && $application->expired_code != NULL){
					            	if( !$this->is_session_valid($application,$response)){
					            		# Attempt refresh session using stored refresh token
					            		if( $this->refresh_session($application) ){
								            # Reconfigure REST and reattempt action
								            $application->config_rest();
								            $response = $application->$_REQUEST['function']();
								        }
					            	}
					            	# Possibly attemp to cache the data response
					            	else {
					            		if( method_exists($application, 'cache_'.$_REQUEST['function']) ){
					            			$cache_method = 'cache_'.$_REQUEST['function'];
					            			$application->$cache_method($response);
					            		}
					            	}
					            }

			            		# Echo json response to ajax caller
			            		echo json_encode($response);

					        } else {
					        	echo 'The '.$_REQUEST['class'].' class does not contain the '.$_REQUEST['function'].'() method';
					        }
					    } else {
				        	echo 'The '.$_REQUEST['class'].' class does not exist';
					    }
			        } else {
			        	echo 'The "function" parameter was not set in the ajax call';
			        }
		        } else {
		        	echo 'The "class" parameter was not set in the ajax call';
		        }
		        # Always die in functions echoing ajax content
		        exit;
		    }

			/**
			 * Make a direct server side request to a RESTful API
			 */
			public function api_request($function) {
				# Configure the REST client
				$this->config_rest();
				
				# Verify the class method exist
				if( method_exists($this, $function) ){
			        # Execute action defined by the application
			        $response = $this->$function();
			        # Before providing the response, make sure it didn't fail from an expired session
			        if( $this->expired_param!= NULL && $this->expired_code != NULL){
			        	if( !$this->is_session_valid($this, $response)){
			        		# Attempt refresh session using stored refresh token
			        		if( $this->refresh_session($this) ){
					            # Reconfigure REST and reattempt action
					            $this->config_rest();
					            $response = $this->$function();
					        }
			        	}
			        }

					# Echo json response to ajax caller
					return json_encode($response);

			    } else {
			    	echo 'The '.__CLASS__.' class does not contain the '.$_REQUEST['function'].'() method';
			    }
					     
			}

			/**
			 * Verify ajax response isn't the result of an expired session
			 *
			 * @param      REST_API $application
			 * @param      array  	$response
			 */
			public function is_session_valid($application, $response) {
				# Hook into this action if you need to rewrite the
				# valid session checker
				do_action('verify_rest_session');

				# If there is an index for the expired param value that matches
				# the value of the expired code then session is expired
				if( ISSET($response[0][$application->expired_param]) && $response[0][$application->expired_param] == $application->expired_code )
					return false;

				return true;
			}

			/**
			 * Attempt to refresh the OAuth session using the refresh token
			 * stored in the application
			 *
			 * @param      REST_API $application
			 */
			public function refresh_session($application) {
				# Action hook if you need to customize the method
				do_action('verify_rest_session');

				# Dynamically load provider class
				$class = "OAuth_".ucfirst($application->provider);
				$oauth = new $class();
				if( method_exists($class,'refresh_session') ){
					$oauth->refresh_session();
					return true;
				}

				return false;
			}

		}

	endif;

}