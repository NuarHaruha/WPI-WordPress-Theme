
/** 
 * $Id$
 * Restore WPI sidebar filters
 *
 * @package WordPress 
 * @subpackage wp-istalker-chrome
 *
 * @author chaoskaizer
 * @see wpi_grid_sidebar_filter()
 * @param string|int index sidebar id
 * @param string|int col column span 
 */
wpi.gridSidebarFilter = function(index,col)
{
	var widgets = jQuery('#sidebar-'+index+' li.widgets');	
	if (widgets.length >= 0){		
		var m,i;			
		for(m = widgets.length,i=0;i<m;i+=col){			
			var widget,st;	
			widget = jQuery(widgets[i]);
			st = widget.attr('class');			
			if (widget.hasClass('cl') == false){ 
				widget.attr('class',st + ' cl'); 
			}
		}
	}
};