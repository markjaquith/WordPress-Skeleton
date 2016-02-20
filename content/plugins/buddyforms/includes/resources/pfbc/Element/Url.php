<?php
class Element_Url extends Element_Textbox {
	protected $_attributes = array("type" => "url");

	public function render() {
		$this->validation[] = new Validation_Url;
		parent::render();
	}
}
