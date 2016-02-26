/** Plugins for Admin Options */
/*!
 * jQuery Cookie Plugin v1.3.1
 * https://github.com/carhartl/jquery-cookie
 *
 * Copyright 2013 Klaus Hartl
 * Released under the MIT license
 */

(function(e){if(typeof define==="function"&&define.amd){define(["jquery"],e)}else{e(jQuery)}})(function(e){function n(e){return e}function r(e){return decodeURIComponent(e.replace(t," "))}function i(e){if(e.indexOf('"')===0){e=e.slice(1,-1).replace(/\\"/g,'"').replace(/\\\\/g,"\\")}try{return s.json?JSON.parse(e):e}catch(t){}}var t=/\+/g;var s=e.cookie=function(t,o,u){if(o!==undefined){u=e.extend({},s.defaults,u);if(typeof u.expires==="number"){var a=u.expires,f=u.expires=new Date;f.setDate(f.getDate()+a)}o=s.json?JSON.stringify(o):String(o);return document.cookie=[encodeURIComponent(t),"=",s.raw?o:encodeURIComponent(o),u.expires?"; expires="+u.expires.toUTCString():"",u.path?"; path="+u.path:"",u.domain?"; domain="+u.domain:"",u.secure?"; secure":""].join("")}var l=s.raw?n:r;var c=document.cookie.split("; ");var h=t?undefined:{};for(var p=0,d=c.length;p<d;p++){var v=c[p].split("=");var m=l(v.shift());var g=l(v.join("="));if(t&&t===m){h=i(g);break}if(!t){h[m]=i(g)}}return h};s.defaults={};e.removeCookie=function(t,n){if(e.cookie(t)!==undefined){e.cookie(t,"",e.extend(n,{expires:-1}));return true}return false}});

/** Custom Code */
(function($){
	
	/** Options Tabs */
	function kamnEasyTwitterFeedWidgetOptionsTabs() {
		
		var relid = $.cookie( 'kamn_easy_twitter_feed_widget_tab_relid' );
		
		if( relid >= 1  ) {
			kamnEasyTwitterFeedWidgetOptionsTabControl( relid );
		} else {
			kamnEasyTwitterFeedWidgetOptionsTabControl( 0 );
		}
		
		$( '.kamn-easy-twitter-feed-widget-group-tab-link-a' ).click( function() {
			
			relid = $(this).attr( 'data-rel' );
			$.cookie( 'kamn_easy_twitter_feed_widget_tab_relid', relid );
			kamnEasyTwitterFeedWidgetOptionsTabControl( relid );		
			
		});
		
	}
	
	function kamnEasyTwitterFeedWidgetOptionsTabControl( relid ) {
		
		$( '.kamn-easy-twitter-feed-widget-group-tab' ).each( function() {
				
			if( $(this).attr( 'id' ) == relid + '_section_group' ) {					
				$(this).delay( 400 ).fadeIn( 1200 );				
			} else{					
				$(this).fadeOut( 'fast' );
			}
			
		});
		
		$( '.kamn-easy-twitter-feed-widget-group-tab-link-li' ).each( function() {
			
			if( $(this).attr('id') != relid + '_section_group_li' && $(this).hasClass( 'active' ) ) {					
				$(this).removeClass( 'active' );				
			}
			
			if( $(this).attr('id') == relid + '_section_group_li' ) {					 
				 $(this).addClass('active');				
			}
		
		});
		
	}
	
	/** jQuery Document Ready */
	$(document).ready(function(){		
		kamnEasyTwitterFeedWidgetOptionsTabs();
	});

})(jQuery);