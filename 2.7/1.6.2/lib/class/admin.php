<?php
if (!defined('KAIZEKU')) { die(42); }

class wpiAdmin
{
	const REQUEST_PREFIX = '/^wpi_/';
	
	const OPTIONS_PREFIX = 'wpi_';
	
	public $request;
	
	public $lang = array();
	
	
	public function __construct()
	{
		$this->registerDefaultScript();
		
		$options = array('optionPagesMenu',	'optionAdvance','optionPostLayout',
		'optionWidgets','optionPlugins','optionMisc','optionCache');
		
		foreach($options as $method){
			add_action(wpiFilter::ACTION_THEME_OPTIONS,array($this,$method));
		}
		
		unset($options,$method);
		
		$this->_lang();
			
	}
	
	
	private function _lang()
	{
		$this->lang['enabled'] = __('Enabled',WPI_META);
		$this->lang['disabled'] = __('Disabled',WPI_META);
	}
	
	
	public function registerDefaultScript()
	{
		wp_register_script(WPI_META.'_admin', WPI_THEME_URL.'admin.js',false, '0.1');	
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
				if ($key == 'flush_css'){
					$this->_flushCSS();
				} else {
					self::save($key,$val);
				}     
            }            
        }
        
        unset($request,$key,$val);	
  
    }
    
    private function _flushCSS(){
    	if ( ($css = wpi_get_dir(WPI_CACHE_CSS_DIR)) != false ){
	    	foreach($css as $filename){
	    		$file = WPI_CACHE_CSS_DIR.$filename;
	    		
	    		if (file_exists($file)){
	    			unlink($file);
	    		}
	    	}
    	}
    }
    
	public function printCSS()
	{		
		t('link','',array('rel'=>'stylesheet','href'=>WPI_THEME_URL.'admin.css','type'=>'text/css'));
		if (is_wp_version('2.7') || is_wp_version('2.7-beta1')){
?>
		<style type="text/css">
		    #wpi-theme-options{background-position:253% 0%;margin:0pt !important}
			#wpi-theme-options .main{width:600px}
			#wpi-theme-options .options-item{width:98%}
			#wpi-theme-options .side-panel h2{margin-top:8px}
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
				echo '<div id="message" class="updated fade"><p><strong>'.__('Options saved.',WPI_META).'</strong></p></div>';
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
	
	public function optionCache(){
?>
<ol class="r mtb options-item">
	<li class="ss"><h4 class="title-">
	<?php _e('Manage Cached files',WPI_META);?>
	</h4>
	<div class="dn">
	<ul class="mtb">
		<li class="last">
			<h4><?php _e('Stylesheet',WPI_META);?></h4>
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
			?>
			<small> Cache directory size : <?php echo format_filesize($size);?>
			</small>
			</dl>
			<button class="sbtn" type="submit" name="wpi_flush_css" id="wpi_flush_css" value="1">Erase Cache</button>
			<?php else:?>
			<p>No cached files.</p>
			<?php endif; ?>
			
			
			
		</li>
	</ul>
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
			<label for="wpi_widget_treeview"><?php _e('Category treeview:',WPI_META);?></label>
				<select name="wpi_widget_treeview" id="wpi_widget_treeview" size="2" class="row-2">
			<?php	$prop = self::option('widget_treeview'); 
			self::htmlOption(array(
						$this->lang['enabled'] => 1,
						$this->lang['disabled'] => 0 ),$prop); ?>
				</select>		
		</li>		
		<li class="last">
			<label><?php _e('Similar post:',WPI_META);?></label>
				<select name="wpi_widget_related_post" id="wpi_widget_related_post" size="2" class="row-2">
			<?php	$prop = self::option('widget_related_post'); 
			self::htmlOption(array(
						$this->lang['enabled'] => 1,
						$this->lang['disabled'] => 0 ),$prop); ?>
				</select>	
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
			
		</li><?php if ($menu['pages']): ?><li>
		<label for="wpi_menu_page_exclude">
			<?php _e('Exclude Page',WPI_META); ?>
		</label><?php t('input', '', array('type' => 'text', 'name' => 'wpi_menu_page_exclude','id' =>'wpi_menu_page_exclude','value' => self::option('menu_page_exclude'))); ?>
		<small>separate page id with comma (e.g., 42,101,31,337)</small>			
		</li><?php endif; ?>
	
		<li class="last">		
			<label><?php _e('Pathway:',WPI_META);?></label>
				<select name="wpi_pathway_enable" id="wpi_pathway_enable" size="2" class="row-2">
			<?php self::htmlOption(array(
						$this->lang['enabled'] => 1,
						$this->lang['disabled'] => 0 ),$menu['pathway']); ?>
				</select> 		
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
				<small>Show hreview ratings</small></label>
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
		<li>
			<label>
				<?php _e('Geo positioning',WPI_META);?>
				<small>Geographical coordinates to URL</small>
			</label>
			<?php t('input', '', array('type' => 'text', 'name' => 'wpi_geourl','id' =>'wpi_geourl','value' => self::option('geourl'))); ?>
		</li>					
		<?php if (file_exists(WP_ROOT.DIRSEP.'labels.rdf')) : ?>
		<li class="last">
			<label>
				<?php _e('PICS',WPI_META);?>
				<small>Platform for Internet Content Selection (PICS)</small>
			</label>
			<textarea id="wpi_icra_label" name="wpi_icra_label" style="width:50%"><?php echo stripslashes_deep(self::option('icra_label'));?></textarea>		
		</li>
		<?php endif; ?>
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
		<li><?php $prop = self::option('relative_links');?>
			<label><?php _e('Make links relative',WPI_META);?>
			<small>Increase relative links.</small></label>
				<select name="wpi_relative_links" id="wpi_relative_links" size="2" class="row-2">
			<?php self::htmlOption(array(
						$this->lang['enabled'] => 1,
						$this->lang['disabled'] => 0 ),$prop);?>
				</select> 	
		</li>
		<li><?php $prop = self::option('post_excerpt'); ?>
			<label><?php _e('Show excerpt',WPI_META);?>
				<small>Show excerpt on post</small></label>
				<select name="wpi_post_excerpt" id="wpi_post_excerpt" size="2" class="row-2">
			<?php self::htmlOption(array(
			$this->lang['enabled'] =>1,
			$this->lang['disabled'] =>0 ),$prop);?>
				</select>			
		</li>
		<li><?php $prop = self::option('meta_robots');?>
			<label><?php _e('Meta robots',WPI_META);?>
			<small>Auto append meta robots.</small></label>
				<select name="wpi_meta_robots" id="wpi_meta_robots" size="2" class="row-2">
			<?php self::htmlOption(array(
						$this->lang['enabled'] => 1,
						$this->lang['disabled'] => 0 ),$prop);?>
				</select> 	
		</li>				
		<li><?php $prop = self::option('meta_title');?>
			<label><?php _e('Page title',WPI_META);?>
			<small>Enable custom page title.</small></label>
				<select name="wpi_meta_title" id="wpi_meta_title" size="2" class="row-2">
			<?php self::htmlOption(array(
						$this->lang['enabled'] => 1,
						$this->lang['disabled'] => 0 ),$prop);?>
				</select> 	
		</li>		
		<li><?php $prop = self::option('meta_description');?>
			<label>
				<?php _e('Meta description',WPI_META);?>
				<small>custom meta description.</small>
			</label>
				<select name="wpi_meta_description" id="wpi_meta_description" size="2" class="row-2">
			<?php self::htmlOption(array(
						$this->lang['enabled'] => 1,
						$this->lang['disabled'] => 0 ),$prop);?>
				</select> 	
				<?php if ($prop): ?>
				<ul>
					<li class="last">
						<label for="wpi_def_meta_description">
						<?php _e('Default description',WPI_META);?>	
						</label>
						
			<textarea id="wpi_def_meta_description" name="wpi_def_meta_description" style="width:50%"><?php echo wpi_safe_stripslash(self::option('def_meta_description'));?></textarea>				
				</li>
				</ul>		
				<?php endif; ?>									
		</li>
		<li><?php $prop = self::option('meta_keywords');?>
			<label>
				<?php _e('Meta keywords',WPI_META);?>
				<small>Custom meta keywords</small>
			</label>
				<select name="wpi_meta_keywords" id="wpi_meta_keywords" size="2" class="row-2">
			<?php self::htmlOption(array(
						$this->lang['enabled'] => 1,
						$this->lang['disabled'] => 0 ),$prop);?>
				</select>
				<?php if ($prop): ?>
				<ul>
					<li class="last">
						<label for="wpi_def_meta_keywords">
						<?php _e('Default keywords',WPI_META);?>	
						</label>
						
			<textarea id="wpi_def_meta_keywords" name="wpi_def_meta_keywords" style="width:50%"><?php echo wpi_safe_stripslash(self::option('def_meta_keywords'));?></textarea>				
				</li>
				</ul>		
				<?php endif; ?>					
			
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
		<li class="last"><?php $prop = self::option('client_time_styles');?>
			<label>
				<?php _e('Client time CSS',WPI_META);?>
				<small>Stylesheets switcher</small>
			</label>
				<select name="wpi_client_time_styles" id="wpi_client_time_styles" size="2" class="row-2" disabled="disabled">
			<?php self::htmlOption(array(
						$this->lang['enabled'] => 1,
						$this->lang['disabled'] => 0 ),$prop);?>
				</select> 	
		</li>															
	</ul>	
	<?php self::saveButton();?>
	</div>		
	</li>
	
</ol>
<?php		
	}	
	
	public function optionPlugins()
	{		
		$nwp_caption = self::option('nwp_caption');
?>
<ol class="r mtb options-item">
	<li class="ss"><h4 class="title-" title="toggle view: Plugins">WordPress Hack</h4>
	<div class="dn">
	<ul class="romanNumerals mtb">
		<li><?php $prop = self::option('meta_rsd');?>
			<label>
			<?php _e('RSD Services',WPI_META);?>
			<small>WordPress <a href="http://tales.phrasewise.com/rfc/rsd.html">RSD</a> meta link</small>
			</label>
				<select name="wpi_meta_rsd" id="wpi_meta_rsd" size="2" class="row-2">
			<?php self::htmlOption(array(
						$this->lang['enabled'] => 1,
						$this->lang['disabled'] => 0 ),$prop);?>
				</select>			
		</li>	
		
		<li><?php $prop = self::option('meta_livewriter');?>
			<label><?php _e('Manifest link',WPI_META);?>
			<small>Windows LiveWriter</small>
			</label>
				<select name="wpi_meta_livewriter" id="wpi_meta_livewriter" size="2" class="row-2">
			<?php self::htmlOption(array(
						$this->lang['enabled'] => 1,
						$this->lang['disabled'] => 0 ),$prop);?>
				</select>			
		</li>
		<li><?php $prop = self::option('meta_wp_generator');?>
			<label>
				<?php _e('Meta generator',WPI_META);?>
				<small>WordPress version number</small>
			</label>
				<select name="wpi_meta_wp_generator" id="wpi_meta_wp_generator" size="2" class="row-2">
			<?php self::htmlOption(array(
						$this->lang['enabled'] => 1,
						$this->lang['disabled'] => 0 ),$prop);?>
				</select> 
		</li>	
		<li class="last">
			<label><?php _e('patch caption',WPI_META);?><small>unofficial patch for Wordpress caption shortcode</small></label>
				<select name="wpi_nwp_caption" id="wpi_nwp_caption" size="2" class="row-2">
			<?php self::htmlOption(array(
			__('Enabled',WPI_META)=>1,
			__('Disabled',WPI_META)=>0 ),$nwp_caption);?>
				</select> 	
			
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
	
	private function __clone(){}
} 

?>