<?php
if (!defined('KAIZEKU')) exit(42);
/**
 * WP-iStalker Chrome Milk 
 * Theme constant
 * 
 * @package	WordPress
 * @subpackage	wp-istalker-chrome
 * 
 * @category	Administration
 * @author	Avice (ChaosKaizer) De'vereux <ck+wp-istalker-chrome@istalker.net>
 * @copyright 	2006 - 2009 Avice De'vereux
 * @license 	http://www.opensource.org/licenses/mit-license.php MIT License
 * @version 	CVS: $Id$
 * @since 	1.2
 */


/**
 * wpiTheme
 * WP-iStalker Chrome configurations
 * 
 * @author	Avice (ChaosKaizer) De'vereux <ck+wp-istalker-chrome@istalker.net>
 * @author	NH. Noah <noah+wp-istalker-chrome@kakkoi.net>
 * @copyright	2007 - 2009 Avice De'vereux, NH. Noah
 * @license	http://www.opensource.org/licenses/mit-license.php MIT License
 * @since	1.5
 * @access	public
 */
final class wpiTheme
{	
	
	/**
	 * wpiTheme::UID 
	 * Default-Style HTTP Header
	 * 
	 * If document render in "Standards compliance mode" 
	 * these will determine the default stylesheet. 
	 * 
	 * @var string 
	 * @see wpiTemplate::httpHeader()
	 * @access public
	 * @since 1.2
	 */
	const UID = 'wp-istalker-chrome';
	
	
	/**
	 * wpiTheme::VERSION
	 * 
	 * @var string
	 * @access public
	 * @since 1.2
	 */		
	const VERSION = '1.6.2 RC 3';
	
	
	/**
	 * wpiTheme::AUTHOR
	 * 
	 * Project's main developer
	 * 
	 * @var string 
	 * @access public
	 * @since 1.2
	 */	
	const AUTHOR = 'ChaosKaizer';
	
	
	/**
	 * wpiTheme::THEME_NAME
	 * 
	 * @var string
	 * @access public
	 * @since 1.2
	 */		
	const THEME_NAME = 'Wp-iStalker';
	
	
	/**
	 * wpiTheme::THEME_RELEASE_TYPE
	 * 
	 * @var string
	 * @access public
	 * @since 1.2
	 */		
	const THEME_RELEASE_TYPE = 'Public Edition';
	
	
	/**
	 * wpiTheme::SIDEBAR_COUNT
	 * 
	 * Numbers of registered dynamic sidebars
	 * 
	 * @var int 
	 * @see wpi_get_active_widget_sidebar_id() 
	 * @since 1.6
	 * @access public
	 */	
	const SIDEBAR_COUNT = 17;
	
	
	/**
	 * wpiTheme::LIB_TYPE_CONFIG
	 * 
	 * @var string
	 * @see Wpi::getFile()
	 * @access public
	 * @since 1.6.2
	 */		
	const LIB_TYPE_CONFIG = 'lib';
	

	/**
	 * wpiTheme::LIB_TYPE_CLASS
	 * 
	 * @var string
	 * @see Wpi::getFile()
	 * @access public
	 * @since 1.6.2
	 */		
	const LIB_TYPE_CLASS = 'class';	


	/**
	 * wpiTheme::LIB_TYPE_MODULES
	 * 
	 * @var string
	 * @see Wpi::getFile()
	 * @access public
	 * @since 1.6.2
	 */		
	const LIB_TYPE_MODULES = 'modules';
	

	/**
	 * wpiTheme::LIB_TYPE_PLUGINS
	 * 
	 * @var string
	 * @see Wpi::getFile()
	 * @access public
	 * @since 1.6.2
	 */			
	const LIB_TYPE_PLUGINS = 'plugins';
	

	/**
	 * wpiTheme::LIB_TYPE_WIDGETS
	 * 
	 * @var string
	 * @see Wpi::getFile()
	 * @access public
	 * @since 1.6.2
	 */		
	const LIB_TYPE_WIDGETS = 'widgets';
	

	/**
	 * wpiTheme::LIB_TYPE_IMPORT
	 * 
	 * @var string
	 * @see Wpi::getFile()
	 * @access public
	 * @since 1.6.2
	 */		
	const LIB_TYPE_IMPORT = 'import';
	

	/**
	 * wpiTheme::LIB_TYPE_SHORTCODE
	 * 
	 * @var string
	 * @see Wpi::getFile()
	 * @access public
	 * @since 1.6.2
	 */			
	const LIB_TYPE_SHORTCODE = 'shortcode';
	

	/**
	 * wpiTheme::REGISTER_ACTIONS
	 * filter init callback name
	 * 
	 * @var string
	 * @access public
	 * @since 1.3
	 */	
	const REGISTER_ACTIONS = 'wpi_register_actions';
	

	/**
	 * wpiTheme::REGISTER_SIDEBAR
	 * register sidebar callback
	 * 
	 * @deprecated
	 * @var string
	 * @access public
	 * @since 1.2
	 */	
	const REGISTER_SIDEBAR = 'register_sidebar';
	
		
	/**
	 * wpiTheme::CONFIG_FILE
	 * WPI brushmilk (1.5 & 1.6)
	 * 
	 * @deprecated
	 * @var string
	 * @access public
	 * @since 1.5
	 */		
	const CONFIG_FILE = 'config.php';
	
	
	/**
	 * wpiTheme::DEFAULT_FILE_EXTENSION
	 * template files extension
	 * 
	 * @var string
	 * @access public
	 * @since 1.5
	 */	
	const DEFAULT_FILE_EXTENSION = '.php';
	
	
	/**
	 * wpiTheme::BLOG_TITLE_SEPARATOR
	 * raquo html entities
	 * 
	 * @var string
	 * @see wpiTemplate::headTitle()
	 * @access public
	 * @since 1.4
	 */		
	const BLOG_TITLE_SEPARATOR = '&#187;';
	
	/**
	 * wpiTheme::PARAMS_SEP
	 * Theme custom permalinks parameter separator
	 * used by combine script 
	 * 
	 * @var string
	 * @see wpiTemplate::processVar()
	 * @access public
	 * @since 1.4
	 */	
	const PARAMS_SEP = ',';
	
	
	/**
	 * wpiTheme::WP_CONSTANT_REGEX
	 * WPI brushmilk (1.5 & 1.6)
	 * 
	 * @deprecated
	 * @var string
	 * @access public
	 * @since 1.5
	 */		
	const WP_CONSTANT_REGEX = '/WP/';
	
	
	/**
	 * wpiTheme::WP_CONSTANT_DB_REGEX
	 * WPI brushmilk (1.5 & 1.6)
	 * 
	 * @deprecated
	 * @var string
	 * @access public
	 * @since 1.5
	 */		
	const WP_CONSTANT_DB_REGEX = '/DB/';
	

	/**
	 * wpiTheme::INCLUDE_TOKEN
	 * nonce key
	 * 
	 * @var string
	 * @access public
	 * @since 1.2
	 */		
	const INCLUDE_TOKEN = 'KAIZEKU';
	
	
	/**
	 * wpiTheme::DOC_URL
	 * WP-Istalker Projects home
	 * 
	 * @var string
	 * @access public
	 * @since 1.5
	 */		
	const DOC_URL = 'http://wp-istalker.googlecode.com/';
	
	
	/**
	 * wpiTheme::THEME_URL
	 * WP-Istalker development and demo blogs
	 * 
	 * @var string
	 * @access public
	 * @since 1.5
	 */		
	const THEME_URL = 'http://wp.istalker.net/';
	

	/**
	 * wpiTheme::LIB_MODULES
	 * 
	 * @deprecated
	 * @var string
	 * @access public
	 * @since 1.5
	 */		
	const LIB_MODULES = 'utils,filters,links,formatting,template,author';
	
	
	/**
	 * wpiTheme::CLASS_MODULES
	 * 
	 * @deprecated
	 * @var string
	 * @access public
	 * @since 1.5
	 */		
	const CLASS_MODULES = 'filters-enum,template,,error,plugins';
	
	
	/**
	 * wpiTheme::IMPORT_MODULES
	 * 
	 * @deprecated
	 * @var string
	 * @access public
	 * @since 1.5
	 */		
	const IMPORT_MODULES = 'enum,body_class';
	
	
	/**
	 * wpiTheme::DATA_JSON_ENCODE
	 * 
	 * @var string
	 * @access public
	 * @since 1.5
	 */	
	const DATA_JSON_ENCODE = 'json_encode';
	
	
	/**
	 * wpiTheme::DATA_B64_ENCODE
	 * 
	 * @var string
	 * @access public
	 * @since 1.5
	 */		
	const DATA_B64_ENCODE = 'b64_safe_encode';
	

	/**
	 * wpiTheme::DATA_JSON_DECODE
	 * 
	 * @var string
	 * @access public
	 * @since 1.5
	 */		
	const DATA_JSON_DECODE = 'json_decode';
	

	/**
	 * wpiTheme::DATA_B64_DECODE
	 * 
	 * @var string
	 * @access public
	 * @since 1.5
	 */		
	const DATA_B64_DECODE = 'b64_safe_decode';
	

	/**
	 * wpiTheme::ENCODE_CONFIG_ENGINE
	 * 
	 * @var string
	 * @access public
	 * @since 1.5
	 */		
	const ENCODE_CONFIG_ENGINE = 'b64_safe_encode';
	

	/**
	 * wpiTheme::DECODE_CONFIG_ENGINE
	 * 
	 * @var string
	 * @access public
	 * @since 1.5
	 */	
	const DECODE_CONFIG_ENGINE = 'b64_safe_decode';
	
	
	/**
	 * wpiTheme::SELF_MSG
	 * X-hacker HTTP Header 
	 *  
	 * @see wpiTemplate::httpHeader()
	 * @access public
	 * @since 1.6.2
	 */	
	const SELF_MSG = 'If you\'re reading this, Congrats! you have won the internets';
	

	/**
	 * wpiTheme::PUB_QUERY_VAR
	 * 
	 * @var string
	 * @see wpiTemplate::registerPublicVar()
	 * @access public
	 * @since 1.6
	 */		
	const PUB_QUERY_VAR = 'wpi-public';
	

	/**
	 * wpiTheme::PUB_QUERY_CONTENT_VAR
	 * 
	 * @var string
	 * @see wpiTemplate::registerPublicVar()
	 * @access public
	 * @since 1.6
	 */		
	const PUB_QUERY_CONTENT_VAR = 'request';


	/**
	 * wpiTheme::PUB_QUERY_AUTHOR
	 * 
	 * @deprecated
	 * @var string
	 * @see wpiTemplate::registerPublicVar()
	 * @access public
	 * @since 1.6
	 */			
	const PUB_QUERY_AUTHOR = 'wpi-stalker';
	

	/**
	 * wpiTheme::PUB_QUERY_VAR_CSS
	 * 
	 * @var string
	 * @see wpiTemplate::registerPublicVar()
	 * @access public
	 * @since 1.6
	 */	
	const PUB_QUERY_VAR_CSS = 'wpi-styles';


	/**
	 * wpiTheme::PUB_QUERY_VAR_JS
	 * 
	 * @var string
	 * @see wpiTemplate::registerPublicVar()
	 * @access public
	 * @since 1.6
	 */		
	const PUB_QUERY_VAR_JS = 'wpi-scripts';


	/**
	 * wpiTheme::PUB_WIDGET_PARAMS
	 * 
	 * @var string
	 * @access public
	 * @since 1.6
	 */	
	const PUB_WIDGET_PARAMS = '/widget,%s/';


	/**
	 * wpiTheme::CTYPE_XML
	 * Content-Type HTTP headers for XML file
	 * 
	 * @var string
	 * @access public
	 * @since 1.2
	 */			
	const CTYPE_XML = 'Content-Type: application/xml';
	
	
	/**
	 * wpiTheme::CTYPE_CSS
	 * Content-Type HTTP headers for CSS file
	 * 
	 * @var string
	 * @access public
	 * @since 1.2
	 */		
	const CTYPE_CSS = 'Content-Type: text/css';
	

	/**
	 * wpiTheme::CTYPE_JS
	 * Content-Type HTTP headers for javascripts file
	 * 
	 * @var string
	 * @access public
	 * @since 1.2
	 */	
	const CTYPE_JS = 'Content-Type: text/javascript';


	/**
	 * wpiTheme::CL_COOKIE_TIME
	 * WPI client cookie for "Client time styles switcher"  
	 * 
	 * @var string 
	 * @access public
	 * @since 1.6.2
	 */		
	const CL_COOKIE_TIME = 'wpi-cl';
	
	
	/**
	 * wpiTheme::CL_COOKIE_WIDTH
	 * WPI Client Cookie for "Client width styles switcher"  
	 *  
	 * @var string
	 * @access public
	 * @since 1.6.2
	 */		
	const CL_COOKIE_WIDTH = 'wpi-clw';
	

	/**
	 * wpiTheme::LAST_PRIORITY	 
	 * 
	 * @see add_filter()
	 * @var int 
	 * @access public
	 * @since 1.6.2
	 */		
	const LAST_PRIORITY = 11;


	/**
	 * wpiTheme::GD_FONT_TYPE	 
	 * 
	 * @var string 
	 * @access public
	 * @since 1.6.2
	 */		
	const GD_FONT_TYPE = "(\.(TTF|ttf|OTF|otf|FON|fon))";
	
	
	/**
	 * wpiTheme::BANNER_IMAGE_TYPE	 
	 * 
	 * @var string 
	 * @access public
	 * @since 1.6.2
	 */		
	const BANNER_IMAGE_TYPE = "(\.(jpg|JPG|jpeg|JPEG|jpe|JPE|png|PNG|gif|GIF))";
	

	/**
	 * wpiTheme::FRONTPAGE_TEMPLATE_TYPE	 
	 * 
	 * @var string 
	 * @access public
	 * @since 1.6.2
	 */		
	const FRONTPAGE_TEMPLATE_TYPE = "/^frontpage\-/";
	
	
	/**
	 * wpiTheme::__construct()
	 * @access private 
	 */
	private function __construct(){}
	
	
	/**
	 * wpiTheme::__clone()
	 * prevent clone
	 * @access private
	 */	
	private function __clone(){}		
}




/**
 * wpiSection
 * 
 * @author	Avice (ChaosKaizer) De'vereux <ck+wp-istalker-chrome@istalker.net>
 * @author	NH. Noah <noah+wp-istalker-chrome@kakkoi.net>
 * @copyright	2007 - 2009 Avice De'vereux, NH. Noah 
 * @license	http://www.opensource.org/licenses/mit-license.php MIT License
 * @since	1.6
 * @access	public
 */
final class wpiSection
{
	
	
	const FRONTPAGE = 'front_page';
	
	const HOME = 'home';
	
	const SEARCH = 'search';
	
	const PAGE404 = '404';
	
	const SINGLE = 'single';
	
	const PAGE = 'page';
	
	const AUTHOR = 'author';
	
	const ATTACHMENT = 'attachment';
	
	const CATEGORY = 'category';
	
	const TAXONOMY = 'tag';
	
	const ARCHIVE = 'archive';
	
	const YEAR = 'year';
	
	const MONTH = 'month';
	
	const DAY = 'day';

	const K_ALL = 10;
	
	const K_HOME = 11;			// home and frontpage
	
	const K_SINGULAR = 12;			// single, page and attachment
	
	const K_SINGLE = 13;
	
	const K_PAGE = 14;
	
	const K_ATTACHMENT = 15;
	
	const K_ARCHIVE = 16;			// all archive; categories, tags, author, year, month, days, hours, minutes
	
	const K_CATEGORIES = 17;
	
	const K_TAGS = 18;
	
	const K_DATE = 19;
	
	const K_DATE_YEAR = 20;
	
	const K_DATE_MONTH = 21;
	
	const K_DATE_DAYS = 22;
	
	const K_AUTHOR = 23;
	
	const K_SEARCH = 24;
	
	const K_404 = 25;

	/**
	 * wpiTheme::__construct() 
	 * @access private
	 */	
	private function __construct(){}
	
	
	/**
	 * wpiTheme::__clone()
	 * prevent clone
	 * @access private
	 */		
	private function __clone(){}		
}



/**
 * wpiFilter
 * 
 * @author	Avice (ChaosKaizer) De'vereux <ck+wp-istalker-chrome@istalker.net>
 * @author	NH. Noah <noah+wp-istalker-chrome@kakkoi.net>
 * @copyright	2008 - 2009 Avice De'vereux, NH. Noah 
 * @license	http://www.opensource.org/licenses/mit-license.php MIT License
 * @since	1.6.2
 * @access	public
 */
final class wpiFilter
{
	
	
	const ACTION_FLUSH = 40000;
	
	const ACTION_SEND_HEADER = 'wpi_send_http_header';
	
	const ACTION_DEBUG = 'wpi_dump';
	
	const ACTION_DOCUMENT_DTD = 'wpi_document_head';
	
	const ACTION_META_HTTP_EQUIV = 'wpi_meta_http_equiv';
	
	const ACTION_META = 'wpi_meta';
	
	const ACTION_META_LINK = 'wpi_meta_link';
	
	const ACTION_SECTION_PREFIX = 'wpi_template_section_';
	
	const ACTION_SECTION_HEADER = 'wpi_section_head';	
	
	const ACTION_TPL_HEADER = 'wpi_section_header';
	
	const ACTION_TPL_HEADER_AFTER = 'wpi_section_header_after';
	
	const ACTION_THEME_OPTIONS = 'wpi_theme_options';
	
	const ACTION_COPYRIGHT_STATEMENTS = 'wpi_copyright';	
	
	const ACTION_EMBED_CSS = 45000;
	
	const ACTION_INTERNAL_CSS = 45001;
	
	const ACTION_GRAVATAR_CSS = 45002;
	
	const ACTION_POST_PAGINATION = 'wpi_post_pagination';
	
	const ACTION_FOOTER_SCRIPT = 'wpi_footer_script';
	
	const ACTION_WIDGET_SUMMARY_AFTER = 'wpi_widget_single_summary_after';
	
	const FILTER_SECTION_HEADER = 'wpi_fl_header';
	
	const FILTER_BLOG_TITLE = 'wpi_blog_title';
	
	const FILTER_PUBLIC_DTD = 'wpi_document_dtd';
	
	const FILTER_CONTENT_LANGUAGE = 'wpi_document_language';
	
	const FILTER_SECTION_OUTER_CLASS = 'wpi_template_outer_class';
	
	const FILTER_SECTION_INNER_CLASS = 'wpi_template_inner_class';
	
	const FILTER_LINKS = 40100;	
	
	const FILTER_WEBFONT_LINKS = 40101;
	
	const FILTER_AUTHOR_NAME = 'wpi_html_display_name';
	
	const FILTER_PUBLIC_CSS = 45003;
	
	const NONCE_THEME_OPTIONS = 'wpi-theme-options';
	
	const FILTER_POST_DATE	 = 'wpi_post_date';
	
	const FILTER_COM_DATE	 = 'wpi_comment_date';
	
	const FILTER_POST_PAGINATION_CLASS = 'wpi_pagination_class';
	
	const FILTER_COMMENTS_SELECTOR = 'wpi_comments_class';
	
	const HTOM_TITLE = 'wpi_hatom_title';
	
	const FILTER_BOOKMARKS = 'wpi_bookmarks';
	
	const REM_PLUGINS = 'wpi_remove_plugin_callback';
	
	const RANDOM_COMMENT_AVA = 'wpi_random_comments_avatar';
	
	const EXTRA_JS = 'wpi_extra_js';
	
	const FILTER_ELM_ID = 'wpi_element_id_';
	
	const ACTION_BANNER_CONTENT = 'wpi_banner_content';
	
	const FILTER_HEAD_PROFILE = 'wpi_head_profile';
	
	const FILTER_ENTRY_CONTENT_CLASS = 'wpi_entry_content_class';
	
	const ACTION_BEFORE_CONTENT_PREFIX = 'wpi_before_content_';
	
	const ACTION_AFTER_CONTENT_PREFIX = 'wpi_after_content_';
	
	const ACTION_INSIDE_CONTENT_BOTTOM_BAR_PREFIX = 'wpi_content_bar_';	
		
	const FILTER_META_DESCRIPTION = 42000;
	
	const FILTER_META_KEYWORDS = 42001;
	
	const FILTER_CUSTOM_HEAD_CONTENT = 42002;
	
	const FILTER_CUSTOM_FOOTER_CONTENT = 42003;
	
	const FILTER_ROOT_CLASS_SELECTOR = 42004;
	
	const ACTION_LIST_COMMENT_FORM = 45004; 
	
	const FILTER_JS_DOM_READY = 'wpi_js_dom_ready';
	
	/**
	 * wpiTheme::__construct() 
	 * @access private
	 */	
	private function __construct(){}
	
	
	/**
	 * wpiTheme::__clone()
	 * prevent clone
	 * @access private
	 */		
	private function __clone(){}
}
?>