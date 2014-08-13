<?php
/**
 * DS_Helper class.
 * General class with main methods and helper methods
 *
 * @class       DS_Helper
 * @version     1.0
 * @package     DivStarter/Classes
 * @category    Class
 * @author      Div Blend Team
 */

if( ! defined( 'ABSPATH' ) ) exit;

class DS_Helper{
    var $dir = array();

    static $_reserved = array( 'attachment', 'attachment_id', 'author', 'author_name', 'calendar', 'cat', 'category','category__and', 'category__in', 'category__not_in', 
    'category_name', 'comments_per_page', 'comments_popup', 'cpage', 'day', 'debug', 'error', 'exact', 'feed', 'hour', 'link_category', 
    'm', 'minute', 'monthnum', 'more', 'name', 'nav_menu', 'nopaging', 'offset', 'order', 'orderby', 'p', 'page', 'page_id', 'paged', 'pagename', 'pb', 
    'perm', 'post', 'post__in', 'post__not_in', 'post_format', 'post_mime_type', 'post_status', 'post_tag', 'post_type', 
    'posts', 'posts_per_archive_page', 'posts_per_page', 'preview', 'robots', 's', 'search', 'second', 'sentence', 'showposts', 
    'static', 'subpost', 'subpost_id', 'tag', 'tag__and', 'tag__in','tag__not_in', 'tag_id', 'tag_slug__and', 'tag_slug__in', 'taxonomy', 
    'tb', 'term', 'type', 'w', 'withcomments', 'withoutcomments', 'year' );

    function __construct() {
        // Determine the full path to the this folder
        $this->_determine_custom_dir( dirname( __FILE__ ) );
    }

    /**
     * Beautifies a string. Capitalize words and remove underscores
     *
     * @param string $string
     * @return string
     *
     * @author Gijs Jorissen
     * @since 1.0
     *
     */
    static function beautify( $string ) {
      return apply_filters( 'ds_beautify', ucwords( str_replace( '_', ' ', $string ) ) );
    }


    /**
     * Uglifies a string. Remove underscores and lower strings
     *
     * @param string $string
     * @return string
     *
     * @author Gijs Jorissen
     * @since 1.0
     *
     */
    static function uglify( $string ) {
      return apply_filters( 'ds_uglify', str_replace( '-', '_', sanitize_title( $string ) ) );
    }

    /**
     * Slugifies a string. Adding underscores and lower strings
     *
     * @param string $string
     * @return string
     *
     * @author Nick Worth
     * @since 1.0
     *
     */
    static public function slugify($text) { 
      // replace non letter or digits by -
      $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

      // trim
      $text = trim($text, '-');

      // transliterate
      $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

      // lowercase
      $text = strtolower($text);

      // remove unwanted characters
      $text = preg_replace('~[^-\w]+~', '', $text);

      if (empty($text))
      {
        return 'n-a';
      }

      return apply_filters( 'ds_sluglify', $text );
    }


    /**
     * Makes a word plural
     *
     * @param string $string
     * @return string
     *
     * @author Gijs Jorissen
     * @since 1.0
     *
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

      // Save time if string in uncountable
      if ( in_array( strtolower( $string ), $uncountable ) )
        return apply_filters( 'ds_pluralize', $string );

      // Check for irregular words
      foreach ( $irregular as $noun ) {
        if ( strtolower( $string ) == $noun[0] )
          return apply_filters( 'ds_pluralize', $noun[1] );
      }

      // Check for plural forms
      foreach ( $plural as $pattern ) {
        if ( preg_match( $pattern[0], $string ) )
          return apply_filters( 'ds_pluralize', preg_replace( $pattern[0], $pattern[1], $string ) );
      }

      // Return if noting found
      return apply_filters( 'ds_pluralize', $string );
    }

    /**
    * Singularizes English nouns.
    *
    * @access public
    * @static
    * @param  string    $word    English noun to singularize
    * @return string Singular noun.
    *
    * @author Gijs Jorissen
    * @since 1.0
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
                return apply_filters( 'ds_singularize', $word );
            }
        }

        foreach ($irregular as $_plural=> $_singular){
            if (preg_match('/('.$_singular.')$/i', $word, $arr)) {
                return apply_filters( 'ds_singularize', preg_replace('/('.$_singular.')$/i', substr($arr[0],0,1).substr($_plural,1), $word) );
            }
        }

        foreach ($singular as $rule => $replacement) {
            if (preg_match($rule, $word)) {
                return apply_filters( 'ds_singularize', preg_replace($rule, $replacement, $word) );
            }
        }

        return apply_filters( 'ds_singularize', $word );
    }

    /**
    * Singularizes the slug.
    *
    * @access public
    * @static
    * @param  string    $word    English noun to singularize
    * @return string Singular noun.
    *
    * @author Gijs Jorissen
    * @since 1.0
    */
    static function singularize_slug($word) {
      return self::singularize(self::slugify($word));
    }

    /**
    * CamelCase String.
    *
    * @access public
    * @param <STRING> $string
    * @return <STRING> $camelCaseString.
    * @link uncamelcaser: via http://www.paulferrett.com/2009/php-camel-case-functions/
    *
    * @author Nick Worth
    * @since 1.0
    */
    static function camel_case($str) {
      $str[0] = strtolower($str[0]);
      $func = create_function('$c', 'return "_" . strtolower($c[1]);');
      return apply_filters( 'ds_camel_case', preg_replace_callback('/([A-Z])/', $func, $str) );
    }

    /**
     * Checks if the callback is a Wordpress callback
     * So, if the class, method and/or function exists. If so, call it.
     * If it doesn't use the data array.
     * 
     * @param string|array    $callback
     * @return  boolean
     *
     * @author  Gijs Jorissen
     * @since   1.5
     * 
     */
    static function is_wp_callback( $callback ) {
      return ( ! is_array( $callback ) ) || ( is_array( $callback ) && ( ( isset( $callback[1] ) && ! is_array( $callback[1] ) && method_exists( $callback[0], $callback[1] ) ) || ( isset( $callback[0] ) && ! is_array( $callback[0] ) && class_exists( $callback[0] ) ) ) );
    }

    /**
     * Check if the term is reserved by Wordpress
     * 
     * @param   string      $term
     * @return  boolean
     *
     * @author  Gijs Jorissen
     * @since   1.6
     * 
     */
    static function is_reserved_term( $term ) {
        if( ! in_array( $term, self::$_reserved ) ) return false;

        return new WP_Error( 'reserved_term_used', __( 'Use of a reserved term.', 'divstarter' ) );
    }

    /**
    * First Date
    * Return the first day of the Week/Month/Quarter/Year that the
    * current/provided date falls within
    *
    * @access public
    * @param string   $period The period to find the first day of. ('year', 'quarter', 'month', 'week')
    * @param DateTime $date   The date to use instead of the current date
    * @return DateTime
    * @throws InvalidArgumentException
    * @link http://davidhancock.co/2013/11/get-the-firstlast-day-of-a-week-month-quarter-or-year-in-php/
    *
    * @author Nick Worth
    * @since 1.0
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
     * Last Date
     * Return the last day of the Week/Month/Quarter/Year that the
     * current/provided date falls within
     *
     * @param string   $period The period to find the last day of. ('year', 'quarter', 'month', 'week')
     * @param DateTime $date   The date to use instead of the current date
     * @return DateTime
     * @throws InvalidArgumentException
     *
     * @author Nick Worth
     * @since 1.0
     */
    static function lastDayOf($period, DateTime $date = null) {
        $period = strtolower($period);
        $validPeriods = array('year', 'quarter', 'month', 'week');

        if ( ! in_array($period, $validPeriods))
            throw new InvalidArgumentException('Period must be one of: ' . implode(', ', $validPeriods));

        $newDate = ($date === null) ? new DateTime() : clone $date;

        switch ($period)
        {
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
     *
     * @param string $path
     * @return string
     *
     * @author Gijs Jorissen
     * @since 0.4.1
     *
     */
    function _determine_custom_dir( $path = __FILE__ )
    {
        $path = dirname( $path );
        $path = str_replace( '\\', '/', $path );
        $explode_path = explode( '/', $path );

        $current_dir = $explode_path[count( $explode_path ) - 1];
        array_push( $this->dir, $current_dir );

        if( $current_dir == 'wp-content' )
        {
            // Build new paths
            $path = '';
            $directories = array_reverse( $this->dir );

            foreach( $directories as $dir )
            {
                $path = $path . '/' . $dir;
            }

            $this->dir = $path;
        }
        else
        {
            return $this->_determine_custom_dir( $path );
        }
    }       
}

?>
