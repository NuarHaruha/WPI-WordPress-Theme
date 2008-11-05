<?php
if ( !defined('KAIZEKU') ) {   die( 42);}

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


function wpi_foreach_hook($hook_array,$is_callback = false,$priority = 10)
{
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

function wpi_foreach_hook_filter($hook_array,$is_callback = false,$priority = 10)
{
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
	return string_len(wpi_cat_image_filter($content),500);
}

function wpi_search_terms_filter($content)
{
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


function wpi_content_meta_title_filter()
{ global $wp_query;
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
			$desc = WPI_BLOG_NAME.'&apos;s archive for '.$title.', there is '.$wp_query->queried_object->count.' articles for this &apos;main&apos; category.';
			$output .= _t('blockquote',_t('p',$desc.'&nbsp;'),array('cite'=>self_uri(),'class'=>'entry-summary r'));
		}	
	}
	
	if ($wp_query->is_tag){
		$title = single_tag_title('',false);
		
		$htm 	= _t('strong',$title);			
		$output = _t('h1',$htm,array('title'=>$title));
		
		$desc = WPI_BLOG_NAME.'&apos;s taxonomy archive for '.$title.', there is '.$wp_query->queried_object->count.' article(s) for this tag.';		
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
		
		$desc = 'Sorry, but you are looking for something that isn&apos;t here';
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
	
	$f['stylesheet_directory'] = 'wpi_get_stylesheet_directory_filter';
	$f['stylesheet_directory_uri'] = 'wpi_stylesheet_directory_uri_filter';
	$f['stylesheet_uri'] = 'wpi_get_stylesheet_uri_filter';
	
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


function wpi_filter_debug(){
	$arr = array();
	// filter: stylesheet
	$arr[] = get_stylesheet();
	// filter: stylesheet_directory
	$arr[] = get_stylesheet_directory();
	// filter: stylesheet_uri ($stylesheet_uri,$stylesheet_dir_uri)
	$arr[] = get_stylesheet_uri();
	// filter: stylesheet_directory_uri
	$arr[] = get_stylesheet_directory_uri();
	wpi_dump($arr);exit;
	
}

?>