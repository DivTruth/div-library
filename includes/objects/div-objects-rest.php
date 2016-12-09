<?php
/**
 * DIV rest object class
 * Make Restful cURL calls through the DIV rest object
 *
 * @package     div-library/objects
 * @author      Nick Worth
 */
namespace DIV\objects{

    if( ! defined( 'ABSPATH' ) ) exit;
    
    if ( ! class_exists( 'rest' ) ) :
    
        class rest{

            /**
             * Access token for authentication
             */
            protected $token;

            /**
             * Endpoint to make call
             */
            protected $endpoint;

            /**
             * Constructor
             *
             * @param      string|boolean  $token
             * @param      string|boolean  $endpoint
             */
            public function __construct($token=false, $endpoint=false) {
                $this->token    = $token;
                $this->endpoint = $endpoint;
            }

            /**
             * Make a POST request to REST API
             *
             * @param      array   $params
             * @param      string  $service
             *
             * @return     array
             */
            public function post( $params, $service = false ) {
                if($service)
                    $url = $this->endpoint.'/'.$service.'?'.http_build_query($params);
                else
                    $url = $this->endpoint.'?'.http_build_query($params);

                # Attempt curl request:
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                    'Accept: application/json',
                    'Authorization:' . 'Bearer ' . $this->token,
                    'X-PrettyPrint:1'
                ));
                $result = curl_exec($curl);
                # Check for curl error
                if(curl_errno($curl)) return false;
                # Close request
                curl_close($curl);

                # Parse & handle the result:
                $response = json_decode($result, true);
                return $response;
            }

        }
    
    endif;

}
