<?php
class Element_Email extends Element_Textbox {
	protected $_attributes = array("type" => "email");

	public function render() {
		$this->validation[] = new Validation_Email;
		parent::render();
	}
}
