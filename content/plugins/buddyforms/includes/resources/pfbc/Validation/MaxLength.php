<?php
class Validation_MaxLength extends Validation {
	protected $message;
	protected $limit;

	public function __construct($limit, $message = "") {
		$this->limit = $limit;
		if(empty($message))
			$message = "%element% is limited to " . $limit . " characters.";
		parent::__construct($message);
	}

	public function isValid($value) {
		if($this->isNotApplicable($value) || strlen($value) <= $this->limit)
			return true;
		return false;	
	}
}
