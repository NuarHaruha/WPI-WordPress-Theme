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
		add_action(wpiFilter::ACTION_INTERNAL_CSS,array($this,$methods));			
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
			$url  = rel(self::getURL($hash,50,$rating).'.ava');
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
						$url 	  	= rel(self::getURL($hash,$size,$rating));
						$attribute 	= 'background-image:url(\''.$url.'.ava\')';
						
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
				$url 	  	= rel(self::getURL($hash,$size,$rating));
				$attribute 	= 'background-image:url(\''.$url.'.ava\')';
				
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
	
	public static function commentGID()
	{
		echo self::T_COMMENT_AUTHOR.md5(get_comment_author_email() );
	}
		
	public static function getURL($h,$s=64,$r='G')
	{	
		return WPI_THEME_URL.join('-',array($h,$s,$r,get_option('avatar_default')));	
	}
	
	public static function avatarURL()
	{
		echo rel(WPI_THEME_URL.md5(get_the_author_email() ).'.ava');
	}
}	
?>