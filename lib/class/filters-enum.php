<?php
if (!defined('KAIZEKU')) { die(42); }
/**
 * $Id$
 * WPI filters
 * 
 * @since 1.6
 * @author Avice D <ck+filtered@animepaper.net> 
 */
final class wpiFilter
{
	const ACTION_FLUSH = 40000;
	
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
	
	const ACTION_EMBED_CSS = 45000;
	
	const ACTION_INTERNAL_CSS = 45001;
	
	const ACTION_GRAVATAR_CSS = 45002;
	
	const ACTION_POST_PAGINATION = 'wpi_post_pagination';
	
	const ACTION_FOOTER_SCRIPT = 'wpi_footer_script';
	
	const ACTION_WIDGET_SUMMARY_AFTER = 'wpi_widget_single_summary_after';
	
	const FILTER_SECTION_HEADER = 'wpi_fl_header';
	
	const FILTER_BLOG_TITLE = 'wpi_blog_title';
	
	const FILTER_PUBLIC_DTD = 'wpi_document_dtd';
	
	const FILTER_CONTENT_LANGUAGE = 'wpi_document_language';
	
	const FILTER_SECTION_OUTER_CLASS = 'wpi_template_outer_class';
	
	const FILTER_SECTION_INNER_CLASS = 'wpi_template_inner_class';
	
	const FILTER_LINKS = 40100;	
	
	const FILTER_WEBFONT_LINKS = 40101;
	
	const FILTER_AUTHOR_NAME = 'wpi_html_display_name';
	
	const FILTER_PUBLIC_CSS = 45003;
	
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
	
	const FILTER_ELM_ID = 'wpi_element_id_';
	
	const ACTION_BANNER_CONTENT = 'wpi_banner_content';
	
	const FILTER_HEAD_PROFILE = 'wpi_head_profile';
	
	const FILTER_ENTRY_CONTENT_CLASS = 'wpi_entry_content_class';
	
	const ACTION_BEFORE_CONTENT_PREFIX = 'wpi_before_content_';
	
	const ACTION_AFTER_CONTENT_PREFIX = 'wpi_after_content_';
	
	const ACTION_INSIDE_CONTENT_BOTTOM_BAR_PREFIX = 'wpi_content_bar_';	
		
	const FILTER_META_DESCRIPTION = 42000;
	
	const FILTER_META_KEYWORDS = 42001;
	
	const FILTER_CUSTOM_HEAD_CONTENT = 42002;
	
	const FILTER_CUSTOM_FOOTER_CONTENT = 42003;
	
	const FILTER_ROOT_CLASS_SELECTOR = 42004;
	
	const ACTION_LIST_COMMENT_FORM = 45004; 
	
	private function __construct(){}
	
	private function __clone(){}
}
?>