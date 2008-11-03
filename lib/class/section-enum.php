<?php
if (!defined('KAIZEKU')) { die(42); }
/**
 * Wp-Istalker config
 * 
 *   
 * @author	Avice Devereux, http://kaizeku.com 
 * @since	17.march.2008
 * @license	MIT License  
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
	
	private function __construct(){}
	private function __clone(){}		
}

?>