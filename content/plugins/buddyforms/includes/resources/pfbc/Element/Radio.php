<?php
class Element_Radio extends OptionElement {
	protected $_attributes = array("type" => "radio");
	protected $inline;

	public function render() { 
		$labelClass = $this->_attributes["type"];
		if(!empty($this->inline))
			$labelClass .= " inline";

		$count = 0;
		foreach($this->options as $value => $text) {
			$value = $this->getOptionValue($value);
			$this->_attributes["id"] = isset($this->_attributes["id"]) ? $this->_attributes["id"] : '';
			echo '<label class="', $labelClass . '"> <input id="', $this->_attributes["id"], '-', $count, '"', $this->getAttributes(array("id", "value", "checked")), ' value="', $this->filter($value), '"';
			if(isset($this->_attributes["value"]) && $this->_attributes["value"] == $value)
				echo ' checked="checked"';
			echo '/> ', $text, ' </label><br> ';
			++$count;
		}	
	}
}
