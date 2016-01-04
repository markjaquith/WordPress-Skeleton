jQuery(document).ready(function() {
	postmanSmtpInit();
});

function postmanSmtpInit() {
	// define the PostmanMandrill class
	var PostmanSmtp = function(name) {
		this.slug = name;
	}

	// behavior for handling the user's transport change
	PostmanSmtp.prototype.handleTransportChange = function(transportName) {
		if (transportName == 'default') {
			hide('div.transport_setting');
			hide('div.authentication_setting');
		} else if (transportName == this.slug) {
			show('div#smtp_config');
			var $choice = jQuery('select#input_auth_type').val();
			if ($choice == 'none') {
				hide('div.authentication_setting');
			} else if ($choice != 'oauth2') {
				show('div#password_settings');
				hide('div.authentication_setting.non-basic');
			} else {
				hide('div.authentication_setting.non-oauth2');
				show('div#oauth_settings');
			}
		}
	}

	// behavior for handling the wizard configuration from the
	// server (after the port test)
	PostmanSmtp.prototype.handleConfigurationResponse = function(response) {
		var transportName = response.configuration.transport_type;
		if (!(transportName == this.slug || transportName == 'gmail_api')) {
			hide('.wizard-auth-oauth2');
			hide('.wizard-auth-basic');
			return;
		}
		console.debug('prepping screen for smtp transport ');
		if (response.configuration.display_auth == 'oauth2') {
			show('p#wizard_oauth2_help');
			jQuery('p#wizard_oauth2_help').html(
					response.configuration.help_text);
			jQuery(postman_redirect_url_el).val(
					response.configuration.redirect_url);
			jQuery('#input_oauth_callback_domain').val(
					response.configuration.callback_domain);
			jQuery('#client_id').html(response.configuration.client_id_label);
			jQuery('#client_secret').html(
					response.configuration.client_secret_label);
			jQuery('#redirect_url').html(
					response.configuration.redirect_url_label);
			jQuery('#callback_domain').html(
					response.configuration.callback_domain_label);
		}
		redirectUrlWarning = response.configuration.dot_notation_url;
		jQuery('#input_transport_type').val(
				response.configuration.transport_type);
		jQuery('#input_auth_type').val(response.configuration.auth_type);
		jQuery('#input_enc_type').val(response.configuration.enc_type);
		jQuery('#input_port').val(response.configuration.port);

		// hide the fields we don't use so validation
		// will work
		if (response.configuration.display_auth == 'oauth2') {
			// where authentication is oauth2
			show('.wizard-auth-oauth2');
			hide('.wizard-auth-basic');
		} else if (response.configuration.display_auth == 'password') {
			// where authentication is password
			hide('.wizard-auth-oauth2');
			show('.wizard-auth-basic');
		} else {
			// where authentication is none
			hide('.wizard-auth-oauth2');
			hide('.wizard-auth-basic');
		}
	}

	// add this class to the global transports
	var transport = new PostmanSmtp('smtp');
	transports.push(transport);

	// since we are initialize the screen, check if needs to be
	// modded by this transport
	var transportName = jQuery('select#input_transport_type').val();
	transport.handleTransportChange(transportName);
}