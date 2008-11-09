<?php
if ( !defined('KAIZEKU') ) { die( 42); }

function wpi_get_stylesheets_url($css){
	global $wp_rewrite;

	$params = '?'.wpiTheme::PUB_QUERY_VAR_CSS.'=';
		
	if ($wp_rewrite && $wp_rewrite->using_permalinks() ){
		$params = wpiTheme::PUB_QUERY_VAR_CSS.'/';	
		$output = trailingslashit(WPI_URL_SLASHIT.$params.$css);
	} else {
		$output = WPI_URL_SLASHIT.$params.$css;
	}
	
	if (get_query_var('preview') == 1){
		$output = wpi_theme_content_url($css);
	}	
					
	return $output;	
}

function wpi_theme_content_url($params,$ext = '.css'){
	
	$params = explode(',',$params);
	$prop = array();
	foreach($params as $file){
		$prop[] = $file.$ext;
	}
		
	$params = join(',',$prop);
		
	return untrailingslashit(WPI_THEME_URL.$params);
		
}

function wpi_logout_self_return_uri(){
	return WPI_URL_SLASHIT.'wp-login.php?action=logout&amp;redirect_to='. urlencode(rel(self_uri()));
}


function wpi_logout_url($redirect = '') {

		$redirect = '&amp;redirect_to='.urlencode(rel(self_uri()));
	
	return wp_nonce_url( site_url("wp-login.php?action=logout$redirect", 'login'), 'log-out' );
}

function wpi_get_scripts_url($js){
	global $wp_rewrite;

	$params = '?'.wpiTheme::PUB_QUERY_VAR_JS.'=';
		
	if ($wp_rewrite && $wp_rewrite->using_permalinks() ){
		$params = wpiTheme::PUB_QUERY_VAR_JS.'/';
		$output = trailingslashit(WPI_URL_SLASHIT.$params.$js);	
	} else {
		$output = WPI_URL_SLASHIT.$params.$js;	
	}	

	if (get_query_var('preview') == 1){
		$output = wpi_theme_content_url($js,'.js');
	}		
	
	$output = apply_filters(wpiFilter::FILTER_LINKS,$output);
	return $output;
}
/**
 * get favicon url
 * @param void
 * @return   
 */

function wpi_get_favicon_url(){		
	return ( (file_exists(WP_ROOT.'favicon.ico') ? WPI_URL.'/favicon.ico' : WPI_THEME_URL.'favicon.ico') );	
}	

function wpi_comment_reply_uri($post,$comment){
	global $wp_rewrite;
	
	$thickbox = 'height=418&amp;width=710';
	
	$query = '?'.wpiTheme::PUB_QUERY_VAR.'=%params%&amp;'.$thickbox;
	
	if ($wp_rewrite && $wp_rewrite->using_permalinks() ){
		$query = wpiTheme::PUB_QUERY_VAR.'/%params%/?'.$thickbox;
	}	
	
	$params = array();
	$params[] = 'pid-'.$post->ID;
	$params[] = 'cid-'.$comment->comment_ID;
	$params[] = 'pcid-'.$comment->comment_parent;
	
	$uri = str_replace("%params%",'reply,'.join(",",$params),$query);
	echo rel(WPI_URL_SLASHIT.$uri);	
}

function wpi_cat_links($echo= 1, $index = false, $separator = '&#184;'){
	global $wp_query;
	
	$section = is_at();	
	$pid = false;
	
	if ($section == wpiSection::ATTACHMENT){
		$pid = $wp_query->post->post_parent;
	} 
	
	$cats 	 = get_the_category($pid);
	
	$options 	= array('class'	=> 'cat-link ttip dc-subject foaf-isPrimaryTopicOf',
					    'href'	=> '#content',
					    'rel'	=> 'category foaf.topic',
					    'title' => 'category',
					    'rev'	=> 'site:archive');	
	$links 	= '';
	
		if (is_bool($index)):
		
			$ismore	= false;			
			$cnt 	= count($cats);
			
			if ($cnt >= 0){
				$ismore = true;
				$endcnt = ($cnt - 1);
			}

			$i = 1;
			

			foreach($cats as $cat){	
				
				$options['href']  = apply_filters('wpi_links_'.$section,get_category_link($cat->cat_ID));

				$options['title'] = $cat->cat_name.' | '.$cat->count.' articles in this category';
				$links .= _t('a',$cat->name, $options);

						 if ( $ismore && $i == $endcnt){
						 	$links .= ' '._t('span','&amp;',array('class'=>'sep')).' ';

						 } elseif ( $i !== $cnt){
						 	$links .= _t('span',$separator,array('class'=>'sep')).' ';

						 }
				
				$i++;				
			}

		elseif(is_integer($index)):

			$cats = $cats[$index];
			$options['href']  = apply_filters('wpi_links_'.$section,get_category_link($cats->cat_ID));				

			$options['title'] = $cats->cat_name.' ('.$cats->count.' articles in this category)';
			$links .= _t('a', $cats->name, $options);

		endif;

		unset($cats);

		if ($echo == 1): echo $links; else:	return $links; endif;
}


function wpi_get_pages_link(){
	
	$options = 'sort_column=menu_order&title_li=&echo=';
	
	$exclude = wpi_get_theme_option('menu_page_exclude');
	
	if ($exclude && !empty($exclude)){
		$options .='&exclude='.$exclude;
	}
	
	$output  = wp_list_pages($options);
	$output = preg_replace('%<a ([^>]+)>%U','<a $1><span>', $output);
	$output = str_replace('</a>','</span></a>', $output);
	return $output;	
}


function wpi_img_url($filename){
	return apply_filters(wpiFilter::FILTER_LINKS,THEME_IMG_URL.$filename);	
}

/**
 * WP log-in, log-out, register & admin dashboard links
 *  
 */

function wpi_acl_links()
{
		$m 			= array();
		$acl_links	= array();

		$m['register'] = array(); // sort first.
		$m['loginout'] = array(
							'/wp-login.php',
							'log-in',
							'Log-in to '.WPI_BLOG_NAME,
							'Log-in');

		if (get_option('users_can_register'))
		{
			$m['register'] = array(
								'/wp-login.php?action=register',
								'registration-open',
								'Register an Account',
								'Register');
		} else {
			$m['register']= array(
							'/#'.wpiTemplate::bodyID(),
							'registration-closed',
							'Registration is Closed',
							'Registration is Closed');
		}

		if (is_user_logged_in())
		{
			$m['register'] = array(
								'/wp-admin/',
								'dashboard',
								WPI_BLOG_NAME.'&apos;s WP Admin Dashboard',
								'Dashboard');
			$req_uri = get_req_url();		
			$uri = (is_wp_version('2.6')) ? wpi_logout_url() : '/wp-login.php?action=logout&amp;redirect_to='.$req_uri;
						
			$m['loginout'] = array($uri,'log-out','Log-out from '.WPI_BLOG_NAME,'Log-out');
		}


		foreach ( $m as $k => $v ){
			// attributes
			$attribs = array();

			$attribs['id']		= $v[1];
			if ($k != 'loginout'){
				$attribs['href']	= rel(WPI_URL.$v[0]);
			} else {
				$attribs['href']	= $v[0];
			}
			
				if ($k == 'log-in'){
					$attribs['href'] = apply_filters($k,$attribs['href']);
				}
		

				$attribs['title']	= 'Info | '. $v[2];

				if ($k != 'register'){
					$attribs['rel']	= 'noindex noarchive';
				}

			$attribs['rev']		= 'site:relative';
			$attribs['class']   = 'ttip';
			$acl_links[]		= _t('a',$v[3],$attribs);
		}

		$output = "\n";
		if (is_array($acl_links)){
			$cnt = 1;
			foreach($acl_links as $link){
				$output .= stab(4)._t('li',$link,array('id'=>'acl-'.$cnt));
				$cnt++;
			}
		}
		
		t('ul',$output.stab(2).PHP_T,array('id'=>'cl-options','class'=>'xoxo r cfl cf'));
}


function get_req_url(){
	return clean_url($_SERVER['REQUEST_URI']);	
}


function wpi_paged_url($pageno = 1){
	return apply_filters('links',clean_url(get_pagenum_link($pageno)));
}


function wpi_get_max_pages(){
	global $wp_query;	
	return $wp_query->max_num_pages;	
}


function wpi_get_current_paged(){
	
	if ( is_paged() ) {
		return intval(get_query_var('paged'));
	} else {
		return false;
	}
}

function wpi_get_prev_post_link($in_same_cat = false, $excluded_categories = ''){
	return wpi_get_adjacent_post_link($in_same_cat, $excluded_categories, true);
}

function wpi_get_next_post_link($in_same_cat = false, $excluded_categories = ''){
	return wpi_get_adjacent_post_link($in_same_cat, $excluded_categories, false);
}

function wpi_get_adjacent_post_link($in_same_cat = false, $excluded_categories = '', $previous = true) {
	if ( $previous && is_attachment() )
		$post = & get_post($GLOBALS['post']->post_parent);
	else
		$post = get_adjacent_post($in_same_cat, $excluded_categories, $previous);

	if ( !$post )
		return;

	$title = $post->post_title;

	if ( empty($post->post_title) )
		$title = $previous ? __('Previous Post') : __('Next Post');

	$title = apply_filters('the_title', $title, $post);
	$link  = get_permalink($post);
	
	return array($link,$title);
}

?>