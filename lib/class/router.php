<?php
class wpiRouter
{

	const REQ_ENCODE_BASE64 	= '.e64';
	
	const REQ_DECODE_BASE64 	= '.d64';
	
	const REQ_THUMB			 	= '.thumb';	
	
	const REQ_CURIE			 	= '.curie';
	
	const REQ_GRAVATAR		 	= '.ava';
	
	const REQ_WEBFONT		 	= '.webfont';	
	
	public $request_time;
	
	public $type;
	
	public $request;
	

	public function __construct($config = false)	
	{	
		
		if (is_array($config))
		{
			
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
		
		// valid timestamp from server 
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
	
	
	private function _ava()
	{
		$uri = self::strip(self::REQ_GRAVATAR,$this->request);
	
		if (self::getFile('redirect') ){				
			
			list($hash,$size,$rating,$default) = explode("-",$uri);
			
			$default = (!empty($default)) ? $default : 'identicon';
				
			$url = 'http://www.gravatar.com/avatar/'.$hash.'?s=';
			$url .= $size.'&d='.$default.'&r='.$rating;
				
			$ht = new wpiRedirect();
				
			$ht->flag = 2;
			$ht->uri  = $url;
			$ht->redirect();
				
				
			unset($ht);
		}	
		
		$this->_gc();		
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

    public static function getFile($file_name = false)
    {
        if (!$file_name) {
            return false;
        }

        $file = WPI_LIB_CLASS .$file_name . '.php';

        if (!file_exists($file)) {
            return false;
        }

        include $file;
        return true;
    }		
}	
?>