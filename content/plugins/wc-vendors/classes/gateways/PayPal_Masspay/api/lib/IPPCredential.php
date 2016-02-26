<?php

/**
 * Base class that represents API credentials
 */
abstract class IPPCredential
{
	/**
	 * Application Id that uniquely identifies the application
	 * The application Id is issued by PayPal.
	 * Test application Ids are available for the sandbox environment
	 * @var string
	 */
	protected $applicationId;

	/**
	 * API username
	 * @var string
	 */
	protected $userName;

	/**
	 * API password
	 * @var string
	 */
	protected $password;

	protected abstract function validate();

	public function __construct( $userName, $password, $applicationId )
	{
		$this->userName      = $userName;
		$this->password      = $password;
		$this->applicationId = $applicationId;
	}

	public function getApplicationId()
	{
		return $this->applicationId;
	}

	public function getUserName()
	{
		return $this->userName;
	}

	public function getPassword()
	{
		return $this->password;
	}
}

?>