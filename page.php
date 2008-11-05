<?php if( function_exists('get_header') ): get_header();  else: die(42); endif;?>
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
					<?php wpi_dynamic_sidebar(7);?>
					</dd>
					<?php $classname = (sidebar_has_widgets_array(array(8,9)) ) ? 'span-5 cf fl' : 'cf' ?>
					<?php if (sidebar_has_widgets(8)): ?>
					<dd id="sidebar-2-page" class="<?php echo $classname;?>">
					<?php wpi_dynamic_sidebar(8);?>
					</dd>					
					<?php endif;?>
					<?php if (sidebar_has_widgets(9)): ?>
					<dd id="sidebar-3-page" class="<?php echo $classname;?>">
					<?php wpi_dynamic_sidebar(9);?>
					</dd>					
					<?php endif;?>					
				</dl>
		</div>	
<?php wpi_section_end();?>
<?php comments_template(); ?>
<?php get_footer();?>