<?php


class PPMissingCredentialException extends Exception
{

	public function __construct( $message = null, $code = 0 )
	{
		parent::__construct( $message, $code );
	}

	public function errorMessage()
	{
		$errorMsg = 'Error on line ' . $this->getLine() . ' in ' . $this->getFile()
			. ': <b>' . $this->getMessage() . '</b>';

		return $errorMsg;
	}

}

?>