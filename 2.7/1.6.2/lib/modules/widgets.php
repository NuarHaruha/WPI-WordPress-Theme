<?php
if ( !defined('KAIZEKU') ) {die( 42);}


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
	if (wpi_option('widget_treeview')){
		wpi_overwrite_widget_cat();
	}
	
	if ( is_active_widget('wp_widget_recent_comments') ){
		remove_filter('wp_head', 'wp_widget_recent_comments_style');
	}	
	
	wpi_grid_sidebar_filter();
		
	// custom widgets
		wpi_foreach_hook(array(
			'wpi_before_sidebar_4',
			'wpi_sidebar_4_nowidget',
			'wpi_before_sidebar_7',
			'wpi_sidebar_7_nowidget'),'wpi_widget_post_summary');
			
		if (wpi_option('widget_related_post')){
			wpi_foreach_hook(array(
				'wpi_before_sidebar_4',
				'wpi_sidebar_4_nowidget'),'wpi_widget_related_post');			
		}
		
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

function wpi_overwrite_widget_cat(){
	global $wp_registered_widgets;
	
	if(has_count($wp_registered_widgets)){
		foreach($wp_registered_widgets as $widgets => $attribs){

			if ( preg_match("/categories/", $widgets) ) {
				$GLOBALS['wp_registered_widgets'][$widgets]['callback'] = 'wpi_category_treeview_widget';
			}
			
		}
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
?>