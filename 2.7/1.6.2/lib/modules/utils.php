<?php
if ( !defined('KAIZEKU') ) {die( 42);}
/**
 * WP-iStalker Chrome 
 * Mashup Utilities
 * 
 * @package	WordPress
 * @subpackage	wp-istalker-chrome
 * 
 * @category	ToolsAndUtilities
 * @author	Avice (ChaosKaizer) De'vereux <ck+wp-istalker-chrome@istalker.net>
 * @copyright 	2007 - 2009 Avice De'vereux, NH. Noah
 * @license 	http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License v2 
 * @version 	CVS: $Id$
 * @since 	1.6.2
 */
 
function wpi_dump($var){
	$style = 'overflow:auto;width:500px;height:250px';
	
	echo '<pre style="'.$style.'">';
	var_dump($var);
	echo '</pre>';	
}


/**
 *
 * @param string $function_name The user function name, as a string.
 * @return Returns TRUE if function_name  exists and is a function, FALSE otherwise.
 */
function wpi_user_func_exists($function_name = 'do_action') {
	
	$func = get_defined_functions();
	
	$user_func = array_flip($func['user']);
	
	unset($func);
	
	return ( isset($user_func[$function_name]) );	
}

function is_wp_version($version='2.5', $operator = '>='){
	
	return (version_compare(WP_VERSION_MAJ,$version,$operator));
}

function string_len($string, $len){

 if (strlen ($string) < $len) {
  return $string;
 }

 if (preg_match ("/(.{1,$len})\s/", $string, $match)) {
  return $match [1] . "&#8230;";
 } else {
  return substr ($string, 0, $len) . "&#8230;";
 }
}


function wpi_get_dir($path, $regex = false){

	if ( ! class_exists('DirectoryIterator') ){
		Wpi::getFile('back-compat','import');
	}
		
	try{
		$dir  = new DirectoryIterator($path);
		$list = array();
		
		foreach ($dir as $file){
			if ($file->isFile()){
				$filename = (string) $file->getFilename();

				if ( ! $regex ){
					$list[] = $filename;
					} elseif ( preg_match($regex, $filename) ){
								$list[] = $filename;
					}
				}
		}

		unset($dir,$file);

		return $list;

	} catch (Exception $ex)	{
			return false;
	}
	 
}  

if(!wpi_user_func_exists('format_filesize')) {
	function format_filesize($rawSize) {
		if($rawSize / 1099511627776 > 1) {
			return round($rawSize/1099511627776, 1).' '.__('TiB', WPI_META);
		} elseif($rawSize / 1073741824 > 1) {
			return round($rawSize/1073741824, 1).' '.__('GiB',WPI_META);
		} elseif($rawSize / 1048576 > 1) {
			return round($rawSize/1048576, 1).' '.__('MiB',WPI_META);
		} elseif($rawSize / 1024 > 1) {
			return round($rawSize/1024, 1).' '.__('KiB',WPI_META);
		} elseif($rawSize > 1) {
			return round($rawSize, 1).' '.__('bytes',WPI_META);
		} else {
			return __('unknown',WPI_META);
		}
	}
}

function str_rem($string,$var) { return str_replace($string,'',$var);}
		
function get_host($uri){ return parse_url($uri,PHP_URL_HOST); }

function wpi_fwrite($file, $content, $flag = 'wb', $timeout = 5){
	$fp = false;		
	if ( ($fp = fopen($file,$flag) )  != false) {			
		stream_set_blocking($fp, TRUE);
        stream_set_timeout($fp,$timeout);
		stream_set_write_buffer($fp, 0);			
		fwrite($fp, $content);
		fclose($fp);			
	}
			
	return $fp;
}

function wpi_safe_stripslash($content){
	$content = htmlentities2($content);
	$content = ent2ncr($content);
	$content = stripslashes_deep($content);
	return $content;
}

function wpi_write_cache($file,$content){
		
	wpi_fwrite(WPI_CACHE_DIR.DIRSEP.$file,$content,'wb',5);
}

function wpi_firebug_console(){	
	t('script','',array(
		'src'=>'http://getfirebug.com/releases/lite/1.2/firebug-lite-compressed.js',
		'type'=> 'text/javascript') 
	);	
	
	t('script','firebug.env.height = 500;',array('type'=> 'text/javascript'));	
}

function wpi_get_fonts(){
	$fonts = wpi_get_dir(WPI_FONTS_DIR,wpiTheme::GD_FONT_TYPE);	
	return (has_count($fonts)) ? $fonts : false;
}

/**
 * void wpi_get_frontpage_template()
 * 
 * @since 1.6.2
 */
function wpi_get_frontpage_template(){
	$tpl = wpi_get_dir(WPI_LIB_IMPORT_TEMPLATE,wpiTheme::FRONTPAGE_TEMPLATE_TYPE);	
	return (has_count($tpl)) ? $tpl : false;
}

/**
 * Determine the default background hex colors
 * 
 * @since 1.6.2
 * @return string 
 */
function wpi_get_bg_hex_color(){
	
	$default = '1787BF';
	
	$hex = array();
	$hex['nt'] = '707071';
	$hex['dy'] = '1787BF';
	$hex['dw'] = 'D7D7B8';
	$hex['dk'] = '8F1A2A';
	
	$client = (string) $_COOKIE[wpiTheme::CL_COOKIE_TIME];
	
	return '#'.( (!empty($client) && isset($hex[$client])) ? $hex[$client] : $default );	
}

/**
 * function wpi_hex2rgb
 * Helper function to convert hex to rgb colors array 
 * 
 * @since 1.6.2
 * @params string hex colors; #000000, 000000, #000 or 000 
 * @return mixed|array   
 */
function wpi_hex2rgb($hex){
	    if(substr($hex,0,1) == '#') $hex = substr($hex,1);
			
	    if(strlen($hex) == 3){
	        $hex = substr($hex,0,1) . substr($hex,0,1) .
	               substr($hex,1,1) . substr($hex,1,1) .
	               substr($hex,2,1) . substr($hex,2,1) ;
	    }
			
	    if(strlen($hex) != 6) return false;
			
	    return array(
			'red' => hexdec(substr($hex,0,2)),
			'green' => hexdec(substr($hex,2,2)),
			'blue' => hexdec(substr($hex,4,2))
		);
}

/**
 * Basic cURL file or page download with basic error trapping.
 * @links http://my.php.net/manual/en/curl.examples.php#83541 jlee8df at gmail dot com
 */

function cURLcheckBasicFunctions()
{
  if( !function_exists("curl_init") &&
      !function_exists("curl_setopt") &&
      !function_exists("curl_exec") &&
      !function_exists("curl_close") ) return false;
  else return true;
}

/*
 * Returns string status information.
 * Can be changed to int or bool return types.
 */
function cURLdownload($url, $file)
{
  if( !cURLcheckBasicFunctions() ) return "UNAVAILABLE: cURL Basic Functions";
  $ch = curl_init();
  if($ch)
  {
    $fp = fopen($file, "w");
    if($fp)
    {
      if( !curl_setopt($ch, CURLOPT_URL, $url) ) return "FAIL: curl_setopt(CURLOPT_URL)";
      if( !curl_setopt($ch, CURLOPT_FILE, $fp) ) return "FAIL: curl_setopt(CURLOPT_FILE)";
      if( !curl_setopt($ch, CURLOPT_HEADER, 0) ) return "FAIL: curl_setopt(CURLOPT_HEADER)";
      if( !curl_exec($ch) ) return "FAIL: curl_exec()";
      curl_close($ch);
      fclose($fp);
      return true;
    }
    else return false;
  }
  else return false;
}

function rand_array($array){
	srand((double) microtime() * 1000000);
	return rand(0, count($array)-1);	
}

/**
 * wpi_has_module()
 * check for installed Apache modules
 * 
 * @param string $name apache register module name
 * @return bool
 */
function wpi_has_module($name){
	
	$prop = false;	
	if (function_exists('apache_get_modules')){
		$modules = array_flip(apache_get_modules());		
		$prop =  (isset($modules[$name]));
		
		unset($modules);
	}		
	
	return $prop;		
}