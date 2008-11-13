<?php 
/**
 * $Id$ 
 */
	if ('comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
		die ('Please do not load this page directly. Thanks!');

	if (!empty($post->post_password)) { // if there's a password
		if ($_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password) {  // and it doesn't match the cookie
			?>
			<p class="nocomments">
			<?php _e('This post is password protected. Enter the password to view comments.',WPI_META);?></p>
			<?php
			return;
		}
	}

	/* This variable is for alternating comment background */
	
	$com_class = ($comments) ? 'comments': 'comments-';
?>
<dd id="wp-comments" class="<?php echo $com_class;?>">
	<div class="outer cf">
		<div class="inner c">	
			<div id="comments" class="content cb cf append-1 prepend-1">
				<?php wpi_comments_title();?>
				<?php if ($comments) : ?>	
				<ol id="comments-list" class="commentlist xoxo r cb cf"><?php $cnt = 0; foreach ($comments as $comment) : $alt = ($cnt % 2) ? 'light' : 'normal'; $alt .= ' list-'.$cnt; $alt = apply_filters(wpiFilter::FILTER_COMMENTS_SELECTOR,$alt);?>		
					<li id="comment-<?php comment_ID(); ?>" class="<?php echo $alt; ?> hreview"><?php $author_uri = get_comment_author_url(); $author_uri = ($author_uri != '' && $author_uri != 'http://') ? $author_uri : get_permalink($post->ID).'#comment-'.get_comment_ID(); $microid = get_microid_hash(get_comment_author_email(),$author_uri);?>		
						<ul class="reviewier-column cf r">
							<li class="span-3 fl rn hcard">
								<address class="vcard microid-mailto+http:sha1:<?php echo $microid;?> dc-source"><img src="<?php wpi_comment_avatar_src();?>" width="80" height="80" alt="<?php comment_author(); ?>&apos;s photo" class="url cc rn <?php echo wpiGravatar::commentGID(); ?> photo" longdesc="#comment-<?php comment_ID() ?>" /><a href="<?php echo $author_uri; ?>" class="url fn db" rel="external me" title="<?php echo get_comment_author();?>"><?php wpi_comment_author(); ?></a></address>						
							</li>
							<li class="span-16 fl review-content">
								<dl class="review r cf">
									<dt class="item title summary"><a rel="dc:source robots-anchortext" href="#comment-<?php comment_ID(); ?>" class="url fn" title="<?php the_title(); ?>"><span>RE:</span> <?php the_title(); ?></a></dt>	
									<dd class="reviewer-meta"><span class="date-since"><?php echo apply_filters(wpiFilter::FILTER_POST_DATE,get_comment_date());?></span> on <abbr class="dtreviewed" title="<? echo comment_date('Y-m-dTH:i:s:Z'); ?>"><?php comment_date(__('F jS, Y',WPI_META) ); ?> at <?php comment_time(); ?></abbr><?php if(function_exists('hreview_rating')): hreview_rating(); else: ?><span class="rating dn">3</span><span class="type dn">url</span><?php endif;?> &middot; <a href="#microid-<?php comment_ID();?>" class="hreviewer-microid ttip" title="Micro ID | <?php comment_author();?>&apos;s Hash">microId</a> <?php edit_comment_link('edit','<span class="edit-comment">','</span>'); ?></dd>
									<dd id="microid-<?php comment_ID();?>" class="microid-embed" style="display:none"><input class="on-click-select claimid icn-l" type="text" value="mailto+http:sha1:<?php echo $microid;?>" /></dd><?php $counter = $cnt + 1;?>				
									<dd class="reviewer-entry"><big class="comment-count fr" title="Post no #<?php echo $counter; ?>"><?php echo $counter; ?></big><div class="description"><?php echo get_comment_text();?>
									</div><?php if ($comment->comment_approved == '0') : ?><p class="notice rn"><?php _e('Your comment is awaiting moderation.',WPI_META);?></p><?php endif; ?></dd>	
								</dl>		
							</li>
						</ul>
							<!--
							<rdf:RDF xmlns="http://web.resource.org/cc/"
							    xmlns:dc="http://purl.org/dc/elements/1.1/"
							    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">
							<Work rdf:about="<?php the_permalink();?>#comment-<?php comment_ID(); ?>">
							<license rdf:resource="http://creativecommons.org/licenses/by-sa/3.0/" />
							</Work>
							<License rdf:about="http://creativecommons.org/licenses/by-sa/3.0/">
							   <requires rdf:resource="http://web.resource.org/cc/Attribution" />
							   <requires rdf:resource="http://web.resource.org/cc/ShareAlike" />
							   <permits rdf:resource="http://web.resource.org/cc/Reproduction" />
							   <permits rdf:resource="http://web.resource.org/cc/Distribution" />
							   <permits rdf:resource="http://web.resource.org/cc/DerivativeWorks" />
							   <requires rdf:resource="http://web.resource.org/cc/Notice" />
							</License>
							</rdf:RDF>
							-->			
					</li><?php $cnt++; endforeach; /* end for each comment */ ?>
					<?php wpi_comment_guide($post,$comments,$cnt);?>
				</ol>
				<p class="mgt cb comments-feed"><a type="application/rss+xml" title="RSS 2.0 Comment Feed" href="<?php echo rel(get_post_comments_feed_link());?>" rev="site:relative" class="rn"><?php _e('RSS feed for comments in this post',WPI_META);?></a></p>
			 <?php else: // displayed if there are no comments so far ?>	
				<?php if ('open' == $post->comment_status) : ?>	
				<ol id="comments-list" class="commentlist r cf">				
				<?php wpi_comment_guide($post,$comments,1);?>
				</ol>
				 <?php else : // comments are closed ?>
					<!-- If comments are closed. -->
					<p class="comments-closed notice rn"><?php _e('Comments are closed.',WPI_META);?></p>
			
				<?php endif; ?>
			<?php endif; ?>	
			</div>
		</div>
	</div>
</dd><!-- /wp-comments --><?php $comstatus = ( 'open' == $post->comment_status) ? 'is_open' : 'is_closed';?>

<dd id="wp-respond" class="<?php echo $comstatus;?>">
	<div class="outer cf">
	<div class="inner c">
<?php if ('open' == $post->comment_status) : ?>		
	<div id="respond" class="content cb cf append-1 prepend-1">
		<div id="respond-heading" class="rn cl">	
		<p class="hint">
		<small class="rgb-hgray" title="Write as if you were talking to a good friend (in front of your mother)"><?php _e('"write as if you were talking to a good friend (in front of your mother)."',WPI_META);?></small></p>
		<h3 id="respond-title"><?php _e('.have<span>your</span><cite>say</cite>',WPI_META);?></h3>		
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
<p>Logged in as <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>. <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?action=logout" title="Log out of this account">Logout &raquo;</a></p>

<?php else : ?>
	<?php $is_reqs = ($req) ?  '<cite>('.__('required').')</cite>' : ''; ?>
	<ul class="r cf">
	<li>
		<input type="text" class="claimid rn" name="author" id="author" value="<?php echo $comment_author; ?>" tabindex="1" />
		<?php printf(__('<label for="author">Name %1$s</label>',WPI_META),$is_reqs);?>
	</li>
	<li>
		<input type="text" class="gravatar rn" name="email" id="email" value="<?php echo $comment_author_email; ?>" tabindex="2" />
		<?php printf(__('<label for="email">Email %1$s</label>',WPI_META),$is_reqs);?>
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
	<li><?php _e('Email will not be published.',WPI_META);?></li>
	<?php endif; ?>
	</ul><?php endif; ?>
<!-- <p><small><strong>XHTML:</strong> You can use these tags: <code><?php echo allowed_tags(); ?></code></small></p>-->

<?php do_action('comment_form', $post->ID); ?>
</li>
</ul>
</form>
<p class="notice cb comment-disclaimer cc-by-sa rn">
<span class="disclaimer db"><?php printf(__('<span class="fw">Disclaimer:</span> For any content that you post, you hereby grant to <strong>%1$s</strong> the royalty-free, irrevocable, perpetual, exclusive and fully sublicensable license to use, reproduce, modify, adapt, publish, translate, create derivative works from, distribute, perform and display such content in whole or in part, world-wide and to incorporate it in other works, in any form, media or technology now known or later developed.</span>',WPI_META),_t('a',WPI_BLOG_NAME,array('href'=>WPI_URL_SLASHIT)) );?>
<span class="license b1s b1t db">
<a href="http://creativecommons.org/licenses/by-sa/3.0/" rel="license"><?php _e('Some rights reserved.',WPI_META);?></a>
</span>
</p>
</div>
<?php endif; // If registration required and not logged in ?>
<?php else:?>
<?php endif; // if you delete this the sky will fall on your head ?>
	</div>
	<!-- /respond-inner -->
	</div>
	<!-- /respond-outer -->
</dd>
<!-- /wp-respond -->