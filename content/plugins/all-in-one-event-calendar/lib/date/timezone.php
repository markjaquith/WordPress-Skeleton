<?php

/**
 * Timezones manipulation object.
 *
 * @author     Time.ly Network, Inc.
 * @since      2.0
 * @package    Ai1EC
 * @subpackage Ai1EC.Date
 */
class Ai1ec_Date_Timezone extends Ai1ec_Base {

	/**
	 * @var Ai1ec_Cache_Interface In-memory storage for timezone objects.
	 */
	protected $_cache           = null;

	/**
	 * @var array Map of timezone names and their Olson TZ counterparts.
	 */
	protected $_zones           = array(
		'+00:00'                           => 'UTC',
		'Z'                                => 'UTC',
		'AUS Central Standard Time'        => 'Australia/Darwin',
		'AUS Eastern Standard Time'        => 'Australia/Sydney',
		'Acre'                             => 'America/Rio_Branco',
		'Afghanistan'                      => 'Asia/Kabul',
		'Afghanistan Standard Time'        => 'Asia/Kabul',
		'Africa_Central'                   => 'Africa/Maputo',
		'Africa_Eastern'                   => 'Africa/Nairobi',
		'Africa_FarWestern'                => 'Africa/El_Aaiun',
		'Africa_Southern'                  => 'Africa/Johannesburg',
		'Africa_Western'                   => 'Africa/Lagos',
		'Aktyubinsk'                       => 'Asia/Aqtobe',
		'Alaska'                           => 'America/Juneau',
		'Alaska_Hawaii'                    => 'America/Anchorage',
		'Alaskan Standard Time'            => 'America/Anchorage',
		'Almaty'                           => 'Asia/Almaty',
		'Amazon'                           => 'America/Manaus',
		'America_Central'                  => 'America/Chicago',
		'America_Eastern'                  => 'America/New_York',
		'America_Mountain'                 => 'America/Denver',
		'America_Pacific'                  => 'America/Los_Angeles',
		'Anadyr'                           => 'Asia/Anadyr',
		'Aqtau'                            => 'Asia/Aqtau',
		'Aqtobe'                           => 'Asia/Aqtobe',
		'Arab Standard Time'               => 'Asia/Riyadh',
		'Arabian'                          => 'Asia/Riyadh',
		'Arabian Standard Time'            => 'Asia/Dubai',
		'Arabic Standard Time'             => 'Asia/Baghdad',
		'Argentina'                        => 'America/Buenos_Aires',
		'Argentina Standard Time'          => 'America/Buenos_Aires',
		'Argentina_Western'                => 'America/Mendoza',
		'Armenia'                          => 'Asia/Yerevan',
		'Armenian Standard Time'           => 'Asia/Yerevan',
		'Ashkhabad'                        => 'Asia/Ashgabat',
		'Atlantic'                         => 'America/Halifax',
		'Atlantic Standard Time'           => 'America/Halifax',
		'Australia_Central'                => 'Australia/Adelaide',
		'Australia_CentralWestern'         => 'Australia/Eucla',
		'Australia_Eastern'                => 'Australia/Sydney',
		'Australia_Western'                => 'Australia/Perth',
		'Azerbaijan'                       => 'Asia/Baku',
		'Azerbaijan Standard Time'         => 'Asia/Baku',
		'Azores'                           => 'Atlantic/Azores',
		'Azores Standard Time'             => 'Atlantic/Azores',
		'Baku'                             => 'Asia/Baku',
		'Bangladesh'                       => 'Asia/Dhaka',
		'Bering'                           => 'America/Adak',
		'Bhutan'                           => 'Asia/Thimphu',
		'Bolivia'                          => 'America/La_Paz',
		'Borneo'                           => 'Asia/Kuching',
		'Brasilia'                         => 'America/Sao_Paulo',
		'British'                          => 'Europe/London',
		'Brunei'                           => 'Asia/Brunei',
		'Canada Central Standard Time'     => 'America/Regina',
		'Cape Verde Standard Time'         => 'Atlantic/Cape_Verde',
		'Cape_Verde'                       => 'Atlantic/Cape_Verde',
		'Caucasus Standard Time'           => 'Asia/Yerevan',
		'Cen. Australia Standard Time'     => 'Australia/Adelaide',
		'Central America Standard Time'    => 'America/Guatemala',
		'Central Asia Standard Time'       => 'Asia/Dhaka',
		'Central Brazilian Standard Time'  => 'America/Manaus',
		'Central Europe Standard Time'     => 'Europe/Budapest',
		'Central European Standard Time'   => 'Europe/Warsaw',
		'Central Pacific Standard Time'    => 'Pacific/Guadalcanal',
		'Central Standard Time'            => 'America/Chicago',
		'Central Standard Time (Mexico)'   => 'America/Mexico_City',
		'Chamorro'                         => 'Pacific/Saipan',
		'Changbai'                         => 'Asia/Harbin',
		'Chatham'                          => 'Pacific/Chatham',
		'Chile'                            => 'America/Santiago',
		'China'                            => 'Asia/Shanghai',
		'China Standard Time'              => 'Asia/Shanghai',
		'Choibalsan'                       => 'Asia/Choibalsan',
		'Christmas'                        => 'Indian/Christmas',
		'Cocos'                            => 'Indian/Cocos',
		'Colombia'                         => 'America/Bogota',
		'Cook'                             => 'Pacific/Rarotonga',
		'Cuba'                             => 'America/Havana',
		'Dacca'                            => 'Asia/Dhaka',
		'Dateline Standard Time'           => 'Etc/GMT+12',
		'Davis'                            => 'Antarctica/Davis',
		'Dominican'                        => 'America/Santo_Domingo',
		'DumontDUrville'                   => 'Antarctica/DumontDUrville',
		'Dushanbe'                         => 'Asia/Dushanbe',
		'Dutch_Guiana'                     => 'America/Paramaribo',
		'E. Africa Standard Time'          => 'Africa/Nairobi',
		'E. Australia Standard Time'       => 'Australia/Brisbane',
		'E. Europe Standard Time'          => 'Europe/Minsk',
		'E. South America Standard Time'   => 'America/Sao_Paulo',
		'East_Timor'                       => 'Asia/Dili',
		'Easter'                           => 'Pacific/Easter',
		'Eastern Standard Time'            => 'America/New_York',
		'Ecuador'                          => 'America/Guayaquil',
		'Egypt Standard Time'              => 'Africa/Cairo',
		'Ekaterinburg Standard Time'       => 'Asia/Yekaterinburg',
		'Europe_Central'                   => 'Europe/Paris',
		'Europe_Eastern'                   => 'Europe/Bucharest',
		'Europe_Western'                   => 'Atlantic/Canary',
		'FLE Standard Time'                => 'Europe/Kiev',
		'Falkland'                         => 'Atlantic/Stanley',
		'Fiji'                             => 'Pacific/Fiji',
		'Fiji Standard Time'               => 'Pacific/Fiji',
		'French_Guiana'                    => 'America/Cayenne',
		'French_Southern'                  => 'Indian/Kerguelen',
		'Frunze'                           => 'Asia/Bishkek',
		'GMT'                              => 'UTC', // seems better than 'Atlantic/Reykjavik'
		'GMT Standard Time'                => 'Europe/London',
		'GTB Standard Time'                => 'Europe/Istanbul',
		'Galapagos'                        => 'Pacific/Galapagos',
		'Gambier'                          => 'Pacific/Gambier',
		'Georgia'                          => 'Asia/Tbilisi',
		'Georgian Standard Time'           => 'Etc/GMT-3',
		'Gilbert_Islands'                  => 'Pacific/Tarawa',
		'Goose_Bay'                        => 'America/Goose_Bay',
		'Greenland Standard Time'          => 'America/Godthab',
		'Greenland_Central'                => 'America/Scoresbysund',
		'Greenland_Eastern'                => 'America/Scoresbysund',
		'Greenland_Western'                => 'America/Godthab',
		'Greenwich Standard Time'          => 'Atlantic/Reykjavik',
		'Guam'                             => 'Pacific/Guam',
		'Gulf'                             => 'Asia/Dubai',
		'Guyana'                           => 'America/Guyana',
		'Hawaii_Aleutian'                  => 'Pacific/Honolulu',
		'Hawaiian Standard Time'           => 'Pacific/Honolulu',
		'Hong_Kong'                        => 'Asia/Hong_Kong',
		'Hovd'                             => 'Asia/Hovd',
		'India'                            => 'Asia/Calcutta',
		'India Standard Time'              => 'Asia/Calcutta',
		'Indian_Ocean'                     => 'Indian/Chagos',
		'Indochina'                        => 'Asia/Saigon',
		'Indonesia_Central'                => 'Asia/Makassar',
		'Indonesia_Eastern'                => 'Asia/Jayapura',
		'Indonesia_Western'                => 'Asia/Jakarta',
		'Iran'                             => 'Asia/Tehran',
		'Iran Standard Time'               => 'Asia/Tehran',
		'Irish'                            => 'Europe/Dublin',
		'Irkutsk'                          => 'Asia/Irkutsk',
		'Israel'                           => 'Asia/Jerusalem',
		'Israel Standard Time'             => 'Asia/Jerusalem',
		'Japan'                            => 'Asia/Tokyo',
		'Jordan Standard Time'             => 'Asia/Amman',
		'Kamchatka'                        => 'Asia/Kamchatka',
		'Karachi'                          => 'Asia/Karachi',
		'Kashgar'                          => 'Asia/Kashgar',
		'Kazakhstan_Eastern'               => 'Asia/Almaty',
		'Kazakhstan_Western'               => 'Asia/Aqtobe',
		'Kizilorda'                        => 'Asia/Qyzylorda',
		'Korea'                            => 'Asia/Seoul',
		'Korea Standard Time'              => 'Asia/Seoul',
		'Kosrae'                           => 'Pacific/Kosrae',
		'Krasnoyarsk'                      => 'Asia/Krasnoyarsk',
		'Kuybyshev'                        => 'Europe/Samara',
		'Kwajalein'                        => 'Pacific/Kwajalein',
		'Kyrgystan'                        => 'Asia/Bishkek',
		'Lanka'                            => 'Asia/Colombo',
		'Liberia'                          => 'Africa/Monrovia',
		'Line_Islands'                     => 'Pacific/Kiritimati',
		'Long_Shu'                         => 'Asia/Chongqing',
		'Lord_Howe'                        => 'Australia/Lord_Howe',
		'Macau'                            => 'Asia/Macau',
		'Magadan'                          => 'Asia/Magadan',
		'Malaya'                           => 'Asia/Kuala_Lumpur',
		'Malaysia'                         => 'Asia/Kuching',
		'Maldives'                         => 'Indian/Maldives',
		'Marquesas'                        => 'Pacific/Marquesas',
		'Marshall_Islands'                 => 'Pacific/Majuro',
		'Mauritius'                        => 'Indian/Mauritius',
		'Mauritius Standard Time'          => 'Indian/Mauritius',
		'Mawson'                           => 'Antarctica/Mawson',
		'Mexico Standard Time'             => 'America/Mexico_City',
		'Mexico Standard Time 2'           => 'America/Chihuahua',
		'Mid-Atlantic Standard Time'       => 'Atlantic/South_Georgia',
		'Middle East Standard Time'        => 'Asia/Beirut',
		'Mongolia'                         => 'Asia/Ulaanbaatar',
		'Montevideo Standard Time'         => 'America/Montevideo',
		'Morocco Standard Time'            => 'Africa/Casablanca',
		'Moscow'                           => 'Europe/Moscow',
		'Mountain Standard Time'           => 'America/Denver',
		'Mountain Standard Time (Mexico)'  => 'America/Chihuahua',
		'Myanmar'                          => 'Asia/Rangoon',
		'Myanmar Standard Time'            => 'Asia/Rangoon',
		'N. Central Asia Standard Time'    => 'Asia/Novosibirsk',
		'Namibia Standard Time'            => 'Africa/Windhoek',
		'Nauru'                            => 'Pacific/Nauru',
		'Nepal'                            => 'Asia/Katmandu',
		'Nepal Standard Time'              => 'Asia/Katmandu',
		'New Zealand Standard Time'        => 'Pacific/Auckland',
		'New_Caledonia'                    => 'Pacific/Noumea',
		'New_Zealand'                      => 'Pacific/Auckland',
		'Newfoundland'                     => 'America/St_Johns',
		'Newfoundland Standard Time'       => 'America/St_Johns',
		'Niue'                             => 'Pacific/Niue',
		'Norfolk'                          => 'Pacific/Norfolk',
		'Noronha'                          => 'America/Noronha',
		'North Asia East Standard Time'    => 'Asia/Irkutsk',
		'North Asia Standard Time'         => 'Asia/Krasnoyarsk',
		'North_Mariana'                    => 'Pacific/Saipan',
		'Novosibirsk'                      => 'Asia/Novosibirsk',
		'Omsk'                             => 'Asia/Omsk',
		'Oral'                             => 'Asia/Oral',
		'Pacific SA Standard Time'         => 'America/Santiago',
		'Pacific Standard Time'            => 'America/Los_Angeles',
		'Pacific Standard Time (Mexico)'   => 'America/Tijuana',
		'Pakistan'                         => 'Asia/Karachi',
		'Pakistan Standard Time'           => 'Asia/Karachi',
		'Palau'                            => 'Pacific/Palau',
		'Papua_New_Guinea'                 => 'Pacific/Port_Moresby',
		'Paraguay'                         => 'America/Asuncion',
		'Peru'                             => 'America/Lima',
		'Philippines'                      => 'Asia/Manila',
		'Phoenix_Islands'                  => 'Pacific/Enderbury',
		'Pierre_Miquelon'                  => 'America/Miquelon',
		'Pitcairn'                         => 'Pacific/Pitcairn',
		'Ponape'                           => 'Pacific/Ponape',
		'Qyzylorda'                        => 'Asia/Qyzylorda',
		'Reunion'                          => 'Indian/Reunion',
		'Romance Standard Time'            => 'Europe/Paris',
		'Rothera'                          => 'Antarctica/Rothera',
		'Russian Standard Time'            => 'Europe/Moscow',
		'SA Eastern Standard Time'         => 'Etc/GMT+3',
		'SA Pacific Standard Time'         => 'America/Bogota',
		'SA Western Standard Time'         => 'America/La_Paz',
		'SE Asia Standard Time'            => 'Asia/Bangkok',
		'Sakhalin'                         => 'Asia/Sakhalin',
		'Samara'                           => 'Europe/Samara',
		'Samarkand'                        => 'Asia/Samarkand',
		'Samoa'                            => 'Pacific/Apia',
		'Samoa Standard Time'              => 'Pacific/Apia',
		'Seychelles'                       => 'Indian/Mahe',
		'Shevchenko'                       => 'Asia/Aqtau',
		'Singapore'                        => 'Asia/Singapore',
		'Singapore Standard Time'          => 'Asia/Singapore',
		'Solomon'                          => 'Pacific/Guadalcanal',
		'South Africa Standard Time'       => 'Africa/Johannesburg',
		'South_Georgia'                    => 'Atlantic/South_Georgia',
		'Sri Lanka Standard Time'          => 'Asia/Colombo',
		'Suriname'                         => 'America/Paramaribo',
		'Sverdlovsk'                       => 'Asia/Yekaterinburg',
		'Syowa'                            => 'Antarctica/Syowa',
		'Tahiti'                           => 'Pacific/Tahiti',
		'Taipei'                           => 'Asia/Taipei',
		'Taipei Standard Time'             => 'Asia/Taipei',
		'Tajikistan'                       => 'Asia/Dushanbe',
		'Tashkent'                         => 'Asia/Tashkent',
		'Tasmania Standard Time'           => 'Australia/Hobart',
		'Tbilisi'                          => 'Asia/Tbilisi',
		'Tokelau'                          => 'Pacific/Fakaofo',
		'Tokyo Standard Time'              => 'Asia/Tokyo',
		'Tonga'                            => 'Pacific/Tongatapu',
		'Tonga Standard Time'              => 'Pacific/Tongatapu',
		'Truk'                             => 'Pacific/Truk',
		'Turkey'                           => 'Europe/Istanbul',
		'Turkmenistan'                     => 'Asia/Ashgabat',
		'Tuvalu'                           => 'Pacific/Funafuti',
		'US/Eastern'                       => 'America/New_York',
		'US Eastern Standard Time'         => 'Etc/GMT+5',
		'US Mountain Standard Time'        => 'America/Phoenix',
		'Uralsk'                           => 'Asia/Oral',
		'Uruguay'                          => 'America/Montevideo',
		'Urumqi'                           => 'Asia/Urumqi',
		'Uzbekistan'                       => 'Asia/Tashkent',
		'Vanuatu'                          => 'Pacific/Efate',
		'Venezuela'                        => 'America/Caracas',
		'Venezuela Standard Time'          => 'America/Caracas',
		'Vladivostok'                      => 'Asia/Vladivostok',
		'Vladivostok Standard Time'        => 'Asia/Vladivostok',
		'Volgograd'                        => 'Europe/Volgograd',
		'Vostok'                           => 'Antarctica/Vostok',
		'W. Australia Standard Time'       => 'Australia/Perth',
		'W. Central Africa Standard Time'  => 'Africa/Lagos',
		'W. Europe Standard Time'          => 'Europe/Berlin',
		'Wake'                             => 'Pacific/Wake',
		'Wallis'                           => 'Pacific/Wallis',
		'West Asia Standard Time'          => 'Asia/Tashkent',
		'West Pacific Standard Time'       => 'Pacific/Port_Moresby',
		'Yakutsk'                          => 'Asia/Yakutsk',
		'Yakutsk Standard Time'            => 'Asia/Yakutsk',
		'Yekaterinburg'                    => 'Asia/Yekaterinburg',
		'Yerevan'                          => 'Asia/Yerevan',
		'Yukon'                            => 'America/Yakutat',
	);

	/**
	 * @var array Map of timezones acceptable by DateTimeZone but not strtotime.
	 */
	protected $_invalid_legacy  = array(
		'US/Eastern' => true,
	);

	/**
	 * @var array|bool List of system identifiers or false if none available.
	 */
	protected $_identifiers     = false;

	/**
	 * Initialize local cache and identifiers.
	 *
	 * @param Ai1ec_Registry_Object $registry Registry to use.
	 *
	 * @return void
	 */
	public function __construct( Ai1ec_Registry_Object $registry ) {
		parent::__construct( $registry );
		$this->_cache = $this->_registry->get( 'cache.memory' );
		$this->_init_identifiers();
	}

	/**
	 * Get default timezone to use in input/output.
	 *
	 * Approach is as follows:
	 * - check user profile for timezone preference;
	 * - if user has no preference - check site for timezone selection;
	 * - if site has no selection - raise notice and use 'UTC'.
	 *
	 * @return string Olson timezone string identifier.
	 */
	public function get_default_timezone() {
		static $default_timezone = null;
		if ( null === $default_timezone ) {
			$candidates = array();
			$candidates[] = (string)$this->_registry->get( 'model.meta-user' )
				->get_current( 'ai1ec_timezone' );
			$candidates[] = (string)$this->_registry->get( 'model.option' )
				->get( 'timezone_string' );
			$candidates[] = (string)$this->_registry->get( 'model.option' )
				->get( 'gmt_offset' );
			$candidates   = array_filter( $candidates, 'strlen' );
			foreach ( $candidates as $timezone ) {
				$timezone = $this->get_name( $timezone );
				if ( false !== $timezone ) {
					$default_timezone = $timezone;
					break;
				}
			}
			if ( null === $default_timezone ) {
				$default_timezone = 'UTC';
				$this->_registry->get( 'notification.admin' )->store(
					sprintf(
						Ai1ec_I18n::__(
							'Please select site timezone in %s <em>Timezone</em> dropdown menu.'
						),
						'<a href="' . admin_url( 'options-general.php' ) .
						'">' . Ai1ec_I18n::__( 'Settings' ) . '</a>'
					),
					'error'
				);
			}
		}
		return $default_timezone;
	}

	/**
	 * Attempt to decode GMT offset to some Olson timezone.
	 *
	 * @param float $zone GMT offset.
	 *
	 * @return string Valid Olson timezone name (UTC is last resort).
	 */
	public function decode_gmt_timezone( $zone ) {
		$auto_zone = timezone_name_from_abbr( null, $zone * 3600, true );
		if ( false !== $auto_zone ) {
			return $auto_zone;
		}
		$auto_zone = timezone_name_from_abbr(
			null,
			( (int) $zone ) * 3600,
			true
		);
		if ( false !== $auto_zone ) {
			return $auto_zone;
		}
		$this->_registry->get( 'notification.admin' )->store(
			sprintf(
				Ai1ec_I18n::__(
					'Timezone "UTC%+d" is not recognized. Please %suse valid%s timezone name, until then events will be created in UTC timezone.'
				),
				$zone,
				'<a href="' . admin_url( 'options-general.php' ) . '">',
				'</a>'
			),
			'error'
		);
		return 'UTC';
	}

	/**
	 * Get valid timezone name from input.
	 *
	 * @param string $zone Name to check/parse.
	 *
	 * @return string Timezone name to use
	 */
	public function get_name( $zone ) {
		if ( is_numeric( $zone ) ) {
			$decoded_zone = $this->decode_gmt_timezone( $zone );
			if ( 'UTC' !== $decoded_zone ) {
				$message = sprintf(
					Ai1ec_I18n::__(
						'Selected timezone "UTC%+d" will be treated as %s.'
					),
					$zone,
					$decoded_zone
				);
				$this->_registry->get( 'notification.admin' )
					->store( $message );
			}
			$zone = $decoded_zone;
		}
		if ( false === $this->_identifiers ) {
			return $zone; // anything should do, as zones are not supported
		}
		if ( ! isset( $this->_identifiers[$zone] ) ) {
			$zone         = $this->_olson_lookup( $zone );
			$valid_legacy = false;
			try {
				new DateTimeZone( $zone ); // throw away instantly
				$valid_legacy = true;
			} catch ( Exception $excpt ) {
				$valid_legacy = false;
			}
			if ( ! $valid_legacy || isset( $this->_invalid_legacy[$zone] ) ) {
				return $this->guess_zone( $zone );
			}
			$this->_identifiers[$zone] = $zone;
			unset( $valid_legacy );
		}
		return $zone;
	}

	/**
	 * Quick map look-up to discard zones that have limited recognition.
	 *
	 * @param string $zone Name of timezone to lookup.
	 *
	 * @return string Timezone name to use. Might be the same as $zone.
	 */
	protected function _olson_lookup( $zone ) {
		if ( isset( $this->_zones[$zone] ) ) {
			return $this->_zones[$zone];
		}
		return $zone;
	}

	/**
	 * Check if timezone is set in wp_option
	 * 
	 */
	public function is_timezone_not_set() {
		$timezone = $this->_registry->get( 'model.option' )
			->get( 'timezone_string' );
		return empty( $timezone );
	}

	/**
	 * Render options for select in settings
	 * 
	 * @return array
	 */
	public function get_timezones( $only_zones = false ) {
		$zones = DateTimeZone::listIdentifiers();
		if (
			empty( $zones )
		) {
			return array();
		}
		if ( ! $only_zones ) {
			$manual =  __( 'Manual Offset', AI1EC_PLUGIN_NAME );
			$options = array();
			$options[$manual][] = array(
				'text'  => __( 'Choose your timezone', AI1EC_PLUGIN_NAME ),
				'value' => '',
				'args'  => array(
					'selected' => 'selected'
				)
			);
		}
		foreach ( $zones as $zone ) {
			$exploded_zone = explode( '/', $zone );
			if ( ! isset( $exploded_zone[1] ) && ! $only_zones ) {
				$exploded_zone[1] = $exploded_zone[0];
				$exploded_zone[0] = $manual;
			}
			$optgroup = $exploded_zone[0];
			unset( $exploded_zone[0] );
			$options[$optgroup][] = array(
				'text'  => implode( '/', $exploded_zone ),
				'value' => $zone,
			);
		}
		
		return $options;
	}

	/**
	 * Guess valid timezone identifier from arbitrary input.
	 *
	 * @param string $meta_name Arbitrary input.
	 *
	 * @return string|bool Parsed timezone name or false if none found.
	 */
	public function guess_zone( $meta_name ) {
		if ( isset( $this->_zones[$meta_name] ) ) {
			return $this->_zones[$meta_name];
		}
		$name_variants = array(
			strtr( $meta_name, ' ', '_' ),
			strtr( $meta_name, '_', ' ' ),
		);
		if ( false !== ( $parenthesis_pos = strpos( $meta_name, '(' ) ) ) {
			foreach ( $name_variants as $name ) {
				$name_variants[] = substr( $name, 0, $parenthesis_pos - 1 );
			}
		}
		foreach ( $name_variants as $name ) {
			if ( isset( $this->_zones[$name] ) ) {
				// cache to avoid future lookups and return
				$this->_zones[$meta_name] = $this->_zones[$name];
				return $this->_zones[$name];
			}
		}
		if (
			isset( $meta_name{0} ) &&
			'(' === $meta_name{0} &&
			$closing_pos = strpos( $meta_name, ')' )
		) {
			$meta_name = trim( substr( $meta_name, $closing_pos + 1 ) );
			return $this->guess_zone( $meta_name );
		}
		if (
			false === strpos( $meta_name, ' Standard ' ) &&
			false !== ( $time_pos = strpos( $meta_name, ' Time' ) )
		) {
			$meta_name = substr( $meta_name, 0, $time_pos ) .
				' Standard' . substr( $meta_name, $time_pos );
			return $this->guess_zone( $meta_name );
		}
		return false;
	}

	/**
	 * Get timezone object instance.
	 *
	 * @param string $timezone Name of timezone to get instance for.
	 *
	 * @return DateTimeZone Instance of timezone object.
	 *
	 * @throws Ai1ec_Date_Timezone_Exception If an error occurs.
	 */
	public function get( $timezone ) {
		if ( 'sys.default' === $timezone ) {
			$timezone = $this->get_default_timezone();
		}
		$name = $this->get_name( $timezone );
		if ( ! $name ) {
			throw new Ai1ec_Date_Timezone_Exception(
				'Unrecognized timezone \'' . $timezone . '\''
			);
		}
		$zone = $this->_cache->get( $name, null );
		if ( null === $zone ) {
			$exception = null;
			try {
				$zone = new DateTimeZone( $name );
			} catch ( Exception $invalid_tz ) {
				$exception = $invalid_tz;
			}
			if ( null !== $exception ) {
				throw new Ai1ec_Date_Timezone_Exception( $exception->getMessage() );
			}
			$this->_cache->set( $name, $zone );
		}
		return $zone;
	}

	/**
	 * Add system identifiers to object registry.
	 *
	 * @return bool Success
	 */
	protected function _init_identifiers() {
		$identifiers  = DateTimeZone::listIdentifiers();
		if ( ! $identifiers ) {
			return false;
		}
		$mapped = array();
		foreach ( $identifiers as $zone ) {
			$zone                = (string)$zone;
			$mapped[$zone]       = true;
			$this->_zones[$zone] = $zone;
		}
		unset( $identifiers, $zone );
		$this->_identifiers = $mapped;
		return true;
	}

}
