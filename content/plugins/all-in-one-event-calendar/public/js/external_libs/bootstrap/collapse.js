/* ========================================================================
 * Bootstrap: collapse.js v3.0.3
 * http://getbootstrap.com/javascript/#collapse
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

timely.define(["jquery_timely"],function(e){var t=function(n,r){this.$element=e(n),this.options=e.extend({},t.DEFAULTS,r),this.transitioning=null,this.options.parent&&(this.$parent=e(this.options.parent)),this.options.toggle&&this.toggle()};t.DEFAULTS={toggle:!0},t.prototype.dimension=function(){var e=this.$element.hasClass("ai1ec-width");return e?"width":"height"},t.prototype.show=function(){if(this.transitioning||this.$element.hasClass("ai1ec-in"))return;var t=e.Event("show.bs.collapse");this.$element.trigger(t);if(t.isDefaultPrevented())return;var n=this.$parent&&this.$parent.find("> .ai1ec-panel > .ai1ec-in");if(n&&n.length){var r=n.data("bs.collapse");if(r&&r.transitioning)return;n.collapse("hide"),r||n.data("bs.collapse",null)}var i=this.dimension();this.$element.removeClass("ai1ec-collapse").addClass("ai1ec-collapsing")[i](0),this.transitioning=1;var s=function(){this.$element.removeClass("ai1ec-collapsing").addClass("ai1ec-in")[i]("auto"),this.transitioning=0,this.$element.trigger("shown.bs.collapse")};if(!e.support.transition)return s.call(this);var o=e.camelCase(["scroll",i].join("-"));this.$element.one(e.support.transition.end,e.proxy(s,this)).emulateTransitionEnd(350)[i](this.$element[0][o])},t.prototype.hide=function(){if(this.transitioning||!this.$element.hasClass("ai1ec-in"))return;var t=e.Event("hide.bs.collapse");this.$element.trigger(t);if(t.isDefaultPrevented())return;var n=this.dimension();this.$element[n](this.$element[n]())[0].offsetHeight,this.$element.addClass("ai1ec-collapsing").removeClass("ai1ec-collapse").removeClass("ai1ec-in"),this.transitioning=1;var r=function(){this.transitioning=0,this.$element.trigger("hidden.bs.collapse").removeClass("ai1ec-collapsing").addClass("ai1ec-collapse")};if(!e.support.transition)return r.call(this);this.$element[n](0).one(e.support.transition.end,e.proxy(r,this)).emulateTransitionEnd(350)},t.prototype.toggle=function(){this[this.$element.hasClass("ai1ec-in")?"hide":"show"]()};var n=e.fn.collapse;e.fn.collapse=function(n){return this.each(function(){var r=e(this),i=r.data("bs.collapse"),s=e.extend({},t.DEFAULTS,r.data(),typeof n=="object"&&n);i||r.data("bs.collapse",i=new t(this,s)),typeof n=="string"&&i[n]()})},e.fn.collapse.Constructor=t,e.fn.collapse.noConflict=function(){return e.fn.collapse=n,this},e(document).on("click.bs.collapse.data-api","[data-toggle=ai1ec-collapse]",function(t){var n=e(this),r,i=n.attr("data-target")||t.preventDefault()||(r=n.attr("href"))&&r.replace(/.*(?=#[^\s]+$)/,""),s=e(i),o=s.data("bs.collapse"),u=o?"toggle":n.data(),a=n.attr("data-parent"),f=a&&e(a);if(!o||!o.transitioning)f&&f.find('[data-toggle=ai1ec-collapse][data-parent="'+a+'"]').not(n).addClass("ai1ec-collapsed"),n[s.hasClass("ai1ec-in")?"addClass":"removeClass"]("ai1ec-collapsed");s.collapse(u)})});