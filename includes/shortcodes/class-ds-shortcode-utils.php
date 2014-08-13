<?php
/**
 * Div Starter Utils Shortcodes
 * These utility shortcodes provide some common use case scenarios for shortcodes
 *
 * @version     1.0
 * @package 	Div-Starter/Shortcodes/Utils
 * @category 	Shortcodes
 * @author 		Div Blend Team
 */
class DS_Shortcode_Utils {

	/**
	 * GOOGLE PDF VIEWER
	 * @example [pdf width="520px" height="700px" url="http://static.fsf.org/common/what-is-fs-new.pdf"]
	 * 
	 * @author Nick Worth
	 * @since 1.0
	 * @param <string> $width
	 * @param <string> $height
	 * @link http://wp.smashingmagazine.com/2012/05/01/wordpress-shortcodes-complete-guide/
	 **/
	public static function pdf( $atts ) {
		extract(shortcode_atts(array(
	    	'width'	 	=> '640',
	       	'height' 	=> '480',
	       	'url'		=> '#'
	    ), $atts));
   		
   		echo '<iframe src="http://docs.google.com/viewer?url=' . $url . '&embedded=true" style="width:' .$width. '; height:' .$height. ';">Your browser does not support iframes</iframe>';
	}

	/**
	 * GOOGLE CHARTS
	 * @example [chart type="pie" title="Example Pie Chart" data="41.12,32.35,21.52,5.01" labels="First+Label|Second+Label|Third+Label|Fourth+Label" background_color="FFFFFF" colors="D73030,329E4A,415FB4,DFD32F" size="450x180"]
	 * 
	 * @author Nick Worth
	 * @since 1.0
	 * @param <string> $width
	 * @param <string> $height
	 * @link http://wp.smashingmagazine.com/2012/05/01/wordpress-shortcodes-complete-guide/
	 **/
	public static function chart( $atts ) {
		extract(shortcode_atts(array(
	    	'data' => '',
			'chart_type' => 'pie',
			'title' => 'Chart',
			'labels' => '',
			'size' => '640x480',
			'background_color' => 'FFFFFF',
			'colors' => '',
	    ), $atts));

		switch ($chart_type) {
			case 'line' :
				$chart_type = 'lc';
				break;
			case 'pie' :
				$chart_type = 'p3';
				break;
			default :
				break;
		}

		$attributes = '';
		$attributes .= '&chd=t:'.$data.'';
		$attributes .= '&chtt='.$title.'';
		$attributes .= '&chl='.$labels.'';
		$attributes .= '&chs='.$size.'';
		$attributes .= '&chf='.$background_color.'';
		$attributes .= '&chco='.$colors.'';

		echo '<img title="'.$title.'" src="http://chart.apis.google.com/chart?cht='.$chart_type.''.$attributes.'" alt="'.$title.'" />';
	}
}