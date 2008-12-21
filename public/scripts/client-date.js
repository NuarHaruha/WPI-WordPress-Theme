/**
 * $Id$
 * WPI client date scripts 
 *  
 */
wpi.date = new Date();
// 25th December (christmas) 
if (wpi.date.getDate() == 25 && wpi.date.getMonth() == 11 ){
	jQuery.getScript(wpi.theme_url + "snow.js", function(){
	  snowStorm=new SnowStorm();
	});	
}

// others festive events drop it here ...