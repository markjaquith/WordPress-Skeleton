var redirectUrlWarning = false;
if (!console)
	console = {
		log : function() {
		}
	}
function disable(identifier) {
	var el = jQuery(identifier);
	console.debug('disabling ' + identifier);
	el.attr('disabled', 'disabled');
}
function enable(identifier) {
	var el = jQuery(identifier);
	console.debug('enabling ' + identifier);
	el.removeAttr('disabled');
}
function hide(identifier) {
	var el = jQuery(identifier);
	console.debug('hiding ' + identifier);
	el.hide("fast");
}
function show(identifier) {
	var el = jQuery(identifier);
	console.debug('showing ' + identifier);
	el.show("fast");
}
function writeable(identifier) {
	var el = jQuery(identifier);
	el.prop("readonly", false);
}
function readonly(identifier) {
	var el = jQuery(identifier);
	el.prop("readonly", true);
}
function hideLoaderIcon() {
	hide('.ajax-loader');
}
function showLoaderIcon() {
	show('.ajax-loader');
}
function handleConfigurationResponse(response) {
	response = response.data;
	if (response.display_auth == 'oauth2') {
		show('p#wizard_oauth2_help');
		jQuery('p#wizard_oauth2_help').html(response.help_text);
		jQuery(postman_redirect_url_el).val(response.redirect_url);
		jQuery('#input_oauth_callback_domain').val(response.callback_domain);
		jQuery('#client_id').html(response.client_id_label);
		jQuery('#client_secret').html(response.client_secret_label);
		jQuery('#redirect_url').html(response.redirect_url_label);
		jQuery('#callback_domain').html(response.callback_domain_label);
	}
}
// add an event on the authentication input field
// on user changing the auth type, determine whether to show
// password or oauth section
jQuery(document).ready(function() {
	jQuery('a#show-diagnostics').click(function() {
		show('#diagnostic-text');
	});
});

// http://www.electrictoolbox.com/toggle-password-field-text-password/
function setupPasswordToggle(passwordFieldId, togglePasswordFieldId) {
	try {
		/**
		 * switch the password field to text, then back to password to see if it
		 * supports changing the field type (IE9+, and all other browsers do).
		 * then switch it back.
		 */
		passwordField = document.getElementById(passwordFieldId);
		for (var i = 0, len = passwordField.value.length; i < len; i++) {
			if (passwordField.value[i] == '*')
				return false;
		}
		passwordField.type = 'text';
		passwordField.type = 'password';

		/**
		 * if it does support changing the field type then add the event handler
		 * and make the button visible. if the browser doesn't support it, then
		 * this is bypassed and code execution continues in the catch() section
		 * below
		 */
		togglePasswordField = document.getElementById(togglePasswordFieldId);
		togglePasswordField.addEventListener('click',
				togglePasswordFieldClicked, false);
		togglePasswordField.style.visibility = 'visible';

		return true;
	}

	catch (err) {
		return true;
	}

}

function togglePasswordFieldClicked() {

	// var passwordField = document.getElementById('passwordField');
	var value = passwordField.value;

	if (passwordField.type == 'password') {
		passwordField.type = 'text';
		togglePasswordField.disabled = true;
	} else {
		// nah, let's not toggle it back
		// passwordField.type = 'password';
	}
	passwordField.value = value;

}

// password toggle
showPassword = false;

function enablePasswordDisplayOnEntry($el1, $el2) {
	// http://stackoverflow.com/questions/1948332/detect-all-changes-to-a-input-type-text-immediately-using-jquery
	console.debug('in enablePasswordDisplayEntryOn');
	jQuery('#' + $el1).each(
			function() {
				var elem = jQuery(this);

				// Save current value of element
				elem.data('oldVal', elem.val());

				// Look for changes in the value
				elem.bind("propertychange change click keyup input paste",
						function(event) {

							// If value has changed...
							if (elem.data('oldVal') != elem.val()) {
								// Updated stored value
								elem.data('oldVal', elem.val());

								if (!showPassword)
									showPassword = setupPasswordToggle($el1,
											$el2);
							}
						});
			});

}

jQuery('body').ajaxStart(function() {
	jQuery(this).css({
		'cursor' : 'wait'
	});
}).ajaxStop(function() {
	jQuery(this).css({
		'cursor' : 'default'
	});
});

function ajaxFailed(response) {
	if (response.responseText) {
		alert(postman_ajax_msg.bad_response + " "
				+ JSON.stringify(response, null, 4));
	}
}

// Production steps of ECMA-262, Edition 5, 15.4.4.18
// Reference: http://es5.github.io/#x15.4.4.18
if (!Array.prototype.forEach) {

	Array.prototype.forEach = function(callback, thisArg) {

		var T, k;

		if (this == null) {
			throw new TypeError(' this is null or not defined');
		}

		// 1. Let O be the result of calling ToObject passing the |this| value
		// as the argument.
		var O = Object(this);

		// 2. Let lenValue be the result of calling the Get internal method of O
		// with the argument "length".
		// 3. Let len be ToUint32(lenValue).
		var len = O.length >>> 0;

		// 4. If IsCallable(callback) is false, throw a TypeError exception.
		// See: http://es5.github.com/#x9.11
		if (typeof callback !== "function") {
			throw new TypeError(callback + ' is not a function');
		}

		// 5. If thisArg was supplied, let T be thisArg; else let T be
		// undefined.
		if (arguments.length > 1) {
			T = thisArg;
		}

		// 6. Let k be 0
		k = 0;

		// 7. Repeat, while k < len
		while (k < len) {

			var kValue;

			// a. Let Pk be ToString(k).
			// This is implicit for LHS operands of the in operator
			// b. Let kPresent be the result of calling the HasProperty internal
			// method of O with argument Pk.
			// This step can be combined with c
			// c. If kPresent is true, then
			if (k in O) {

				// i. Let kValue be the result of calling the Get internal
				// method of O with argument Pk.
				kValue = O[k];

				// ii. Call the Call internal method of callback with T as the
				// this value and
				// argument list containing kValue, k, and O.
				callback.call(T, kValue, k, O);
			}
			// d. Increase k by 1.
			k++;
		}
		// 8. return undefined
	};
}
function postmanValidateAjaxResponse(response) {
	if (response.data == undefined) {
		// handle corrupt response
		jQuery('#postman_test_message_status').html(
				postman_email_test.ajax_error);
		jQuery('#postman_test_message_status').css('color', 'red');
		jQuery('#postman_test_message_error_message').val(
				postman_ajax_msg.corrupt_response + ":\n\n" + response);
		jQuery('li + li').removeClass('disabled');
		return false;
	}
	return true;
}
function postmanValidateAjaxResponseWithPopup(response) {
	if (response.data == undefined) {
		alert(postman_ajax_msg.corrupt_response + ":\n\n"
				+ JSON.stringify(response, null, 4));
		return false;
	}
	return true;
}