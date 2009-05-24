<?php
/**
 * $Id$
 * Overwrite WP caption shortcode 
 *  
 * @author 		Avice Devereux <ck+filtered@animepaper.net>
 * @license		http://www.gnu.org/licenses/lgpl.html GNU Lesser GPL   
 * @links 		http://blog.kaizeku.com/  
 * 
 */

if (version_compare($GLOBALS['wp_version'], '2.6', '>='))
{
	add_action('init', 'shortcode_init');
}

function shortcode_init()
{
	add_action('loop_start', 'remove_caption_shortcode', 10);
	add_action('loop_start', 'reg_shortcode', 11);
}


function reg_shortcode()
{
	add_shortcode('caption', 'nwp_caption_shortcode');
	add_shortcode('wp_caption', 'nwp_caption_shortcode');
}

function remove_caption_shortcode()
{
	foreach (array('wp_caption', 'caption') as $tag)
	{
		remove_shortcode($tag);
	}
}

function nwp_caption_shortcode($attr, $content = null)
{

	if (defined('CAPTIONS_OFF'))
	{
		// no check for bool its literally meant off
		return $content;
	}

	extract(shortcode_atts(array('id' => '', 'align' => 'alignnone', 'width' => '',
		'caption' => ''), $attr));

	if (1 > (int)$width || empty($caption))
	{
		return $content;
	}

	if ($id) $id = 'id="' . $id . '" ';

	$output = '<span ' . $id . 'class="wp-caption ' . $align . '" ';
	$output .= 'style="width: ' . (10 + (int)$width) . 'px;display:block">';
	$output .= $content;
	$output .= '<dfn class="wp-caption-text">' . $caption . '</dfn></span>';

	return apply_filters('nwp_caption_shortcode', $output);
}
?>