/*!
 * jQuery.ScrollTo
 * Copyright (c) 2007-2012 Ariel Flesler - aflesler(at)gmail(dot)com | http://flesler.blogspot.com
 * Dual licensed under MIT and GPL.
 * Date: 12/14/2012
 *
 * @projectDescription Easy element scrolling using jQuery.
 * http://flesler.blogspot.com/2007/10/jqueryscrollto.html
 * @author Ariel Flesler
 * @version 1.4.5 BETA
 *
 * @id jQuery.scrollTo
 * @id jQuery.fn.scrollTo
 * @param {String, Number, DOMElement, jQuery, Object} target Where to scroll the matched elements.
 *	  The different options for target are:
 *		- A number position (will be applied to all axes).
 *		- A string position ('44', '100px', '+=90', etc ) will be applied to all axes
 *		- A jQuery/DOM element ( logically, child of the element to scroll )
 *		- A string selector, that will be relative to the element to scroll ( 'li:eq(2)', etc )
 *		- A hash { top:x, left:y }, x and y can be any kind of number/string like above.
 *		- A percentage of the container's dimension/s, for example: 50% to go to the middle.
 *		- The string 'max' for go-to-end.
 * @param {Number, Function} duration The OVERALL length of the animation, this argument can be the settings object instead.
 * @param {Object,Function} settings Optional set of settings or the onAfter callback.
 *	 @option {String} axis Which axis must be scrolled, use 'x', 'y', 'xy' or 'yx'.
 *	 @option {Number, Function} duration The OVERALL length of the animation.
 *	 @option {String} easing The easing method for the animation.
 *	 @option {Boolean} margin If true, the margin of the target element will be deducted from the final position.
 *	 @option {Object, Number} offset Add/deduct from the end position. One number for both axes or { top:x, left:y }.
 *	 @option {Object, Number} over Add/deduct the height/width multiplied by 'over', can be { top:x, left:y } when using both axes.
 *	 @option {Boolean} queue If true, and both axis are given, the 2nd axis will only be animated after the first one ends.
 *	 @option {Function} onAfter Function to be called after the scrolling ends.
 *	 @option {Function} onAfterFirst If queuing is activated, this function will be called after the first scrolling ends.
 * @return {jQuery} Returns the same jQuery object, for chaining.
 *
 * @desc Scroll to a fixed position
 * @example $('div').scrollTo( 340 );
 *
 * @desc Scroll relatively to the actual position
 * @example $('div').scrollTo( '+=340px', { axis:'y' } );
 *
 * @desc Scroll using a selector (relative to the scrolled element)
 * @example $('div').scrollTo( 'p.paragraph:eq(2)', 500, { easing:'swing', queue:true, axis:'xy' } );
 *
 * @desc Scroll to a DOM element (same for jQuery object)
 * @example var second_child = document.getElementById('container').firstChild.nextSibling;
 *			$('#container').scrollTo( second_child, { duration:500, axis:'x', onAfter:function(){
 *				alert('scrolled!!');
 *			}});
 *
 * @desc Scroll on both axes, to different values
 * @example $('div').scrollTo( { top: 300, left:'+=200' }, { axis:'xy', offset:-20 } );
 */

timely.define("external_libs/jquery.scrollTo",["jquery_timely"],function(e){function n(e){return typeof e=="object"?e:{top:e,left:e}}var t=e.scrollTo=function(t,n,r){e(window).scrollTo(t,n,r)};t.defaults={axis:"xy",duration:parseFloat(e.fn.jquery)>=1.3?0:1,limit:!0},t.window=function(t){return e(window)._scrollable()},e.fn._scrollable=function(){return this.map(function(){var t=this,n=!t.nodeName||e.inArray(t.nodeName.toLowerCase(),["iframe","#document","html","body"])!=-1;if(!n)return t;var r=(t.contentWindow||t).document||t.ownerDocument||t;return/webkit/i.test(navigator.userAgent)||r.compatMode=="BackCompat"?r.body:r.documentElement})},e.fn.scrollTo=function(r,i,s){return typeof i=="object"&&(s=i,i=0),typeof s=="function"&&(s={onAfter:s}),r=="max"&&(r=9e9),s=e.extend({},t.defaults,s),i=i||s.duration,s.queue=s.queue&&s.axis.length>1,s.queue&&(i/=2),s.offset=n(s.offset),s.over=n(s.over),this._scrollable().each(function(){function h(e){u.animate(l,i,s.easing,e&&function(){e.call(this,r,s)})}if(r==null)return;var o=this,u=e(o),a=r,f,l={},c=u.is("html,body");switch(typeof a){case"number":case"string":if(/^([+-]=?)?\d+(\.\d+)?(px|%)?$/.test(a)){a=n(a);break}a=e(a,this);if(!a.length)return;case"object":if(a.is||a.style)f=(a=e(a)).offset()}e.each(s.axis.split(""),function(e,n){var r=n=="x"?"Left":"Top",i=r.toLowerCase(),p="scroll"+r,d=o[p],v=t.max(o,n);if(f)l[p]=f[i]+(c?0:d-u.offset()[i]),s.margin&&(l[p]-=parseInt(a.css("margin"+r))||0,l[p]-=parseInt(a.css("border"+r+"Width"))||0),l[p]+=s.offset[i]||0,s.over[i]&&(l[p]+=a[n=="x"?"width":"height"]()*s.over[i]);else{var m=a[i];l[p]=m.slice&&m.slice(-1)=="%"?parseFloat(m)/100*v:m}s.limit&&/^\d+$/.test(l[p])&&(l[p]=l[p]<=0?0:Math.min(l[p],v)),!e&&s.queue&&(d!=l[p]&&h(s.onAfterFirst),delete l[p])}),h(s.onAfter)}).end()},t.max=function(t,n){var r=n=="x"?"Width":"Height",i="scroll"+r;if(!e(t).is("html,body"))return t[i]-e(t)[r.toLowerCase()]();var s="client"+r,o=t.ownerDocument.documentElement,u=t.ownerDocument.body;return Math.max(o[i],u[i])-Math.min(o[s],u[s])}}),timely.define("scripts/calendar/event",["jquery_timely","external_libs/jquery.scrollTo"],function(e){var t=function(t){t.preventDefault(),e("div.ai1ec-popover").remove();var n="jsonp",r=e(this).closest(".timely-calendar"),s={request_type:n,ai1ec_doing_ajax:!0,ai1ec:i(r)};e.ajax({url:e(this).attr("href"),dataType:n,data:s,method:"get",success:function(n){e(t.target).closest("#ai1ec-calendar-view").html(n.html);var i=e(".ai1ec-calendar-link").attr("href"),s=e(t.target).closest(".timely-calendar").data("action");s&&(i=i+"action~"+s+"/"),e.scrollTo(r,1e3,{offset:{left:0,top:-100}}),timely.require(["pages/event"])}})},n=function(e,t,n,i){var s=r(e),o=n.data(s);return o===undefined?t:(i?t.push(o):t.push(e+"~"+o),t)},r=function(e){return e.replace(/\W+(.)/g,function(e,t){return t.toUpperCase()})},i=function(t){var r=e(t),i=[];return i=n("action",i,r),i=n("cat_ids",i,r),i=n("auth_ids",i,r),i=n("tag_ids",i,r),i=n("exact_date",i,r),i=n("no_navigation",i,r),i.join("|")};return{load_event_through_jsonp:t,create_ai1ec_to_send:i}});