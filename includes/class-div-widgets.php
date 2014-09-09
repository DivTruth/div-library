<?php
/**
 * Div Library Widget Class
 *
 * @version 	1.0
 * @package 	div_library/Classes
 * @category 	Class
 * @author 		Div Blend Team
 * @extends 	WP_Widget
 */
abstract class DIV_Widget extends WP_Widget {

	public $widget_cssclass;
	public $widget_description;
	public $widget_id;
	public $widget_name;
	public $settings;
	public $widget_template = "/templates/widgets/{widget}.php";

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->library = div_library::instance();

		$widget_ops = array(
			'classname'   => $this->widget_cssclass,
			'description' => $this->widget_description
		);

		$this->WP_Widget( $this->widget_id, $this->widget_name, $widget_ops );

		add_action( 'save_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_widget_scripts') );
	}

	/**
	 * enqueue scripts on widgets.php function.
	 */
	function admin_widget_scripts( $hook ) {
		if ( 'widgets.php' == $hook ) {
			wp_enqueue_style( 'div_admin_styles', $this->library->path['css_url'].'div_widget.css' );
			wp_enqueue_script( 'jquery-ui-tooltip' );
		}
	}

	/**
	 * get_cached_widget function.
	 */
	function get_cached_widget( $args ) {
		$cache = wp_cache_get( $this->widget_id, 'widget' );

		if ( ! is_array( $cache ) )
			$cache = array();

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return true;
		}

		return false;
	}

	/**
	 * Cache the widget
	 */
	public function cache_widget( $args, $content ) {
		$cache[ $args['widget_id'] ] = $content;
		wp_cache_set( $this->widget_id, $cache, 'widget' );
	}

	/**
	 * Flush the cache
	 * @return [type]
	 */
	public function flush_widget_cache() {
		wp_cache_delete( $this->widget_id, 'widget' );
	}

	/**
	 * update function.
	 *
	 * @see WP_Widget->update
	 * @access public
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		if ( ! $this->settings )
			return $instance;

		foreach ( $this->settings as $key => $setting ) {
			if ( isset( $new_instance[ $key ] ) ) {
				$instance[ $key ] = sanitize_text_field( $new_instance[ $key ] );
			} elseif ( 'checkbox' === $setting['type'] ) {
				$instance[ $key ] = 0;
			}
		}

		$this->flush_widget_cache();
		return $instance;
	}

	/**
	 * form function.
	 *
	 * @see WP_Widget->form
	 * @access public
	 * @param array $instance
	 * @return void
	 */
	function form( $instance ) {
		if ( ! $this->settings )
			return;

		$widget = strtolower( $this->widget_id );
		$widget = str_replace( 'divlibrary_widget_', '', $widget );
		$file = str_replace( '_', '-', $widget ) . '.php';
		
		# TODO: Complete self-generating documentation (https://app.asana.com/0/7877374858636/14410061109225)
		$docs = '
		<section class="divlibrary-documentation" style="margin: 5px 0;">
			<a href="#" class="button" onclick="jQuery(this).next().toggle(); return false;">Documentation</a>
			<aside  style="display:none; background:#eee; margin: 5px 0px; padding: 5px 10px;">
				<h3 style="margin-bottom:5px;">Modify Widget Template</h3>
				To edit the html output of this widget, copy the <strong style="margin: 5px;padding:5px;border:1px solid #c03f3f;background:#da2c2c;color: #fff;cursor:pointer;display:inline-block;" title="'.$this->widget_template.'">TEMPLATE FILE</strong> => <strong style="margin: 5px;padding:5px;border:1px solid #c03f3f;background:#da2c2c;color: #fff;cursor:pointer; display:inline-block;" title="'.basename(get_stylesheet_directory_uri()).'/'.$this->library->template_path().$file.'">TEMPLATE PATH</strong>

				<h3 style="margin-bottom:5px;">Varibles</h3>
				<ul style="list-style-type: disc;padding-left: 30px;">
		';

		foreach ( $this->settings as $key => $setting ) {

			$value   = isset( $instance[ $key ] ) ? $instance[ $key ] : $setting['std'];

			switch ( $setting['type'] ) {
				case "text" :
					?>
					<p class="text_field">
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" type="text" value="<?php echo esc_attr( $value ); ?>" />
					</p>
					<?php
				break;
				case "textarea" :
					?>
					<p class="textarea_field">
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $key ) ); ?>" rows="5" type="text"><?php echo esc_attr( $value ); ?></textarea>
					</p>
					<?php
				break;
				case "number" :
					?>
					<p class="number_field">
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" type="number" step="<?php echo esc_attr( $setting['step'] ); ?>" min="<?php echo esc_attr( $setting['min'] ); ?>" max="<?php echo esc_attr( $setting['max'] ); ?>" value="<?php echo esc_attr( $value ); ?>" />
					</p>
					<?php
				break;
				case "select" :
					?>
					<p class="select_field">
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>">
							<?php foreach ( $setting['options'] as $option_key => $option_value ) : ?>
								<option value="<?php echo esc_attr( $option_key ); ?>" <?php selected( $option_key, $value ); ?>><?php echo esc_html( $option_value ); ?></option>
							<?php endforeach; ?>
						</select>
					</p>
					<?php
				break;
				case "checkbox" :
					?>
					<p class="checkbox_field">
						<input id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $key ) ); ?>" type="checkbox" value="1" <?php checked( $value, 1 ); ?> />
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
					</p>
					<?php
				break;
				case "image" :
					?>
					<p class="image_field">
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<?php pco_image_field( $this, $instance, array( 'title' => $setting['label'], 'update' => 'Select an Image', 'field' => $key ) ); ?>
					</p>
					<?php
				break;

			}
			$docs .= '<li><strong>$'.$key.'</strong></li>';
		}

		$docs .= '</ul></aside></section>';
		echo apply_filters( 'div_widget_documentation', $docs );
	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	public function widget( $args, $instance ) {
		$file = str_replace( '_', '-', strtolower( $this->widget_id ) ) . '.php';

		$args['instance'] = $instance;
		div_get_template( $file, $args, $this->template['path'], $this->template['default'] );
	}

}