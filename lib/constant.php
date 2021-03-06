<?php
define('KAIZEKU',1);

// wordpress root dir
if ( !defined('WP_ROOT') ) {
    define( 'WP_ROOT', (strtr(realpath(ABSPATH), array("\\", DIRSEP))) );
}

// non expensive current timestamp -time();
define('SV_CURRENT_TIMESTAMP',$_SERVER['REQUEST_TIME']);

// template root dir
define('WPI_DIR',TEMPLATEPATH.DIRSEP);

define('WPI_LIB',WPI_DIR.'lib'.DIRSEP);

//class
define('WPI_LIB_CLASS',WPI_LIB.'class'.DIRSEP);

// modules dir
define('WPI_LIB_MOD',WPI_LIB.'modules'.DIRSEP);

// import class dir
define('WPI_LIB_IMPORT',WPI_LIB.'import'.DIRSEP);


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
define('WPI_CACHE_JS_DIR',WPI_CACHE_DIR.DIRSEP.'js'.DIRSEP);


/**
 * Wordpress blog URL
 * @since	1.6
 */
define( 'WPI_URL', get_bloginfo('url') );

// minus 1 redirection dude!
define('WPI_URL_SLASHIT', trailingslashit( WPI_URL ) );

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
?>