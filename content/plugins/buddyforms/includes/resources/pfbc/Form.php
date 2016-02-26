<?php
/*This project's namespace structure is leveraged to autoload requested classes at runtime.*/
function PFBC_Load($class) {
	$file = dirname(__FILE__) . "/" . str_replace("_", DIRECTORY_SEPARATOR, $class) . ".php";
	if(is_file($file))
		include_once $file;
}
spl_autoload_register("PFBC_Load");
if(in_array("__autoload", spl_autoload_functions()))
	spl_autoload_register("__autoload");

class Form extends Base {
	protected $_elements = array();
	protected $_prefix = "http";
	protected $_values = array();
	protected $_attributes = array();

	protected $ajax;
	protected $ajaxCallback;
	protected $errorView;
	protected $labelToPlaceholder;
	protected $resourcesPath;
	/*Prevents various automated from being automatically applied.  Current options for this array
	included jquery, jqueryui, bootstrap and focus.*/
	protected $prevent = array();
	protected $view;

	public function __construct($id = "pfbc") {
		$this->configure(array(
			"action" => basename($_SERVER["SCRIPT_NAME"]),
			"id" => preg_replace("/\W/", "-", $id),
			"method" => "post"
		));

		if(isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on")
			$this->_prefix = "https";
		
		/*The Standard view class is applied by default and will be used unless a different view is
		specified in the form's configure method*/
		if(empty($this->view))
			$this->view = new View_SideBySide;

		if(empty($this->errorView))
			$this->errorView = new ErrorView_Standard;
		
		/*The resourcesPath property is used to identify where third-party resources needed by the
		project are located.  This property will automatically be set properly if the PFBC directory
		is uploaded within the server's document root.  If symbolic links are used to reference the PFBC
		directory, you may need to set this property in the form's configure method or directly in this
		constructor.*/
		$path = dirname(__FILE__) . "/Resources";
		if(strpos($path, $_SERVER["DOCUMENT_ROOT"]) !== false)
			$this->resourcesPath = substr($path, strlen($_SERVER["DOCUMENT_ROOT"]));
		else
			$this->resourcesPath = "/PFBC/Resources";
	}

	/*When a form is serialized and stored in the session, this function prevents any non-essential
	information from being included.*/
	public function __sleep() {
		return array("_attributes", "_elements", "errorView");
	}

	public function addElement(Element $element) {
		$element->_setForm($this);

		//If the element doesn't have a specified id, a generic identifier is applied.
		$id = $element->getAttribute("id");
        if(empty($id))
            $element->setAttribute("id", $this->_attributes["id"] . "-element-" . sizeof($this->_elements));
        $this->_elements[] = $element;

		/*For ease-of-use, the form tag's encytype attribute is automatically set if the File element
		class is added.*/
		if($element instanceof Element_File)
			$this->_attributes["enctype"] = "multipart/form-data";
    }

	/*Values that have been set through the setValues method, either manually by the developer
	or after validation errors, are applied to elements within this method.*/
    protected function applyValues() {
        foreach($this->_elements as $element) {
			$name = $element->getAttribute("name");
            if(isset($this->_values[$name]))
				$element->setAttribute("value", $this->_values[$name]);
            elseif(substr($name, -2) == "[]" && isset($this->_values[substr($name, 0, -2)]))
				$element->setAttribute("value", $this->_values[substr($name, 0, -2)]);
        }
    }

	public static function clearErrors($id = "pfbc") {
		if(!empty($_SESSION["pfbc"][$id]["errors"]))
			unset($_SESSION["pfbc"][$id]["errors"]);
	}

	public static function clearValues($id = "pfbc") {
		if(!empty($_SESSION["pfbc"][$id]["values"]))
			unset($_SESSION["pfbc"][$id]["values"]);
	}

    public function getAjax() {
        return $this->ajax;
    }

    public function getElements() {
        return $this->_elements;
    }

	public function getErrorView() {
		return $this->errorView;
	}

	public function getPrefix() {
		return $this->_prefix;
	}

    public function getResourcesPath() {
        return $this->resourcesPath;
    }

	public function getErrors() {
		$errors = array();
		if(session_id() == "")
			$errors[""] = array("Error: The pfbc project requires an active session to function properly.  Simply add session_start() to your script before any output has been sent to the browser.");
		else {
			$errors = array();
			$id = $this->_attributes["id"];
			if(!empty($_SESSION["pfbc"][$id]["errors"]))
				$errors = $_SESSION["pfbc"][$id]["errors"];
		}	

		return $errors;	
	}

	protected static function getSessionValues($id = "pfbc") {
		$values = array();
		if(!empty($_SESSION["pfbc"][$id]["values"]))
			$values = $_SESSION["pfbc"][$id]["values"];
		return $values;
	}

	public static function isValid($id = "pfbc", $clearValues = true) {
		$valid = true;
		/*The form's instance is recovered (unserialized) from the session.*/
		$form = self::recover($id);
		if(!empty($form)) {
			if($_SERVER["REQUEST_METHOD"] == "POST")
				$data = $_POST;
			else
				$data = $_GET;
			
			/*Any values/errors stored in the session for this form are cleared.*/
			self::clearValues($id);
			self::clearErrors($id);

			/*Each element's value is saved in the session and checked against any validation rules applied
			to the element.*/
			if(!empty($form->_elements)) {
				foreach($form->_elements as $element) {
					$name = $element->getAttribute("name");
					if(substr($name, -2) == "[]")
						$name = substr($name, 0, -2);

					/*The File element must be handled differently b/c it uses the $_FILES superglobal and
					not $_GET or $_POST.*/
					if($element instanceof Element_File)
						$data[$name] = $_FILES[$name]["name"];

					if(isset($data[$name])) {
						$value = $data[$name];
						if(is_array($value)) {
							$valueSize = sizeof($value);
							for($v = 0; $v < $valueSize; ++$v)
								$value[$v] = stripslashes($value[$v]);
						}
						else
							$value = stripslashes($value);

						if($element->prefillAfterValidation())
							self::_setSessionValue($id, $name, $value);
					}		
					else
						$value = null;
					
					/*If a validation error is found, the error message is saved in the session along with
					the element's name.*/
					if(!$element->isValid($value)) {
						self::setError($id, $element->getErrors(), $name);
						$valid = false;
					}	
				}
			}

			/*If no validation errors were found, the form's session values are cleared.*/
			if($valid) {
				if($clearValues)
					self::clearValues($id);
				self::clearErrors($id);
			}		
		}
		else
			$valid = false;

		return $valid;
	}

	 protected function isAllowedUrl($url) {
        if(!empty($this->prevent)) {
            if(in_array("bootstrap", $this->prevent) && strpos($url, "/bootstrap/") !== false)
                return false;
            elseif(in_array("jquery", $this->prevent) && strpos($url, "/jquery.min.js") !== false)
                return false;
            elseif(in_array("jqueryui", $this->prevent) && strpos($url, "/jquery-ui/") !== false)
                return false;
        }

        return true;
    }

	/*This method restores the serialized form instance.*/
	protected static function recover($id) {
		if(!empty($_SESSION["pfbc"][$id]["form"]))
			return unserialize($_SESSION["pfbc"][$id]["form"]);
		else
			return "";
	}

	public function render($returnHTML = false) {
		if(!empty($this->labelToPlaceholder)) {
			foreach($this->_elements as $element) {
				$label = $element->getLabel();
				if(!empty($label)) {
					$element->setAttribute("placeholder", $label);
					$element->setLabel("");
				}	
			}	
		}

		$this->view->_setForm($this);
		$this->errorView->_setForm($this);

		/*When validation errors occur, the form's submitted values are saved in a session 
		array, which allows them to be pre-populated when the user is redirected to the form.*/
		$values = self::getSessionValues($this->_attributes["id"]);
		if(!empty($values))
			$this->setValues($values);
		$this->applyValues();

		if($returnHTML)
			ob_start();

		//For usability, the prevent array is treated case insensitively.
		$this->prevent = array_map("strtolower", $this->prevent);

		$this->renderCSS();
		$this->view->render();
		$this->renderJS();

		/*The form's instance is serialized and saved in a session variable for use during validation.*/
		$this->save();

		if($returnHTML) {
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		}
	}

	/*When ajax is used to submit the form's data, validation errors need to be manually sent back to the 
	form using json.*/
	public static function renderAjaxErrorResponse($id = "pfbc") {
		$form = self::recover($id);
		if(!empty($form))
			$form->errorView->renderAjaxErrorResponse();
	}

	protected function renderCSS() {
		$this->renderCSSFiles();

		echo '<style type="text/css">';
		$this->view->renderCSS();
		$this->errorView->renderCSS();
		foreach($this->_elements as $element)
			$element->renderCSS();
		echo '</style>';
	}

	protected function renderCSSFiles() {
		$urls = array(
			$this->resourcesPath . "/bootstrap/css/bootstrap.min.css",
			$this->resourcesPath . "/bootstrap/css/bootstrap-responsive.min.css"
		);

		foreach($this->_elements as $element) {
			$elementUrls = $element->getCSSFiles();
			if(is_array($elementUrls))
				$urls = array_merge($urls, $elementUrls);
		}	

		/*This section prevents duplicate css files from being loaded.*/ 
		if(!empty($urls)) {	
			$urls = array_values(array_unique($urls));
			foreach($urls as $url) {
				if($this->isAllowedUrl($url))
					echo '<link type="text/css" rel="stylesheet" href="', $url, '"/>';
			}		
		}	
	}

	protected function renderJS() {
		$this->renderJSFiles();	

		echo '<script type="text/javascript">';
		$this->view->renderJS();
		foreach($this->_elements as $element)
			$element->renderJS();
		
		$id = $this->_attributes["id"];

		echo 'jQuery(document).ready(function() {';

		/*When the form is submitted, disable all submit buttons to prevent duplicate submissions.*/
		echo <<<JS
		jQuery("#$id").bind("submit", function() { 
			//jQuery(this).find("input[type=submit]").attr("disabled", "disabled");
		});
JS;

		/*jQuery is used to set the focus of the form's initial element.*/
		if(!in_array("focus", $this->prevent))
			echo 'jQuery("#', $id, ' :input:visible:enabled:first").focus();';

		$this->view->jQueryDocumentReady();
		foreach($this->_elements as $element)
			$element->jQueryDocumentReady();
		
		/*For ajax, an anonymous onsubmit javascript function is bound to the form using jQuery.  jQuery's
		serialize function is used to grab each element's name/value pair.*/
		if(!empty($this->ajax)) {
			echo <<<JS
			jQuery("#$id").bind("submit", function() { 
JS;

			/*Clear any existing validation errors.*/
			$this->errorView->clear();

			echo <<<JS
				jQuery.ajax({ 
					url: "{$this->_attributes["action"]}", 
					type: "{$this->_attributes["method"]}", 
					data: jQuery("#$id").serialize(), 
					success: function(response) { 
						if(response != undefined && typeof response == "object" && response.errors) {
JS;

			$this->errorView->applyAjaxErrorResponse();

			echo <<<JS
							jQuery("html, body").animate({ scrollTop: jQuery("#$id").offset().top }, 500 );
						} 
						else {
JS;


			/*A callback function can be specified to handle any post submission events.*/
			if(!empty($this->ajaxCallback))
				echo $this->ajaxCallback, "(response);";

			/*After the form has finished submitting, re-enable all submit buttons to allow additional submissions.*/
			echo <<<JS
						} 
						jQuery("#$id").find("input[type=submit]").removeAttr("disabled"); 
					}	
				}); 
				return false; 
			});
JS;
		}

		echo '}); </script>';
	}

	protected function renderJSFiles() {
		$urls = array(
			$this->resourcesPath . "/jquery.min.js",
			$this->resourcesPath . "/bootstrap/js/bootstrap.min.js"
		);

		foreach($this->_elements as $element) {
			$elementUrls = $element->getJSFiles();
			if(is_array($elementUrls))
				$urls = array_merge($urls, $elementUrls);
		}		

		/*This section prevents duplicate js files from being loaded.*/ 
		if(!empty($urls)) {	
			$urls = array_values(array_unique($urls));
			foreach($urls as $url) {
				if($this->isAllowedUrl($url))
					echo '<script type="text/javascript" src="', $url, '"></script>';
			}		
		}	
	}

	/*The save method serialized the form's instance and saves it in the session.*/
	protected function save() {
		$_SESSION["pfbc"][$this->_attributes["id"]]["form"] = serialize($this);
	}

	/*Valldation errors are saved in the session after the form submission, and will be displayed to the user
	when redirected back to the form.*/
	public static function setError($id, $errors, $element = "") {
		if(!is_array($errors))
			$errors = array($errors);
		if(empty($_SESSION["pfbc"][$id]["errors"][$element]))
			$_SESSION["pfbc"][$id]["errors"][$element] = array();

		foreach($errors as $error)
			$_SESSION["pfbc"][$id]["errors"][$element][] = $error;
	}

	public static function _setSessionValue($id, $element, $value) {
		$_SESSION["pfbc"][$id]["values"][$element] = $value;
	}

	/*An associative array is used to pre-populate form elements.  The keys of this array correspond with
	the element names.*/
	public function setValues(array $values) {
        $this->_values = array_merge($this->_values, $values);
    }
}
