jQuery(window).load(function() {

	jQuery("#home-slider .flexslider").flexslider({
		'controlNav': true,
		'directionNav': false,
		'animation': ThemeOption.slider_effect,
		'direction': ThemeOption.slider_direction,
		'slideshow': Boolean(ThemeOption.slider_autoslide),
		'slideshowSpeed': Number(ThemeOption.slider_speed),
		'animationSpeed': Number(ThemeOption.slider_duration)
	});

	jQuery('#slider-carousel').flexslider({
		animation: "slide",
		animationLoop: true,
		slideshow: false,
		itemWidth: 180,
		asNavFor: '#slider-gallery'
	});

	jQuery('#slider-gallery').flexslider({
		controlNav: false,
		animationLoop: false,
		slideshow: false,
		sync: "#slider-carousel"
	});


	// Isotope
	var items = jQuery('#portfolio-items');
	items.isotope({
		itemSelector : '.portfolio-item'
	});

	// Isotope filtering
	jQuery('#portfolio-filters a').click(function(){
		jQuery(this).siblings().removeClass('active-item');
		jQuery(this).addClass('active-item');
		var selector = jQuery(this).attr('data-filter');
		items.isotope({ filter: selector });
		return false;
	});

	jQuery("#client-array").equalHeights();
	jQuery("#portfolio-items").equalHeights();
	jQuery(".service-array").equalHeights();
	jQuery(".business-info").equalHeights();
	jQuery("#footer-widgets").equalHeights();

});

jQuery(document).ready(function($) {
	// Scroll back to top
	$('.back-top').click(function(){
		$.smoothScroll({
			scrollTarget: '#header',
			speed: 1000
		});
		return false;
	});
	// Main navigation
	$('ul#navigation').superfish({
	    delay:       1000,
	    animation:   {opacity:'show'},
	    speed:       'fast',
	    dropShadows: false
	});
	
	// Responsive Menu
    // Create the dropdown base
    $("<select class='alt-nav' />").appendTo("#nav");

    // Create default option "Go to..."
    $("<option />", {
       "selected": "selected",
       "value"   : "",
       "text"    : "Go to..."
    }).appendTo("#navigation select");

    // Populate dropdown with menu items
    $("#navigation a").each(function() {
     var selected = "";
     var el = $(this);
     var cl = $(this).parents('li').hasClass('current-menu-item');
     if (cl) {
	     $("<option />", { "value": el.attr("href"), "text" : el.text(), "selected": selected }).appendTo("#nav select");
	 }
	 else {
		 $("<option />", { "value": el.attr("href"), "text" : el.text() }).appendTo("#nav select");
	 }    
    });

    $(".alt-nav").change(function() {
      window.location = $(this).find("option:selected").val();
    });

	// FitVids
	$(".format-video .entry-thumb").fitVids();

//	$item.hoverIntent(hconfig);

	if ( $("#map").length ) {
		initialize();
	}

	$(".fancybox").fancybox({
		fitToView	: true
	});
});


function initialize() {
	var myLatlng = new google.maps.LatLng(ThemeOption.map_coords_lat,ThemeOption.map_coords_long);

	var mapOptions = {
		zoom: parseInt(ThemeOption.map_zoom_level),
		center: myLatlng,
		mapTypeId: google.maps.MapTypeId.ROADMAP,
		scrollwheel: false
	};

	var map = new google.maps.Map(document.getElementById('map'), mapOptions);

	var contentString = '<div id="content">'+ThemeOption.map_tooltip+'</div>';

	var infowindow = new google.maps.InfoWindow({
		content: contentString
	});

	var marker = new google.maps.Marker({
		position: myLatlng,
		map: map,
		title: ''
	});
	google.maps.event.addListener(marker, 'click', function() {
		infowindow.open(map,marker);
	});
}
