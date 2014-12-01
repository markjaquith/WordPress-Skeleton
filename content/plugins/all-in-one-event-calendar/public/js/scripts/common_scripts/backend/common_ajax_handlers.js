/**
	 * AJAX result after clicking Dismiss in license warning.
	 * @param  {object} response Data returned by HTTP response
	 */

timely.define(["jquery_timely"],function(e){var t=function(t){t&&(typeof t.message!="undefined"?window.alert(t.message):e(".ai1ec-facebook-cron-dismiss-notification").closest(".message").fadeOut())},n=function(t){t.error?window.alert(t.message):e(".ai1ec-dismiss-notification").closest(".message").fadeOut()},r=function(t){t.error?window.alert(t.message):e(".ai1ec-dismiss-intro-video").closest(".message").fadeOut()},i=function(t){t.error?window.alert(t.message):e(".ai1ec-dismiss-license-warning").closest(".message").fadeOut()};return{handle_dismiss_plugins:t,handle_dismiss_notification:n,handle_dismiss_intro_video:r,handle_dismiss_license_warning:i}});