<?php
/**
 * Download Shortcode
 *
 * These shortcode provide an icon specific buttong for downloading media files by URL
 * @example 	[download type="jpg" url="http://4.bp.blogspot.com/-E-uqTphmvlM/U36UVO-W4_I/AAAAAAACF2k/OPzg-bacCX0/s1600/google+glass.jpg" title="Google Glass" desc="What is next for Google Glass?" align="left" target="_blank"]
 *
 * @author 		H.-Peter Pfeufer (modified by Div Truth)
 * @category 	Shortcodes
 * @package 	DivStarter/Shortcodes/Download
 * @version     1.0
 * @credit 		Graphics made by: <a href="http://kkoepke.de">Kai Köpke</a>. If you made your own graphic for this button, 
 *            feel free to write it in the comments under http://blog.ppfeufer.de/wordpress-button-fuer-downloads-erzeugen/.
 */
class DIV_Shortcode_Download {

	/**
	 * Constructor
	 *
	 * @since 2.0
	 * @author ppfeufer
	 */
	function DIV_Shortcode_Download() {
		DIV_Shortcode_Download::construct_button();
	}

	function construct_button() {
		if(!is_admin()) {

			$js = '$(".download").click(function(e) { $(this).parents("form:first").submit(); e.preventDefault(); });';
			div_enqueue_js($js);
		    
		} // END if(!is_admin())

	} // END construct_button()

	/**
	 * Shortcode in HTML-Code umwandeln
	 * @param $atts
	 */
	public static function output($atts) {
		extract(shortcode_atts(array(
			'type' => '',
			'url' => '',
			'title' => '',
			'desc' => '',
			'align' => '',
			'target' => ''
		), $atts));

		$array_Downloadtypes = array(
			'pdf',
			'archive',
			'doc',
			'image',
			'audio',
			'video',
			'link'
		);

		/**
		 * If none URL is given, do not return any code.
		 */
		if(!$url) {
			return;
		} // END if(!$url)

		/**
		 * If no align, simply center the button.
		 */
		if(!$align) {
			$align = 'center';
		} // END if(!$align)

		/**
		 * Linktarget
		 */
		if($target) {
			$target = 'target="_' . $target . '"';
		} else {
			$target = '';
		} // END if($target)

		/**
		 * Autodetecting filetype of the given download.
		 * Only runs if no type is given in shortcode.
		 *
		 * @since 1.0
		 */
		if(!$type) {
			$var_sFiletype = strrchr($url, ".");

			switch($var_sFiletype) {
				case '.ez':
					$type = 'andrew-inset';
					break;
				case '.hqx':
					$type = 'mac-binhex40';
					break;
				case '.cpt':
					$type = 'mac-compactpro';
					break;
				case '.doc':
					$type = 'ms-word document';
					break;
				case '.bin':
				case '.dms':
				case '.lha':
				case '.lzh':
				case '.exe':
				case '.class':
				case '.so':
				case '.dll':
					$type = 'octet-stream';
					break;
				case '.oda':
					$type = 'oda';
					break;
				case '.pdf':
					$type = 'pdf';
					break;
				case '.ai':
				case '.eps':
				case '.ps':
					$type = 'postscript';
					break;
				case '.smi':
				case '.smil':
					$type = 'smil';
					break;
				case '.xls':
					$type = 'ms-excel';
					break;
				case '.ppt':
					$type = 'ms-powerpoint';
					break;
				case '.wbxml':
					$type = 'wap-wbxml';
					break;
				case '.wmlc':
					$type = 'wap-wmlc';
					break;
				case '.wmlsc':
					$type = 'wap-wmlscriptc';
					break;
				case '.bcpio':
					$type = 'bcpio';
					break;
				case '.vcd':
					$type = 'cdlink';
					break;
				case '.pgn':
					$type = 'chess-pgn';
					break;
				case '.cpio':
					$type = 'cpio';
					break;
				case '.csh':
					$type = 'csh';
					break;
				case '.dcr':
				case '.dir':
				case '.dxr':
					$type = 'director';
					break;
				case '.dvi':
					$type = 'dvi';
					break;
				case '.spl':
					$type = 'futuresplash';
					break;
				case '.hdf':
					$type = 'hdf';
					break;
				case '.js':
					$type = 'text javascript';
					break;
				case '.skp':
				case '.skd':
				case '.skt':
				case '.skm':
					$type = 'koan';
					break;
				case '.latex':
					$type = 'latex';
					break;
				case '.nc':
				case '.cdf':
					$type = 'application x-netcdf';
					break;
				case '.sh':
					$type = 'sh';
					break;
				case '.shar':
					$type = 'shar';
					break;
				case '.swf':
					$type = 'shockwave-flash';
					break;
				case '.sit':
					$type = 'stuffit';
					break;
				case '.sv4cpio':
					$type = 'sv4cpio';
					break;
				case '.sv4crc':
					$type = 'sv4crc';
					break;
				case '.tcl':
					$type = 'tcl';
					break;
				case '.tex':
					$type = 'tex';
					break;
				case '.texinfo':
				case '.texi':
					$type = 'texinfo';
					break;
				case '.t':
				case '.tr':
				case '.roff':
					$type = 'troff';
					break;
				case '.man':
					$type = 'troff-man';
					break;
				case '.me':
					$type = 'troff-me';
					break;
				case '.ms':
					$type = 'troff-ms';
					break;
				case '.ustar':
					$type = 'ustar';
					break;
				case '.src':
					$type = 'wais-source';
					break;
				case '.xhtml':
				case '.xht':
					$type = 'xhtml-xml';
					break;
				case '.7z':
					$type = 'archive sevenzip';
					break;
				case '.zip':
					$type = 'archive zip';
					break;
				case '.arj':
					$type = 'archive arj';
					break;
				case '.rar':
					$type = 'archive rar';
					break;
				case '.ace':
					$type = 'archive ace';
					break;
				case '.tar':
					$type = 'archive tar';
					break;
				case '.gtar':
					$type = 'archive gtar';
					break;
				case '.gz':
					$type = 'archive gzip';
					break;
				case '.bzip':
				case '.bzip2':
					$type = 'archive bzip';
					break;
				case '.iso':
					$type = 'archive iso-image';
					break;
				case '.au':
				case '.snd':
					$type = 'audio basic';
					break;
				case '.mid':
				case '.midi':
				case '.kar':
					$type = 'audio midi';
					break;
				case '.mpga':
				case '.mp2':
				case '.mp3':
					$type = 'audio mpeg';
					break;
				case '.aif':
				case '.aiff':
				case '.aifc':
					$type = 'audio aiff';
					break;
				case '.m3u':
					$type = 'audio mpegurl';
					break;
				case '.ram':
				case '.rm':
				case '.ra':
					$type = 'audio realaudio';
					break;
				case '.rpm':
					$type = 'audio ealaudio-plugin';
					break;
				case '.wav':
					$type = 'audio wav';
					break;
				case '.pdb':
					$type = 'chemical pdb';
					break;
				case '.xyz':
					$type = 'chemical xyz';
					break;
				case '.bmp':
					$type = 'image bmp';
					break;
				case '.gif':
					$type = 'image gif';
					break;
				case '.ief':
					$type = 'image ief';
					break;
				case '.jpeg':
				case '.jpg':
				case '.jpe':
					$type = 'image jpeg';
					break;
				case '.png':
					$type = 'image png';
					break;
				case '.tiff':
				case '.tif':
					$type = 'image tiff';
					break;
				case '.djvu':
				case '.djv':
					$type = 'image vnd-djvu';
					break;
				case '.wbmp':
					$type = 'image wap-wbmp';
					break;
				case '.ras':
					$type = 'image cmu-raster';
					break;
				case '.pnm':
					$type = 'image portable-anymap';
					break;
				case '.pbm':
					$type = 'image portable-bitmap';
					break;
				case '.pgm':
					$type = 'image portable-graymap';
					break;
				case '.ppm':
					$type = 'image portable-pixmap';
					break;
				case '.rgb':
					$type = 'image rgb';
					break;
				case '.xbm':
					$type = 'image xbitmap';
					break;
				case '.xpm':
					$type = 'image xpixmap';
					break;
				case '.xwd':
					$type = 'image xwindowdump';
					break;
				case '.igs':
				case '.iges':
					$type = 'model iges';
					break;
				case '.msh':
				case '.mesh':
				case '.silo':
					$type = 'model mesh';
					break;
				case '.wrl':
				case '.vrml':
					$type = 'model vrml';
					break;
				case '.css':
					$type = 'text css';
					break;
				case '.html':
					$type = 'text html';
					break;
				case '.htm':
					$type = 'text html';
					break;
				case '.asc':
				case '.txt':
					$type = 'text plain';
					break;
				case '.rtx':
					$type = 'text richtext';
					break;
				case '.rtf':
					$type = 'text rtf';
					break;
				case '.sgml':
					$type = 'text sgml';
					break;
				case '.sgm':
					$type = 'text sgml';
					break;
				case '.tsv':
					$type = 'text tab-separated-values';
					break;
				case '.wml':
					$type = 'text vnd-wap-wml';
					break;
				case '.wmls':
					$type = 'text vnd-wap-wmlscript';
					break;
				case '.etx':
					$type = 'text setext';
					break;
				case '.xml':
					$type = 'text xml';
					break;
				case '.xsl':
					$type = 'text xml';
					break;
				case '.mpeg':
				case '.mpg':
				case '.mpe':
					$type = 'video mpeg';
					break;
				case '.qt':
				case '.mov':
					$type = 'video quicktime';
					break;
				case '.mxu':
					$type = 'video vnd-mpegurl';
					break;
				case '.avi':
					$type = 'video msvideo';
					break;
				case '.movie':
					$type = 'video sgi-movie';
					break;
				case '.asf':
				case '.asx':
					$type = 'video ms-asf';
					break;
				case '.wm':
				case '.wmv':
					$type = 'video ms-wmv';
					break;
				case '.wvx':
					$type = 'video ms-wvx';
					break;
				case '.ice':
					$type = 'conference cooltalk';
					break;
			} // END switch($var_sFiletype)
		} // END if(!$type)

		/**
		 * Downloadtype
		 */
		if(strstr($type, ' ')) {
			$types = explode(' ', $type);
		} // END if(strstr($type, ' '))

		if($type && in_array($type, $array_Downloadtypes, true) || isset($types['0'])) {
			$type = 'class="download type-' . $type . '"';
		} else {
			$type = 'class="download"';
		} // END if($type)

		/**
		 * The HTML
		 */
		$var_sHTML = '';

		$var_sHTML .= '<form name="" class="download_form" action="'.DIV_INCLUDES_URL.'/div-download.php">
			<input type="hidden" id="file" name="file" value="'.$url.'">
			<div class="button download-button align' . $align . '">
				<a ' . $type . ' href="" ' . $target . '>
					<span>
						<span class="download-title">' . $title . '</span><br />
						<em class="download-description">' . $desc . '</em>
					</span>
				</a>
			</div>
	   	</form>';

		echo $var_sHTML;
	} // END output($atts)

} // END class DIV_Shortcode_Download

new DIV_Shortcode_Download();