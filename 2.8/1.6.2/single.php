<?php if( function_exists('get_header') ): get_header();  else: die(); endif;?>
<?php wpi_section_start('meta-title');?>
<?php wpi_section_end();?>
<?php wpi_section_start('content-top');?>
		<div id="main">
<?php if ( have_posts() ): ?>
<?php wpi_current_template(); ?>	
<?php else:?>
<?php wpi_template_nopost(); ?>	
<?php endif;?>
		</div>
		<div id="sidebar" class="fl">
				<dl class="xoxo cf">					
					<dd id="sidebar-1" class="cf">
					<?php wpi_dynamic_sidebar(4);?>
					</dd>
					<?php $classname = (sidebar_has_widgets_array(array(5,6)) ) ? 'span-5 cf fl' : 'cf' ?>
					<?php if (sidebar_has_widgets(5)): ?>
					<dd id="sidebar-2-single" class="<?php echo $classname;?>">
					<?php wpi_dynamic_sidebar(5);?>
					</dd>					
					<?php endif;?>
					<?php if (sidebar_has_widgets(6)): ?>
					<dd id="sidebar-3-single" class="<?php echo $classname;?>">
					<?php wpi_dynamic_sidebar(6);?>
					</dd>					
					<?php endif;?>					
				</dl>
		</div>		
<?php wpi_section_end();?>
<?php comments_template(); ?>
<?php get_footer();?>