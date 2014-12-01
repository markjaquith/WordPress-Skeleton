/* ========================================================================
 * Bootstrap: alert.js v3.0.3
 * http://getbootstrap.com/javascript/#alerts
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

timely.define(["jquery_timely"],function(e){var t='[data-dismiss="ai1ec-alert"]',n=function(n){e(n).on("click",t,this.close)};n.prototype.close=function(t){function s(){i.trigger("closed.bs.alert").remove()}var n=e(this),r=n.attr("data-target");r||(r=n.attr("href"),r=r&&r.replace(/.*(?=#[^\s]*$)/,""));var i=e(r);t&&t.preventDefault(),i.length||(i=n.hasClass("ai1ec-alert")?n:n.parent()),i.trigger(t=e.Event("close.bs.alert"));if(t.isDefaultPrevented())return;i.removeClass("ai1ec-in"),e.support.transition&&i.hasClass("ai1ec-fade")?i.one(e.support.transition.end,s).emulateTransitionEnd(150):s()};var r=e.fn.alert;e.fn.alert=function(t){return this.each(function(){var r=e(this),i=r.data("bs.alert");i||r.data("bs.alert",i=new n(this)),typeof t=="string"&&i[t].call(r)})},e.fn.alert.Constructor=n,e.fn.alert.noConflict=function(){return e.fn.alert=r,this},e(document).on("click.bs.alert.data-api",t,n.prototype.close)});