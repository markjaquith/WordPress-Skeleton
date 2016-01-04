postman_begin_test_button_id = 'input#begin-port-test';

jQuery(document).ready(function() {
	var elHostname = jQuery(postman_begin_test_button_id);
	jQuery(postman_hostname_element_name).focus();
	elHostname.click(function() {
		valid = jQuery('#port_test_form_id').valid();
		if (!valid) {
			return false;
		}

		// initialize the view for a new test
		elHostname.attr('disabled', 'disabled');
		hide('#conclusion');
		hide('#blocked-port-help');
		jQuery('ol.conclusion').html('');
		showLoaderIcon();

		var $elTestingTable = jQuery('#connectivity_test_table');
		$elTestingTable.show();
		show('.portquiz');

		var hostname = jQuery(postman_hostname_element_name).val();
		var data = {
			'action' : 'postman_get_hosts_to_test',
			'hostname' : hostname
		};

		totalPortsTested = 0;
		portsToBeTested = 0;

		jQuery.post(ajaxurl, data, function(response) {
			if (postmanValidateAjaxResponseWithPopup(response)) {
				handleHostsToCheckResponse(response.data);
			}
		}).fail(function(response) {
			ajaxFailed(response);
		});

		//
		return false;
	});
});

/**
 * Handles the response from the server of the list of sockets to check.
 * 
 * @param hostname
 * @param response
 */
function handleHostsToCheckResponse(response) {
	for ( var x in response.hosts) {
		var id = response.hosts[x].id;
		var transportSlug = response.hosts[x].transport_id;
		var hostname = response.hosts[x].host;
		var port = response.hosts[x].port
		var cell = 'tr#' + id + " td.socket";
		var testEl = jQuery(cell);
		testEl.html('<span>' + hostname + ':' + port + '</span>');
		portQuizTest(response.hosts[x], hostname, port);
	}
}

function portQuizTest(socket, hostname, port) {
	resetView(socket.id);
	portsToBeTested += 1;
	var cell = 'tr#' + socket.id + " td.firewall";
	var testEl = jQuery(cell);
	testEl.html('<span style="color:blue">' + postman_port_test.in_progress
			+ '</span>');
	var data = {
		'action' : 'postman_port_quiz_test',
		'hostname' : hostname,
		'port' : port
	};
	jQuery.post(
			ajaxurl,
			data,
			function(response) {
				if (postmanValidateAjaxResponseWithPopup(response)) {
					if (response.success) {
						testEl.html('<span style="color:green">'
								+ postman_port_test.open + '</span>');
						// start the next test
					} else {
						testEl.html('<span style="color:red">'
								+ postman_port_test.closed + '</span>');
					}
					firstServiceTest(socket, hostname, port, response.success);
				}
			}).fail(
			function(response) {
				totalPortsTested += 1;
				testEl.html('<span style="color:red">'
						+ postman_email_test.ajax_error + "</span>");
				enableButtonCheck();
			});
}
function firstServiceTest(socket, hostname, port, open) {
	var cell = 'tr#' + socket.id + " td.service";
	var testEl = jQuery(cell);
	testEl.html('<span style="color:blue">' + postman_port_test.in_progress
			+ '</span>');
	var data = {
		'action' : 'postman_test_port',
		'hostname' : hostname,
		'port' : port
	};
	jQuery
			.post(
					ajaxurl,
					data,
					function(response) {
						if (postmanValidateAjaxResponseWithPopup(response)) {
							if (response.success) {
								totalPortsTested += 1;
								if (port == 443) {
									// API test
									testEl
											.html('<span style="color:green">&#x1f512; '
													+ response.data.protocol
													+ '</span>');
									var cell = 'tr#' + socket.id
											+ " td.reported_id";
									var p443El = jQuery(cell);
									if (response.data.reported_hostname_domain_only) {
										p443El
												.html('<span>'
														+ response.data.reported_hostname_domain_only
														+ '</span>');
									}
									addConclusion(sprintf(
											postman_port_test.https_success,
											response.data.port,
											socket.transport_name), true,
											response.data.secure);
								} else {
									// SMTP test
									testEl.html('<span style="color:green">'
											+ response.data.protocol
											+ '</span>');
									inspectResponse(socket.id, response.data,
											port);
									var message = sprintf(postman_port_test.success,
											port, hostname);
									if (response.data.mitm) {
										message += ' <span style="background-color:yellow">'
												+ sprintf(
														postman_port_test.mitm,
														response.data.reported_hostname_domain_only,
														response.data.hostname_domain_only)
												+ '</span>';
									}
									addConclusion(message, true,
											response.data.secure);
								}
							} else {
								if (port == 443) {
									// API test
									testEl.html('<span style="color:red">'
											+ postman_port_test.no + '</span>');
									totalPortsTested += 1;
									var p443El = jQuery(cell);
									addConclusion(sprintf(postman_port_test.try_dif_smtp,
											port, hostname), false,
											response.data.secure);
								} else {
									if (response.data.try_smtps) {
										// start the SMTPS test
										portTest3(socket, hostname, port, open);
									} else {
										testEl.html('<span style="color:red">'
												+ postman_port_test.no + '</span>');
										totalPortsTested += 1;
										addConclusion(sprintf(
												postman_port_test.blocked, port),
												false, response.data.secure);
										show('#blocked-port-help');
									}
								}
							}
							enableButtonCheck();
						}
					}).fail(
					function(response) {
						totalPortsTested += 1;
						testEl.html('<span style="color:red">'
								+ postman_email_test.ajax_error + "</span>");
						enableButtonCheck();
					});
}
function portTest3(socket, hostname, port, open) {
	var cell = 'tr#' + socket.id + " td.service";
	var testEl = jQuery(cell);
	testEl.html('<span style="color:blue">' + postman_port_test.in_progress
			+ '</span>');
	var data = {
		'action' : 'postman_test_smtps',
		'hostname' : hostname,
		'port' : port
	};
	jQuery
			.post(
					ajaxurl,
					data,
					function(response) {
						if (postmanValidateAjaxResponseWithPopup(response)) {
							if (response.success) {
								if (response.data.protocol == 'SMTPS') {
									testEl
											.html('<span style="color:green">&#x1f512; '
													+ response.data.protocol
													+ '</span>');
								} else {

									testEl.html('<span style="color:green">'
											+ response.data.protocol
											+ '</span>');
								}
								inspectResponse(socket.id, response.data, port);
								var message = sprintf(postman_port_test.success,
										port, hostname);
								if (response.data.mitm) {
									message += ' <span style="background-color:yellow">'
											+ sprintf(
													postman_port_test.mitm,
													response.data.reported_hostname_domain_only,
													response.data.hostname_domain_only
															+ '</span>');
								}
								addConclusion(message, true,
										response.data.secure);
							} else {
								testEl.html('<span style="color:red">'
										+ postman_port_test.no + '</span>');
								show('#blocked-port-help');
								if (open) {
									addConclusion(sprintf(postman_port_test.try_dif_smtp,
											port, hostname), false,
											response.data.secure);
								} else {
									addConclusion(sprintf(postman_port_test.blocked,
											port), false, response.data.secure);
								}
							}
							totalPortsTested += 1;
							enableButtonCheck();
						}
					}).fail(
					function(response) {
						totalPortsTested += 1;
						testEl.html('<span style="color:red">'
								+ postman_email_test.ajax_error + "</span>");
						enableButtonCheck();
					});
}
function enableButtonCheck() {
	if (totalPortsTested >= portsToBeTested) {
		enable(postman_begin_test_button_id);
		hideLoaderIcon();
		jQuery(postman_hostname_element_name).focus();
	}
}
function inspectResponse(id, response, port) {
	var cell = 'tr#' + id + " td.reported_id";
	var testEl = jQuery(cell);
	if (response.reported_hostname_domain_only) {
		testEl.html('<span>' + response.reported_hostname_domain_only
				+ '</span>');
	}
	var cell = 'tr#' + id + " td.service";
	var testEl = jQuery(cell);
	if (response.protocol == 'SMTPS') {
		testEl.html('<span style="color:green">&#x1f512; SMTPS</span>');
	} else if (response.start_tls) {
		testEl.html('<span style="color:green">&#x1f512; SMTP-STARTTLS</span>');
	} else {
		testEl.html('<span style="color:green">SMTP</span>');
	}
	var cell = 'tr#' + id + " td.auth_none";
	var testEl = jQuery(cell);
	if (response.auth_none) {
		testEl.html('<span style="color:green">' + postman_port_test.yes + '</span>');
	} else {
		testEl.html('<span>' + postman_port_test.no + '</span>');
	}
	var cell = 'tr#' + id + " td.auth_plain";
	var testEl = jQuery(cell);
	if (response.auth_plain) {
		testEl.html('<span style="color:green">' + postman_port_test.yes + '</span>');
	} else {
		testEl.html('<span>' + postman_port_test.no + '</span>');
	}
	var cell = 'tr#' + id + " td.auth_login";
	var testEl = jQuery(cell);
	if (response.auth_login) {
		testEl.html('<span style="color:green">' + postman_port_test.yes + '</span>');
	} else {
		testEl.html('<span>' + postman_port_test.no + '</span>');
	}
	var cell = 'tr#' + id + " td.auth_crammd5";
	var testEl = jQuery(cell);
	if (response.auth_crammd5) {
		testEl.html('<span style="color:green">' + postman_port_test.yes + '</span>');
	} else {
		testEl.html('<span>' + postman_port_test.no + '</span>');
	}
	var cell = 'tr#' + id + " td.auth_xoauth2";
	var testEl = jQuery(cell);
	if (response.auth_xoauth) {
		testEl.html('<span style="color:green">' + postman_port_test.yes + '</span>');
	} else {
		testEl.html('<span>' + postman_port_test.no + '</span>');
	}
}
function resetView(id) {
	var testEl = jQuery('tr#' + id + ' td.resettable');
	testEl.html('-');
}
function addConclusion(message, success, isSecure) {
	show('#conclusion');
	var secureIcon = '';
	if (isSecure) {
		secureIcon = '&#x1f512; ';
	}
	if (success) {
		message = '&#9989; ' + secureIcon + message;
	} else {
		message = '&#10060; ' + secureIcon + message;
	}
	jQuery('ol.conclusion').append('<li>' + message + '</li>');
}