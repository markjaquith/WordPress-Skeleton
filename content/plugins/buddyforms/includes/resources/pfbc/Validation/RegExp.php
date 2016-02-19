<?php
class Validation_RegExp extends Validation {
	protected $message = "Error: %element% contains invalid characters.";
	protected $pattern;

	public function __construct($pattern, $message = "") {
		$this->pattern = $pattern;
		parent::__construct($message);
	}

	public function isValid($value) {
		if($this->isNotApplicable($value) || preg_match($this->pattern, $value))
			return true;
		return false;	
	}
}
