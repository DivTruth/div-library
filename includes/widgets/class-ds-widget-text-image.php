<?php
/**
 * Div Starter Text/Image Widget
 *
 * A text widget with an image
 *
 * @version 	1.0
 * @package 	DivStarter/Widgets
 * @category 	Widgets
 * @author 		Div Blend Team
 * @extends 	DS_Widget
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class DS_Widget_Text_Image extends DS_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_cssclass    = 'divstarter widget_text_image';
		$this->widget_description = __( "Simple text widget with an image.", 'divstarter' );
		$this->widget_id          = 'divstarter_widget_text_image';
		$this->widget_name        = __( 'DS: Text & Image', 'divstarter' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => __( '', 'divstarter' ),
				'label' => __( 'Title:', 'divstarter' )
			),
			'image_id' => array(
				'type'  => 'image',
				'std'   => 0,
				'label' => __( 'Select a Banner Image:', 'divstarter' )
			),
			'link'  => array(
				'type'  => 'text',
				'std'   => __( 'http://', 'divstarter' ),
				'label' => __( 'Set a link (optional):', 'divstarter' )
			),
			'new_window' => array(
				'type'  => 'checkbox',
				'std'   => 1,
				'label' => __( 'Open in a new window?', 'divstarter' )
			),
			'content'  => array(
				'type'  => 'textarea',
				'std'   => __( '', 'divstarter' ),
				'label' => __( 'Text:', 'divstarter' )
			),
		);
		parent::__construct();
	}

}

register_widget( 'DS_Widget_Text_Image' );