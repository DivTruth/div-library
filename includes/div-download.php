<?php 
/**
 * Plugin name: Download Button Shortcode
 * Plugin URI: http://blog.ppfeufer.de/wordpress-button-fuer-downloads-erzeugen/
 * Author: H.-Peter Pfeufer (modified by Nick Worth)
 * Author URI: http://ppfeufer.de
 * Version: 2.0-modified
 * Description: Add a shortcode to your wordpress for a nice downloadbutton.
 *             [download url="" title="" desc="" type="" align=""]
 *            Graphics made by: <a href="http://kkoepke.de">Kai Köpke</a>. If you made your own graphic for this button, 
 *            feel free to write it in the comments under http://blog.ppfeufer.de/wordpress-button-fuer-downloads-erzeugen/.
 */
if($_REQUEST['file']){
	downloadFile($_REQUEST['file']);
}
function downloadFile($file) {
    $mm_type = "application/octet-stream";

    // IE 6.0 fix for SSL
    // SRC http://ca3.php.net/header
    // Brandon K [ brandonkirsch uses gmail ] 25-Apr-2007 03:34
    header('Cache-Control: maxage=3600'); //Adjust maxage appropriately
    header('Pragma: public');

    header("Cache-Control: public, must-revalidate");
    header("Pragma: hack");
    header("Content-Type: " . $mm_type);
    header("Content-Length: " . (string)(filesize($file)));
    header('Content-Disposition: attachment; filename="'.basename($file).'"');
    header("Content-Transfer-Encoding: binary");

    readfile($file);
}

?>