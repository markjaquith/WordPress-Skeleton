<?php
if (! interface_exists ( 'PostmanPluginOptions' )) {
	interface PostmanPluginOptions {
		public function getPluginSlug();
		public function getPluginName();
		public function isImportable();
		public function getHostname();
		public function getPort();
		public function getMessageSenderEmail();
		public function getMessageSenderName();
		public function getAuthenticationType();
		public function getEncryptionType();
		public function getUsername();
		public function getPassword();
	}
}
if (! class_exists ( 'PostmanImportableConfiguration' )) {
	
	/**
	 * This class instantiates the Connectors for new users to Postman.
	 * It determines which Connectors can supply configuration data
	 *
	 * @author jasonhendriks
	 *        
	 */
	class PostmanImportableConfiguration {
		private $lazyInit;
		private $availableOptions;
		private $importAvailable;
		private $logger;
		function __construct() {
			$this->logger = new PostmanLogger ( get_class ( $this ) );
		}
		function init() {
			if (! $this->lazyInit) {
				$this->queueIfAvailable ( new PostmanEasyWpSmtpOptions () );
				$this->queueIfAvailable ( new PostmanWpSmtpOptions () );
				$this->queueIfAvailable ( new PostmanWpMailBankOptions () );
				$this->queueIfAvailable ( new PostmanWpMailSmtpOptions () );
				$this->queueIfAvailable ( new PostmanCimySwiftSmtpOptions () );
				$this->queueIfAvailable ( new PostmanConfigureSmtpOptions () );
			}
			$this->lazyInit = true;
		}
		private function queueIfAvailable(PostmanPluginOptions $options) {
			$slug = $options->getPluginSlug ();
			if ($options->isImportable ()) {
				$this->availableOptions [$slug] = $options;
				$this->importAvailable = true;
				$this->logger->debug ( $slug . ' is importable' );
			} else {
				$this->logger->debug ( $slug . ' is not importable' );
			}
		}
		public function getAvailableOptions() {
			$this->init ();
			return $this->availableOptions;
		}
		public function isImportAvailable() {
			$this->init ();
			return $this->importAvailable;
		}
	}
}

if (! class_exists ( 'PostmanAbstractPluginOptions' )) {
	
	/**
	 *
	 * @author jasonhendriks
	 */
	abstract class PostmanAbstractPluginOptions implements PostmanPluginOptions {
		protected $options;
		protected $logger;
		public function __construct() {
			$this->logger = new PostmanLogger ( get_class ( $this ) );
		}
		public function isValid() {
			$valid = true;
			$host = $this->getHostname ();
			$port = $this->getPort ();
			$fromEmail = $this->getMessageSenderEmail ();
			$fromName = $this->getMessageSenderName ();
			$auth = $this->getAuthenticationType ();
			$enc = $this->getEncryptionType ();
			$username = $this->getUsername ();
			$password = $this->getPassword ();
			$valid &= ! empty ( $host );
			$this->logger->trace ( 'host ok ' . $valid );
			$valid &= ! empty ( $port ) && absint ( $port ) > 0 && absint ( $port ) <= 65535;
			$this->logger->trace ( 'port ok ' . $valid );
			$valid &= ! empty ( $fromEmail );
			$this->logger->trace ( 'from email ok ' . $valid );
			$valid &= ! empty ( $fromName );
			$this->logger->trace ( 'from name ok ' . $valid );
			$valid &= ! empty ( $auth );
			$this->logger->trace ( 'auth ok ' . $valid );
			$valid &= ! empty ( $enc );
			$this->logger->trace ( 'enc ok ' . $valid );
			if ($auth != PostmanOptions::AUTHENTICATION_TYPE_NONE) {
				$valid &= ! empty ( $username );
				$valid &= ! empty ( $password );
			}
			$this->logger->trace ( 'user/pass ok ' . $valid );
			return $valid;
		}
		public function isImportable() {
			return $this->isValid ();
		}
	}
}

if (! class_exists ( 'PostmanConfigureSmtpOptions' )) {
	// ConfigureSmtp (aka "SMTP") - 80,000
	class PostmanConfigureSmtpOptions extends PostmanAbstractPluginOptions {
		const SLUG = 'configure_smtp';
		const PLUGIN_NAME = 'Configure SMTP';
		const MESSAGE_SENDER_EMAIL = 'from_email';
		const MESSAGE_SENDER_NAME = 'from_name';
		const HOSTNAME = 'host';
		const PORT = 'port';
		const AUTHENTICATION_TYPE = 'smtp_auth';
		const ENCRYPTION_TYPE = 'smtp_secure';
		const USERNAME = 'smtp_user';
		const PASSWORD = 'smtp_pass';
		public function __construct() {
			parent::__construct ();
			$this->options = get_option ( 'c2c_configure_smtp' );
		}
		public function getPluginSlug() {
			return self::SLUG;
		}
		public function getPluginName() {
			return self::PLUGIN_NAME;
		}
		public function getMessageSenderEmail() {
			if (isset ( $this->options [self::MESSAGE_SENDER_EMAIL] ))
				return $this->options [self::MESSAGE_SENDER_EMAIL];
		}
		public function getMessageSenderName() {
			if (isset ( $this->options [self::MESSAGE_SENDER_NAME] ))
				return $this->options [self::MESSAGE_SENDER_NAME];
		}
		public function getHostname() {
			if (isset ( $this->options [self::HOSTNAME] ))
				return $this->options [self::HOSTNAME];
		}
		public function getPort() {
			if (isset ( $this->options [self::PORT] ))
				return $this->options [self::PORT];
		}
		public function getUsername() {
			if (isset ( $this->options [self::USERNAME] ))
				return $this->options [self::USERNAME];
		}
		public function getPassword() {
			if (isset ( $this->options [self::PASSWORD] ))
				return $this->options [self::PASSWORD];
		}
		public function getAuthenticationType() {
			if (isset ( $this->options [self::AUTHENTICATION_TYPE] )) {
				if ($this->options [self::AUTHENTICATION_TYPE] == 1) {
					return PostmanOptions::AUTHENTICATION_TYPE_PLAIN;
				} else {
					return PostmanOptions::AUTHENTICATION_TYPE_NONE;
				}
			}
		}
		public function getEncryptionType() {
			if (isset ( $this->options [self::ENCRYPTION_TYPE] )) {
				switch ($this->options [self::ENCRYPTION_TYPE]) {
					case 'ssl' :
						return PostmanOptions::SECURITY_TYPE_SMTPS;
					case 'tls' :
						return PostmanOptions::SECURITY_TYPE_STARTTLS;
					case '' :
						return PostmanOptions::SECURITY_TYPE_NONE;
				}
			}
		}
	}
}

if (! class_exists ( 'PostmanCimySwiftSmtpOptions' )) {
	// Cimy Swift - 9,000
	class PostmanCimySwiftSmtpOptions extends PostmanAbstractPluginOptions {
		const SLUG = 'cimy_swift_smtp';
		const PLUGIN_NAME = 'Cimy Swift SMTP';
		const MESSAGE_SENDER_EMAIL = 'sender_mail';
		const MESSAGE_SENDER_NAME = 'sender_name';
		const HOSTNAME = 'server';
		const PORT = 'port';
		const ENCRYPTION_TYPE = 'ssl';
		const USERNAME = 'username';
		const PASSWORD = 'password';
		public function __construct() {
			parent::__construct ();
			$this->options = get_option ( 'cimy_swift_smtp_options' );
		}
		public function getPluginSlug() {
			return self::SLUG;
		}
		public function getPluginName() {
			return self::PLUGIN_NAME;
		}
		public function getMessageSenderEmail() {
			if (isset ( $this->options [self::MESSAGE_SENDER_EMAIL] ))
				return $this->options [self::MESSAGE_SENDER_EMAIL];
		}
		public function getMessageSenderName() {
			if (isset ( $this->options [self::MESSAGE_SENDER_NAME] ))
				return $this->options [self::MESSAGE_SENDER_NAME];
		}
		public function getHostname() {
			if (isset ( $this->options [self::HOSTNAME] ))
				return $this->options [self::HOSTNAME];
		}
		public function getPort() {
			if (isset ( $this->options [self::PORT] ))
				return $this->options [self::PORT];
		}
		public function getUsername() {
			if (isset ( $this->options [self::USERNAME] ))
				return $this->options [self::USERNAME];
		}
		public function getPassword() {
			if (isset ( $this->options [self::PASSWORD] ))
				return $this->options [self::PASSWORD];
		}
		public function getAuthenticationType() {
			if (! empty ( $this->options [self::USERNAME] ) && ! empty ( $this->options [self::PASSWORD] )) {
				return PostmanOptions::AUTHENTICATION_TYPE_PLAIN;
			} else {
				return PostmanOptions::AUTHENTICATION_TYPE_NONE;
			}
		}
		public function getEncryptionType() {
			if (isset ( $this->options [self::ENCRYPTION_TYPE] )) {
				switch ($this->options [self::ENCRYPTION_TYPE]) {
					case 'ssl' :
						return PostmanOptions::SECURITY_TYPE_SMTPS;
					case 'tls' :
						return PostmanOptions::SECURITY_TYPE_STARTTLS;
					case '' :
						return PostmanOptions::SECURITY_TYPE_NONE;
				}
			}
		}
	}
}

// Easy WP SMTP - 40,000
if (! class_exists ( 'PostmanEasyWpSmtpOptions' )) {
	
	/**
	 * Imports Easy WP SMTP options into Postman
	 *
	 * @author jasonhendriks
	 */
	class PostmanEasyWpSmtpOptions extends PostmanAbstractPluginOptions implements PostmanPluginOptions {
		const SLUG = 'easy_wp_smtp';
		const PLUGIN_NAME = 'Easy WP SMTP';
		const SMTP_SETTINGS = 'smtp_settings';
		const MESSAGE_SENDER_EMAIL = 'from_email_field';
		const MESSAGE_SENDER_NAME = 'from_name_field';
		const HOSTNAME = 'host';
		const PORT = 'port';
		const ENCRYPTION_TYPE = 'type_encryption';
		const AUTHENTICATION_TYPE = 'autentication';
		const USERNAME = 'username';
		const PASSWORD = 'password';
		public function __construct() {
			parent::__construct ();
			$this->options = get_option ( 'swpsmtp_options' );
		}
		public function getPluginSlug() {
			return self::SLUG;
		}
		public function getPluginName() {
			return self::PLUGIN_NAME;
		}
		public function getMessageSenderEmail() {
			if (isset ( $this->options [self::MESSAGE_SENDER_EMAIL] ))
				return $this->options [self::MESSAGE_SENDER_EMAIL];
		}
		public function getMessageSenderName() {
			if (isset ( $this->options [self::MESSAGE_SENDER_NAME] ))
				return $this->options [self::MESSAGE_SENDER_NAME];
		}
		public function getHostname() {
			if (isset ( $this->options [self::SMTP_SETTINGS] [self::HOSTNAME] ))
				return $this->options [self::SMTP_SETTINGS] [self::HOSTNAME];
		}
		public function getPort() {
			if (isset ( $this->options [self::SMTP_SETTINGS] [self::PORT] ))
				return $this->options [self::SMTP_SETTINGS] [self::PORT];
		}
		public function getUsername() {
			if (isset ( $this->options [self::SMTP_SETTINGS] [self::USERNAME] ))
				return $this->options [self::SMTP_SETTINGS] [self::USERNAME];
		}
		public function getPassword() {
			if (isset ( $this->options [self::SMTP_SETTINGS] [self::PASSWORD] )) {
				// wpecommerce screwed the pooch
				$password = $this->options [self::SMTP_SETTINGS] [self::PASSWORD];
				if (strlen ( $password ) % 4 != 0 || preg_match ( '/[^A-Za-z0-9]/', $password )) {
					$decodedPw = base64_decode ( $password, true );
					$reencodedPw = base64_encode ( $decodedPw );
					if ($reencodedPw === $password) {
						// encoded
						return $decodedPw;
					} else {
						// not encoded
						return $password;
					}
				}
			}
		}
		public function getAuthenticationType() {
			if (isset ( $this->options [self::SMTP_SETTINGS] [self::AUTHENTICATION_TYPE] )) {
				switch ($this->options [self::SMTP_SETTINGS] [self::AUTHENTICATION_TYPE]) {
					case 'yes' :
						return PostmanOptions::AUTHENTICATION_TYPE_PLAIN;
					case 'no' :
						return PostmanOptions::AUTHENTICATION_TYPE_NONE;
				}
			}
		}
		public function getEncryptionType() {
			if (isset ( $this->options [self::SMTP_SETTINGS] [self::ENCRYPTION_TYPE] )) {
				switch ($this->options [self::SMTP_SETTINGS] [self::ENCRYPTION_TYPE]) {
					case 'ssl' :
						return PostmanOptions::SECURITY_TYPE_SMTPS;
					case 'tls' :
						return PostmanOptions::SECURITY_TYPE_STARTTLS;
					case 'none' :
						return PostmanOptions::SECURITY_TYPE_NONE;
				}
			}
		}
	}
}

if (! class_exists ( 'PostmanWpMailBankOptions' )) {
	
	/**
	 * Import configuration from WP Mail Bank
	 *
	 * @author jasonhendriks
	 *        
	 */
	class PostmanWpMailBankOptions extends PostmanAbstractPluginOptions implements PostmanPluginOptions {
		const SLUG = 'wp_mail_bank';
		const PLUGIN_NAME = 'WP Mail Bank';
		public function __construct() {
			parent::__construct ();
			// data is stored in table wp_mail_bank
			// fields are id, from_name, from_email, mailer_type, return_path, return_email, smtp_host, smtp_port, word_wrap, encryption, smtp_keep_alive, authentication, smtp_username, smtp_password
			global $wpdb;
			$wpdb->show_errors ();
			$wpdb->suppress_errors ();
			$this->options = @$wpdb->get_row ( "SELECT from_name, from_email, mailer_type, smtp_host, smtp_port, encryption, authentication, smtp_username, smtp_password FROM " . $wpdb->prefix . "mail_bank" );
		}
		public function getPluginSlug() {
			return self::SLUG;
		}
		public function getPluginName() {
			return self::PLUGIN_NAME;
		}
		public function getMessageSenderEmail() {
			if (isset ( $this->options->from_email ))
				return $this->options->from_email;
		}
		public function getMessageSenderName() {
			if (isset ( $this->options->from_name )) {
				return stripslashes ( htmlspecialchars_decode ( $this->options->from_name, ENT_QUOTES ) );
			}
		}
		public function getHostname() {
			if (isset ( $this->options->smtp_host ))
				return $this->options->smtp_host;
		}
		public function getPort() {
			if (isset ( $this->options->smtp_port ))
				return $this->options->smtp_port;
		}
		public function getUsername() {
			if (isset ( $this->options->authentication ) && isset ( $this->options->smtp_username ))
				if ($this->options->authentication == 1)
					return $this->options->smtp_username;
		}
		public function getPassword() {
			if (isset ( $this->options->authentication ) && isset ( $this->options->smtp_password )) {
				if ($this->options->authentication == 1)
					return $this->options->smtp_password;
			}
		}
		public function getAuthenticationType() {
			if (isset ( $this->options->authentication )) {
				if ($this->options->authentication == 1) {
					return PostmanOptions::AUTHENTICATION_TYPE_PLAIN;
				} else if ($this->options->authentication == 0) {
					return PostmanOptions::AUTHENTICATION_TYPE_NONE;
				}
			}
		}
		public function getEncryptionType() {
			if (isset ( $this->options->mailer_type )) {
				if ($this->options->mailer_type == 0) {
					switch ($this->options->encryption) {
						case 0 :
							return PostmanOptions::SECURITY_TYPE_NONE;
						case 1 :
							return PostmanOptions::SECURITY_TYPE_SMTPS;
						case 2 :
							return PostmanOptions::SECURITY_TYPE_STARTTLS;
					}
				}
			}
		}
	}
}

// "WP Mail SMTP" (aka "Email") - 300,000
// each field is a new row in options : mail_from, mail_from_name, smtp_host, smtp_port, smtp_ssl, smtp_auth, smtp_user, smtp_pass
// "Easy SMTP Mail" aka. "Webriti SMTP Mail" appears to share the data format of "WP Mail SMTP" so no need to create an Options class for it.
//
if (! class_exists ( 'PostmanWpMailSmtpOptions' )) {
	class PostmanWpMailSmtpOptions extends PostmanAbstractPluginOptions implements PostmanPluginOptions {
		const SLUG = 'wp_mail_smtp';
		const PLUGIN_NAME = 'WP Mail SMTP';
		const MESSAGE_SENDER_EMAIL = 'mail_from';
		const MESSAGE_SENDER_NAME = 'mail_from_name';
		const HOSTNAME = 'smtp_host';
		const PORT = 'smtp_port';
		const ENCRYPTION_TYPE = 'smtp_ssl';
		const AUTHENTICATION_TYPE = 'smtp_auth';
		const USERNAME = 'smtp_user';
		const PASSWORD = 'smtp_pass';
		public function __construct() {
			parent::__construct ();
			$this->options [self::MESSAGE_SENDER_EMAIL] = get_option ( self::MESSAGE_SENDER_EMAIL );
			$this->options [self::MESSAGE_SENDER_NAME] = get_option ( self::MESSAGE_SENDER_NAME );
			$this->options [self::HOSTNAME] = get_option ( self::HOSTNAME );
			$this->options [self::PORT] = get_option ( self::PORT );
			$this->options [self::ENCRYPTION_TYPE] = get_option ( self::ENCRYPTION_TYPE );
			$this->options [self::AUTHENTICATION_TYPE] = get_option ( self::AUTHENTICATION_TYPE );
			$this->options [self::USERNAME] = get_option ( self::USERNAME );
			$this->options [self::PASSWORD] = get_option ( self::PASSWORD );
		}
		public function getPluginSlug() {
			return self::SLUG;
		}
		public function getPluginName() {
			return self::PLUGIN_NAME;
		}
		public function getMessageSenderEmail() {
			if (isset ( $this->options [self::MESSAGE_SENDER_EMAIL] ))
				return $this->options [self::MESSAGE_SENDER_EMAIL];
		}
		public function getMessageSenderName() {
			if (isset ( $this->options [self::MESSAGE_SENDER_NAME] ))
				return $this->options [self::MESSAGE_SENDER_NAME];
		}
		public function getHostname() {
			if (isset ( $this->options [self::HOSTNAME] ))
				return $this->options [self::HOSTNAME];
		}
		public function getPort() {
			if (isset ( $this->options [self::PORT] ))
				return $this->options [self::PORT];
		}
		public function getUsername() {
			if (isset ( $this->options [self::USERNAME] ))
				return $this->options [self::USERNAME];
		}
		public function getPassword() {
			if (isset ( $this->options [self::PASSWORD] ))
				return $this->options [self::PASSWORD];
		}
		public function getAuthenticationType() {
			if (isset ( $this->options [self::AUTHENTICATION_TYPE] )) {
				switch ($this->options [self::AUTHENTICATION_TYPE]) {
					case 'true' :
						return PostmanOptions::AUTHENTICATION_TYPE_PLAIN;
					case 'false' :
						return PostmanOptions::AUTHENTICATION_TYPE_NONE;
				}
			}
		}
		public function getEncryptionType() {
			if (isset ( $this->options [self::ENCRYPTION_TYPE] )) {
				switch ($this->options [self::ENCRYPTION_TYPE]) {
					case 'ssl' :
						return PostmanOptions::SECURITY_TYPE_SMTPS;
					case 'tls' :
						return PostmanOptions::SECURITY_TYPE_STARTTLS;
					case 'none' :
						return PostmanOptions::SECURITY_TYPE_NONE;
				}
			}
		}
	}
}

// WP SMTP - 40,000
if (! class_exists ( 'PostmanWpSmtpOptions' )) {
	class PostmanWpSmtpOptions extends PostmanAbstractPluginOptions implements PostmanPluginOptions {
		const SLUG = 'wp_smtp'; // god these names are terrible
		const PLUGIN_NAME = 'WP SMTP';
		const MESSAGE_SENDER_EMAIL = 'from';
		const MESSAGE_SENDER_NAME = 'fromname';
		const HOSTNAME = 'host';
		const PORT = 'port';
		const ENCRYPTION_TYPE = 'smtpsecure';
		const AUTHENTICATION_TYPE = 'smtpauth';
		const USERNAME = 'username';
		const PASSWORD = 'password';
		public function __construct() {
			parent::__construct ();
			$this->options = get_option ( 'wp_smtp_options' );
		}
		public function getPluginSlug() {
			return self::SLUG;
		}
		public function getPluginName() {
			return self::PLUGIN_NAME;
		}
		public function getMessageSenderEmail() {
			if (isset ( $this->options [self::MESSAGE_SENDER_EMAIL] ))
				return $this->options [self::MESSAGE_SENDER_EMAIL];
		}
		public function getMessageSenderName() {
			if (isset ( $this->options [self::MESSAGE_SENDER_NAME] ))
				return $this->options [self::MESSAGE_SENDER_NAME];
		}
		public function getHostname() {
			if (isset ( $this->options [self::HOSTNAME] ))
				return $this->options [self::HOSTNAME];
		}
		public function getPort() {
			if (isset ( $this->options [self::PORT] ))
				return $this->options [self::PORT];
		}
		public function getUsername() {
			if (isset ( $this->options [self::USERNAME] ))
				return $this->options [self::USERNAME];
		}
		public function getPassword() {
			if (isset ( $this->options [self::PASSWORD] ))
				return $this->options [self::PASSWORD];
		}
		public function getAuthenticationType() {
			if (isset ( $this->options [self::AUTHENTICATION_TYPE] )) {
				switch ($this->options [self::AUTHENTICATION_TYPE]) {
					case 'yes' :
						return PostmanOptions::AUTHENTICATION_TYPE_PLAIN;
					case 'no' :
						return PostmanOptions::AUTHENTICATION_TYPE_NONE;
				}
			}
		}
		public function getEncryptionType() {
			if (isset ( $this->options [self::ENCRYPTION_TYPE] )) {
				switch ($this->options [self::ENCRYPTION_TYPE]) {
					case 'ssl' :
						return PostmanOptions::SECURITY_TYPE_SMTPS;
					case 'tls' :
						return PostmanOptions::SECURITY_TYPE_STARTTLS;
					case '' :
						return PostmanOptions::SECURITY_TYPE_NONE;
				}
			}
		}
	}
}