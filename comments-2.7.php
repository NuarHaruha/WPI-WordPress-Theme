<?php
/**
 * $Id$ 
 * WordPress 2.7 Comments template
 */
	if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die ('Please do not load this page directly. Thanks!');

	if ( post_password_required() ) { wpi_section_class_filter('wpi_selector_protected')?>
<?php wpi_section_start('comments');?>
		<?php wpi_comments_title();?>
			<p class="nocomments">
				<?php _e('<big>This post is <cite class="fw" title="password protected">password protected</cite>. Enter the password to view comments.</big>');?>
			</p>
<?php wpi_section_end(); 
		  wpi_remove_section_class_filter('wpi_selector_protected');
		return;
	}
	
	$com_class = ($comments) ? 'comments': 'comments-';
?>
<dd id="wp-comments" class="<?php echo $com_class;?>">
	<div class="outer cf">
		<div class="inner c">	
			<div id="comments" class="content cb cf append-1 prepend-1">			
				<?php wpi_comments_title();?>
				<div id="comment-column" class="cf">
				<?if (get_query_var('cpage'))  wpi_comment_paging_heading(); ?>
				<div id="comment-entry" class="fl">					
				<?php if (have_comments()) : ?>	
						<?php $page_links = paginate_comments_links(array('echo'=>false)); // @todo check for $comments scope  ?>			
						<ol id="comments-list" class="commentlist xoxo r cb cf">
						<?php wp_list_comments(array('walker' => null, 'max_depth' => 3, 'style' => 'ol', 'callback' => 'wpi_comment_start', 'end-callback' => 'wpi_comment_end', 'type' => 'all',
				'page' => '', 'per_page' => '', 'avatar_size' => 48, 'reverse_top_level' => null, 'reverse_children' => '','type'=>'comment'));?>
							<?php wpi_comment_guide($post,$comments,0);?>
						</ol>
			<p class="mgt cb comments-feed"><a type="application/rss+xml" title="RSS 2.0 Comment Feed" href="<?php echo rel(get_post_comments_feed_link());?>" rev="site:relative" class="rn cl"><?php _e('Subscribe to this discussion via RSS',WPI_META);?></a></p>
			<p class="comment-nav cb cf"><?php echo $page_links;?></p>											
			 <?php else: // displayed if there are no comments so far ?>	
				<?php if ('open' == $post->comment_status) : ?>	
				<ol id="comments-list" class="commentlist r cf">				
				<?php wpi_comment_guide($post,$comments,1);?>
				</ol>				
				 <?php else : // comments are closed ?>
					<p class="comments-closed notice rn">
					<?php _e('Comments are closed.',WPI_META);?></p>			
				<?php endif; ?>				
			<?php endif; ?>
				</div>
					<div id="comment-sidebar" class="fl">
							<dl class="xoxo cf">					
								<dd id="comment-sidebar-1" class="cf">
								<?php wpi_dynamic_sidebar(17);?>
								</dd>										
							</dl>
					</div>
				</div>
						
			</div>
		</div>
	</div>
</dd><!-- /#wp-comments --><?php $comstatus = ( 'open' == $post->comment_status) ? 'is_open' : 'is_closed dn';?>

<dd id="wp-respond" class="<?php echo $comstatus;?>">
	<div class="outer cf">
	<div class="inner c">
<?php if ('open' == $post->comment_status) : ?>		
	<div id="respond" class="content cb cf append-1 prepend-1">
		<div id="respond-heading" class="rn cl">	
		<p class="hint">
		<small class="rgb-hgray" title="Write as if you were talking to a good friend (in front of your mother)"><?php _e('"write as if you were talking to a good friend (in front of your mother)."',WPI_META);?></small></p>
		<h3 id="respond-title">
			<?php _e('.have<span>your</span><cite>say</cite>',WPI_META);?>
		</h3>		
		</div>
<?php if ( get_option('comment_registration') && !$user_ID ) : ?>
<p>You must be <a href="<?php echo WPI_URL_SLASHIT; ?>wp-login.php?redirect_to=<?php echo urlencode(get_permalink()); ?>">logged in</a> to post a comment.</p><?php else : $is_opid = ( class_exists('WordpressOpenID') ); ?>
<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">
<ul id="respond-column" class="r cf">
<li id="respond-textarea" class="fl span-13"><?php $tabindex = ($is_opid) ? '5' : '4'; ?>
	<textarea name="comment" id="comment" cols="200" rows="10" tabindex="<?php echo $tabindex;?>"></textarea>
	<p><?php $tabindex = ($is_opid) ? '6' : '5'; ?>
		<button name="submit" type="submit" id="submit" tabindex="<?php echo $tabindex;?>"><span class="combtn"><?php _e('Submit Comment',WPI_META);?></span></button><input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" />
	</p>
</li>
<li id="respond-meta" class="fl span-8">
<?php if ( $user_ID ) : ?>
<p>Logged in as <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>. <a href="<?php echo wpi_logout_url(); ?>" title="Log out of this account">Logout &raquo;</a></p>

<?php else : ?>
	<?php $is_reqs = ($req) ?  '<cite>('.__('required',WPI_META).')</cite>' : ''; ?>
	<ul class="r cf">
	<li>
		<input type="text" class="claimid rn" name="author" id="author" value="<?php echo $comment_author; ?>" tabindex="1" />
		<?php printf(__('<label for="author">Name %1$s</label>',WPI_META),$is_reqs);?>
	</li>
	<li>
		<input type="text" class="gravatar rn" name="email" id="email" value="<?php echo $comment_author_email; ?>" tabindex="2" />
		<?php printf(__('<label for="email" title="Email will not be published">Email %1$s</label>',WPI_META),$is_reqs);?>
	</li>
	<li>
		<input type="text" class="favicon rn" name="url" id="url" value="<?php echo $comment_author_url; ?>" tabindex="3" />
		<?php _e('<label for="url">Website</label>',WPI_META);?>
	</li>
	<?php if( class_exists('WordpressOpenID')): ?>
	<li>
		<input type="text" name="openid_url" class="openid rn" id="openid_url" tabindex="4" />
		<label for="openid_url">OpenID URL</label>
	</li>
	<?php else: ?>
	<?php endif; ?>
	<?php do_action(wpiFilter::ACTION_LIST_COMMENT_FORM,$post->ID);?>
	</ul><?php endif; ?>
<!-- <p><small><strong>XHTML:</strong> You can use these tags: <code><?php echo allowed_tags(); ?></code></small></p>-->
<?php comment_id_fields(); ?>
<?php do_action('comment_form', $post->ID); ?>
</li>
</ul>
</form>
<p id="disclaimer" class="notice cb comment-disclaimer cc-by-sa rn">
<span class="disclaimer db"><?php printf(__('<span class="fw">Disclaimer:</span> For any content that you post, you hereby grant to <strong>%1$s</strong> the royalty-free, irrevocable, perpetual, exclusive and fully sublicensable license to use, reproduce, modify, adapt, publish, translate, create derivative works from, distribute, perform and display such content in whole or in part, world-wide and to incorporate it in other works, in any form, media or technology now known or later developed.</span>',WPI_META),_t('a',WPI_BLOG_NAME,array('href'=>WPI_HOME_URL_SLASHIT)) );?>
<span class="license b1s b1t db">
<a href="http://creativecommons.org/licenses/by-sa/3.0/" rel="license">
<?php _e('Some rights reserved.',WPI_META);?></a>
</span>
</p>
</div>
	</div>
	</div>
</dd><!-- /wp-respond -->
<?php endif; // If registration required and not logged in ?>
<?php else:?>
<?php endif; // if you delete this the sky will fall on your head ?>