jQuery(document).ready( function(){
	wpi.toggle = function(elm){jQuery(elm).animate({"height":"toggle","opacity":"toggle"},{duration:550});};
	
	wpi.htoggle = function(elm){jQuery(elm).animate({"width":"toggle","opacity":"toggle"},{duration:550});};
	
	jQuery('.title-').click(function(){wpi.toggle(jQuery(this).next());});
	
	jQuery('.widget-title').click(function(){wpi.toggle(jQuery(this).next());});
	
	jQuery('a.hreviewer-microid').click(function(){wpi.toggle(this.hash);return false;});
	
	if(jQuery('#archives').length > 0){ jQuery('#archives .widget-content ul').addClass('select-odd');};
	
	jQuery('input.on-click-select').click(function(){this.select();});
	
	jQuery('.ttip').tooltip({track: true,delay: 0,showURL: false,showBody:" | ", fade:150});
	
	jQuery("a[rel^='external']").tooltip({track: true,delay: 0,showURL: true,showBody:" | ", fade:150});
	
	jQuery('.htitle-').click(function(){wpi.htoggle(jQuery(this).next());});
	
	jQuery('.show-slow').click(function(){jQuery(this).next().show("slow");jQuery(this).hide("fast")});
	
	wpi.ftsize=function(e,s){jQuery(e).click(function(){jQuery(this.hash).css({fontSize:s});return false;})};
	wpi.ftsize('#font',"1.4em");wpi.ftsize('#font-',"1em");
	
	jQuery('.entry-content .toggle-content').each(function(){if (!jQuery(this).hasClass("expand")){ jQuery(jQuery(this).next()).css({paddingLeft:"13px"}).hide();} else { jQuery(jQuery(this).next()).css({paddingLeft:"13px"});}});
	
	jQuery('.toggle-content').click(function(){wpi.toggle(jQuery(this).next());jQuery(this).toggleClass("expand");});$('.top').click(function(){$('#nav').ScrollTo(800); return false;});
	
	jQuery('a.scroll-to').click(function(){ jQuery(this.hash).ScrollTo(800);return false;});
	
	if (jQuery('.thickbox').length > 0){			
		jQuery.getScript(wpi.theme_url + "thickbox.js", function(){
			
		});
	}
});