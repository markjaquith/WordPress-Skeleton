/* ========================================================================
 * Bootstrap: button.js v3.0.3
 * http://getbootstrap.com/javascript/#buttons
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

timely.define(["jquery_timely"],function(e){var t=function(n,r){this.$element=e(n),this.options=e.extend({},t.DEFAULTS,r)};t.DEFAULTS={loadingText:"loading..."},t.prototype.setState=function(e){var t="disabled",n=this.$element,r=n.is("input")?"val":"html",i=n.data();e+="Text",i.resetText||n.data("resetText",n[r]()),n[r](i[e]||this.options[e]),setTimeout(function(){e=="loadingText"?n.addClass("ai1ec-"+t).attr(t,t):n.removeClass("ai1ec-"+t).removeAttr(t)},0)},t.prototype.toggle=function(){var e=this.$element.closest('[data-toggle="ai1ec-buttons"]'),t=!0;if(e.length){var n=this.$element.find("input");n.prop("type")==="radio"&&(n.prop("checked")&&this.$element.hasClass("ai1ec-active")?t=!1:e.find(".ai1ec-active").removeClass("ai1ec-active")),t&&n.prop("checked",!this.$element.hasClass("ai1ec-active")).trigger("change")}t&&this.$element.toggleClass("ai1ec-active")};var n=e.fn.button;e.fn.button=function(n){return this.each(function(){var r=e(this),i=r.data("bs.button"),s=typeof n=="object"&&n;i||r.data("bs.button",i=new t(this,s)),n=="toggle"?i.toggle():n&&i.setState(n)})},e.fn.button.Constructor=t,e.fn.button.noConflict=function(){return e.fn.button=n,this},e(document).on("click.bs.button.data-api","[data-toggle^=ai1ec-button]",function(t){var n=e(t.target);n.hasClass("ai1ec-btn")||(n=n.closest(".ai1ec-btn")),n.button("toggle"),t.preventDefault()})});