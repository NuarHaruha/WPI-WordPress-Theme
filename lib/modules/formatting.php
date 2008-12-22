<?php
if (!defined('KAIZEKU')) die(42);
/**
 * $Id$
 * WPI General functions
 */


/**
 * void wpi_get_hatom_title()
 * hAtom (microformats) SEO hierachical heading title 
 * @see is_at()
 * @see wpiSection 
 * @return string raw HTML heading output
 */
function wpi_get_hatom_title()
{
	$sc = is_at();

	$att = array();
	$att['href'] = get_permalink();
	$att['rel'] = 'bookmark archive';
	$att['rev'] = 'vote-for';
	$att['class'] = 'taggedlink url fn';

	if ($sc == wpiSection::ATTACHMENT || $sc == wpiSection::SINGLE || $sc == wpiSection::PAGE)
	{
		$att['class'] .= ' dn';
	}

	$att['hreflang'] = get_hreflang();
	$att['lang'] = $att['hreflang'];
	$att['xml:lang'] = $att['hreflang'];
	$att['title'] = get_the_title();

	$htm = _t('a', $att['title'], $att);

	if ($sc == wpiSection::HOME)
	{
		$htm .= wpi_get_subtitle();
	}

	$htm = _t('h2', $htm, array('class' => 'entry-title item'));

	return apply_filters(wpiFilter::HTOM_TITLE, $htm);

}

/**
 * void wpi_hatom_title()
 * @see wpi_get_hatom_title()
 * @return string raw HTML heading output
 */
function wpi_hatom_title()
{
	echo wpi_get_hatom_title();
}

function wpi_get_subtitle()
{
	if (($subtitle = wpi_get_postmeta('subtitle')) != false)
	{
		return _t('cite', $subtitle, array('class' => 'literal-label subtitle'));
	} 
}

function b64_safe_encode($string)
{

	settype($string, "string");

	$output = base64_encode($string);

	$output = str_replace(array('+', '/', '='), array('-', '_', ''), $output);

	return $output;
}


function b64_safe_decode($string)
{

	settype($string, "string");

	$output = str_replace(array('-', '_'), array('+', '/'), $string);

	$mod4 = (strlen($output) % 4);

	if ($mod4)
	{
		$output .= substr('====', $mod4);
	}

	$output = base64_decode($output);

	return $output;
}

/**
 * HTML tag Helper function 
 * function _t()
 * 
 * @author Avice (chaoskaizer ) Devereux <ck+filtered@animepaper.net> 
 * @param string		$tag html tagname; a p strong
 * @param mixed|string	$content			the content 
 * @param mixed|array	$htmlattributes 	html tag attributes in pair key value
 * 
 * example
 * <code>
 * echo _t('a','website',array('href'=>'http://blog.kaizeku.com'));
 * echo _t('script','',array('src'=>'http://getfirebug.com/releases/lite/1.2/firebug-lite-compressed.js'));
 * </code>
 */
function _t($tag = 'a', $content = false, $htmlattributes = false)
{


	$tag = strtolower($tag);
	$htm = '<' . $tag;

	if (is_array($htmlattributes))
	{

		// add name || id for input
		if ($tag == 'input')
		{
			if (isset($htmlattributes['id']))
			{
				$htmlattributes['name'] = $htmlattributes['id'];
			} elseif (isset($htmlattributes['name']))
			{
				$htmlattributes['id'] = $htmlattributes['name'];
			}

		}

		$esc_prop = array_flip(array('title', 'alt', 'caption', 'content'));

		foreach ($htmlattributes as $name => $txt)
		{

			$name = strtolower($name);

			if ($tag == 'blockquote')
			{
				if ($name == 'cite')
				{
					$txt = urlencode($txt);
				}
			}

			if (isset($esc_prop[$name]))
			{
				$htm .= ' ' . $name . '="' . attribute_escape($txt) . '"';
			} else
			{
				$htm .= ' ' . $name . '="' . $txt . '"';
			}
		}


		unset($htmlattributes, $esc_prop, $name, $txt);
	}

	$typesingle = array_flip(array('img', 'input', 'hr', 'link', 'meta', 'br'));

	/**
	 * HTML Compatibility Guideline 2
	 * Include a space before the trailing /
	 * {@link http://www.w3.org/TR/xhtml1/#C_2 Empty Elements}
	 */
	$htm .= (isset($typesingle[$tag])) ? ' />' : '>' . $content . '</' . $tag . '>';

	/** 
	 * WCAG AAA
	 * newline for common BLOCK elements
	 */
	$newline_tag = array_flip(array('h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'ul',
		'ol', 'dl', 'li', 'script', 'pre', 'code', 'div', 'form', 'table', 'dt', 'dd',
		'blockquote', 'meta', 'link', 'title'));

	if (isset($newline_tag[$tag]))
	{
		$htm .= PHP_EOL;
	}

	unset($typesingle, $newline_tag, $tag, $content);

	return $htm;
}
/**
 * @see _t
 */
function t($tag = 'a', $content = false, $htmlattributes = false)
{
	echo _t($tag, $content, $htmlattributes);
}

function wpi_decode($var)
{
	return call_user_func_array(wpiTheme::DECODE_CONFIG_ENGINE, array($var));
}


function _d($var)
{
	return wpi_decode($var);
}

function rel($url)
{
	return preg_replace('|https?://[^/]+(/.*)|i', '$1', $url);
}

function stab($tab_repeat = false)
{
	return wpiTemplate::spacing($tab_repeat);
}


function romanNumerals($num)
{
	$n = intval($num);
	$res = '';

	/*** roman_numerals array  ***/
	$roman_numerals = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' =>
		100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' =>
		4, 'I' => 1);

	foreach ($roman_numerals as $roman => $number)
	{
		/*** divide to get  matches ***/
		$matches = intval($n / $number);

		/*** assign the roman char * $matches ***/
		$res .= str_repeat($roman, $matches);

		/*** substract from the number ***/
		$n = $n % $number;
	}

	unset($roman_numerals, $roman, $number);

	/*** return the res ***/
	return $res;
}

function romanMonth($month)
{
	$romanized = array('January' => 'IANVARIVS', 'February' => 'FEBRVARIVS', 'March' =>
		'MARTIVS', 'April' => 'APRILIS', 'May' => 'MAIVS', 'June' => 'IVNIVS', 'July' =>
		'IVLIVS', 'August' => 'AVGVSTVS', 'September' => 'SEPTEMBER', 'October' =>
		'OCTOBER', 'November' => 'NOVEMBER', 'December' => 'DECEMBER');

	return ucfirst(strtr($month, $romanized));

}


function get_microid_hash($email = false, $url = false)
{

	if (!$email)
	{
		$email = 'postmaster@' . parse_url($url, PHP_URL_HOST);
	}

	if (get_comment_type() != 'comment')
	{
		$host = parse_url($url, PHP_URL_HOST);
		$email = 'postmaster@' . $host;
		$url = 'http://' . $host;
	}

	return sha1(sha1('mailto:' . $email) + sha1($url));
}


function microid_hash($email = false, $url = false)
{
	echo get_microid_hash($email, $url);
}


function author_microid()
{
	$hash = get_microid_hash(get_the_author_email(), get_the_author_url());
	echo 'microid-' . $hash;
}

function wpi_get_post_author($type = 'display_name')
{
	global $authordata;

	if (!isset($authordata->$type))
	{
		return false;
	}

	$output = $authordata->$type;
	$output = apply_filters('wpi_authordata_' . $type, $output);

	return $output;
}

function wpi_post_author($type = 'display_name')
{
	echo wpi_get_post_author($type);
}


function wpi_author_display_name_filter($author_name)
{
	global $authordata;
	$output = $author_name;
	$name = explode(" ", $author_name);

	// reformat hcard styles
	if (is_array($name))
	{
		if (($cnt = count($name)) >= 0)
		{
			if (isset($name[0]))
			{
				$output = _t('span', $name[0], array('class' => 'nickname'));
			}
			if (isset($name[1]))
			{
				$output = _t('span', $name[0], array('class' => 'given-name')) . ' ';
				$output .= _t('span', $name[1], array('class' => 'family-name'));
			}

		}
	} else
	{
		$output = _t('span', $author_name, array('class' => 'nickname'));
	}

	// links it
	$url = get_author_posts_url($authordata->ID, $authordata->user_nicename);
	$url = apply_filters(wpiFilter::FILTER_LINKS, $url);

	$output = _t('a', $output, array('href' => $url, 'class' => 'url fn dc-creator',
		'rel' => 'colleague foaf.homepage foaf.maker'));

	return $output;
}

function wpi_post_author_html()
{
	$author = ent2ncr(wpi_get_post_author());

	$attrib = array();
	$attrib['class'] = 'author reviewer';

	$htm = _t('cite', $author, array('class' => 'author reviewer', 'title' => $author));


	$attrib = array();
	$attrib['class'] = 'pby';

	if (!wpi_option('post_by_enable'))
	{
		$attrib['class'] .= ' dn';
	}

	$htm = _('by') . ' ' . $htm;

	t('span', $htm, $attrib);
}

/**
 * hAtom Published Date Format 
 * Required  
 */
function wpi_get_hatom_date($timestamp = false, $class = false)
{
	$timestamp = ($timestamp && !empty($timestamp)) ? $timestamp :
		wpi_get_post_timestamp();
	$attribs = array();
	$attribs['class'] = ($class) ? $class : 'published dtreviewed dc-date';
	$attribs['title'] = date('Y-m-dTH:i:s:Z', $timestamp);

	$date = date(get_option('date_format'), $timestamp);

	$output = apply_filters('date', $date);
	$output = _t('abbr', $output, $attribs);
	return $output;

}

function wpi_single_type_title()
{
	$section = is_at();
	$callback = 'single_' . $section . '_title';
	$output = get_the_title();

	$time = get_the_time('F jS, Y');
	
	switch ($section)
	{
		case 'day': $time = get_the_time('F jS, Y'); break;
		case 'month': $time = get_the_time('F, Y'); break;
		case 'year': $time = get_the_time('Y');	break;
	}
	
	$output = sprintf(__('Archive for %s',WPI_META),$time);

	echo $output;
}

function wpi_hatom_date($timestamp = false, $class = false)
{
	echo wpi_get_hatom_date($timestamp, $class);
}

function wpi_get_relative_date($timestamp)
{

	if (wpi_user_func_exists('time_since'))
	{
		$date = abs(strtotime($timestamp) - (60 * 120));
		return (time_since($date) . ' ' . __('ago', WPI_META));
	} else
	{
		return $timestamp;
	}
}

function wpi_relative_date($timestamp)
{
	echo wpi_get_relative_date($timestamp);
}

function wpi_get_postime()
{
	$date = get_the_time(get_option('date_format'));
	return apply_filters(wpiFilter::FILTER_POST_DATE, $date);
}


function wpi_hrating()
{
	echo wpi_get_hrating();
}

function wpi_get_hrating()
{
	$rated = 3;

	if (($is_rated = wpi_get_postmeta('hrating')) != false)
	{
		$rated = ($is_rated) ? $is_rated : $rated;
	}

	$output = _t('span', $rated, array('class' => 'rating rtxt rated-' . $rated));
	$output = _t('div', __('Rated ', WPI_META) . $output, array('class' => 'hrating rtxt'));

	return $output;
}

function wpi_pagination()
{
	$class = apply_filters(wpiFilter::FILTER_POST_PAGINATION_CLASS,
		'pagination cb cf');
?>	
	<div class="<?php echo $class; ?>">
	<?php do_action(wpiFilter::ACTION_POST_PAGINATION); ?>
	</div>	
<?php
}


function wpi_post_link()
{
	next_posts_link(__('&laquo; Older Entries', WPI_META));
	echo '&nbsp;';
	previous_posts_link(__('Newer Entries &raquo;', WPI_META));
}

function wpi_get_range_increment($start, $increment)
{
	$end = ceil(intval(get_option('posts_per_page')));

	$output = array();
	
	if ($start > $end) return false; 
	$end = (($start + 1) == $end) ? $end + 2 : $end + 3;	

	foreach (range($start, $end, $increment) as $i) 	$output[$i] = $i;

	return $output;
}
?>