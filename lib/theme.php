<?php
if ( !defined('KAIZEKU') ) {die( 42);}
/**
 * $Id$
 * WPI Template functions
 * @package WordPress
 * @subpackage Template
 */
require WPI_LIB_CLASS.'theme-enum.php';

class Wpi
{
	
	/**
	 * WP $wp_filter id
	 * @var int 
	 */   	
	public $wp_filter_id;
	
	public $errors = array();	
	
	/**
	 * 
	 * wpi stylesheet class
	 * 
	 * @see 	wpiStyle
	 * @since 	1.6
	 * @access	public
	 * @var 	mixed|object
	 */
	public $Style;

	/**
	 * 
	 * Browscap object
	 * 
	 * @see 	Browscap
	 * @since 	1.6
	 * @access	public
	 * @var 	mixed|object
	 */	
	public $Browser;

	/**
	 * 
	 * wpi script object
	 * 
	 * @see 	wpiScript
	 * @since 	1.6
	 * @access	public
	 * @var 	mixed|object
	 */		
	public $Script;	

	/**
	 * 
	 * wpi dynamic sidebar object
	 * 
	 * @see 	wpiSidebar
	 * @since 	1.6
	 * @access	public
	 * @var 	mixed|object
	 */		
	public $Sidebar;

	/**
	 * 
	 * wpi template object
	 * 
	 * @see 	wpiTemplate
	 * @since 	1.6
	 * @access	public
	 * @var 	mixed|object
	 */		
	public $Template;
	
	/**
	 * Wpi::__construct()
	 * 
	 * @return
	 */
	public function __construct(){
		self::getFile(array('filters-enum','client','style','template','gravatar','sidebar'),'class');
		self::getFile(array('browscap','body_class'),'import');
		self::getFile(array('utils','formatting','filters','query','links','template','plugin','widgets','comments','author') );
		
		if ( is_admin() ) {		
			add_action('admin_menu', array($this,'setThemeOptions') );
			
			// post template form			
			wpi_foreach_hook(array(
								'simple_edit_form',
								'edit_form_advanced',
								'edit_page_form'),'wpi_post_metaform');
											
			wpi_foreach_hook(array(
								'edit_post',
								'publish_post',
								'save_post',
								'edit_page_form'),'wpi_update_post_form');
			// User profile form
			wpi_foreach_hook(array(
				'profile_personal_options'=>'wpi_profile_options',
				'personal_options_update'=>'wpi_profile_options_update'));				
		}
		
		wpi_plugin_init();
		wpi_default_filters();
		
		// client browser
		$this->Browser = new Browscap(WPI_CACHE_DIR);
		
		if (is_object($this->Browser)){
			$this->setBrowscapPref();
		}		
		
		// section
		$this->section = is_at(); // lost
		
		// scripts
		if ($this->Browser->JavaScript){			
			self::getFile('scripts','class');			
			$this->Script = new wpiScripts();
			
			$js = array('jquery'=>'head','tooltip'=>'head','scroll'=>'head','footer'=>'footer','css'=>'footer');	
					
			if (wpi_option('client_time_styles')){
				$js['cookie'] = 'head';
				$js['client-time'] = 'head';
			}	
						
			// default scripts library
			$this->registerScript($js);
					
			if (wpi_option('widget_treeview')){
				$this->registerScript(array(
					'treeview'=>'head',
					'f-treeview'=>'footer') );
			}
			
			add_action('wp_head',array($this->Script,'printHead'),10);
			add_action('wp_head',array($this->Script,'embedScript'),10);
			add_action(wpiFilter::ACTION_FOOTER_SCRIPT,
							array($this->Script,'printFooter'),10);
		}
				
		// stylesheets
		if ($this->Browser->supportsCSS && ! is_admin() && ! is_feed()){			
			
			$this->Style = new wpiStyle();			
			$this->Style->client_css_version = $this->Browser->CssVersion;
			
			if (wpi_option('widget_treeview')){
				$this->Style->register('image-treeview');
			}
			
			if (strtolower($this->Browser->Browser) == 'ie'){
				$this->Style->register('image-ie');
			}
			
			if (is_active_widget('widget_flickrRSS')){
				// load at active section
				
				$widget_id = 'flickrrss';
				$active = wpi_widget_active_section($widget_id);			
					foreach($active as $k=> $section){
						$this->Style->register($widget_id,$section);
					}
					
				unset($active,$widget_id);			
			}
			
			if (wpi_is_plugin_active('global-translator/translator.php')){
				$tag = 'translator-image';
				$this->Style->register($tag,wpiSection::SINGLE);				
				$this->Style->register($tag,wpiSection::PAGE);
				$this->Style->register($tag,wpiSection::ATTACHMENT);
			}
			
			if (wpi_option('css_via_header')){
				$this->Style->printStyles();
			} else {
				add_action(wpiFilter::ACTION_META_LINK,array($this->Style,'printStyles'));
			}		
		}
		
				
		// sidebar
		$this->Sidebar = new wpiSidebar();
		$this->Sidebar->setSidebar();
		
		// custom header
		$this->Template = new wpiTemplate();
		
		if (defined('WPI_DEBUG')) {
			add_action('wp_footer',array($this,'debug'));
		}
		
		// self::debugDefaultFilters()
		if (defined('FIREBUG_CONSOLE')){
			add_action('wp_head','wpi_firebug_console',wpiTheme::LAST_PRIORITY);
		}			
	}
	
	public function registerScript(array $arr)
	{
		if (is_object($this->Script) && has_count($arr)){
			foreach($arr as $tag => $section){
				$this->Script->register($tag,$section);
			}
			
			unset($arr);
		}
	}
	
	/**
	 * Wpi::getFile()
	 * 
	 * @return
	 */
	public static function getFile($filename,$type = 'modules')
	{
		
		switch ($type){
			case wpiTheme::LIB_TYPE_CLASS:
				$lib = WPI_LIB_CLASS;
			break;
						
			case wpiTheme::LIB_TYPE_MODULES:
				$lib = WPI_LIB_MOD;
			break;			
			
			case wpiTheme::LIB_TYPE_IMPORT:
				$lib = WPI_LIB_IMPORT;
			break;			
			
			default:
				$lib = WPI_LIB;
			break;	
		}
		
		
		if (is_array($filename)){
			foreach($filename as $name){
				$file = $lib.$name.'.php';
				if (file_exists($file)){
					load_template($file);
				}				
			}
			
		} else {
		
			$file = $lib.$filename.'.php';
		
			if (file_exists($file)){
				load_template($file);
			} 
		
		}
		
		unset($file,$lib,$filename);
	}
	
	public static function debugDefaultFilters()
	{
		add_action(wpiFilter::ACTION_FLUSH,'wpi_filter_debug');
	}	
	

	
	/**
	 * Wpi::setBrowscapPref()
	 * 
	 * @return
	 */
	public function setBrowscapPref()
	{
		//if( ! wpi_option('browscap_autoupdate') ){
			$this->Browser->doAutoUpdate = false;
		//}
		
		if (function_exists('curl_init')){
			$this->Browser->updateMethod = Browscap::UPDATE_CURL;
		}
		
		$this->Browser = $this->Browser->getBrowser($_SERVER['HTTP_USER_AGENT']);
	}
	
	
	/**
	 * Wpi::setThemeOptions()
	 * 
	 * @return
	 */
	public function setThemeOptions()
	{
		
		
		if ( !class_exists('wpiAdmin') )
		{			
			
			self::getFile('admin','class');
			
			$this->AdminUI = new wpiAdmin();
	
	        $req_page = basename(WPI_DIR.'functions.php');
	
	        $token = wpiFilter::NONCE_THEME_OPTIONS;
	
	        if (is_get('page') && is_get('page') == $req_page)
			{        	
	        	
	            if (is_req('action') && 'save' == b64_safe_decode($_REQUEST['action']))
				{
					
	                check_admin_referer($token);
	                $this->AdminUI->filterRequest($_REQUEST);
	
	                wp_redirect('themes.php?page='.$req_page.'&saved=true');
	                die;
	            }
	            
	            add_action('admin_head', array($this->AdminUI,'printCSS') );
	            
	            wp_enqueue_script(WPI_META.'_admin');
	        }
	
	        add_theme_page( __('WPI Theme Options',WPI_META), 
						    __('Theme Options',WPI_META), 
							'edit_themes', $req_page, 
							array($this->AdminUI, 'themeOptions') );
    	}		
	}
	
	
	/**
	 * Wpi::debug()
	 * 
	 * @return
	 */
	public function debug()
	{
		wpi_dump($this);
	}
	
		
}	
?>