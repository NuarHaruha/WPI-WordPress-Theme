<?php if( function_exists('get_header') ): get_header();  else: die(42); endif; $section = is_at();?>
<?php if ( $section == wpiSection::YEAR || $section == wpiSection::MONTH || $section == wpiSection::DAY || $section == wpiSection::AUTHOR ||
 $section == wpiSection::PAGE404): ?>
<?php wpi_section_start('meta-title');?>
<?php wpi_section_end();?>
<?php endif;?>
<?php wpi_section_start('content-top');?>
		<div id="main">
<?php if ( have_posts() ): ?>
<?php wpi_current_template(); ?>
<?php wpi_pagination();?>		
<?php else:?>
<?php wpi_template_nopost(); ?>	
<?php endif;?>
		</div>
		<?php if($section == wpiSection::AUTHOR):?>
		<div id="sidebar" class="fl">
				<dl class="xoxo cf">					
					<dd id="sidebar-1" class="cf">
					<?php wpi_dynamic_sidebar(13);?>
					</dd>
					<?php $classname = (sidebar_has_widgets_array(array(14,15)) ) ? 'span-5 cf fl' : 'cf' ?>
					<?php if (sidebar_has_widgets(14)): ?>
					<dd id="sidebar-2-author" class="<?php echo $classname;?>">
					<?php wpi_dynamic_sidebar(14);?>
					</dd>					
					<?php endif;?>
					<?php if (sidebar_has_widgets(15)): ?>
					<dd id="sidebar-3-author" class="<?php echo $classname;?>">
					<?php wpi_dynamic_sidebar(15);?>
					</dd>					
					<?php endif;?>					
				</dl>
		</div>		
		<?php endif;?>		
<?php wpi_section_end();?>
<?php get_footer();?>