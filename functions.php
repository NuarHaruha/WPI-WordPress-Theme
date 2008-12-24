<?php
/**
 * 
 * Wordpress iStalker Chrome Milk
 * 
 * 
 * @author		Avice (ChaosKaizer) De'vereux <ck@animepaper.net>
 * @link		<http://blog.kaizeku.com/> 
 */
 
// Directory Separator Constant
if (!defined('DIRSEP')) define('DIRSEP',DIRECTORY_SEPARATOR);

	// common theme constant
	$wpi_constant = TEMPLATEPATH.DIRSEP.'lib'.DIRSEP.'constant.php';
	
	if (file_exists($wpi_constant)) {
		require $wpi_constant;
		require WPI_LIB.'theme.php';
		
		load_theme_textdomain(WPI_META);		
		$Wpi = new Wpi();
		unset($wpi_constant);
	}	
?>