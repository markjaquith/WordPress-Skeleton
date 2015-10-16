jQuery(function()
{
		jQuery('.flexslider').flexslider({	
		animation: slider_settings.animation,
		animationSpeed:slider_settings.animationSpeed,
		direction: slider_settings.slide_direction,
		slideshowSpeed:slider_settings.slideshowSpeed,
		directionNav: true, 
		//prevText: "Previous",          
		//nextText: "Next",
		easing: "swing",
		controlNav: true,
		pauseOnHover: true, 
		slideshow: true,
		start: function(slider){
		jQuery('body').removeClass('loading');
			}			
		  });
		});
