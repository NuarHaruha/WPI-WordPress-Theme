<?php
if ( !defined('KAIZEKU') ) exit(42);
/**
 * WP-iStalker Chrome Milk 
 * Template Pathway
 * 
 * @package	WordPress
 * @subpackage	wp-istalker-chrome
 * 
 * @category	Template
 * @author	Avice (ChaosKaizer) De'vereux <ck+wp-istalker-chrome@istalker.net>
 * @copyright 	2006 - 2009 Avice De'vereux
 * @license 	http://www.opensource.org/licenses/mit-license.php MIT License
 * @version 	CVS:$Id$
 * @since 	1.2
 */

/**
 * wpiPathway
 * @since 1.2
 */
 	

class wpiPathway
{

	const PSEP = '&#8250;';

	const RSS_REL = 'alternate noarchive robots-noindex';

	const CAT_LINK_CLASS = 'cat-link tags dc-subject';

	const DN_ARROW = 'rn icn-rs darr-';

	public function __construct(){}


	public function build()
	{
		global $wp_query;

		switch (is_at())
		{
			case wpiSection::HOME:
			case wpiSection::FRONTPAGE:
				$pathway = $this->getHome();
				break;

			case wpiSection::SINGLE:
				$pathway = $this->getSingle();
				break;

			case wpiSection::PAGE:
				$pathway = $this->getPage();
				break;

			case wpiSection::CATEGORY:
				$pathway = $this->getCategory();
				break;

			case wpiSection::TAXONOMY:
				$pathway = $this->getTag();
				break;

			case wpiSection::SEARCH:
				$pathway = $this->getSearch();
				break;

			case wpiSection::YEAR:
				$pathway = $this->getDate(wpiSection::YEAR);
				break;

			case wpiSection::MONTH:
				$pathway = $this->getDate(wpiSection::MONTH);
				break;

			case wpiSection::DAY:
				$pathway = $this->getDate(wpiSection::DAY);
				break;
			case wpiSection::AUTHOR:
				$pathway = $this->getAuthor();
				break;

			case wpiSection::PAGE404:
				$pathway = $this->getLost();
				break;
			case wpiSection::ATTACHMENT:
				$pathway = $this->getAttachment();
				break;
		}

		$htm = PHP_EOL;

		if (!has_count($pathway))
			return;

		foreach ($pathway as $key => $value)
		{
			$att = $value[1];
			$att['href'] = apply_filters(wpiFilter::FILTER_LINKS, $att['href']);

			if ($key == 'home')
			{
				$att['href'] = trailingslashit($att['href']);
			}

			// append tooltips  class if there is title
			if (isset($att['title']))
			{
				$att['class'] = $att['class'] . ' ttip';				
			}

			$links = _t('a', $value[0], $att);
			$htm .= stab(4) . _t('li', $links);

			/**
			 * Add trailing slash on home url 
			 * correct status header 	
			 * @bugs fixes 301 redirect
			 * 6/27/2008 7:57:01 PM $ck 
			 */

			if ($key != 'first' && $key != 'last')
			{
				$htm .= stab(4) . _t('li', _t('span', '&#8250;'), array('class' => 'sep'));
			}
		}

		unset($pathway);

		$htm = _t('ul', $htm . stab(3), array('class' => 'r cfl cf'));


		return $htm;
	}


	public static function metaHome()
	{
		$text = wpi_option('pathway_frontpage_text');
		$text = (!empty($text)) ? $text : __('Frontpage', WPI_META);
		return array($text, array('href' => '#main',
			// skip to content
			'title' => __('You are here | ' . WPI_BLOG_NAME . '&#39;s Frontpage', WPI_META),
			'rel' => 'home robots-anchortext','class'=>'scroll-to'));
	}


	/**
	 *  home & frontpage 
	 */
	public function getHome()
	{
		global $wp_query;

		$pathway = array();

		//  rss
		$pathway['first'] = array('RSS', array('href' => get_bloginfo('rss2_url'),
			'type' => 'application/rss+xml', 'title' => __(WPI_BLOG_NAME .
			' | RSS 2 Syndication', WPI_META), 'hreflang' => get_hreflang(), 'rel' => self::
			RSS_REL, 'class' => 'rtxt rss16', 'rev' => 'feed:rss2'));

		// home
		$home = self::metaHome();


		if ($wp_query->is_paged)
		{
			$pathway['home'] = $home;
			$att = $pathway['home'][1];

			$att['title'] = __(WPI_BLOG_NAME . '&#39;s Frontpage', WPI_META);
			$att['href'] = WPI_HOME_URL;

			$pathway['home'][1] = $att;

			// paged

			$page_no = intval(get_query_var('paged'));
			$max_page = $wp_query->max_num_pages;

			$title = sprintf(__('Page %1$d of %2$d', WPI_META), $page_no, $max_page);

			$pathway['last'] = array($title, array('class' => self::DN_ARROW . ' scroll-to',
				'href' => get_pagenum_link($page_no) . '#main', 'title' => $title . __(' | Skip to Content',
				WPI_META)));

		} else
		{
			$home[1]['class'] = self::DN_ARROW;
			$pathway['last'] = $home;
		}

		return $pathway;
	}

	/**
	 *  Single 'Articles view'
	 */
	public function getSingle()
	{
		global $wp_query;

		$wp_title = trim(wp_title('', false));
		$title = string_len($wp_title, 90);
		$pathway = array();
		//  rss
		$pathway['first'] = array('Comments RSS', array('href' =>
			get_post_comments_feed_link(), 'type' => 'application/rss+xml', 'title' => __($title .
			' | Comments Feed', WPI_META), 'hreflang' => get_hreflang(), 'rel' => self::
			RSS_REL, 'class' => 'rtxt rss16', 'rev' => 'feed:rss2'));

		// home
		$pathway['home'] = $this->getFrontpage();

		// category
		$cat = wpi_get_first_cat_obj();

		if (is_object($cat))
		{

			$pathway['archive'] = array(wp_specialchars($cat->name), array('href' =>
				get_category_link($cat->term_id), 'title' => __('Topic | Archive for ' . $cat->
				name, WPI_META), 'rel' => 'tags dc-subject', 'class' => 'cat-link', 'rev' =>
				'archive:term'));

		}

		unset($cat);

		//  articles
		$articles = array($title, array('href' => get_permalink() . '#iscontent',
			'title' => __('Skip to content | ' . self::PSEP . ' ' . $wp_title . ' ',
			WPI_META), 'hreflang' => get_hreflang(), 'class' => self::DN_ARROW, 'rel' =>
			'robots-anchortext', ));

		if (($page_no = wpi_get_post_current_paged()) != false)
		{

			$pathway['post'] = $articles;
			$pathway['post'][1]['href'] = str_rem('#content', $pathway['post'][1]['href']);
			$pathway['post'][1]['title'] = str_replace('Skip to content ', 'Back to ', $pathway['post'][1]['title']);

			$title = sprintf(__('Page %d', WPI_META), $page_no);

			$pathway['last'] = array($title, array('href' => self_uri() . '#iscontent',
				'title' => __('Skip to Content | ' . $title, WPI_META)));

		} else
		{

			$pathway['last'] = $articles;
		}


		return $pathway;

	}


	public function getPage()
	{
		global $wp_query;

		$pid = $wp_query->post->ID;

		$wp_title = trim(wp_title('', false));
		$title = string_len($wp_title, 90);
		$pathway = array();
		//  rss
		$pathway['first'] = array('Comments RSS', array('href' => get_permalink($pid) .
			'feed/', 'type' => 'application/rss+xml', 'title' => __($title .
			' | Comments RSS 2 Syndication', WPI_META), 'hreflang' => get_hreflang(), 'rel' =>
			self::RSS_REL, 'class' => 'rtxt rss16', 'rev' => 'feed:rss2'));

		// home
		$pathway['home'] = $this->getFrontpage();

		// is this child page?
		if (($has_parent = $wp_query->post->post_parent) != false)
		{
			$post_page = &get_post($has_parent);
			$pathway['parent'] = array($post_page->post_title, array('href' => get_permalink
				($has_parent), 'title' => __($post_page->post_title . ' Index page', WPI_META),
				'rev' => 'page:parent', 'rel' => 'previous'));

			unset($post_page);
		}

		// page

		$pathway['last'] = array($title, array('href' => get_permalink() .
			'#content-top', 'title' => __('Skip to content | ' . self::PSEP . ' ' . $wp_title .
			' ', WPI_META), 'hreflang' => get_hreflang(), 'class' => self::DN_ARROW, 'rel' =>
			'robots-anchortext', ));

		return $pathway;
	}


	public function getCategory()
	{
		$cid = (int)get_query_var('cat');
		$cat = get_category($cid);

		$wp_title = trim(wp_title('', false));
		$title = string_len($wp_title, 90);
		$pathway = array();

		//  rss
		$pathway['first'] = array('Category Feed', array('href' =>
			get_category_feed_link($cid), 'type' => 'application/rss+xml', 'title' => __($title .
			' | Subscribe to this feed', WPI_META), 'hreflang' => get_hreflang(), 'rel' =>
			self::RSS_REL, 'class' => 'rtxt rss16', 'rev' => 'feed:rss2'));


		// home
		$pathway['home'] = $this->getFrontpage();

		// Topics
		$cat_base = str_rem('/', get_option('category_base'));

		if ($cat_base)
		{
			$cat_base = ucfirst($cat_base);
		} else
		{
			$cat_base = __('Topics', WPI_META);
		}

		$pathway['topics'] = array($cat_base, array('href' => '#content-top', 'title' =>
			$cat_base . ' | Skip to content '));

		// Category

		if (is_object($cat))
		{

			$pcid = $cat->category_parent;
			if ($pcid)
			{
				$pname = get_cat_name($pcid);
				$pathway['parent'] = array($pname, array('href' => get_category_link($pcid),
					'title' => $cat_base . ' | ' . $pname, 'class' => $pname, 'rev' =>
					'site:archive'));
			}

			$pathway['last'] = array($cat->name, array('href' => '#content', 'title' => $cat_base .
				' | ' . $cat->name, 'class' => self::CAT_LINK_CLASS . ' ' . self::DN_ARROW,
				'rev' => 'site:archive'));
		}

		unset($cat);

		return $pathway;
	}


	public function getTag()
	{	global $wp_query;
	
		$term = $wp_query->get_queried_object();
		
		$pathway = array();

		$tid = (int) $term->term_id;
		
		$tag = get_category($tid);

		$title = single_cat_title('', false);

		//  rss
		$pathway['first'] = array($title . ' taxonomy feeds', array('href' =>
			get_tag_feed_link($tid), 'type' => 'application/rss+xml', 'title' => __($title .
			' | Subscribe to this feed', WPI_META), 'hreflang' => get_hreflang(), 'rel' =>
			self::RSS_REL, 'class' => 'rtxt rss16', 'rev' => 'feed:rss2'));

		// home
		$pathway['home'] = $this->getFrontpage();

		//tag_base
		$tag_base = str_rem('/', get_option('tag_base'));

		if ($tag_base)
		{
			$tag_base = ucfirst($tag_base);
		} else
		{
			$tag_base = __('Taxonomy', WPI_META);
		}

		$pathway['archive'] = array($tag_base, array('href' => '#content-top', 'title' =>
			__($tag_base . ' | Skip to content', WPI_META)));

		$tid = get_query_var('tag_id');

		$tag = get_term($tid, 'post_tag');

		$pathway['last'] = array($tag->name, array('href' => '#content-top', 'title' =>
			__('Skip to content | ' . $tag->name, WPI_META), 'class' => self::
			CAT_LINK_CLASS . ' ' . self::DN_ARROW, 'rev' => 'site:archive'));

		unset($tag,$term);

		return $pathway;

	}

	public function getSearch()
	{
		$pathway = array();
		$terms = get_search_query();
		//  rss
		$pathway['first'] = array($title . ' taxonomy feeds', array('href' =>
			get_search_feed_link($terms), 'type' => 'application/rss+xml', 'title' => __($terms .
			' | Subscribe to this search feed', WPI_META), 'hreflang' => get_hreflang(),
			'rel' => self::RSS_REL, 'class' => 'rtxt rss16', 'rev' => 'feed:rss2'));

		// home
		$pathway['home'] = $this->getFrontpage();

		$pathway['archive'] = array(__('Search', WPI_META), array('href' =>
			'#content-top', 'title' => __('Search | Skip to content', WPI_META)));

		$pathway['last'] = array($terms, array('href' => '#content-top', 'title' => __('Skip to content | ' .
			$terms, WPI_META), 'class' => self::CAT_LINK_CLASS . ' ' . self::DN_ARROW, 'rev' =>
			'site:archive'));
		return $pathway;
	}

	public function getFrontpage()
	{
		$pathway = array();
		// home
		$pathway = self::metaHome();

		$att = $pathway[1];

		$att['title'] = sprintf(__('%s | Return back to Frontpage', WPI_META),WPI_BLOG_NAME);
		$att['href'] = WPI_HOME_URL;
		$att['rel'] = 'home previous';

		$pathway[1] = $att;

		return $pathway;
	}

	public function getDate($type)
	{

		$pathway = array();
		$day = get_the_time('j');
		$month = get_the_time('m');
		$year = get_the_time('Y');

		switch ($type)
		{
			case wpiSection::YEAR:
				$title = get_the_time('Y');
				//  rss
				$pathway['first'] = array($title . ' rss', array('href' => get_year_link($year) .
					'feed/', 'type' => 'application/rss+xml', 'title' => __($title .
					' archive  | Subscribe to this feed', WPI_META), 'hreflang' => get_hreflang(),
					'rel' => self::RSS_REL, 'class' => 'rtxt rss16', 'rev' => 'feed:rss2'));

				// home
				$pathway['home'] = $this->getFrontpage();

				// Topics
				$att = array();
				$att['href'] = '#content-top';
				$att['title'] = $title . ' Archive | Skip to content';
				$pathway['topics'] = array(__('Archive', WPI_META), $att);

				// Year
				$att = array();
				$att['href'] = get_year_link($year);
				$att['title'] = 'Archive | for &#187; ' . $title;
				$att['class'] = 'foaf-primaryTopic tags dc-subject ' . self::DN_ARROW;
				$att['rev'] = 'site:year-' . $year;
				$att['hreflang'] = get_hreflang();
				$pathway['last'] = array($year, $att);
				break;

			case wpiSection::MONTH:

				$title = get_the_time('F, Y');
				//  rss
				$pathway['first'] = array($title . ' rss', array('href' => get_month_link($year,
					$month) . 'feed/', 'type' => 'application/rss+xml', 'title' => __($title .
					' archive  | Subscribe to this feed', WPI_META), 'hreflang' => get_hreflang(),
					'rel' => self::RSS_REL, 'class' => 'rtxt rss16', 'rev' => 'feed:rss2'));

				// home
				$pathway['home'] = $this->getFrontpage();

				// Topics
				$att = array();
				$att['href'] = '#content-top';
				$att['title'] = 'Archive | Skip to content';
				$pathway['topics'] = array(__('Archive', WPI_META), $att);

				// Year
				$att = array();
				$att['href'] = get_year_link($year);
				$att['title'] = 'Archive | for the year ' . $year;
				$pathway['year'] = array($year, $att);

				// Month
				$att = array();
				$att['href'] = get_month_link($year, $month);
				$att['title'] = 'Archive | for ' . $title;
				$att['class'] = 'foaf-primaryTopic tags dc-subject ' . self::DN_ARROW;
				$att['rev'] = 'site:month-' . $month;
				$att['hreflang'] = get_hreflang();
				$pathway['last'] = array(get_the_time('F'), $att);
				break;
			case wpiSection::DAY:
				$title = get_the_time('F jS, Y');

				//  rss
				$pathway['first'] = array($title . ' rss', array('href' => get_day_link($year, $month,
					$day) . 'feed/', 'type' => 'application/rss+xml', 'title' => __($title .
					' archive  | Subscribe to this feed', WPI_META), 'hreflang' => get_hreflang(),
					'rel' => self::RSS_REL, 'class' => 'rtxt rss16', 'rev' => 'feed:rss2'));

				// home
				$pathway['home'] = $this->getFrontpage();

				// Topics
				$att = array();
				$att['href'] = '#content';
				$att['title'] = 'Archive';
				$pathway['topics'] = array(__('Archive', WPI_META), $att);

				// Year
				$att = array();
				$att['href'] = get_year_link($year);
				$att['title'] = 'Archive for the year ' . $year;
				$pathway['year'] = array($year, $att);

				// Month
				$att = array();
				$att['href'] = get_month_link($year, $month);
				$att['title'] = 'Archive for the Month of ' . get_the_time('F');
				;
				$pathway['month'] = array($month, $att);

				// day
				$att = array();
				$att['href'] = '#content';
				$att['title'] = 'Archive for &#187; ' . $title;
				$att['class'] = 'foaf-primaryTopic tags dc-subject ' . self::DN_ARROW;
				$att['rev'] = 'site:day-' . $day;
				$att['hreflang'] = get_hreflang();

				$pathway['last'] = array(get_the_time('jS'), $att);
				break;

		}

		return $pathway;
	}

	public function getAuthor()
	{
		$pathway = array();
		$user = wpi_get_current_author();
		//  rss
		$pathway['first'] = array($user->display_name . ' feeds', array('href' =>
			get_author_feed_link($user->ID), 'type' => 'application/rss+xml', 'title' => __
			($user->display_name . ' | Subscribe to this author feed', WPI_META), 'hreflang' =>
			get_hreflang(), 'rel' => self::RSS_REL, 'class' => 'rtxt rss16', 'rev' =>
			'feed:rss2'));

		// home
		$pathway['home'] = $this->getFrontpage();

		$pathway['archive'] = array(__('Author', WPI_META), array('href' =>
			'#content-top', 'title' => __('Author | Skip to content', WPI_META)));

		$pathway['last'] = array($user->display_name, array('href' => '#content-top',
			'class' => self::DN_ARROW, 'title' => __('Author | Skip to content', WPI_META)));

		return $pathway;
	}

	/* +fav methods, inject some humours here */
	public function getLost()
	{
		//$this->drool;
		$pathway = array();
		//  rss
		$pathway['first'] = array( sprintf(__('%s&#39;s Feeds',WPI_META),WPI_BLOG_NAME), 
		array(	'href' => get_feed_link(), 
				'type' => 'application/rss+xml', 
				'title' => sprintf(__('%s | Subscribe to this feed', WPI_META),WPI_BLOG_NAME), 
				'hreflang' => get_hreflang(), 
				'rel' => self::RSS_REL, 'class' => 'rtxt rss16'));

		// home
		$pathway['home'] = $this->getFrontpage();

		global $wp_query;

		$pathway['last'] = array('404 Not Found', array('href' => '#content-top',
			'class' => self::DN_ARROW));

		return $pathway;
	}

	public function getAttachment()
	{
		global $wp_query;

		$title = string_len(trim(wp_title('', false)), 90);
		$pid = $wp_query->post->ID;
		$ppid = $wp_query->post->post_parent;
		$pathway = array();

		//  rss
		$pathway['first'] = array(
			sprintf(__('%s comments feeds',WPI_META),$title), 
			array(	'href' => get_post_comments_feed_link($pid), 
					'type' => 'application/rss+xml', 
					'title' => sprintf(__('%s | Subscribe to this comments feed', WPI_META),$title), 
					'hreflang' => get_hreflang(), 
					'rel' => self::RSS_REL, 'class' => 'rtxt rss16'));


		// home
		$pathway['home'] = $this->getFrontpage();

		// parent
		$att = array();
		$att['href'] = get_permalink($ppid);
		$att['title'] = 'Parent | ' . get_the_title($ppid);

		$att['class'] = 'parent-link dc-subject';
		$att['hreflang'] = get_hreflang();
		$pathway['parent'] = array(get_the_title($ppid), $att);

		//  attachments
		$att = array();
		$att['href'] = get_permalink($pid);
		$att['title'] = __('Attachment | permalink', WPI_META);

		$pathway['attachment'] = array('Attachment', $att);

		//  attachments-pid
		$att = array();
		$att['href'] = '#content-top';
		$att['title'] = sprintf(__('%s | Skip to content',WPI_META),$title);
		$att['hreflang'] = get_hreflang();
		$att['class'] = 'last-items scrollto ' . self::DN_ARROW;

		if (($pageno = wpi_get_post_current_paged()) != false)
		{
			$pathway['post'] = array($title, $att);
			$title = 'on page ' . $pageno;

			$att = array();
			$att['href'] = wpi_paged_url($pageno);
			$att['title'] = $title;
		}

		$pathway['last'] = array($title, $att);
		return $pathway;
	}
}
?>