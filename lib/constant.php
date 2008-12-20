<?php
define('KAIZEKU',1);
/**
 * $Id$
 * WP-iStalker Constant
 */
// wordpress root dir
if ( !defined('WP_ROOT') ) {
    define( 'WP_ROOT', (strtr(realpath(ABSPATH), array("\\", DIRSEP))) );
}

// template root dir
define('WPI_DIR',TEMPLATEPATH.DIRSEP);

define('WPI_LIB',WPI_DIR.'lib'.DIRSEP);

//class
define('WPI_LIB_CLASS',WPI_LIB.'class'.DIRSEP);

// modules dir
define('WPI_LIB_MOD',WPI_LIB.'modules'.DIRSEP);

// import class dir
define('WPI_LIB_IMPORT',WPI_LIB.'import'.DIRSEP);

/**
 * Import WP shortcode dir
 * @since 1.6.2
 */
define('WPI_LIB_IMPORT_SHORTCODE',WPI_LIB_IMPORT.'shortcode'.DIRSEP);

// public dir
define('WPI_PUB',WPI_DIR.'public'.DIRSEP);

//public cache dir
define('WPI_CACHE_DIR',WPI_PUB.'cache');

// public images dir
define('WPI_IMG_DIR',WPI_DIR.'images'.DIRSEP);

// public images import dir
define('WPI_IMG_IMPORT_DIR',WPI_IMG_DIR.'import'.DIRSEP);

// public stylesheet dir
define('WPI_CSS_DIR',WPI_PUB.'css'.DIRSEP);

// public stylesheet cached dir
define('WPI_CACHE_CSS_DIR',WPI_CACHE_DIR.DIRSEP.'css'.DIRSEP);

// public scripts dir
define('WPI_JS_DIR',WPI_PUB.'scripts'.DIRSEP);

// public javascript cached dir
define('WPI_CACHE_JS_DIR',WPI_CACHE_DIR.DIRSEP.'scripts'.DIRSEP);

/**
 * Fonts dir
 * @since 1.2
 */
define('WPI_FONTS_DIR',(strtr(realpath(WPI_PUB.'webfonts'), array("\\", DIRSEP))).DIRSEP);

/**
 * GD fonts Image cache dir
 * @since 1.2
 */
define('WPI_CACHE_FONTS_DIR',(strtr(realpath(WPI_CACHE_DIR.DIRSEP.'webfonts'), array("\\", DIRSEP))));

define('WPI_CACHE_AVATAR_DIR',WPI_CACHE_DIR.DIRSEP.'avatar');

/**
 * Wordpress blog URL
 * @since	1.6 site_url()
 */
define('WPI_URL', get_option('siteurl') );

define('WPI_URL_SLASHIT', trailingslashit( WPI_URL ) );

/**
 * Blog home URL
 * @since 1.6.2
 */
define('WPI_HOME_URL', get_option('home') );

/**
 * Blog home URL with trailing slash (not user trailing slash)
 * @since 1.6.2
 */
define('WPI_HOME_URL_SLASHIT', trailingslashit(get_option('home')) );

/**
 * WordPress Admin URL
 * @since 1.6.2
 */
define('WPI_ADMIN_URL', site_url('wp-admin/','admin') );

/**
 * Wordpress Blog Name
 * @since	1.6
 */
define( 'WPI_BLOG_NAME', get_bloginfo('name')  );

define( 'WPISTALKER', 'wp_istalker' );

define( 'WPI_META', 'wp-istalker-chrome' );

define( 'WPI_THEME_URL', trailingslashit( get_bloginfo('template_url') ) );

define('THEME_IMG_URL', WPI_THEME_URL . 'images/' );
/**
 * Wp-istalker database prefix  
 * @since	1.6
 */
define( 'WPI_META_PREFIX', 'mods_' . WPI_META . '_' );

define( 'WPI_KEY', md5(SECRET_KEY) );	

/**
 * Client Support XML
 * @since 1.6.2
 */

define('WPI_CLIENT_ACCEPT_XML',(stristr($_SERVER['HTTP_ACCEPT'],'application/xml') ) );

/**
 * Client Support XHTML parser (q= 0.x)
 * @since 1.6.2
 */
define('WPI_CLIENT_ACCEPT_XHTML_XML',(stristr($_SERVER['HTTP_ACCEPT'],'application/xhtml+xml') ) );

define('PHP_T',"\t");

if (!defined('WP_VERSION'))  define('WP_VERSION', get_bloginfo('version'));

if (!defined('WP_VERSION_MAJ')) define('WP_VERSION_MAJ', (float) WP_VERSION);

//define('FIREBUG_CONSOLE',1);

// non expensive current timestamp -time();
define('SV_CURRENT_TIMESTAMP',$_SERVER['REQUEST_TIME']);

/**
 * Client User agent string
 * @since 1.6.2
 */
define('SV_UA_STRING',$_SERVER['HTTP_USER_AGENT']); 
?>