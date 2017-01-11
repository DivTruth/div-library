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
					            # Execute action and echo the response
					            $response = $application->$_REQUEST['function']();
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

		}

	endif;

}