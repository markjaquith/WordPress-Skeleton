jQuery(document).ready(function(){// Hide (Collapse) the toggle containers on load
	
	jQuery("tbody.toggle_container tr.event").hide();
	
		//Slide up and down on click
	jQuery("tbody.toggle_container .trigger").click(function(){
		jQuery(this).siblings("tr.event").show(); /*slideToggle("slow"); */
	});
	
			//Slide up and down on click
	jQuery("tbody.toggle_container .trigger").toggle(function(){
		jQuery(this).siblings("tr.event").show(); 
		},function () {
		jQuery(this).siblings("tr.event").hide(); 
	});
	
	//Switch the "Open" and "Close" state per click
	jQuery("tbody.toggle_container .trigger").toggle(function(){
		jQuery(this).addClass("active");
		}, function () {
		jQuery(this).removeClass("active");
	});	


	//Slide up and down on click
	jQuery("#expandall").click(function(){
			jQuery("#events_wrap tbody.toggle_container tr.event").show(); // just to test
		
	});
	//Slide up and down on click
	jQuery("#hideall").click(function(){
			jQuery("#events_wrap tbody.toggle_container tr.event").hide(); // just to test
		
	});	

	jQuery("#expandall").toggle(function(){
		jQuery(this).addClass("active");
		}, function () {
		jQuery(this).removeClass("active");
	});
	
	jQuery("#hideall").toggle(function(){
		jQuery(this).addClass("active");
		}, function () {
		jQuery(this).removeClass("active");
	});

})
