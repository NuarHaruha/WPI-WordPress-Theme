<?php
if (!defined('KAIZEKU')) { die(42); }
require WPI_LIB_CLASS.'section-enum.php';
/**
 * $Id$
 * Wp-Istalker default configuration
 * @since 1.2
 */

final class wpiTheme
{
	const UID = 'wp-istalker-chrome';
	
	const VERSION = '1.6.2 RC 2';
	
	const AUTHOR = 'ChaosKaizer';
	
	const THEME_NAME = 'Wp-iStalker';
	
	const THEME_RELEASE_TYPE = 'Public Edition';
	
	const SIDEBAR_COUNT = 16;
	
	const LIB_TYPE_CONFIG = 'lib';
	
	const LIB_TYPE_CLASS = 'class';
	
	const LIB_TYPE_MODULES = 'modules';
	
	const LIB_TYPE_PLUGINS = 'plugins';
	
	const LIB_TYPE_WIDGETS = 'widgets';
	
	const LIB_TYPE_IMPORT = 'import';
	
	const REGISTER_ACTIONS = 'wpi_register_actions';
	
	const REGISTER_SIDEBAR = 'register_sidebar';
		
	const CONFIG_FILE = 'config.php';
	
	const DEFAULT_FILE_EXTENSION = '.php';
	
	const BLOG_TITLE_SEPARATOR = '&#187;';
	
	const PARAMS_SEP = ',';
	
	const WP_CONSTANT_REGEX = '/WP/';
	
	const WP_CONSTANT_DB_REGEX = '/DB/';
	
	const INCLUDE_TOKEN = 'KAIZEKU';
	
	const DOC_URL = 'http://wp-istalker.googlecode.com/';
	
	const THEME_URL = 'http://wp.istalker.net/';
	
	const LIB_MODULES = 'utils,filters,links,formatting,template,author';
	
	const CLASS_MODULES = 'filters-enum,template,,error,plugins';
	
	const IMPORT_MODULES = 'enum,body_class';
	
	const DATA_JSON_ENCODE = 'json_encode';
	
	const DATA_B64_ENCODE = 'b64_safe_encode';
	
	const DATA_JSON_DECODE = 'json_decode'; 
	
	const DATA_B64_DECODE = 'b64_safe_decode';
	
	const ENCODE_CONFIG_ENGINE = 'b64_safe_encode';
	
	const DECODE_CONFIG_ENGINE = 'b64_safe_decode';
	
	const SELF_MSG = 'If you\'re reading this, Congrats! you have won the internets';
	
	const PUB_QUERY_VAR = 'wpi-public';
	
	const PUB_QUERY_CONTENT_VAR = 'request';
	
	const PUB_QUERY_AUTHOR = 'wpi-stalker';
	
	const PUB_QUERY_VAR_CSS = 'wpi-styles';
	
	const PUB_QUERY_VAR_JS = 'wpi-scripts';
		
	const CTYPE_XML = 'Content-Type: application/xml';
	
	const CTYPE_CSS = 'Content-Type: text/css';
	
	const CTYPE_JS = 'Content-Type: text/javascript';
	
	const CL_COOKIE_TIME = 'wpi-cl';
	
	const LAST_PRIORITY = 11;
	
	const GD_FONT_TYPE = "(\.(TTF|ttf|OTF|otf|FON|fon))";
	
	private function __construct(){}		
}

?>