<?php do_action(wpiFilter::ACTION_DOCUMENT_DTD); ?>
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes('xhtml');?>>
<head profile="<?php wpi_head_profile_uri();?>">
<?php do_action('wp_head');?>
</head><?php do_action(wpiFilter::ACTION_FLUSH);?>

<body id="<?php echo wpiTemplate::bodyID();?>" class="<?php wpi_body_class();?>">
<dl id="page" class="hfeed r">
<?php wpi_section_start('nav');?>
<?php do_action(wpiFilter::ACTION_TPL_HEADER); ?>
				<div id="search">
					<form method="get" id="searchform" action="<?php echo WPI_URL_SLASHIT; ?>">		
						<p> 
							<?php wpi_search_box()?>
							<button id="searchsubmit" type="submit" class="rtxt"><?php _e('Search',WPI_META);?>
							</button>
						</p>
					</form>
				</div>
<?php wpi_section_end();?>
<?php wpi_section_start('pathway');?>
				<ul id="pathway-column" class="r cfl">
					<?php if (wpi_option('pathway_enable')):?>
					<li id="breadcrumb">
						<?php wpi_pathway();?>
					</li>
					<?php endif;?>
					<li id="tools">
						<?php wpi_acl_links();?>						
					</li>
				</ul>
<?php wpi_section_end();?>