<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

    
class MPP_Admin_Settings_Section {
    /**
     *
     * @var string Unique section Id for the page 
     */
    private $id;
    /**
     *
     * @var string section title 
     */
    private $title;
    /**
     *
     * @var string Section description  
     */
    private $desc ='';
    
    /**
     *
     * @var array  of fields
     */
    private $fields = array();//array
   
    
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
     * Adds a field to this section
     * 
     * We can use it to chain and add multiple fields in a go
     * 
     * @return MPP_Admin_Settings_Section
     */
    public function add_field( $field ) {
       
        //check if a field class with name MPP_Admin_Settings_Field_$type exists, use it 
        $type = 'text';
        
        if( isset( $field['type'] ) )
            $type = $field['type'];//text/radio etc
        
        $class_name = 'MPP_Admin_Settings_Field';
        //a field specific class can be declared as MPP_Admin_Settings_Field_typeName
        $field_class_name = $class_name . '_' . ucfirst( $type );
       
        if( class_exists( $field_class_name ) && is_subclass_of( $field_class_name, $class_name ) ) {
                $class_name = $field_class_name; 
		}
       
		$field_object = new $class_name( $field );
		
        $id = $field_object->get_id();
		
       //let us store the field  
       $this->fields[ $id ] = $field_object;
        
        return $this;
    }
    /**
     * Adds Multiple Setting fields at one
     * 
     * @see MPP_Admin_Settings_Section::add_field()
     * @return MPP_Admin_Settings_Section
     * 
     */
    public function add_fields( $fields ) {
        
        foreach( $fields as $field ) {
            $this->add_field( $field );
		}
        
        return $this;
    }

    /**
     * Override fields
     * 
     * @param type $fields
     * @return MPP_Admin_Settings_Section
     */
    public function set_fields( $fields ) {
        //if set fields is called, first reset fiels
        $this->reset_fields();
        
        $this->add_fields( $fields );
        
        return $this;
    }
	
    /**
     * Resets fields
     */
    public function reset_fields() {
        unset( $this->fields );
		
        $this->fields = array();
		
        return $this;
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
     * Return a multidimensional array of the setting fields Objects in this section
     * @return MPP_Admin_Settings_Field
     */
    public function get_fields() {
		
        return $this->fields;
    }
    /**
     * 
     * @param type $name
     * @return MPP_Admin_Settings_Field
     */
    public function get_field( $name ) {
		
        return $this->fields[$name];
    }
	
  
}
