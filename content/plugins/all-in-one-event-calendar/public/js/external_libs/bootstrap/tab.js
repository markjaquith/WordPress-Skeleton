/* ========================================================================
 * Bootstrap: tab.js v3.0.3
 * http://getbootstrap.com/javascript/#tabs
 * ========================================================================
 * Copyright 2013 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ======================================================================== */

timely.define(["jquery_timely"],function(e){var t=function(t){this.element=e(t)};t.prototype.show=function(){var t=this.element,n=t.closest("ul:not(.ai1ec-dropdown-menu)"),r=t.data("target");r||(r=t.attr("href"),r=r&&r.replace(/.*(?=#[^\s]*$)/,""));if(t.parent("li").hasClass("ai1ec-active"))return;var i=n.find(".ai1ec-active:last a")[0],s=e.Event("show.bs.tab",{relatedTarget:i});t.trigger(s);if(s.isDefaultPrevented())return;var o=e(r);this.activate(t.parent("li"),n),this.activate(o,o.parent(),function(){t.trigger({type:"shown.bs.tab",relatedTarget:i})})},t.prototype.activate=function(t,n,r){function o(){i.removeClass("ai1ec-active").find("> .ai1ec-dropdown-menu > .ai1ec-active").removeClass("ai1ec-active"),t.addClass("ai1ec-active"),s?(t[0].offsetWidth,t.addClass("ai1ec-in")):t.removeClass("ai1ec-fade"),t.parent(".ai1ec-dropdown-menu")&&t.closest("li.ai1ec-dropdown").addClass("ai1ec-active"),r&&r()}var i=n.find("> .ai1ec-active"),s=r&&e.support.transition&&i.hasClass("ai1ec-fade");s?i.one(e.support.transition.end,o).emulateTransitionEnd(150):o(),i.removeClass("ai1ec-in")};var n=e.fn.tab;e.fn.tab=function(n){return this.each(function(){var r=e(this),i=r.data("bs.tab");i||r.data("bs.tab",i=new t(this)),typeof n=="string"&&i[n]()})},e.fn.tab.Constructor=t,e.fn.tab.noConflict=function(){return e.fn.tab=n,this},e(document).on("click.bs.tab.data-api",'[data-toggle="ai1ec-tab"], [data-toggle="ai1ec-pill"]',function(t){t.preventDefault(),e(this).tab("show")})});