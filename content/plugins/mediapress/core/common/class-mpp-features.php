<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

/**
 * MPP_Features class allows us to add features/test for features easily
 * We can associate features object to anything
 * 
 */
class MPP_Features {
	
	/**
	 *
	 * @var array of features 
	 */
	private $supported = array();
	/**
	 * Add support for specific feature
	 * If $single is set to true, adding a feature multiple times will just replace it
	 * If $single is false, adding a feature multiple times will append the new values to the already existing one
	 * 
	 * @param type $feature
	 * @param type $value
	 * @param type $single
	 * 
	 * @return MPP_Features
	 */
	public function register( $feature, $value, $single = false ) {
		
//		if( $single ) {
//			$this->supported[$feature] = $value;
//			
//		}else {
		
			$this->supported[ $feature ][] = $value;
		
	//	}
		
	//	return $this;
	}
	
	public function deregister( $feature, $value = null ) {
		
		//if value is not given, remove support for this feature
		if ( ! $value ) {
			unset( $this->supported[ $feature ] );
		} else {
			//if value is given, just remove that value
			$vals = $this->supported[ $feature ];
		
			for ( $i = 0; $i < count( $vals ); $i++ ) {
				
				if ( $vals[ $i ] == $value ) {
					
					unset( $vals[ $i ] );
					break;
				}
			}
			
			$vals = array_filter( $vals );
			
			$this->supported[ $feature ] = $vals;
			
		}
		
	}
	/**
	 * 
	 * @param string $feature name
	 * @return mixed|boolean
	 */
	public function get( $feature ) {
		
		if ( isset( $this->supported[ $feature ] ) ) {
			return $this->supported[ $feature ];
		}
		
		return false;
	}
	
	/**
	 * Check if the feature supports given value
	 * 
	 * @param type $feature
	 * 
	 * @return boolean
	 * 
	 */
	public function supports( $feature, $value = null ) {
		
		if ( ! isset( $this->supported[ $feature ] ) || empty( $this->supported[ $feature ] ) ) {
			return false;
		}	
		
		if ( ! $value ) {
			return true;
		}
		
		$vals = $this->supported[ $feature ];
		
		if ( in_array( $value, $vals ) ) {
			return true;
		}
		
		return false;
	}
	
	
}

//mpp_add_component_support( $component, $feature, $value );
//mpp_add_component_support( $component, $feature, $value );