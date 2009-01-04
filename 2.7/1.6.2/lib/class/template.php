<?php
if (!defined('KAIZEKU')) { die(42); }
/**
 * $Id$
 * Wp-Istalker template class
 * @package WordPress
 * @subpackage Template 
 */

class wpiTemplate
{
	/**
 	 * Template section name
 	 * @var string
	 */   	
	public $section;	
	
	public $tpl;
	
	/**
 	 * WordPress $wp_filter id
 	 * @var int 
	 */   	
	public $wp_filter_id;
		
	/**
 	 * pad to content
 	 * @var int
	 */   	
	const TAB_CONTENT_SPACING = 3;	

	public function __construct()
	{ global $wp_query;
		
		add_filter('query_vars', array($this,'registerPublicVar'));
		$this->action('template_redirect','processVar');
		
		$this->action('init','flushWPRewriteRules');
		$this->action('generate_rewrite_rules','rewriteRules');		
		
		$this->action('init','registerWidgets');
		
		if ( ( $txt_direction = wpi_option('text_dir')) != 'ltr' && !is_admin()){
			$this->textDirection($txt_direction);
		}
		
		if (wpi_option('relative_links')){
			add_filter('wpi_links_home','rel');
			add_filter('wpi_links_single','rel');
			add_filter(wpiFilter::FILTER_LINKS,'rel');
		}		
				
		// http header
		$this->action('send_headers','httpHeader');

		if (wpi_option('banner')){
			$this->action(wpiFilter::ACTION_SECTION_PREFIX.'pathway_after', 'banner');
			$this->action(wpiFilter::ACTION_INTERNAL_CSS,'bannerIntenalCSS');
		}
				
		// dtd
		$this->action(wpiFilter::ACTION_DOCUMENT_DTD,'dtd',1);
		
		if (!wpi_option('meta_rsd')){
			remove_filter('wp_head','rsd_link',10,1);
		}
		
		if (!wpi_option('meta_livewriter')){
			remove_filter('wp_head','wlwmanifest_link',10,1);
		}
		
		if (!wpi_option('meta_wp_generator')){
			remove_filter('wp_head','wp_generator',10,1);
		}					
		
		// head
		$this->action( wpiFilter::ACTION_META_HTTP_EQUIV, 'metaHTTP');		
		$this->action( wpiFilter::ACTION_META, 'meta');
		$this->action( wpiFilter::ACTION_META_LINK, 'metaLink');			
		$this->action( 'wp_head', 'registerMetaActionFilters', 2);
		
		if (wpi_option('meta_title')){
			$this->action( 'wp_head', 'headTitle', 1);
		}	
		
		// custom content
		$this->action('wp_head','headCustomContent',wpiTheme::LAST_PRIORITY);
		$this->action('wp_footer','footerCustomContent',wpiTheme::LAST_PRIORITY);	
		
		/**
		 * Content
		 */		
		// header
		$gd_blogname = wpi_option('gd_blogname');
		$pages_menu = wpi_option('menu_page_enable');
		
		if (! $gd_blogname && ! $pages_menu){
			$this->action( wpiFilter::ACTION_TPL_HEADER, 'htmlBlogContentHeader');
			
		} elseif( $gd_blogname && ! $pages_menu) {			
			$this->action(wpiFilter::ACTION_INTERNAL_CSS, 'gdBlognameStyles',wpiTheme::LAST_PRIORITY+10);
			$this->action(wpiFilter::ACTION_SECTION_PREFIX.'nav_before','htmlBlogHeader');
			
		} elseif( $pages_menu && ! $gd_blogname){
			$this->action(wpiFilter::ACTION_SECTION_PREFIX.'nav_before','htmlBlogHeader');
			$this->action( wpiFilter::ACTION_TPL_HEADER, 'htmlPagesMenu');
			
		} elseif( $pages_menu && $gd_blogname ){
			$this->action(wpiFilter::ACTION_INTERNAL_CSS, 'gdBlognameStyles',wpiTheme::LAST_PRIORITY+10);
			$this->action( wpiFilter::ACTION_TPL_HEADER, 'htmlPagesMenu');
			$this->action(wpiFilter::ACTION_SECTION_PREFIX.'nav_before','htmlBlogHeader');
		}
		
		unset($gd_blogname,$pages_menu);
		
		// content
		if (wpi_option('relative_date')){
			
			if (!wpi_user_func_exists('time_since')){
				Wpi::getFile('timesince',wpiTheme::LIB_TYPE_IMPORT);
			}
			
			wpi_foreach_hook_filter(array( wpiFilter::FILTER_POST_DATE,
										   wpiFilter::FILTER_COM_DATE),
										   'wpi_get_relative_date');					
		}
		if (wpi_option('post_excerpt')){
			wpi_foreach_hook(array(
			wpiFilter::ACTION_BEFORE_CONTENT_PREFIX.'home',
			wpiFilter::ACTION_BEFORE_CONTENT_PREFIX.'single',
			wpiFilter::ACTION_BEFORE_CONTENT_PREFIX.'author'
			),'wpi_post_excerpt');
		}
		
		if (wpi_option('post_author_description')){
			add_action(wpiFilter::ACTION_AFTER_CONTENT_PREFIX.'single','wpi_post_author_descriptions');
		}
		
		add_action(wpiFilter::ACTION_SECTION_PREFIX.'meta-title_content',
			'wpi_content_meta_title_filter');
		
		
		$this->action(
		wpiFilter::ACTION_SECTION_PREFIX.'content-end_content','navLink');
		
		wpi_plugin_active_elif(array(
			'plugin'	=> 'wp-pagenavi/wp-pagenavi.php',
			'hook'		=> wpiFilter::ACTION_POST_PAGINATION,
			'fallback'	=> 'wpi_post_link'));
		
		add_filter(wpiFilter::FILTER_COMMENTS_SELECTOR,'wpi_post_author_selector_filter');
		add_filter('get_comment_text','wpi_get_comment_text_filter');
		
		// footer			
		$this->action('wp_footer','footerCopyright',1);
		$this->action(wpiFilter::ACTION_COPYRIGHT_STATEMENTS,'leftBottomContent');
		
		add_action('wp_footer','wpi_register_widgets');	
	}

	public function htmlBlogHeader(){
		
		$outer_class = apply_filters(wpiFilter::FILTER_SECTION_OUTER_CLASS,'outer cf');
		$inner_class = apply_filters(wpiFilter::FILTER_SECTION_INNER_CLASS,'inner c');		
		do_action(wpiFilter::ACTION_SECTION_PREFIX.'header_before');
?>
<dd id="wp-header">
	<div class="<?php echo $outer_class;?>">
		<div class="<?php echo $inner_class;?>">
			<div id="header">
						<?php $this->htmlBlogContentHeader();?>
			</div>
		</div>
	</div>		
</dd> <!-- /#wp-header -->
<?php		
		
	}
	
	public function navLink()
	{
		$str = ' WordPress Themes';
		$url = _t('a',WPI_BLOG_NAME,array('href'=>WPI_HOME_URL_SLASHIT,'title'=>WPI_BLOG_NAME,'rev'=>'vote-for'));
		$htm = _t('a',wpiTheme::THEME_NAME.$str,array('href'=>wpiTheme::THEME_URL,'class'=>'rn rtxt','id'=>'designer','title'=> wpiTheme::THEME_NAME.' WordPress Theme') );
		
		$htm .= _t('a','top',array('href'=>'#'.self::bodyID(),'title'=>'back to top','class'=>'rn rtxt top'));
				$htm .= _t('span','Copyright &#169; '.wpi_get_blog_since_year().' '.$url.'.',array('id'=>'copyright'));
		$htm = _t('p',$htm,array('class'=>'nav-links r'));
		
		echo $htm;
	}
	
	public function leftBottomContent(){
?>
			<div id="validation" class="pa">
				<dl class="validation xoxo fl">
					<dt><?php _e('Validation');?></dt>
					<dd>
						<a href="http://qa-dev.w3.org/unicorn/observe?ucn_task=conformance&amp;ucn_uri=<?php echo urlencode(WPI_HOME_URL_SLASHIT);?>&amp;ucn_lang=en" title="W3C Unicorn Universal Conformance Checker">W3C Unicorn</a>
					</dd>
					<dd>
						<a href="http://www.validome.org/xml/validate/?lang=en&amp;onlyWellFormed=1&amp;url=<?php echo urlencode(WPI_HOME_URL_SLASHIT);?>" title="Valid XHTML+XML Documents (structured well-formed)">XML</a>
					</dd>
					<dd> 
						<a href="http://www.contentquality.com/mynewtester/cynthia.exe?Url1=<?php echo urlencode(WPI_HOME_URL_SLASHIT);?>" title="Web Content Accessibility Valid Section 508 Standards" rel="nofollow noarchive">508</a>
					</dd>
				</dl>
				<dl class="syndication xoxo fl cf">
					<dt><?php _e('Syndication');?></dt>
					<dd> 
						<a href="<?php bloginfo('comments_rss2_url'); ?>" title="Comments RSS feed" rel="rss2" type="application/rss+xml">Comments</a>
					</dd>
					<dd> 
						<a href="<?php bloginfo('rss2_url'); ?>" title="Articles RSS feed" rel="rss2" type="application/rss+xml">Articles</a>
					</dd>										
					<dd> 
						<a href="http://tools.microformatic.com/transcode/rss/hatom/<?php echo WPI_HOME_URL_SLASHIT;?>" title="Raw hAtom feeds" rel="atom" type="application/rss+xml">Atom</a>
					</dd>
				</dl>				
			</div>
			
<?php		
	}
	
	public function registerWidgets()
	{
		wpi_register_widgets();
	}
	

	public function action($hook,$method_name,$priority = 10)
	{
		add_action($hook,array($this,$method_name),$priority);
	}
	
	
	public static function bodyID()
	{		
		return strtr(WPI_HOME_URL_SLASHIT,array('http://'=>'','.'=>'-','/'=>'-') );
	}
	
			
	public static function getContentMIMEType()
	{
		$html_type = get_bloginfo('html_type');
		
		if (defined('WPI_CLIENT_ACCEPT_XHTML_XML') 
			&& WPI_CLIENT_ACCEPT_XHTML_XML && wpi_option('xhtml_mime_type')){
			$html_type = 'application/xhtml+xml';
		} else {
			$html_type = 'text/html';
		}
		
		return $html_type;
	}
	
	/**
	 * Display custom header content on singular sections
	 * @since 1.6.2
	 * @uses wp_query WP_Query object
	 * @uses wpi_get_postmeta() get head content postmeta
	 * @return string HTML output
	 */
	public function headCustomContent()
	{	global $wp_query;
		
		if (!$wp_query->is_singular) return;
		
		if ( ($content = wpi_get_postmeta('header_content') ) != false ){
			$content = apply_filters(wpiFilter::FILTER_CUSTOM_HEAD_CONTENT,$content);
			echo PHP_EOL.PHP_T.$content.PHP_EOL;
		}			
	}
	
	/**
	 * Display custom footer content on singular sections
	 * @since 1.6.2
	 * @uses wp_query WP_Query object
	 * @uses wpi_get_postmeta() get head content postmeta
	 * @return string HTML output
	 */			
	public function footerCustomContent()
	{	global $wp_query;
		
		if (!$wp_query->is_singular) return;
		
		if ( ($content = wpi_get_postmeta('footer_content') ) != false ){
			$content = apply_filters(wpiFilter::FILTER_CUSTOM_FOOTER_CONTENT,$content);
			echo PHP_EOL.PHP_T.$content.PHP_EOL;
		}		
	}
	

	public function textDirection($direction = 'ltr')
	{	global $wp_locale;
		
		$wp_locale->text_direction = $direction;
		
	}
	
	public function httpHeader()
	{
		$h = array();	
		
		if (defined('WPI_CLIENT_ACCEPT_XML') && WPI_CLIENT_ACCEPT_XML){	
        	
        	if (WPI_CLIENT_ACCEPT_XHTML_XML && wpi_option('xhtml_mime_type') ){        		
        		$h[] = wpiTheme::CTYPE_XML.';charset='.get_bloginfo('charset');   		
        	}
        }
		
        $h[] = 'Content-Style-Type: text/css';
        $h[] = 'Content-Script-Type: text/javascript';
        $h[] = 'Content-Language:'.self::getContentLanguage();
        $h[] = 'Theme:'.wpiTheme::UID.'/'.wpiTheme::VERSION.'; url:'.wpiTheme::DOC_URL;
        
		if ( ($icra = wpi_option('icra_label')) != false ){
			$h[] = 'pics-Label:'.stripslashes_deep($icra);
		} 
		
		if ( ($xxrds = wpi_option('xxrds')) != false ){
			$h[] = 'X-XRDS-Location:'.$xxrds;
		}
        
		$h[] = 'Default-Style:'.wpiTheme::UID;	
        $h[] = 'X-Hacker:'.wpiTheme::SELF_MSG;
        	
        $h = apply_filters(wpiFilter::FILTER_SECTION_HEADER, $h);
        	
        if (has_count($h)){        		
        	foreach($h as $request) @header($request);
        
		}
			
		unset($h,$request);   				
	}	
	

	public static function getContentLanguage()
	{
		$lang = get_option('rss_language');		
		$lang = ($lang == 'en') ? 'en-US' : 'en';
		
		return apply_filters(wpiFilter::FILTER_CONTENT_LANGUAGE,$lang);		
	}
	
	public static function dtd()
	{	global $is_IE, $Wpi;
		
		if (defined('WPI_CLIENT_ACCEPT_XML') && WPI_CLIENT_ACCEPT_XML){			
			$charset = get_bloginfo('charset');			
			$xml = '<?xml version="1.0" encoding="'.$charset.'"?>'.PHP_EOL;
			
			if (! $is_IE && wpi_option('xhtml_mime_type') && ! wpi_option('css_via_header')){
			
				$css_url = wpi_get_stylesheets_url($Wpi->Style->css);
					
		        $xml .='<?xml-stylesheet href="'.$css_url.'" title="'.wpiTheme::UID.'" type=""text/css""?>'.PHP_EOL;
	        }
        	
        }
        
	        $dtd = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'.PHP_EOL;
	        
	        $output = apply_filters(wpiFilter::FILTER_PUBLIC_DTD,$dtd);
        
        if (isset($xml)) $output = $xml.$output;		
        
		echo $output;	
	}
	
	public function headTitle()
	{	global $wp_query;
		
		
		$name  = $output = WPI_BLOG_NAME;
		
		$separator = ' '.WpiTheme::BLOG_TITLE_SEPARATOR.' '; // >> chars			
						
		if ($wp_query->is_home ){
			$output .= $separator.' '.wpi_safe_stripslash(get_bloginfo('description'));
			
		} elseif ($wp_query->is_single || $wp_query->is_page){
			
			if ( ($mtitle = wpi_get_postmeta('maintitle') ) != false ){
				$output = $mtitle.$separator.$name;	
			} else {
				$output = wp_title($separator,false,'right').$name;
			}
			
		} elseif ($wp_query->is_search){
			$term = wp_specialchars(get_query_var('s'), 1);
			$output  = sprintf(__('%1$s - Search results for: %2$s',WPI_META),$name,$term);
					
		} elseif($wp_query->is_404){
			$output  .= ' '.$separator.__(' 404 Error: Page not found',WPI_META);
			
		} else {
			$output = wp_title($separator,false,'right').$name;
			
		}
	
		$output = '<title>'.$output.'</title>';			
		$output = apply_filters(wpiFilter::FILTER_BLOG_TITLE, $output);
		
	
		echo $output.PHP_EOL;

	}
	
	
	public function registerMetaActionFilters()
	{
		
		$filters = array(wpiFilter::ACTION_META_HTTP_EQUIV,wpiFilter::ACTION_META,wpiFilter::ACTION_META_LINK);
		
		foreach($filters as $action) do_action($action);
		
		unset($filters,$action);
	}
	
	public function metaHTTP()
	{
		
		$m = array();		
		
		$m[] = array('http-equiv' => 'Content-Type',
					 'content'	=> self::getContentMIMEType().'; charset='.get_bloginfo('charset'));			 
		
		if (has_count($m)){	
			foreach($m as $attribs)	echo "\t"._t('meta','',$attribs);
		}	
		
		unset($m,$attribs);			
	}
	
	
	public function meta()
	{ global $wp_query;
		
		$m = array();
		$section = is_at();
	
		$m[] = array('name'=> 'distribution','content'=> 'global');					 
		$m[] = array('name'=>'rating','content'=>'general');						 	
		$m[] = array('name'=>'designer','content'=>'Avice De&#39;v&eacute;reux; url:http://blog.kaizeku.com');

			if ($wp_query->is_singular ){
				$aid = (int) $wp_query->post->post_author;
				$user = get_userdata($aid);
				$name = ent2ncr(htmlentities2($user->display_name));
				
				unset($aid,$user);
				
				$m[] = array('name'=> 'author','content'=> $name);
			}
		
		// Robots exclusion rules	
		if (get_option('blog_public') != '0' && wpi_option('meta_robots')){
			
			$nodp = false; // @todo get theme options
			
	        $robots = sprintf('%s, follow', (((is_home() || is_single() || is_page()) && !is_paged()) ? 'index' : 'noindex')); 
	        
			if ($nodp){
				$robots .= ', noodp';
			} 
			
				if (is_wp_version(2.7,'>=')){					
					if ($wp_query->is_singular && get_option('page_comments'))
					{			
						if (isset($wp_query->query['cpage']) 
							&& absint($wp_query->query['cpage']) >= 1 )
						{
							$robots = 'noindex';			
						}
					}	
				}
				
			if ($wp_query->is_author && wpi_option('meta_robots_author')){
				$robots = 'noindex, follow';	
			}
			
			if ($wp_query->is_search && wpi_option('meta_robots_search')){
				$robots = 'noindex, follow';	
			} 									   
			   
	        $m[] = array('name'	=> 'robots','content' => $robots);	
		}

		/**
		 * Prevents Google Translation services
		 * {@link http://googlewebmastercentral.blogspot.com/2008/10/helping-you-break-language-barrier.html Excluding Google translator}
		 */			 		
		if (wpi_option('google_notranslate')){
			// the attributes should be 'content=%s' not 
			// 'value=%s; but google has their own standards pigeon
			$m[] = array('name'=> 'google','value'=>'notranslate');
		}
		
		
		// DC metadata
		$m[] = array('name'=> 'DC.type','content'=>'text','scheme'=>'DCTERMS.DCMIType');					 
		$m[] = array('name'=> 'DC.format','content'=>self::getContentMIMEType(),'scheme'=>'DCTERMS.IMT');	
		$m[] = array('name'=> 'DC.language','content'=> self::getContentLanguage(),'scheme'=> 'DCTERMS.RFC3066');	
		$m[] = array('name'=> 'DC.identifier','content'=> self_uri(),'scheme'=> 'DCTERMS.URI');
		$m[] = array('name'=> 'DC.title','content'=> wp_title('&#187;',false,'right').WPI_BLOG_NAME );
		
		/**
		 * Site Meta-tag verifications
		 */
		 
		 $prop = array(
		 	'verify_google_webmaster' => 'verify-v1',
		 	'verify_yahoo_explorer' => 'y_key',
		 	'verify_msn' => 'msvalidate.01'
		 );
		 
		foreach($prop as $meta => $name){
			if ( ($token = wpi_option($meta)) != '' ){
				$m[] = array('name'=> $name, 'content'=> stripslashes_deep($token) );
			} 
		}
		
		unset($prop,$meta,$name);
					 
		if ($section == wpiSection::HOME){
			if (wpi_option('meta_description')){
				$prop = wpi_safe_stripslash(wpi_option('def_meta_description'));
				
				if ('' == $prop){
					$prop = attribute_escape(get_option('blogdescription'));
				}				
				
				$prop = apply_filters(wpiFilter::FILTER_META_DESCRIPTION,$prop);
				
				$m[] = array('name'=>'description','content' => $prop );
			}
			
			// keywords
			if (wpi_option('meta_keywords')){	
				$prop = wpi_option('def_meta_keywords');	
				
				if ('' == $prop){
					$prop = WPI_BLOG_NAME;
				}
				
				$prop = apply_filters(wpiFilter::FILTER_META_KEYWORDS,$prop);
				
				$m[] = array('name'=>'keywords','content' => $prop);
			}			
		}
		
		$is_desc = wpi_option('meta_description');
		$is_keyswords = wpi_option('meta_keywords');
		
		if ($section == wpiSection::SINGLE || $section == wpiSection::PAGE){
			global $post;
		
			if ( ($subtitle = wpi_get_postmeta('subtitle') ) != false ){
				$m[] = array('name'=>'abstract','content'=> attribute_escape($subtitle));
			}			
			
			if ($is_desc){
				if ( ($desc = wpi_get_postmeta('meta_description')) != false){				
					$m[] = array('name'=>'description','content'=> attribute_escape($desc));
				}	 				
			}
			
			if ($is_keyswords){
				if ( ($keywords = wpi_get_postmeta('meta_keywords')) != false){				
					$m[] = array('name'=>'keywords','content'=> attribute_escape($keywords));
				}	 				
			}			
		}	
		
		if ($section == wpiSection::CATEGORY || $section == wpiSection::TAXONOMY){			
			if ($is_desc){
			global $cat;
				
				$cat = get_category($cat);
				if ( ($desc = $cat->category_description) != '' ){
					$m[] = array('name'=>'description','content'=> attribute_escape($desc));				
				} else {
					$desc = WPI_BLOG_NAME.'&#39;s archive for '.$cat->name.', there is '.$cat->count.' articles in this category';
					$m[] = array('name'=>'description','content'=> attribute_escape($desc));				
				}
			}	
		}	
					 
				 
		$geourl = wpi_get_theme_option('geourl');	
				 
		if ($geourl && !empty($geourl)){
			
			$geo_position = explode(",",$geourl);
			
			$m[] = array('name'=>'geo.position','content'=> $geo_position[0].';'.$geo_position[1]);						 		
			$m[] = array('name'=>'ICBM','content'=> $geourl);					 				 	
		}
		
		$microid = wpi_get_theme_option('microid_hash');	
				 
		if ($microid && !empty($microid)){
			$m[] = array('name'=>'microid','content'=> $microid);				 				 	
		}
		
		if (has_count($m)){	
			foreach($m as $attribs)	echo PHP_T._t('meta','',$attribs);
		}	
		
		unset($m,$attribs);
	}
	
	
	function metaLink()
	{	global $wp_query;
		$m = array();
		
		$m[] = array('rel'	=> 'schema.DC',
					 'href'	=> 'http://purl.org/dc/elements/1.1/');	
		
		$m[] = array('rel'	=> 'schema.DCTERMS',
					 'href'	=> 'http://purl.org/dc/terms/');
					 	
		$m[] = array('rel'	=> 'schema.foaf',
					 'href'	=> 'http://xmlns.com/foaf/0.1/');
					 			 	
		$m[] = array('rel'	=> 'transformation',
					 'href'	=> 'http://www.w3.org/2000/06/dc-extract/dc-extract.xsl');
					 
		$m[] = array('rel'	=> 'transformation',
					 'href'	=> 'http://www.w3.org/2003/12/rdf-in-xhtml-xslts/grokCC.xsl');
				 
		$claimid = wpi_option('claimid');
		
		if ($claimid && !empty($claimid)){
			$m[] = array('rel'	=> 'openid.server',
						 'href'	=> 'http://openid.claimid.com/server');	
						 				 
			$m[] = array('rel'	=> 'openid.delegate',
						 'href'	=> 'http://openid.claimid.com/'.$claimid);	
		}
		
		if (file_exists(WP_ROOT.DIRSEP.'my-pavatar.png')){
			$m[] = array('rel'	=> 'pavatar',
						 'href'	=> WPI_URL_SLASHIT.'my-pavatar.png',
						 'title'=> WPI_BLOG_NAME);		
		}
		
		// assume label.rdf are in root WP dir
		if (file_exists(WP_ROOT.DIRSEP.'labels.rdf')) { 
			$m[] = array('rel'	=> 'meta',
						 'href'	=> WPI_URL_SLASHIT.'labels.rdf',
						 'type'	=> 'application/rdf+xml');					 
		}	
	
		$m[] = array('rel'	=> 'foaf-maker',
					 'href'	=> WPI_HOME_URL.'/#'.self::bodyID(),
					 'rev'	=> 'foaf-homepage foaf-made');
		
		// favicon
		$favicon_url = wpi_get_favicon_url();
		
			$mime = explode(".",$favicon_url);
			$mime = (string) $mime[count($mime) - 1];
			$mime = ($mime == 'ico') ? 'image/x-ico' : 'image/'.$mime;
		
		$m[] = array('rel'	=> 'shortcut icon','href'=> $favicon_url,'type'=>'image/vnd.microsoft.icon');	
	
		$m[] = array('rel'	=> 'icon','href'=> $favicon_url,'type'=>$mime);	
		
	
		// iphone
		if ( ($prop = wpi_option('icn_iphone')) != '' ){
			$m[] = array('rel'=>'apple-touch-icon','href'=> $prop);				
		}		
			
		$m[] = array('rel'	=> 'alternate',
					 'href'	=> get_bloginfo('rss_url'),
					 'type'	=> 'application/rss+xml',
					 'title'=> WPI_BLOG_NAME.'&#39;s RSS 0.92 Feed',
					 'rev'	=> 'syndication:rss');
					 
		$m[] = array('rel'	=> 'alternate',
					 'href'	=> get_bloginfo('rss2_url'),
					 'type'	=> 'application/rss+xml',
					 'title'=> WPI_BLOG_NAME.'&#39;s RSS 2.0 Feed',
					 'rev'	=> 'syndication:rss2');
	
		$m[] = array('rel'	=> 'alternate',
					 'href'	=> get_bloginfo('atom_url'),
					 'type'	=> 'application/rss+xml',
					 'title'=> WPI_BLOG_NAME.'&#39;s Atom Feed',
					 'rev'	=> 'syndication:atom');
	
		$m[] = array('rel'	=> 'alternate',
					 'href'	=> get_bloginfo('rdf_url'),
					 'type'	=> 'application/rss+xml',
					 'title'=> WPI_BLOG_NAME.'&#39;s RDF Feed',
					 'rev'	=> 'syndication:rdf');
					 
		$m[] = array('rel'	=> 'alternate',
					 'href'	=> get_bloginfo('comments_rss2_url'),
					 'type'	=> 'application/rss+xml',
					 'title'=> WPI_BLOG_NAME.'&#39;s comments RSS 2.0 Feed',
					 'rev'	=> 'syndication:rdf');

		$m[] = array('rel' 	=> 'pingback',
					 'href'	=> get_bloginfo('pingback_url'));

		$m[] = array('rel'	=> 'start',
					 'href'	=> rel(WPI_HOME_URL.'/#'.self::bodyID()) );
					 
		// prefetch 
		if ($wp_query->is_singular){
			
			list($href,$title) = $prop = wpi_get_prev_post_link();
			
				if ($prop){
					$href = rel($href);
				} else {
					$ref = wp_get_referer();
					$href = (!empty($ref)) ? $ref : WPI_HOME_URL_SLASHIT;
					$title = 'Previous referrer '.WPI_BLOG_NAME;
				}	
				
					$m[] = array('rel'	=> 'prev',
								 'href'	=> $href,
								 'title'=> $title);						 
			
			list($href,$title) = $prop = wpi_get_next_post_link();
			
				if ($prop && ! $wp_query->is_attachment){
					$m[] = array('rel'	=> 'next',
								 'href'	=> rel($href),
								 'title'=> $title);
				}		 
		}				 				 				 

		$m[] = array('rel'	=> 'copyright',
					 'href'	=> rel(WPI_HOME_URL_SLASHIT.'/#copyright') );			

				 
	// 
		if (has_count($m)){
			foreach($m as $attribs) echo "\t"._t('link','',$attribs);	
			
			unset($m,$attribs);
		}
		
		echo "\t".$this->osdLink();
		
		// #12 query (+1) 
		//wp_get_archives(array( 'type' => 'monthly','format' => 'link'));	
	}
	
	public function sectionStart()
	{
		$outer_class = apply_filters(wpiFilter::FILTER_SECTION_OUTER_CLASS,'outer cf');
		$inner_class = apply_filters(wpiFilter::FILTER_SECTION_INNER_CLASS,'inner c');
		
		do_action(wpiFilter::ACTION_SECTION_PREFIX.$this->section.'_before');
		
		$output = '<dd id="wp-'.$this->section.'">'.PHP_EOL;
		$output .= PHP_T.'<div class="'.$outer_class.'">'.PHP_EOL;
		$output .= PHP_T.PHP_T.'<div class="'.$inner_class.'">'.PHP_EOL;
		$output .= stab(0).'<div id="'.$this->section.'" class="content cb cf">'.PHP_EOL;
		echo $output;
		unset($output,$outer_class,$inner_class);
		
		do_action(wpiFilter::ACTION_SECTION_PREFIX.$this->section.'_content');
	}
	
	public function sectionEnd()
	{		
		$output = stab(0).'</div>'.PHP_EOL;
		$output .= PHP_T.PHP_T.'</div>'.PHP_EOL;
		$output .= PHP_T.'</div>'.PHP_EOL;
		$output .= '</dd>'.'<!-- /#wp-'.$this->section.' -->'.PHP_EOL;
		echo $output;
		unset($output,$outer_class,$inner_class);
		
		do_action(wpiFilter::ACTION_SECTION_PREFIX.$this->section.'_after');
	}
	
	public static function spacing($increase_spacing = false)
	{
		$output = str_repeat(PHP_T,self::TAB_CONTENT_SPACING);
		if ($increase_spacing){
			$output .= str_repeat(PHP_T,$increase_spacing);
		}
		
		return $output;
	}
	
	public function htmlPagesMenu()
	{
		
		$output = _t('ul',wpi_get_pages_link(),array('id'=>'page-links'));
		
		$output = stab()._t('div',PHP_EOL.$output.stab(),array('id'=>'pages-menu','class'=>'dc-subject fl'));
		
		echo $output;
	}	

	public function htmlBlogContentHeader()
	{
		
		$output = $this->getHtmlBlogname();
		$output .= $this->getHtmlBlogDescription();
		$output = stab()._t('div',PHP_EOL.$output.stab(),array('id'=>'blog-meta','class'=>'dc-subject ox fl'));
		
		echo $output;
	}
	
	public function getHtmlBlogname()
	{ global $wp_query;
	
		$blog_name = (wpi_option('gd_blogname_text')) ? wpi_option('gd_blogname_text') : WPI_BLOG_NAME;
		
		$attribs = array('href'=> rel(trailingslashit(WPI_HOME_URL_SLASHIT)),
		'rel'=>'home','title'=> $blog_name,'class'=>'url fn');
			
		if (wpi_option('gd_blogname')){
			$attribs['class'] .= ' db rtxt rn';
			$attribs['style'] = 'height:'.ceil(wpi_option('gd_blogname_text_size') + 12).'px;' ;
			
		}	
		
		$output = _t('a',$blog_name,$attribs);
		
		$heading_type = ($wp_query->is_home) ? 'h1' : 'h2';		
		
		$output =  self::spacing()._t($heading_type,_t('strong',$output),array('id'=>'blog-title'));		
		
		return $output;
	}
	
	
	/**
	 * Append gd_blogname stylesheet
	 * void wpiTemplate::gdBlognameStyles()
	 * hook: wpiFilter::ACTION_INTERNAL_CSS
	 */
	public function gdBlognameStyles()
	{	
		
		$base_height = 72;
		$height = abs(wpi_option('gd_blogname_text_size'));
		$inc = 8;
		$cache = wpi_option('cache_webfont');
		
		$uri = wpi_get_webfont_url(wpi_option('gd_blogname_text'),$height,wpi_option('gd_blogname_font'),wpi_option('gd_blogname_color'),$cache);
		
		$css = PHP_T.'#blog-meta{';
		$css .= 'height:'.(ceil($base_height+$height)) .'px !important';
		$css .= '}'.PHP_EOL;		
		$css .= PHP_T.'#blog-title a{';
		$css .= 'background-image:url(\''.$uri.'\') !important;height:'. ceil($height + $inc) .'px';
		$css .= ';width:700px;display:block}'.PHP_EOL;
		echo $css;
	}
	
	public function getHtmlBlogDescription()
	{
		$output = PHP_EOL;
		$output .= self::spacing(1)._t('p',get_bloginfo('description'),
							array('id'=>'blog-description','class'=>'note site-summary'));
		
		$output = self::spacing()._t('blockquote',self::spacing(1).$output.self::spacing(),
						array('cite'=> self_uri().'#blog-description',
						'class'=>'description'));
		return $output;		
	}
	
	public function htmlDebugInfo()
	{
		$output = _t('p', _t('strong',get_num_queries()).
				 ' queries. '.timer_stop(0).' seconds',array('class'=>'error'));
		
		echo $output;
	}	
	
	public function footerCopyright()
	{
		do_action(wpiFilter::ACTION_COPYRIGHT_STATEMENTS);
	}
	
	public function htmlCopyright()
	{
		$year = date('Y');
?>
<p id="theme-copyright" class="r"> WP-iStalker Chrome theme designed by <a href="<?php echo wpiTheme::THEME_URL;?>" rev="vote-for" class="url fn" title="Avice Devereux">Avice Devereux</a>.</p>
<p id="copyright">&copy; <span title="<?php echo $year;?>"><?php echo romanNumerals($year); ?></span>. <?php bloginfo('blogname');?>.</p>
<?php		
	}	
	
	public function banner()
	{	
		if (!self::bannerReady()) return;
		$this->bannerContentFilters();
		
		$output = '<dd id="wp-banner">'.PHP_EOL;
		$output .= PHP_T.'<div class="outer">'.PHP_EOL;
		$output .= stab(2).'<div class="inner icontent c">'.PHP_EOL;
		$output .= stab(3).'<div id="banner" class="content cb cf">'.PHP_EOL;
		echo $output;
		do_action(wpiFilter::ACTION_BANNER_CONTENT);			
		$output = stab(3).'</div>'.PHP_EOL;
		$output .= stab(2).'</div>'.PHP_EOL;
		$output .= PHP_T.'</div>'.PHP_EOL;
		$output .= '</dd>'.'<!-- /#wp-banner -->'.PHP_EOL;
		echo $output;
		unset($output);		
	}
	
	public static function bannerReady()
	{ global $wp_query;
		
		$bn = wpi_option('banner_na');
		$op = ( (is_at() == $bn) ? false : true );
		
		
		if ($wp_query->is_singular){
			$op = (wpi_get_postmeta('banner')) ? true : false;
		}
		
		if ($wp_query->is_author){
			/**
			 * @todo	inconsistent, user_show_banner should return false not null
			 */
			$op = (isset($wp_query->queried_object->user_show_banner)) ? true : false;
					
		}		
		
		return $op;
	}
	
	public function bannerContentFilters()
	{ global $wp_query;
		
		// 1. animepaper banner filter
		$uri = wpi_option('banner_url');
		
		if ($wp_query->is_single || $wp_query->is_page){
			$uri = wpi_get_postmeta('banner_url');
		}
		
		if ($wp_query->is_author){
			$uri = $wp_query->queried_object->user_banner_url;
		}
		
		// filter uri
		if ($uri == 'http://static.animepaper.net/upload/rotate.jpg'){
			add_action(wpiFilter::ACTION_BANNER_CONTENT,array($this,'randomAPBannerLinks'),wpiTheme::LAST_PRIORITY);
		}
	}
	
	/**
	 * Using animepaper random wallpaper banner require
	 * a link back to animepaper.net <support@animepaper.net>
	 */
	public function randomAPBannerLinks()
	{				
		t('a','animepaper',array('class'=>'rtxt icn-16 ttip','rev'=>'vote-for','rel'=>'dc-source','title'=>'Random Wallpaper | by Animepaper Community','style'=>'width:100%;height:'.wpi_option('banner_height'),'href'=>'http://www.animepaper.net'));
		
	}
	
	public function bannerIntenalCSS()
	{
		if (!self::bannerReady()) return;
		$sc = is_at();
		$is_articles = ($sc == wpiSection::SINGLE || $sc == wpiSection::PAGE) ? true : false;		
		
		$css = PHP_EOL;
		$burl = wpi_option('banner_url');
		$height = wpi_option('banner_height');
		$repeat = wpi_option('banner_repeat');	
		$position = wpi_option('banner_position');
		
		// global image url	
		$burl = (!empty($burl)) ? $burl : 'http://static.animepaper.net/upload/rotate.jpg';		
		$height = (!empty($height)) ? $height : '72px';			
		$repeat = (!empty($repeat)) ? $repeat : 'no-repeat';
		$position = (!empty($position)) ? $position : '0% 0%';
		
		if ($is_articles){
			
			if ( ($purl = wpi_get_postmeta('banner_url') ) != false ){
				$burl = (!empty($purl) ) ? $purl : $burl;
			}
						
			if ( ($pheight = wpi_get_postmeta('banner_height') ) != false ){
				$height = (!empty($pheight)) ? $pheight : $height;
			}	
						
			if ( ($prepeat = wpi_get_postmeta('banner_repeat') ) != false ){
				$repeat = (!empty($prepeat)) ? $prepeat : $repeat;
			}			
			
			if ( ($pposition = wpi_get_postmeta('banner_position') ) != false ){
				$position = (!empty($pposition)) ? $pposition : $position;
			}				
		}
		
		if ($sc == wpiSection::AUTHOR){			
		global $wp_query;
		
			$puser = $wp_query->queried_object; 
				 
			if ( ($url = $puser->user_banner_url) != false ){
				$burl = (!empty($url) ) ? $url : $burl;
			}
						
			if ( ($uheight = $puser->user_banner_height ) != false ){
				$height = (!empty($uheight)) ? $uheight : $height;
			}	
						
			if ( ($urepeat = $puser->user_banner_repeat ) != false ){
				$repeat = (!empty($urepeat)) ? $urepeat : $repeat;
			}
			
			if ( ($uposition = $puser->user_banner_position ) != false ){
				$position = (!empty($uposition)) ? $uposition : $position;
			}
						
			unset($puser);				
		}		
		
		if ( ($hbanner = wpi_has_banner()) != false) {		
			$burl = strtr($burl,array('%RANDOM_BANNER_URL%'=>$hbanner['uri'],'%BANNER_URL%'=>THEME_IMG_URL.'banner/') );
			unset($hbanner);		
		}
		
		
		$css .= PHP_T.'#banner{background-color:#f9f9f9;background-image:url(';
		$css .= $burl.');border-bottom:2px solid #ddd;';
		$css .= 'border-top:1px solid #999;height:'.$height.';';
		$css .= 'background-position:'.$position.';background-repeat:'.$repeat;
		$css .= '}'.PHP_EOL;
		
		echo $css;
		unset($css,$burl,$height,$position,$repeat);
	}
	
	public function osdLink()
	{ global $wp_rewrite;		
		
		$params = '?'.wpiTheme::PUB_QUERY_VAR.'=osd';
		
		if ($wp_rewrite && $wp_rewrite->using_permalinks() ){
			$params = wpiTheme::PUB_QUERY_VAR.'/osd/';	
		}
		
		$url = apply_filters(wpiFilter::FILTER_LINKS,WPI_HOME_URL_SLASHIT.$params);
		
		return _t('link','',array(
			'rel'=>'search',
			'type'=>'application/opensearchdescription+xml',
			'href'=> $url,
			'title'=> WPI_BLOG_NAME.__('! Search',WPI_META) ) );
		
	}
	
	
	public function registerPublicVar($query) {
		$query[] = wpiTheme::PUB_QUERY_VAR;
		$query[] = wpiTheme::PUB_QUERY_VAR_CSS;
		$query[] = wpiTheme::PUB_QUERY_VAR_JS;
		return $query;
	}
	
	public function processVar() {
		
		$css		= get_query_var(wpiTheme::PUB_QUERY_VAR_CSS);		
		$js			= get_query_var(wpiTheme::PUB_QUERY_VAR_JS);
				
		if ( ($option = get_query_var(wpiTheme::PUB_QUERY_VAR)) != false ){
			
			if ($option == 'osd') wpi_get_osd();
			
			if ( stristr($option,'reply') ){
				$args = explode(wpiTheme::PARAMS_SEP,$option);
				wpi_get_reply_form($args);
			}
			/*
			if ( stristr($option,'webfont') ){
				wpi_get_webfont(explode(wpiTheme::PARAMS_SEP,$option));
			}*/			

		}
		
		if ($css) wpi_get_public_content($css,'css');
		
		if ($js) wpi_get_public_content($js,'js');			
	}
	
	public function flushWPRewriteRules()
	{ global $wp_rewrite;	
		$wp_rewrite->flush_rules();
	}
	
	public function rewriteRules( $wp_rewrite ) 
	{
	  $new_rules = array( 
	    wpiTheme::PUB_QUERY_VAR.'/(.+)' =>'index.php?'.wpiTheme::PUB_QUERY_VAR.'='.$wp_rewrite->preg_index(1),
		wpiTheme::PUB_QUERY_VAR.'/reply/(.+)' => 'index.php?'.wpiTheme::PUB_QUERY_VAR.'=reply/'.$wp_rewrite->preg_index(1),/*
		wpiTheme::PUB_QUERY_VAR.'/webfont/(.+)' => 'index.php?'.wpiTheme::PUB_QUERY_VAR.'=webfont/'.$wp_rewrite->preg_index(1),	*/
		wpiTheme::PUB_QUERY_VAR_CSS.'/(.+)' => 'index.php?'.wpiTheme::PUB_QUERY_VAR_CSS.'='.$wp_rewrite->preg_index(1),
		wpiTheme::PUB_QUERY_VAR_JS.'/(.+)' => 'index.php?'.wpiTheme::PUB_QUERY_VAR_JS.'='.$wp_rewrite->preg_index(1));
	
	  $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
	  
	}				
}