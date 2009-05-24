<?php
/**
 * @package WordPress
 * @subpackage wp-istalker-chrome
 */
if( function_exists('get_header') ): get_header();  else: die(42); endif;?>
<?php $wpi_nopost = false; ?>
<?php wpi_section_start('content-top');?>
		<div id="main" class="start">		
<?php if ( have_posts() ): ?>
<?php wpi_current_template(); ?>
<?php if(sidebar_has_widgets(2)): ?>
<?php wpi_pagination();?>		
<?php endif; ?>
<?php else:?>
<?php wpi_template_nopost(); ?>	
<?php $wpi_nopost = true; ?>
<?php endif;?>
		</div>
			<div id="sidebar" class="fl cf">
				<dl class="xoxo fl col-1">					
					<dd id="sidebar-1" class="cf">
					<?php wpi_dynamic_sidebar(1);?>
					</dd>
					<?php $classname = (sidebar_has_widgets_array(array(2,3)) ) ? 'span-5 cf fl' : 'cf' ?>
					<?php if (sidebar_has_widgets(2)): ?>
					<dd id="sidebar-2-frontpage-a" class="<?php echo $classname;?>">
					<?php wpi_dynamic_sidebar(2);?>
					</dd>					
					<?php endif;?>
					<?php if (sidebar_has_widgets(3) && ! is_cl_width(1280) ): ?>
					<dd id="sidebar-3-frontpage-a" class="<?php echo $classname;?>">
					<?php wpi_dynamic_sidebar(3);?>
					</dd>					
					<?php endif;?>					
				</dl>
				<?php if (sidebar_has_widgets(3) && is_cl_width(1280) ): ?>
				<dl class="xoxo fl col-2">
					<dd id="sidebar-3-frontpage-a" class="span-5 cf">
					<?php wpi_dynamic_sidebar(3);?>
					</dd>					
				</dl>	
				<?php endif; ?>
			</div>	
<?php wpi_section_end();?>
<?php get_footer();?>