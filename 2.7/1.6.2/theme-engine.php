<?php
/**
 * WP-iStalker Chrome Milk 
 * Handle WPI content  
 *  
 * PHP 5
 * 
 * @package	WordPress
 * @subpackage	wp-istalker-chrome
 * 
 * @category	HTTPRedirect
 * @author	Avice (ChaosKaizer) De'vereux <ck+wp-istalker-chrome@istalker.net>
 * @copyright 	2007 - 2009 Avice De'vereux
 * @license 	http://www.opensource.org/licenses/mit-license.php MIT License
 * @version 	CVS: $Id$
 * @since 	1.2
 */
 
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
	
	$compress = false;
		
	$cache 	  = false;
		
	$cachedir 	= WPI_CACHE;
		
	$cssdir   	= RT. 'public'.DIRSEP.'css';	
	
	$jsdir    	= RT. 'public'.DIRSEP.'scripts';
		
	$lib		= WPI_LIB;	
	
	$type = $_GET['type'];	
	
	if ($type != 'css' && $type != 'javascript'):	
		require WPI_LIB_CLASS.'router.php';		
		$fc = new wpiRouter($_GET);
				
	else:
		if (isset($_SERVER['HTTP_USER_AGENT']) 
			&& '' != trim($_SERVER['HTTP_USER_AGENT'])
			&& $type = 'css'){	
	
				if (isset($_GET['files'])){
					$request = explode(',',$_GET['files']);
					
					if (is_array($request)){	
						$request = array_flip($request);
						
						if (isset($request['user-agent.css'])){
							
							unset($request['user-agent.css']);
							
							// Browscap
							require WPI_LIB_IMPORT.'browscap.php';
						
							$Client = new Browscap(WPI_CACHE);
							
							// disabled the expensive autoupdate (we relies on cached data for now)
							$Client->doAutoUpdate = false;
							
							// fetch and reused object
							$Client = $Client->getBrowser($_SERVER['HTTP_USER_AGENT']);
								
							$browser = $client_css = array();
						
							
							$browser[] = (string) $Client->Browser;
							$browser[] = (string) $Client->Parent;
							$browser[] = (string) $Client->Platform;
							
							unset($Client);
								
							foreach($browser as $tag){
								$tag = strtolower(strip_tags($tag));
								$tag = preg_replace('/&.+?;/', '', $tag); 
								$tag = preg_replace('/[^%a-z0-9 _-]/', '', $tag);
								$tag = preg_replace('/\s+/', '-', $tag);
								$tag = preg_replace('|-+|', '-', $tag);
								$tag = trim($tag, '-');		
									
								$tag = $tag.'.css';
									
								$tag = str_replace('0.css','.css',$tag);
									
								if (file_exists($cssdir.DIRSEP.$tag)){
									
									// remove duplicated request
									unset($request[$tag]);
									
									$client_css[] = $tag;		
								}
							}						
								
							$request = array_flip($request);
								
							$request = array_merge($client_css, $request);
								
							$_GET['files'] = implode(',', $request);	
						}			
					}					
				}
				
				unset($request, $client_css, $browser);			
		}
		
		require WPI_LIB_IMPORT.'combine.php';
				
	endif;
?>	