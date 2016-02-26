<?php
class Element_Checkbox extends OptionElement {
	protected $_attributes = array("type" => "checkbox");
	protected $inline;

	public function render() {
		//echo $this->getAttributes();

		if(isset($this->_attributes["value"])) {
			if(!is_array($this->_attributes["value"]))
				$this->_attributes["value"] = array($this->_attributes["value"]);
		}
		else
			$this->_attributes["value"] = array();

		if(substr($this->_attributes["name"], -2) != "[]")
			$this->_attributes["name"] .= "[]";

		$labelClass = $this->_attributes["type"];
		if(!empty($this->inline))
			$labelClass .= " inline";

		$count = 0;

        if(!isset( $this->_attributes["id"]))
            $this->_attributes["id"] = $this->_attributes["name"];

		foreach($this->options as $value => $text) {
			$value = $this->getOptionValue($value);

			//echo 'da' . $labelClass;

			echo '<label class="', $labelClass, '"> <input id="', $this->_attributes["id"], '-', $count, '"', $this->getAttributes(array("id", "value", "checked")), ' value="', $this->filter($value), '"';

//			if($this->isRequired())
//				echo ' required="required"';
			if(in_array($value, $this->_attributes["value"]))
				echo ' checked="checked"';
			echo '/> ', $text, ' </label><br> ';
			++$count;
		}	
	}
}
