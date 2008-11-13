<?php
if ( !defined('KAIZEKU') ) {die( 42);}
/**
 * $Id$
 * WPI Template functions
 * @package WordPress
 * @subpackage Template
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
	
	return (version_compare($GLOBALS['wp_version'],$version,$operator));
}


function wpi_get_dir($path, $regex = false){

	if ( class_exists('DirectoryIterator') ){
		
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
	} else{
		// @todo fallback 
		return;
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

?>