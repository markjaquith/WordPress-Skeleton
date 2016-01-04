jQuery(document).ready(function() {

	// enable toggling of the API field from password to plain text
	enablePasswordDisplayOnEntry('mandrill_api_key', 'toggleMandrillApiKey');

	// define the PostmanMandrill class
	var PostmanMandrill = function() {
		this.slug = 'mandrill_api';
	}

	// behavior for handling the user's transport change
	PostmanMandrill.prototype.handleTransportChange = function(transportName) {
		if (transportName == this.slug) {
			hide('div.transport_setting');
			hide('div.authentication_setting');
			show('div#mandrill_settings');
		}
	}

	// behavior for handling the wizard configuration from the
	// server (after the port test)
	PostmanMandrill.prototype.handleConfigurationResponse = function(response) {
		var transportName = response.configuration.transport_type;
		if (transportName == this.slug) {
			show('section.wizard_mandrill');
		} else {
			hide('section.wizard_mandrill');
		}
	}

	// add this class to the global transports
	var transport = new PostmanMandrill();
	transports.push(transport);

	// since we are initialize the screen, check if needs to be modded by this
	// transport
	var transportName = jQuery('select#input_transport_type').val();
	transport.handleTransportChange(transportName);

});
