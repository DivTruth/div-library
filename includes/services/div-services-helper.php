<?php
/**
 * DIV Service: Helper Class
 * A service class with utility and helper methods
 *
 * @package     div-library/services
 * @author      Div Blend Team
 */
namespace DIV\services{

    if( ! defined( 'ABSPATH' ) ) exit;
    
    if ( ! class_exists( 'helper' ) ) :

        /**
         * helper service class
         * @example    DIV\Services\helper::method()
         */
        class helper{
            var $dir = array();

            static $_reserved = array( 
                'attachment',
                'attachment_id',
                'author',
                'author_name',
                'calendar',
                'cat',
                'category',
                'category__and',
                'category__in',
                'category__not_in',
                'category_name',
                'comments_per_page',
                'comments_popup',
                'cpage',
                'day',
                'debug',
                'error',
                'exact',
                'feed',
                'hour',
                'link_category',
                'm',
                'minute',
                'monthnum',
                'more',
                'name',
                'nav_menu',
                'nopaging',
                'offset',
                'order',
                'orderby',
                'p',
                'page',
                'page_id',
                'paged',
                'pagename',
                'pb',
                'perm',
                'post',
                'post__in',
                'post__not_in',
                'post_format',
                'post_mime_type',
                'post_status',
                'post_tag',
                'post_type',
                'posts',
                'posts_per_archive_page',
                'posts_per_page',
                'preview',
                'robots',
                's',
                'search',
                'second',
                'sentence',
                'showposts',
                'static',
                'subpost',
                'subpost_id',
                'tag',
                'tag__and',
                'tag__in',
                'tag__not_in',
                'tag_id',
                'tag_slug__and',
                'tag_slug__in',
                'taxonomy',
                'tb',
                'term',
                'type',
                'w',
                'withcomments',
                'withoutcomments',
                'year'
            );

            function __construct() {
                # Determine the full path to the this folder
                $this->_determine_custom_dir( dirname( __FILE__ ) );
            }

            /**
             * Beautifies a string. Capitalize words and remove underscores
             * @author     Gijs Jorissen
             *
             * @param      string $string
             * @return     string
             */
            static function beautify( $string ) {
              return apply_filters( 'div_beautify', ucwords( str_replace( '_', ' ', $string ) ) );
            }


            /**
             * Uglifies a string. Remove underscores and lower strings
             * @author     Gijs Jorissen
             *
             * @param      string $string
             * @return     string
             */
            static function uglify( $string ) {
              return apply_filters( 'div_uglify', str_replace( '-', '_', sanitize_title( $string ) ) );
            }

            /**
             * Slugifies a string. Adding underscores and lower strings
             * @author     Nick Worth
             *
             * @param      string  $string
             * @return     string
             */
            static public function slugify($text) { 
              # Replace non letter or digits by -
              $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

              # Trim
              $text = trim($text, '-');

              # Transliterate
              $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

              # Lowercase
              $text = strtolower($text);

              # Remove unwanted characters
              $text = preg_replace('~[^-\w]+~', '', $text);

              if (empty($text)) return 'n-a';

              return apply_filters( 'div_sluglify', $text );
            }

            /**
             * Removes slug format from a string. Capitalize words and remove
             * underscores and/or hyphens
             * @author     Nick Worth
             *
             * @param      string  $string
             * @return     string
             */
            static function unslugify( $string ) {
              # Remove hyphens
              $text = str_replace( '-', ' ', $string);

              # Remove underscores
              $text = str_replace( '_', ' ', $text);
              
              # Remove underscores
              $text = ucwords($text);
              
              return apply_filters( 'unslugify', $text );
            }

            /**
             * Makes a word plural
             * @author     Gijs Jorissen
             *
             * @param      string  $string
             * @return     string
             */
            static function pluralize( $string ) {
              $plural = array(
                array( '/(quiz)$/i',               "$1zes"   ),
                array( '/^(ox)$/i',                "$1en"    ),
                array( '/([m|l])ouse$/i',          "$1ice"   ),
                array( '/(matr|vert|ind)ix|ex$/i', "$1ices"  ),
                array( '/(x|ch|ss|sh)$/i',         "$1es"    ),
                array( '/([^aeiouy]|qu)y$/i',      "$1ies"   ),
                array( '/([^aeiouy]|qu)ies$/i',    "$1y"     ),
                array( '/(hive)$/i',               "$1s"     ),
                array( '/(?:([^f])fe|([lr])f)$/i', "$1$2ves" ),
                array( '/sis$/i',                  "ses"     ),
                array( '/([ti])um$/i',             "$1a"     ),
                array( '/(buffal|tomat)o$/i',      "$1oes"   ),
                array( '/(bu)s$/i',                "$1ses"   ),
                array( '/(alias|status)$/i',       "$1es"    ),
                array( '/(octop|vir)us$/i',        "$1i"     ),
                array( '/(ax|test)is$/i',          "$1es"    ),
                array( '/s$/i',                    "s"       ),
                array( '/$/',                      "s"       )
              );

              $irregular = array(
                array( 'move',   'moves'    ),
                array( 'sex',    'sexes'    ),
                array( 'child',  'children' ),
                array( 'man',    'men'      ),
                array( 'person', 'people'   )
              );

              $uncountable = array( 
                'sheep', 
                'fish',
                'series',
                'species',
                'money',
                'rice',
                'information',
                'equipment'
              );

              # Save time if string in uncountable
              if ( in_array( strtolower( $string ), $uncountable ) )
                return apply_filters( 'div_pluralize', $string );

              # Check for irregular words
              foreach ( $irregular as $noun ) {
                if ( strtolower( $string ) == $noun[0] )
                  return apply_filters( 'div_pluralize', $noun[1] );
              }

              # Check for plural forms
              foreach ( $plural as $pattern ) {
                if ( preg_match( $pattern[0], $string ) )
                  return apply_filters( 'div_pluralize', preg_replace( $pattern[0], $pattern[1], $string ) );
              }

              # Return if noting found
              return apply_filters( 'div_pluralize', $string );
            }

            /**
             * Singularizes English nouns
             * @author     Gijs Jorissen
             *
             * @param      string  $word        English noun to singularize
             * @return     string  $singular
             */
            static function singularize($word) {
                $singular = array (
                '/(quiz)zes$/i' => '\1',
                '/(matr)ices$/i' => '\1ix',
                '/(vert|ind)ices$/i' => '\1ex',
                '/^(ox)en/i' => '\1',
                '/(alias|status)es$/i' => '\1',
                '/([octop|vir])i$/i' => '\1us',
                '/(cris|ax|test)es$/i' => '\1is',
                '/(shoe)s$/i' => '\1',
                '/(o)es$/i' => '\1',
                '/(bus)es$/i' => '\1',
                '/([m|l])ice$/i' => '\1ouse',
                '/(x|ch|ss|sh)es$/i' => '\1',
                '/(m)ovies$/i' => '\1ovie',
                '/(s)eries$/i' => '\1eries',
                '/([^aeiouy]|qu)ies$/i' => '\1y',
                '/([lr])ves$/i' => '\1f',
                '/(tive)s$/i' => '\1',
                '/(hive)s$/i' => '\1',
                '/([^f])ves$/i' => '\1fe',
                '/(^analy)ses$/i' => '\1sis',
                '/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => '\1\2sis',
                '/([ti])a$/i' => '\1um',
                '/(n)ews$/i' => '\1ews',
                '/s$/i' => '',
                );

                $uncountable = array('equipment', 'information', 'rice', 'money', 'species', 'series', 'fish', 'sheep', "press");

                $irregular = array(
                'person' => 'people',
                'man' => 'men',
                'child' => 'children',
                'sex' => 'sexes',
                'move' => 'moves');

                $lowercased_word = strtolower($word);
                foreach ($uncountable as $_uncountable) {
                    if(substr($lowercased_word,(-1*strlen($_uncountable))) == $_uncountable){
                        return apply_filters( 'div_singularize', $word );
                    }
                }

                foreach ($irregular as $_plural=> $_singular){
                    if (preg_match('/('.$_singular.')$/i', $word, $arr)) {
                        return apply_filters( 'div_singularize', preg_replace('/('.$_singular.')$/i', substr($arr[0],0,1).substr($_plural,1), $word) );
                    }
                }

                foreach ($singular as $rule => $replacement) {
                    if (preg_match($rule, $word)) {
                        return apply_filters( 'div_singularize', preg_replace($rule, $replacement, $word) );
                    }
                }

                return apply_filters( 'div_singularize', $word );
            }

            /**
             * Singularizes the slug
             * @author     Gijs Jorissen
             *
             * @param      string  $word        English noun to singularize
             * @return     string  $singular
             */
            static function singularize_slug($word) {
              return self::singularize(self::slugify($word));
            }

            /**
             * CamelCase String
             * @author     Nick Worth
             * @link       uncamelcaser: via http://www.paulferrett.com/2009/php-camel-case-functions/
             *
             * @param      string  $string
             * @return     string  $camelCaseString.
             */
            static function camel_case($str) {
              $str[0] = strtolower($str[0]);
              $func = create_function('$c', 'return "_" . strtolower($c[1]);');
              return apply_filters( 'div_camel_case', preg_replace_callback('/([A-Z])/', $func, $str) );
            }
            
            /**
             * Truncate String
             * @author     Nick Worth
             *
             * @param      string  $string
             * @param      number  $truncated
             * @return     string  $truncated_text
             */
            static function truncate($string, $truncated=100) {
              $parts = preg_split('/([\s\n\r]+)/', $string, null, PREG_SPLIT_DELIM_CAPTURE);
              $parts_count = count($parts);

              $length = 0;
              $last_part = 0;
              for (; $last_part < $parts_count; ++$last_part) {
                $length += strlen($parts[$last_part]);
                if ($length > $truncated) { break; }
              }

              return apply_filters( 'div_truncate', implode(array_slice($parts, 0, $last_part)) );
            }

            /**
             * Checks if the callback is a Wordpress callback
             * So, if the class, method and/or function exists. If so, call it.
             * If it doesn't use the data array.
             * @author     Gijs Jorissen
             * 
             * @param      string|array  $callback
             * @return     boolean
             */
            static function is_wp_callback( $callback ) {
                return ( ! is_array( $callback ) ) || ( is_array( $callback ) && ( ( isset( $callback[1] ) && ! is_array( $callback[1] ) && method_exists( $callback[0], $callback[1] ) ) || ( isset( $callback[0] ) && ! is_array( $callback[0] ) && class_exists( $callback[0] ) ) ) );
            }

            /**
             * Check if the term is reserved by Wordpress
             * @author     Gijs Jorissen
             * 
             * @param      string   $term
             * @return     boolean
             */
            static function is_reserved_term( $term ) {
                if( ! in_array( $term, self::$_reserved ) ) return false;

                return new WP_Error( 'reserved_term_used', __( 'Use of a reserved term.', 'divlibrary' ) );
            }

            /**
             * Return the first day of the Week/Month/Quarter/Year that the 
             * current/provided date falls within
             * @author     Nick Worth
             * @link       http://davidhancock.co/2013/11/get-the-firstlast-day-of-a-week-month-quarter-or-year-in-php/
             *
             * @param      string    $period  The period to find the first day of. ('year', 'quarter', 'month', 'week')
             * @param      DateTime  $date    The date to use instead of the current date
             * @return     DateTime
             * @throws     InvalidArgumentException
             */
            static function firstDayOf($period, DateTime $date = null) {
                $period = strtolower($period);
                $validPeriods = array('year', 'quarter', 'month', 'week');

                if ( ! in_array($period, $validPeriods))
                    throw new InvalidArgumentException('Period must be one of: ' . implode(', ', $validPeriods));

                $newDate = ($date === null) ? new DateTime() : clone $date;

                switch ($period) {
                    case 'year':
                        $newDate->modify('first day of january ' . $newDate->format('Y'));
                        break;
                    case 'quarter':
                        $month = $newDate->format('n') ;

                        if ($month < 4) {
                            $newDate->modify('first day of january ' . $newDate->format('Y'));
                        } elseif ($month > 3 && $month < 7) {
                            $newDate->modify('first day of april ' . $newDate->format('Y'));
                        } elseif ($month > 6 && $month < 10) {
                            $newDate->modify('first day of july ' . $newDate->format('Y'));
                        } elseif ($month > 9) {
                            $newDate->modify('first day of october ' . $newDate->format('Y'));
                        }
                        break;
                    case 'month':
                        $newDate->modify('first day of this month');
                        break;
                    case 'week':
                        $newDate->modify(($newDate->format('w') === '0') ? 'monday last week' : 'monday this week');
                        break;
                }

                return $newDate;
            }

            /**
             * Return the last day of the Week/Month/Quarter/Year that the current/provided date falls within
             * @author     Nick Worth
             *
             * @param      string    $period  The period to find the last day of. ('year', 'quarter', 'month', 'week')
             * @param      DateTime  $date    The date to use instead of the current date
             * @return     DateTime
             * @throws     InvalidArgumentException
             */
            static function lastDayOf($period, DateTime $date = null) {
                $period = strtolower($period);
                $validPeriods = array('year', 'quarter', 'month', 'week');

                if ( ! in_array($period, $validPeriods))
                    throw new InvalidArgumentException('Period must be one of: ' . implode(', ', $validPeriods));

                $newDate = ($date === null) ? new DateTime() : clone $date;

                switch ($period) {
                    case 'year':
                        $newDate->modify('last day of december ' . $newDate->format('Y'));
                        break;
                    case 'quarter':
                        $month = $newDate->format('n') ;

                        if ($month < 4) {
                            $newDate->modify('last day of march ' . $newDate->format('Y'));
                        } elseif ($month > 3 && $month < 7) {
                            $newDate->modify('last day of june ' . $newDate->format('Y'));
                        } elseif ($month > 6 && $month < 10) {
                            $newDate->modify('last day of september ' . $newDate->format('Y'));
                        } elseif ($month > 9) {
                            $newDate->modify('last day of december ' . $newDate->format('Y'));
                        }
                        break;
                    case 'month':
                        $newDate->modify('last day of this month');
                        break;
                    case 'week':
                        $newDate->modify(($newDate->format('w') === '0') ? 'now' : 'sunday this week');
                        break;
                }

                return $newDate;
            }

            /**
             * Recursive method to determine the path to the custom folder
             * @author     Gijs Jorissen
             *
             * @param      string  $path
             * @return     string
             */
            static function _determine_custom_dir( $path = __FILE__ ) {
                $path = dirname( $path );
                $path = str_replace( '\\', '/', $path );
                $explode_path = explode( '/', $path );

                $current_dir = $explode_path[count( $explode_path ) - 1];
                array_push( $this->dir, $current_dir );

                if( $current_dir == 'wp-content' ) {
                    # Build new paths
                    $path = '';
                    $directories = array_reverse( $this->dir );

                    foreach( $directories as $dir ) {
                        $path = $path . '/' . $dir;
                    }

                    $this->dir = $path;
                } else {
                    return $this->_determine_custom_dir( $path );
                }
            } 

            /**
             * Return the output of an include file to a string variable
             * @author     Jeremy Kauffman
             * @link       http://stackoverflow.com/a/2150215/1058371
             * 
             * @param      string  $file  path to the file you want to encapsulate
             * @return     string
             */
            static function return_output($file){
                ob_start();
                include $file;
                return ob_get_clean();
            }

            /**
             * Returns whether a submitted string is a valid email address
             * @author  Michael Rushton
             *
             * @param      string  $email
             */
            static function IsEmail($email) {
                return filter_var($email, FILTER_VALIDATE_EMAIL);
            }

            /**
             * Get the current url (including parameters)
             *
             * @return     string
             */
            static function currentURL(){
                $protocol = strpos(strtolower($_SERVER['SERVER_PROTOCOL']),'https') === FALSE ? 'http' : 'https';
                $host     = $_SERVER['HTTP_HOST'];
                $script   = $_SERVER['SCRIPT_NAME'];
                $params   = $_SERVER['QUERY_STRING'];

                return $protocol . '://' . $host . $script . '?' . $params;
            }

            /**
             * Encrypt a given string with AES-256-CBC
             * NOTE: AES is used by the U.S. gov't to encrypt top secret documents
             *  
             * @param      string $data
             * @return     string
             */
            static function encrypt($data){
                # Setup the initialization vector
                $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
                $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
                # Encrypt the data
                $encryptedMessage = openssl_encrypt($data, "AES-256-CBC", self::getEncryptKey(), 0, $iv);
                return base64_encode($encryptedMessage.'~~~'.$iv);
            }

            /**
             * Decrypt a given string with AES-256-CBC
             * NOTE: AES is used by the U.S. gov't to encrypt top secret documents
             *
             * @param      string $encrypted
             * @return     string
             */
            static function decrypt($encrypted){
                # Separate the encrypted data from the initialization vector ($iv)
                $data = explode('~~~', base64_decode($encrypted));
                # Decrypt the data
                if(isset($data[1])){
                    $decryptedMessage = openssl_decrypt($data[0], "AES-256-CBC", self::getEncryptKey(), 0, $data[1]);
                    return $decryptedMessage;
                } else {
                    return $encrypted;
                }
            }

            private static function getEncryptKey(){
                $key = get_option( 'div_encrypt_key' );
                if($key==''){
                    $encryption_key = base64_encode(openssl_random_pseudo_bytes(32));
                    $key = add_option( 'div_encrypt_key', $encryption_key, '', 'no' );
                    return $key;
                } else {
                    return $key;
                }
            }

        }

    endif;

}

?>