<?php
class Element_jQueryUIDate extends Element_Textbox {
	protected $_attributes = array(
		"type" => "text",
		"autocomplete" => "off"
	);
	protected $jQueryOptions;

	public function getCSSFiles() {
		return array(
			//$this->_form->getResourcesPath() . "/jquery-ui/css/smoothness/jquery-ui.min.css"
		);
	}

	public function getJSFiles() {
		return array(
		//	$this->_form->getResourcesPath() . "/jquery-ui/js/jquery-ui.min.js"
		);
	}

    public function jQueryDocumentReady() {
        parent::jQueryDocumentReady();
        echo 'jQuery("#', $this->_attributes["id"], '").datepicker(', $this->jQueryOptions(), ');';
    }

    public function render() {
        $this->validation[] = new Validation_Date;
        parent::render();
    }
}
