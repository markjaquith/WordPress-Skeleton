<?php
class Element_Month extends Element_Textbox {
    protected $_attributes = array(
        "type" => "month",
        "pattern" => "\d{4}-\d{2}"
    );

    public function __construct($label, $name, array $properties = null) {
        $this->_attributes["placeholder"] = "YYYY-MM (e.g. " . date("Y-m") . ")";
        $this->_attributes["title"] = $this->_attributes["placeholder"];

        parent::__construct($label, $name, $properties);
    }

    public function render() {
        $this->validation[] = new Validation_RegExp("/" . $this->_attributes["pattern"] . "/", "Error: The %element% field must match the following date format: " . $this->_attributes["title"]);
        parent::render();
    }
}
