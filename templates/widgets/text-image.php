<?php
/**
 * Text Image Widget Template
 *
 * @author 		Div Truth
 * @package 	DivStarter/Templates/Widgets
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

extract($args);
extract($instance);

echo $before_widget;
	
	$title  = apply_filters('widget_title', empty( $title ) ? __( '', 'divstarter' ) : $title );
	$target = ( $new_window == 1 ) ? "_blank" : "_self";
	$banner =  wp_get_attachment_image_src( $image_id, 'full' );

	if ( $title )
		echo $before_title . $title . $after_title;

	echo '<div class="widget_text_image_content">';

		echo '<div style="max-width:'.$banner[1].'px; max-height:'.$banner[2].'px; margin:auto;" class="widget_banner_content">';

			echo '<a href="'.$link.'" target="'.$target.'">
	    	    <img src="'.$banner[0].'" class="fit" alt="'.$link.'">
		    </a>';
			
		echo '</div>';

		echo '<p class="widget-content">'.$content.'</p>';
		
	echo '</div>';

echo $after_widget;