<?php
/**
 * Banner Widget
 *
 * Easily add a banner image and link
 *
 * @author 		DivStarter
 * @category 	Widgets
 * @package 	DivStarter/Widgets
 * @version 	1.0
 * @extends 	DIV_Widget
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Div_Widget_Banner extends DIV_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_cssclass    = 'divstarter widget_banner';
		$this->widget_description = __( "Add a banner image in the sidebar.", 'divstarter' );
		$this->widget_id          = 'divstarter_widget_banner';
		$this->widget_name        = __( 'DIV: Banner', 'divstarter' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => __( 'Advertisement', 'divstarter' ),
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
		);
		parent::__construct();
	}

}

register_widget( 'Div_Widget_Banner' );