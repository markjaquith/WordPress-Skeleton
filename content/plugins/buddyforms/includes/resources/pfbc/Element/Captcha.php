<?php
class Element_Captcha extends Element {
	protected $privateKey = "6LcazwoAAAAAAD-auqUl-4txAK3Ky5jc5N3OXN0_";
	protected $publicKey = "6LcazwoAAAAAADamFkwqj5KN1Gla7l4fpMMbdZfi";

	public function __construct($label = "", array $properties = null) {
		parent::__construct($label, "recaptcha_response_field", $properties);
	}	

	public function render() {
		$this->validation[] = new Validation_Captcha($this->privateKey);
		require_once(dirname(__FILE__) . "/../Resources/recaptchalib.php");
		echo recaptcha_get_html($this->publicKey);
	}
}
