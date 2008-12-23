<?php
if (!defined('KAIZEKU')) { die(42); }
/**
 * $Id$
 * Wp-Istalker Theme options
 * 
 * @package WordPress
 * @subpackage Administration
 */
 
 
/**
 * wpiAdmin
 * 
 * @access public
 * @since 1.4 
 */
class wpiAdmin
{
	const REQUEST_PREFIX = '/^wpi_/';
	
	const OPTIONS_PREFIX = 'wpi_';
	
	public $request;
	
	public $lang = array();
	
	
	public function __construct()
	{
		$this->registerDefaultScript();
		
		$options = array('optionPagesMenu','optionSEO','optionAdvance','optionFrontpageLayout','optionPostLayout',
		'optionWidgets','optionPlugins','optionMisc','optionCache');
		
		foreach($options as $method){
			add_action(wpiFilter::ACTION_THEME_OPTIONS,array($this,$method));
		}
		
		unset($options,$method);
		
		$this->_lang();
		$this->select_options = array($this->lang['enabled'] => 1,$this->lang['disabled'] => 0 );	
		$this->select_hdirection = array($this->lang['left'] => 'left',$this->lang['right'] => 'right' );
	}
	
	
	private function _lang()
	{
		$this->lang['enabled'] = __('Enabled',WPI_META);
		$this->lang['disabled'] = __('Disabled',WPI_META);
		$this->lang['cache_dir_size'] = __('Cache directory size : %s',WPI_META);
		$this->lang['update_settings'] = __('Update settings',WPI_META);
		$this->lang['left'] = __('Left',WPI_META);
		$this->lang['right'] = __('Right',WPI_META);
	}
	
	
	public function registerDefaultScript()
	{
		wp_register_script(WPI_META.'_admin', WPI_THEME_URL.'admin.js',false, '20081203');	
	}
	
	
	public function __destruct()
	{		
		unset($this);
	}
	
	
    public function filterRequest($request)
    {    			
		
		if (!is_array($request)) return false;		
		
        foreach ($request as $key => $val) {
        	
            if (preg_match(self::REQUEST_PREFIX, $key) ) {				                
				$key = str_rem(self::OPTIONS_PREFIX,$key);				
				if (stristr($key,'flush_')){
					$this->_flushType($key);
				} else {
					
				if ($key == 'verify_google_webmaster'
					|| $key == 'verify_yahoo_explorer'
					|| $key == 'verify_msn'){
					$val =  stripslashes_deep($val);
				}
									
					self::save($key,$val);
				}     
            }            
        }
        
        unset($request,$key,$val);	
  
    }
    
    private function _flushType($type = 'flush_css'){
    	
    	switch ($type){
    		case 'flush_css': $path = WPI_CACHE_CSS_DIR;break;
    		case 'flush_webfont': $path = WPI_CACHE_FONTS_DIR.DIRSEP;break;
    		case 'flush_avatar': $path = WPI_CACHE_AVATAR_DIR.DIRSEP;break;
    		case 'flush_robotstxt': 
				$file = ABSPATH.'robots.txt';
				$content = htmlspecialchars( (string) $_REQUEST['wph_robots_txt_rules']);					
					wpi_fwrite($file,$content);
				return;
			break;
    		case 'flush_update_robotstxt': 
				$file = ABSPATH.'robots.txt';
				$content = htmlspecialchars( (string) $_REQUEST['wph_robots_txt_rules']);	
					if (file_exists($file) && is_writable($file)){				
						wpi_fwrite($file,$content,'w+');
					}
				return;
			break;			
			
    	}
    	
    	
    	
    	if ( ($files = wpi_get_dir($path)) != false ){
	    	foreach($files as $filename){
	    		$file = (string) $path.$filename;
	    		
	    		if (file_exists($file) && is_writable($file)){
	    				unlink($file);
	    		} else {
	    				// suppress error (expensive), force chmod
	    				@chmod($file,0777);
	    				@unlink($file);
	    		}
	    	}
	    	unset($files);
    	}
    }
    
	public function printCSS()
	{		
		t('link','',array('rel'=>'stylesheet','href'=>WPI_THEME_URL.'admin.css','type'=>'text/css'));
		if (is_wp_version(2.7)){
?>
		<style type="text/css">
		    #wpi-theme-options{background-image:none;margin:0pt !important}
			#wpi-theme-options .main{width:80%}
			#wpi-theme-options .side-panel{width:18%}
			#wpi-theme-options .options-item{width:98%}
			#wpi-theme-options .side-panel h2{margin-top:8px}
			.row-2{height:48px !important}
		</style>
<?php			
		}
	}
	
	
	public function script()
	{
		wp_register_script(WPI_META.'_admin', WPI_THEME_URL.'admin.js',false, '0.1');
	}
	
	    
    public function themeOptions()
    {
    	if (is_req('saved')){
				echo '<div id="message" class="updated"><p><strong>'.__('Options saved.',WPI_META).'</strong></p></div>';
		}    	
?>
		<div id="wpi-theme-options" class="wrap cf">
		<div class="main">
		<h2><?php _e('General Settings',WPI_META); ?></h2>
		<form method="post" action="">
		<?php wp_nonce_field(wpiFilter::NONCE_THEME_OPTIONS); ?>
			<?php do_action(wpiFilter::ACTION_THEME_OPTIONS);?>
		<input type="hidden" name="action" value="c2F2ZQ" />
		</form>
		</div>
		<div class="side-panel">
		<h2>Help</h2>
		<ul>
		  <li><a href="http://wp.istalker.net/chrome-milk/quick-start/" title="Quick start guide">Quick start</a></li>
		  <li><a href="http://wp.istalker.net/chrome-milk/features/" title="Features">Features</a></li>
		  <li><a href="http://wp.istalker.net/" title="Project Home">Project Home</a></li>
		  <li><a href="http://blog.kaizeku.com" title="Author Blog">Kaizeku Ban</a></li>		  
		</ul>
		</div>
		</div>
<?php		
	}
	
	public function optionFrontpageLayout()
	{
?>		
<ol class="r mtb options-item">
	<li class="ss"><h4 class="title-">
	<?php _e('Frontpage layout options',WPI_META);?>
	</h4>
	<div class="dn">
	<ul class="mtb">	
		<li>
			<label for="wpi_home_avatar"><?php _e('Avatar',WPI_META);?>				
				<small><?php _e('Display Avatar.',WPI_META);?></small>			
			</label>
			<?php self::addSelect('home_avatar',$this->select_options);?>		
		</li>		
		<li class="last">
			<label for="wpi_home_sidebar_position"><?php _e('Sidebar position',WPI_META);?>				
				<small><?php _e('Vertical Sidebar position, default: Right',WPI_META);?></small>			
			</label>
			<?php self::addSelect('home_sidebar_position',$this->select_hdirection);?>		
		</li>			
	</ul>
		<?php self::saveButton();?>	
	</div>
	</li>
</ol>		
<?				
	}
	
	public function optionSEO()
	{
?>
<ol class="r mtb options-item">
	<li class="ss"><h4 class="title-">
	<?php _e('Search Engine Optimization',WPI_META);?>
	</h4>
	<div class="dn">
	<ul class="mtb">
		<li>
			<h4><?php _e('Robots Exclusion Standards',WPI_META);?></h4>
			<small><?php _e('Method used to exclude robots (search engine indexes and services) from particular URLs or contents on server.',WPI_META);?></small>
			<ul style="list-style:lower-roman outside;padding-left:28px">
				<li>			
				<label for="wpi_robots_txt"><?php _e('robots.txt',WPI_META);?>				
				<small><?php _e('Enabled custom robots rules.',WPI_META);?>
				<?php self::helpIcon('http://www.robotstxt.org/orig.html',__('About Robots Exlusion Standards',WPI_META) );?></small>			
				</label>
				<?php $prop = self::option('robots_txt');?>
				<?php self::addSelect('robots_txt',$this->select_options,$prop);?>
				<?php if($prop):
				    $file = ABSPATH.'robots.txt';
					$file_exists = file_exists($file);
					$is_writable = is_writable($file);
				?>
					<dl>
						<dd>	
						<label for="wpi_robots_txt_rules" style="width:150px">
							<?php _e('Robots rules',WPI_META);?>
							<small><?php _e('<strong>Symbols:</strong><br />\'<strong>*</strong>\' - wildcard match a sequence of characters in URL</small><br /><small>\'<strong>$</strong>\' - anchors at the end of the URL string',WPI_META);?></small>	
						</label>
						<?php if(!$file_exists): // no robots.txt found ?>
							<?php if( ($rules = wpi_get_robots_rules()) != false): ?>							
								<?php 	if (has_count($rules)){
										$rules = implode(PHP_EOL,$rules);
										}
								?>
							<?php endif; ?>
						<?php else: // file exists ?>
								<?php if ( ($f = fopen($file, 'r')) != false){	
										$rules = fread($f, filesize($file));
										$rules = htmlspecialchars($rules);
										fclose($f);		
									  }
										
								?>						
						<?php endif; ?>
						<?php			t('textarea',trim($rules),array(
											'style'=>'width:72%;height:200px;overflow:auto;margin-left:10px',
											'id'=>'wph_robots_txt_rules','name'=>'wph_robots_txt_rules')); 
						
						?>
						<?php if ($file_exists && $is_writable):?>
						<button class="sbtn" type="submit" name="wpi_flush_update_robotstxt" id="wpi_flush_update_robotstxt" value="1">
							<?php echo $this->lang['update_settings']; ?>
						</button>
						<?php elseif( !$file_exists):?>
							<p class="notice">
								<?php _e('<strong>Error</strong> <tt>robots.txt</tt> file not found.',WPI_META);?>
							</p>
							<button class="sbtn" style="width:190px" type="submit" name="wpi_flush_robotstxt" id="wpi_flush_robotstxt" value="1">
							<?php _e('Create robots.txt',WPI_META); ?>
							</button>
						<?php endif;?>
						</dd>
					</dl>
				<? endif;?>
				</li>			
				<li class="last"><?php $prop = self::option('meta_robots'); ?>
					<label for="wpi_meta_robots"><?php _e('Robots Meta',WPI_META);?>
					<small><?php _e('Auto append meta robot.',WPI_META);?></small></label>
					<?php self::addSelect('meta_robots',$this->select_options,$prop);?>
					<?php if($prop):?>
						<ul>
							<li>
								<label for="wpi_meta_robots_author"><?php _e('Author page',WPI_META);?>
									<small><?php _e('Add No-index rules on author page.',WPI_META);?></small></label>
									<?php self::addSelect('meta_robots_author',$this->select_options);?>	
							</li>						
							<li class="last">
								<label for="wpi_meta_robots_search"><?php _e('Search results',WPI_META);?>
									<small><?php _e('Add No-index rules on search results pages.',WPI_META);?></small></label>
									<?php self::addSelect('meta_robots_search',$this->select_options);?>	
							</li>
						</ul>
					<?php endif;?>
				</li>				
			</ul>		
		</li>	
		<li>
			<h4><?php _e('Feed Syndications Rules',WPI_META);?></h4>
			<ul style="list-style:lower-roman outside;padding-left:28px">
				<li>
					<label for="wpi_exclude_feed"><?php _e('All RSS Feeds',WPI_META);?>
					<small><?php _e('Add No-index rules to all RSS feeds.<br>Not recommended.',WPI_META);?></small></label>
					<?php self::addSelect('exclude_feed',$this->select_options);?>
				</li>
				<li>
					<label for="wpi_exclude_comments_feed"><?php _e('Comments RSS Feeds',WPI_META);?>
					<small><?php _e('Add No-index rules to all Comment feeds.',WPI_META);?></small></label>
					<?php self::addSelect('exclude_comments_feed',$this->select_options);?>
				</li>					
				<li class="last">
					<label for="wpi_exclude_ypipe"><?php _e('Yahoo! Pipe',WPI_META);?>
					<small><?php _e('Disabled Yahoo! Pipe from syndicating/manipulating your feeds.',WPI_META);?><?self::helpIcon('http://pipes.yahoo.com/pipes/','Yahoo! Pipe');?></small></label>
					<?php self::addSelect('exclude_ypipe',$this->select_options);?>
				</li>				
			</ul>
		</li>						
		<li><?php $prop = self::option('meta_title');?>
			<label><?php _e('Page title',WPI_META);?>
			<small><?php _e('Enable custom page title.',WPI_META); ?></small></label>
			<?php self::addSelect('meta_title',$this->select_options,$prop);?>
		</li>		
		<li><?php $prop = self::option('meta_description');?>
			<label for="wpi_meta_description"><?php _e('Meta description',WPI_META);?><small><?php _e('custom meta description.',WPI_META); ?></small></label>
			<?php self::addSelect('meta_description',$this->select_options,$prop);?> 	
				<?php if ($prop): ?>
				<ul>
					<li class="last">
						<label for="wpi_def_meta_description"><?php _e('Default description',WPI_META);?></label>						
						<textarea id="wpi_def_meta_description" name="wpi_def_meta_description" style="width:50%"><?php echo wpi_safe_stripslash(self::option('def_meta_description'));?></textarea>				
					</li>
				</ul>		
				<?php endif; ?>							
		</li>
		<li><?php $prop = self::option('meta_keywords');?>
			<label for="wpi_meta_keywords"><?php _e('Meta keywords',WPI_META);?><small><?php _e('Custom meta keywords',WPI_META); ?></small></label>
			<?php self::addSelect('meta_keywords',$this->select_options,$prop);?> 
				<?php if ($prop): ?>
				<ul>
					<li class="last">
						<label for="wpi_def_meta_keywords"><?php _e('Default keywords',WPI_META);?></label>						
						<textarea id="wpi_def_meta_keywords" name="wpi_def_meta_keywords" style="width:50%"><?php echo wpi_safe_stripslash(self::option('def_meta_keywords'));?></textarea>				
					</li>
				</ul>		
				<?php endif; ?>			
		</li>
		<li class="last">
			<label for="wpi_relative_links"><?php _e('Make links relative',WPI_META);?><small><?php _e('Increase relative links.',WPI_META); ?></small></label>
				<?php self::addSelect('relative_links',$this->select_options);?>  	
		</li>		
	</ul>
		<?php self::saveButton();?>	
	</div>
	</li>
</ol>		
<?php		
	}	
		
	
	public function optionCache(){
?>
<ol class="r mtb options-item">
	<li class="ss"><h4 class="title-">
	<?php _e('Manage Cached files',WPI_META);?>
	</h4>
	<div class="dn">
	<ul class="mtb">
		<li>
			<h4><?php _e('Stylesheet',WPI_META);?></h4>
			<p>
			<?php $disabled = is_writable(WPI_CACHE_CSS_DIR) ? '' : 'disabled="disabled"'?>
			<label for="wpi_cache_css"><?php _e('Cache CSS:',WPI_META);?>
			<?php if ($disabled != ''):?>
			<small><?php _e('Notice: Public CSS cache dir is not writable');?></small>
			<?php endif;?>
			</label>
				<select name="wpi_cache_css" id="wpi_cache_css" size="2" <?php echo $disabled;?> class="row-2">
			<?php	$prop = self::option('cache_css'); 
			self::htmlOption(array(
						$this->lang['enabled'] => 1,
						$this->lang['disabled'] => 0 ),$prop); ?>
				</select>
			</p>
			<?php if($prop): ?>					
			<?php $css = wpi_get_dir(WPI_CACHE_CSS_DIR);?>
			<?php if (has_count($css) && !empty($css)):?>
			<dl>
			<?php 
			$size = 0;
			$n = 1;
				foreach($css as $tag){
					$s = filesize(WPI_CACHE_CSS_DIR.$tag);
					$size += $s;
					$s = format_filesize($s);
					$s = _t('small',' - '.$s);
					$t = str_rem('.css',$tag);
					$a = _t('a',$tag,array('href'=>wpi_get_stylesheets_url($t),'target'=>'_blank' ));
					$c = _t('small',$n.'. ');
					t('dd',$c.$a.$s,array('style'=>'display:block;clear:both'));
					$n++;
				}
				unset($css);
			?>
			<small><?php sprintf($this->lang['cache_dir_size'], format_filesize($size)) ;?></small>			
			</dl>
			<button class="sbtn" type="submit" name="wpi_flush_css" id="wpi_flush_css" value="1">Erase Cache</button>
			<?php else:?>
			<p><?php _e('No cached files.',WPI_META);?></p>
			<?php endif; ?>		
			<?php endif; ?>	
		</li>
		<li>
			<h4><?php _e('GD webfont image',WPI_META);?></h4>
			<p>
			<?php $disabled = is_writable(WPI_CACHE_FONTS_DIR) ? '' : 'disabled="disabled"'?>
			<label for="wpi_cache_webfont"><?php _e('Cache GD font images',WPI_META);?>
			<?php if ($disabled != ''):?>
			<small><?php _e('Notice: Public webfont cache dir is not writable');?></small>
			<?php endif;?>
			</label>
				<select name="wpi_cache_webfont" id="wpi_cache_webfont" size="2" <?php echo $disabled;?> class="row-2">
			<?php	$prop = self::option('cache_webfont'); 
			self::htmlOption(array(
						$this->lang['enabled'] => 1,
						$this->lang['disabled'] => 0 ),$prop); ?>
				</select>		
			</p>
			<?php if($prop): ?>									
			<?php $fonts = wpi_get_dir(WPI_CACHE_FONTS_DIR);?>	
			<?php if (has_count($fonts) && !empty($fonts)):?>
			<dl>
			<?php 
			$size = 0;
			$n = 1;
				foreach($fonts as $tag){
					$s = filesize(WPI_CACHE_FONTS_DIR.DIRSEP.$tag);
					$size += $s;
					$s = format_filesize($s);
					$s = _t('small',' - '.$s);
					$a = _t('a',str_rem('.png',$tag),array('href'=>WPI_THEME_URL.'public/cache/webfonts/'.$tag,'target'=>'_blank' ));
					$c = _t('small',$n.'. ');
					t('dd',$c.$a.$s,array('style'=>'display:block;clear:both'));
					$n++;
				}
				unset($fonts);
			?>
			<small><?php sprintf($this->lang['cache_dir_size'], format_filesize($size)) ;?></small>
			</dl>
			<button class="sbtn" type="submit" name="wpi_flush_webfont" id="wpi_flush_webfont" value="1">Erase Cache</button>
			<?php else:?>
			<p><?php _e('No cached files.',WPI_META);?></p>
			<?php endif; ?>	
			<?php endif;?>		
		</li>		
		<li class="last">
			<h4><?php _e('Avatar image',WPI_META);?></h4>
			<p>
			<?php $disabled = is_writable(WPI_CACHE_AVATAR_DIR) ? '' : 'disabled="disabled"'?>
			<label for="wpi_cache_avatar"><?php _e('Cache Avatar images',WPI_META);?>
			<?php if ($disabled != ''):?>
			<small><?php _e('Notice: Public avatar cache dir is not writable');?></small>
			<?php endif;?>
			</label>
				<select name="wpi_cache_avatar" id="wpi_cache_avatar" size="2" <?php echo $disabled;?> class="row-2">
			<?php	$prop = self::option('cache_avatar'); 
			self::htmlOption(array(
						$this->lang['enabled'] => 1,
						$this->lang['disabled'] => 0 ),$prop); ?>
				</select>				
			</p>
			<?php if($prop): ?>	
			<?php $ava = wpi_get_dir(WPI_CACHE_AVATAR_DIR);?>
			<?php if (has_count($ava) && !empty($ava)):?>
			<dl>
			<?php 
			$size = 0;
			$n = 1;
				foreach($ava as $tag){
					$s = filesize(WPI_CACHE_AVATAR_DIR.DIRSEP.$tag);
					$size += $s;
					$s = format_filesize($s);
					$s = _t('small',' - '.$s);
					$a = _t('a',str_rem('.png',$tag),array('href'=>WPI_THEME_URL.'public/cache/avatar/'.$tag,'target'=>'_blank' ));
					$c = _t('small',$n.'. ');
					t('dd',$c.$a.$s,array('style'=>'display:block;clear:both'));
					$n++;
				}
			?>
			<small><?php sprintf($this->lang['cache_dir_size'], format_filesize($size)) ;?></small>
			</dl>
			<button class="sbtn" type="submit" name="wpi_flush_avatar" id="wpi_flush_avatar" value="1">Erase Cache</button>
			<?php else:?>
			<p><?php _e('No cached files.',WPI_META);?></p>
			<?php endif; ?>
			<?php endif; ?>			
		</li>		
	</ul>
		<?php self::saveButton();?>	
	</div>
	</li>
</ol>					
<?php		
	}	
	
	public function optionWidgets()
	{
?>
<ol class="r mtb options-item">
	<li class="ss"><h4 class="title-">
	<?php _e('Sidebar widgets',WPI_META);?>
	</h4>
	<div class="dn">
	<ul class="mtb">
		<li>
			<label for="wpi_widget_treeview"><?php _e('Category treeview:',WPI_META);?>
				<small><?php _e('Replace categories widgets with jQuery Treeview',WPI_META);?>
				<?php self::helpIcon('http://bassistance.de/jquery-plugins/jquery-plugin-treeview/',__('jQuery plugin Treeview ',WPI_META) );?>
				</small>
			</label>
			<?php self::addSelect('widget_treeview',$this->select_options);?>	
		</li>
		<li>
			<label for="wpi_widget_dynacloud"><?php _e('Most used terms',WPI_META);?>
				<small><?php _e('Display Dyna Cloud most used keywords (singular comment widget)',WPI_META);?>
				<?php self::helpIcon('http://johannburkard.de/blog/programming/javascript/dynacloud-a-dynamic-javascript-tag-keyword-cloud-with-jquery.html',__('jQuery plugin DynaCloud ',WPI_META) );?>
				</small>
			</label>
			<?php self::addSelect('widget_dynacloud',$this->select_options);?>	
		</li>
		<?php if(wpi_is_plugin_active('global-translator/translator.php')) :?>
		<li>
			<label for="wpi_widget_gtranslator"><?php _e('Global Translator',WPI_META);?>
				<small><?php _e('Display NH2\'s Global translator (singular sub widget)',WPI_META);?>
				<?php self::helpIcon('http://www.nothing2hide.net/wp-plugins/wordpress-global-translator-plugin/',__('Nothing2Hide\'s Global Translator',WPI_META) );?>
				</small>
			</label>
			<?php self::addSelect('widget_gtranslator',$this->select_options);?>	
			<ul>
				<li class="last">
					<label for="wpi_widget_gtranslator_meta"><?php _e('Language Metalink',WPI_META);?>
						<small><?php _e('Append Global Translator language meta-link',WPI_META);?>						
						</small>
					</label>
					<?php self::addSelect('widget_gtranslator_meta',$this->select_options);?>				
				</li>
			</ul>
		</li>		
		<?php endif; ?>
		<?php if(wpi_is_plugin_active('wp-postviews/wp-postviews.php')) :?>
		<li>
			<label for="wpi_widget_wppostview"><?php _e('WP Post views',WPI_META);?>
				<small><?php _e('Display Lester Chan\'s WP post view',WPI_META);?>
				<?php self::helpIcon('http://lesterchan.net/wordpress/readme/wp-postviews.html',__('Lester Chan\'s WP post views',WPI_META) );?>
				</small>
			</label>
			<?php self::addSelect('widget_wppostview',$this->select_options);?>	
		</li>		
		<?php endif; ?>		
		<li>
			<label for="wpi_widget_technorati_backlink"><?php _e('Technorati link count',WPI_META);?>
				<small><?php _e('Display Technorati blog reaction (singular sub widget)',WPI_META);?>
				<?php self::helpIcon('http://technorati.com/tools/linkcount/',__('Technorati Link Count Widget',WPI_META) );?>
				</small>
			</label>
			<?php self::addSelect('widget_technorati_backlink',$this->select_options);?>	
		</li>							
		<li class="last">
			<label><?php _e('Similar post:',WPI_META);?>
			<small><?php _e('Display similar post(s) base on tags (singular widget)',WPI_META);?></small>
			</label>
			<?php $prop = self::option('widget_related_post'); self::addSelect('widget_related_post',$this->select_options); ?>		
			<?php if ($prop): ?>
				<ul class="cb cf">
					<li>
						<label><?php _e('Title:',WPI_META);?></label>
							<?php t('input', '', array('type' => 'text', 'name' => 'wpi_related_post_widget_title','id' =>'wpi_related_post_widget_title','value' => self::option('related_post_widget_title'))); ?>	
					</li>				
					<li>
						<label><?php _e('Max post:',WPI_META);?></label>
							<?php t('input', '', array('type' => 'text', 'name' => 'wpi_related_post_widget_max','id' =>'wpi_related_post_widget_max','value' => self::option('related_post_widget_max'))); ?>	
					</li>
					<li>
						<label><?php _e('Show date:',WPI_META);?></label>
							<select name="wpi_related_post_widget_date" id="wpi_related_post_widget_date" size="2" class="row-2">
						<?php	$prop = self::option('related_post_widget_date'); 
						self::htmlOption(array(
									$this->lang['enabled'] => 1,
									$this->lang['disabled'] => 0 ),$prop); ?>
							</select>		
					</li>		
					<li class="last">
						<label><?php _e('Count comments:',WPI_META);?></label>
							<select name="wpi_related_post_widget_comments_count" id="wpi_related_post_widget_comments_count" size="2" class="row-2">
						<?php	$prop = self::option('related_post_widget_comments_count'); 
						self::htmlOption(array(
									$this->lang['enabled'] => 1,
									$this->lang['disabled'] => 0 ),$prop); ?>
							</select>		
					</li>								
				</ul>
			<?php endif;?>		
		</li>		
	</ul>
		<?php self::saveButton();?>
	</div>		
	</li>
</ol>
<?php		
	}
	
	
	public function optionPagesMenu()
	{
		$menu = array();
		
		$menu['pages'] = self::option('menu_page_enable');
		$menu['pathway'] = self::option('pathway_enable');
?>
<ol class="r mtb options-item">
	<li class="ss"><h4 class="title-">
	<?php _e('Navigation',WPI_META);?>
	</h4>
	<div class="dn">
	<ul class="mtb">
		<li>
			<label><?php _e('Enable pages menu:',WPI_META);?></label>
				<select name="wpi_menu_page_enable" id="wpi_menu_page_enable" size="2" class="row-2" disabled="disabled">
			<?php self::htmlOption(array(
						$this->lang['enabled'] => 1,
						$this->lang['disabled'] => 0 ),$menu['pages']); ?>
				</select> 	
			
		</li><?php if ($menu['pages']): ?>
		<ul>
			<li>
			<label for="wpi_menu_page_exclude">
				<?php _e('Exclude Page',WPI_META); ?>
			</label><?php t('input', '', array('type' => 'text', 'name' => 'wpi_menu_page_exclude','id' =>'wpi_menu_page_exclude','value' => self::option('menu_page_exclude'))); ?>
			<small>separate page id with comma (e.g., 42,101,31,337)</small>			
			</li>
		</ul>	
		<?php endif; ?>	
		<li class="last">		
			<label><?php _e('Pathway:',WPI_META);?></label>
				<select name="wpi_pathway_enable" id="wpi_pathway_enable" size="2" class="row-2">
			<?php self::htmlOption(array(
						$this->lang['enabled'] => 1,
						$this->lang['disabled'] => 0 ),$menu['pathway']); ?>
				</select> 
				<?php if($menu['pathway']): ?>
				<?php $prop = self::option('pathway_frontpage_text');?>
				<?php $prop = (!empty($prop)) ? $prop : 'Frontpage'; ?>
					<ul>
						<li class="last">
						<label for="wpi_pathway_frontpage_text">
							<?php _e('Frontpage label',WPI_META); ?>
							<small><?php _e('Default: Frontpage',WPI_META); ?></small>
						</label><?php t('input', '', array('type' => 'text', 'name' => 'wpi_pathway_frontpage_text','id' =>'wpi_pathway_frontpage_text','value' => $prop)); ?>
									
						</li>
					</ul>				
				<?php endif;?>
		</li>
	</ul>
		<?php self::saveButton();?>
	</div>		
	</li>
</ol>
<?php		
	}
	
	
	public function optionPostLayout()
	{
		$prop = self::option('post_by_enable');
?>
<ol class="r mtb options-item">
	<li class="ss"><h4 class="title-">Singular layout options</h4>
	<div class="dn">
	<ul class="romanNumerals mtb">
		<li>
			<label><?php _e('Author name:',WPI_META);?></label>
				<select name="wpi_post_by_enable" id="wpi_post_by_enable" size="2" class="row-2">
			<?php self::htmlOption(array(
			$this->lang['enabled'] =>1,
			$this->lang['disabled'] =>0 ),$prop);?>
				</select>			
		</li>
		<li><?php $prop = self::option('post_excerpt'); ?>
			<label><?php _e('Show excerpt',WPI_META);?>
				<small>Show excerpt on post (frontpage &amp; articles page)</small></label>
				<select name="wpi_post_excerpt" id="wpi_post_excerpt" size="2" class="row-2">
			<?php self::htmlOption(array(
			$this->lang['enabled'] =>1,
			$this->lang['disabled'] =>0 ),$prop);?>
				</select>			
		</li>		
		<li><?php $prop = self::option('relative_date'); ?>
			<label><?php _e('Relative post date:',WPI_META);?>
				<small>Time since date format</small></label>
				<select name="wpi_relative_date" id="wpi_relative_date" size="2" class="row-2">
			<?php self::htmlOption(array(
			$this->lang['enabled'] =>1,
			$this->lang['disabled'] =>0 ),$prop);?>
				</select>			
		</li>
				
		<li><?php $prop = self::option('post_hrating'); ?>
			<label><?php _e('hReview rating',WPI_META);?>			
				<small>Show hreview ratings star</small></label>
				<select name="wpi_post_hrating" id="wpi_post_hrating" size="2" class="row-2">
			<?php self::htmlOption(array(
			$this->lang['enabled'] =>1,
			$this->lang['disabled'] =>0 ),$prop);?>
				</select>
		</li>
		<li><?php $prop = self::option('post_bookmarks'); ?>
			<label><?php _e('Social Bookmarks',WPI_META);?></label>
				<select name="wpi_post_bookmarks" id="wpi_post_bookmarks" size="2" class="row-2">
			<?php self::htmlOption(array(
			$this->lang['enabled'] =>1,
			$this->lang['disabled'] =>0 ),$prop);?>
				</select>			
		</li>
		
		<li class="last"><?php $prop = self::option('post_author_description'); ?>
			<label><?php _e('Author description',WPI_META);?><small>Show author description on single page.</small>	</label>
				<select name="wpi_post_author_description" id="wpi_post_author_description" size="2" class="row-2">
			<?php self::htmlOption(array(
			$this->lang['enabled'] =>1,
			$this->lang['disabled'] =>0 ),$prop);?>
				</select>
					
		</li>								
	</ul>
		<?php self::saveButton();?>
	</div>		
	</li>
</ol>
<?php	
	}
	
	public function optionMisc()
	{
?>
<ol class="r mtb options-item">
	<li class="ss"><h4 class="title-"><?php _e('Extra metadata',WPI_META);?></h4>
	<div class="dn">
	<ul class="mtb">
		<li>
			<label>
				<?php _e('ClaimID',WPI_META);?>
				<small>OpenID Server and Delegation</small>
			</label>
			<?php t('input', '', array('type' => 'text', 'name' => 'wpi_claimid','id' =>'wpi_claimid','value' => self::option('claimid'))); ?>
		</li>		
		<li>
			<label>
				<?php _e('MicroID',WPI_META);?>
				<small>Small Decentralized Verifiable Identity</small>
			</label>
			<?php t('input', '', array('type' => 'text', 'name' => 'wpi_microid_hash','id' =>'wpi_microid_hash','value' => self::option('microid_hash'))); ?></li>
		<li>
			<label>
				<?php _e('OpenID',WPI_META);?>
				<small>X-XRDS-Location</small>
			</label>
			<?php t('input','', array('type' => 'text', 'name' => 'wpi_xxrds','id' =>'wpi_xxrds','value' => self::option('xxrds'))); ?>
		</li>
		<?php if (file_exists(WP_ROOT.DIRSEP.'labels.rdf')) : ?>
		<li>
			<label>
				<?php _e('PICS',WPI_META);?>
				<small>Platform for Internet Content Selection (PICS)</small>
			</label>
			<textarea id="wpi_icra_label" name="wpi_icra_label" style="width:50%"><?php echo stripslashes_deep(self::option('icra_label'));?></textarea>		
		</li>
		<?php endif; ?>	
		<li>
			<h4>Site verification</h4>
			<small>Site verification using a meta tag</small>
			<ul style="list-style:lower-roman outside;padding-left:28px">
				<li>
				<label for="wpi_verify_google_webmaster">
					<?php _e('Google Webmaster',WPI_META);?>
					<small>Google webmaster tool. <?php self::helpIcon('http://www.google.com/support/webmasters/bin/answer.py?answer=35181&cbid=-s56o30hh5mb7&src=cb&lev=answer','Google Webmaster help')?></small>
				</label>
				<?php t('input','', array('type' => 'text', 'name' => 'wpi_verify_google_webmaster','id' =>'wpi_verify_google_webmaster','value' => self::option('verify_google_webmaster'))); ?>			
				</li>
				<?php if(!file_exists(ABSPATH.'LiveSearchSiteAuth.xml')): ?>
				<li>
				<label for="wpi_verify_msn">
					<?php _e('Live Webmaster ID',WPI_META);?>
					<small>MS Live search. <?php self::helpIcon('http://help.live.com/help.aspx?mkt=en-us&project=wl_webmasters','Windows Live Webmaster help')?></small>
				</label>
				<?php t('input','', array('type' => 'text', 'name' => 'wpi_verify_msn','id' =>'wpi_verify_msn','value' => self::option('verify_msn'))); ?>			
				</li>
				<?php endif;?>					
				<li class="last">
				<label for="wpi_verify_yahoo_explorer">
					<?php _e('Yahoo! Explorer ID',WPI_META);?>
					<small>Yahoo! Site Explorer. <?php self::helpIcon('http://help.yahoo.com/l/us/yahoo/search/siteexplorer/register/','Yahoo! Site Explorer Registering and Authenticating Sites')?></small>
				</label>
				<?php t('input','', array('type' => 'text', 'name' => 'wpi_verify_yahoo_explorer','id' =>'wpi_verify_yahoo_explorer','value' => self::option('verify_yahoo_explorer'))); ?>			
				</li>						
			</ul>
		</li>
		<li>
			<h4>Web services</h4>
			<ul>
				<li>
					<label for="wpi_google_analytics_tracking_code"><?php _e('Google Analytics',WPI_META);?>
					<small><?php _e('Web Property ID or Tracking Code (UA-XXXXXXX-X)',WPI_META);?>
					<?php self::helpIcon('https://www.google.com/support/googleanalytics/bin/answer.py?answer=55603&cbid=caibe02nb1ja&src=cb&lev=answer',
					__('Google Analytics  tracking code',WPI_META) )?></small>					
					</label>
					<?php t('input','', array('type' => 'text', 'name' => 'wpi_google_analytics_tracking_code','id' =>'wpi_google_analytics_tracking_code','value' => self::option('google_analytics_tracking_code'))); ?>				
				</li>			
				<li class="last">
					<label for="wpi_meta_google_notranslate"><?php _e('Google translate',WPI_META);?>
					<small><?php _e('Prevent google from translating the entire document.',WPI_META);?></small></label>
					<?php self::addSelect('google_notranslate',$this->select_options);?>				
				</li>
			</ul>
		</li>
		<li>
			<h4>Web icons</h4>
			<small>Browser and mobile web icon</small>
			<ul>
				<li>
					<label for="wpi_icn_favicon"><?php _e('Favicon',WPI_META);?>
						<small><?php _e('Favicon <tt>16x16</tt> pixels (ico, gif or png)',WPI_META);?>
							<?php self::helpIcon('www.favicon.cc',__('Create custom favicon via favicon.cc',WPI_META) )?>
						</small>					
					</label> <?php $icn = (self::option('icn_favicon')) ? self::option('icn_favicon') : wpi_get_favicon_url(); ?>
					<?php t('input','', array('type' =>'text', 'name' =>'wpi_icn_favicon', 'id'=>'wpi_icn_favicon', 'value'=> clean_url($icn) )); ?>				
				</li>			
				<li class="last">
					<label for="wpi_icn_iphone"><?php _e('iPhone',WPI_META);?>
					<small><?php _e('PNG file <tt>57x57</tt> pixels.',WPI_META);?></small></label>
					<?php t('input','', array('type' =>'text', 'name' =>'wpi_icn_iphone', 'id'=>'wpi_icn_iphone', 'value'=>self::option('wpi_icn_iphone'))); ?>				
				</li>
			</ul>			
		</li>
		<li class="last">
			<label>
				<?php _e('Geo positioning',WPI_META);?>
				<small>Geographical coordinates to URL</small>
			</label>
			<?php t('input', '', array('type' => 'text', 'name' => 'wpi_geourl','id' =>'wpi_geourl','value' => self::option('geourl'))); ?>
		</li>
	</ul>
	<?php self::saveButton();?>
	</div>
	</li>
</ol>		
<?php		
	}
	
	public function optionAdvance()
	{
		$prop = self::option('xhtml_mime_type');
?>
<ol class="r mtb options-item">
	<li class="ss"><h4 class="title-" title="toggle view: Advance">Advanced Preferences</h4>
	<div class="dn">
	<ul class="romanNumerals mtb">
		<li>
			<label><?php _e('True xhtml+xml',WPI_META);?><small>Send real application/xhtml+xml content</small></label>
				<select name="wpi_xhtml_mime_type" id="wpi_xhtml_mime_type" size="2" class="row-2" disabled="disabled">
			<?php self::htmlOption(array(
						$this->lang['enabled'] => 1,
						$this->lang['disabled'] => 0 ),$prop);?>
				</select> 	
			
		</li>
		<li><?php $prop = self::option('css_via_header');?>
			<label><?php _e('Cloak CSS',WPI_META);?><small>For supporting browser only.</small></label>
				<select name="wpi_css_via_header" id="wpi_css_via_header" size="2" class="row-2" disabled="disabled">
			<?php self::htmlOption(array(
						$this->lang['enabled'] => 1,
						$this->lang['disabled'] => 0 ),$prop);?>
				</select> 	
			
		</li>
		<li><?php $prop = self::option('text_dir');?>
			<label><?php _e('Text direction',WPI_META);?>
			<small>refer <a href="http://www.w3.org/TR/REC-html40/struct/dirlang.html">W3C HTML Documentations</a>.</small></label>
				<select name="wpi_text_dir" id="wpi_text_dir" size="2" class="row-2">
			<?php self::htmlOption(array(
			__('Left to Right',WPI_META)=>'ltr',
			__('Right to Left',WPI_META)=>'rtl' ),$prop);?>
				</select> 	
		</li>	
		
		<li><?php $prop = self::option('browscap_autoupdate');?>
			<label><?php _e('Auto-update Browser Library',WPI_META);?>
			<small>Refer: <a href="http://browsers.garykeith.com/">Gary Keith's Browser capabilities project Library</a>.</small>
			</label>
				<select name="wpi_browscap_autoupdate" id="wpi_browscap_autoupdate" size="2" class="row-2" disabled="disabled">
			<?php self::htmlOption(array(
						$this->lang['enabled'] => 1,
						$this->lang['disabled'] => 0 ),$prop);?>
				</select> 	
			
		</li>	
		<li><?php $prop = self::option('banner'); ?>
			<label><?php _e('Show banner:',WPI_META);?></label>
				<select name="wpi_banner" id="wpi_banner" size="2" class="row-2">
			<?php self::htmlOption(array(
			$this->lang['enabled'] =>1,
			$this->lang['disabled'] =>0 ),$prop);?>
				</select>		
				<?php if ($prop): ?>
				<ul>
					<li>
						<label>
							<?php _e('Image URL',WPI_META);?>
							<small>default: http://static.animepaper.net/upload/rotate.jpg</small>
						</label>
						<?php $burl = self::option('banner_url');?>
						<?php if(empty($burl)){ $burl = 'http://static.animepaper.net/upload/rotate.jpg';}?>
						<?php t('input', '', array('type' => 'text', 'name' => 'wpi_banner_url','id' =>'wpi_banner_url','value' => $burl)); ?>	
						<?php if( ($b = wpi_has_banner()) != false ): ?>
							<?php t('small',sprintf(__('<strong>Note:</strong> You have %d custom banner(s).',WPI_META),count($b['banner'])),array('style'=>'clear:left;display:block') );?>
								<small><strong>Banner: </strong>
								<?php $cnt =1; foreach($b['banner'] as $filename): ?>
									<span><?php t('a',$cnt,array('href'=>THEME_IMG_URL.'banner/'.$filename,'class'=>'thickbox','title'=>$filename,'rel'=>'banner'));?></span>
								<?php $cnt++;endforeach;?>
								</small>
						<?php endif;?>	
						<?php t('small',__('<strong>Allowed Variables:</strong>',WPI_META),array('style'=>'display:block') );?>	
						<?php t('small','%BANNER_URL%');?>
						<?php if(count($b['banner']) >= 2):?>
						<br /><?php t('small','%RANDOM_BANNER_URL%');?>
						<?php endif;?>		
					</li>
					<li>
						<label>
							<?php _e('Background repeat',WPI_META);?>
							<small>default: none</small>
						</label>
						<select name="wpi_banner_repeat" id="wpi_banner_repeat" size="2" class="row-4">
					<?php $repeat = wpi_option('banner_repeat');
					  if (empty($repeat)){
					  	$repeat = 'no-repeat';
					  } ?>
					<?php self::htmlOption(array(
					'None' => 'no-repeat',
					'Tile' => 'repeat',
					'Horizontal'=>'repeat-x',
					'Vertical'=>'repeat-y'),$repeat);?>
						</select>
					</li>
					<li>
						<label>
							<?php _e('Banner height',WPI_META);?>
							<small>default: 72<sub>px</sub></small>
						</label>
						<?php $bheight = self::option('banner_height');?>
					<?php  if (empty($bheight)){
					  	$bheight = '72px';
					  } ?>						
						<?php t('input', '', array('type' => 'text', 'name' => 'wpi_banner_height','id' =>'wpi_banner_height','value' => $bheight)); ?>					
					</li>
					<li>
						<label>
							<?php _e('Background position',WPI_META);?>
							<small>default: left top</small>
						</label>
						<?php $bpos = self::option('banner_position');?>
					<?php  if (!$bpos || empty($bpos)){
					  	$bpos = 'left top';
					  } t('input','', array('type' => 'text', 'name' => 'wpi_banner_position','id' =>'wpi_banner_position','value' => $bpos)); ?>					
					</li>					
					<li class="last">
						<label>
							<?php _e('Exclude banner',WPI_META);?>
							<small>default: none</small>
						</label>
						<select name="wpi_banner_na" id="wpi_banner_na" size="2" class="row-4">
					<?php $dbanner = wpi_option('banner_na');
					  if (empty($dbanner)){
					  	$dbanner = 'none';
					  } ?>
					<?php self::htmlOption(array(
					'None' => 'none',
					'Home' => wpiSection::HOME,
					'Single'=> wpiSection::SINGLE,
					'Page'=> wpiSection::PAGE),$dbanner);?>
						</select>
					</li>															
				</ul>
				<?php endif;?>	
		</li>						
		<li>
			<h4>Client Side features</h4>
				<small>Client side script enhancements</small>
			<ul>
				
				<li>
				<label for="client_time_styles"><?php _e('Client Time',WPI_META);?><small>Stylesheets switcher base on visitor time</small></label>
				<?php self::addSelect('client_time_styles',$this->select_options);?>
				</li>	
				<li>
				<label for="wpi_client_date_styles"><?php _e('Client Date',WPI_META);?><small>Client Date scripts</small></label>
				<?php self::addSelect('client_date_styles',$this->select_options);?>
				</li>	
				<li class="last"><?php $prop = self::option('iframe_breaker');?>
					<label for="wpi_iframe_breaker"><?php _e('Frame Breaker',WPI_META);?><small>Disabled client view inside frame or iframe</small>
					</label>
					<?php self::addSelect('iframe_breaker',$this->select_options);?>		
				</li>							
			</ul>
		</li><?php if(function_exists('ImageCreate')): ?>
		<li><?php $prop = self::option('gdfont_image');?>
			<label>
				<?php _e('GD font',WPI_META);?>
				<small>Enable <a href="http://my.php.net/gd">PHP GD</a> text to image replacement</small>
			</label>
				<select name="wpi_gdfont_image" id="wpi_gdfont_image" size="2" class="row-2">
			<?php self::htmlOption(array($this->lang['enabled'] => 1,$this->lang['disabled'] => 0 ),$prop);?>
				</select> 	
				<?php if ($prop && ( ($fonts = wpi_get_fonts()) != false ) ): ?>
					<ul>						
						<li><?php $prop = self::option('gd_blogname'); ?>
							<label for="wpi_gd_blogname"><?php _e('Blog name',WPI_META);?>
							<small>Replace blog name heading with gdfont-image. <abbr title="Search Engine Friendly">SEF</abbr></small>
							</label>
								<select name="wpi_gd_blogname" id="wpi_gd_blogname" size="2" class="row-2">
							<?php self::htmlOption(array(
							$this->lang['enabled'] =>1,
							$this->lang['disabled'] =>0 ),$prop);?>
								</select>
						</li>
						<li>
							<label for="wpi_gd_blogname_text">
								<?php _e('Custom text',WPI_META);?>
								<small>Default heading text: "<?php echo WPI_BLOG_NAME;?>"</small>
							</label>
							<?php $txt = self::option('gd_blogname_text');?>
						<?php  if (''== $txt){
						  	$txt = WPI_BLOG_NAME;
						  } t('input','', array('type' => 'text', 'name' => 'wpi_gd_blogname_text','id' =>'wpi_gd_blogname_text','value' => $txt)); ?>					
						</li>	
						<li><?php $prop = self::option('gd_blogname_font'); ?>
							<label for="wpi_gd_blogname_font"><?php _e('Font face',WPI_META);?>
							<small>Select available font</small>
							</label>
								<select name="wpi_gd_blogname_font" id="wpi_gd_blogname_font" size="2" class="row-4">
								<?php $prop = (empty($prop)) ? $fonts[0] : $prop ?>
								<?php self::htmlOption($fonts,$prop,true);?>
								</select>
						</li>	
						<li>
							<label for="wpi_gd_blogname_color">
								<?php _e('Foreground color',WPI_META);?>
								<small>Hex: #336699, 336699, #369, 369 </small>
							</label>
							<?php $txt = self::option('gd_blogname_color');?>
						<?php  if (!$txt || empty($txt)){
						  	$txt = 'ffffff';
						  } t('input','', array('type' => 'text', 'name' => 'wpi_gd_blogname_color','id' =>'wpi_gd_blogname_color','value' => $txt)); ?>					
						</li>	
						<li class="last">
							<label for="gd_blogname_text_size">
								<?php _e('Pixel size',WPI_META);?>
								<small>Default: 36<sub>px</sub></small>
							</label>
							<?php $txt = self::option('gd_blogname_text_size');?>
						<?php  if (!$txt || empty($txt)){
						  	$txt = 36;
						  } t('input','', array('type' => 'text', 'name' => 'wpi_gd_blogname_text_size','id' =>'wpi_gd_blogname_text_size','value' => $txt)); ?>					
						</li>																											
					</ul>
				<?php elseif(wpi_get_fonts() == false):?>
				<ul>
					<li class="last">
					<p><?php _e('<strong>Whoops</strong> No fonts found',WPI_META);?></p>
					<p><?php _e('Please upload your font at  the following directory <tt>'. WPI_FONTS_DIR.'</tt>',WPI_META);?></p></li>					
				</ul>
				<?php endif;?>
		</li><?php endif; ?>																	
	</ul>	
	<?php self::saveButton();?>
	</div>		
	</li>
	
</ol>
<?php		
	}	
	
	public function optionPlugins()
	{		
?>
<ol class="r mtb options-item">
	<li class="ss"><h4 class="title-" title="toggle view: Plugins">WordPress Hack</h4>
	<div class="dn">
	<ul class="romanNumerals mtb">
		<li>
			<label for="wpi_meta_rsd"><?php _e('RSD Services',WPI_META);?>
				<small>WordPress <a href="http://tales.phrasewise.com/rfc/rsd.html">RSD</a> meta link</small>
			</label>
			<?php self::addSelect('meta_rsd',$this->select_options);?>			
		</li>		
		<li>
			<label for="wpi_meta_livewriter"><?php _e('Manifest link',WPI_META);?><small>Windows LiveWriter</small></label>
			<?php self::addSelect('meta_livewriter',$this->select_options);?>			
		</li>
		<?php if(is_wp_version(2.6, '>=') ):?>
		<li>
			<label for="wpi_nwp_caption"><?php _e('Patch caption',WPI_META);?>
			<small><?php _e('unofficial patch for Wordpress caption shortcode.',WPI_META);?></small></label>
			<?php self::addSelect('nwp_caption',$this->select_options);?>				
		</li>
		<?php endif;?>		
		<li>
			<h4>Feed syndication</h4>
			<ul>
				<li>			
					<label for="wpi_rss_logo"><?php _e('Feed logo')?>
						<small><?php _e('Add custom RSS feed logo.')?></small>
					</label>
					<?php $prop = self::option('rss_logo'); $url = (!empty($prop)) ? $prop : WPI_URL_SLASHIT.'wp-admin/images/logo.gif';?>
					<?php t('input','', array('type'=>'text','name'=>'wpi_rss_logo','id'=>'wpi_rss_logo','value'=> $url )); ?>
				</li>				
				<li class="last">			
					<label for="wpi_rss_delay"><?php _e('Delay RSS feed')?>
						<small><?php _e('Minute to delay RSS feed publication')?></small>
					</label>
					<?php t('input','', array('type'=>'text','name'=>'wpi_rss_delay','id'=>'wpi_rss_delay','value'=> self::option('rss_delay') )); ?>
				</li>				
			</ul>			
		</li>
		<li class="last">
			<label for="wpi_meta_wp_generator"><?php _e('Meta generator',WPI_META);?>
				<small>WordPress version number</small>
			</label>
			<?php self::addSelect('meta_wp_generator',$this->select_options);?>
		</li>		
	</ul>	
	<?php self::saveButton();?>
	</div>		
	</li>
	
</ol>
<?php		
	}		
	
	public static function saveButton()
	{	
		t('p',_t('button',__('Save',WPI_META),
							array('type'=>'submit',
							'name'=>'submitform') ),array('class'=>'cb savebtn'));
	}
	
	public static function option($name)
	{
		return wpi_get_theme_option($name);
	}
	
    
    public static function save($name,$value)
    {
		return wpi_update_theme_options($name,$value);		
	}
    
	public static function htmlOption($args, $selected = 'selected',$textasvalue = false)
	{
		foreach($args as $key => $value){
			$attrib = array();
			
			$attrib['value'] = $value;
			
			if ($selected == $value){
					$attrib['selected'] = 'selected';
			}
			
			$text = ( ( !empty($key) && $key != '') ? $key : $value );
			
			if ($textasvalue){
				$text = $value;
			}
					
			t('option',$text,$attrib);
		}
	}
	
	public static function helpIcon($url='#',$title='help'){
		
		$htm = _t('img','',array('src'=>wpi_img_url('icons/help.gif'),'width'=>11,'height'=>11,'alt'=>'help'));
		$htm = _t('a',$htm,array('href'=>$url,'title'=>$title,'style'=>'cursor:help;text-decoration:none','target'=>'blank'));
		
		echo $htm;
	}
	
	public static function addSelect($id,$options,$prop=false,$textasvalue = false,$class='row-2',$size=2)
	{
		if (!$prop){
			$prop = self::option($id);
		}
?>		
		<select name="wpi_<?php echo $id?>" id="wpi_<?php echo $id?>" size="<?php echo $size;?>" class="<?php echo $class;?>">
			<?php self::htmlOption($options,$prop,$textasvalue);?>
		</select>
<?php				 			
	}
	
	private function __clone(){}
} 

?>