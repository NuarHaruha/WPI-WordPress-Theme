<?php
/**
 * $Id: gravatar.php, rev 0004 10/4/2008 11:29:54 AM ChaosKaizer $
 * 
 * @author		Avice De'vereux (ChaosKaizer) <ck+nospam@animepaper.net>
 * @link		http://blog.kaizeku.com Kaizeku Ban
 * 
 */
class wpiGravatar
{
	const T_COMMENT_AUTHOR	= 'c-';
	
	const T_POST_AUTHOR 	= 'p-';	
	
	const GRAVATAR_URL		= 'http://www.gravatar.com/avatar/';
	
	const USER_EMAIL		= 'user_email';
	
	const INVALID_EMAIL		= 'invalid-email';
	
	const TPL = "%1s{background-image:url('%2s')}";
	
	public function __construct()
	{
		
	}
	
	public function filterCSS($section)
	{
		switch ($section){
			case wpiSection::HOME:
			case wpiSection::FRONTPAGE:
			case wpiSection::SEARCH:
				$this->internalCSS('authorAvatar');			
			break;
			case wpiSection::PAGE:
			case wpiSection::SINGLE:
			case wpiSection::ATTACHMENT:
				$this->internalCSS('commentsAvatar');
			break;
		}
		
	}
	
	
	public function internalCSS($methods){
		add_action(wpiFilter::ACTION_GRAVATAR_CSS,array($this,$methods),1);			
	}
	/**
	 * active at single, page & attachments page 
	 */
	public function commentsAvatar()
	{	global $post;
		
		$gravatar = array();
	
		$pid = absint($post->ID);		
		$rating 	= get_option('avatar_rating');
		$output		= PHP_EOL;	
					
		// set post author first
		if ( ($author = get_userdata( (int) $post->post_author )) != false){
			$hash = md5($author->user_email);
			$url  = self::getURL($hash,50,$rating);
			
				if (wpi_option('cache_css')){
					$url = rel($url).'.ava';
				}
			
			$selector = PHP_T.'.'.self::T_POST_AUTHOR.$hash;		
			$output .= sprintf(self::TPL,$selector,$url).PHP_EOL;
		}
		// is comment or ping enabled ?				
		if ( 'open' == wpi_get_post_single_field('comment_status',$pid) 
		  || 'open' == wpi_get_post_single_field('ping_status',$pid)) {
		  	
		  $comments = wpi_get_comments($pid);
		  
		  if ( $comments ){					
				foreach($comments as $comment){
					$gravatars[]  = md5($comment->comment_author_email);	
				}
										
				unset($comments);
				
				if (has_count($gravatars)){
					// removed duplicate hash;
					$gravatars = array_unique($gravatars);					
					$size 		= 80;					
					
					foreach($gravatars as $hash){			
						$url 	  	= self::getURL($hash,$size,$rating);
						
							if (wpi_option('cache_css')){
								$url = rel($url).'.ava';
							}
										
						$attribute 	= 'background-image:url(\''.$url.'\')';
						
						// build css
						$output .= "\t".'.'.self::T_COMMENT_AUTHOR.$hash.'{'.$attribute.'}'.PHP_EOL;			
					}
							
				}
			}
			
			echo $output;
			unset($output);				 		  
		}
	}
	
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
				
				if (wpi_option('cache_css')){
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
	
	public static function authorGID()
	{
		echo self::T_POST_AUTHOR.md5(get_the_author_email() );
	}
	
	public static function commentGID($comment=false)
	{
		if (!$comment){
			global $comment;
		}
		
		echo self::T_COMMENT_AUTHOR.md5($comment->comment_author_email );
	}
		
	public static function getURL($h,$s=64,$r='G')
	{	
		$d = get_option('avatar_default');
		
		if (wpi_option('cache_css')){
		
			wpiGravatar::is_cached($h,$s,$r,$d);
			$output = WPI_THEME_URL.join('-',array($h,$s,$r,$d));
			
	 	} else {
	 		$url = sprintf('http://www.gravatar.com/avatar/%1$s.png?s=%2$s&amp;r=%3$s&amp;d=%4$s',$h,$s,$r,$d); 
	 		$output = $url;
	 	}
	 	
	 	return $output;
			
	}
	
	public static function avatarURL()
	{
		echo rel(WPI_THEME_URL.md5(get_the_author_email() ).'.ava');
	}
	
	public static function is_cached($h,$s=64,$r='G',$d){
		
		$url = sprintf('http://www.gravatar.com/avatar/%1$s.png?s=%2$s&r=%3$s&d=%4$s',$h,$s,$r,$d); 
		
		$file = WPI_CACHE_AVATAR_DIR.DIRSEP.md5($url).'.png';

		if (!file_exists($file)){
			cURLdownload($url,$file);
		} else {
			return true;
		}		
	}
}	
?>