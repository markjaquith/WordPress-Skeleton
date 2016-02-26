/*
 * jQuery FlexSlider v2.2.0
 * Copyright 2012 WooThemes
 * Created by ----Shahid
 */
 
jQuery(window).load(function(){
		  jQuery('.flexslider').flexslider({	
			animation: "slide",
			animationSpeed: 1500,
			direction: "horizontal",
			directionNav: true, 
			//prevText: "Previous",          
			//nextText: "Next",
			easing: "swing",  
			
			
			controlNav: true,			
			slideshowSpeed: 3000,
			pauseOnHover: true, 
			slideshow: true,
			start: function(slider){
			  jQuery('body').removeClass('loading');
			}			
		  });
		  
		  
		});