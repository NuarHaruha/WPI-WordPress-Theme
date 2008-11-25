<?php

	define('DIRSEP',DIRECTORY_SEPARATOR);	
	define('RT',dirname(__FILE__).DIRSEP);	
	define('WPI_LIB',RT.'lib'.DIRSEP);	
	define('WPI_LIB_CLASS',WPI_LIB.'class'.DIRSEP);
	define('WPI_LIB_IMPORT',WPI_LIB.'import'.DIRSEP);
	define('WPI_LIB_MODULES',WPI_LIB.'modules'.DIRSEP);	
	define('WPI_IMAGE',RT.'images'.DIRSEP);	
	define('WPI_CACHE',RT.'public'.DIRSEP.'cache');	
	define('WPI_FONTS_DIR',RT. 'public'.DIRSEP.'webfonts'.DIRSEP);		
	define('WPI_CACHE_FONTS_DIR',WPI_CACHE.DIRSEP.'webfonts'); 
	define('WPI_CACHE_AVATAR_DIR',WPI_CACHE.DIRSEP.'avatar'.DIRSEP); 
		
	$cache 	  = false;	
	$cachedir 	= RT. 'cache';	
	$cssdir   	= RT. 'public'.DIRSEP.'css';	
	$jsdir    	= RT. 'public'.DIRSEP.'scripts';	
	$lib		= RT. 'lib'.DIRSEP;	
	
	$type = $_GET['type'];	
	
	if ($type != 'css' && $type != 'javascript'):	
		require WPI_LIB.'class'.DIRSEP.'router.php';		
		$fc = new wpiRouter($_GET);				
	else:
		require WPI_LIB.'import'.DIRSEP.'combine.php';		
	endif;
?>	