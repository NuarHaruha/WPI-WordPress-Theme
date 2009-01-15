<?php
if (!defined('KAIZEKU')) die(42);
/**
 * $Id$
 * WPI Template functions
 * @package WordPress
 * @subpackage Template
 */
if ( !defined('KAIZEKU') ) { die( 42); }

/**
 * Start def child template wrapper
 * 
 * @since 1.6.2
 * @see wpiTemplate::sectionStart()
 * @param string $section_name valid id name
 * @uses $Wpi wpi object
 * @return string Formatted output in HTML
 */
function wpi_section_start($section_name){
	global $Wpi;
	
	if (!empty($section_name)){
		$Wpi->Template->section = $section_name;
		$Wpi->Template->sectionStart();
	}		
}

/**
 * End def child template wrapper
 * 
 * @since 1.6.2
 * @see wpiTemplate::sectionStart()
 * @uses $Wpi wpi object
 * @return string Formatted output in HTML
 */
function wpi_section_name(){
	global $Wpi;
	return $Wpi->Template->section;
}

function wpi_search_box(){

	$att = array('type'=>'text','value'=>get_search_query(), 'name'=>'s','id'=>'s',
	'title'=>'Search | Start typing. We&#39;ll figure it out','class'=>'ttip');

	if (is_ua('Safari')){
		$att['type'] = 'search';
		$att['placeholder'] = 'Search';
		$att['autosave'] = str_rem('http://',WPI_HOME_URL);
		$att['results'] = '5';	
	} 
	
	echo stab(3)._t('input','',$att);
}

function wpi_get_osd(){
	if (!class_exists('wpiOSD')){
		Wpi::getFile('osd','class');
	}
				
	$osd = new wpiOSD();
	
	if (is_object($osd)){
		header(wpiTheme::CTYPE_XML);
		echo $osd->getContent();
	}
	
	unset($osd);
	exit;
}


/**
 * function wpi_get_webfont()
 * PHP GD text to image replacement
 * 
 * @since 1.5
 * @params mixed|array font settings 
 * @return output PNG type image 
 */
function wpi_get_webfont($args){
	
	if (!wpi_option('gdfont_image')) wpi_http_error_cat();	
	
	$gdtxt = WPI_LIB_IMPORT.'gdtxt.php';
	
	$cache_folder   = WPI_CACHE_FONTS_DIR;
	$background_color = wpi_get_bg_hex_color();
	$transparent_background  = true ;
	$cache_images = true ;

	list(,$type,$rfont,$rsize,$rcolor,$rbgcolor) = $args;
		
	switch ($type){
		case 'blog-name':
			if (!wpi_option('gd_blogname')) wpi_http_error_cat();
				$text = wpi_option('gd_blogname_text');
				$font_size = (float) wpi_option('gd_blogname_text_size');
				$font_color = wpi_option('gd_blogname_color');
				$font_file = WPI_FONTS_DIR.wpi_option('gd_blogname_font');
				require $gdtxt;		
			break;
		}
	exit;
}

function wpi_get_reply_form($args){
	global $wp_query, $withcomments, $post, $wpdb, $id, $comment, $user_login, $user_ID, $user_identity, $overridden_cpage;
		
	list($reply,$pid,$cid,$pcid) = $args;
	$cid = (int) str_rem('cid-',$cid);
	$cpid = (int) str_rem('cpid-',$cpid);
	$pid = (int) str_rem('pid-',$pid);
	
	
	$comment = $GLOBALS['comment'] = get_comment($cid);
	$post = $GLOBALS['post'] = get_post($pid);
	
	echo '<dl class="r">';
	wpi_section_start('reply');	
	$ava = _t('p',get_avatar($comment,'45','identicon'),array('class'=>'fl'));
	t('blockquote',$ava.'<small style="display:block">Excerpt</small>'.get_comment_excerpt(),array('style'=>'width:90%','class'=>'r'));
	//wpi_dump($args);
	t('hr','',array('style'=>'clear:both'));
?>
<?php if ( get_option('comment_registration') && !$user_ID ) : ?>
	<p>You must be <a href="<?php echo WPI_URL_SLASHIT; ?>wp-login.php?redirect_to=<?php echo urlencode(get_permalink($pid)); ?>">logged in</a> to post a comment.</p>
<?php else :?>
<!-- reply form start -->
<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">
<ul id="respond-column" class="r cf">
<li id="respond-textarea" class="fl span-8"><?php $tabindex = ($is_opid) ? '5' : '4'; ?>
	<textarea name="comment" id="comment" cols="200" rows="10" tabindex="<?php echo $tabindex;?>" class="span-8"></textarea>
	<p><?php $tabindex = ($is_opid) ? '6' : '5'; ?>
		<button name="submit" type="submit" id="submit" tabindex="<?php echo $tabindex;?>"><span class="combtn"><?php _e('Submit Comment',WPI_META);?></span></button><input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" />
	</p>
</li>
<li id="respond-meta" class="fl span-8">
<?php if ( $user_ID ) : ?>
<p class="cb">Logged in as <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>. <a href="<?php echo wpi_logout_url(); ?>" title="Log out of this account">Logout &raquo;</a></p>

<?php else : ?>
	<?php $is_reqs = ($req) ?  '<cite>('.__('required').')</cite>' : ''; ?>
	<ul class="r cf">
	<li>
		<input type="text" class="claimid rn" name="author" id="author" value="<?php echo $comment_author; ?>" tabindex="1" />
		<label for="author">Name <?php echo $is_reqs; ?></label>
	</li>
	<li>
		<input type="text" class="gravatar rn" name="email" id="email" value="<?php echo $comment_author_email; ?>" tabindex="2" />
		<label for="email">Email <?php echo $is_reqs; ?></label>
	</li>
	<li>
		<input type="text" class="favicon rn" name="url" id="url" value="<?php echo $comment_author_url; ?>" tabindex="3" />
		<label for="url">Website</label>
	</li>
	<?php if( class_exists('WordpressOpenID')): ?>
	<li>
		<input type="text" name="openid_url" class="openid rn" id="openid_url" tabindex="4" />
		<label for="openid_url">OpenID URL</label>
	</li>
	<?php else: ?>
	<li>Email will not be published.</li>
	<?php endif; ?>
	</ul><?php endif; ?>
<!-- <p><small><strong>XHTML:</strong> You can use these tags: <code><?php echo allowed_tags(); ?></code></small></p>-->
	<input type='hidden' name='comment_post_ID' value='<?php echo $pid;?>' />
	<input type='hidden' name='comment_parent' id='comment_parent' value='<?php echo $cid;?>' />
<?php do_action('comment_form', $post->ID); ?>
</li>
</ul>
</form>
<!-- /reply form end -->
<?php endif; ?>
<?php	
	//t('a','close',array('href'=>'#comment-'.$cid,'onclick'=>'tb_remove();'));
	wpi_section_end();
	echo '</dl>';
	exit;
}

/**
 * Entry content class
 * 
 * @since 1.6.2
 * @param bool $has_excerpt 
 */
function wpi_get_entry_content_class($has_excerpt=false){
	
	$class = 'entry-content description entry cl ox';
		
	$output = ( ( wpi_option('post_excerpt') && $has_excerpt) ? $class : $class.' entry-summary summary'   );
	
	return apply_filters(wpiFilter::FILTER_ENTRY_CONTENT_CLASS,$output);		
}

function wpi_get_public_content($content, $type = 'css'){
	$files = explode(",",$content);
	$lastmodified = 0;
	
	$base = ($type == 'css') ? WPI_CSS_DIR : WPI_JS_DIR;
	
	while (list(,$file) = each($files)) {
		$path = realpath($base.$file.'.'.$type);
		if (!file_exists($path)){
				wpi_http_error_cat();			
		} else {
		
		$lastmodified = max($lastmodified, filemtime($path));
		
		}
		
	}	

	$hash = $lastmodified . '-' . md5($content);
	$h[] = "Etag: \"" . $hash . "\"";

	/**
	 * Prevent MIME-sniffing
	 */
 	$h[] = 'X-Content-Type-Options: nosniff';
	 	
	// returned visit
	if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && 
		stripslashes($_SERVER['HTTP_IF_NONE_MATCH']) == '"' . $hash . '"') {
		
		$h[] = "HTTP/1.0 304 Not Modified";
		$h[] = 'Content-Length: 0';
	} else {
		$contents = '';
		reset($files);
		
		while (list(,$file) = each($files)) {
			$path = realpath($base.$file.'.'.$type);			
			
			if (preg_match('/image/',$file) ){		
				
				$cache_dir = ($type == 'css') ? WPI_CACHE_CSS_DIR : WPI_CACHE_JS_DIR;
				
				$cached_file = $cache_dir.$file.'.'.$type;
				if (file_exists($cached_file)){
					$contents .= file_get_contents($cached_file);
				} else {
					$contents .= wpi_write_css($file.'.'.$type,$path);
				}
			} else {
				$contents .= file_get_contents($path);
			}
		}
		
		if ($type == 'js'){
			$type = 'javascript';
			$contents = str_replace("%theme_url%",json_encode(WPI_THEME_URL),$contents);
		}		
	
	}	
		
		$h[] = "Content-Type: text/" .$type;
		$h[] = 'Content-Length: ' . strlen($contents);	
	
	if (has_count($h)){
		foreach($h as $v){ header($v);	}
	}
	
	echo $contents;	
	exit;	
}
/**
 * Temporary workaround for rel images
 */
function wpi_write_css($filename,$path){
	$content .= file_get_contents($path);	
	$content = str_replace('url(images/','url('.THEME_IMG_URL,$content);
	
	if (wpi_option('cache_css')){
		wpi_write_cache('css'.DIRSEP.$filename,$content);
	}
	
	return $content;
}

function wpi_write_js($filename,$path){
	$content = file_get_contents($path);		
	wpi_write_cache('scripts'.DIRSEP.$filename,$content);
	
	return $content;
}

function wpi_section_end(){
	global $Wpi;
	
	$Wpi->Template->sectionEnd();	
}

function wpi_http_error_cat()
{
	header ("HTTP/1.1 404 Not Found");
	header("Status: 404 Not Found");
	t('img','',array('src'=> wpi_img_url('err.jpg')) );
	exit();	
}

function wpi_body_class(){ echo wpi_get_body_class();}


function wpi_get_body_class($browser_object = false, $load_style = false){

	$sc = is_at();
	
	if (!$load_style && !$browser_object && !is_object($browser_object)){
		global $Wpi;
		$browser_object = $Wpi->Browser;
	}
	
	$output = false;
	
	if (! wpi_user_func_exists('sandbox_body_class')){ 		
		Wpi::getFile('body_class',wpiTheme::LIB_TYPE_IMPORT);
	}
		
	$output = sandbox_body_class(false);
	
	if ($browser_object && !$load_style){
		// append client useragent data  
		$ua = array();
		
		$ua[] = $browser_object->Browser;
		$ua[] = (string) trim(trim($browser_object->Parent, '0'), '.');
		$ua[] = $browser_object->Platform;
		
		$ua = array_map('sanitize_title_with_dashes',$ua);
		$ua = strtolower(join(" ",$ua));
		
		$output .= ' '. $ua;
		
		unset($ua);
	}
	
	if (isset($_COOKIE[wpiTheme::CL_COOKIE_TIME])){
		$cl = $_COOKIE[wpiTheme::CL_COOKIE_TIME];
	
		if (wpi_option('client_time_styles') && $cl != ''){
			$output .= ' '. (string) $cl;
		}	
		
		unset($cl);
	}
	
	if (isset($_COOKIE[wpiTheme::CL_COOKIE_WIDTH])){
		$cl = $_COOKIE[wpiTheme::CL_COOKIE_WIDTH];
	
		if (wpi_option('client_width') && $cl != ''){
			$output .= ' '. (string) $cl;
		}	
	}	
	
	if ($sc == wpiSection::HOME){
		// frontpage type		
		$type = wpi_option('frontpage_style');
		$style = strtr($type,array('frontpage-'=>'','.php'=>''));
		$frontpage_type = sprintf('frontpage-type-%s', strtolower($style));
		
		$output .= ' '.$frontpage_type;
		
		if ('default' != $type){
			// split sidebar 2 & 3 if both has widgets
			if (sidebar_has_widgets_array(array(2,3))){
				$output .= ' sidebar-break';
			}
		}
	}
	
	// has pages menu
	if (wpi_option('menu_page_enable')){
		$output .= ' has-nav';
	}
	
	// gd blogname
	if (wpi_option('gd_blogname')){
		$output .= ' has-gdtitle';
	}	
	
	$output = $output.' -foaf-Document';
	
	return apply_filters(wpiFilter::FILTER_ROOT_CLASS_SELECTOR, $output);
}	


function is_at($display=false,$strip = true){
	
	$ref = 'is_lost';
	$arr = array('is_home','is_front_page','is_single','is_page','is_category',	
				'is_author','is_tag','is_day','is_month','is_year',
				'is_archive','is_search','is_404');

	foreach ($arr as $k){
		if(call_user_func($k)){
			$ref = $k;
			break;
		}
	}
	
	if ($ref == 'is_single'){
		// attachment;
		if (is_attachment()){
			$ref = 'is_attachment';
		}
	}	
	
	if ($strip){
		$ref = str_replace('is_','',$ref);
	}

	if ($display): echo $ref; else: return $ref; endif;
}

function get_hreflang(){
	$output = get_locale();
	$output = (empty($output)) ? 'en-US' : str_replace('_','-',$output) ;
	return $output;
}


function wpi_get_first_cat_obj(){
	$cats	= get_the_category();
	if (is_array($cats)){
		if (isset($cats[0])){
			return $cats[0];
		}
	}
}


function wpi_get_post_current_paged(){
	$page = intval(get_query_var('page'));
	return ($page) ? $page : false;
}

function is_aria_supported(){
	
	$ua = trim($_SERVER['HTTP_USER_AGENT']);	
	return (strpos($ua,'gecko 1.9') || strpos($ua,'Opera/9'));
	
}


function wpi_get_pathway(){
	
	if (!class_exists('wpiPathway')){		
		Wpi::getFile('pathway',wpiTheme::LIB_TYPE_CLASS);
	}
	
	$pt = new wpiPathway();
	return $pt->build();
}


function wpi_pathway(){ echo wpi_get_pathway(); }


function wpi_current_template()
{
	$section 	= is_at();	
	$callback 	= 'wpi_template_'.$section;
	
	if (! wpi_user_func_exists($callback)){
		wpi_template_404();
	} else {
	
		$f = array();		
		$f['wpi_authordata_display_name']	= 'wpi_author_display_name_filter';	
		
		//$f['the_content'] 					= 'wpi_attachment_image_filters';
		if ( $section == wpiSection::CATEGORY 
			|| $section == wpiSection::TAXONOMY 
			|| $section == wpiSection::ARCHIVE 
			|| $section == wpiSection::YEAR  
			|| $section == wpiSection::MONTH  
			|| $section == wpiSection::DAY ){
			$f['the_content'] = 'wpi_cat_content_filter';			
		}
		
		if ($section == wpiSection::SEARCH){			
			$f['the_content'] = 'wpi_search_content_filter';
		}
		
		if ($section == wpiSection::SINGLE
			|| $section == wpiSection::PAGE
			|| $section == wpiSection::HOME){
			$f['the_content'] = 'wpi_google_ads_targeting_filter';
		}
		
		wpi_foreach_hook_filter($f);
		
		call_user_func($callback);
		
		foreach($f as $h => $c) remove_filter($h,$c);
		unset($f);
	}
}

function wpi_post_author_descriptions($post){ 
?>
					<fieldset id="post-author" class="cb cf pdt mgt">
						<?php t('legend',__('About the Author',WPI_META),array('title'=>__('About the Author',WPI_META)));?>			
						<address class="author-avatar <?php wpiGravatar::authorGID();?> rn fl"><span class="rtxt">&nbsp;</span></address>	
						<p id="about-author" class="postmetadata fl"><small class="db rn"><?php the_author_description();?>&nbsp;</small></p>
					</fieldset><?php	
}


function wpi_post_excerpt($post){

	$pid = (int) $post->ID;
	
	if (!has_excerpt($pid)) return;
	
	$eid = 'excerpt-'.$pid;
	$uri = urlencode(get_permalink($pid));
?>
<blockquote id="<?php echo $eid;?>" cite="<?php echo $uri;?>#<?php echo $eid;?>" class="has-excerpt entry-summary summary span-4 fr"><?php the_excerpt();?></blockquote>	
<?php
}

/**
 * Post template for Home
 */

function wpi_template_home()
{ global $post, $authordata;
	$pby_class = (wpi_option('post_by_enable')) ? 'pby' : 'pby dn';
	$rating_class = (wpi_option('post_hrating') ) ? 'rating-count' : 'rating-count dn';	
	$cnt = 0;
	$normal_loop = false;
	
	if (wpi_option('frontpage_style') != 'default'){
		$normal_loop = true;
	}	
?>	
	<ul class="r cf">
	<?php while (have_posts() && $cnt == 0) : the_post(); ?>	
	<li class="xfolkentry hentry hreview vevent cf prepend-1 append-1">	
		<?php if(wpi_option('home_post_thumb') && wpi_get_postmeta('post_thumb_url') != '' ):?>
		<samp id="wpi-thumb-<?php echo $post->ID;?>" class="rtxt wpi-post-thumb depiction">Thumb: <?php echo get_the_title($post->ID);?></samp>
		<?php endif; ?>	
		<dl class="r span-13">
			<dd class="postmeta-date fl" title="<?php the_time('l M, jS Y')?>">
				<ul class="span-1 pdate r">
					<li class="date-month"><span><?php the_time(__('M',WPI_META) );?></span></li>
					<li class="date-day"><span><?php the_time(__('d',WPI_META) );?></span></li>					
					<li class="Person ox">
						<?php if (wpi_option('home_avatar')): ?>
						<address class="rtxt ava <?php wpiGravatar::authorGID();?> depiction">
						<?php else: ?>
						<address class="depiction">
						<?php endif;?>
							<span class="photo rtxt microid-Sha1sum"><?php author_microid();?></span>
						</address>
					</li>									
				</ul>
			</dd>			
			<dd class="postmeta-head span-13 start fl">
				<?php wpi_hatom_title(); ?>
				<div class="postmeta-info">			
					<?php t('span',sprintf(__('Posted by %s.',WPI_META),
						  _t('cite',wpi_get_post_author(),array('class'=>'vcard reviewer author'))), array('class'=>$pby_class));?>
					<p class="di"><?php printf(__('Filed under %s',WPI_META),wpi_cat_links(2)); ?>.</p>
					<p><span class="ptime r ttip" title="Published | <?php echo htmlentities2(wpi_get_postime())?>"><?php printf(__(' <cite>%s</cite>',WPI_META),wpi_get_postime() );?></span></p>
				</div>	
			</dd>	
			<dd class="<?php echo wpi_get_entry_content_class(has_excerpt($post->ID));?>">
		<?php do_action(wpiFilter::ACTION_BEFORE_CONTENT_PREFIX.'home',$post); ?>
				<?php the_content('<span>Read the rest of this entry</span>'); ?>
			<?php do_action(wpiFilter::ACTION_AFTER_CONTENT_PREFIX.'home',$post); ?>
			</dd>		
			<?php the_tags('<dd class="postmeta-tags"><acronym  class="rtxt fl" title="Tags &#187; Taxonomy">Tags:</acronym> <ul class="tags r cfl cf"><li>', '<span class="sep">,</span>&nbsp;</li><li>', '</li></ul></dd>'); ?>
			<dd class="postmeta-comments cf">
			<ul class="xoxo cfl r cf">
			<li class="<?php echo $rating_class;?>"><?php wpi_hrating();?>&nbsp;</li>
			<?php do_action(wpiFilter::ACTION_INSIDE_CONTENT_BOTTOM_BAR_PREFIX.'home'); ?>
			<li class="comments-link">
			<?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?>
			</li>
			</ul>
				<ul class="more dn">
					<li><abbr class="dtstart published dtreviewed dc-date" title="<?php the_time('Y-m-dTH:i:s:Z');?>"><?php the_time('F j, Y'); ?> at <?php the_time('g:i a'); ?></abbr></li>
					<li><abbr class="dtend updated dtexpired" title="<?php the_modified_date('Y-m-dTH:i:s:Z');?>"><?php the_modified_date('F j, Y'); ?> at <?php the_modified_date('g:i a'); ?></abbr></li>
					<li class="type">url</li>					
				</ul>			
			</dd>				
		</dl>
<!--
<?php trackback_rdf(); ?>
-->			
	</li>	
	<?php 
		if (!$normal_loop) $cnt++; 
	?>
	<?php endwhile; ?>
	</ul>
<?php			
}

function wpi_template_content_bottom()
{ global $post, $authordata;
	$pby_class = (wpi_option('post_by_enable')) ? 'pby' : 'pby dn';
	$cnt = 0;
	add_filter('wpi_authordata_display_name','wpi_author_display_name_filter');
?>	
	<ul class="r cf">
	<?php while (have_posts()) : the_post(); ?>
	<?php if ($cnt >= 1 ): ?>
	<li class="xfolkentry hentry hreview vevent cf prepend-1 append-1">
		<?php if(wpi_option('home_post_thumb') && wpi_get_postmeta('post_thumb_url') != '' ):?>
		<samp id="wpi-thumb-<?php echo $post->ID;?>" class="rtxt wpi-post-thumb depiction">Thumb: <?php echo get_the_title($post->ID);?></samp>
		<?php endif; ?>		
		<dl class="r span-13">
			<dd class="postmeta-date fl" title="<?php the_time('l M, jS Y')?>">
				<ul class="span-1 pdate r">
					<li class="date-month"><span><?php the_time(__('M',WPI_META) );?></span></li>
					<li class="date-day"><span><?php the_time(__('d',WPI_META) );?></span></li>		
					<li class="Person ox">
						<?php if (wpi_option('home_avatar')): ?>
						<address class="rtxt ava <?php wpiGravatar::authorGID();?> depiction">
						<?php else: ?>
						<address class="depiction">
						<?php endif;?>
							<span class="photo rtxt microid-Sha1sum"><?php author_microid();?></span>
						</address>
					</li>					
				</ul>
			</dd>			
			<dd class="postmeta-head span-13 start fl">
				<?php wpi_hatom_title(); ?>
				<div class="postmeta-info">			
					<?php t('span',sprintf(__('Posted by %s.',WPI_META),
						  _t('cite',wpi_get_post_author(),array('class'=>'vcard reviewer author'))), array('class'=>$pby_class));?>
					<p class="di"><?php printf(__('Filed under %s',WPI_META),wpi_cat_links(2)); ?>.</p>
					<p><span class="ptime r ttip" title="Published | <?php echo htmlentities2(wpi_get_postime())?>"><?php printf(__(' <cite>%s</cite>',WPI_META),wpi_get_postime() );?></span></p>
				</div>	
			</dd>	
			<dd class="<?php echo wpi_get_entry_content_class(has_excerpt($post->ID));?>">
		<?php do_action(wpiFilter::ACTION_BEFORE_CONTENT_PREFIX.'home',$post); ?>
				<?php the_content('<span>Read the rest of this entry</span>'); ?>
		<?php do_action(wpiFilter::ACTION_AFTER_CONTENT_PREFIX.'home',$post); ?>
			</dd>		
			<?php the_tags('<dd class="postmeta-tags"><acronym  class="rtxt fl" title="Tags &#187; Taxonomy">Tags:</acronym> <ul class="tags r cfl cf"><li>', '<span class="sep">,</span>&nbsp;</li><li>', '</li></ul></dd>'); ?>
			<?php $rating_class = (wpi_option('post_hrating') ) ? 'rating-count' : 'rating-count dn'; ?>
			<dd class="postmeta-comments cf">
			<ul class="xoxo cfl r cf">
			<li class="<?php echo $rating_class;?>"><?php wpi_hrating();?>&nbsp;</li>
			<?php do_action(wpiFilter::ACTION_INSIDE_CONTENT_BOTTOM_BAR_PREFIX.'home'); ?>
			<li class="comments-link">
			<?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?>
			</li>
			</ul>
			</dd>
			<dd class="dn">
				<ul class="more">
					<li>				
						<abbr class="dtstart published dtreviewed dc-date" title="<?php the_time('Y-m-dTH:i:s:Z');?>"><?php the_time('F j, Y'); ?> at <?php the_time('g:i a'); ?></abbr>	
					</li>
					<li>
						<abbr class="dtend updated dtexpired" title="<?php the_modified_date('Y-m-dTH:i:s:Z');?>"><?php the_modified_date('F j, Y'); ?> at <?php the_modified_date('g:i a'); ?></abbr>
					</li>						
					<li class="version">0.3</li>
					<li class="type">url</li>					
				</ul>
			</dd>			
		</dl>
<!--
<?php trackback_rdf(); ?>
-->			
	</li>	
	<?php endif;?>
	<?php $cnt++;?>
	<?php endwhile; ?>
	</ul>
<?php
	remove_filter('wpi_authordata_display_name','wpi_author_display_name_filter');
}
/**
 * Post template for Single
 */

function wpi_template_single()
{ global $post, $authordata;
	$pby_class = (wpi_option('post_by_enable')) ? 'pby' : 'pby dn';
	$rating_class = (wpi_option('post_hrating') ) ? 'rating-count' : 'rating-count dn';
?>
	<ul class="hfeed r cf"><?php while (have_posts()) : the_post(); ?>		
	<li class="xfolkentry hentry hreview vevent hlisting cf">		
		<dl class="r">
			<dd class="postmeta-date fl" title="<?php the_time('l M, jS Y')?>">
				<ul class="span-1 pdate r">
					<li class="pmonth"><span><?php the_time('M');?></span></li>
					<li class="pday"><span><?php the_time('d');?></span></li>
					<li class="pyear"><span><?php the_time('Y');?></span></li>
				</ul>
			</dd>
			<dd class="postmeta-head span-13 start fl">
				<?php wpi_hatom_title(); ?>
				<div class="postmeta-info">			
					<?php t('span',sprintf(__('Posted by %s.',WPI_META),
						  _t('cite',wpi_get_post_author(),array('class'=>'vcard reviewer author'))), array('class'=>$pby_class));?>
					<p class="di"><?php printf(__('Filed under %s',WPI_META),wpi_cat_links(2)); ?>.</p>
					<p><span class="ptime r ttip" title="Published | <?php echo htmlentities2(wpi_get_postime())?>"><?php printf(__(' <cite>%s</cite>',WPI_META),wpi_get_postime() );?></span></p><?php wpi_text_size();?> 
				</div>	
			</dd>
			<dd class="<?php echo wpi_get_entry_content_class(has_excerpt($post->ID));?>">
		<?php do_action(wpiFilter::ACTION_BEFORE_CONTENT_PREFIX.'single',$post); ?>
				<div id="iscontent" class="dynacloud">					
				<?php the_content('Read the rest of this entry &raquo;'); ?>
				</div>
				<?php do_action(wpiFilter::ACTION_AFTER_CONTENT_PREFIX.'single',$post); ?>
			</dd>
			<?php wp_link_pages(array('before' => '<dd class="postmeta-pages"><strong>'.__('Pages',WPI_META).'</strong> ', 'after' => '</dd>', 'next_or_number' => 'number')); ?>
			<?php the_tags('<dd class="postmeta-tags"><acronym  class="rtxt fl" title="Tags &#187; Taxonomy">Tags:</acronym> <ul class="tags r cfl cf"><li>', '<span class="sep">,</span>&nbsp;</li><li>', '</li></ul></dd>'); ?>
			<dd class="postmeta-comments cf">
				<ul class="xoxo cfl r cf">
					<li class="<?php echo $rating_class;?>"><?php wpi_hrating();?>&nbsp;</li>					
					<?php do_action(wpiFilter::ACTION_INSIDE_CONTENT_BOTTOM_BAR_PREFIX.'single'); ?>					
				<?php if ( wpi_option('post_bookmarks') ): ?>
					<li class="postmeta-response"><?php wpi_bookmarks();?></li>
				<?endif;?>			
				</ul>
			<?php edit_post_link(__('Edit this entry.',WPI_META),'<p class="cb edit-links">','</p>');?>
				<ul class="more dn">
					<li><abbr class="dtstart published dtreviewed dc-date" title="<?php the_time('Y-m-dTH:i:s:Z');?>"><?php the_time('F j, Y'); ?> at <?php the_time('g:i a'); ?></abbr></li>
					<li><abbr class="dtend updated dtexpired" title="<?php the_modified_date('Y-m-dTH:i:s:Z');?>"><?php the_modified_date('F j, Y'); ?> at <?php the_modified_date('g:i a'); ?></abbr></li>
					<li class="type">url</li>					
				</ul>			
			</dd>			
		</dl>		
<!--
<?php trackback_rdf(); ?>
-->			
	</li>
	<?php endwhile; ?>
	</ul>
<?php	
}
 
/**
 * Post template for page
 */

function wpi_template_page()
{ global $post;
  $rating_class = (wpi_option('post_hrating') ) ? 'rating-count' : 'rating-count dn';
?>
	<ul class="hfeed r cf">
	<?php while (have_posts()) : the_post(); ?>
	<li class="xfolkentry hentry hreview hlisting cf">		
		<dl class="r">
			<dd class="postmeta-head span-13 fl">
				<?php wpi_hatom_title(); ?>
			<div class="postmeta-info">			
			<span class="pby dn vcard"><?php printf(__('Posted by <acronym class="reviewer author" title="%1$s">%2$s</acronym>',WPI_META),get_the_author_nickname(),wpi_get_post_author());?></span>
			 <span class="ptime r"><?php printf(__(' <cite>%s</cite>',WPI_META),wpi_get_postime() );?>.</span>	
			</div>	
			</dd>
			<dd class="cb entry-content description entry entry-summary summary ox">
		<?php do_action(wpiFilter::ACTION_BEFORE_CONTENT_PREFIX.'page',$post); ?>
				<div id="iscontent" class="dynacloud mgb">					
				<?php the_content('Read the rest of this entry &raquo;'); ?>
				</div>
				<?php do_action(wpiFilter::ACTION_AFTER_CONTENT_PREFIX.'page',$post); ?>
			</dd>
			<?php wp_link_pages(array('before' => '<dd class="postmeta-pages"><strong>'.__('Pages',WPI_META).'</strong> ', 'after' => '</dd>', 'next_or_number' => 'number')); ?>
			<dd class="postmeta-comments cf">
			<ul class="xoxo cfl r cf">
				<li class="<?php echo $rating_class;?>"><?php wpi_hrating();?>&nbsp;</li>					
					<?php do_action(wpiFilter::ACTION_INSIDE_CONTENT_BOTTOM_BAR_PREFIX.'page'); ?>
				<?php if ( wpi_get_theme_option('post_bookmarks') ): ?>
				<li class="postmeta-response"><?php wpi_bookmarks();?></li>
				<?endif;?>			
			</ul>
			<?php edit_post_link(__('Edit this entry.',WPI_META),'<p class="cb edit-links">','</p>');?>
				<ul class="more dn">
					<li class="node-1">				
						<abbr class="dtstart published dtreviewed dc-date" title="<?php the_time('Y-m-dTH:i:s:Z');?>"><?php the_time('F j, Y'); ?> at <?php the_time('g:i a'); ?></abbr>	
					</li>
					<li class="node-2">
						<abbr class="dtend updated dtexpired" title="<?php the_modified_date('Y-m-dTH:i:s:Z');?>"><?php the_modified_date('F j, Y'); ?> at <?php the_modified_date('g:i a'); ?></abbr>
					</li>
					<li class="version">0.3</li>
					<li class="type">url</li>					
				</ul>			
			</dd>			
		</dl>
<!--
<?php trackback_rdf(); ?>
-->			
	</li>	
	<?php endwhile; ?>
	</ul>
<?php	
} 

function wpi_template_author(){
	global $post;	
	$rating_class = (wpi_option('post_hrating') ) ? 'rating-count' : 'rating-count dn';
?>	
	<ul class="hfeed r cf">
	<?php while (have_posts()) : the_post(); ?>
	<li class="xfolkentry hentry hreview vevent cf prepend-1 append-1">		
		<dl class="r span-13">
			<dd class="postmeta-date fl" title="<?php the_time('l M, jS Y')?>">
				<ul class="span-1 pdate r">
					<li class="date-month"><span><?php the_time('M');?></span></li>
					<li class="date-day"><span><?php the_time('d');?></span></li>	
				</ul>
			</dd>			
			<dd class="postmeta-head span-13 start fl">
				<?php wpi_hatom_title(); ?>
				<div class="postmeta-info">			
					<?php t('span',sprintf(__('Posted by %s.',WPI_META),
						  _t('cite',wpi_get_post_author(),array('class'=>'vcard reviewer author'))), array('class'=>$pby_class));?>
					<p class="di"><?php printf(__('Filed under %s',WPI_META),wpi_cat_links(2)); ?>.</p>
					<p><span class="ptime r ttip" title="Published | <?php echo htmlentities2(wpi_get_postime())?>"><?php printf(__(' <cite>%s</cite>',WPI_META),wpi_get_postime() );?></span></p>
				</div>	
			</dd>
			<dd class="<?php echo wpi_get_entry_content_class(has_excerpt($post->ID));?>">
		<?php do_action(wpiFilter::ACTION_BEFORE_CONTENT_PREFIX.'author',$post); ?>	
				<?php the_content('<span>Read the rest of this entry &raquo;</span>'); ?>
		<?php do_action(wpiFilter::ACTION_AFTER_CONTENT_PREFIX.'author',$post); ?>
			</dd>		
			<?php the_tags('<dd class="postmeta-tags"><acronym  class="rtxt fl" title="Tags &#187; Taxonomy">Tags:</acronym> <ul class="tags r cfl cf"><li>', '<span class="sep">,</span>&nbsp;</li><li>', '</li></ul></dd>'); ?>
			<dd class="postmeta-comments cf">
			<ul class="xoxo cfl r cf">
			<li class="<?php echo $rating_class;?>"><?php wpi_hrating();?>&nbsp;</li>					
					<?php do_action(wpiFilter::ACTION_INSIDE_CONTENT_BOTTOM_BAR_PREFIX.'author'); ?>
			<li class="comments-link">
			<?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?>
			</li>
			</ul>
				<ul class="more dn">
					<li>				
						<abbr class="dtstart published dtreviewed dc-date" title="<?php the_time('Y-m-dTH:i:s:Z');?>"><?php the_time('F j, Y'); ?> at <?php the_time('g:i a'); ?></abbr>	
					</li>
					<li>
						<abbr class="dtend updated dtexpired" title="<?php the_modified_date('Y-m-dTH:i:s:Z');?>"><?php the_modified_date('F j, Y'); ?> at <?php the_modified_date('g:i a'); ?></abbr>
					</li>						
					<li class="version">0.3</li>
					<li class="type">url</li>					
				</ul>			
			</dd>			
		</dl>
<!--
<?php trackback_rdf(); ?>
-->			
	</li>	
	<?php endwhile; ?>
	</ul>
<?php	
}

function wpi_template_year()
{
	wpi_template_category();
}

function wpi_template_month()
{
	wpi_template_category();
}

function wpi_template_day()
{
	wpi_template_category();
}

function wpi_template_tag()
{
	wpi_template_category();
}

function wpi_template_category()
{ global $post;
		$pby = wpi_get_theme_option('post_by_enable');
		$pby_class = ($pby) ? 'pby' : 'pby dn';
		$range 	= wpi_get_range_increment(3,3);
		$cnt 	= 1;
		$rating_class = (wpi_get_theme_option('post_hrating') ) ? 'rating-count' : 'rating-count dn';
?>
	<ul class="hfeed r cf">
	<?php while (have_posts()) : the_post(); ?>
	<li class="xfolkentry hentry hreview hlisting vevent span-7 fl prepend-1">		
		<dl class="r">			
			<dd class="postmeta-head">
			<span class="ptime r" title="<?php echo get_the_time('Y-m-dTH:i:s:Z');?>"><?php printf(__(' <cite>%s</cite>',WPI_META),wpi_get_postime() );?></span>
				<?php wpi_hatom_title(); ?>
			<div class="postmeta-info">	
					<?php t('span',sprintf(__('Posted by %s.',WPI_META),
						  _t('cite',wpi_get_post_author(),array('class'=>'vcard reviewer author'))), array('class'=>$pby_class));?>				
			</div>	
			</dd>
			<dd class="entry-content description entry ox">
		<?php do_action(wpiFilter::ACTION_BEFORE_CONTENT_PREFIX.is_at(),$post); ?>	
				<?php the_content('Read the rest of this entry &raquo;'); ?>
		<?php do_action(wpiFilter::ACTION_AFTER_CONTENT_PREFIX.is_at(),$post); ?>
			</dd>
			<dd class="postmeta-comments cf">
			<ul class="xoxo cfl r cf">
				<li class="<?php echo $rating_class;?>"><?php wpi_hrating();?>&nbsp;</li>					
					<?php do_action(wpiFilter::ACTION_INSIDE_CONTENT_BOTTOM_BAR_PREFIX.is_at()); ?>
				<li class="comments-link">
				<?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?>
				</li>
			</ul>
				<ul class="more dn">
					<li>				
						<abbr class="dtstart published dtreviewed dc-date" title="<?php the_time('Y-m-dTH:i:s:Z');?>"><?php the_time('F j, Y'); ?> at <?php the_time('g:i a'); ?></abbr>	
					</li>
					<li>
						<abbr class="dtend updated dtexpired" title="<?php the_modified_date('Y-m-dTH:i:s:Z');?>"><?php the_modified_date('F j, Y'); ?> at <?php the_modified_date('g:i a'); ?></abbr>
					</li>
					<li class="version">0.3</li>
					<li class="type">url</li>					
				</ul>			
			</dd>			
		</dl>
<!--
<?php trackback_rdf(); ?>
-->			
	</li>
	<?php if (isset($range[$cnt])): ?>	
	<li class="hr-line cb cf">&nbsp;</li>
	<?php endif; ?>	
	<?php $cnt++?>
	<?php endwhile; ?>
	</ul>
<?php	
} 

function wpi_template_search()
{ global $post;
	$pby_class = (wpi_get_theme_option('post_by_enable')) ? 'pby' : 'pby dn';
?>	
	<ul class="r cf">
	<?php while (have_posts()) : the_post(); ?>	
	<li class="xfolkentry hentry hreview vevent cf prepend-1 append-1">		
		<dl class="r span-13">
			<dd class="postmeta-date fl">
				<ul class="span-1 pdate r">
					<li class="date-month"><span><?php the_time('M');?></span></li>
					<li class="date-day"><span><?php the_time('d');?></span></li>	
					<li class="Person ox">
						<address class="rtxt ava <?php wpiGravatar::authorGID();?> depiction">
							<span class="photo rtxt microid-Sha1sum"><?php author_microid();?></span>
						</address>
					</li>					
				</ul>
			</dd>			
			<dd class="postmeta-head span-13 start fl">
				<?php wpi_hatom_title(); ?>
			<div class="postmeta-info">
					<?php t('span',sprintf(__('Posted by %s.',WPI_META),
						  _t('cite',wpi_get_post_author(),array('class'=>'vcard reviewer author'))), array('class'=>$pby_class));?>							
			<p class="di"><?php _e('Filed under ',WPI_META);?><?php wpi_cat_links(1); ?>.</p>
			<p><span class="ptime r"><?php printf(__(' <cite>%s</cite>',WPI_META),wpi_get_postime() );?></span></p> 
			</div>	
			</dd>	
			<?php $content_class = 'entry-content description entry cl ox';?>		
		<?php // maybe rep summary for vevent 
			if( !has_excerpt($post->ID) ) $content_class .= ' entry-summary summary';?>
			<dd class="<?php echo $content_class;?>">
			<?php if (wpi_option('post_excerpt') && has_excerpt($post->ID)): ?>
				<blockquote class="has-excerpt entry-summary summary span-4 fr" cite="<?php rawurlencode(get_permalink());?>">
					<?php the_excerpt(); ?>
				</blockquote>
			<?php endif; ?>
			<?php do_action('wpi_before_content_'.is_at(),$post); ?>
				<?php the_content('<span>Read the rest of this entry</span>'); ?>
			</dd>		
			<?php the_tags('<dd class="postmeta-tags"><acronym  class="rtxt fl" title="Tags &#187; Taxonomy">Tags:</acronym> <ul class="tags r cfl cf"><li>', '<span class="sep">,</span>&nbsp;</li><li>', '</li></ul></dd>'); ?>
			<?php $rating_class = (wpi_option('post_hrating') ) ? 'rating-count' : 'rating-count dn'; ?>
			<dd class="postmeta-comments cf">
			<ul class="xoxo cfl r cf">
			<li class="<?php echo $rating_class;?>"><?php wpi_hrating();?>&nbsp;</li>					
					<?php do_action(wpiFilter::ACTION_INSIDE_CONTENT_BOTTOM_BAR_PREFIX.'search'); ?>
			<li class="comments-link"><?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?>
			</li>
			</ul>
				<ul class="more dn">
					<li>				
						<abbr class="dtstart published dtreviewed dc-date" title="<?php the_time('Y-m-dTH:i:s:Z');?>"><?php the_time('F j, Y'); ?> at <?php the_time('g:i a'); ?></abbr>	
					</li>
					<li>
						<abbr class="dtend updated dtexpired" title="<?php the_modified_date('Y-m-dTH:i:s:Z');?>"><?php the_modified_date('F j, Y'); ?> at <?php the_modified_date('g:i a'); ?></abbr>
					</li>						
					<li class="version">0.3</li>
					<li class="type">url</li>					
				</ul>			
			</dd>		
		</dl>
<!--
<?php trackback_rdf(); ?>
-->			
	</li>	
	<?php $cnt++;?>
	<?php endwhile; ?>
	</ul>
<?php	
}

function wpi_template_attachment()
{ global $post;
	$pby_class = (wpi_get_theme_option('post_by_enable')) ? 'pby' : 'pby dn';
?>
	<ul class="hfeed r cf">
	<?php while (have_posts()) : the_post(); ?>
	<li class="xfolkentry hentry hreview hlisting cf">		
		<dl class="r">
			<dd class="postmeta-date fl">
				<ul class="span-1 pdate r">
					<li class="pmonth"><span><?php the_time('M');?></span></li>
					<li class="pday"><span><?php the_time('d');?></span></li>
					<li class="pyear"><span><?php the_time('Y');?></span></li>
				</ul>
			</dd>
			<dd class="postmeta-head span-13 start fl">
				<?php wpi_hatom_title(); ?>
				<div class="postmeta-info">				
					<?php t('span',sprintf(__('Posted by %s.',WPI_META),
						  _t('cite',wpi_get_post_author(),array('class'=>'vcard reviewer author'))), array('class'=>$pby_class));?>
						  <p class="di"><?php _e('Filed under ',WPI_META);?><?php wpi_cat_links(1); ?>.</p>
					<p><span class="ptime r"><?php printf(__(' <cite>%s</cite>',WPI_META),wpi_get_postime() );?></span></p><?php wpi_text_size();?> 
				</div>	
			</dd>
			<dd class="entry-content description entry cb ox">
			<?php do_action('wpi_before_content_attachment'); ?>
<div class="entry-attachment pdt"><a href="<?php echo wp_get_attachment_url($post->ID); ?>" title="<?php echo wp_specialchars( get_the_title($post->ID), 1 ) ?>" class="thickbox thumb-" rel="attachment"><?php echo wp_get_attachment_image( $post->ID, 'large' ); ?></a></div>
					<div class="entry-caption mgt"><?php if ( !empty($post->post_excerpt) ) the_excerpt(); ?></div>			
					<?php if (wpi_option('post_author_description') ): ?>
					<fieldset id="post-author" class="cb cf pdt mgt">
						<?php $ll = __('About the Author',WPI_META);?>
						<?php t('legend',$ll,array('title'=>$ll));?>
						
					<address class="author-avatar <?php wpiGravatar::authorGID();?> rn fl">
					<span class="rtxt">&nbsp;</span>
					</address>	
					<p id="about-author" class="postmetadata fl">
						<small class="db rn"><?php the_author_description();?>&nbsp;</small>
					</p>
					</fieldset>
				<?php endif;?>
			</dd>
			<?php $rating_class = (wpi_get_theme_option('post_hrating') ) ? 'rating-count' : 'rating-count dn'; ?>
			<dd class="postmeta-comments cf">
			<ul class="xoxo cfl r cf">
			<li class="<?php echo $rating_class;?>"><?php wpi_hrating();?>&nbsp;</li>					
					<?php do_action(wpiFilter::ACTION_INSIDE_CONTENT_BOTTOM_BAR_PREFIX.'attachment'); ?>
			<?php if ( wpi_get_theme_option('post_bookmarks') ): ?>
			<li class="postmeta-response"><?php wpi_bookmarks();?>			
			<?endif;?>
			</li>
			
			</ul>
			<?php edit_post_link(__('Edit this entry.',WPI_META),'<p class="cb edit-links">','</p>');?>
			</dd>
			<dd class="dn">
				<ul class="more">
					<li class="node-1">				
						<abbr class="dtstart published dtreviewed dc-date" title="<?php the_time('Y-m-dTH:i:s:Z');?>"><?php the_time('F j, Y'); ?> at <?php the_time('g:i a'); ?></abbr>	
					</li>
					<li class="node-2">
						<abbr class="dtend updated dtexpired" title="<?php the_modified_date('Y-m-dTH:i:s:Z');?>"><?php the_modified_date('F j, Y'); ?> at <?php the_modified_date('g:i a'); ?></abbr>
					</li>
					<li class="version">0.3</li>
					<li class="type">url</li>					
				</ul>
			</dd>			
		</dl>
<!--
<?php trackback_rdf(); ?>
-->			
	</li>
	<?php endwhile; ?>
	</ul>
<?php	
}

function wpi_template_404()
{
	wpi_template_nopost();
}

function wpi_template_nopost()
{
	if (is_search()){
		$terms = get_search_query();
		t('h5','Sorry, no matching articles for <strong>'.$terms.'</strong>.');
		t('script','',array('id'=>'wpi-google-webmaster-widgets', 'type'=>'text/javascript', 'src'=>'http://linkhelp.clients.google.com/tbproxy/lh/wm/fixurl.js'));
		$r = new WP_Query(array('showposts' => 15, 'what_to_show' => 'posts', 'nopaging' => 0, 'post_status' => 'publish'));
		if ($r->have_posts()){
	?>
		<hr class="hr-line"/>
		<h3>Recent posts</h3>
				<ul class="xoxo">
				<?php  while ($r->have_posts()) : $r->the_post(); ?>
				<li><a href="<?php the_permalink() ?>"><?php if ( get_the_title() ) the_title(); else the_ID(); ?> </a></li>
				<?php endwhile; ?>
				</ul>
	<?php
			wp_reset_query();  
		}
	} elseif(is_home()){	
		//t('h5',__('Sorry there is no post yet, please check us back later',WPI_META));
	} else {
	$c = _t('img','',array('src'=> wpi_img_url('err.jpg'),'class'=>'thumb fl') );
	$f = _t('form',_t('div',_t('strong','Marvin&#39;s error log:',array('class'=>'pdl'))._t('textarea','',
	array('row'=>'8','cols'=>'60','wrap'=>'soft','class'=>'db mgt')),array('class'=>'fl')) );
	t('div',$c.$f,array('class'=>'span-22 mgt pdt'));
	t('script','',array('type'=>'text/javascript','src'=>wpi_get_scripts_url('404')));
	t('div',_t('script','',array('id'=>'wpi-google-webmaster-widgets', 'type'=>'text/javascript', 'src'=>'http://linkhelp.clients.google.com/tbproxy/lh/wm/fixurl.js')),array('id'=>'google-webmaster','class'=>'cb mgt rx hr-line tl span-22 pdt'));
	}	
}

function wpi_template_terms_notfound()
{
	t('h2',__('Nothing Found',WPI_META));
	t('p',__('Its not your fault, but nothing matched your search criteria. Please try again with some different keywords.',WPI_META),array('class'=>'notice'));	
}

/**
 * page
 */
 
function wpi_get_post_query($params)
{	global $wp_query;
		
	if (is_object($wp_query))
	{
		settype($params,"string");
		
		if (isset($wp_query->post->$params))
		{
			return $wp_query->post->$params;
		}
	}
	
} 
 
/**
 * @hook	get_the_excerpt
 */
 
function wpi_excerpt_filter($content)
{
	return wpi_append_excerpt($content);
}

function wpi_append_excerpt($excerpt)
{
	$attribs = array();
	$attribs['src'] 		= wpi_get_img_uri('drop-quote.gif');
	$attribs['alt'] 		= get_the_title().' exceprt';	
	$attribs['width'] 		= 25;
	$attribs['height'] 		= 20;
	$attribs['longdesc'] 	= $attribs['src'];
	$attribs['class']		= 'drop-quote fl';
	
	$start = _t('img','',$attribs);
	
	$output = str_replace('<p>','<p>'.$start,$excerpt);
	
	$attribs['src'] 		= str_replace('drop-quote','end-quote',$attribs['src']);
	$attribs['longdesc'] 	= $attribs['src'];
	$attribs['class']		= 'end-quote fn r--';
	
	$end = _t('img','',$attribs);
		
	$output = str_replace('</p>',$end.'</p>',$output);
	
	return $output;
	
}

function wpi_template_comment($post,$comment,$cnt)
{ 
	$author_id 		= get_the_author_ID(); 
	$wauthor_name 	= wpi_get_comment_author();
	$author_uri 	= get_comment_author_url();
	$author_uri 	= ($author_uri != '' && $author_uri != 'http://') ? $author_uri : get_permalink($post->ID).'#comment-'.get_comment_ID();
	$microid 		= wpi_get_microid_hash(get_comment_author_email(),$author_uri);	
?>	
						<li id="comment-<?php comment_ID(); ?>" class="<?php  wpi_comment_root_class($cnt,get_comment_author()); ?>">
							<ul class="reviewier-column cf r">
								<li class="span-1 fl rn hcard">
						<div class="published dtreviewed dc-date span-1 si rn fl" title="<?php comment_time('Y-m-dTH:i:s:Z');?>">
						<ul class="r ox">			
							<li class="month">
								<span><?php comment_time('M') ?></span>
							</li>
							<li class="day">
								<span><?php comment_time('d') ?></span>
							</li>	
							<li>
							<address class="comment-gravatar <?php wpi_comment_author_avatar(get_comment_author_email(),35);?> rn">
								<span class="photo rtxt">
								<?php echo wpi_get_avatar_url(get_comment_author_email(),58,$wauthor_name);?></span>
								</address>
							</li>
						</ul>
						</div>											
						</li>
								<li class="<?php wpi_comment_content_width();?> fl review-content dc-source">
								<dl class="review r cf">				
								<dt class="item title summary ox">	
									<a rel="dc:source robots-anchortext" href="#comment-<?php comment_ID(); ?>" class="url fn" title="<?php the_title(); ?>">
							<span>RE:</span> <?php the_title(); ?></a> 				
								</dt>	
								<dd class="reviewer-meta vcard microid-<?php echo $microid;?> db">									<span class="note dn"><?php the_title(); ?></span>
									<a href="<?php wpi_curie_url($author_uri);?>" class="url fn reviewer" rel="contact noarchive robots-noarchive" title="<?php attribute_escape($wauthor_name);?>"><strong class="nickname"><?php echo $wauthor_name;?></strong></a>			
									 <abbr class="dtreviewed" title="<? comment_date('Y-m-dTH:i:s:Z'); ?>">
									<?php wpi_comment_date(); ?>
									</abbr>	
								<span class="rating dn">3</span>
								<span class="type dn">url</span>				
								&middot; <a href="#microid-<?php comment_ID();?>" class="hreviewer-microid" title="<?php comment_author();?> Micro ID 'click to view' ">microId</a>
								<?php edit_comment_link(__('edit',WPI_META),'&middot; <span class="edit-comment">','</span>'); ?>			 				 
								</dd>
								<dd id="microid-<?php comment_ID();?>" class="microid-embed dn">
								<input class="on-click-select claimid icn-l" type="text" value="mailto+http:sha1:<?php echo $microid;?>" /></dd>
								<dd class="reviewer-entry">						
									<div class="description">
										<p class="br rn r">				
											<?php echo nl2br(get_comment_text()); ?>
										</p>
									</div>
								<?php if ($comment->comment_approved == '0') : ?>
									<p class="notice rn"><?php _e('Your comment is awaiting moderation.',WPI_META);?></p>
								<?php endif; ?>
									
								</dd>	
								<dd class="gml cf">
								<ul class="xoxo r cf">
								<li class="cc span-6 fl ox">
									<span>(cc) <?php echo wpi_get_since_year(get_comment_date('Y'));?> <?php echo $wauthor_name; ?>.</span> 
								</li><?php $counter = $cnt + 1; ?>
								<li class="bookmark fr">
								<?php wpi_comment_ua_html($comment); ?> &middot; 
								<a href="#comment-<?php comment_ID(); ?>" title="Permalink &#187; comments &#35;<?php echo $counter;?>" rel="robots-noanchortext">&#35;<?php echo $counter;?></a></li>  	
								 </ul>
								</dd>
								</dl>		
								</li>
							</ul>
				<!--
				<rdf:RDF xmlns="http://web.resource.org/cc/"
				    xmlns:dc="http://purl.org/dc/elements/1.1/"
				    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">
				<Work rdf:about="<?php the_permalink();?>#comment-<?php comment_ID(); ?>">
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


function wpi_template_comment_pingback($post,$comment,$cnt)
{ 
	$author_id 		= get_the_author_ID(); 
	$wauthor_name 	= wpi_get_comment_author();
	$author_uri 	= get_comment_author_url();
	$author_uri 	= ($author_uri != '' && $author_uri != 'http://') ? $author_uri : get_permalink($post->ID).'#comment-'.get_comment_ID();
	$microid 		= wpi_get_microid_hash(get_comment_author_email(),$author_uri);
	
?>	
						<li id="comment-<?php comment_ID(); ?>" class="<?php  wpi_comment_root_class($cnt,get_comment_author()); ?>">
							<ul class="reviewier-column cf r">
								<li class="span-1 fl">&nbsp;
				<!--
				<rdf:RDF xmlns="http://web.resource.org/cc/"
				    xmlns:dc="http://purl.org/dc/elements/1.1/"
				    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">
				<Work rdf:about="<?php the_permalink();?>#comment-<?php comment_ID(); ?>">
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
								<li class="<?php wpi_comment_content_width();?> fl review-content dc-source">
								
								<dl class="review r cf">				
								<dt class="item title summary ox">
<a rel="dc:source robots-anchortext" href="#comment-<?php comment_ID(); ?>" class="url fn" title="<?php the_title(); ?>">permalink</a> 
								</dt>	
								<dd class="reviewer-meta vcard microid-<?php echo $microid;?> db">									<span class="note dn"><?php the_title(); ?></span>
									<a href="<?php wpi_curie_url($author_uri);?>" class="url fn reviewer" rel="contact noarchive robots-noarchive" title="<?php attribute_escape($wauthor_name);?>">
									<strong class="org" style="background-image:url('<?php echo wpi_comment_pingback_favicon($comment);?>')">
									<?php echo $wauthor_name;?>
									</strong>
									</a>&nbsp;			
									 <abbr class="dtreviewed" title="<? comment_date('Y-m-dTH:i:s:Z'); ?>">
									<?php wpi_comment_date(); ?>
									</abbr>	
								<span class="rating dn">3</span>
								<span class="type dn">url</span>					
								<?php edit_comment_link(__('edit',WPI_META),'&middot; <span class="edit-comment">','</span>'); ?>			 				 
								</dd>
								
								<dd class="reviewer-entry">						
									<div class="description">
										<p class="br rn r">				
											<?php echo nl2br(get_comment_text()); ?>
										</p>
									</div>
								<?php if ($comment->comment_approved == '0') : ?>
									<p class="notice rn"><?php _e('Your comment is awaiting moderation.',WPI_META);?></p>
								<?php endif; ?>
									
								</dd><?php $counter = $cnt + 1; ?>	
								<dd class="gml cf">
								<ul class="xoxo r cf">
								<li class="cc">
									<span><?php echo wpi_pingback_footer($comment);?> </span> 
								</li> 	
								 </ul>
								</dd>
								</dl>		
								</li>
							</ul>
						</li>
<?php							
}

function wpi_comment_guide($post,$comments,$cnt){
	global $comment_alt;
	if (isset($comment_alt)){
		$cnt = (int) $comment_alt;
	}
	$alt = ($cnt % 2) ? 'light' : 'normal';
	$ava = 'avatar-wrap.png';
	if (wpi_option('client_time_styles')){
		$file = (string) $_COOKIE[wpiTheme::CL_COOKIE_TIME].'-'.$ava;
		if (file_exists(WPI_IMG_DIR.$file) ){
			$ava = $file;
		}
	}
?>					
						<li id="comment-00" class="hreview <?php echo $alt;?>">
			<ul class="reviewier-column cf r">
							<li class="span-2 fl rn hcard">
							<address class="vcard microid-mailto+http:sha1:<?php echo get_microid_hash(get_comment_author_email(),WPI_HOME_URL)?>">
							<?php	$photo_url = THEME_IMG_URL.'default-avatar.png';?>
							<img src="<?php echo wpi_img_url($ava);?>" width="42" height="42" alt="stalker&#39;s photo" style="background-image:url('<?php echo wpi_get_random_avatar_uri();?>');background-position:42% 16%;background-color:#2482B0" class="url gravatar photo rn" longdesc="#comment-<?php comment_ID(); ?>" />				
								<a href="<?php echo WPI_HOME_URL; ?>" class="url fn db">
								<?php echo WPI_BLOG_NAME ;?></a> 
							</address>				
							</li>
							<li class="span-9 fl review-content">
							<dl class="review r cf">				
							<dt class="item title summary">	
								<a href="#comment-00" class="url fn scroll-to" title="<?php the_title(); ?>">
								<?php the_title(); ?> - <?php _e('&#39;Comment Guidlines&#39;',WPI_META);?> &darr;</a> 				
							</dt>	
							<dd class="reviewer-meta">
								<span class="date-since">				
									<?php echo apply_filters(wpiFilter::FILTER_POST_DATE,$post->post_date);?>
								</span> on <abbr class="dtreviewed" title="<? echo date('Y-m-dTH:i:s:Z',$post->post_date); ?>">
								<?php the_time('l, F jS, Y') ?> at <?php the_time(); ?>
								</abbr>	
								<span class="rating dn">5</span>
								<span class="type dn">url</span>
							</dd>
								<dd class="reviewer-entry">								
									<?php if('open' == $post->comment_status): ?>
									<p id="comment-guidline" class="description ox">
									<?php _e('If you want to comment, please read the following guidelines. These are designed to protect you and other users of the site.',WPI_META);?></p>
									<ol class="xoxo">
										<li><?php _e('<strong>Be relevant:</strong> Your comment should be a thoughtful contribution to the subject of the entry. Keep your comments constructive and polite.',WPI_META);?></li>
										<li><?php _e('<strong>No advertising or spamming:</strong> Do not use the comment feature to promote commercial entities/products, affiliates services or websites. You are allowed to post a link as long as it&#39;s relevant to the entry.',WPI_META);?></li>						
										<li><?php _e('<strong>Keep within the law:</strong> Do not link to offensive or illegal content websites. Do not make any defamatory or disparaging comments which might damage the reputation of a person or organisation.',WPI_META);?></li>
										<li><?php _e('<strong>Privacy:</strong> Do not post any personal information relating to yourself or anyone else (i.e., address, place of employment, telephone or mobile number or email address).',WPI_META);?></li>
									</ol>
									<p><?php _e('In order to keep these experiences enjoyable and interesting for all of our users, we ask that you follow the above guidlines. Feel free to engage, ask questions, and tell us what you are thinking! insightful comments are most welcomed.',WPI_META);?></p>
										<?php if( (count($comments))  == false):?>
										<p class="no-comments notice rn prepend-3"><?php _e('be the first to comment.',WPI_META);?></p>
										<?php endif;?>
									<?php else:?>
									<p id="comment-closed-notice" class="description ox">
										<?php printf(__('<strong class="db">Comment are closed</strong>Return %1s &middot; %2s',WPI_META),
												_t('a',__('Home',WPI_META),array('href'=>WPI_HOME_URL_SLASHIT,'class'=>'home')),
												_t('a',__('Back to top',WPI_META),array('href'=>'#page','class'=>'scroll-to')) ); ?>
									</p>
									<?php endif;?>
								</dd>	
							</dl>		
							</li>
			</ul>					
						</li>
			<?php // wp_include_comments_adsense_banner(1);?>
<?php	
}

function wpi_template_comment_trackback($post,$comment,$cnt)
{ 
	$author_id 		= get_the_author_ID(); 
	$wauthor_name 	= wpi_get_comment_author();
	$author_uri 	= get_comment_author_url();
	$author_uri 	= ($author_uri != '' && $author_uri != 'http://') ? $author_uri : get_permalink($post->ID).'#comment-'.get_comment_ID();
	$microid 		= wpi_get_microid_hash(get_comment_author_email(),$author_uri);
	
?>	
						<li id="comment-<?php comment_ID(); ?>" class="<?php  wpi_comment_root_class($cnt,get_comment_author()); ?>">
							<ul class="reviewier-column cf r">
								<li class="span-1 fl">&nbsp;
				<!--
				<rdf:RDF xmlns="http://web.resource.org/cc/"
				    xmlns:dc="http://purl.org/dc/elements/1.1/"
				    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">
				<Work rdf:about="<?php the_permalink();?>#comment-<?php comment_ID(); ?>">
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
								<li class="<?php wpi_comment_content_width();?> fl review-content dc-source">
								
								<dl class="review r cf">				
								<dt class="item title summary ox">
<a rel="dc:source robots-anchortext" href="#comment-<?php comment_ID(); ?>" class="url fn" title="<?php the_title(); ?>">permalink</a> 
								</dt>	
								<dd class="reviewer-meta vcard microid-<?php echo $microid;?> db">									<span class="note dn"><?php the_title(); ?></span>
									<a href="<?php wpi_curie_url($author_uri);?>" class="url fn reviewer" rel="contact noarchive robots-noarchive" title="<?php attribute_escape($wauthor_name);?>">
									<strong class="org" style="background-image:url('<?php echo wpi_comment_pingback_favicon($comment);?>')">
									<?php echo $wauthor_name;?>
									</strong>
									</a>&nbsp;			
									 <abbr class="dtreviewed" title="<? comment_date('Y-m-dTH:i:s:Z'); ?>">
									<?php wpi_comment_date(); ?>
									</abbr>	
								<span class="rating dn">3</span>
								<span class="type dn">url</span>					
								<?php edit_comment_link(__('edit',WPI_META),'&middot; <span class="edit-comment">','</span>'); ?>			 				 
								</dd>
								
								<dd class="reviewer-entry">						
									<div class="description">
										<p class="br rn r">				
											<?php echo nl2br(get_comment_text()); ?>
										</p>
									</div>
								<?php if ($comment->comment_approved == '0') : ?>
									<p class="notice rn"><?php _e('Your comment is awaiting moderation.',WPI_META);?></p>
								<?php endif; ?>
									
								</dd><?php $counter = $cnt + 1; ?>	
								<dd class="gml cf">
								<ul class="xoxo r cf">
								<li class="cc">
									<span><?php echo wpi_trackback_footer($comment);?> </span> 
								</li> 	
								 </ul>
								</dd>
								</dl>		
								</li>
							</ul>
						</li>
<?php							
}

function wpi_metabox_start($title,$id,$hide = false){
	
	$tog = _t('a','+',array('href'=>'#','class'=>'togbox'));
	
	$title = ( (is_wp_version('2.6','=')) ? $tog.$title : $title );
	
	$class = 'postbox';
	if ($hide) $class .= ' closed';
	
	$output = '<div id="post'.$id.'" class="'.$class.'">'.PHP_EOL;
	$output .= _t('h3',$title);
	$output .= '<div class="inside">';	
	echo $output;
}

function wpi_metabox_end(){	
	echo '</div>'.PHP_EOL.'</div>';
}

function wpi_postmeta_input($postmeta_id,$style='width:78%', $ifnone = ''){
	$prop = wpi_get_postmeta($postmeta_id);
	t('input','',array(
		'id'	=> 'wpi_'.$postmeta_id,
		'type'	=> 'text',
		'size'	=> 16,
		'style'	=> $style,
		'value' =>  ( ($prop && !empty($prop)) ? $prop : $ifnone)
	));
}

function wpi_postmeta_label($id,$label){
	t('label',$label,array('for'=>'wpi_'.$id,'style'=>'color:#555;width:100px;float:left;display:block;font-weight:700'));
}

/**
 * Page title singular post form
 */
function wpi_postform_metatitle(){
		echo '<p>'.PHP_EOL;
		wpi_postmeta_label('maintitle',__('Title: ',WPI_META));
		wpi_postmeta_input('maintitle');
		echo '</p>'; ?>
	<p><?php _e('<strong>Tips: </strong> Try keep the title tag as unique as possible and make sure the title is relevant to the content on the page. Avoid repeated keywords phrases in title tag.',WPI_META);?></p>
<?php	
}

/**
 * Abstract 'Sub title' singular post form
 */
function wpi_postform_metasubtitle(){
		echo '<p>'.PHP_EOL;
		wpi_postmeta_label('subtitle',__('Sub title',WPI_META));
		wpi_postmeta_input('subtitle');
		echo '</p>'; ?>
	<p><?php _e('<strong>Note: </strong> Entry sub title will be added as meta-abstract.',WPI_META);?></p>
<?php	
} 

/**
 * Meta description singular post form
 */
function wpi_postform_metadescription(){
		echo '<p>'.PHP_EOL;
		wpi_postmeta_label('meta_description',__('Descriptions',WPI_META));
		wpi_postmeta_input('meta_description');
		echo '</p>'; ?>
	<p><?php _e('<strong>Tips: </strong> Make it descriptive and include relevant keywords or keyphrases, 25-30 words, no more than two sentences.',WPI_META);?></p>
<?php	
} 

/**
 * Meta keywords singular post form
 */
function wpi_postform_metakeywords(){
		echo '<p>'.PHP_EOL;
		wpi_postmeta_label('meta_keywords',__('Keywords',WPI_META));
		wpi_postmeta_input('meta_keywords');
		echo '</p>'; ?>
	<p><?php _e('<strong>Tips: </strong> Pick the 10 or 15 terms that most accurately describe the content of the page. Use comma to separate the keywords.',WPI_META);?></p>
<?php	
}

/**
 * Post thumbnail
 */
function wpi_postform_postthumb(){
		echo '<p>'.PHP_EOL;
		wpi_postmeta_label('post_thumb_url',__('Image URL',WPI_META));
		wpi_postmeta_input('post_thumb_url');
		echo '</p>'; 
}

/**
 * Banner singular post form
 */
function wpi_postform_banner(){
		echo '<p id="banner-enabled" style="padding-bottom:9px">';
		wpi_postmeta_label('banner',__('Show banner',WPI_META));
		echo '<select name="wpi_banner" id="wpi_banner" size="2" class="row-4" style="height:36px">';
		$prop = wpi_get_postmeta('banner');  //if (empty($prop))	$prop = 1;
		wpiAdmin::htmlOption(array('Enabled' => 1,'Disabled' => 0),$prop);?>		
	</select></p>
			<p id="banner-url" style="clear:both;border-top:1px solid #dfdfdf;padding-top:18px;padding-bottom:9px">
				<?php $ltitle = __('Image URL ',WPI_META);?>
				<?php wpi_postmeta_label('banner_url',$ltitle);?>
				<?php wpi_postmeta_input('banner_url');?>			
			</p>	
			<p id="banner-height" style="clear:both;border-top:1px solid #dfdfdf;padding-top:18px;padding-bottom:9px">
				<?php $ltitle = __('Banner height ',WPI_META);?>
				<?php wpi_postmeta_label('banner_height',$ltitle);?>				
				<?php wpi_postmeta_input('banner_height','width:10%','72px');?>	
			</p>	
			<p style="clear:both;border-top:1px solid #dfdfdf;padding-top:18px;padding-bottom:9px">			
				<?php $ltitle = __('Background repeat:',WPI_META);?>
				<?php wpi_postmeta_label('banner_repeat',$ltitle);?>
					<select name="wpi_banner_repeat" id="wpi_banner_repeat" size="2" class="row-4" style="height:68px">
			<?php	$prop = wpi_get_postmeta('banner_repeat'); 
					if(empty($prop))	$prop = 'no-repeat';
					wpiAdmin::htmlOption(array('None' => 'no-repeat','Tile' => 'repeat',
					'Horizontal'=>'repeat-x','Vertical'=>'repeat-y'),$prop);?>		
					</select>
			</p>
			<p id="banner-position" style="clear:both;border-top:1px solid #dfdfdf;padding-top:18px;padding-bottom:9px">
				<?php $ltitle = __('Background position ',WPI_META);?>
				<?php wpi_postmeta_label('banner_position',$ltitle);?>				
				<?php wpi_postmeta_input('banner_position','width:10%','left top');?>	
			</p>					

<?php	
}
 
/**
 * Head custom content singular post form
 */
function wpi_postform_headcontent(){
		echo '<p>'.PHP_EOL;
		wpi_postmeta_label('header_content',__('Content',WPI_META));
 ?><textarea id="wpi_header_content" name="wpi_header_content" style="width:70%;height:200px"><?php echo stripslashes_deep(wpi_get_postmeta('header_content'));?></textarea></p>
	<p><?php _e('Content will be added before the &#60;&#47;head&#62; tag.',WPI_META);?></p>
<?php	
}


/**
 * Footer custom content singular post form
 */
function wpi_postform_footercontent(){
		echo '<p>'.PHP_EOL;
		wpi_postmeta_label('footer_content',__('Content',WPI_META));
 ?><textarea id="wpi_footer_content" name="wpi_footer_content" style="width:70%;height:200px"><?php echo stripslashes_deep(wpi_get_postmeta('footer_content'));?></textarea></p>
	<p><?php _e('Content will be added before the &#60;&#47;body&#62; tag.',WPI_META);?></p>
<?php	
}


/**
 * hReview singular post form
 */
function wpi_postform_hreview(){
		echo '<p>'.PHP_EOL;
		wpi_postmeta_label('hrating',__('Content rating',WPI_META));
		wpi_postmeta_input('hrating','width:10%',3);
		echo '</p>'; ?>	
	<p><?php _e('<strong>Note: </strong> content rating for this entry. Max is 5',WPI_META);?></p>
<?php	
}

 
function wpi_register_metaform(){
	$args = array();
	
	if (wpi_option('meta_title')){
		$args[] = array('meta-title',__('Title',WPI_META),'metatitle');
	}
	
	// Entry sub title
	$args[] = array('meta-subtitle',__('Entry sub title',WPI_META),'metasubtitle');
	
	// meta descriptions
	if(wpi_option('meta_description')){
		$args[] = array('meta-descriptions',__('Meta descriptions',WPI_META),'metadescription');
	}
	
	// meta keywords
	if(wpi_option('meta_keywords')){
		$args[] = array('meta-keywords',__('Meta Keywords',WPI_META),'metakeywords');
	}
	
	// post thumb
	if (wpi_option('home_post_thumb')){
		$args[] = array('wpipost-thumb',__('Post thumb',WPI_META),'postthumb');
		
	}
	// banner
	if(wpi_option('banner')){
		$args[] = array('wpi-banner',__('Banner Settings',WPI_META),'banner');
	}		

	// Head custom contents
	$args[] = array('meta-headcontent',__('Head content',WPI_META),'headcontent');

	// Footer custom contents
	$args[] = array('meta-footercontent',__('Footer content',WPI_META),'footercontent');
	
	// hReview
	$args[] = array('meta-hreviewcontent',__('hReview',WPI_META),'hreview');	
			
	if (has_count($args)){
		
		foreach($args as $k=>$option){
			
			list($id,$title,$callback) = $option;
			$callback = 'wpi_postform_'.$callback;
			add_meta_box($id,$title,$callback,'post','advanced');
			add_meta_box($id,$title,$callback,'page','advanced');
			
		}
		
		unset($args);
	}
}

function wpi_post_metaform(){
	
?>
<h2>WP-iStalker Theme options</h2>
	<?php 
	if(wpi_option('meta_title')):?>
	<?php $ptitle = __('Page Title',WPI_META); $ltitle = __('Title: ',WPI_META); ?>
	<?php wpi_metabox_start($ptitle,'maintitle');?>
		<p>
			<?php wpi_postmeta_label('maintitle',$ltitle);?>
			<?php wpi_postmeta_input('maintitle');?>					
		</p>
	<?php wpi_metabox_end();?>
	<?php endif; ?>
	<?php if(wpi_option('meta_description')):?>
	<?php $ptitle = __('Meta Description',WPI_META); $ltitle = __('Descriptions: ',WPI_META); ?>
	<?php wpi_metabox_start($ptitle,'metadescription');?>
		<p>
			<?php wpi_postmeta_label('meta_description',$ltitle);?>
			<?php wpi_postmeta_input('meta_description');?>					
		</p>
	<?php wpi_metabox_end();?>	
	<?php endif; ?>	
	
	<?php if(wpi_option('meta_keywords')):?>
	<?php $ptitle = __('Meta Keywords',WPI_META); $ltitle = __('Keywords: ',WPI_META); ?>
	<?php wpi_metabox_start($ptitle,'metakeywords');?>
		<p>
			<?php wpi_postmeta_label('meta_keywords',$ltitle);?>
			<?php wpi_postmeta_input('meta_keywords');?>					
		</p>
	<?php wpi_metabox_end();?>
	<?php endif; ?>	
	<?php if(wpi_option('banner')):?>
	<?php $ptitle = __('Banner Settings',WPI_META); ?>
	<?php wpi_metabox_start($ptitle,'banner',true);?>
			<p> <?php $ltitle = __('Show banner: ',WPI_META);?>
			<?php wpi_postmeta_label('banner',$ltitle);?>
					<select name="wpi_banner" id="wpi_banner" size="2" class="row-4" style="height:36px">
			<?php	$prop = wpi_get_postmeta('banner');  if (empty($prop))	$prop = 1;
					  wpiAdmin::htmlOption(array('Enabled' => 1,'Disabled' => 0),$prop);?>
					</select>
			</p>		
			<p id="banner-url" style="clear:both">
				<?php $ltitle = __('Image URL: ',WPI_META);?>
				<?php wpi_postmeta_label('banner_url',$ltitle);?>
				<?php wpi_postmeta_input('banner_url');?>			
			</p>
			<p id="banner-height" style="clear:both">
				<?php $ltitle = __('Banner height: ',WPI_META);?>
				<?php wpi_postmeta_label('banner_height',$ltitle);?>				
				<?php wpi_postmeta_input('banner_height','width:10%','72px');?>	
			</p>			
			<p style="clear:both">			
				<?php $ltitle = __('Background repeat:',WPI_META);?>
				<?php wpi_postmeta_label('banner_repeat',$ltitle);?>
					<select name="wpi_banner_repeat" id="wpi_banner_repeat" size="2" class="row-4" style="height:68px">
			<?php	$prop = wpi_get_postmeta('banner_repeat'); 
					if(empty($prop))	$prop = 'no-repeat';
					wpiAdmin::htmlOption(array('None' => 'no-repeat','Tile' => 'repeat',
					'Horizontal'=>'repeat-x','Vertical'=>'repeat-y'),$prop);?>		
					</select>
			</p>			
	<?php wpi_metabox_end();?>	
	<?php endif; ?>	
	<?php $ptitle = __('Entry sub title',WPI_META); ?>
	<?php wpi_metabox_start($ptitle,'subtitle',true);?>	
			<p><?php $ltitle = __('Sub title:',WPI_META);?>
				<?php wpi_postmeta_label('subtitle',$ltitle);?>				
				<?php wpi_postmeta_input('subtitle');?>	
			</p>
			<p><?php _e('will also be added to header as Meta Abstract',WPI_META);?></p>
	<?php wpi_metabox_end();?>
	
	<?php $ptitle = __('hReview',WPI_META); ?>
	<?php wpi_metabox_start($ptitle,'hrating',true);?>	
			<p><?php $ltitle = __('Rating',WPI_META);?>
				<?php wpi_postmeta_label('hrating',$ltitle);?>				
				<?php wpi_postmeta_input('hrating','style:width:10%',3);?>	
			</p>
			<p><?php _e('hReview rating for this entry. Max is 5',WPI_META);?></p>
	<?php wpi_metabox_end();?>
	
	<?php $ptitle = __('Header Content',WPI_META); ?>
	<?php wpi_metabox_start($ptitle,'header_content',true);?>	
			<p><?php $ltitle = __('Content: ',WPI_META);?>
				<?php wpi_postmeta_label('header_content',$ltitle);?>				
				<textarea id="wpi_header_content" name="wpi_header_content" style="width:70%;height:200px"><?php echo stripslashes_deep(wpi_get_postmeta('header_content'));?></textarea>	
			</p>
			<p><?php _e('Content will be added before the &#60;&#47;head&#62; tag.',WPI_META);?></p>
	<?php wpi_metabox_end();?>	
	
	<?php $ptitle = __('Footer Content',WPI_META); ?>
	<?php wpi_metabox_start($ptitle,'footer_content',true);?>	
			<p><?php $ltitle = __('Content: ',WPI_META);?>
				<?php wpi_postmeta_label('footer_content',$ltitle);?>				
				<textarea id="wpi_footer_content" name="wpi_footer_content" style="width:70%"><?php echo stripslashes_deep(wpi_get_postmeta('footer_content'));?></textarea>	
			</p>
			<p><?php _e('Content will be added before the &#60;&#47;body&#62; tag.',WPI_META);?></p>
	<?php wpi_metabox_end();?>		
<?php	
}
/**
 * User profile
 * @hook	profile_personal_options	
 */
function wpi_profile_options()
{ global $user_id;
	$user_profession = get_usermeta($user_id,'user_profession');
	$user_profession = ($user_profession) ? $user_profession : 'Professional Scoundrel';
	
	$user_job_title = get_usermeta($user_id,'user_job_title');
	$user_job_title = ($user_job_title) ? $user_job_title : 'Public Relation Officer';

	$user_birthdate = get_usermeta($user_id,'user_birthdate');
	$user_birthdate = ($user_birthdate) ? $user_birthdate : 'Unknown year';	
	// banner settings
	$user_show_banner = get_usermeta($user_id,'user_show_banner');
	$user_show_banner = ($user_show_banner) ? 1 : 0;
	
	$user_banner_url = get_usermeta($user_id,'user_banner_url');
	$user_banner_url = ($user_banner_url) ? $user_banner_url : 'http://static.animepaper.net/upload/rotate.jpg';

	$user_banner_repeat = get_usermeta($user_id,'user_banner_repeat');
	$user_banner_repeat = ($user_banner_repeat) ? $user_banner_repeat : 'repeat';

	$user_banner_height = get_usermeta($user_id,'user_banner_height');
	$user_banner_height = ($user_banner_height) ? $user_banner_height : '72px';	
	
	$user_banner_position = get_usermeta($user_id,'user_banner_position');
	$user_banner_position = ($user_banner_position) ? $user_banner_position : '0% 0%';			
?>
<h3><?php _e('WPI Profile badge settings');?></h3>
<table class="form-table">
	<tr>
		<th><label for="user_profession"><?php _e('Profession'); ?></label></th>
		<td><input type="text" name="user_profession" id="user_profession" value="<?php echo $user_profession; ?>" /> <?php _e('default is \'Professional Scoundrel\''); ?></td>
	</tr>
	<tr>
		<th><label for="user_job_title"><?php _e('Job title'); ?></label></th>
		<td><input type="text" name="user_job_title" id="user_job_title" value="<?php echo $user_job_title; ?>" /> <?php _e('i.e., Web developer, Front-end Developer,Part time Ninja'); ?></td>
	</tr>	
	<tr>
		<th><label for="user_birthdate"><?php _e('Birthdate'); ?></label></th>
		<td><input type="text" name="user_birthdate" id="user_birthdate" value="<?php echo $user_birthdate; ?>" /></td>
	</tr>	
</table>	
<h3><?php _e('WPI Profile banner settings');?></h3>
<table class="form-table">
	<tr>
		<th><label for="user_show_banner"><?php _e('Show Banner'); ?></label></th>
		<td>
			<select name="user_show_banner" id="user_show_banner" size="2" class="row-2" style="height:36px"><?php wpiAdmin::htmlOption(array('enabled'=>1,'disabled' =>0 ),$user_show_banner);?></select>		
		</td>
	</tr>
	<tr>	
		<th><label for="user_banner_url"><?php _e('Image URL'); ?></label></th>
		<td><?php t('input', '', array('type' => 'text', 'name' => 'user_banner_url','id' =>'user_banner_url','value' => $user_banner_url)); ?>		
		</td>
	</tr>
	<tr>		
		<th><label for="user_banner_repeat"><?php _e('Background repeat'); ?></label></th>
		<td>
			<select name="user_banner_repeat" id="user_banner_repeat" size="2" class="row-4" style="height:68px">					<?php wpiAdmin::htmlOption(array(
					'None' => 'no-repeat',
					'Tile' => 'repeat',
					'Horizontal'=>'repeat-x',
					'Vertical'=>'repeat-y'),$user_banner_repeat);?></select>		
		</td>		
	</tr>
	<tr>	
		<th><label for="user_banner_height"><?php _e('Banner Height'); ?></label></th>
		<td><?php t('input', '', array('type' => 'text', 'name' => 'user_banner_height','id' =>'user_banner_height','value' => $user_banner_height)); ?>		
		</td>
	</tr>
	<tr>	
		<th><label for="user_banner_position"><?php _e('Background position'); ?></label></th>
		<td><?php t('input', '', array('type' => 'text', 'name' => 'user_banner_position','id' =>'user_banner_position','value' => $user_banner_position)); ?>		
		</td>
	</tr>		
</table>
<?php	
}

/**
 * XMDP profile URI
 */
function wpi_head_profile_uri($separator = ','){
	$uri = array();
	
	// hAtom/0.1
	$uri[] = 'http://purl.org/uF/hAtom/0.1/';
	
	// XFN/1.1
	$uri[] = 'http://gmpg.org/xfn/11';
	
	// microformats
	$uri[] = 'http://purl.org/uF/2008/03/';
	
	// rel-tags/1.0
	$uri[] = 'http://purl.org/uF/rel-tag/1.0/';
	
	// rel-license/1.0
	$uri[] = 'http://purl.org/uF/rel-license/1.0/';
	
	// rel-nofollow/1.0
	$uri[] = 'http://purl.org/uF/rel-nofollow/1.0/';
	
	// VoteLinks/1.0
	$uri[] = 'http://purl.org/uF/VoteLinks/1.0/';
	
	// GRDDL
	$uri[] = 'http://www.w3.org/TR/grddl-primer/';
	
	// foaf/0.91
	$uri[] = 'http://purl.org/NET/erdf/profile';
		
	// Standard DC
	$uri[] = 'http://dublincore.org/documents/dcq-html/';
	
	// hCard/1.0
	$uri[] = 'http://purl.org/uF/hCard/1.0/';
	
	// hCalendar/1.0
	$uri[] = 'http://purl.org/uF/hCalendar/1.0/'; 	
	
	
	$uri = apply_filters(wpiFilter::FILTER_HEAD_PROFILE,$uri);
	
	echo join($separator,$uri);
}
?>