var dx = new Date();
// 25th December (christmas) 
if (dx.getDate() == 25 && dx.getMonth() == 11 ){
	jQuery.getScript(wpi.script.url + "snow.js", function(){
	  wpi.snowStorm = new SnowStorm();
	});	
}

// others festival events maybe ...