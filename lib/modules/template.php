<?php
if ( !defined('KAIZEKU') ) { die( 42); }

function wpi_section_start($section_name){
	global $Wpi;
	
	if (!empty($section_name)){
		$Wpi->Template->section = $section_name;
		$Wpi->Template->sectionStart();
	}		
}

function wpi_section_name(){
	global $Wpi;
	return $Wpi->Template->section;
}

function wpi_search_box(){

	$att = array('type'=>'text','value'=>get_search_query(), 'name'=>'s','id'=>'s',
	'title'=>'Search | Start typing. We&apos;ll figure it out','class'=>'ttip');

	if (is_ua('Safari')){
		$att['type'] = 'search';
		$att['placeholder'] = 'Search';
		$att['autosave'] = str_rem('http://',WPI_URL);
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
	// returned visit
	if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && 
		stripslashes($_SERVER['HTTP_IF_NONE_MATCH']) == '"' . $hash . '"') {
		
		$h[] = "HTTP/1.0 304 Not Modified";
		$h[] = 'Content-Length: 0';
	} else {
		$contents = '';
		reset($files);
		
		$pattern = 'url(\'images/';
		$replace = 'url(\''.THEME_IMG_URL;
		
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
		}
		
		$h[] = "Content-Type: text/" .$type;
		$h[] = 'Content-Length: ' . strlen($contents);		
	}	
		

	
	if (has_count($h)){
		foreach($h as $v){ header($v);	}
	}
	
	echo $contents;	
	exit;	
}

function wpi_write_css($filename,$path){
	$time = date('c',SV_CURRENT_TIMESTAMP);
	$content = PHP_EOL.'/** $Id '.$filename.', wpi-cached:0162 '.$time.' '.WPI_BLOG_NAME.' $ **/'.PHP_EOL;
	$content .= file_get_contents($path);	
	$content = str_replace('url(\'images/','url(\''.THEME_IMG_URL,$content);
	
	wpi_write_cache('css'.DIRSEP.$filename,$content);
	
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
	exit;	
}

function wpi_body_class(){ echo wpi_get_body_class();}


function wpi_get_body_class($browser_object = false){


	if (!$browser_object && !is_object($browser_object)){
		global $Wpi;
		$browser_object = $Wpi->Browser;
	}
	
	$output = false;
	
	if (! wpi_user_func_exists('sandbox_body_class')){ 
		
		Wpi::getFile('body_class',wpiTheme::LIB_TYPE_IMPORT);
	}
	
	 
	
	$output = sandbox_body_class(false);
	
	// append client useragent data  
	$ua = array();
	
	// new browser might not get filtered
	$ua[] = $browser_object->Browser;
	$ua[] = (string) trim(trim($browser_object->Parent, '0'), '.');
	$ua[] = $browser_object->Platform;
	
	$ua = array_map('sanitize_title_with_dashes',$ua);
	$ua = strtolower(join(" ",$ua));
	
	if (wpi_option('client_time_styles') && is_cookie(wpiTheme::CL_COOKIE_TIME)){
		$output .= ' '. (string) $_COOKIE[wpiTheme::CL_COOKIE_TIME];
	}
	
	return $output.' '.$ua.' -foaf-Document';
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

/**
 * Post template for Home
 */

function wpi_template_home()
{ global $post, $authordata;
	$pby_class = (wpi_option('post_by_enable')) ? 'pby' : 'pby dn';
	$cnt = 0;
	
?>	
	<ul class="r cf">
	<?php while (have_posts() && $cnt == 0) : the_post(); ?>	
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
			<span class="<?php echo $pby_class;?>">Posted by <cite class="vcard reviewer author"><?php wpi_post_author();?></cite>.</span> <p class="di"><?php _e('Filed under',WPI_META);?><?php wpi_cat_links(1); ?>.</p>
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
	<?php $cnt++;?>
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
		<dl class="r span-13">
			<dd class="postmeta-date fl">
				<ul class="span-1 pdate r">
					<li class="date-month">
						<span><?php the_time('M');?></span>
					</li>
					<li class="date-day">
						<span><?php the_time('d');?></span>
					</li>			
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
			<span class="<?php echo $pby_class;?>">Posted by <cite class="vcard reviewer author"><?php wpi_post_author();?></cite>.</span> <p class="di"><?php _e('Filed under',WPI_META);?><?php wpi_cat_links(1); ?>.</p>
				<p><span class="ptime r"><?php printf(__(' <cite>%s</cite>',WPI_META),wpi_get_postime() );?></span></p> 
			</div>	
			</dd>	
			<?php $content_class = 'entry-content description entry cl ox';?>		
			<?php // maybe rep summary for vevent 
			if( !has_excerpt($post->ID) ) $content_class .= ' entry-summary summary';
			
			?>
			<dd class="<?php echo $content_class;?>">
			<?php if (wpi_option('post_excerpt') && has_excerpt($post->ID)): ?>
				<blockquote class="has-excerpt entry-summary summary span-4 fr" cite="<?php rawurlencode(get_permalink());?>">
					<?php the_excerpt(); ?>
				</blockquote>
			<?php endif; ?>
			<?php do_action('wpi_before_content_home',$post); ?>
				<?php the_content('<span>Read the rest of this entry</span>'); ?>
			</dd>		
			<?php the_tags('<dd class="postmeta-tags"><acronym  class="rtxt fl" title="Tags &#187; Taxonomy">Tags:</acronym> <ul class="tags r cfl cf"><li>', '<span class="sep">,</span>&nbsp;</li><li>', '</li></ul></dd>'); ?>
			<?php $rating_class = (wpi_option('post_hrating') ) ? 'rating-count' : 'rating-count dn'; ?>
			<dd class="postmeta-comments cf">
			<ul class="xoxo cfl r cf">
			<li class="<?php echo $rating_class;?>"><?php wpi_hrating();?>&nbsp;</li>
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
	$pby_class = (wpi_get_theme_option('post_by_enable')) ? 'pby' : 'pby dn';
?>
	<ul class="hfeed r cf"><?php while (have_posts()) : the_post(); ?>		
	<li class="xfolkentry hentry hreview vevent hlisting cf">		
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
				<span class="<?php echo $pby_class;?>">Posted by <cite class="vcard reviewer author"><?php wpi_post_author();?></cite>.</span> <p class="di"><?php _e('Filed under',WPI_META);?><?php wpi_cat_links(1); ?>.</p>
					<p><span class="ptime r"><?php printf(__(' <cite>%s</cite>',WPI_META),wpi_get_postime() );?></span></p><?php wpi_text_size();?> 
				</div>	
			</dd>
			<dd class="entry-content description entry cl ox">
			<?php do_action('wpi_before_content_'.is_at(),$post); ?>
			<?php if (wpi_option('post_excerpt') && has_excerpt($post->ID)): ?>
				<blockquote cite="<?php echo urlencode(get_the_permalink());?>#excerpt" class="has-excerpt entry-summary summary span-4 fr">
					<?php the_excerpt(); ?>
				</blockquote>			
			<?php endif; ?>
					<?php 	if ( ($pageno = wpi_get_post_current_paged()) != false ):?>
					<div class="postmeta-page span-1 fl">				
						<small>Page</small>		
						<big title="<?php _e('Page '.$pageno,WPI_META);?>"><?php echo $pageno;?></big>
					</div>
					<?php endif; ?>
				<div id="iscontent">					
				<?php the_content('Read the rest of this entry &raquo;'); ?>
				</div>
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
			<?php wp_link_pages(array('before' => '<dd class="postmeta-pages"><strong>'.__('Pages',WPI_META).'</strong> ', 'after' => '</dd>', 'next_or_number' => 'number')); ?>
			<?php the_tags('<dd class="postmeta-tags"><acronym  class="rtxt fl" title="Tags &#187; Taxonomy">Tags:</acronym> <ul class="tags r cfl cf"><li>', '<span class="sep">,</span>&nbsp;</li><li>', '</li></ul></dd>'); ?>
			<?php $rating_class = (wpi_get_theme_option('post_hrating') ) ? 'rating-count' : 'rating-count dn'; ?>
			<dd class="postmeta-comments cf">
			<ul class="xoxo cfl r cf">
			<li class="<?php echo $rating_class;?>"><?php wpi_hrating();?>&nbsp;</li>
			<?php if ( wpi_option('post_bookmarks') ): ?>
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
 
/**
 * Post template for page
 */

function wpi_template_page()
{ global $post;

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
			<dd class="cb entry-content description entry ox">
			<?php do_action('wpi_before_content_'.is_at(),$post); ?>
			<?php if (wpi_get_theme_option('post_excerpt') && has_excerpt($post->ID)): ?>
				<blockquote cite="<?php the_permalink();?>#excerpt" class="has-excerpt entry-summary summary span-4 fr">
					<?php the_excerpt(); ?>
				</blockquote>			
			<?php endif; ?>
					<?php 	if ( ($pageno = wpi_get_post_current_paged()) != false ):?>
					<div class="postmeta-page span-1 fl">				
						<small>Page</small>		
						<big title="<?php _e('Page '.$pageno,WPI_META);?>"><?php echo $pageno;?></big>
					</div>
					<?php endif; ?>
				<div id="iscontent" class="mgb">					
				<?php the_content('Read the rest of this entry &raquo;'); ?>
				</div>

			</dd>
			<?php wp_link_pages(array('before' => '<dd class="postmeta-pages"><strong>'.__('Pages',WPI_META).'</strong> ', 'after' => '</dd>', 'next_or_number' => 'number')); ?>
			<?php the_tags('<dd class="postmeta-tags"><acronym  class="rtxt fl" title="Tags &#187; Taxonomy">Tags:</acronym> <ul class="tags r cfl cf"><li>', '<span class="sep">,</span>&nbsp;</li><li>', '</li></ul></dd>'); ?>
			<?php $rating_class = (wpi_get_theme_option('post_hrating') ) ? 'rating-count' : 'rating-count dn'; ?>
			<dd class="postmeta-comments cf">
			<ul class="xoxo cfl r cf">
			<li class="<?php echo $rating_class;?>"><?php wpi_hrating();?>&nbsp;</li>
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

function wpi_template_author(){
	global $post;	
?>	
	<ul class="hfeed r cf">
	<?php while (have_posts()) : the_post(); ?>
	<li class="xfolkentry hentry hreview vevent cf prepend-1 append-1">		
		<dl class="r span-13">
			<dd class="postmeta-date fl">
				<ul class="span-1 pdate r">
					<li class="date-month"><span><?php the_time('M');?></span></li>
					<li class="date-day"><span><?php the_time('d');?></span></li>	
					<li class="Person dn ox">
						<address class="rtxt ava <?php wpiGravatar::authorGID();?> depiction">
							<span class="photo rtxt microid-Sha1sum"><?php author_microid();?></span>
						</address>
					</li>					
				</ul>
			</dd>			
			<dd class="postmeta-head span-12 start fl">
				<?php wpi_hatom_title(); ?>
			<div class="postmeta-info">				
			<span class="dn">Posted by <cite class="vcard reviewer author"><?php wpi_post_author();?></cite>.</span> <p class="di"><?php _e('Filed under',WPI_META);?><?php wpi_cat_links(1); ?>.</p>
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
		//wpi_dump($range);exit;
?>
	<ul class="hfeed r cf">
	<?php while (have_posts()) : the_post(); ?>
	<li class="xfolkentry hentry hreview hlisting span-7 fl prepend-1">		
		<dl class="r">			
			<dd class="postmeta-head">
			<span class="ptime r" title="<?php echo get_the_time('Y-m-dTH:i:s:Z');?>"><?php printf(__(' <cite>%s</cite>',WPI_META),wpi_get_postime() );?></span>
				<?php wpi_hatom_title(); ?>
			<div class="postmeta-info">	
			<span class="<?php echo $pby_class;?> dn"><?php printf(__('Posted by <acronym class="reviewer author" title="%1$s">%2$s</acronym>',WPI_META),get_the_author_nickname(),wpi_get_post_author());?></span>				
			</div>	
			</dd>
			<?php if (has_excerpt($post->ID)): ?>
			<dd class="entry-summary summary span-4 fr">
				<blockquote cite="<?php the_permalink();?>#excerpt">
					<?php the_excerpt(); ?>
				</blockquote>
			</dd>
			<?php else: ?>
			<dd class="entry-content description entry ox">
				<?php the_content('Continue reading &raquo;'); ?>
			</dd>
			<?php endif; ?>
			<?php $rating_class = (wpi_get_theme_option('post_hrating') ) ? 'rating-count' : 'rating-count dn'; ?>
			<dd class="postmeta-comments cf">
			<ul class="xoxo cfl r cf">
				<li class="<?php echo $rating_class;?>"><?php wpi_hrating();?>&nbsp;</li>
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
	<?php if (isset($range[$cnt])): ?>	
	<li class="hr-line cb cf">
	&nbsp;
	</li>
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
			<span class="<?php echo $pby_class;?>">Posted by <cite class="vcard reviewer author"><?php wpi_post_author();?></cite>.</span> <p class="di"><?php _e('Filed under',WPI_META);?><?php wpi_cat_links(1); ?>.</p>
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
				<span class="<?php echo $pby_class;?>">Posted by <cite class="vcard reviewer author"><?php wpi_post_author();?></cite>.</span> <p class="di"><?php _e('Filed under',WPI_META);?><?php wpi_cat_links(1); ?>.</p>
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
	if ($r->have_posts()) :
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
	endif;
	} else {
	$c = _t('img','',array('src'=> wpi_img_url('err.jpg'),'class'=>'thumb fl') );
	$f = _t('form',_t('div',_t('strong','Marvin&apos;s error log:',array('class'=>'pdl'))._t('textarea','',
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
	$alt = ($cnt % 2) ? 'light' : 'normal';
?>					
						<li id="comment-00" class="hreview <?php echo $alt;?>">
			<ul class="reviewier-column cf r">
							<li class="span-3 fl rn hcard">
							<address class="vcard microid-mailto+http:sha1:<?php echo get_microid_hash(get_comment_author_email(),WPI_URL)?>">
							<?php	$photo_url = THEME_IMG_URL.'default-avatar.png';?>
							<img src="<?php echo wpi_img_url('avatar-wrap.png');?>" width="80" height="80" alt="stalker's photo" style="background-image:url('<?php echo wpi_get_random_avatar_uri();?>');background-position:42% 16%;background-color:#2482B0" class="url gravatar photo rn" longdesc="#comment-<?php comment_ID(); ?>" />				
								<a href="<?php echo WPI_URL; ?>" class="url fn db">
								<?php echo WPI_BLOG_NAME;?></a>
							</address>				
							</li>
							<li class="span-16 fl review-content">
							<dl class="review r cf">				
							<dt class="item title summary">	
								<a href="#comment-00" class="url fn" title="<?php the_title(); ?>">
								<span>RE:</span> <?php the_title(); ?> - 'Commenting Guidlines' &darr;</a> 				
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
								<big class="comment-count fr">0%</big>
								<p id="comment-guidline" class="description">If you want to comment, please read the following guidelines.These are designed to protect you and other users of the site.</p>
								<ol class="xoxo">
									<li><strong>Be relevant:</strong> Your comment should be a thoughtful contribution to the subject of the entry. Keep your comments constructive and polite. </li>
									<li><strong>No advertising or spamming:</strong> Do not use the comment feature to promote commercial entities/products, affiliates services or websites. You are allowed to post a link as long as it's relevant to the entry.</li>						
									<li><strong>Keep within the law:</strong> Do not link to offensive or illegal content websites. Do not make any defamatory or disparaging comments which might damage the reputation of a person or organisation.</li>
									<li><strong>Privacy:</strong> Do not post any personal information relating to yourself or anyone else - (ie: address, place of employment, telephone or mobile number or email address).</li>
								</ol>
								<p>In order to keep these experiences enjoyable and interesting for all of our users, we ask that you follow the above guidlines. Feel free to engage, ask questions, and tell us what you are thinking! insightful comments are most welcomed.</p>
								<?php if( (count($comments))  == false):?>
								<p class="no-comments notice rn prepend-3">be the first to comment.</p><?php endif;?></dd>	
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
	$class = 'postbox';
	if ($hide) $class .= ' closed';
	
	$output = '<div id="post'.$id.'" class="'.$class.'">'.PHP_EOL;
	$output .= _t('h3',$tog.$title);
	$output .= '<div class="inside">';	
	echo $output;
}

function wpi_metabox_end(){	
	echo '</div>'.PHP_EOL.'</div>';
}

function wpi_postmeta_input($postmeta_id,$style='width:70%', $ifnone = ''){
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
	t('label',$label,array('for'=>'wpi_'.$id,'style'=>'color:#555;width:160px;float:left;display:block;font-weight:700'));
}

function wpi_post_metaform(){
	
?>
<h2>WP-iStalker Theme options</h2>
	<?php if(wpi_option('meta_title')):?>
	<?php $ptitle = __('Page Title',WPI_META); $ltitle = __('Title: ',WPI_META); ?>
	<?php wpi_metabox_start($ptitle,'maintitle');?>
		<p>
			<?php wpi_postmeta_label('maintitle',$ltitle);?>
			<?php wpi_postmeta_input('maintitle');?>					
		</p>
	<?php wpi_metabox_end();?>
	<?php endif;?>
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
</table>
<?php	
}?>