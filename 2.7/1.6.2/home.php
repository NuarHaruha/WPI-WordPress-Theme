<?php if( function_exists('get_header') ): get_header();  else: die(42); endif;?>
<?php wpi_section_start('content-top');?>
		<div id="main" class="start">
<?php if ( have_posts() ): ?>
<?php wpi_current_template(); ?>
<?php if(wpiSidebar::hasWidget(2)): ?>
<?php wpi_pagination();?>		
<?php endif; ?>
<?php else:?>
<?php wpi_template_nopost(); ?>	
<?php endif;?>
		</div>
			<div id="sidebar" class="fl">
				<dl class="xoxo">					
					<dd id="sidebar-1" class="cf">
					<?php wpi_dynamic_sidebar(1);?>
					</dd>
				</dl>
			</div>	
<?php wpi_section_end();?>
<?php if(wpiSidebar::hasWidget(2)): ?>
<?php wpi_section_start('content-mid');?>	
		<div id="sidebar-mid">
				<dl class="xoxo w cf">					
					<dd id="sidebar-2" class="cf">
					<?php wpi_dynamic_sidebar(2);?>
					</dd>
				</dl>
		</div>
<?php wpi_section_end();?>
<?php endif;?>
<?php wpi_section_start('content-bottom');?>
<?php rewind_posts();?>
<?php if ( have_posts() ): ?>
		<div id="main-bottom" class="start">
<?php wpi_template_content_bottom(); ?>
<?php wpi_pagination();?>		
<?php else:?>
<?php wpi_template_nopost(); ?>	
<?php endif;?>
		</div>
		<div id="sidebar-bottom" class="fl">
				<dl class="xoxo">					
					<dd id="sidebar-3" class="cf">
					<?php wpi_dynamic_sidebar(3);?>
					</dd>
				</dl>
		</div>		
<?php wpi_section_end();?>
<?php get_footer();?>