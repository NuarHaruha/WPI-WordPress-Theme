jQuery(document).ready( function(){
	var wpi= {};
	wpi.toggle = function(elm){jQuery(elm).animate(
		{"height":"toggle","opacity":"toggle"},{duration:550});
	};
	jQuery('.title-').click(function(){
		wpi.toggle(jQuery(this).next());
	}); 
	if (adminMenu != 'undefined') adminMenu.fold();
});