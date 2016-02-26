<?php
class PPConnectionException extends Exception
{
	/**
	 * The url that was being connected to when the exception occured
	 * @var string
	 */
	private $url;

	public function __construct( $url, $message, $code = 0 )
	{
		parent::__construct( $message, $code );
		$this->url = $url;
	}

	public function getUrl()
	{
		return $this->url;
	}
}