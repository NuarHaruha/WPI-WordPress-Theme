<?php
if ( !defined('KAIZEKU') ) { die( 42); }

function wpi_get_comment_author(){ 
	global $comment;	
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

/**
 * @since 1.6.2
 * 
 */
function wpi_comments_title($len=80){
?>	
<h2 class="heading-title cf"><span class="fl"><?php comments_number('No Responses', 'One Response', '% Responses' );?></span><strong class="fl">to &#8220;<?php echo string_len(get_the_title(),$len); ?>&#8221;</strong></h2>
<?php	
}

function wpi_comment_start($comment,$option,$depth){	
	global $wp_query, $post;
	$GLOBALS['comment'] = $comment;	
	$cid 		= $comment->comment_ID;
	$author_uri	= $comment->comment_author_url;
	$email 		= $comment->comment_author_email; 
	$author_uri = ($author_uri != '' && $author_uri != 'http://') ? $author_uri : htmlspecialchars( get_comment_link( $cid, $page )); 
	$microid	= get_microid_hash($email,$author_uri);
	$author		= apply_filters('get_comment_author',$comment->comment_author);	
	$rclass		= comment_class('hreview',$cid,$post->ID,false);
	$ava 		= ( stristr($rclass,'bypostauthor')) ? 'avatar-author-wrap.png' : 'avatar-wrap.png';
	
	if (wpi_option('client_time_styles')){
		$file = (string) $_COOKIE[wpiTheme::CL_COOKIE_TIME].'-'.$ava;
		if (file_exists(WPI_IMG_DIR.$file) ){
			$ava = $file;
		}
	} 
?>
				<li id="comment-<?php echo $cid;?>" <?php echo $rclass;?>>
						<ul class="reviewier-column cf r">
							<li class="span-3 fl rn hcard">
								<address class="vcard microid-mailto+http:sha1:<?php echo $microid;?> dc-source"><img src="<?php echo wpi_img_url($ava);?>" width="80" height="80" alt="<?php echo $author; ?>&apos;s photo" class="url cc rn <?php echo wpiGravatar::commentGID($comment); ?> photo" longdesc="#comment-<?php echo $cid ?>" /><a href="<?php echo $author_uri; ?>" class="url fn db" rel="external me" title="<?php echo $author;?>"><?php echo $author; ?></a></address>						
							</li>
							<li class="span-16 fl review-content">
								<dl class="review r cf">
									<dt class="item title summary"><?php wpi_comment_reply_title($comment,$page);?></dt>	
									<dd class="reviewer-meta"><span class="date-since"><?php echo apply_filters(wpiFilter::FILTER_POST_DATE,wpi_get_comment_date('',$comment));?></span> on <abbr class="dtreviewed" title="<? echo wpi_get_comment_date('Y-m-dTH:i:s:Z'); ?>"><?php wpi_get_comment_date('F jS, Y'); ?> at <?php wpi_comment_time('',false,$comment); ?></abbr><?php if(function_exists('hreview_rating')): hreview_rating(); else: ?><span class="rating dn">3</span><span class="type dn">url</span><?php endif;?> &middot; <a href="#microid-<?php echo $cid;?>" class="hreviewer-microid ttip" title="Micro ID | <?php echo $author;?>&apos;s Hash">microId</a> <?php wpi_edit_comment_link('edit','<span class="edit-comment">','</span>',$comment,$post); ?></dd>
									<dd id="microid-<?php echo $cid;?>" class="microid-embed" style="display:none"><input class="on-click-select claimid icn-l" type="text" value="mailto+http:sha1:<?php echo $microid;?>" /></dd>			
									<dd class="reviewer-entry">
									<div class="description"><?php echo apply_filters('get_comment_text', $comment->comment_content);?>
									<?php if ('open' == $post->comment_status && $depth <=2) : ?>	
									<p class="cb reply-links"><small>
									<a href="<?php wpi_comment_reply_uri($post,$comment);?>" title="Reply to <?php echo $author;?>&apos;s comment" class="thickbox">Reply</a></small></p><?php endif;?>
									</div><?php if ($comment->comment_approved == '0') : ?><p class="notice rn">Your comment is awaiting moderation.</p><?php endif; ?></dd>	
								</dl>		
							</li>
						</ul>
<?php
	//wpi_dump($option);
	
}

function wpi_comment_end($comment,$option,$depth){
	global $post;
	$GLOBALS['comment'] = $comment;	
?>
							<!--
							<rdf:RDF xmlns="http://web.resource.org/cc/"
							    xmlns:dc="http://purl.org/dc/elements/1.1/"
							    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">
							<Work rdf:about="<?php the_permalink($post->ID);?>#comment-<?php echo $comment->comment_ID; ?>">
							<license rdf:resource="http://creativecommons.org/licenses/by-sa/3.0/" />
							</Work>
							<License rdf:about="http://creativecommons.org/licenses/by-sa/3.0/">
							   <requires rdf:resource="http://web.resource.org/cc/Attribution" />
							   <requires rdf:resource="http://web.resource.org/cc/ShareAlike" />
							   <permits rdf:resource="http://web.resource.org/cc/Reproduction" />
							   <permits rdf:resource="http://web.resource.org/cc/Distribution" />
							   <permits rdf:resource="http://web.resource.org/cc/DerivativeWorks" />
							   <requires rdf:resource="http://web.resource.org/cc/Notice" />
							</License>
							</rdf:RDF>
							-->	
			</li>
<?php
}

function wpi_comment_reply_title($comment,$page){
	$label = 'RE:';
	if ($comment->comment_parent > 0){
		$pauthor = get_comment($comment->comment_parent);
		$label = sprintf(__('Replying to %s &rarr;',WPI_META),$pauthor->comment_author);
	}
?>
<a rel="dc:source robots-anchortext" href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID, $page )); ?>" class="url fn" title="<?php the_title(); ?>"><span><?php echo $label;?></span> <?php the_title(); ?></a>
<?php	
}

function wpi_edit_comment_link( $link = 'Edit This', $before = '', $after = '',$comment,$post ) {

	if ( $post->post_type == 'attachment' ) {
	} elseif ( $post->post_type == 'page' ) {
		if ( !current_user_can( 'edit_page', $post->ID ) )
			return;
	} else {
		if ( !current_user_can( 'edit_post', $post->ID ) )
			return;
	}

	$link = '<a href="' . get_edit_comment_link( $comment->comment_ID ) . '" title="' . __( 'Edit comment' ) . '">' . $link . '</a>';
	echo $before . apply_filters( 'edit_comment_link', $link, $comment->comment_ID ) . $after;
}

function wpi_comment_paging_heading(){
	global $wp_query;
	//wpi_dump($wp_query);exit;
	$numcomments = intval($wp_query->comment_count);
	$max = intval($wp_query->max_num_comment_pages);
	$comments_per_page = intval(get_query_var('comments_per_page'));
	$pagemax = ceil($numcomments / $comments_per_page);
	t('h5','Comment page '.intval(get_query_var('cpage')).' of '.$pagemax,array('class'=>'comment-paging cb cf') );
}

function wpi_get_comment_date( $d = '', $comment = false ) {
	if ( '' == $d )
		$date = mysql2date( get_option('date_format'), $comment->comment_date);
	else
		$date = mysql2date($d, $comment->comment_date);
	return apply_filters('get_comment_date', $date, $d);
}

function wpi_get_comment_time( $d = '', $gmt = false, $comment = false ) {
	$comment_date = $gmt? $comment->comment_date_gmt : $comment->comment_date;
	if ( '' == $d )
		$date = mysql2date(get_option('time_format'), $comment_date);
	else
		$date = mysql2date($d, $comment_date);
	return apply_filters('get_comment_time', $date, $d, $gmt);
}

function wpi_comment_time( $d = '', $gmt = false, $comment = false ) {
	echo wpi_get_comment_time($d,$gmt,$comment);
}	


function wpi_comment_avatar_src($cid=false,$pid=false){
	
	$src = 'avatar-wrap.png';	

	if (stristr(wpi_post_author_selector_filter(),'post-author') ){
		$src = 'avatar-author-wrap.png';
	}
	
	if ($cid && $pid){
		if (stristr(get_comment_class('',$cid,$pid),'bypostauthor') ){
			$src = 'avatar-author-wrap.png';
		}
	}
	
	
	$file = (string) $_COOKIE[wpiTheme::CL_COOKIE_TIME].'-'.$src;
	if (file_exists(WPI_IMG_DIR.$file)){
		$src = $file;
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