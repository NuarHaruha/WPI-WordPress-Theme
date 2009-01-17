<?php
/**
 * WP-iStalker Chrome Milk 
 * Gravatar
 * 
 * @package	WordPress
 * @subpackage	wp-istalker-chrome
 * 
 * @author	Avice (ChaosKaizer) De'vereux <ck+wp-istalker-chrome@istalker.net>
 * @copyright 	2006 - 2009 Avice De'vereux
 * @license 	http://www.opensource.org/licenses/mit-license.php MIT License
 * @version 	CVS: $Id$
 * @since 	1.5
 */

/**
 * wpiGravatar
 * Gravatar for wordpress using CSS 
 * 
 * @since 1.5
 * @link http://en.gravatar.com Gravatar
 * @access public
 * @todo AJAX mode
 */
class wpiGravatar
{
	
	/**
	 * wpiGravatar::T_COMMENT_AUTHOR
	 * Comments prefix ID
	 * 
	 * @var string
	 * @since 1.6
	 * @access public  
	 */
	const T_COMMENT_AUTHOR	= 'c-';


	/**
	 * wpiGravatar::T_POST_AUTHOR
	 * Posts prefix ID
	 * 
	 * @var string
	 * @since 1.6
	 * @access public  
	 */	
	const T_POST_AUTHOR 	= 'p-';	
	
	
	/**
	 * wpiGravatar::GRAVATAR_URL
	 * Gravatar Avatar URL
	 * 
	 * @var string
	 * @since 1.6
	 * @access public  
	 */		
	const GRAVATAR_URL		= 'http://www.gravatar.com/avatar/';


	/**
	 * wpiGravatar::USER_EMAIL
	 * user email comments field name
	 * 
	 * @deprecated
	 * @var string
	 * @since 1.5
	 * @access public  
	 */		
	const USER_EMAIL		= 'user_email';


	/**
	 * wpiGravatar::INVALID_EMAIL
	 * 
	 * 
	 * @deprecated
	 * @var string
	 * @since 1.5
	 * @access public  
	 */		
	const INVALID_EMAIL		= 'invalid-email';


	/**
	 * wpiGravatar::TPL
	 * css selector template
	 * 
	 * @deprecated
	 * @var string
	 * @since 1.5
	 * @access public  
	 */		
	const TPL = "%1s{background-image:url('%2s')}";
	

	/**
	 * void wpiGravatar::__construct()
	 * class constructor
	 * 
	 * @since 1.5
	 * @access public  
	 */
	public function __construct(){}
	
	
	/**
	 * wpiGravatar::filterCSS()
	 * class constructor
	 * 
	 * @since 1.5
	 * @access public  
	 * @param string $section WP section name {@see is_at()}	  
	 */	
	public function filterCSS($section)
	{
		switch ($section){
			case wpiSection::HOME:
			case wpiSection::FRONTPAGE:
			case wpiSection::SEARCH:
				if (wpi_option('home_avatar')) $this->internalCSS('authorAvatar');
							
			break;
			case wpiSection::PAGE:
			case wpiSection::SINGLE:
			case wpiSection::ATTACHMENT:
				$this->internalCSS('commentsAvatar');
			break;
		}		
	}
	
	
	/**
	 * wpiGravatar::internalCSS()
	 * Queue methods to template hook, wrapper for add_action()
	 * 
	 * @uses add_action 
	 * @see	wpiFilter::ACTION_GRAVATAR_CSS filters (hook) name
	 * @since 1.6
	 * @access public  
	 * @param string $methods wpiGravatar methods name	  
	 */	
	public function internalCSS($methods){
		add_action(wpiFilter::ACTION_GRAVATAR_CSS, array($this, $methods), 1);			
	}	


	/**
	 * void wpiGravatar::commentsAvatar()
	 * Gravatar Internal CSS for WP Comments section
	 * 
	 * @uses $post WP_Query object
	 * @since 1.5
	 * @access public    
	 */	
	public function commentsAvatar()
	{	global $post;
		
		$gravatar = array();
	
		$pid = absint($post->ID);		
		$rating 	= get_option('avatar_rating');
		$output		= PHP_EOL;	
					
		// set post author first
		if ( ($author = get_userdata( (int) $post->post_author) ) != false){
			$hash = md5($author->user_email);
			$url = self::getURL($hash, 50, $rating);
			
				if (wpi_option('cache_avatar')){
					$url = rel($url).'.ava';
				}
			
			$selector = PHP_T.'.'.self::T_POST_AUTHOR.$hash;		
			$output .= sprintf(self::TPL, $selector, $url).PHP_EOL;
		}
		
		$comments = wpi_get_comments($pid);		
						
		if ( 'open' == wpi_get_post_single_field('comment_status',$pid) || has_count($comments) ) {		  	
		  
		  if ( $comments ){					
				foreach($comments as $comment){
					$gravatars[]  = md5($comment->comment_author_email);	
				}
										
				unset($comments);
				
				if (has_count($gravatars)){
					// removed duplicate hash;
					$gravatars = array_unique($gravatars);					
					$size 		= 42;					
					
					foreach($gravatars as $hash){			
						$url 	  	= self::getURL($hash,$size,$rating);
						
							if (wpi_option('cache_avatar')){
								$url = rel($url).'.ava';
							}
										
						$attribute 	= 'background-image:url(\''.$url.'\')';
						
						// build css
						$output .= "\t".'.'.self::T_COMMENT_AUTHOR.$hash.'{'.$attribute.'}'.PHP_EOL;			
					}
							
				}
			}
			
			echo $output;
			unset($gravatars, $output);				 		  
		}
	}
	

	/**
	 * void wpiGravatar::authorAvatar()
	 * Gravatar Internal CSS for Home and Frontpage section
	 * 
	 * @since 1.5
	 * @access public    
	 * @todo customize avatar size options
	 */	
	public function authorAvatar()
	{
		
		$userdata = wpi_get_author_data(self::USER_EMAIL);		
		$output = array();
		
		foreach($userdata as $user){
			if (!empty($user->user_email) ){
				$output[] = (string) $user->user_email;
			} else {
				$output[] = self::INVALID_EMAIL;
			}
		}		
		
		unset($userdata);
			
		$output = array_unique($output);
		
		if (has_count($output)){				
				
			$output 	= array_map('md5',$output);
			$css 		= '';
			
			// gravatar settings
			$size 		= 36;
			$rating 	= get_option('avatar_rating');
			
			foreach($output as $hash)
			{			
				$url 	  	= self::getURL($hash,$size,$rating);
				
				if (wpi_option('cache_avatar')){
					$url = rel($url).'.ava';
				}
				
				$attribute 	= 'background-image:url(\''.$url.'\')';
				
				// build css
				$css .= "\t".'.'.self::T_POST_AUTHOR.$hash.'{'.$attribute.'}'.PHP_EOL;			
			}				
			
				
		} else {
			return false;
		}	
		
		unset($output);
		
		echo PHP_EOL.$css;
	}
	

	/**
	 * void wpiGravatar::authorGID()
	 * Posts author gravatar hash (class selector)
	 * used as CSS referrence 
	 * @since 1.6
	 * @access public    
	 */		
	public static function authorGID()
	{
		echo self::T_POST_AUTHOR.md5(get_the_author_email() );
	}
	
	
	/**
	 * static wpiGravatar::commentGID()
	 * Comments author gravatar hash (class selector)
	 * 
	 * @since 1.6
	 * @access public 
	 * @param mixed|object $comment WP_Query comments object   
	 */		
	public static function commentGID($comment = false)
	{
		if (!$comment){
			global $comment;
		}
		
		echo self::T_COMMENT_AUTHOR.md5($comment->comment_author_email );
	}
	
	/**
	 * static wpiGravatar::getURL()
	 * Gravatar URL
	 * 
	 * @since 1.6
	 * @access public
	 * @param string $h gravatar email md5 hash
	 * @param mixed|int $s gravatar size default is 64
	 * @param string $r gravatar ratings (G | PG | R | X )    
	 */			
	public static function getURL($h, $s=64, $r='G')
	{	
		$d = get_option('avatar_default');
		
		if (wpi_option('cache_avatar')){
		
			wpiGravatar::is_cached($h,$s,$r,$d);
			$output = WPI_THEME_URL.join('-',array($h,$s,$r,$d));
			
	 	} else {
	 		
	 		$output = self::GRAVATAR_URL.$h.'png?s='.$s.'&r='.$r.'&d='.$d;
	 	}
	 	
	 	return $output;
			
	}
	

	/**
	 * static void wpiGravatar::avatarURL()
	 * Relative Post author gravatar URL
	 * 
	 * @since 1.6
	 * @access public    
	 */		
	public static function avatarURL()
	{
		echo rel(WPI_THEME_URL.md5(get_the_author_email() ).'.ava');
	}


	/**
	 * static wpiGravatar::is_cached()	 
	 * 
	 * @since 1.6
	 * @access public
	 * @link http://en.gravatar.com/site/implement/url How the URL is constructed
	 * @param string $h gravatar email md5 hash
	 * @param mixed|int $s gravatar size default is 64
	 * @param string $r gravatar ratings (G | PG | R | X ) 
	 * @param string $d default avatar replacements  (default| identicon | monsterid | wavatar)    
	 */		
	public static function is_cached($h, $s=64, $r='G', $d)
	{		
		$url = self::GRAVATAR_URL.$h.'png?s='.$s.'&r='.$r.'&d='.$d;; 
		
		$file = WPI_CACHE_AVATAR_DIR.DIRSEP.md5($url).'.png';

		if (!file_exists($file)){
			cURLdownload($url, $file);
		} else {
			return true;
		}		
	}
}	
?>