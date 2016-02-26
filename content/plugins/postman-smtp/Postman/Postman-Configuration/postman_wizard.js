var transports = [];

connectivtyTestResults = {};
portTestInProgress = false;

/**
 * Functions to run on document load
 */
jQuery(document).ready(function() {
	jQuery(postman_input_sender_email).focus();
	initializeJQuerySteps();
	// add an event on the plugin selection
	jQuery('input[name="input_plugin"]').click(function() {
		getConfiguration();
	});

	// add an event on the transport input field
	// when the user changes the transport, determine whether
	// to show or hide the SMTP Settings
	jQuery('select#input_transport_type').change(function() {
		hide('#wizard_oauth2_help');
		reloadOauthSection();
		switchBetweenPasswordAndOAuth();
	});

});

function checkGoDaddyAndCheckEmail(email) {
	hide('#godaddy_block');
	hide('#godaddy_spf_required');
	// are we hosted on GoDaddy? check.
	var data = {
		'action' : 'postman_wizard_port_test',
		'hostname' : 'relay-hosting.secureserver.net',
		'port' : 25,
		'timeout' : 3
	};
	goDaddy = 'unknown';
	checkedEmail = false;
	jQuery.post(ajaxurl, data, function(response) {
		if (postmanValidateAjaxResponseWithPopup(response)) {
			checkEmail(response.success, email);
		}
	}).fail(function(response) {
		ajaxFailed(response);
	});
}

function checkEmail(goDaddyHostDetected, email) {
	var data = {
		'action' : 'postman_check_email',
		'go_daddy' : goDaddyHostDetected,
		'email' : email
	};
	jQuery.post(
			ajaxurl,
			data,
			function(response) {
				if (postmanValidateAjaxResponseWithPopup(response)) {
					checkedEmail = true;
					smtpDiscovery = response.data;
					if (response.data.hostname != null
							&& response.data.hostname) {
						jQuery(postman_hostname_element_name).val(
								response.data.hostname);
					}
					enableSmtpHostnameInput(goDaddyHostDetected);
				}
			}).fail(function(response) {
		ajaxFailed(response);
	});
}

function enableSmtpHostnameInput(goDaddyHostDetected) {
	if (goDaddyHostDetected && !smtpDiscovery.is_google) {
		// this is a godaddy server and we are using a godaddy smtp server
		// (gmail excepted)
		if (smtpDiscovery.is_go_daddy) {
			// we detected GoDaddy, and the user has entered a GoDaddy hosted
			// email
		} else if (smtpDiscovery.is_well_known) {
			// this is a godaddy server but the SMTP must be the email
			// service
			show('#godaddy_block');
		} else {
			// this is a godaddy server and we're using a (possibly) custom
			// domain
			show('#godaddy_spf_required');
		}
	}
	enable('#input_hostname');
	jQuery('li').removeClass('disabled');
	hideLoaderIcon();
}

/**
 * Initialize the Steps wizard
 */
function initializeJQuerySteps() {
	jQuery("#postman_wizard").steps(
			{
				bodyTag : "fieldset",
				headerTag : "h5",
				transitionEffect : "slideLeft",
				stepsOrientation : "vertical",
				autoFocus : true,
				startIndex : parseInt(postman_setup_wizard.start_page),
				labels : {
					current : steps_current_step,
					pagination : steps_pagination,
					finish : steps_finish,
					next : steps_next,
					previous : steps_previous,
					loading : steps_loading
				},
				onStepChanging : function(event, currentIndex, newIndex) {
					return handleStepChange(event, currentIndex, newIndex,
							jQuery(this));

				},
				onInit : function() {
					jQuery(postman_input_sender_email).focus();
				},
				onStepChanged : function(event, currentIndex, priorIndex) {
					return postHandleStepChange(event, currentIndex,
							priorIndex, jQuery(this));
				},
				onFinishing : function(event, currentIndex) {
					var form = jQuery(this);

					// Disable validation on fields that
					// are disabled.
					// At this point it's recommended to
					// do an overall check (mean
					// ignoring
					// only disabled fields)
					// form.validate().settings.ignore =
					// ":disabled";

					// Start validation; Prevent form
					// submission if false
					return form.valid();
				},
				onFinished : function(event, currentIndex) {
					var form = jQuery(this);

					// Submit form input
					form.submit();
				}
			}).validate({
		errorPlacement : function(error, element) {
			element.before(error);
		}
	});
}

function handleStepChange(event, currentIndex, newIndex, form) {
	// Always allow going backward even if
	// the current step contains invalid fields!
	if (currentIndex > newIndex) {
		if (currentIndex === 2 && !(checkedEmail)) {
			return false;
		}
		if (currentIndex === 3 && portTestInProgress) {
			return false;
		}
		return true;
	}

	// Clean up if user went backward
	// before
	if (currentIndex < newIndex) {
		// To remove error styles
		jQuery(".body:eq(" + newIndex + ") label.error", form).remove();
		jQuery(".body:eq(" + newIndex + ") .error", form).removeClass("error");
	}

	// Disable validation on fields that
	// are disabled or hidden.
	form.validate().settings.ignore = ":disabled,:hidden";

	// Start validation; Prevent going
	// forward if false
	valid = form.valid();
	if (!valid) {
		return false;
	}

	if (currentIndex === 1) {
		// page 1 : look-up the email
		// address for the smtp server
		checkGoDaddyAndCheckEmail(jQuery(postman_input_sender_email).val());

	} else if (currentIndex === 2) {

		if (!(checkedEmail)) {
			return false;
		}
		// page 2 : check the port
		portsChecked = 0;
		portsToCheck = 0;
		totalAvail = 0;

		getHostsToCheck(jQuery(postman_hostname_element_name).val());

	} else if (currentIndex === 3) {

		// user has clicked next but we haven't finished the check
		if (portTestInProgress) {
			return false;
		}
		// or all ports are unavailable
		if (portCheckBlocksUi) {
			return false;
		}
		valid = form.valid();
		if (!valid) {
			return false;
		}
		var chosenPort = jQuery(postman_port_element_name).val();
		var hostname = jQuery(postman_hostname_element_name).val();
		var authType = jQuery(postman_input_auth_type).val()

	}

	return true;
}

function postHandleStepChange(event, currentIndex, priorIndex, myself) {
	var chosenPort = jQuery('#input_auth_type').val();
	// Suppress (skip) "Warning" step if
	// the user is old enough and wants
	// to the previous step.
	if (currentIndex === 2) {
		jQuery(postman_hostname_element_name).focus();
		// this is the second place i disable the next button but Steps
		// re-enables it after the screen slides
		if (priorIndex === 1) {
			disable('#input_hostname');
			jQuery('li').addClass('disabled');
			showLoaderIcon();
		}
	}
	if (currentIndex === 3) {
		if (priorIndex === 2) {
			// this is the second place i disable the next button but Steps
			// re-enables it after the screen slides
			jQuery('li').addClass('disabled');
			showLoaderIcon();
		}
	}
	if (currentIndex === 4) {
		if (redirectUrlWarning) {
			alert(postman_wizard_bad_redirect_url);
		}
		if (chosenPort == 'none') {
			if (priorIndex === 5) {

				myself.steps("previous");
				return;
			}
			myself.steps("next");
		}
	}

}

/**
 * Asks the server for a List of sockets to perform port checks upon.
 * 
 * @param hostname
 */
function getHostsToCheck(hostname) {
	jQuery('table#wizard_port_test').html('');
	jQuery('#wizard_recommendation').html('');
	hide('.user_override');
	hide('#smtp_not_secure');
	hide('#smtp_mitm');
	connectivtyTestResults = {};
	portCheckBlocksUi = true;
	portTestInProgress = true;
	var data = {
		'action' : 'postman_get_hosts_to_test',
		'hostname' : hostname,
		'original_smtp_server' : smtpDiscovery.hostname
	};
	jQuery.post(ajaxurl, data, function(response) {
		if (postmanValidateAjaxResponseWithPopup(response)) {
			handleHostsToCheckResponse(response.data);
		}
	}).fail(function(response) {
		ajaxFailed(response);
	});
}

/**
 * Handles the response from the server of the list of sockets to check.
 * 
 * @param hostname
 * @param response
 */
function handleHostsToCheckResponse(response) {
	for ( var x in response.hosts) {
		var hostname = response.hosts[x].host;
		var port = response.hosts[x].port;
		var transport = response.hosts[x].transport_id;
		portsToCheck++;
		show('#connectivity_test_status');
		updateStatus(postman_port_test.in_progress + " " + portsToCheck);
		var data = {
			'action' : 'postman_wizard_port_test',
			'hostname' : hostname,
			'port' : port,
			'transport' : transport
		};
		postThePortTest(hostname, port, data);
	}
}

/**
 * Asks the server to run a connectivity test on the given port
 * 
 * @param hostname
 * @param port
 * @param data
 */
function postThePortTest(hostname, port, data) {
	jQuery.post(ajaxurl, data, function(response) {
		if (postmanValidateAjaxResponseWithPopup(response)) {
			handlePortTestResponse(hostname, port, data, response);
		}
	}).fail(function(response) {
		ajaxFailed(response);
		portsChecked++;
		afterPortsChecked();
	});
}

/**
 * Handles the result of the port test
 * 
 * @param hostname
 * @param port
 * @param data
 * @param response
 */
function handlePortTestResponse(hostname, port, data, response) {
	if (!response.data.try_smtps) {
		portsChecked++;
		updateStatus(postman_port_test.in_progress + " "
				+ (portsToCheck - portsChecked));
		connectivtyTestResults[hostname + '_' + port] = response.data;
		if (response.success) {
			// a totalAvail > 0 is our signal to go to the next step
			totalAvail++;
		}
		afterPortsChecked();
	} else {
		// SMTP failed, try again on the SMTPS port
		data['action'] = 'postman_wizard_port_test_smtps';
		postThePortTest(hostname, port, data);
	}
}

/**
 * 
 * @param message
 */
function updateStatus(message) {
	jQuery('#port_test_status').html(
			'<span style="color:blue">' + message + '</span>');
}

/**
 * This functions runs after ALL the ports have been checked. It's chief
 * function is to push the results of the port test back to the server to get a
 * suggested configuration.
 */
function afterPortsChecked() {
	if (portsChecked >= portsToCheck) {
		hideLoaderIcon();
		if (totalAvail != 0) {
			jQuery('li').removeClass('disabled');
			portCheckBlocksUi = false;
		}
		var data = {
			'action' : 'get_wizard_configuration_options',
			'original_smtp_server' : smtpDiscovery.hostname,
			'host_data' : connectivtyTestResults
		};
		postTheConfigurationRequest(data);
		hide('#connectivity_test_status');
	}
}

function userOverrideMenu() {
	disable('input.user_socket_override');
	disable('input.user_auth_override');
	var data = {
		'action' : 'get_wizard_configuration_options',
		'original_smtp_server' : smtpDiscovery.hostname,
		'user_port_override' : jQuery(
				"input:radio[name='user_socket_override']:checked").val(),
		'user_auth_override' : jQuery(
				"input:radio[name='user_auth_override']:checked").val(),
		'host_data' : connectivtyTestResults
	};
	postTheConfigurationRequest(data);
}

function postTheConfigurationRequest(data) {
	jQuery.post(
			ajaxurl,
			data,
			function(response) {
				if (postmanValidateAjaxResponseWithPopup(response)) {
					portTestInProgress = false;
					var $message = '';
					if (response.success) {
						$message = '<span style="color:green">'
								+ response.data.configuration.message
								+ '</span>';
						handleConfigurationResponse(response.data);
						enable('input.user_socket_override');
						enable('input.user_auth_override');
						// enable both next/back buttons
						jQuery('li').removeClass('disabled');
					} else {
						$message = '<span style="color:red">'
								+ response.data.configuration.message
								+ '</span>';
						// enable the back button only
						jQuery('li').removeClass('disabled');
						jQuery('li + li').addClass('disabled');
					}
					if (!response.data.configuration.user_override) {
						jQuery('#wizard_recommendation').append($message);
					}
				}
			}).fail(function(response) {
		ajaxFailed(response);
	});
}
function handleConfigurationResponse(response) {
	jQuery('#input_transport_type').val(response.configuration.transport_type);
	transports.forEach(function(item) {
		item.handleConfigurationResponse(response);
	})

	// this stuff builds the options and is common to all transports
	// populate user Port Override menu
	show('.user_override');
	var el1 = jQuery('#user_socket_override');
	el1.html('');
	for (i = 0; i < response.override_menu.length; i++) {
		buildRadioButtonGroup(el1, 'user_socket_override',
				response.override_menu[i].selected,
				response.override_menu[i].value,
				response.override_menu[i].description,
				response.override_menu[i].secure);
		// populate user Auth Override menu
		if (response.override_menu[i].selected) {
			if (response.override_menu[i].mitm) {
				show('#smtp_mitm');
				jQuery('#smtp_mitm')
						.html(
								sprintf(
										postman_port_test.mitm,
										response.override_menu[i].reported_hostname_domain_only,
										response.override_menu[i].hostname_domain_only));
			} else {
				hide('#smtp_mitm');
			}
			var el2 = jQuery('#user_auth_override');
			el2.html('');
			hide('#smtp_not_secure');
			for (j = 0; j < response.override_menu[i].auth_items.length; j++) {
				buildRadioButtonGroup(el2, 'user_auth_override',
						response.override_menu[i].auth_items[j].selected,
						response.override_menu[i].auth_items[j].value,
						response.override_menu[i].auth_items[j].name, false);
				if (response.override_menu[i].auth_items[j].selected
						&& !response.override_menu[i].secure
						&& response.override_menu[i].auth_items[j].value != 'none') {
					show('#smtp_not_secure');
				}
			}
			// add an event on the user port override field
			jQuery('input.user_auth_override').change(function() {
				userOverrideMenu();
			});
		}

	}
	// add an event on the user port override field
	jQuery('input.user_socket_override').change(function() {
		userOverrideMenu();
	});
}

function buildRadioButtonGroup(tableElement, radioGroupName, isSelected, value,
		label, isSecure) {
	var radioInputValue = ' value="' + value + '"';
	var radioInputChecked = '';
	if (isSelected) {
		radioInputChecked = ' checked = "checked"';
	}
	var secureIcon = '';
	if (isSecure) {
		secureIcon = '&#x1f512; ';
	}
	tableElement.append('<tr><td><input class="' + radioGroupName
			+ '" type="radio" name="' + radioGroupName + '"'
			+ radioInputChecked + radioInputValue + '/></td><td>' + secureIcon
			+ label + '</td></tr>');
}

/**
 * Handles population of the configuration based on the options set in a
 * 3rd-party SMTP plugin
 */
function getConfiguration() {
	var plugin = jQuery('input[name="input_plugin"]' + ':checked').val();
	if (plugin != '') {
		var data = {
			'action' : 'import_configuration',
			'plugin' : plugin
		};
		jQuery
				.post(
						ajaxurl,
						data,
						function(response) {
							if (response.success) {
								jQuery('select#input_transport_type').val(
										'smtp');
								jQuery(postman_input_sender_email).val(
										response.sender_email);
								jQuery(postman_input_sender_name).val(
										response.sender_name);
								jQuery(postman_hostname_element_name).val(
										response.hostname);
								jQuery(postman_port_element_name).val(
										response.port);
								jQuery(postman_input_auth_type).val(
										response.auth_type);
								jQuery('#input_enc_type')
										.val(response.enc_type);
								jQuery(postman_input_basic_username).val(
										response.basic_auth_username);
								jQuery(postman_input_basic_password).val(
										response.basic_auth_password);
								switchBetweenPasswordAndOAuth();
							}
						}).fail(function(response) {
					ajaxFailed(response);
				});
	} else {
		jQuery(postman_input_sender_email).val('');
		jQuery(postman_input_sender_name).val('');
		jQuery(postman_input_basic_username).val('');
		jQuery(postman_input_basic_password).val('');
		jQuery(postman_hostname_element_name).val('');
		jQuery(postman_port_element_name).val('');
		jQuery(postman_input_auth_type).val('none');
		jQuery(postman_enc_for_password_el).val('none');
		switchBetweenPasswordAndOAuth();
	}
}
