<?php
if ( !defined('KAIZEKU') ) { die( 42); }
	
function wpi_get_theme_option($name){
	
    $options = get_option(WPI_META_PREFIX.'settings');

    if (isset($options[$name])) {
        return $options[$name];
    } else {
        wpi_update_theme_options($name);
        return wpi_get_theme_option($name);
    }
}

function wpi_blog_since_year(){
	global $wpdb;
	return (int) $wpdb->get_var("SELECT DATE_FORMAT(`user_registered`,'%Y') FROM $wpdb->users ORDER BY user_registered ASC LIMIT 1");
}


function wpi_theme_option($name){
    echo wpi_get_theme_option($name);
}

function wpi_option($name){
	return wpi_get_theme_option($name);
}


function wpi_update_theme_options($name, $value = ''){
	
    $metakey = WPI_META_PREFIX.'settings';
    $options = $noptions = get_option($metakey);
    $noptions[$name] = $value;

    if ($options != $noptions) {
        $options = $noptions;
        update_option($metakey, $options);
    }
}

function wpi_update_form_meta($pid,$tag){
	delete_post_meta( $pid, $tag );
	if ($tag == 'header_content' || $tag == 'footer_content'){
		$_POST['wpi_'.$tag] = stripslashes_deep($_POST['wpi_'.$tag]);
	}
	add_post_meta( $pid, $tag, $_POST['wpi_'.$tag] );	
}


function wpi_get_postmeta($key){ 
	global $post;

	if ( ($meta = wpi_get_post_meta($post->ID,$key)) != false){
		if (!isset($meta[0])){
			return false;
		}
		return (string) $meta[0];
	} else {
		return false;
	}
}


function wpi_get_post_meta($post_id, $key, $single = false){
	
	$post_id = (int) $post_id;

	$meta_cache = wp_cache_get($post_id, 'post_meta');

	if (!isset($meta_cache[$key])){
		return false;
	}
	
	if ( isset($meta_cache[$key]) ) {
		if ( $single ) {
			return maybe_unserialize( $meta_cache[$key][0] );
		} else {
			return maybe_unserialize( $meta_cache[$key] );
		}
	}

	if ( !$meta_cache ) {
		update_postmeta_cache($post_id);
		$meta_cache = wp_cache_get($post_id, 'post_meta');
	}
	
	if ( $single ) {
		if ( isset($meta_cache[$key][0]) )
			return maybe_unserialize($meta_cache[$key][0]);
		else
			return '';
	} else {
		return maybe_unserialize($meta_cache[$key]);
	}
}


function wpi_get_author_data($field_name = 'user_email',$output = OBJECT){	
	global $wpdb;
	
	// get all authors
	$output = $wpdb->get_results("SELECT $field_name from $wpdb->users",$output);	
	return $output;	
}

function wpi_get_postmeta_key($post_id,$meta_key){
	global $wpdb;

	$post_id = (int) $post_id;
	$output =  $wpdb->get_var( $wpdb->prepare( "SELECT meta_value FROM $wpdb->postmeta WHERE meta_key = %s AND post_id = %d", $meta_key, $post_id ) );
	
	return maybe_serialize($output); 	
}

function wpi_get_post_single_field($field_name,$post_id){ 
	global $wpdb;

	return $wpdb->get_var("SELECT $field_name FROM $wpdb->posts WHERE ID = '$post_id' LIMIT 1");	
}


function wpi_get_comments($post_id)
{ global $wp_query, $wpdb, $user_ID;

	$commenter = wp_get_current_commenter();
	extract($commenter, EXTR_SKIP);
	
	if ( $user_ID) {
		$comments = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d AND (comment_approved = '1' OR ( user_id = %d AND comment_approved = '0' ) )  ORDER BY comment_date", $post_id, $user_ID));
	} else if ( empty($comment_author) ) {
		$comments = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d AND comment_approved = '1' ORDER BY comment_date", $post_id));
	} else {
		$comments = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->comments WHERE comment_post_ID = %d AND ( comment_approved = '1' OR ( comment_author = %s AND comment_author_email = %s AND comment_approved = '0' ) ) ORDER BY comment_date", $post_id, $comment_author, $comment_author_email));
	}

	return $comments;
}

function wpi_get_related_post_tag()
{	global $wpdb, $post,$table_prefix;
	
	$exclude = '';
	
	$limit = (int) wpi_option('related_post_widget_max');
	$limit = ( ($limit ) ? $limit : 5);
	$wp_no_rp = 5;
	$show_date = wpi_option('related_post_widget_date');
	$show_comments_count = wpi_option('related_post_widget_comments_count');
	
	if ( $exclude != '' ) {
		$q = "SELECT tt.term_id FROM ". $table_prefix ."term_taxonomy tt, " . $table_prefix . "term_relationships tr WHERE tt.taxonomy = 'category' AND tt.term_taxonomy_id = tr.term_taxonomy_id AND tr.object_id = $post->ID";

		$cats = $wpdb->get_results($q);
		
		foreach(($cats) as $cat) {
			if (in_array($cat->term_id, $exclude) != false){
				return;
			}
		}
	}
		
	if(!$post->ID){
		return false;
	}
	
	$now = current_time('mysql', 1);
	$tags = wp_get_post_tags($post->ID);

	
	$taglist = "'" . $tags[0]->term_id. "'";
	
	$tagcount = count($tags);
	if ($tagcount > 1) {
		for ($i = 1,$tagcount = count($tags); $i <= $tagcount; $i++) {
			if (isset($tags[$i])){
				$taglist = $taglist . ", '" . $tags[$i]->term_id . "'";
			}
		}
	}
		
	if ($limit) {
		$limitclause = "LIMIT $limit";
	}	else {
		$limitclause = "LIMIT 10";
	}
	
	$q = "SELECT DISTINCT p.ID, p.post_title, p.post_date, p.comment_count, count(t_r.object_id) as cnt FROM $wpdb->term_taxonomy t_t, $wpdb->term_relationships t_r, $wpdb->posts p WHERE t_t.taxonomy ='post_tag' AND t_t.term_taxonomy_id = t_r.term_taxonomy_id AND t_r.object_id  = p.ID AND (t_t.term_id IN ($taglist)) AND p.ID != $post->ID AND p.post_status = 'publish' AND p.post_date_gmt < '$now' GROUP BY t_r.object_id ORDER BY cnt DESC, p.post_date_gmt DESC $limitclause;";

	$related_posts = $wpdb->get_results($q);
	$output = "";
	$cnt = 0;	
	
	if (!$related_posts){
		return false;
	}
	
	foreach ($related_posts as $related_post ){
		
		$xt = 'list-'.$cnt;
		$xt .= ($cnt % 2 ) ? ' even' : ' odd';
	
		
		$ttitle = $ntitle	= wptexturize($related_post->post_title);
				
		$attrib = array();
		$attrib['href'] 	= apply_filters(wpiFilter::FILTER_LINKS,(get_permalink($related_post->ID)) );
		$attrib['title'] 	= $ttitle;
		$attrib['class'] 	= 'foaf-isPrimaryTopicOf dc-subject';
		$attrib['rev'] 		= 'site:relative';
		
		if ($show_comments_count){		
			$ntitle .=  ' '._t('small','('.$related_post->comment_count.')',
			array(
				'title'=> 'Comments Count: '.$related_post->comment_count,
				'class'=>'comment-count'));
		}
		
		$link = _t('a',$ntitle,$attrib);
		
		if ($show_date){
			$dateformat = 'm/d';
			$date = mysql2date($dateformat, $related_post->post_date);
			$date_title = mysql2date('r', $related_post->post_date);
			$link = _t('small',$date,array('title'=>'Published on '.$date_title)).' '.$link;
		}	
		
		$output .=  _t('li',$link,array('class'=>$xt));
		$cnt++;
	}
	
	$output = '<ul class="xoxo cf">' . $output . '</ul>';
		
	return $output;	
}

/** comments */

function wpi_get_comment_type_count($post_id, $type = '')
{ global $wpdb;

	$query = "SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = %d AND comment_post_ID = %d AND comment_type = %s";
	
	return $wpdb->get_var($wpdb->prepare($query,1, $post_id,$type));
}

function wpi_count_pingback_by($comment)
{ global $wpdb;

	$url = parse_url($comment->comment_author_url,PHP_URL_HOST);
	$query = "SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = %d AND comment_type = %s AND comment_author_url RLIKE %s";
			
	return $wpdb->get_var($wpdb->prepare($query,1, 'pingback',$url));	
}

function wpi_count_trackback_by($comment)
{ global $wpdb;

	$url = parse_url($comment->comment_author_url,PHP_URL_HOST);
	$query = "SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = %d AND comment_type = %s AND comment_author_url RLIKE %s";
	
	return $wpdb->get_var($wpdb->prepare($query,1, 'trackback',$url));	
}

function wpi_update_post_form($id = false){

	if ( is_post('wpi_maintitle') ) {
		wpi_update_form_meta($id,'maintitle');
	}	

	if ( is_post('wpi_meta_description') ) {
		wpi_update_form_meta($id,'meta_description');
	}

	if ( is_post('wpi_def_meta_description') ) {
		wpi_update_form_meta($id,'def_meta_description');
	}	
	
	if ( is_post('wpi_meta_keywords') ) {
		wpi_update_form_meta($id,'meta_keywords');
	}
	
	if ( is_post('wpi_def_meta_keywords') ) {
		wpi_update_form_meta($id,'def_meta_keywords');
	}	
	
	if ( is_post('wpi_banner_url') ) {
		wpi_update_form_meta($id,'banner_url');
	}
	
	if ( is_post('wpi_banner_repeat') ) {
		wpi_update_form_meta($id,'banner_repeat');
	}

	if ( is_post('wpi_banner_position') ) {
		wpi_update_form_meta($id,'banner_position');
	}
	
	if ( is_post('wpi_banner_height') ) {
		wpi_update_form_meta($id,'banner_height');
	}
	
	if ( is_post('wpi_banner') ) {
		wpi_update_form_meta($id,'banner');
	}	
		
	if ( is_post('wpi_subtitle') ) {
		wpi_update_form_meta($id,'subtitle');
	}	
		
	if ( is_post('wpi_hrating') ){		
		wpi_update_form_meta($id,'hrating');
	}
	
	if ( is_post('wpi_header_content') ){
		wpi_update_form_meta($id,'header_content');
	}
	
	if ( is_post('wpi_footer_content') ){
		wpi_update_form_meta($id,'footer_content');
	}		
}

function wpi_update_usermeta($id=false,$key=false){
	
	if (is_post($key) && $id){
		if ($key == 'user_banner_url'){
			$_POST[$key] = clean_url($_POST[$key]);
		}
		
		update_usermeta($id,$key,$_POST[$key]);	
	}	
}

/**
 * @hook	personal_options_update
 */
function wpi_profile_options_update($id = false)
{
	$id = intval($_POST['user_id']);	
	$key = array('profession','job_title','birthdate','show_banner',
				 'banner_url','banner_repeat','banner_height','banner_position');
				 	
	foreach($key as $index){
		wpi_update_usermeta($id,'user_'.$index);
	}
	
	unset($key,$index);		
}
?>