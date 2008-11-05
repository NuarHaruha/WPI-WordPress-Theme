<?php
if ( !defined('KAIZEKU') ) { die( 42); }

function wpi_get_comment_author(){ 
		
	if (get_comment_type() != 'comment'){
		$author = get_host(get_comment_author_url());
		$author = str_rem("www.",$author);
	} else {
		$author = get_comment_author();
	}
	
	return $author;
}

function wpi_comment_author(){ echo wpi_get_comment_author(); }

function wpi_post_author_selector_filter($selector=false){
	global $authordata, $comment;
	
	$output = get_comment_type();
	
	if ($output == 'comment'){
	$post_author_email 	= strtolower($authordata->user_email);
	$commenter_email 	= strtolower(get_comment_author_email());
	
	$output = ($post_author_email == $commenter_email 
	&& $authordata->ID == $comment->user_id) ? 'post-author' : 'commenter';
	//$output = ' '.$authordata->ID.' == '.$comment->user_id ;
	}
	
	return $selector.' '.$output;
}

function wpi_get_comment_text_filter($content){
	global $comment;
	$type = get_comment_type();
	
	if ($type != 'comment'){
		$htm = call_user_func_array('wpi_'.$type.'_footer',array($comment) );
		if (!empty($htm)){
			$content .= _t('span',$htm,array('class'=>'db cb cf comment-footer'));
		}
	}
	
	return $content;
}

function wpi_comment_avatar_src(){
	
	$src = 'avatar-wrap.png';	
	if (stristr(wpi_post_author_selector_filter(),'post-author') ){
		$src = 'avatar-author-wrap.png';
	}
	
	echo wpi_img_url($src);
}

function wpi_comment_copy_cc(){
	$date = (int) get_comment_date('Y');
	$cur  = date('Y',$_SERVER['REQUEST_TIME']);
	
	if ( $cur > $date){	
		$date = $date . ' - '. $cur;
	}
	
	$cc = _t('small','(cc)');
	$cc .= _t('span',' '.$date.' '.get_comment_author());
	t('p',$cc,array('class'=>'ta-r')) ;	
}


function wpi_pingback_agentname($comment){	
	$output =  str_replace('Incutio XML-RPC --','',$comment->comment_agent);
	$output =  str_replace('/',' ',$output);
	return $output;
}


function wpi_pingback_footer($comment)
{
	$count = wpi_count_pingback_by($comment);
	$ua    = wpi_pingback_agentname($comment);
	$host  = parse_url($comment->comment_author_url,PHP_URL_HOST);
	
	$output = sprintf(__('%1$s pingback(s) &#254; %2$s using %3$s',WPI_META),$count,$host,$ua);
	return $output;
}

function wpi_trackback_footer($comment)
{
	$count = wpi_count_trackback_by($comment);
	$ua    = wpi_pingback_agentname($comment);
	$host  = parse_url($comment->comment_author_url,PHP_URL_HOST);
	
	$output = sprintf(__('%1$s trackback(s) &#254; %2$s using %3$s',WPI_META),$count,$host,$ua);
	return $output;
}

function wpi_get_random_avatar_uri(){
	
	$ava = wpi_get_dir(WPI_IMG_IMPORT_DIR.'avatar'.DIRSEP);	
	$ava = apply_filters(wpiFilter::RANDOM_COMMENT_AVA,$ava);
	
	if (has_count($ava)){
		$index	= rand(0,count($ava));
		$path	= 'import/avatar/';
		
		$ava = ( (isset($ava[$index])) ? $ava[$index] : $ava[0] );
		return wpi_img_url($path.$ava);
	}
	
	return false;
}


?>