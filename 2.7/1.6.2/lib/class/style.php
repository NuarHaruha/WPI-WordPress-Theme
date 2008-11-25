<?php
if ( !defined('KAIZEKU') ) {
    die( 42);
}
/**
 * stylesheet class for wp-istalker 
 */	

class wpiStyle
{
	const CSS_SEPARATOR = ',';
	
	public $css;
	
	public $tag = array();
	
	public $wp_filter_id;
	
	public $theme_url;	
	
	public $client_css_version; // set by browscap browser->CssVersion
	
	public $section = array();
	
	public function __construct()
	{ 
		$this->setCSS();		
		$this->theme_url = WPI_THEME_URL;
		
		if (defined(WPI_REBUILD_FLAG)){
			self::buildFlagFile();
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
			wp_register_style($tag.'-css',$uri);
		} elseif($file && $section != false ) {
			$this->section[$section][] = $tag;			
			wp_register_style($tag.'-css', $uri);			
		}else {	
			return false;
		}
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
		
		$selectors  = str_rem('-foaf-Document',wpi_get_body_class());
		$selectors  = str_rem('archive',$selectors);		
		$selectors	= explode(" ",$selectors);
			
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
		t('link','',array(
					'id'=>'wpi-css',
					'type'=>'text/css',
					'rel'=>'stylesheet',
					'href'=> wpi_get_stylesheets_url($this->css),
					'media'=>'screen',
					'title'=> wpiTheme::UID));		
	}
	
	public function internalStyles()
	{ ?>
	<style id="wpi-css" type="text/css" title="<?php echo wpiTheme::UID; ?>" media="screen,projectile">
	/*<![CDATA[*/<?php if ( ! self::getOption('css_via_header') ): ?>
	
	@import url('<?php echo wpi_get_stylesheets_url($this->css);?>');
		<?php endif; ?>	<?php do_action( wpiFilter::ACTION_INTERNAL_CSS); ?>
		<?php do_action( wpiFilter::ACTION_GRAVATAR_CSS ); ?>
	/*]]>*/
	</style>	
	<?php		
	}
	

	
	public function debug()
	{
		var_dump($this);
	}
	
	public static function buildFlagFile()
	{
		$flags = wpi_get_dir(WPI_IMG_DIR.'flags'.DIRSEP);
		
		$template = '#%selector%{background-image:url(\'images/flags/%tag%\')}'.PHP_EOL;
		
		$contents = "#translate{padding-top:6px}#translate small{margin-left:-4px;padding-right:3px}#translate a{background-repeat:no-repeat;display:block;float:left;height:11px;margin-right:3px;overflow:hidden;padding:0 !important;position:relative;text-indent:-999em;width:16px}".PHP_EOL;		
		foreach($flags as $tag){			
			list($selector,$extension) = explode('.',$tag);
			$contents .= strtr($template,array('%selector%'=>$selector,'%tag%'=>$tag));
		}
		
		wpi_fwrite(WPI_CSS_DIR.'translator-image.css',$contents);
	}	
} 
?>