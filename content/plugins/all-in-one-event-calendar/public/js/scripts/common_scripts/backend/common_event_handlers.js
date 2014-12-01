/**
	 * Dismiss button clicked in invalid license warning.
	 *
	 * @param  {Event} e jQuery event object
	 */

timely.define(["jquery_timely","scripts/common_scripts/backend/common_ajax_handlers"],function(e,t){var n=function(n){var r={action:"ai1ec_facebook_cron_dismiss"};e.post(ajaxurl,r,t.handle_dismiss_plugins,"json")},r=function(n){var r=e(this);r.attr("disabled",!0);var i={action:"ai1ec_disable_notification",note:!1};e.post(ajaxurl,i,t.handle_dismiss_notification)},i=function(n){var r=e(this);r.attr("disabled",!0);var i={action:"ai1ec_disable_intro_video",note:!1};e.post(ajaxurl,i,t.handle_dismiss_intro_video)},s=function(n){var r=e(this);r.attr("disabled",!0);var i={action:"ai1ec_set_license_warning",value:"dismissed"};e.post(ajaxurl,i,t.handle_dismiss_license_warning)},o=function(t){e(this).parent().next(".ai1ec-limit-by-options-container").toggle().find("option").removeAttr("selected")};return{dismiss_plugins_messages_handler:n,dismiss_notification_handler:r,dismiss_intro_video_handler:i,dismiss_license_warning_handler:s,handle_multiselect_containers_widget_page:o}});