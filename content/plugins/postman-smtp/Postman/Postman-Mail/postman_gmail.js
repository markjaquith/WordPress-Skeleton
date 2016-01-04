jQuery(document).ready(function() {
	postmanGmailInit();
});

function postmanGmailInit() {

	// define the PostmanMandrill class
	var PostmanGmail = function() {
		this.slug = 'gmail_api';
	}

	// behavior for handling the user's transport change
	PostmanGmail.prototype.handleTransportChange = function(transportName) {
		if (transportName == this.slug) {
			hide('div.transport_setting');
			hide('div.authentication_setting');
			show('div#oauth_settings');
		}
	}

	PostmanGmail.prototype.handleConfigurationResponse = function(response) {
		// handled by PostmanSmtp
	}

	// add this class to the global transports
	var transport = new PostmanGmail();
	transports.push(transport);

	// since we are initialize the screen, check if needs to be modded
	// by this
	// transport
	var transportName = jQuery('select#input_transport_type').val();
	transport.handleTransportChange(transportName);

}