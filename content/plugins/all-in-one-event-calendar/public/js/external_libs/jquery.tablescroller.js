/*

Copyright (c) 2009 Dimas Begunoff, http://www.farinspace.com

Licensed under the MIT license
http://en.wikipedia.org/wiki/MIT_License

Permission is hereby granted, free of charge, to any person
obtaining a copy of this software and associated documentation
files (the "Software"), to deal in the Software without
restriction, including without limitation the rights to use,
copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the
Software is furnished to do so, subject to the following
conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
OTHER DEALINGS IN THE SOFTWARE.

*/

timely.define(["jquery_timely"],function(e){function n(){if(t)return t;var n=e('<div style="width:50px;height:50px;overflow:hidden;position:absolute;top:-200px;left:-200px;"><div style="height:100px;"></div></div>');e("body").append(n);var r=e("div",n).innerWidth();n.css("overflow-y","auto");var i=e("div",n).innerWidth();return e(n).remove(),t=r-i,t}var t=0;e.fn.tableScroll=function(t){if(t=="undo"){var r=e(this).parent().parent();r.hasClass("tablescroll_wrapper")&&(r.find(".tablescroll_head thead").prependTo(this),r.find(".tablescroll_foot tfoot").appendTo(this),r.before(this),r.empty());return}var i=e.extend({},e.fn.tableScroll.defaults,t);return i.scrollbarWidth=n(),this.each(function(){var t=i.flush,n=e(this).addClass("tablescroll_body"),r=e('<div class="tablescroll_wrapper ai1ec-popover-boundary"></div>').insertBefore(n).append(n);r.parent("div").hasClass(i.containerClass)||e("<div></div>").addClass(i.containerClass).insertBefore(r).append(r);var s=i.width?i.width:n.outerWidth(),o=i.scroll?"auto":"hidden";r.css({width:s+"px",height:i.height+"px",overflow:o}),n.css("width",s+"px");var u=r.outerWidth(),a=u-s;r.css({width:s-a-2+"px"}),n.css("width",s-a-i.scrollbarWidth+"px"),n.outerHeight()<=i.height&&(r.css({height:"auto",width:s-a+"px"}),t=!1);var f=e("thead",n).length?!0:!1,l=e("tfoot",n).length?!0:!1,c=e("thead tr:first",n),h=e("tbody tr:first",n),p=e("tfoot tr:first",n),d=0;e("th, td",c).each(function(t){d=e(this).width(),e("th:eq("+t+"), td:eq("+t+")",c).css("width",d+"px"),e("th:eq("+t+"), td:eq("+t+")",h).css("width",d+"px"),l&&e("th:eq("+t+"), td:eq("+t+")",p).css("width",d+"px")});if(f)var v=e('<table class="tablescroll_head" cellspacing="0"></table>').insertBefore(r).prepend(e("thead",n));if(l)var m=e('<table class="tablescroll_foot" cellspacing="0"></table>').insertAfter(r).prepend(e("tfoot",n));v!=undefined&&(v.css("width",s+"px"),t&&(e("tr:first th:last, tr:first td:last",v).css("width",d+i.scrollbarWidth+"px"),v.css("width",r.outerWidth()+"px"))),m!=undefined&&(m.css("width",s+"px"),t&&(e("tr:first th:last, tr:first td:last",m).css("width",d+i.scrollbarWidth+"px"),m.css("width",r.outerWidth()+"px")))}),this},e.fn.tableScroll.defaults={flush:!0,width:null,height:100,containerClass:"tablescroll",scroll:!0}});