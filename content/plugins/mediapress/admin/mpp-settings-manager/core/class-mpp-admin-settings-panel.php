<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
/**
 * @since 1.0.0
 * Panel allows us to kepe multiple section inside the tabbed page.
 * each tab is a panel
 * 
 */
    
class MPP_Admin_Settings_Panel {
    /**
     *
     * @var string Unique section Id for the page 
     */
    private $id;
    /**
     *
     * @var string panel title 
     */
    private $title;
    /**
     *
     * @var string panel description  
     */
    private $desc = '';
    
    /**
     *
     * @var array  of fields
     */
    private $sections = array();//array
   
    
   /**
    * 
    * @param string $id Section Id
    * @param string $title Section Title
    * @param string $desc Section description
    */
    public function __construct( $id, $title, $desc = '') {
        
        $this->id    = $id;
        $this->title = $title;
        $this->desc  = $desc;
        
    }
    /**
     * Add new Setting Section 
     * 
     * @param  string $id section id
     * @param  string $title section title
     * @param  string $desc Section description
     * @return return MPP_Admin_Settings_Section
     */
    public function add_section( $id, $title, $desc = false ) {
        
        $section_id = $id ;
        
        $this->sections[ $section_id ] = new MPP_Admin_Settings_Section( $id, $title, $desc );        
       
        return $this->sections[ $section_id ];
        
    }
     /**
      * 
      * @param type $sections
      * @return MPP_Admin_Settings_Page
      */
    public function add_sections( $sections ) {
       
        foreach ( $sections as $id => $title ) {
			
            $this->add_section ( $id, $title );
			
		}	

        return $this;
    }
    /**
     * 
     * @param string $id
     * @return MPP_Admin_Settings_Section
     */
    public function get_section( $id ) {
		
        return isset( $this->sections[$id] ) ? $this->sections[$id] : false ;
        
    }
    /**
     * 
     * @param string $id
     * @return MPP_Admin_Settings_Section[]
     */
    public function get_sections(  ) {
		return $this->sections;
        
    }
    /**
     * Setters
     */
    
    public function set_id( $id ) {
		
        $this->id = $id;
		
        return $this;
    }
    
    public function set_title( $title ) {
		
        $this->title = $title;
        return $this;
    }
	
    public function set_description( $desc ) {
		
        $this->desc = $desc;
        return $this;
    }
    
    
    /**
     * Retuns the Section ID
     * @return string Section ID
     */
    public function get_id() {
		
        return $this->id;
    }
    /**
     *  Returns Section title
     * @return string Section title
     */
    public function get_title() {
		
        return $this->title;
    }
    
    /**
     * Retursn Section Description
     * @return string section description
     */
    public function get_disc() {
		
        return $this->desc;
    }
    
	/**
	 * Is this panel empty?
	 * 
	 * A panel is considered empty if it is registered but have no sections added
	 * 
	 * @return boolean
	 */
	public function is_empty() {
		
		if( empty( $this->sections ) ) {
			return true;
		}
		return false;
	}
  
}
