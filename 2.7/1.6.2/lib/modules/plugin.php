<?php

/**
 * Plugins function
 */

function wpi_plugin_init(){
	$rm_plug = $call_plug = array();
	$rm_plug[] = 'wp-pagenavi/wp-pagenavi.php,pagenavi_css,wp_head';	
	$rm_plug[] = 'wp-downloadmanager/wp-downloadmanager.php,downloads_css,wp_head';
	
	$rm_plug = apply_filters(wpiFilter::REM_PLUGINS,$rm_plug);
	
	if (has_count($rm_plug)){
		foreach($rm_plug as $index){
			list($plugin,$callback,$hook) = explode(',',$index);
			wpi_remove_plugin_action($plugin,$callback,$hook);
		}
	}
	
	$call_plug[] = 'wp-pagenavi/wp-pagenavi.php,wp_pagenavi,wpi_post_pagination';
	$call_plug[] = 'global-translator/translator.php,wpi_get_gt_translate_links,widget_single_summary_after';
		$call_plug[] = 'global-translator/translator.php,wpi_global_translator_metalinks,wpi_meta_link';
	//wpi_global_translator_metalinks
	if (has_count($call_plug)){
		foreach($call_plug as $index){
			list($plugin,$callback,$hook) = explode(',',$index);
			wpi_plugin_active_elif(array('plugin'=>$plugin,'callback'=>$callback,'hook'=>$hook) );
		}
	}
	
	unset($rm_plug,$call_plug);
}

function wpi_curl_remote_file($url)
{
	$ch = curl_init($url);
				
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
	curl_setopt($ch, CURLOPT_USERAGENT, wpiTheme::UID);
				
	$file = curl_exec($ch);
				
	curl_close($ch);
				
	if ($file !== false) {
		return $file;
	}	
}

function wpi_is_plugin_active($plugin_filename)
{ 
	$plugins = get_option('active_plugins');;

		if( !is_array($plugins) ){
			 $plugins = array($plugins);
		}

	return ( in_array($plugin_filename, $plugins) ) ;
}

function wpi_remove_plugin_action($plugin,$callback,$hook = 'wp_head')
{ 
	$active_plugins = get_option('active_plugins');	
	
		if (!is_array($active_plugins)){
			$active_plugins = array($active_plugins);
		}		
	
		if (in_array($plugin, $active_plugins)){
			if (is_callable($callback)){
				remove_action($hook,$callback);
			}
		} 	
}
 
function wpi_foreach_remove($registered_plugins = false)
{
	if (!$registered_plugins){
		$registered_plugins = wpi_get_plugin_remove();	
	}
	
	if (!is_array($registered_plugins)){
		return false;
	}
	
	for($i = 0, $m = count($registered_plugins); $i <$m; $i++){
		$p = $registered_plugins[$i];
		
		if (!isset($p[2])){
			$p[2] = 'wp_head';
		}
		
		wpi_remove_plugin_action($p[0],$p[1],$p[2]);
	}	
	
}

function wpi_plugin_active_elif($option)
{
	if (is_array($option) && count($option) >= 3){
		$priority = 10;
	  
		 if (wpi_is_plugin_active($option['plugin'])){ 	 	 
		 		if ($option['hook'] == 'wpi_meta_link'){
		 			$priority = wpiTheme::LAST_PRIORITY;
		 		}
		 		
				add_action($option['hook'],$option['callback'],$priority);
						
		 } else {
				add_action($option['hook'],$option['fallback']);			
		 }	
	 }
}


function wpi_get_gt_translate_links()
{	global  $gltr_engine, $wp_query;
	
	if (!isset($gltr_engine)||$gltr_engine == null) {
		return 'Global Translator not configured yet.';
	}
	
	$transl_map   = $gltr_engine->get_languages_matrix();
	$translations = $transl_map[BASE_LANG];
	$transl_count = count($translations);	
	  
	$output = "\n".FLAG_BAR_BEGIN;
	  
	$is_original_page	= !isset($wp_query->query_vars['lang']);
	$is_browser 		= gltr_is_browser();
	$is_search_engine	= !$is_browser;
  
  	$is_indexable_page= ( is_single() || is_page() || is_home() );  
						
  if ( ( $is_original_page && $is_indexable_page && $is_search_engine) 
  	  || $is_browser || !BAN_PREVENTION )
  {
  	$output .= _t('small',__('Translate',WPI_META),array('class'=>'fl','title'=>'Translate this articles'));
  	
  	$options 	= array();
  	
  	// cached the options instead of recalling it for array search
  	$prefs	 	= (array) get_option('gltr_preferred_languages');

	$_pref		= array_flip($prefs);
	
	// in_array is expensive
    foreach ($translations as $key => $value) {     	
      if ($key == BASE_LANG || isset($_pref[$key]) ) {
        $options[$key] = $value;
      }  
    }
    
    unset($_pref);
    
    $self_url	= gltr_get_self_url();
    $post_title = wp_title('',false);
    $title		= __(' | Translation ',WPI_META);
    
    foreach ($options as $key => $value) {
    	
      $url 		= gltr_get_translated_url($key, $self_url);
      $url		= apply_filters(wpiFilter::FILTER_LINKS,$url);
	  $name		= $translations[$key];	
      $output 	.= _t('a', 'Translate &apos;'.$post_title.'&apos; in '.$name,
	  			   array(
					 	'id'	=> 'flag_'.$key,
						'href'	=> $url,
						'hreflang'	=> $key,
						'title'	=> $name.$title,
						'rel'	=> 'alternate',
						'class' => 'ttip',
						'rev'	=> 'relative:'.$key) );
    } 
   
  }  
  
  $output = _t('div',$output,array('id'=>'translate','class'=>'cf') );
  $output .= FLAG_BAR_END . "\n";
  
  echo apply_filters('wpi_gt_translate_links',$output);  	
}

function wpi_text_size()
{?>
			
					<ul id="acc" class="r fr cfr cf"><li class="first-"><?php t('a',__('Increase text size',WPI_META),array('id'=>'font','class'=>'rtxt ttip','rel'=>'noarchive','type'=>'application/x-javascript','href'=>'#iscontent','title'=>'Increase | text size'));?></li><li class="last-"><?php t('a',__('Decrease text size',WPI_META),array('id'=>'font-','class'=>'rtxt ttip','rel'=>'noarchive','type'=>'application/x-javascript','href'=>'#iscontent','title'=>'Decrease | text size'));?></li></ul><?php }

function wpi_get_bookmarks()
{	global $post;

	if (!is_object($post)){
		return false;
	}
	
	$share = array();
	
	$max_title = 75;
	$max_text  = 350;
	
	$share['title'] 	= string_len($post->post_title, 75);
	$share['url']		= get_permalink($post->ID);
	
	$text 	   = (( $post->post_excerpt) ? $post->post_excerpt : $post->post_content);
	
	$share['bodytext']	=  string_len($text,350);
	$share['media']		= 'news';
	
	unset($text);
	
	$share = array_map('urlencode',$share);
	
	$bookmarks = array();


	
	// http://del.icio.us/post?url=uri&title=title
	$bookmarks[] = array(
					'name'	=> 'del.icio.us',
					'uri'	=> 'http://del.icio.us/post',
					'params'=>	array('url','title'));
	
	// http://digg.com/submit?phase=2&url=url
	/**
     * @link 	http://digg.com/tools/integrate 	
     * url		max 255 	
     * title	max 75
     * bodytext max 350
	 * media	news | image |   
	 */  	
		// title max 75
	$bookmarks[] = array(
					'name'	=> 'Digg',
					'uri'	=> 'http://digg.com/submit',
					'params'=>	array('url','title','bodytext','media'));

	/**
     * @links	http://www.sphere.com
     *  
	 */   	
	 // http://www.sphere.com/search?q=sphereit:url
	$bookmarks[] = array(
					'name'	=> 'Sphere it',
					'uri'	=> 'http://www.sphere.com/search',
					'params'=>	array('q'));
											
	/**
     * @links	http://www.stumbleupon.com/buttons.php
     *  
	 */   	
	 // http://www.stumbleupon.com/submit?url=uri&title=title
	$bookmarks[] = array(
					'name'	=> 'StumbleUpon',
					'uri'	=> 'http://www.stumbleupon.com/submit',
					'params'=>	array('url','title'));
	
	$bookmarks	 = apply_filters(wpiFilter::FILTER_BOOKMARKS,$bookmarks);
	
	$output = PHP_EOL;
	
	
	foreach($bookmarks as $key => $meta)
	{
	
		$att 	= $params = array();
		$uid  	= sanitize_title_with_dashes($meta['name']);	
		
		$att['title']  = __('Social bookmark | Add to ',WPI_META).$meta['name'];
		
		$att['rel']	   = 'bookmark noarchive';
		$att['rev']	   = 'vote-for';
		$att['class']  = 'ttip';
		
		foreach($meta['params'] as $param)
		{
			if (isset($share[$param]) && $meta['name'] != 'Sphere it'){
				$params[] = $param.'='.$share[$param];
				
			} elseif ($meta['name'] == 'Sphere it'){
				$params[] = $param.'=sphereit:'.$share['url'];
			}
		}
		
		$params = '?'.implode("&amp;",$params);
		
		$att['href']   = $meta['uri'].$params;
		
		$output .= _t('li', _t('a',$meta['name'],$att),array('class'=>$uid) );
	}
	
	
	$output = _t('ul',$output,array('class'=>'xoxo dn cfl r cf','id'=>'wpi-bookmarks'));
	$htm = _t('acronym',__('Share this',WPI_META),array('class'=>'show-slow share rn fl'));
	return apply_filters('wpi_get_bookmarks',$htm.$output);	

}

function wpi_bookmarks()
{
	echo wpi_get_bookmarks();
}

function get_wpi_plugins()
{
	$plugin_files = Wpi::listDirectoryFile(WPI_LIB,"/\.plugin\.php/");
	
	$plugins = array();
	foreach($plugin_files as $file){
		$name 		= wpi_strip_ftype('.plugin.php',$file);
		$callback	= str_replace('-','_',$name);
		
		$plugins[] = array('name'=>$name,'id'=>$callback);
	}
	
	unset($plugin_files);
	
	return $plugins;
}

function wpi_register_plugins()
{
	$plugins = get_wpi_plugins();
	
	if (is_array($plugins) && has_count($plugins) )
	{
		foreach($plugins as $plugin)
		{			
			if ( Wpi::loadPlugin( $plugin['name']) )
			{				
				$callback = 'wpi_get_'.$plugin['id'].'_pluginname';
				
				if (wpi_is_user_func_exists($callback)){
					
					$plugin_filename = (string) call_user_func($callback);
					
					if (wpi_is_plugin_active($plugin_filename))
					{
						$init_callback = 'wpi_'.$plugin['id'].'_init';
							
						if (wpi_is_user_func_exists($init_callback)){
							call_user_func($init_callback);
						}	
					}
				}
				
			}
		}
		
	} else {
		return false;
	}
}

/**
 * Header alternate link tag
 * @hook	wp_head
 */
function wpi_global_translator_metalinks()
{ global  $gltr_engine;

	
	$transl_map   = $gltr_engine->get_languages_matrix();
	$translations = $transl_map[BASE_LANG];	
	
  	$options 	= array();
  	
  	$prefs	 	= (array) get_option('gltr_preferred_languages');

	$_pref		= array_flip($prefs);
	
    foreach ($_pref as $k=>$v) {     	
        $options[$k] = $translations[$k];
    }  
    
	unset($_prefs,$prefs,$translations);	
	$output = '';
	
	foreach($options as $iso => $language)
	{
		$attribs = array();
		
		$attribs['rel'] 	= 'alternate';
		$attribs['type'] 	= get_bloginfo('html_type');
		$attribs['charset'] = strtolower(get_bloginfo('charset'));		
		$attribs['href'] 	= rel(WPI_URL_SLASHIT.$iso.'/');		
		$attribs['hreflang'] = $iso;
		$attribs['lang'] = $iso;
		$attribs['xml:lang'] = $iso;		
		$attribs['title'] = WPI_BLOG_NAME.'&apos;s in '.$language;		
		$output .= PHP_T._t('link','',$attribs);
	}
	
	unset($options);	
	echo $output;		
}

?>