<?php
if (!defined('KAIZEKU')) { die(42); }
/**
 * Wp-Istalker Action Filters
 * 
 *   
 * @author	Avice Devereux, http://kaizeku.com 
 * @since	17.march.2008
 * @license	MIT License  
 */

final class wpiFilter
{
	const ACTION_FLUSH = 'wpi_flush';
	
	const ACTION_SEND_HEADER = 'wpi_send_http_header';
	
	const ACTION_DEBUG = 'wpi_dump';
	
	const ACTION_DOCUMENT_DTD = 'wpi_document_head';
	
	const ACTION_META_HTTP_EQUIV = 'wpi_meta_http_equiv';
	
	const ACTION_META = 'wpi_meta';
	
	const ACTION_META_LINK = 'wpi_meta_link';
	
	const ACTION_SECTION_PREFIX = 'wpi_template_section_';
	
	const ACTION_SECTION_HEADER = 'wpi_section_head';	
	
	const ACTION_TPL_HEADER = 'wpi_section_header';
	
	const ACTION_TPL_HEADER_AFTER = 'wpi_section_header_after';
	
	const ACTION_THEME_OPTIONS = 'wpi_theme_options';
	
	const ACTION_COPYRIGHT_STATEMENTS = 'wpi_copyright';	
	
	const ACTION_EMBED_CSS = 'wpi_embed_css';
	
	const ACTION_INTERNAL_CSS = 'wpi_internal_css';
	
	const ACTION_POST_PAGINATION = 'wpi_post_pagination';
	
	const ACTION_FOOTER_SCRIPT = 'wpi_footer_script';
	
	const ACTION_WIDGET_SUMMARY_AFTER = 'wpi_widget_single_summary_after';
	
	const FILTER_SECTION_HEADER = 'wpi_fl_header';
	
	const FILTER_BLOG_TITLE = 'wpi_blog_title';
	
	const FILTER_PUBLIC_DTD = 'wpi_document_dtd';
	
	const FILTER_CONTENT_LANGUAGE = 'wpi_document_language';
	
	const FILTER_SECTION_OUTER_CLASS = 'wpi_template_outer_class';
	
	const FILTER_SECTION_INNER_CLASS = 'wpi_template_inner_class';
	
	const FILTER_LINKS = 'wpi_all_links';	
	
	const FILTER_AUTHOR_NAME = 'wpi_html_display_name';
	
	const FILTER_PUBLIC_CSS = 'wpi_public_css';
	
	const NONCE_THEME_OPTIONS = 'wpi-theme-options';
	
	const FILTER_POST_DATE	 = 'wpi_post_date';
	
	const FILTER_COM_DATE	 = 'wpi_comment_date';
	
	const FILTER_POST_PAGINATION_CLASS = 'wpi_pagination_class';
	
	const FILTER_COMMENTS_SELECTOR = 'wpi_comments_class';
	
	const HTOM_TITLE = 'wpi_hatom_title';
	
	const FILTER_BOOKMARKS = 'wpi_bookmarks';
	
	const REM_PLUGINS = 'wpi_remove_plugin_callback';
	
	const RANDOM_COMMENT_AVA = 'wpi_random_comments_avatar';
	
	const EXTRA_JS = 'wpi_extra_js';
	
	private function __construct(){}
	
	private function __clone(){}
} 

?>