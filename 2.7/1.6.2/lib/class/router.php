<?php
class wpiRouter
{

	protected $_max_buffer = 4096;	
	
	public $uri = array();
	
	const REQ_ENCODE_BASE64 	= '.e64';
	
	const REQ_DECODE_BASE64 	= '.d64';
	
	const REQ_THUMB			 	= '.thumb';	
	
	const REQ_CURIE			 	= '.curie';
	
	const REQ_GRAVATAR		 	= '.ava';
	
	const REQ_WEBFONT		 	= '.webfont';	
	
	const GRAVATAR_URI = 'http://www.gravatar.com/avatar/%1$s.png?s=%2$s&r=%3$s&d=%4$s';
	
	public $request_time;
	
	public $type;
	
	public $request;
	
	

	public function __construct($config = false)	
	{	
		
		if (is_array($config)){
			
			if (isset($config['type'])){
				$this->type = $config['type'];
			}

		
			if (isset($config['files'])){
				$this->request = $config['files'];
			}	
			
			if (isset($config['uri'])){
				$this->request = $config['uri'];
			}						
		}
		
		$this->request_time = $_SERVER['REQUEST_TIME'];
		$this->_process();
			
	}
	
	private function _process()
	{
		
		if ( isset($this->type) && !empty($this->type) )
		{
			$method_name = (string) '_'.$this->type;
			
			if (method_exists(__CLASS__,$method_name)) {							
				call_user_func(array($this,$method_name));
			}
		}
	}
	
	private function _e64()
	{
		$this->output 	= self::strip(self::REQ_ENCODE_BASE64,$this->request);
		$this->output 	= $this->b64e( $this->output );
		
		$this->_gc();			
	}
	
	private function _d64()
	{
		$this->output = self::strip(self::REQ_DECODE_BASE64,$this->request);
		$this->output = $this->b64d( $this->output );
				
		$this->_gc();			
	}
	
	/**
	 * 
	 * Gravatar with cache
	 * void _ava()
	 * @private 
	 * @tutorial http://blog.gravatar.com/2008/03/14/big-changes-afoot/
	 */
	private function _ava()
	{		

		header("Cache-Control: private, max-age=10800, pre-check=10800");
		header("Pragma: private");
		header("Expires: " . date(DATE_RFC822,strtotime("+2 day")));
				
		$uri = self::strip(self::REQ_GRAVATAR,$this->request);
			
		list($hash,$size,$rating,$default) = explode("-",$uri);			
			
		$default = (!empty($default)) ? $default : 'identicon';
				
		$url = sprintf(self::GRAVATAR_URI,$hash,$size,$rating,$default); 
		
		$file = WPI_CACHE_AVATAR_DIR.md5($url).'.png';
		
		//var_dump($file);exit;
		
		if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])){
		  header('Last-Modified: '.$_SERVER['HTTP_IF_MODIFIED_SINCE'],true,304);
		  exit;
		}

		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && 
			(strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == filemtime($file))){
		  header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($file)).' GMT', true, 304);
		  exit;
		}		

		if (!file_exists($file)){
			cURLdownload($url, $file);
		}
		
		header("Content-type: image/png");
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($file)) . ' GMT');
		$contents = file_get_contents($file);
		header("Content-Length: ".strlen($contents));
		echo $contents;
		exit;
				
	}		

	private function _webfont()
	{
		$lastmodified = 0;
		//wpi_cache_header();
		// settings
		$cache_images = true;
		$mime_type = 'image/png' ;
		$extension = '.png' ;
		$send_buffer_size = $this->_max_buffer;	
				
		$font_size = 36;		
		$text = 'WP-iStalker';		
		$data = self::strip(self::REQ_WEBFONT,$this->request);		
		$prop = explode("-",$data);		
		
		if (isset($prop[1])){
			$text = $prop[0];
			$font_size = $prop[1];
		} else {
			$text = $data;
		}
		
		$text = $this->b64d( $text );			
		
		$cache_folder   = WPI_CACHE_FONTS_DIR;
		$font_file  	= WPI_FONTS_DIR.'DANUBE__.TTF' ;
		$font_color 	= '#ffffff';
		
		if (isset($prop[2])){
			$font_file = WPI_FONTS_DIR.$this->b64d($prop[2]);
		}
		
		if (isset($prop[3])){
			$font_color = $this->b64d($prop[3]);
		}
		
		// cache image
		if (isset($prop[4])){
			$intbool = (int) $prop[4];
			$cache_images = (($intbool === 1) ? true : false );
		}		

		$default = '1787BF';
		
		$hex = array();
		$hex['nt'] = '707071';
		$hex['dy'] = '1787BF';
		$hex['dw'] = 'D7D7B8';
		
		$client = (string) $_COOKIE['wpi-cl'];	
	
		
		$background_color = '#'.( (!empty($client) && isset($hex[$client])) ? $hex[$client] : $default );
		
		if ( $client == 'dw' ) $font_color = '494738';
			
		$transparent_background  = true ;

		if(get_magic_quotes_gpc()) $text = stripslashes($text) ;
		$text = javascript_to_html($text) ;
		
		$hash = md5(basename($font_file) . $font_size . $font_color .
		            $background_color . $transparent_background . $text) ;		
		
		$cache_filename = $cache_folder . '/' . $hash . $extension ;
		
		if (file_exists($cache_filename)){
			$lastmodified = max($lastmodified, filemtime($cache_filename));
			$etag = $lastmodified . '-' . md5($cache_filename);
			header("Etag: \"" . $etag . "\"");				
		}
		
		if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && 
			stripslashes($_SERVER['HTTP_IF_NONE_MATCH']) == '"' . $etag . '"') {
				header('HTTP/1.0 304 Not Modified');
				header('Content-Length: 0');
				exit;
		}		
		
		wpi_is_304_request($cache_filename);				

		include WPI_LIB_IMPORT.'gdtxt.php';	
		
		exit;					
	}

	private function _curie()
	{
		$uri = self::strip(self::REQ_CURIE,$this->request);
		
		require WPI_LIB_CLASS.'redirect.php';
		$redirect = new wpiRedirect($uri);
		
		exit;		
	}
		
	public static function strip($string,$value)
	{
		return str_replace($string,'',$value);
	}
	
	public function __desctruct()
	{
		unset($this);
	}
	
	public function b64d($datum){
		$data = str_replace( array('-', '_'), array('+', '/'), $datum );
		$mod4 = strlen( $data ) % 4;
			if ( $mod4 ):					
				$data .= substr( '====', $mod4 );
			endif;
		return base64_decode( $data );				
	}
	
	public function b64e($string)
	{
		$b64 = base64_encode( $string );
		return str_replace( array('+', '/', '='), array('-', '_', ''), $b64 );
	}
	
	
	public function _gc()
	{
		if (isset($this->output)){
			die($this->output);
		} else {
			die(42);
		}		
	}
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

/**
 * Convert embedded, javascript unicode characters into embedded HTML
 * entities. (e.g. '%u2018' => '&#8216;'). returns the converted string.
 * @author Stewart Rosenberger 
 * @links http://www.stewartspeak.com
 * @filesource gdtxt.php
 */
function javascript_to_html($text)
{
    $matches = null ;
    preg_match_all('/%u([0-9A-F]{4})/i',$text,$matches) ;
    if(!empty($matches)) for($i=0;$i<sizeof($matches[0]);$i++)
        $text = str_replace($matches[0][$i],
                            '&#'.hexdec($matches[1][$i]).';',$text) ;

    return $text ;
}

function wpi_cache_header(){
	header("Cache-Control: private, max-age=10800, pre-check=10800");
	header("Pragma: private");
	header("Expires: " . date(DATE_RFC822,strtotime("+2 day")));	
}

function wpi_is_304_request($file = false){
	
	if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])){
		header('Last-Modified: '.$_SERVER['HTTP_IF_MODIFIED_SINCE'],true,304);
		exit;
	}
	
	if ($file){	
		
		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && 
			(strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == filemtime($file))){					
		  	header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($file)).' GMT', true, 304);
		  exit;
		}
	}
	
}
?>