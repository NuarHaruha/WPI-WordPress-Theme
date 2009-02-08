<?php
if ( !defined('KAIZEKU') ) {die( 42);}
/**
 *  WPI Widgets 
 * 
 * @package WordPress
 * @subpackage wp-istalker-chrome
 */

function wpi_get_sidebar(){
	get_sidebar(is_at());
}

function wpi_get_active_widget_sidebar_id($widget_id = 'category'){
	global $wp_registered_widgets;
	
	if (!has_count($wp_registered_widgets)){
		return false;
	}
	
	$registered_sidebar = wpiTheme::SIDEBAR_COUNT;
	$key = (string) strtolower($widget_id);
	$widgets = wp_get_sidebars_widgets();	
	
	$sidebar = $active = array();
	
	for( $i = 1; $i < $registered_sidebar; $i++){
		if (! wpiSidebar::hasWidget($i)){
			continue;
		} 
		
		$sidebar[$i] = $widgets['sidebar-'.$i];			
	}
	
	foreach($sidebar as $i=>$items){
		$items = array_flip($items);
		if (isset($items[$key])){
			$active[] = $i;
		}
	}
		
	unset($widgets,$key,$sidebar,$i,$items);
	
	return (is_array($active)) ? $active : false;	
}

function wpi_widget_active_section($widget_id=wpiSection::CATEGORY){	

	$sidebar_id = wpi_get_active_widget_sidebar_id($widget_id);
	
	if (!is_array($sidebar_id)){
		return false;
	}
	
	$sidebar = array();
	
	foreach(range(1,3) as $index){
		$sidebar[$index] = wpiSection::HOME;
	}
	
	foreach(range(4,6) as $index){
		$sidebar[$index] = wpiSection::SINGLE;
	}	

	foreach(range(7,9) as $index){
		$sidebar[$index] = wpiSection::PAGE;
	}		
	
	$sidebar[10] = wpiSection::CATEGORY;
	$sidebar[11] = wpiSection::TAXONOMY;
	$sidebar[12] = wpiSection::ARCHIVE;

	foreach(range(13,15) as $index){
		$sidebar[$index] = wpiSection::AUTHOR;
	}
	
	// 16 - others - custom sidebar, can be place anywhere lol
	// 17 - singular comments
			
	$active = array();
	
	foreach($sidebar_id as $id){
		if (isset($sidebar[$id])){
			$active[] = $sidebar[$id];
		}
	}
	
	unset($sidebar,$sidebar_id);
	
	return $active;	
}

function wpi_widget_start($title='random widget',$name= false)
{	global $Wpi;

	$name = ($name) ? $name : 'widget_'.$_SERVER['REQUEST_TIME'];
	
	$tpl = $Wpi->Sidebar->tpl['widget'];
	
	printf($tpl['before_widget'],$name,'widget_'.$name);
	echo PHP_EOL.$tpl['before_title'].$title.$tpl['after_title'].PHP_EOL;
}

function wpi_widget_end()
{	global $Wpi;	
	echo $Wpi->Sidebar->tpl['widget']['after_widget'].PHP_EOL;
}

/**
 * void wpi_widget_post_summary()
 * Post summary, active at single & page
 * @uses $post  - WP_query post object
 */
function wpi_widget_post_summary()
{	global $post, $commentdata;

	$section = is_at();
	$name 	= 'about-articles';
	$title	= ($section == 'single') ? 'About this articles': 'About';
	$title 	= apply_filters('widget_title',$title);
	
	wpi_widget_start($title,$name);		
		$title	= apply_filters( 'the_title', $post->post_title );
		$link	= _t('a',WPI_BLOG_NAME,array(
				'href'	=>	apply_filters(wpiFilter::FILTER_LINKS,WPI_HOME_URL_SLASHIT),
				'title'	=>	WPI_BLOG_NAME,
				'rel'	=> 'home'));
										
		$hdate 	= apply_filters('postdate',$post->post_date);								
		$date	= _t('span',get_the_time(__('l M jS, Y',WPI_META)),array('class'=>'published-date','title'=>$hdate));
		
		$output = sprintf(__('<big>Y</big>ou&rsquo;re currently reading &ldquo; <strong class="fw-">%1s</strong>&rdquo;. 
		This entry appeared in %2s on %3s.',WPI_META), $title, $link, $date);
		
		t('p',$output,array('class'=>'meta-title'));
		
		$output = sprintf(__('It was last updated at %1s on %2s approximately %3s %4s.',WPI_META),		
				_t('span', get_the_modified_time(__('H:i a',WPI_META)),array('class'=>'date')),
				_t('span', get_the_modified_time(__('M jS o',WPI_META)),array('class'=>'date')),
				_t('sup','&#8773;'), // 'approximately equal to' symbol;
				_t('span',wpi_get_relative_date($post->post_modified),array('class'=>'last-updated hdate')) );
				
		t('p',$output,array('class'=>'meta-published-date'));
		
		do_action('widget_single_summary_after');
	wpi_widget_end();	
}

/**
 * single page next/previous links
 */
function wpi_widget_single_nav()
{ 
	$name 	= 'entry-navigation';
	$title	= __('Keep looking',WPI_META);
	$title 	= apply_filters('widget_title',$title);
	
	wpi_widget_start($title,$name);
	rewind_posts();
?>
		<dl class="xoxo vert profile">
		<?php previous_post_link('<dt>Previous article</dt><dd>%link</dd>');?>
		<?php next_post_link('<dt>Next article</dt><dd>%link</dd>') ?>
		</dl>
<?php	
	wpi_widget_end();
}

/**
 * related post base on taxonomy (single)
 */

function wpi_widget_related_post()
{
	$title  = wpi_option('related_post_widget_title');
	
	$name 	= 'related-article';
	$title	= ( ($title) ? $title : __('Related articles',WPI_META)  );
	$title 	= apply_filters('widget_title',$title);
	if ( ($rel_post = wpi_get_related_post_tag()) != false)
	{
		wpi_widget_start($title,$name);
		echo $rel_post;
		wpi_widget_end();
	}
}

/**
 * Post page children links
 */
function wpi_widget_subpages()
{ global $post;	
	
	$children = false;
	
	if ( $post->post_parent ) {
		$post_page = &get_post($post->post_parent);
		$plink = _t('a',$post_page->post_title,array(
			'href'=> get_permalink($post->post_parent),
			'title'=> __($post_page->post_title.' Index page',WPI_META),
			'rev' => 'page:parent',
			'rel' => 'previous'
		));
		unset($post_page);
		$children = wp_list_pages("title_li=&child_of=".$post->post_parent."&echo=0&exclude=".$post->ID); 
		$children = _t('ul',$children,array('class'=>'r cf') );
		$children = _t('li',$plink.$children,array('class'=>'parent page_item'));
		
	} else {
		$children = wp_list_pages("title_li=&child_of=".$post->ID."&echo=0");
	}				
	
	if ($children)
	{
		$name 	= 'subpages';
		$title  = __ngettext('page','pages',count(explode('</li>',$children)),WPI_META);		
		$title 	= apply_filters('widget_title',__('Similar ',WPI_META).$title);	
			
		wpi_widget_start($title,$name);
		t('ul',$children,array( 'class'=>'xoxo r cf') );
		wpi_widget_end();
	}		
}
function wpi_do_sidebars_range($start=1,$end=3)
{
	$cnt = 1;
	$option = array();
	
	
	foreach(range($start,$end) as $index)
	{
		$css_class = ($cnt === 1) ? 'class="cf"' : null;
			
		echo '<dd id="sidebar-'.$cnt.'" '.$css_class.'>'."\n";
		wpi_dynamic_sidebar($index);
		echo '</dd>'."\n";		
		
		$cnt++;
	}
}

function wpi_dynamic_sidebar($id = 1)
{	
	$sidebar_start 	= '<ul class="xoxo r">';
	$sidebar_end	= '</ul>';
	
	if (wpiSidebar::hasWidget($id))
	{
		echo $sidebar_start;
		do_action('wpi_before_sidebar_'.$id);
		dynamic_sidebar($id);	
		do_action('wpi_after_sidebar_'.$id);
		echo $sidebar_end;
	} else {
		wpi_no_sidebar($id);
	}	
}

function wpi_no_sidebar($id = 1)
{
	$filter = 'wpi_sidebar_'.$id.'_nowidget';
	echo '<ul class="xoxo r cf">';
	do_action($filter);
	echo '</ul>';
	
}

// copies of wp_widget_categories with a little twist
function wpi_category_treeview_widget($args, $widget_args = 1)
{
	extract($args, EXTR_SKIP);
	if ( is_numeric($widget_args) )
		$widget_args = array( 'number' => $widget_args );
	$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
	extract($widget_args, EXTR_SKIP);

	$options = get_option('widget_categories');
	if ( !isset($options[$number]) )
		return;

	$c = $options[$number]['count'] ? '1' : '0';
	$h = $options[$number]['hierarchical'] ? '1' : '0';
	$d = $options[$number]['dropdown'] ? '1' : '0';

	$title = empty($options[$number]['title']) ? __('Categories') : apply_filters('widget_title', $options[$number]['title']);

	echo $before_widget;
	echo $before_title . $title . $after_title;

	$cat_args = array('orderby' => 'name', 'show_count' => $c, 'hierarchical' => $h);

	if ( $d ) {
		$cat_args['show_option_none'] = __('Select Category');
		wp_dropdown_categories($cat_args);
?>

<script type='text/javascript'>
/* <![CDATA[ */
    var dropdown = document.getElementById("cat");
    function onCatChange() {
		if ( dropdown.options[dropdown.selectedIndex].value > 0 ) {
			location.href = "<?php echo get_option('home'); ?>/?cat="+dropdown.options[dropdown.selectedIndex].value;
		}
    }
    dropdown.onchange = onCatChange;
/* ]]> */
</script>

<?php
	} else {
?>
		<ul id="categories-treeview" class="xoxo cf treeview">
		<?php 
			$cat_args['title_li'] = '';
			wp_list_categories($cat_args); 
		?>
		</ul>
<?php
	echo $after_widget;
}
}



function wpi_widget_author_stalker_pass()
{ global $authordata;
	$name 	= 'stalker-pass';
	$title  = sprintf(__('%s&apos;s press badge',WPI_META),$authordata->display_name);
	$title  = apply_filters('widget_title',$title);
	
	if (is_object($authordata)){
		$user_name = $authordata->display_name;
		
		$user_desc = $authordata->user_description;
		
		$user_desc = (!empty($user_desc)) ? $user_desc : 'unknown stalkers';
		
		$avatar_uri = wpiGravatar::getURL(md5($authordata->user_email),92,'G');
		if (wpi_option('cache_avatar')){
			$avatar_uri = apply_filters(wpiFilter::FILTER_LINKS,$avatar_uri.'.ava');
		}
		
		// jobs
		
		$profession = (isset($authordata->user_profession)) ? $authordata->user_profession : 'Professional Scoundrel';
		
		$job_title = (isset($authordata->user_job_title)) ? $authordata->user_job_title : 'Public Relation Officer';
		
		// sub info
		
		$user_registered = $dstart = strtotime($authordata->user_registered);		
		$user_registered = date('M j, Y',$user_registered);
		$user_registered_title = attribute_escape( date('Y-m-dTH:i:s:Z',$dstart) );
		
		$year =  31556926; 
		
		$dend = $dstart + ($year * 12 );
		$user_expired  = date('M j, Y',$dend);
		$user_expired_title = attribute_escape( date('Y-m-dTH:i:s:Z',$dend) );
		
		$bday = (isset($authordata->user_birthdate)) ? $authordata->user_birthdate : 'Unknown year';		
	}
	
	wpi_widget_start($title,$name);
?>	
	<dl class="r profile cf ox">
		<dt id="display-name"><?php echo $user_name;?></dt>
		<dd id="user-description" class="dn"><?php echo $user_desc;?></dd>
		<dd id="user-avatar"><img src="<?php echo $avatar_uri;?>" width="92" height="92" alt="<?php echo attribute_escape($user_name);?>" longdesc="/" class="photo"/></dd>
		<dt id="profession"><?php echo $profession;?></dt>
		<dd id="job_title"><small><?php echo $job_title;?></small></dd>
		<dd id="stalker-info">
			<ul class="xoxo cf r">
				<li class="stalker-since">
				<small><abbr class="dstart" title="<?php echo $user_registered_title;?>"><?php echo $user_registered;?></abbr></small></li>
				<li class="stalker-expired">
				<small><abbr class="dend" title="<?php echo $user_expired_title;?>"><?php echo $user_expired;?></abbr></small> </li>
				<li><small><?php echo $bday;?></small></li>
			</ul>
		</dd>
	</dl>
<?php	
	wpi_widget_end();	
}

function wpi_most_download_widget(){
	if (!wpi_user_func_exists('get_most_downloaded')) return false;
	global $wp_query;
	// asume downloads is located at download page
	if (get_option('download_page_url') != self_uri()) return;	

	$limit  = 5;
	$title  = apply_filters('widget_title',__('Most downloads',WPI_META));
	
	wpi_widget_start($title,'most-downloads');
		$htm = get_most_downloaded($limit,0,false);
		t('ul',$htm,array('class'=>'select-odd'));	
	wpi_widget_end();
}

function wpi_widget_author_summary()
{global $authordata;

	if (!is_object($authordata)){
		return ;
	}
	
	$name 	= 'author-data';
	$title	= apply_filters('widget_title',__('Author details',WPI_META) );
	
	wpi_widget_start($title,$name);
	$name = convert_chars($authordata->display_name);
	$url = ($authordata->user_url != 'http://') ? $authordata->user_url : WPI_HOME_URL_SLASHIT;
	
	$im = array();	
	
	if (isset($authordata->aim) && !empty($authordata->aim))
	{
		$attribs = array(
			'title'	=> __('AOL Instant Messenger'),
			'href'	=> 'aim:goim?screenname='.$authordata->aim);
		
		$im['AIM'] = _t('a',$authordata->aim,$attribs).' '._t('a','profile', array('href'=>'http://profiles.aim.com/'.$authordata->aim));

	}
	
	if (isset($authordata->jabber) && !empty($authordata->jabber))
	{
		$im['jabber'] =	_t('a',$authordata->jabber,array('title'=>__('eXtensible Messaging Jabber Client'),'href'=> 'xmpp:'.antispambot($authordata->jabber) ) );

	}	
	
	
	if (isset($authordata->yim) && !empty($authordata->yim))
	{
		$im['Yim'] =	_t('a',$authordata->yim,array('title'=>__('Yahoo Instant Messenger'),'href'=> 'ymsgr:sendIM?'.$authordata->yim) ) ;

	}
	
		
	
?>
	<div class="author-content">
		<ul class="xoxo r cf">
			<li><span>Name: </span> <?php t('strong',$name);?></li>
			<li><span>Website: </span> <?php t('a',$url,array('href'=>$url,'rel'=>'me'));?></li>
			<li><span>Articles: </span> <?php echo get_the_author_posts();?></li>
		<?php 
			if (has_count($im) ) {
				foreach($im as $k => $v){
					t('li',$k.': '.$v);
				}
			}
		?>
		</ul>
	</div>
<?php	
	wpi_widget_end();
}


function wpi_tags_widget()
{
	$options = get_option('widget_tag_cloud');
	
	$title = empty($options['title']) ? __('Tags',WPI_META) : apply_filters('widget_title', $options['title']);
	
	wpi_widget_start($title,'tag_cloud');
	wp_tag_cloud();
	wpi_widget_end();
}

function wpi_dynacloud_widget()
{	
	$title =apply_filters('widget_title', 'Most used terms');
	
	wpi_widget_start($title,'widget_dyna_cloud');
	t('div','',array('id'=>'dynacloud'));
	wpi_widget_end();
}

function wpi_trackback_pingback_widget()
{	global $wp_query;
	
	// has comments?
	if (!$wp_query->comments) return; 
	
	// has ping, pingback or trackback;
	$has_ping = intval(wpi_has_trackback_pingback($wp_query->post->ID));
	if ( $has_ping <= 0  ) return;

	$title =apply_filters('widget_title', 'Trackback &amp; Pingback');
	$len   = 69; 
	$count = 0;
	$htm = PHP_EOL;
	
	wpi_widget_start($title,'widget_tping');	

	foreach($wp_query->comments as $comment){
			$GLOBALS['comment'] = $comment;
		if ( ($type = get_comment_type()) != 'comment'){
			
			$title = get_comment_author();			
			$uri = get_comment_author_url();
			$host = get_host($uri);
			
			if ($type == 'pingback'){
				$count = wpi_count_pingback_by($comment);
			} else {
				$count = wpi_count_trackback_by($comment);
			}
			
			
			$by = _t('cite', sprintf(__('%1$s %2$s from %3$s',WPI_META),$count ,$type ,$host));
			$link = _t('a',string_len($title,$len),array('href'=>wpi_get_curie_url($uri),
			'title'=> sprintf(__('%1$s from %2$s | %3$s',WPI_META),ucfirst($type),$host,$title),'class'=> 'ttip'));
						
			$htm .= _t('li',$link.$by);
		}
	}
	t('ol',$htm);
	
	wpi_widget_end();
	
}

function wpi_pages_widget()
{
	// inherit settings from default pages options widgets	
	$options = get_option( 'widget_pages' );
	$title = empty( $options['title'] ) ? __( 'Pages',WPI_META ) : apply_filters('widget_title', $options['title']);
	$sortby = empty( $options['sortby'] ) ? 'menu_order' : $options['sortby'];
	$exclude = empty( $options['exclude'] ) ? '' : $options['exclude'];	

	if ( $sortby == 'menu_order' ) {
		$sortby = 'menu_order, post_title';
	}

	$output = wp_list_pages( array('title_li' => '', 'echo' => 0, 'sort_column' => $sortby, 'exclude' => $exclude) );
	// return false  if there is no pages
	if ( empty( $output ) ) return false;
	
	// Conflict maybe?
	$elmID = apply_filters(wpiFilter::FILTER_ELM_ID.'wpi_pages_widget','pages');
	
	wpi_widget_start($title,$elmID);
	t('ul',$output,array('class'=>'xoxo'));
	wpi_widget_end();
}

function wpi_technorati_backlink()
{	global $post, $commentdata;

	$class = 'cf';
	
	if (wpi_is_plugin_active('global-translator/translator.php')
	&& wpi_option('widget_gtranslator')){
		$class .=' hr-line';
	}
	
	echo '<div id="technorati_til" class="'.$class.'">'.PHP_EOL;
	t('script','',array('src'=>'http://embed.technorati.com/linkcount', 'type'=> 'text/javascript', 'charset'=>'utf-8'));
	$title = __('View blog reactions',WPI_META);	
	t('a',$title,array('href'=>'http://technorati.com/search/'.get_permalink($posts->ID ),'title'=> $title,'class'=>'tr-linkcount'));
	echo '</div>';	
}

function wpi_register_widgets()
{ global $wp_query;

	$owidgets = array();
	
	
	if (wpi_option('widget_treeview')){
		$owidgets[] = array('key'=>'/categories/','callback'=>'wpi_category_treeview_widget');
	}
	
	if (wpi_option('overwrite_flickrrss')){
		$owidgets[] = array('key'=>'/flickrrss/','callback'=>'wpi_flickrrss_widget_rep');
	}
	
	if (wpi_option('overwrite_recent_comments')){
		$owidgets[] = array('key'=>'/recent-comments/','callback'=>'wpi_widget_recent_comments_rep');	
	}
	
	wpi_overwrite_widgets_callback($owidgets);
	
	if ( is_active_widget('wp_widget_recent_comments') 
	|| is_active_widget('wpi_widget_recent_comments_rep') ){
		remove_filter('wp_head', 'wp_widget_recent_comments_style');
	}	
	
	wpi_grid_sidebar_filter();
		
	// custom widgets
		wpi_foreach_hook(array(
			'wpi_before_sidebar_4',
			'wpi_sidebar_4_nowidget',
			'wpi_before_sidebar_7',
			'wpi_sidebar_7_nowidget'),'wpi_widget_post_summary');
		
		wpi_foreach_hook(array('wpi_before_sidebar_4','wpi_sidebar_4_nowidget'),'wpi_single_tab');	
			
		/*if (wpi_option('widget_related_post')){
			wpi_foreach_hook(array(
				'wpi_before_sidebar_4',
				'wpi_sidebar_4_nowidget'),'wpi_widget_related_post');			
		}*/
		
		wpi_foreach_hook(array(
			'wpi_after_sidebar_4',
			'wpi_sidebar_4_nowidget'),'wpi_widget_single_nav');
			
		// author 	
	wpi_foreach_hook(array(
	  'wpi_before_sidebar_13',
	  'wpi_sidebar_13_nowidget'),'wpi_widget_author_stalker_pass');
			
		// author info	
		wpi_foreach_hook(array(
			'wpi_before_sidebar_13',
			'wpi_sidebar_13_nowidget'),'wpi_widget_author_summary');
		
	add_action('wpi_after_sidebar_7','wpi_widget_subpages');	
	add_action('wpi_before_sidebar_7','wpi_most_download_widget',11);
	
	
	// main sidebar 1 (active when there is no widgets)
		
		foreach(array('tags','pages') as $name){
			$priority = ($name == 'tags') ? 10 : wpiTheme::LAST_PRIORITY;
			wpi_foreach_hook(array(
					'wpi_sidebar_1_nowidget',
					'wpi_sidebar_7_nowidget'),'wpi_'.$name.'_widget',$priority);	
		}
	// comments
	wpi_foreach_hook(array('wpi_before_sidebar_17','wpi_sidebar_17_nowidget'),'wpi_trackback_pingback_widget');
	wpi_foreach_hook(array('wpi_before_sidebar_17','wpi_sidebar_17_nowidget'),'wpi_tags_widget');
	
	if (wpi_option('widget_dynacloud')){
		wpi_foreach_hook(array('wpi_before_sidebar_17','wpi_sidebar_17_nowidget'),'wpi_dynacloud_widget');
	}
	
	if (wpi_option('widget_technorati_backlink')){
		add_action('widget_single_summary_after','wpi_technorati_backlink');	
	}
	
}

function sidebar_has_widgets($id){
	return wpiSidebar::hasWidget($id);
}

function sidebar_has_widgets_array(array $sidebar_id){
	
	$bool = false;
	
	if (has_count($sidebar_id)){		
		foreach($sidebar_id as $id){
			$bool = wpiSidebar::hasWidget($id);
		}	
		
		unset($sidebar_id);
	}
	
	return $bool;
}

/**
 * void wpi_flickrrss_widget_rep()
 * flickr rss widget replacement
 * @since 1.6.2
 */
 
function wpi_flickrrss_widget_rep(){
	$options = get_option('widget_flickrRSS');	
	$title = apply_filters('widget_title', $options['title']);	
	wpi_widget_start($title,'flickrrss');
	
	$spinner = _t('img','',array(
		'src'=>wpi_img_url('loadingAnimation.gif'),
		'alt'=>'loading-content','width'=>'208','height'=>'13','class'=>'db')
	);
	
	$label = __('Loading &#8230;',WPI_META);
	
	t('div',$spinner. _t('cite', $label ),array('class'=>'preloading') );
	
	wpi_widget_end();	
}

/**
 * void wpi_flickrrss_widget()
 * 
 * @since 1.6.2
 * @link http://eightface.com/wordpress/flickrrss/
 */
function wpi_flickrrss_widget(){
	
	if ( ! wpi_is_plugin_active('flickr-rss/flickrrss.php') ) return;
	
	$options = get_option('widget_flickrRSS');	
	$title = apply_filters('widget_title', $options['title']);
	
	wpi_widget_start($title,'flickrrss');
	echo $options['before_images'];
	get_flickrRSS();
	echo $options['after_images'];	
	wpi_widget_end();
	
	unset($options,$title);
}
 

/**
 * wpi_get_widget_content()
 * 
 * output static type widget
 * $hash  = md5(callback);
 * 
 * @since 1.6.2
 */

function wpi_get_widget_content($args){
	global $wp_query;
	
	$widgets = array();
		
	list(,$hash) = $args;
		
	// static widgets
	$widgets['686a006014345b3a36aff17a668d6156'] = array('name'=>'pages','callback'=>'wpi_pages_widget');		
	$widgets['2a22b5dd770eb3a9b53f91aa6ab36f73'] = array('name'=>'tags cloud','callback'=>'wpi_tags_widget');
	$widgets['ef2bee7f7667c7fb6894ea9769a410b9'] = array('name'=>'recent entries','callback'=>'wpi_widget_recent_entries');
		
	/**
	 * noncache flickr rss content is expensive 
	 * seo +1
	 */	
	$widgets['df695b32187596617d0beaa25760a8a0'] = array('name'=>'flickrrss','callback'=>'wpi_flickrrss_widget');
	
	/**
	 * Recent comments with external links! 
	 * seo +1
	 */
	 $widgets['b47bdb6bde262b0537f6f2a7fbfe825f'] = array('name'=>'recent comments','callback'=>'wpi_widget_recent_comments');
	
	if (isset($widgets[$hash])){
		$callback = (string) $widgets[$hash]['callback'];
		
		if (wpi_user_func_exists($callback) ){
			call_user_func($callback);
		}		
	} else {
		wpi_widget_start(apply_filters('widget_title', __('Widget aren\'t ready yet!',WPI_META) ),'widget-error-'.SV_CURRENT_TIMESTAMP);
		t('h3',__('<strong>Error<strong>, request not found',WPI_META));
		t('img','',array('src'=> wpi_get_random_avatar_uri()) );
		wpi_widget_end();
	}
	
	//wpi_dump(md5('wpi_widget_recent_entries'));
	exit();
}

/**
 * void wpi_widget_recent_comments_rep()
 * @since 1.6.2
 */

function wpi_widget_recent_comments_rep(){
	
	$options = get_option('widget_recent_comments');
	$title = empty($options['title']) ? __('Recent Comments') : apply_filters('widget_title', $options['title']);
		
	wpi_widget_start($title,'recent-comments');
	
	$spinner = _t('img','',array(
		'src'=>wpi_img_url('loadingAnimation.gif'),
		'alt'=>'loading-content','width'=>'208','height'=>'13','class'=>'db')
	);
	
	$label = __('Loading &#8230;',WPI_META);
	
	t('div',$spinner. _t('cite', $label ),array('class'=>'preloading') );
	
	wpi_widget_end();	
}
 

/**
 * void wpi_widget_recent_comments()
 * @since 1.6.2
 */
function wpi_widget_recent_comments() {
	global $wpdb, $comments, $comment;
	
	$options = get_option('widget_recent_comments');
	$title = empty($options['title']) ? __('Recent Comments') : apply_filters('widget_title', $options['title']);
	if ( !$number = (int) $options['number'] )
		$number = 5;
	else if ( $number < 1 )
		$number = 1;
	else if ( $number > 15 )
		$number = 15;

	if ( !$comments = wp_cache_get( 'recent_comments', 'widget' ) ) {
		$comments = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_approved = '1' ORDER BY comment_date_gmt DESC LIMIT $number");
		wp_cache_add( 'recent_comments', $comments, 'widget' );
	}
	
	wpi_widget_start($title,'recent-comments'); ?>
			<ul id="recentcomments" class="xoxo">
			<?php 
			if ( $comments ) : 
			$cnt = 0;
				foreach ( (array) $comments as $comment) :
					$class = 'recentcomments';
					$class .= ($cnt % 2) ? ' even' : ' odd';
				echo  '<li class="'.$class.'">' . sprintf(__('%1$s on %2$s'), get_comment_author_link(), '<a href="'. get_comment_link($comment->comment_ID) . '">' . get_the_title($comment->comment_post_ID) . '</a>') . '</li>';
				$cnt++;
				endforeach; 
			endif;?>
			</ul>
	<?php wpi_widget_end();
} 

function wpi_overwrite_widgets_callback(array $owidgets){
	
	if (has_count($owidgets)){
		global $wp_registered_widgets;
		
		if(has_count($wp_registered_widgets)){
			
			foreach($owidgets as $w){				
			
				foreach($wp_registered_widgets as $widgets => $attribs){
		
					if ( preg_match($w['key'], $widgets) ) {
						$GLOBALS['wp_registered_widgets'][$widgets]['callback'] = $w['callback'];
					}						
				}
				
			}
		}			
	}
}


function wpi_get_one_liner($display = false){
	
	$q = array();
	
	$q[] = "It is bad luck to be superstitious.";
	$q[] = "No one is listening until you make a mistake";
	$q[] = "As long as there are tests, there will be prayer in public school";
	$q[] = "Things are more like they used to be than they are now.";
	$q[] = "Always be sincere, even if you don't mean it";
	$q[] = "A clear conscience is ussualy the sign of bad memory";
	$q[] = "If at first you DO succeed, try not to look astonished!";	
	$q[] = "You can't have everything. Where will you put it.";
	$q[] = " Ambition is a poor excuse for not having enough sense to be lazy.";
	$q[] = "If you can do something later why do it now.";
	$q[] = "I wish they all would stop trying.";
	$q[] = "Procrastination is the art of keeping up with yesterday.";
	$q[] = "If you try to fail, and succeed, which have you done?";
	$q[] = "Always be sincere, even if you don't mean it";		
	$q[] = "Lust, not Love.";
	$q[] = "Time is what keeps thing from happening all at once.";
	$q[] = "If practice makes perfect, and nobody's perfect, why practice?";
	$q[] = "MY inspiration is me, me, me, ALL ME.";
	$q[] = "I intend to live forever - so far, so good.";
	$q[] = "According to my best recollection... I dont remembers.";
	$q[] = " When I'm not in my right mind, my left mind gets pretty crowded.";
	$q[] = "Nothing is <strong>Fool</strong> Proof.";
	$q[] = "<strong>On the other hand</strong>. you have different fingers";
	$q[] = "Too many freaks, not enough circuses.";
	$q[] = "If you can't be kind, at least have the decency to be vague.";
	$q[] = "I smile because I don't know what the hell is going on.";
	$q[] = " Sarcasm is just one more service we offer.";
	$q[] = "Errors have been made. Others will be blamed.";
	$q[] = "See no evil, hear no evil, date no evil.";
	$q[] = "Stress is when you wake up screaming & you realize you haven't fallen asleep yet.";
	$q[] = "If you're too open minded, your brains will fall out.";
	$q[] = "Artificial intelligence is no match for natural stupidity.";
	$q[] = "If you must choose between two evils, pick the one you\'ve never tried before.";
	$q[] = "It is easier to get forgiveness than permission";
	$q[] = "If you look like your passport picture, you probably need the trip.";
	$q[] = "A conscience is what hurts when all your other parts feel so good.";
	$q[] = "When everything's coming your way, you're in the wrong lane";
	$q[] = "Thou shalt not weigh more than thy refrigerator.";
	$q[] = "Someone who thinks logically provides a nice contrast to the real world.";
	$q[] = "Blessed are they who can laugh at themselves for they shall never cease to be amused.";
	$q[] = "If everything seems to be going well, you have obviously overlooked something";
	$q[] = "Everyone has a photographic memory. Some don't have film.";
	$q[] = "OK, so what's the speed of dark?";
	$q[] = "Mental Floss prevents Moral Decay.";
	$q[] = "Proofread carefully to see if you any words out.";
	$q[] = "Ever stop to think, and forget to start again? ";
	$q[] = "Our forum never has a bugs. It just develops random features.";
	$q[] = "Access denied--nah nani na nah nah! ";
	$q[] = "All computers wait at the same speed.";
	$q[] = "Don't sweat the petty things, and don't pet the sweaty things.";
	$q[] = "One nice thing about egotists: They don't talk about other people.";
	$q[] = "To be intoxicated is to feel sophisticated but not be able to say it.";
	$q[] = "Never underestimate the power of stupid people in <strong>large</strong> groups.";
	$q[] = "The older you get, the better you realize you were.";
	$q[] = "I doubt, therefore I might be.";
	$q[] = "Age is a very high price to pay for maturity.";	
	$q[] = "A fool and his money are soon partying.";
	$q[] = "44444 is the new 31337";
	
	
	$r = rand_array($q);
	
	if ($display): echo $q[$r]; else : return $q[$r]; endif;
}

function wpi_single_tab(){
	global $wp_query;
	
	$title = $content = array();
	
	wpi_widget_start('Related meta','related-meta');
	if ( ($rel_post = wpi_get_related_post_tag()) != false)
	{
		$title[1]  = wpi_option('related_post_widget_title');
		$title[1]	= ( ($title[1]) ? $title[1] : __('Related entries',WPI_META)  );
		$content[1] = $rel_post;
	} 
	
	$view_options = get_option('widget_views_most_viewed');
	
	if ($most_view = wpi_get_most_viewed('post',6,0,false)){
		
		$title[3] = htmlspecialchars(stripslashes($view_options['title']));			
		$title[3]	= ( ($title[3]) ? $title[3] : __('Most views',WPI_META)  );
		$content[3] = _t('ul',$most_view,array('class'=>'select-odd') );			
	} else {		
		$cat_options = get_option('widget_categories');
		if ( !isset($cat_options[$number]) ){
			$cat_options[$number] = 0;		
		}		
	
		$c = $cat_options[$number]['count'] ? '1' : '0';
	
		$title[3] = empty($cat_options[$number]['title']) ? __('Categories') :  $cat_options[$number]['title'];
	
		$cat_args = array('orderby' => 'name', 'show_count' => $c, 'hierarchical' => 1 , 'title_li'=>'','echo'=>0);
		
		
		$output = '<ul id="categories-treeview" class="xoxo cf treeview">';
		$output .= wp_list_categories($cat_args); 
		$output .= '</ul>';
		
		$content[3] = $output;	
	}
?>
        <div id="singular-relmeta">
            <ul class="ui-tabs-nav cf">
            	<?php if(isset($title[1])): ?>
                <li><a href="#relmeta-1"><span><?php echo $title[1];?></span></a></li>
                <?php endif; ?>
                <li><a href="#relmeta-2"><span>Recent entries</span></a></li>
                <li><a href="#relmeta-3"><span><?php echo $title[3]; ?></span></a></li>
            </ul>
            <div class="tabs-panel">
            
           	<?php if(isset($content[1])): ?>
            <div id="relmeta-1">
                <?php echo $content[1];?>
            </div>
            <?php endif;?>
            <div id="relmeta-2" class="ui-tabs-hide">
            <?php
				$spinner = _t('img','',array(
					'src'=>wpi_img_url('loadingAnimation.gif'),
					'alt'=>'loading-content','width'=>'208','height'=>'13','class'=>'db')
				);
				
				$label = __('Loading &#8230;',WPI_META);
				
				t('div',$spinner. _t('cite', $label ),array('class'=>'preloading','id'=>'mrecentpost') );
			?>
			</div>
            <div id="relmeta-3" class="ui-tabs-hide">
            	<?php echo $content[3]; ?>
            </div>
            </div>
        </div>
<?php	
	wpi_widget_end();
}

function wpi_widget_recent_entries() {
	$meta = 'wpi_widget_recent_entries';
	$output = wp_cache_get($meta, 'widget');
	if ( !$output || '' == $output){
	$options = get_option('widget_recent_entries');
	$title = empty($options['title']) ? __('Recent Posts') : apply_filters('widget_title', $options['title']);
	if ( !$number = (int) $options['number'] )
		$number = 10;
	else if ( $number < 1 )
		$number = 1;
	else if ( $number > 15 )
		$number = 15;

	$r = new WP_Query(array('showposts' => $number, 'what_to_show' => 'posts', 'nopaging' => 0, 'post_status' => 'publish', 'caller_get_posts' => 1));
	if ($r->have_posts()) :
		$cnt = 0;
		$output = '<ul class="xoxo">'.PHP_EOL;
		while ($r->have_posts()) : $r->the_post(); 
			$rg = ($cnt % 2) ? 'even' : 'odd'; 
			$output .= PHP_T.'<li class="'.$rg.'"><a href="'.rel(get_permalink()).'">';
			if ( get_the_title() ){ 
				$output .= get_the_title(); 
			} else {
				$output = get_the_ID();
			}
			$output .='</a></li>'.PHP_EOL;
		$cnt++; 
		endwhile; 
		$output .='</ul>'.PHP_EOL;

		wp_reset_query();  // Restore global post data stomped by the_post().
	endif;	
		wp_cache_add($meta, $output, 'widget');
	}

	echo $output;
	unset($output);
}

function wpi_get_most_viewed($mode,$limit,$chars,$display=false){
	
	if(function_exists('get_most_viewed')){
		return get_most_viewed($mode,$limit,$chars,$display);	
	} else {
		return false;	
	}
}
?>