var THEMEVISION = THEMEVISION || {};

(function($) {
	
	"use strict"
	
	THEMEVISION.initialize = {

		init: function(){
			
			THEMEVISION.initialize.responsiveClasses();
			THEMEVISION.initialize.goToTop();
			
		},
		
		responsiveClasses: function(){
			var jRes = jRespond([
				{
					label: 'smallest',
					enter: 0,
					exit: 479
				},{
					label: 'handheld',
					enter: 480,
					exit: 767
				},{
					label: 'tablet',
					enter: 768,
					exit: 991
				},{
					label: 'laptop',
					enter: 992,
					exit: 1199
				},{
					label: 'desktop',
					enter: 1200,
					exit: 10000
				}
			]);
			jRes.addFunc([
				{
					breakpoint: 'desktop',
					enter: function() { $body.addClass('device-lg'); },
					exit: function() { $body.removeClass('device-lg'); }
				},{
					breakpoint: 'laptop',
					enter: function() { $body.addClass('device-md'); },
					exit: function() { $body.removeClass('device-md'); }
				},{
					breakpoint: 'tablet',
					enter: function() { $body.addClass('device-sm'); },
					exit: function() { $body.removeClass('device-sm'); }
				},{
					breakpoint: 'handheld',
					enter: function() { $body.addClass('device-xs'); },
					exit: function() { $body.removeClass('device-xs'); }
				},{
					breakpoint: 'smallest',
					enter: function() { $body.addClass('device-xxs'); },
					exit: function() { $body.removeClass('device-xxs'); }
				}
			]);
		},
		
		goToTop: function(){
			$goToTopEl.click(function() {
				$('body,html').stop(true).animate({scrollTop:0},400);
				return false;
			});
		},
		
		goToTopScroll: function(){
			if( $body.hasClass('device-lg') || $body.hasClass('device-md') || $body.hasClass('device-sm') ) {
				if($window.scrollTop() > 450) {
					$goToTopEl.fadeIn();
				} else {
					$goToTopEl.fadeOut();
				}
			}
		}
	};
	
	THEMEVISION.header = {
		
		init: function() {
			
			THEMEVISION.header.superfish();
			THEMEVISION.header.mobilemenu();
			
		},
		
		superfish: function() {
			
			jQuery('.sticky-nav').superfish({
				popUpSelector: 'ul',
				delay: 250,
				speed: 350,
				animation: {opacity:'show',height:'show'},
				animationOut:  {opacity:'hide',height:'hide'},
				cssArrows: false
			});
			
			jQuery('.top-nav-menu').superfish({
				popUpSelector: 'ul',
				delay: 250,
				speed: 350,
				animation: {opacity:'show',height:'show'},
				animationOut:  {opacity:'hide',height:'hide'},
				cssArrows: false
			});
			
			jQuery('.nav-menu').superfish({
				popUpSelector: 'ul',
				delay: 250,
				speed: 350,
				animation: {opacity:'show',height:'show'},
				animationOut:  {opacity:'hide',height:'hide'},
				cssArrows: false
			});
			
		},
		
		topsocial: function(){
			if( $topSocialEl.length > 0 ){
				if( $body.hasClass('device-md') || $body.hasClass('device-lg') ) {
					$topSocialEl.show();
					$topSocialEl.find('a').css({width: 40});

					$topSocialEl.find('.tv-text').each( function(){
						var $clone = $(this).clone().css({'visibility': 'hidden', 'display': 'inline-block', 'font-size': '13px', 'font-weight':'bold'}).appendTo($body),
							cloneWidth = $clone.innerWidth() + 52;
						$(this).parent('a').attr('data-hover-width',cloneWidth);
						$clone.remove();
					});

					$topSocialEl.find('a').hover(function() {
						if( $(this).find('.tv-text').length > 0 ) {
							$(this).css({width: $(this).attr('data-hover-width')});
						}
					}, function() {
						$(this).css({width: 40});
					});
				} else {
					$topSocialEl.show();
					$topSocialEl.find('a').css({width: 40});

					$topSocialEl.find('a').each(function() {
						var topIconTitle = $(this).find('.tv-text').text();
						$(this).attr('title', topIconTitle);
					});

					$topSocialEl.find('a').hover(function() {
						$(this).css({width: 40});
					}, function() {
						$(this).css({width: 40});
					});

					if( $body.hasClass('device-xxs') ) {
						$topSocialEl.hide();
						$topSocialEl.slice(0, 8).show();
					}
				}
			}
		},
		
		mobilemenu: function(){
			
			$('.mobile-nav-icons .fa-bars').click(function() {
				$('.mobile-nav-menu').slideToggle();
			});
			
		}
		
	};
	
	THEMEVISION.extras = {
		
		init: function(){
			
			THEMEVISION.extras.tipsntabs();
			THEMEVISION.extras.customclasses();
			THEMEVISION.extras.bbPress();
			THEMEVISION.extras.contact7form();
			
		},
		
		tipsntabs: function(){
			
			$('[data-toggle="tooltip"]').tooltip();
  
			$('#tabs a:first').tab('show'); // Show first tab by default
		  
			$('#tabs a').click(function (e) {
				e.preventDefault()
				$(this).tab('show');
			})
			
		},
		
		customclasses: function(){
			
			$('a.comment-reply-link').append('<i class="fa fa-reply"></i>');
			
		},
		
		bbPress: function(){
			
			$('#bbp_search').addClass('sm-form-control');
			$('#bbp_topic_title').addClass('sm-form-control');
			
		},
		
		contact7form: function() {
			
			$('.wpcf7-form-control').css('width', 'auto');
			$('.wpcf7-form-control').addClass('sm-form-control');
			$('.wpcf7-submit').removeClass('sm-form-control');
			
		}
		
	};
	
	THEMEVISION.isMobile = {
		Android: function() {
			return navigator.userAgent.match(/Android/i);
		},
		BlackBerry: function() {
			return navigator.userAgent.match(/BlackBerry/i);
		},
		iOS: function() {
			return navigator.userAgent.match(/iPhone|iPad|iPod/i);
		},
		Opera: function() {
			return navigator.userAgent.match(/Opera Mini/i);
		},
		Windows: function() {
			return navigator.userAgent.match(/IEMobile/i);
		},
		any: function() {
			return (THEMEVISION.isMobile.Android() || THEMEVISION.isMobile.BlackBerry() || THEMEVISION.isMobile.iOS() || THEMEVISION.isMobile.Opera() || THEMEVISION.isMobile.Windows());
		}
	};
	
	// Document on resize
	THEMEVISION.documentOnResize = {
		
		init: function(){
			
			var t = setTimeout( function(){
				THEMEVISION.header.topsocial();
			}, 500 );
			
		}
		
	};
	
	// Document on ready
	THEMEVISION.documentOnReady = {
		
		init: function(){
			
			THEMEVISION.initialize.init();
			THEMEVISION.header.init();
			THEMEVISION.extras.init();
			THEMEVISION.documentOnReady.windowscroll();
			
		},
		
		windowscroll: function(){
			
			$window.on( 'scroll', function(){
				
				// Go To Top
				THEMEVISION.initialize.goToTopScroll();
				
				// Sticky Header Class
				if(jQuery(this).scrollTop() > 1){ 
					
					// If sticky header & top navigation enabled
					if( agama.sticky_header == 'sticky' && agama.top_navigation ) {
						
						$body.addClass("top-bar-out");
						$topbar.hide();
					}
					
					$stickyheader.addClass("sticky-header-shrink");
					
				}else{
					
					// If sticky header & top navigation enabled
					if( agama.sticky_header == 'sticky' && agama.top_navigation ) {
						$body.removeClass("top-bar-out");
						$topbar.show();
					}
					
					$stickyheader.removeClass("sticky-header-shrink");
				}

			});
			
		}
		
	};
	
	// Document on load
	THEMEVISION.documentOnLoad = {
		
		init: function(){
			
			THEMEVISION.header.topsocial();
		
		}
		
	};
	
	var $window	 		= $(window),
		$document		= $(document),
		$body	 		= $('body'),
		$topbar			= $('#top-bar'),
		$header			= $('#masthead'),
		$stickyheader 	= $('.sticky-header'),
		$topSocialEl 	= $('#top-social').find('li'),
		$goToTopEl		= $('#toTop');
		
	$(document).ready( THEMEVISION.documentOnReady.init );
	$window.load( THEMEVISION.documentOnLoad.init );
	$window.on( 'resize', THEMEVISION.documentOnResize.init );
	
})(jQuery);