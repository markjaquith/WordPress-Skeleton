<?php
class View_SideBySide extends View {
	protected $class = "form-horizontal";

	public function render() {
		$this->_form->appendAttribute("class", $this->class);

		echo '<form data-ajax="false"', $this->_form->getAttributes(), '><fieldset>';
		$this->_form->getErrorView()->render();

		$elements = $this->_form->getElements();
		$elementSize = sizeof($elements);
		$elementCount = 0;
		for($e = 0; $e < $elementSize; ++$e) {
			$element = $elements[$e];

			if($element instanceof Element_Hidden || $element instanceof Element_HTML)
				$element->render();
            elseif($element instanceof Element_Button) {
                if($e == 0 || !$elements[($e - 1)] instanceof Element_Button)
					echo '<div class="form-actions">';
				else
					echo ' ';
				
				$element->render();

                if(($e + 1) == $elementSize || !$elements[($e + 1)] instanceof Element_Button)
                    echo '</div>';
            }
            else {
				echo '<div class="control-group">', $this->renderLabel($element), '<div class="controls">', $element->render(), $this->renderDescriptions($element), '</div></div>';
				++$elementCount;
			}
		}

		echo '</fieldset></form>';
    }

	protected function renderLabel(Element $element) {
        $label = $element->getLabel();
        if(!empty($label)) {
			echo '<label class="control-label" for="', $element->getAttribute("id"), '">';
			if($element->isRequired())
				echo '<span class="required">* </span>';
			echo $label, '</label>'; 
        }
    }
}
