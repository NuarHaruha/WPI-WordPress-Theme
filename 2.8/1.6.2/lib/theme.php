<?php
if ( !defined('KAIZEKU') ) { exit();}
/**
 * WP-iStalker Chrome Milk 
 * Template functions
 * 
 * @package	WordPress
 * @subpackage	wp-istalker-chrome
 * 
 * @category	Template
 * @author	Avice (ChaosKaizer) De'vereux <ck+wp-istalker-chrome@istalker.net>
 * @copyright 	2006 - 2009 Avice De'vereux
 * @license 	http://www.opensource.org/licenses/mit-license.php MIT License
 * @version 	CVS: $Id$
 * @since 	1.2
 */
 
// Theme main configurations 
require WPI_LIB_CLASS.'enum.php';

/**
 * Wpi
 * Class loader
 * 
 * @author	Avice (ChaosKaizer) De'vereux <ck+wp-istalker-chrome@istalker.net>
 * @author	NH. Noah <noah+wp-istalker-chrome@kakkoi.net>
 * @copyright	2007 - 2009 Avice De'vereux, NH. Noah
 * @license	http://www.opensource.org/licenses/mit-license.php MIT License
 * @since	1.2
 * @access	public
 */ 
class Wpi
{
	
	/**
	 * WP filter id
	 * @var int 
	 */   	
	public $wp_filter_id;
	
	
	public $errors = array();
		
	
	/**
	 * wpiStyle Object
	 * 
	 * @see 	wpiStyle
	 * @since 	1.6
	 * @access	public
	 * @var 	mixed|object
	 */
	public $Style;
	

	/**
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
	 */
	public function __construct(){
		self::getFile(array('client','style','template','gravatar','sidebar'),'class');
		self::getFile(array('browscap','body_class'),'import');
		self::getFile(array('utils','formatting','filters','query','links','template','plugin','widgets','comments','author') );
		
		$this->_defaultSettings();
		
		if ( is_admin() ):	
			
			add_action('admin_menu', array($this,'setThemeOptions') );
			
			// singular template form		
			$callback = (is_wp_version('2.6','>=')) ? 'wpi_register_metaform' : 'wpi_post_metaform';
			
			wpi_foreach_hook(array(
								'simple_edit_form',
								'edit_form_advanced',
								'edit_page_form'),$callback);			
							
			wpi_foreach_hook(array(
								'edit_post',
								'publish_post',
								'save_post',
								'edit_page_form'),'wpi_update_post_form');
			
			// User profile form
			wpi_foreach_hook(array(
				'profile_personal_options'=>'wpi_profile_options',
				'personal_options_update'=>'wpi_profile_options_update'));								
		else:
		
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
			
			$js = array(/*'jquery'=>'head',*/'tooltip'=>'head','scroll'=>'head','footer'=>'footer','css'=>'footer');	
					
			if (wpi_option('client_time_styles')){
				$js['cookie'] = 'head';
				$js['client-time'] = 'head';
			}	

			if (wpi_option('client_date_styles')){
				$js['client-date'] = 'footer';
			}
			
			if (wpi_option('overwrite_recent_comments') 
			|| wpi_option('overwrite_flickrrss')
			|| wpi_option('widget_treeview')){
				$js['filter-sidebar'] = 'footer';			
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
			add_action(wpiFilter::ACTION_FOOTER_SCRIPT, array($this->Script,'printFooter'),10);
		}
				
		// stylesheets
		if ( /*$this->Browser->supportsCSS && */ ! is_admin() && ! is_feed()){			
			
			$this->Style = new wpiStyle();			
			$this->Style->client_css_version = $this->Browser->CssVersion;
			
			if (wpi_option('widget_treeview')){
				$this->Style->register('image-treeview');
			}
			
			if (strtolower($this->Browser->Browser) == 'ie'){
				$this->Style->register('image-ie');
			}		
			
			if (is_active_widget('widget_flickrRSS')
			|| is_active_widget('wpi_flickrrss_widget_rep')){
				// load at active section
				
				$widget_id = 'flickrrss';
				$active = wpi_widget_active_section($widget_id);			
					foreach($active as $k=> $section){
						$this->Style->register($widget_id,$section);
					}
					
				unset($active,$widget_id);			
			}
			
			if (wpi_is_plugin_active('global-translator/translator.php') 
			&& wpi_option('widget_gtranslator')){
				$tag = 'translator';
				$this->Style->register($tag,wpiSection::SINGLE);				
				$this->Style->register($tag,wpiSection::PAGE);
				$this->Style->register($tag,wpiSection::ATTACHMENT);
			}
			
			if (wpi_option('home_sidebar_position') != 'right'){
				add_action(wpiFilter::ACTION_INTERNAL_CSS,'wpi_sidebar_dir_filter',wpiTheme::LAST_PRIORITY);
			}
			
			if (wpi_option('home_post_thumb')){
				add_action(wpiFilter::ACTION_INTERNAL_CSS,'wpi_post_thumbnail_filter',wpiTheme::LAST_PRIORITY+2);
			}			

			if (wpi_option('client_width')){
				$this->Style->register('cl-width');
			}
			
			if (wpi_option('menu_page_enable')){
				$this->Style->register('nav');
			}
						
			if (wpi_option('css_via_header')){
				$this->Style->printStyles();
			} else {
				add_action(wpiFilter::ACTION_META_LINK,array($this->Style,'printStyles'));
			}		
		}
				
		// custom header
		$this->Template = new wpiTemplate();
		
		if (defined('WPI_DEBUG')) {
			add_action(wpiFilter::ACTION_FLUSH,array($this,'debug'));
		}
		
		// self::debugDefaultFilters()
		if (defined('FIREBUG_CONSOLE')){
			add_action('wp_head','wpi_firebug_console',wpiTheme::LAST_PRIORITY);
		}
		
		endif;	
		
			// sidebar
			$this->Sidebar = new wpiSidebar();
			$this->Sidebar->setSidebar();		
			
			
			// compare hash
			$this->_prepImage()->_prepHtaccess();	
	}
	
	private function _defaultSettings(){
		
		$meta = WPI_META_PREFIX.'flag';
		$is_install = get_option($meta);
		
		if ( '' == $is_install || $is_install <= 0 ){
			
			$options = array(
			'pathway_enable' => 1, 
			'relative_date' => 0, /* disabled (mess-up foreign blog)*/
			'post_hrating' => 1, /* require for hReview */
			'relative_links'=> 1, /* make links relative (seo) */
			'meta_robots' => 1,
			'meta_title' => 1,
			'meta_description' => 1,
			'def_meta_description' => apply_filters(wpiFilter::FILTER_META_DESCRIPTION,get_option('blogdescription')),
			'text_dir'=>'ltr',
			'post_bookmarks'=>1,
			'icn_favicon'=> clean_url(wpi_get_favicon_url()),
			'home_avatar'=>1,
			'home_sidebar_position'=> 'right',
			'frontpage_style'=>'default', // frontpage template
			'client_time_styles'=>1	
			);
		
		foreach($options as $k=>$v){
			wpi_update_theme_options($k,$v,false);
		}
		
		unset($options,$k,$v);
		$this->_setCachePerm();
		
			if (WPI_IS_HTACCESS && WPI_IS_IMAGE_READY){
				update_option($meta, 1);			
			} else {
				update_option($meta, 0);			
			}																																
			
			update_option(WPI_META_PREFIX.'version', wpiTheme::VERSION);
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

			case wpiTheme::LIB_TYPE_SHORTCODE:
				$lib = WPI_LIB_IMPORT_SHORTCODE;
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
		
		unset($file,$lib, $filename);
	}


	public static function debugDefaultFilters()
	{
		add_action(wpiFilter::ACTION_FLUSH,'wpi_filter_debug');
	}	


	/**
	 * Wpi::setBrowscapPref()
	 */
	public function setBrowscapPref()
	{
		/**
		 * Gary Keith {@link http://browsers.garykeith.com/ Browser capabilities project} 
		 * blocked all default Browscap php class useragent
		 * issues related to {@link http://code.google.com/p/phpbrowscap/issues/detail?id=3 issue 3}
		 */
		$this->Browser->userAgent = wpiTheme::BROWSCAP_UA;
		
		//if( ! wpi_option('browscap_autoupdate') ){
			$this->Browser->doAutoUpdate = true; // autoupdate is too expensive
		//}
		
		if (function_exists('curl_init')){
			$this->Browser->updateMethod = Browscap::UPDATE_CURL;
		}
		
		$this->Browser = $this->Browser->getBrowser(SV_UA_STRING);
	}
	
	
	/**
	 * Wpi::setThemeOptions()
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
	            
	            if ( (wpi_option('banner') ) && wpi_has_banner() != false ){
	            	wp_enqueue_script('thickbox');
	            	wp_enqueue_style('thickbox');
	            }
	            
	            wp_enqueue_script(WPI_META.'_admin');
	        }
	
	        add_theme_page( __('WPI Theme Options',WPI_META), 
						    __('Theme Options',WPI_META), 
							'edit_themes', $req_page, 
							array($this->AdminUI, 'themeOptions') );
    	}		
	}
	

	private function _setCachePerm(){
		$d = array(
			'cache'=>WPI_CACHE_DIR,
			'css'=>WPI_CACHE_CSS_DIR,
			'scripts'=>WPI_CACHE_JS_DIR,
			'webfonts'=>WPI_CACHE_FONTS_DIR,
			'avatar'=>WPI_CACHE_AVATAR_DIR,
			'images'=>WPI_IMG_DIR);
		
		foreach($d as $index=>$path){
			$path = (string) $path;
			if (!is_writable($path)){
				// force all permission to owner, read and execute by others
				@chmod($path,0755); 
			} 
		}
		
		unset($d);
		return $this;	
	}
	
	/**
	 * void Wpi::_prepImage()
	 * Prepare theme images files
	 * 
	 * @access private
	 */
	private function _prepImage()
	{
		
		if (WPI_IS_IMAGE_READY) return $this;
		
		$file = WPI_CACHE_DIR.DIRSEP.'wpi-chrome-images.zip';
		
		if (! file_exists($file)){
			$response = cURLdownload(wpiTheme::DOC_PUB_IMAGE_URL, $file);
			
			if ($response == false || is_string($response) ){
			
				$options = array('timeout' => 3,'user-agent' => wpiTheme::BROWSCAP_UA);		
				$response = wp_remote_get(wpiTheme::DOC_PUB_IMAGE_URL, $options);
			
				if ( is_wp_error( $response ) ){
					return false;		
				}
						
				if ( isset($response['response']['code']) 
				&& 200 != $response['response']['code'] ){
					return false;	
				}
						
				wpi_fwrite($file, $response['body']);
				unset($response);							
			}
		}
				
		if (!class_exists('PclZip')){
			require WP_ROOT.DIRSEP.'wp-admin'.DIRSEP.'includes'.DIRSEP.'class-pclzip.php';
		}		
		
		if (PATH_SEPARATOR == ';'){
			$file = PclZipUtilTranslateWinPath($file, false);		
		}
		
		$archive = new PclZip($file);		
		
		$archive->extract(PCLZIP_OPT_PATH, WPI_IMG_DIR);
		
		unset($archive);
		@unlink($file);
					
		return $this;			
	}
	
	
	private function _prepHtaccess(){
				
		$file = WPI_DIR.'.htaccess';
		
		if (! file_exists($file)){
			
					if (!cURLcheckBasicFunctions()){
						$options = array('timeout' => 3,'user-agent' => wpiTheme::BROWSCAP_UA);		
						$response = wp_remote_get(wpiTheme::DOC_PUB_HTACCESS_URL, $options);
			
						if ( is_wp_error( $response ) ){
							return false;		
						}
						
						if ( 200 != $response['response']['code'] ){
							return false;	
						}
						
						wpi_fwrite(WPI_DIR.'.htaccess',	$response['body']);
						unset($response);
					} else {
						cURLdownload(wpiTheme::DOC_PUB_HTACCESS_URL,$file);					
					}		
		}												
			
	}

	public function debugOptions()
	{
		wpi_dump(get_option(WPI_META_PREFIX.'settings'));		
	}
				
	/**
	 * Wpi::debug()
	 */
	public function debug()
	{
		wpi_dump($this);
	}								
}