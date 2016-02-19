<?php
class Element_Textbox extends Element {
	protected $_attributes = array("type" => "text");
	protected $prepend;
	protected $append;

	public function render() {
		$addons = array();
		if(!empty($this->prepend))
			$addons[] = "input-prepend";
		if(!empty($this->append))
			$addons[] = "input-append";
		if(!empty($addons))
			echo '<div class="', implode(" ", $addons), '">';

		$this->renderAddOn("prepend");
		parent::render();
		$this->renderAddOn("append");

		if(!empty($addons))
			echo '</div>';
	}

	protected function renderAddOn($type = "prepend") {
		if(!empty($this->$type)) {
			$span = true;
			if(strpos($this->$type, "<button") !== false)
				$span = false;

			if($span)
				echo '<span class="add-on">';

			echo $this->$type;

			if($span)
				echo '</span>';
		}
	}
}
