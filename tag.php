<?php if( function_exists('get_header') ): get_header();  else: die(42); endif;?>
<?php wpi_section_start('meta-title');?>
<?php wpi_section_end();?>
<?php wpi_section_start('content-top');?>
		<div id="main">
<?php if ( have_posts() ): ?>
<?php wpi_current_template(); ?>
<?php wpi_pagination();?>		
<?php else:?>
<?php wpi_template_nopost(); ?>	
<?php endif;?>
		</div>		
<?php wpi_section_end();?>
<?php if(wpiSidebar::hasWidget(11)): ?>
<?php wpi_section_start('content-mid');?>
		<div id="sidebar-mid">
				<dl class="xoxo w cf">					
					<dd id="sidebar-2" class="cf">
					<?php wpi_dynamic_sidebar(11);?>
					</dd>
				</dl>
		</div>
<?php wpi_section_end();?>
<?php endif;?>
<?php get_footer();?>