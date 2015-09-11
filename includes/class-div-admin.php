<?php
/**
 * DIV_Admin class.
 * Wordpress admin UI class with static helper methods
 *
 * @class       DIV_Admin
 * @package     div_library/Classes
 * @category    Class
 * @author      Div Blend Team
 */

if( ! defined( 'ABSPATH' ) ) exit;

class DIV_Admin{

    /**
     * Output markup for fields 
     *
     * @param array $fields
     * @param array $values
     * @param boolean $output
     * @return string
     *
     * @author Nick Worth
     * @since 1.0
     */
    static function output_fields( $fields, $values = array(), $output = true ) {
        $markup = '';
        foreach ( $fields as $field => $setting ) {
            $value = isset( $values[ $field ] ) ? $values[ $field ] : $setting['std'];

            switch ( $setting['type'] ) {
                case "text" :
                    $markup .= DIV_Admin::get_text_field($field, $value, $setting);
                break;
                case "textarea" :
                    $markup .= DIV_Admin::get_textarea_field($field, $value, $setting);
                break;
                case "number" :
                    $markup .= DIV_Admin::get_number_field($field, $value, $setting);
                break;
                case "select" :
                    $markup .= DIV_Admin::get_select_field($field, $value, $setting);
                break;
                case "checkbox" :
                    $markup .= DIV_Admin::get_checkbox_field($field, $value, $setting);
                break;
            }
        }

        if($output)
            _e($markup);
        else 
            return $markup;
    }

    /**
     * Markup for text field 
     *
     * @param string $name
     * @param string $value
     * @param array $settings
     * @return string
     *
     * @author Nick Worth
     * @since 1.0
     */
    static function get_text_field($name, $value, $setting){
        $s = '<p class="text_field">';
            $s .= '<label for="'.$name.'">'.$setting['label'].'</label>';
            $s .= '<input class="widefat" id="'.esc_attr( $name ).'" name="'.$name.'" type="text" value="'.esc_attr( $value ).'" />';
        $s .= '</p>';
        return $s;
    }

    /**
     * Markup for textarea field 
     *
     * @param string $name
     * @param string $value
     * @param array $settings
     * @return string
     *
     * @author Nick Worth
     * @since 1.0
     */
    static function get_textarea_field($name, $value, $setting){
        $s = '<p class="textarea_field">';
            $s .= '<label for="'.$name.'">'.$setting['label'].'</label>';
            $s .= '<textarea class="widefat" id="'.esc_attr( $name ).'" name="'.esc_attr( $name ).'" rows="5" type="text">'.esc_attr( $value ).'</textarea>';
        $s .= '</p>';
        return $s;
    }

    /**
     * Markup for number field 
     *
     * @param string $name
     * @param string $value
     * @param array $settings
     * @return string
     *
     * @author Nick Worth
     * @since 1.0
     */
    static function get_number_field($name, $value, $setting){
        $s = '<p class="number_field">';
            $s .= '<label for="'.$name.'">'.$setting['label'].'</label>';
            $s .= '<input class="widefat" id="'.esc_attr( $name ).'" name="'.$name.'" type="number" step="'.esc_attr( $setting['step'] ).'" min="'.esc_attr( $setting['min'] ).'" max="'.esc_attr( $setting['max'] ).'" value="'.esc_attr( $value ).'" />';
        $s .= '</p>';
        return $s;
    }

    /**
     * Markup for select field 
     *
     * @param string $name
     * @param string $value
     * @param array $settings
     * @return string
     *
     * @author Nick Worth
     * @since 1.0
     */
    static function get_select_field($name, $value, $setting){
        $s = '<p class="select_field">';
            $s .= '<label for="'.$name.'">'.$setting['label'].'</label>';
            $s .= '<select class="widefat" id="'.esc_attr( $name ).'" name="'.$name.'">';
                foreach ( $setting['options'] as $option_key => $option_value ) :
                    $s .= '<option value="'.esc_attr( $option_key ).'" '.selected( $option_key, $value, false ).'>'.esc_html( $option_value ).'</option>';
                endforeach;
            $s .= '</select>';
        $s .= '</p>';
        return $s;
    }

    /**
     * Markup for checkbox field 
     *
     * @param string $name
     * @param string $value
     * @param array $settings
     * @return string
     *
     * @author Nick Worth
     * @since 1.0
     */
    static function get_checkbox_field($name, $value, $setting){
        $s = '<p class="checkbox_field">';
            $s .= '<input id="'.esc_attr( $name ).'" name="'.esc_attr( $name ).'" type="checkbox" value="1" '.checked( $value, 1, false ).' />';
            $s .= '<label for="'.$name.'">'.$setting['label'].'</label>';
        $s .= '</p>';
        return $s;
    }

}

?>
