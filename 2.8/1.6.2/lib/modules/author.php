<?php
if ( !defined('KAIZEKU') ) { die( 42); }

/**
 *  authors template functions
 */  
 
/**
 *  author display_name
 *  
 */ 
function wpi_get_author_name($type = 'hcard'){ 
global $authordata;	
	
	$name = $display_name = apply_filters('the_author',$authordata->display_name);
	$name = $display_name = ent2ncr(htmlentities2($name));
	
	$author_url = $authordata->user_url;
	$author_url = ( $author_url != 'http://') ? $author_url : WPI_HOME_URL;	
	
	switch ($type):
	
	case 'link':
	/** simple links
	 *	
	 */ 	
		$attribs = array( 
			'href' => $author_url,
			'class' => 'url fn dc-creator',
			'rel' => 'me foaf.homepage foaf.maker',
			'title' => 'Visit '.$display_name.'&apos;s Website',
			'rev' => 'author:'.$authordata->user_nicename );
		
		$output = _t('a',$display_name,$attribs);		
		
	break;
	
	case 'hcard':
	/** convert to microformats
	 *	address:a:span
	 */  
	
		// split the name
		$name = explode('name',$name);	
			 
		if (is_array($name))
		{
			if ( Wpi::hasCount($name) )	{
				if (isset($name[0])){
					$output = _t('span',$name[0],array('class'=>'nickname'));
				}
				if (isset($name[1])){
					$output = _t('span',$name[0],array('class'=>'given-name')).' ';
					$output .= _t('span',$name[1],array('class'=>'family-name'));
				}
				
			}
			
		} else {
			$output = _t('span',$author_name,array('class'=>'nickname'));
		}	
		
		// author post url; 
	
		$url = get_author_posts_url($authordata->ID,$authordata->user_nicename);
		$url = apply_filters(wpiFilter::FILTER_LINKS,$url);
		
		$attribs = array( 
			'href' => $url,
			'class' => 'url fn dc-creator',
			'rel' => 'colleague foaf.homepage foaf.maker',
			'title' => 'Visit '.$display_name.'&apos;s Author page',
			'rev' => 'author:'.$authordata->user_nicename );
		
		$output = _t('a',$output,$attribs);
		
		// microID sha-1 hash 	
		
		$hash = get_microid_hash($authordata->user_email,$author_url);
		
		// wrap hcard	
		$output = _t('cite',$output,array('class' => 'vcard microid-'.$hash));
		
	break;
	
	default:
		$output = $name;
	break;
	
	
	endswitch;
	
	return apply_filters(wpiFilter::FILTER_AUTHOR_NAME.$type,$output);
	
} 

function wpi_post_author_name()
{
	$author_url = wpi_get_author_name();
	
	$literal_label = __('Post by',WPI_META);
	
	$attribs = array('class'=>'literal-label');
	
	$post_by = _t('span',$literal_label,array('class'=>'literal-label'));
	
	if (wpi_get_theme_option('post_by_enable')){
		
		$output = $post_by.' '.$author_url;
	} else {
		$output = $post_by.' '.$author_url;
		$output = _t('dfn',$output,array('class'=>'dn'));
	}
	
	echo $output;
}

function wpi_get_current_author(){
	global $author, $author_name;

	if(is_get('author_name')) 
		$user = get_userdatabylogin($author_name);
	else 
		$user = get_userdata(intval($author));

	return $user;
}

function wpi_get_blog_since_year($year = false){
	
	$cur = date('Y',SV_CURRENT_TIMESTAMP);
	if (!$year){
		$since = wpi_blog_since_year();
	} else {
		$since = absint($year);
	}
	
	$date = $since.' - '.$cur;
	
	if ($cur == $since)	$date = $cur; 
	
	return _t('span',$date,array('class'=>'since-year') );
}
		
?>