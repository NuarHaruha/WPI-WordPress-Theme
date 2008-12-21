<?php

/**
 * Simple Redirect
 */

class wpiRedirect
{
	const BASE_EXTENSION 	= '.curie';
	const STATUS_CODE 		= 302;
	
	// base64 encode uri
	public $curie;
	
	// loop flag
	public $flag;
	
	// the decode redirect url
	public $uri;
	
	public function __construct($curie = false)
	{
		if ($curie){
			
			$this->curie = $curie;
			$this->flag = 1;
			$this->_decode()->redirect();
			
		}
	}
	
	private function _decode()
	{
		$data = str_replace( array('-', '_'), array('+', '/'), $this->curie );
		$mod4 = strlen( $data ) % 4;
			if ( $mod4 ):					
				$data .= substr( '====', $mod4 );
			endif;		
		$this->uri = (string) 'http://'.base64_decode($data);
		$this->flag = 2;
		return $this;
		
	}
	
	public function redirect()
	{
		
		if ($this->flag == 2){
					
       		header("Location: $this->uri", TRUE, self::STATUS_CODE); 
       		exit();  		
       }
	}
}


?>