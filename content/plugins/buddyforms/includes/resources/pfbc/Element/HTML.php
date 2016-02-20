<?php
class Element_HTML extends Element {

	protected $_attributes = array("type" => "html");

	public function __construct($value) {
		$properties = array("value" => $value);
		parent::__construct("", "", $properties);
	}

	public function render() {
		echo $this->_attributes["value"];
	}
}
