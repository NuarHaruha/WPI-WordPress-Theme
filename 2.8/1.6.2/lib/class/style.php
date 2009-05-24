<?php
if (!defined('KAIZEKU')) exit(42);
/**
 * WP-iStalker Chrome Milk 
 * Theme constant
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

/**
 * wpiStyle
 * @uses $wp_styles WP_styles object (BackPress Scripts enqueue)
 * @since 0.1
 */
class wpiStyle
{
	
	/**
	 * wpiStyle::CSS_SEPARATOR
	 * URL separator for combine css
	 * @var string
	 * @access public
	 */
	const CSS_SEPARATOR = ',';
	
	
	public $css;
	
	
	public $tag = array();
	
		
	public $wp_filter_id;
	
	
	/**
	 * Store theme base url
	 * @var string
	 * @access public	 
	 */
	public $theme_url;	
	
	
	public $client_css_version; // set by browscap browser->CssVersion
	
	
	public $section = array();
	
	
	/**
	 * Wordpress base url
	 * 
	 * @var string
	 * @see $wp_styles
	 * @access public
	 * @since 1.6.2
	 */
	public $base_url;
	
	
	public $build;
	

	/**
	 * Store array list of wpiStyle error message
	 *	 
	 * @access	public
	 * @var		mixed|array
	 */		
	public $errors;
	
	
	/**
	 * Loop procedure counter
	 * @access	private
	 * @var		int
	 */	
	private $_flag;
	
	/**
	 * default css 
	 * @access	private
	 * @var		mixed|array
	 */	
	private $default_css;
	
			
	/**
	 * wpiStyle::__construct()
	 * class constructor
	 * 
	 * @uses $wp_styles WP_Styles object
	 */
	public function __construct()
	{ 	global $wp_styles;
				
		$this->theme_url = WPI_THEME_URL;
		
		if (! is_a($wp_styles,'WP_Styles')){
			$wp_styles = new WP_Styles();			
		}
		
		$this->base_url = $wp_styles->base_url;	
		
		$this->build = date('Ymd',SV_CURRENT_TIMESTAMP);
		
		//$this->defaultStyle()->_registerStyle($wp_styles);
		$this->setCSS();
	}
	
	
	/**
	 * void wpiStyle::defaultStyle()
	 * register default stylesheets
	 * 
	 * @see wpiStyle::$default_css
	 * @since 1.6.2
	 * @return object wpiStyle
	 */
	public function defaultStyle()
	{
    // lists of stylesheet filename without extension
		$css = array('framework','image','icecream','style');		
		
		$this->default_css = apply_filters(wpiFilter::DEFAULT_STYLESHEETS, $css);
		
		return $this;
	}	
	
	/**
	 * private wpiStyle::_registerStyle()
	 * 
	 * Register all theme stylesheet	 
	 * 
	 * @uses	$wp_styles WP_Styles object
	 * @access	private
	 * @since	1.6.2
	 * @param	mixed|object $deps WP_styles object 
	 * @todo reduce overhead, save file list to DB
	 */	
	private function _registerStyle(WP_Styles $deps)
	{
		if ( ($css = wpi_get_dir(WPI_CSS_DIR, wpiTheme::CSS_FILE_REGEX)) != false){
			
			if (has_count($css)){
								
				foreach($css as $file){
					$tag = str_rem('.css', $file);
					
					$this->stylesheets[] = $handle = wpiTheme::H_PREFIX.$tag;
					
					$dependencies = array('combine' => true);	
					
					$stylesheets = array_flip($this->default_css);					
					// section	
					if (isset($stylesheets[$tag])){
						$dependencies['section'] = wpiSection::K_ALL;					
					}																

					/**
					 * For dependencies with general caching plugins
					 * we exclude the browser stylesheets from
					 * being combine with the main stylsheets 
					 */				
					if ('user-agent.css' == $file) $dependencies = array();
										
					$deps->add($handle, self::relativeUrl($file, $deps->base_url), $dependencies, $this->build, 'all');
				}
				
				unset($css, $file, $handle);				
			}
						
		} else {
			$this->errors[] = __(__METHOD__.'() failed',WPI_META);		
		}			
	}
	
	
	public function setCSS()
	{
		$tag = array('framework','image','icecream','style');
		foreach($tag as $v){
			$this->register($v);
		}
	}
	
	
	public function register($tag = 'style', $section = false)
	{	
		$file = file_exists(WPI_CSS_DIR.$tag.'.css');
		$uri = $this->theme_url.$tag.'.css';
		
		if ($file && $section == false){
			$this->tag[] = $tag;			
			//wp_register_style($tag.'-css',$uri);
		} elseif($file && $section != false ) {
			$this->section[$section][] = $tag;			
			//wp_register_style($tag.'-css', $uri);			
		}else {	
			return false;
		}
	}

	public function printStylesheets()
	{ global $wp_styles;		
		
		$handle = wpiTheme::H_PREFIX.'combine';
		
		if (has_count($wp_styles->registered)){			
			$uri = str_rem($this->base_url, WPI_THEME_URL);			
			foreach($wp_styles->registered as $stylesheet){
				if (isset($stylesheet->deps['section']) 
				&& $stylesheet->deps['section'] == wpiSection::K_ALL){
					$this->css[] = str_rem($uri, $stylesheet->src);									
				}																
			}		
			
			$wp_styles->add($handle, 
				self::relativeUrl(implode(self::CSS_SEPARATOR,$this->css)),
				array('title'=> wpiTheme::UID), $this->build );
		}
		
		$wp_styles->enqueue($handle);
	}
	
			
	public function printStyles()
	{ global $is_gecko;
		$this->registerExtraCSS();
		
		if (has_count($this->tag)){
			
			$this->tag = array_unique($this->tag);
						
			$this->css = join(self::CSS_SEPARATOR,$this->tag);
						
		}
		
		// send via header?
		if (self::getOption('css_via_header') && $is_gecko){
			add_filter(wpiFilter::FILTER_SECTION_HEADER,array($this,'httpStyles'));
		} 
			elseif ( ! self::getOption('css_cia_header') 
			|| has_filter(wpiFilter::ACTION_INTERNAL_CSS) )
		{		
		//  internal
			add_action('wp_head',array($this,'internalStyles'));
		}
		
			
	}
	
	
	public function registerExtraCSS()
	{
		$sc = is_at();
		$this->register($sc);
		// year, month, date will used archive as sub stylesheets
		if ($sc == wpiSection::YEAR || $sc == wpiSection::MONTH 
		|| $sc == wpiSection::DAY){			
			$this->register(wpiSection::ARCHIVE);
		}
		
		if ($sc == wpiSection::PAGE || $sc == wpiSection::SINGLE){
			$this->register('comments');
		// attachment is inside single & page so it must be separated 
		// from the above conditional	
		} elseif($sc == wpiSection::ATTACHMENT){
			$this->register('comments');
		}
		
		$selectors  = str_rem('-foaf-Document', wpi_get_body_class(false, true));
		$selectors  = str_rem('archive', $selectors);		
		$selectors	= explode(" ", $selectors);
			
			foreach($selectors as $tag){
				$this->register($tag);
			}		
			
		unset($selectors,$tag);				
				
		$this->Avatar = new wpiGravatar();
		$this->Avatar->filterCSS($sc);	
		
		if (wpi_option('text_dir') == 'rtl'){
			$this->register('rtl');
		}

		if (isset($this->section[$sc])){
			foreach($this->section[$sc] as $tag){
				$this->register($tag);
			}
		}
				
	}
	
	public static function getOption($options)
	{
		return wpi_get_theme_option($options);
	}
	
	/**
	 * 
	 * @see wpiFilter::FILTER_SECTION_HEADER
	 * 
	 */
	
	public function httpStyles($h)
	{
		$css_url = wpi_get_stylesheets_url($this->css);
		$h[] = 'Link: <'.$css_url.'>; rel="stylesheet"; title="'.wpiTheme::UID.'"';
		
		return $h;		
	}
	
	public function externalStyles()
	{
		echo PHP_T._t('link','',array(
					'id'=>'wpi-css-combine',
					'type'=>'text/css',
					'rel'=>'stylesheet',
					'href'=> wpi_theme_content_url($this->css),
					'media'=>'screen',
					'title'=> wpiTheme::UID));		
	}
	
	public function internalStyles()
	{ global $wp_query;
		$this->externalStyles();			
	?>
	<style id="wpi-css-embed" type="text/css">
	/*<![CDATA[*/<?php if ( ! self::getOption('css_via_header') ): ?>
	
	@import url('<?php echo wpi_theme_content_url('user-agent'); ?>');	
	<?php endif; ?>	
		<?php do_action( wpiFilter::ACTION_INTERNAL_CSS); ?>
		<?php do_action( wpiFilter::ACTION_GRAVATAR_CSS ); ?>		
	/*]]>*/
	</style>	
	<?php		
	}
	

	
	public function debug()
	{ global $wp_styles;
		wpi_dump($wp_styles);
		exit();
	}
	
	public static function buildFlagFile()
	{
		$plugin = 'global-translator';
		$flags = wpi_get_dir(WP_PLUGIN_DIR.DIRSEP.$plugin.DIRSEP,'/\.png/');
		
		$template = '#%selector%{background-image:url('.WP_PLUGIN_URL.'/'.$plugin.'/%tag%)}'.PHP_EOL;
		
		$contents = "#translate{padding-top:6px}#translate small{margin-left:-4px;padding-right:3px}#translate a{background-repeat:no-repeat;display:block;float:left;height:11px;margin-right:3px;overflow:hidden;padding:0 !important;position:relative;text-indent:-999em;width:16px}".PHP_EOL;		
		foreach($flags as $tag){			
			list($selector,$extension) = explode('.',$tag);
			$contents .= strtr($template,array('%selector%'=>$selector,'%tag%'=>$tag));
		}
		
		wpi_fwrite(WPI_CSS_DIR.'translator.css',$contents);
	}


	/**
	 * static wpiStyle::relativeUrl()
	 * Make link relative to WP_CONTENT_URL
	 * 
	 * @uses	$wp_styles	WP_Style object
	 * @since	0.1
	 * @param	string $filename The css filename
	 * @param	string $base_url Wordpress URL  
	 * @return	string Relative URL
	 * 
	 */
	public static function relativeUrl($filename, $base_url=false)
	{
		if (!$base_url){
			global $wp_styles;
			$base_url = $wp_styles->base_url;		
		}
		
		return str_rem($base_url, WPI_THEME_URL.$filename);		
	}
	
		
	/**
	 * public wpiStyle::setFlag()
	 * set conditional procedure flag number 
	 * for debugging purpose
	 * 
	 * @param mixed|int	$int Number
	 * @return object 
	 */
	public function setFlag($int)
	{
		$this->_flag = (int) $int;	
		return $this;
	}	
} 