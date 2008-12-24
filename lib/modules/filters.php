<?php
if ( !defined('KAIZEKU') ) {   die( 42);}
/**
 * $Id$
 * WPI default actions and filters functions
 * 
 * @package WordPress
 * @subpackage Template 
 */
 
function wpi_register_actions(array $hook_array, $is_callback = false) {
	
	if($is_callback ) {
		
		foreach($hook_array as $filter_name){
			add_action($filter_name,$is_callback);
		}
		
	} else {
		
		foreach($hook_array as $filter_name => $callback){
			add_action($filter_name,$callback);
		}		
	}	
	
	unset($hook_array,$is_callback, $filter_name, $callback);
}


function is_post($get_variables){	
	
    return isset($_POST[$get_variables]);
} 

function is_get($get_variables){
	
    return isset($_GET[$get_variables]);
}

function is_req($request_variables){
	
    return isset($_REQUEST[$request_variables]);
}

function is_cookie($cookie_name){
	return isset($_COOKIE[$cookie_name]);
}

function is_ua($name){		
	
	return ( strpos($_SERVER['HTTP_USER_AGENT'], $name) !== false );
}

function has_count($arr){
	
	 return ( (is_array($arr) && count($arr) >= 0) );
	
}

function self_uri(){		
		
		$url = 'http';
	  	$script_name = '';
	  	
	  	if (isset($_SERVER['REQUEST_URI'])):		  	  		
	    	$script_name = $_SERVER['REQUEST_URI'];
	    	
	  	else:	  	
	    	$script_name = $_SERVER['PHP_SELF'];
				    	
	    	if ($_SERVER['QUERY_STRING'] > ' '):	    	
	      		$script_name .= '?' . $_SERVER['QUERY_STRING'];
	      		
	    	endif;	    
	  	endif;
	  	
	  	
	  if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') $url .= 's';
	  	  
	  $url .= '://';
	  
	  if ($_SERVER['SERVER_PORT'] != '80'):	  	
	    $url .= $_SERVER['HTTP_HOST'] . ':' . $_SERVER['SERVER_PORT'] . $script_name;
	    
	  else:	  	
	    $url .= $_SERVER['HTTP_HOST'] . $script_name;
		
	  endif;
	  
	  return $url;	
}


function wpi_foreach_hook($hook_array,$is_callback = false,$priority = 10){
	if($is_callback && is_string($is_callback)){
		foreach($hook_array as $filter_name){
			add_action($filter_name,$is_callback,$priority);
		}
		
	} else {
		foreach($hook_array as $filter_name => $callback){
			add_action($filter_name,$callback,$priority);
		}		
	}	
	
}

function wpi_foreach_hook_filter($hook_array,$is_callback = false,$priority = 10){
	if($is_callback){
		foreach($hook_array as $filter_name){
			add_filter($filter_name,$is_callback,$priority);
		}
		
	} else {
		foreach($hook_array as $filter_name => $callback){
			add_filter($filter_name,$callback,$priority);
		}		
	}
	
	unset($hook_array,$is_callback);
	
}

function wpi_cat_image_filter($content){	
	return preg_replace("/<img[^>]+\>/i", "<small>(image)</small> ", $content);
}

function wpi_cat_content_filter($content){
	$content = apply_filters('the_excerpt',$content);
	$content = string_len(wpi_cat_image_filter($content),500);
	$content = force_balance_tags($content);
	return $content;
}

function wpi_search_terms_filter($content){
	$terms = get_search_query();	
	$pat = '#(\>(((?>([^><]+|(?R)))*)\<))#se';
	$rep = "preg_replace('#\b(" . $terms . ")\b#i', '<strong class=\"hilite-2\"><span>\\\\1</span></strong>', '\\0')";
	
	return str_replace('\"','"',substr(preg_replace($pat, $rep,'>'.$content.'<'),1,-1));
}

function wpi_search_content_filter($content){
	$content = wpi_cat_image_filter($content);
	$content = wpi_search_terms_filter($content);
	return $content;
}


function wpi_content_meta_title_filter(){ 
	global $wp_query;
	
	$title 	= get_the_title();
	$output = false;
	
	if ($wp_query->is_single || $wp_query->is_page){			
		$htm 	= _t('strong',$title);
		$htm 	.= wpi_get_subtitle();
		$output = _t('h1',$htm);
	}
	
	if ($wp_query->is_category){		
		$title = single_cat_title('',false);
		$htm 	= _t('strong',$title);	
			
		$output = _t('h1',$htm);
		if ( ($desc = $wp_query->queried_object->category_description) != '' ){
			$output .= _t('blockquote',_t('p',$desc.'&nbsp;'),array('cite'=>self_uri(),'class'=>'entry-summary r'));
		} else {
			$desc = WPI_BLOG_NAME.'&apos;s archive for '.$title.', there is '.$wp_query->queried_object->count.' articles for this &#39;main&#39; category.';
			$output .= _t('blockquote',_t('p',$desc.'&nbsp;'),array('cite'=>self_uri(),'class'=>'entry-summary r'));
		}	
	}
	
	if ($wp_query->is_tag){
		$title = single_tag_title('',false);
		
		$htm 	= _t('strong',$title);			
		$output = _t('h1',$htm,array('title'=>$title));
		
		$desc = WPI_BLOG_NAME.'&#39;s taxonomy archive for '.$title.', there is '.$wp_query->queried_object->count.' article(s) for this tag.';		
		$output .= _t('blockquote',_t('p',$desc.'&nbsp;'),array('cite'=>self_uri(),'class'=>'entry-summary r'));		
	}

	if ($wp_query->is_search){
		$title = get_search_query();		
		$htm 	= _t('small',__('Search results for: ', WPI_META));
		$htm 	.= _t('strong',$title);			
		$output = _t('h1',$htm,array('title'=>$title));			
	}
	
	if ($wp_query->is_year){
		$title = get_the_time('Y');			
		$htm 	= _t('strong',$title);		
		$output = _t('small',__('Archive for', WPI_META));	
		$output .= _t('h1',$htm,array('title'=>$title));				
	}	

	if ($wp_query->is_month){
		$title = get_the_time('F, Y');		
		$htm 	= _t('strong',$title);		
		$output = _t('small',__('Archive for', WPI_META));	
		$output .= _t('h1',$htm,array('title'=>$title));				
	}
	
	if ($wp_query->is_day){
		$title = get_the_time('F jS, Y');		
		$htm 	= _t('strong',$title);		
		$output = _t('small',__('Archive for', WPI_META));	
		$output .= _t('h1',$htm,array('title'=>$title));				
	}
	
	if ($wp_query->is_author){
		$user = wpi_get_current_author();
		$title = $user->display_name;
		
		$htm 	= _t('strong',$title);			
		$output = _t('h1',$htm,array('title'=>$title));
		
		$desc = $user->user_description;		
		$output .= _t('blockquote',_t('p',$desc.'&nbsp;'),array('cite'=>self_uri(),'class'=>'entry-summary r'));		
	}	
	
	if ($wp_query->is_404){
		
		$htm 	= _t('strong',__('404 Not Found',WPI_META));	
		$output = _t('h1',$htm,array('title'=>$title));
		
		$desc = 'Sorry, but you are looking for something that isn&#39;t here';
		$output .= _t('blockquote',_t('p',$desc.'&nbsp;'),array('cite'=>self_uri(),'class'=>'entry-summary r'));		
	}
	
	$output = wpi_google_ads_targeting_filter($output);
	
	if ($output) echo stab(1).$output;
	//wpi_dump($wp_query); exit;
	unset($output,$section,$title,$htm);
	
}

function wpi_google_ads_targeting_filter($content){
	return PHP_EOL.'<!-- google_ad_section_start -->'.PHP_EOL . $content . '<!-- google_ad_section_end -->'.PHP_EOL;
}


function wpi_default_filters(){

	$f = array();	
	
	$f['stylesheet_directory'] 		= 'wpi_get_stylesheet_directory_filter';
	$f['stylesheet_directory_uri'] 	= 'wpi_stylesheet_directory_uri_filter';
	$f['stylesheet_uri'] 			= 'wpi_get_stylesheet_uri_filter';	
	$f['the_password_form'] 		= 'wpi_password_form_filters';	
	$f['comments_template'] 		= 'wpi_comments_template_filter';
	$f['language_attributes'] 		= 'wpi_filter_language_attributes';
	
	// head	
	$f[wpiFilter::FILTER_META_DESCRIPTION] = 'wpi_meta_description_filter';
	$f[wpiFilter::FILTER_META_KEYWORDS] = 'wpi_meta_keywords_filter';
	$f[wpiFilter::FILTER_CUSTOM_HEAD_CONTENT] = 'wpi_custom_content_filter';
	$f[wpiFilter::FILTER_CUSTOM_FOOTER_CONTENT] = 'wpi_custom_content_filter';	
	
	if (is_wp_version(2.6)){
		$f['login_form'] = 'wpi_login_form_action';
	}
	
	if (is_wp_version(2.7,'>=')){
		$f['http_headers_useragent'] = 'wpi_append_http_ua_string_filter';
	}	
	
	// robots
	$f['do_robotstxt'] = 'wpi_robots_rules_filter';

	// feeds
	$f['rss_head'] = $f['rss2_head'] = 'wpi_rss_header';
	
	if ( '' != wpi_option('delay_rss')  
		&& absint(wpi_option('delay_rss')) >= 1 ){
		$f['posts_where'] = 'wpi_delay_feeds';
	}
	
	$f[wpiFilter::ACTION_FOOTER_SCRIPT] = 'wpi_google_analytics_tracking_script';
	
	$f['get_comment_text'] = 'wpi_comment_text_filter';
			
	wpi_foreach_hook_filter($f);	
}

function wpi_stylesheet_directory_uri_filter($stylesheet_dir_uri=false, $stylesheet=false){
	
	$uri = wpi_get_stylesheets_url('css');
	$uri = str_rem('/css/',$uri);	
	return $uri;	
}

function wpi_get_stylesheet_uri_filter($stylesheet_uri=false, $stylesheet_dir_uri=false){	
	global $Wpi;
	return wpi_get_stylesheets_url($Wpi->Style->css);
}

function wpi_get_stylesheet_directory_filter($stylesheet_dir=false, $stylesheet=false){	
	$dir= WPI_CSS_DIR;
	
	if (PATH_SEPARATOR == ';'){
		$dir = strtr(realpath($dir), array("\\", DIRSEP));
	} else {
		$dir = strtr(realpath($dir), array("/", DIRSEP));
	}
	
	return $dir;
}

/**
 * Microformats require proper 'summary'.
 * filter: the_password_form
 */
function wpi_password_form_filters($content){
	global $posts;
	// append if there is no excerpt	
	if (!has_excerpt($posts->ID)){
	
	$patt = '<p>'.__("This post is password protected. To view it please enter your password below:");
	
	$rep = _t('p',get_the_title(),array('class'=>'summary dn'));
	
	 $content = str_replace($patt,$rep.$patt,$content);
	}	
	
	return $content;
}

function wpi_comments_template_filter($file){
	
	$version_file = WPI_DIR.'comments-'.WP_VERSION_MAJ.'.php';
	if (file_exists($version_file)) $file = $version_file;
	return $file;
}

function wpi_login_form_action(){
}

function wpi_selector_protected($selector){
	return $selector.' protected';
}

function wpi_selector_grid($selector){
	return $selector.' grid';
}

function wpi_section_class_filter($callback,$type='inner'){	
	$hook = ($type == 'inner') ? wpiFilter::FILTER_SECTION_INNER_CLASS : wpiFilter::FILTER_SECTION_OUTER_CLASS;
	
	add_filter($hook,$callback);
}

function wpi_remove_section_class_filter($callback,$type='inner'){
		$hook = ($type == 'inner') ? wpiFilter::FILTER_SECTION_INNER_CLASS : wpiFilter::FILTER_SECTION_OUTER_CLASS;
	remove_filter($hook,$callback);
}

function wpi_meta_description_filter($content){
	/**
	 * + Meta-Description should not contain more than 30/48 words.
	 * - Length should not be greater than 130/150 characters.
	 * - Meta-Description should include all keywords.
	 */
	$content = string_len($content,150); // 
	$content = ent2ncr(htmlentities2($content));
	
	return $content;
}

function wpi_meta_keywords_filter($content){
	
	$content = wpi_safe_stripslash($content);
	$content = strtolower($content);
	
	return $content;
}

function wpi_custom_content_filter($content){
	return str_replace("\n",PHP_EOL.PHP_T,$content);
}

/**
 * Filter for language_attributes()
 * Strip localization string
 * {@link http://www.w3.org/TR/xhtml1/#C_7 HTML compatibility Guidline C.7} 
 * 
 * function wpi_filter_language_attributes()
 * hook: language_attributes
 * 
 * @params mixed|string HTML tag attributes
 * @see language_attributes() Display the language attributes for the html tag.
 */
function wpi_filter_language_attributes($content){
	
	$lang = get_bloginfo('language');
	list($i18n,$l10n) = explode('-',$lang);	
	
	return str_replace($lang,$i18n,$content);
}

/**
 * for WP 2.7
 * hook: http_headers_useragent
 */
function wpi_append_http_ua_string_filter($content){
	$content .= ' ('.wpiTheme::UID.' '.wpiTheme::VERSION.')';	
	return $content;	
}

/**
 * WPI robots.text
 * void wpi_robots_rules_filter()
 * 
 * WP hook: do_robotstxt
 * @see do_robots()
 * @since 1.6.2 
 * 
 */
function wpi_robots_rules_filter(){	
	
	$rules = wpi_get_robots_rules();	
	if (has_count($rules)){
		$rules = implode(PHP_EOL,$rules);
		
		if (!file_exists(ABSPATH.'robots.txt')){
			echo $rules;	
		}
	}
	
	exit;
}



/**
 * WPI Robots Exclusion Standards Rules
 * 
 * Symbols:
 * 	'*' - wildcard match a sequence of characters in URL
 *  '$' - anchors at the end of the URL string
 * 
 * @since 1.6.2 
 * @link http://www.robotstxt.org/orig.html 
 */
function wpi_get_robots_rules()
{	global $wp_rewrite;

	$rules = array();
	
	$user_agent = 'User-agent: ';
	$disallow = 'Disallow: ';
	$delay = 'Crawl-delay: ';
	$uri = rel(WPI_HOME_URL);
	$uri_wp = rel(WPI_URL);
	
	if (file_exists(ABSPATH.'sitemap.xml')){
		
		$rules[] = PHP_EOL.'# General sitemap';
		$rules[] = 'Sitemap: '.WPI_HOME_URL_SLASHIT.'sitemap.xml';	
	}
	$rules[] = '# Google bot';
	if (is_wp_version(2.7)){
		$rules[] = $user_agent.'Googlebot';
					
		if (get_option('page_comments')){
			if ($wp_rewrite && $wp_rewrite->using_permalinks() ){		
				$sep = explode("/",get_option('permalink_structure'));
				$rules[] = $disallow. rel(WPI_HOME_URL.str_repeat('/*',count($sep) - 2).'/comment-page-*');	
				$compage = $disallow. '/*comment-page-*$';
					
			} else {
				$rules[] = $disallow. '/*cpage=*$';
			}
		}				
	}
	
	$disallow_theme_image = $disallow.rel(THEME_IMG_URL);
	
	$rules[] = $user_agent.'Googlebot-Image';
	$rules[] = $disallow_theme_image;	
	$rules[] = $user_agent.'MediaPartners-Google';
	$rules[] = $disallow.'';
	
	$rules[] = PHP_EOL.'# Dug Mirror';
	$rules[] = $user_agent.'duggmirror';
	$rules[] = $disallow_theme_image;	

	$rules[] = PHP_EOL.'# dmoz.org';
	$rules[] = $user_agent.'Robozilla';
	$rules[] = $disallow.'';
	
	$rules[] = PHP_EOL.'# MSN';
	$rules[] = $user_agent.'msnbot';
	$rules[] = $delay.'120';
	$rules[] = $disallow.'';

	$rules[] = '# MSN Media search';
	$rules[] = $user_agent.'MSNBot-Media';
	$rules[] = $disallow_theme_image;
	
	/**
	 * Validator and data mining
	 * @link http://research.microsoft.com/research/sv/msrbot/
	 */
	$rules[] = '# MSN research bot';
	$rules[] = $user_agent.'msrbot';
	$rules[] = $disallow.'';		
	
	$rules[] = PHP_EOL.'# Yahoo! Slurp';
	$rules[] = $user_agent.'Slurp';
	$rules[] = $delay.'8';
	$rules[] = $disallow.'';
		
	$rules[] = '# all user agent';
	$rules[] = $user_agent.'*';		
	
	if (is_dir(ABSPATH.'cgi-bin')){
		$rules[] = $disallow. $uri.'cgi-bin';
	}
	
	$rules[] = $disallow. $uri_wp.'wp-admin';
	$rules[] = $disallow. $uri_wp.'wp-content';
	$rules[] = $disallow. $uri_wp.'wp-includes';
		
	if ($wp_rewrite && $wp_rewrite->using_permalinks() ){
		if ( ( $tag = get_option('tag_base') ) != false ){
			$rules[] = $disallow. $uri.$tag;
		} else {
			$rules[] = $disallow. $uri.'tags';
		}
		
		$rules[] = $disallow. '/*/feed';
		$rules[] = $disallow. '/*/trackback';	
		if (isset($compage)){
			$rules[] = $compage;
		}
	}	
	
	$rules[] = '# WP-iStalker Filetypes';
	$rules[] = $disallow. '/*.webfont$';
	$rules[] = $disallow. '/*.ava$';
	$rules[] = $disallow. '/*.e64$';
	$rules[] = $disallow. '/*.d64$';
	/**
	 * @todo Exclude plugins custom permalinks & params
	 * 		1. Alex king's popularity contest plugin
	 * 		2. Lester chan's WP-ratings plugin
	 * 		3. Nothing2hide Global Translator plugin
	 */
	// $rules[] = '# WP plugins';

	if ( '0' == get_option( 'blog_public' ) ) {		
		$rules = array();	// reset all
		
		$rules[] = $user_agent. '*';
		$rules[] = $disallow. '/';		
	}
	
	$rules[] = PHP_EOL.'# Generated by '.wpiTheme::THEME_NAME. ' ('.wpiTheme::VERSION.') Robots Exclusion Rules '.wpiTheme::THEME_URL;
		
	return $rules;
} 

function wpi_noindex_rss_header(){
	echo '<xhtml:meta xmlns:xhtml="http://www.w3.org/1999/xhtml" name="robots" content="noindex" />'.PHP_EOL;
}

function wpi_ypipe_noindex_rss_meta(){
	echo PHP_T.'<meta xmlns="http://pipes.yahoo.com" name="pipes" content="noprocess" />'.PHP_EOL;
}

function wpi_rss_logo(){
	$htm = PHP_T.'<image>'.PHP_EOL;
	$htm .= PHP_T.PHP_T.'<link>'.WPI_HOME_URL_SLASHIT.'</link>'.PHP_EOL;
	$htm .= PHP_T.PHP_T.'<url>'.wpi_option('rss_logo').'</url>'.PHP_EOL;
	$htm .= PHP_T.PHP_T.'<title>'.get_bloginfo_rss('name').'</title>'.PHP_EOL;
	$htm .= PHP_T.'</image>'.PHP_EOL;
	
	echo $htm;
	unset($htm);
}

function wpi_rss_header(){
	
	if (wpi_option('exclude_feed')){
		wpi_noindex_rss_header();
	}
	
	if (wpi_option('exclude_ypipe')){
		wpi_ypipe_noindex_rss_meta();
	}
	
	if ( '' != wpi_option('rss_logo') ){
		wpi_rss_logo();
	}
}

/**
 * wpi_delay_feeds()
 * @link http://wpengineer.com/publish-the-feed-later/
 * @since 1.6.2
 */
function wpi_delay_feeds($clause) {
	global $wpdb;

	if ( is_feed() ) {
		
		$now = gmdate('Y-m-d H:i:s');
		$wait = absint(wpi_option('rss_delay')) >= 1  ? absint(wpi_option('rss_delay')) : 5 ; 
		$device = 'MINUTE';
		$clause .= " AND TIMESTAMPDIFF($device, $wpdb->posts.post_date_gmt, '$now') > $wait ";
	}
	
	return $clause;
}

/**
 * void wpi_google_analytics_tracking_script()
 * Google Analytics Tracking script
 * @since 1.6.2
 */
function wpi_google_analytics_tracking_script()
{
	if ( ($id = wpi_option('google_analytics_tracking_code')) != '') :
?>
	<script type="text/javascript" charset="utf-8">/*<![CDATA[*/ var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www."); document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E")); /*]]>*/ </script>
	<script type="text/javascript" charset="utf-8">/*<![CDATA[*/ try {var pageTracker = _gat._getTracker("<?php echo $id;?>"); pageTracker._trackPageview(); } catch(err) {}; /*]]>*/ </script>
<?php endif;
}

function wpi_comment_text_filter($content)
{
	$content = wptexturize($content);
	$content = convert_chars($content);
	$content = make_clickable($content);
	$content = force_balance_tags($content);
	$content = convert_smilies($content);
	$content = wpautop($content);
	return $content;	
}

/**
 * WPI intergalactic null function
 * @since 1.6.2
 */
function wpi_null(){ return null;}

	
/**
 * void wpi_sidebar_dir_filter()
 * 
 * load after init
 * filter: wpiFilter::ACTION_INTERNAL_CSS
 * @uses is_at() WP_query related
 * @since 1.6.2
 */	
function wpi_sidebar_dir_filter(){
	
	$sc = is_at();
	$direction = false;
	$css = PHP_EOL.PHP_T;
	$NL = PHP_EOL.PHP_T;
		
	if (wpiSection::HOME == $sc){
		$direction = wpi_option('home_sidebar_position');
		
		if ($direction == 'left'){		
			$css .= '.home #main,.home #main-bottom{float:right!important}'.$NL;
			$css .= '.home .hentry .postmeta-date{float:right!important;background-position:-82px 0px;margin: 0pt -30px 0pt 0pt !important}'.$NL;
			$css .= '.home .postmeta-date .date-month{padding:0pt}'.$NL; 
			$css .= '.home .hentry{padding-left:0px}'.$NL;
			$css .= '.home #sidebar{margin-left:10px}';
		}		
	}
	
	if ($direction) echo $css;
}

/**
 * void wpi_post_thumbnail_filter()
 * 
 * load after init
 * filter: wpiFilter::ACTION_INTERNAL_CSS
 * @since 1.6.2
 */	
function wpi_post_thumbnail_filter(){
	global $wp_query;
	
	$tpl = '#wpi-thumb-%1d{background-image:url(%2s)}'.PHP_EOL.PHP_T;
	
	echo PHP_EOL.PHP_EOL.PHP_T.'/** WPI post thumb */'.PHP_EOL.PHP_T;
	$margin = (wpi_option('home_sidebar_position') == 'left') ? ';margin:0pt' : ';margin: 0pt 0pt 0pt -30px';
	echo PHP_EOL.PHP_T.'.wpi-post-thumb{width:540px;height:100px;border:5px solid #f2f2f2'.$margin.'}'.PHP_EOL.PHP_T;
	
	if ($wp_query->posts){
		
		foreach ($wp_query->posts as $p){
			$pid = $p->ID;
			if ( ($url = wpi_get_postmeta_key($pid,'post_thumb_url')) != ''){
				printf($tpl,$pid,$url);
			}
		}
		
		unset($p);
	}
}
?>