<?php
/**
 * PPConfigManager loads the SDK configuration file and
 * hands out appropriate config params to other classes
 */
require_once 'exceptions/PPConfigurationException.php';

class PPConfigManager
{

	private $config;
	/**
	 *
	 *
	 * @var PPConfigManager
	 */
	private static $instance;

	private function __construct()
	{
		$configFile = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . ".."
			. DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "sdk_config.ini";
		$this->load( $configFile );
	}

	// create singleton object for PPConfigManager
	public static function getInstance()
	{
		if ( !isset( self::$instance ) ) {
			self::$instance = new PPConfigManager();
		}

		return self::$instance;
	}

	//used to load the file
	private function load( $fileName )
	{
		if ( class_exists( 'Woocommerce' ) ) {
			global $woocommerce;
			$gateways = $woocommerce->payment_gateways->payment_gateways();
			$settings = $gateways[ 'paypalap' ]->settings;

			$mode = $settings[ 'sandbox_enabled' ];

			$this->config = array(
				'acct1.UserName'         => $mode == 'yes' ? $settings[ 'username' ] : $settings[ 'username_live' ],
				'acct1.Password'         => $mode == 'yes' ? $settings[ 'password' ] : $settings[ 'password_live' ],
				'acct1.Signature'        => $mode == 'yes' ? $settings[ 'signature' ] : $settings[ 'signature_live' ],
				'acct1.AppId'            => $mode == 'yes' ? 'APP-80W284485P519543T' : $settings[ 'app_id' ],

				'service.Binding'        => 'NV',
				'service.EndPoint'       => $mode == 'yes' ? 'https://svcs.sandbox.paypal.com/' : 'https://svcs.paypal.com/',
				'service.RedirectURL'    => $mode == 'yes' ? 'https://sandbox.paypal.com/webscr&cmd=' : 'https://paypal.com/webscr&cmd=',
				'service.DevCentralURL'  => 'https://developer.paypal.com',
				'http.ConnectionTimeOut' => '30',
				'http.Retry'             => '5',
				'log.FileName'           => 'PayPal.log',
				'log.LogLevel'           => 'INFO',
				'log.LogEnabled'         => $settings[ 'logging_enabled' ] == 'yes' ? 'true' : 'false',
			);
		} else {
			$this->config = @parse_ini_file( $fileName );
		}

		if ( $this->config == null || count( $this->config ) == 0 ) {
			throw new PPConfigurationException( "Config file $fileName not found", "303" );
		}
	}

	/**
	 * simple getter for configuration params
	 * If an exact match for key is not found,
	 * does a "contains" search on the key
	 */
	public function get( $searchKey )
	{

		if ( array_key_exists( $searchKey, $this->config ) ) {
			return $this->config[ $searchKey ];
		} else {
			$arr = array();
			foreach ( $this->config as $k => $v ) {
				if ( strstr( $k, $searchKey ) ) {
					$arr[ $k ] = $v;
				}
			}

			return $arr;
		}

	}

	/**
	 * Utility method for handling account configuration
	 * return config key corresponding to the API userId passed in
	 *
	 * If $userId is null, returns config keys corresponding to
	 * all configured accounts
	 */
	public function getIniPrefix( $userId = null )
	{

		if ( $userId == null ) {
			$arr = array();
			foreach ( $this->config as $key => $value ) {
				$pos = strpos( $key, '.' );
				if ( strstr( $key, "acct" ) ) {
					$arr[ ] = substr( $key, 0, $pos );
				}
			}

			return array_unique( $arr );
		} else {
			$iniPrefix = array_search( $userId, $this->config );
			$pos       = strpos( $iniPrefix, '.' );
			$acct      = substr( $iniPrefix, 0, $pos );

			return $acct;
		}
	}
}

?>
