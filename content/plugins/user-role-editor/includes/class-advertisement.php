<?php

/*
 * User Role Editor plugin: advertisement showing class
 * Author: Vladimir Garagulya
 * email: vladimir@shinephp.com
 * site: http://shinephp.com
 * 
 */

class ure_Advertisement {
	
	private $slots = array(0=>'', 1=>'', 2=>'');
				
	function __construct() {
		
		$used = array(-1);
		$index = $this->rand_unique( $used );		
		$this->slots[$index] = $this->admin_menu_editor();
		$used[] = $index;
    
		$index = $this->rand_unique( $used );
		$this->slots[$index] = $this->wp_esignature();
  $used[] = $index;
				
	}
	// end of __construct
	
	
	/**
	 * Returns random number not included into input array
	 * 
	 * @param array $used - array of numbers used already
	 * 
	 * @return int
	 */
	private function rand_unique( $used = array(-1) ) {
		$index = rand(0, 2);
		while (in_array($index, $used)) {
			$index = rand(0, 2);
		}
		
		return $index;
	}
	// return rand_unique()
	
	
	// content of Admin Menu Editor advertisement slot
	private function admin_menu_editor() {
	
		$output = '
			<div style="text-align: center;">
				<a href="http://w-shadow.com/admin-menu-editor-pro/?utm_source=UserRoleEditor&utm_medium=banner&utm_campaign=Plugins " target="_new" >
					<img src="'. URE_PLUGIN_URL . 'images/admin-menu-editor-pro.jpg' .'" alt="Admin Menu Editor Pro" title="Move, rename, hide, add admin menu items, restrict access"/>
				</a>
			</div>  
			';
		
		return $output;
	}
	// end of admin_menu_editor()
	 
 
 // content of WP eSignature advertisement slot
	private function wp_esignature() {
	
		$output = '
			<div style="text-align: center;">
			<a title="WP E-Signature" href="https://www.approveme.me/?utm_source=role-editor&utm_medium=250x250&utm_term=get%20contracts%20signed%205x%20faster%20with%20wordpress&utm_content=v1&utm_campaign=role-editor" target="_new" >
				<img width="250" height="250" alt="WP E-Signature" src="'. URE_PLUGIN_URL .'images/wp-esignature.png">
			</a>                        
		</div>  
			';

		return $output;
	}
	// end of manage_wp()
			
	
	/**
	 * Output all existed ads slots
	 */
	public function display() {
	
		foreach ($this->slots as $slot) {
			echo $slot."\n";
		}
		
	}
	// end of display()
	
}
// end of ure_Advertisement