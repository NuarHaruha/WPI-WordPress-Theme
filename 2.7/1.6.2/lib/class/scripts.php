<?php
if ( !defined('KAIZEKU') ) { die( 42);}
/** 
 * Wpi scripts class
 * 
 * $Id scripts.php, 0022 10/23/2008 12:24:28 PM ck $
 * 
 * @links		http://www.w3.org/TR/REC-html40/interact/scripts.html W3C/Scripts
 */
 	
class wpiScripts{
	
	const SEP = ',';
	
	public $wp_filter_id;
	
	public $tag = array();
	
	public $head = array();
	
	public $footer = array();
	
	public $path;
	
	public $js;
	
	
	public function __construct()
	{
		$this->path = WPI_JS_DIR;
	}
	
	public function setScripts()
	{
		$this->register('jquery','head');	
	}
	
	public function register($tag = 'jquery', $type = 'head')
	{	
		$prop = array();
		if (file_exists(WPI_JS_DIR.$tag.'.js')){
			
			switch ($type){
				case 'head':
					$this->head[] = $tag;
				break;
				case 'footer':
					$this->footer[] = $tag;
				break;
				default:
					$this->tag[] = $tag;
				break;
			}
								
			wp_register_script($tag.'-js', $this->theme_url.$tag.'.js');
			
		} else {
			return false;
		}
	}
	
	public function flushJs()
	{
		$this->js = null;
	}
	
	public function getScripts($type = 'tag')
	{
		$this->flushJs();
		if (has_count($this->$type)){
			
			$this->$type = array_unique($this->$type);
						
			$this->js = join(self::SEP,$this->$type);
						
		}
	}
	
	public function printHead()
	{
		$this->type = 'head';
		
		$this->flushJs();
		
		$this->setExtraJS();
		
		if (has_count($this->head)){
			
			$this->head = array_unique($this->head);
						
			$this->js = join(self::SEP,$this->head);
						
		}
		
		$this->printScripts();		
		
	}
	
	
	public function printFooter()
	{
		$this->type = 'footer';
		
		$this->flushJs();
		
		if (has_count($this->footer)){
			
			$this->head = array_unique($this->footer);
						
			$this->js = join(self::SEP,$this->footer);
						
		}
		
		$this->printScripts();	
	}	
	
	public function printScripts()
	{
		echo PHP_EOL.PHP_T;	
		t('script','',array(
					'id'=>'wpi-js-'.$this->type,
					'type'=>'text/javascript',					
					'src'=> wpi_get_scripts_url($this->js),
					'charset'=>'utf-8') );
	}
	
	public function getHeaderScripts(){			
		$this->getScripts('head');
		$this->printScripts();
	}
	
	public function setExtraJS()
	{	global $wp_query;
	
		if ($wp_query->is_singular){
		 // && $wp_query->post->comment_status  == 'open'
		 $this->register('thickbox','head');
		}		
	}
	
	public function embedScript()
	{	global $wp_query;
	
		list($lang, $locale) = explode('-',get_bloginfo('language'));
		$pid = (isset($wp_query->post->ID)) ? $wp_query->post->ID : 0;
				
		$js = PHP_EOL.PHP_T;
		$js .= '/*<![CDATA[*/'.PHP_EOL.PHP_T.PHP_T;	
		$js .= 'var wpi = {url:'.json_encode(WPI_URL_SLASHIT);
		$js .= ',id:'.json_encode(wpiTemplate::bodyID());
		$js .= ',blogname:'.json_encode(WPI_BLOG_NAME);
		$js .= ',theme_url:'.json_encode(WPI_THEME_URL);
		$js .= ',section:'.json_encode(is_at());
		$js .= ',permalink:'.json_encode(trailingslashit(self_uri()));
		
		$jspath = json_encode(rel(WPI_THEME_URL.'public/scripts/') );
		$jsurl  = json_encode(wpi_get_scripts_url('%s'));	
			
		$js .= ',script:{path:'.$jspath.',url:'.$jsurl.'}';
		if (wpi_option('client_time_styles')){
			$js .= ',pid:'.$pid.',cl_type:td};jQuery(document).ready(function(){if( $(\'#\'+wpi.id).hasClass(wpi.cl_type) == false){ $(\'#\'+wpi.id).addClass(wpi.cl_type);jQuery.cookie(\'wpi-cl\',wpi.cl_type,{duration: 1/24,path: "/"});};});'.PHP_EOL;
		} else {
			$js .= ',pid:'.$pid.'};'.PHP_EOL;
		}
		
		// check client cookie;				
		if ($wp_query->is_search || $wp_query->is_404){
		// google webmaster 404 widget
		$js .= PHP_T.PHP_T.'var GOOG_FIXURL_LANG = \''.$lang.'\';var GOOG_FIXURL_SITE = wpi.url;'.PHP_EOL;
		}
		$js .= PHP_T.'/*]]>*/'.PHP_EOL.PHP_T;
				
		echo PHP_T;
		t('script',$js,array('id'=>'wp-js-head-embed','type'=>'text/javascript','defer'=>'defer','charset'=>'utf-8'));
		

	}

}