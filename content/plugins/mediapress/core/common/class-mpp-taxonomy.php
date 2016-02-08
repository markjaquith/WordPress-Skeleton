<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

/**
 * Base class used for storing basic details
 * name may change in future
 */
class MPP_Taxonomy {
    public $id;
    public $label;
    public $singular_name = '';
	public $plural_name = '';
	public $tt_id;//term_taxonomy_id
	public $slug;
    
    
    public function __construct( $args, $taxonomy ) {
		
		$term = null;

		if ( isset( $args['key'] ) ) {	
			$term = _mpp_get_term( $args['key'], $taxonomy );
		} elseif ( isset( $args['id'] ) ) {
			$term = _mpp_get_term( $args['id'], $taxonomy );
		}

		if ( $term && ! is_wp_error( $term ) ) {

			$this->id		= $term->term_id;
			$this->tt_id	= $term->term_taxonomy_id;

			//to make it truely multilingual, do not use the term name instead use the registered label if available
			if ( isset( $args['label'] ) ) {
				$this->label	= $args['label'];
			} else { 
				$this->label	= $term->name;
			}

			$this->slug	= str_replace( '_', '', $term->slug );//remove _ from the slug name to make it private/public etc

			if ( isset( $args['labels']['singular_name'] ) ) {
				$this->singular_name = $args['labels']['singular_name'];
			} else {
				$this->singular_name = $this->label;
			}

			if ( isset( $args['labels']['plural_name'] ) ) {
				$this->plural_name = $args['labels']['plural_name'];
			} else {
				$this->plural_name = $this->label;
			}


		}
	}
    /**
     * 
     * @return string the label for this taxonomy
     */
    public function get_label() {
        return $this->label;
    } 
    /**
     * 
     * @return int the actual internal term id
     */
    public function get_id() {
        return $this->id;
    }
	/**
	 * Get term_taxonomy_id for the current tax term
	 * 
	 * @return int Term_taxonomy ID
	 */
	public function get_tt_id() {
		
		return $this->tt_id;
	}
    /**
     * 
     * @return string, slug (It has underscores appended)
     */
    public function get_slug(){

        return $this->slug;
    }
}
/**
 * Gallery|Media Status class
 * @property string $activity_privacy Status mapped to activity privacy(supports BuddyPress Activity privacy plugin )
 * @property callable $callback Callback function to check for the current user's access to gallery/media with this privacy
 */
class MPP_Status extends MPP_Taxonomy{
    
    public function __construct( $args ) {
		
        parent::__construct( $args, mpp_get_status_taxname() );
		
    }
}
/**
 * Gallery|Media Type object
 */
class MPP_Type extends MPP_Taxonomy{
    /**
     *
     * @var mixed file extentions for the media type array('jpg', 'gif', 'png'); 
     */
    private $extensions;
	
	/**
	 * These are the initial registered extension for this type
	 * 
	 * @var array of extensions e.g ( 'gif', 'png') 
	 */
	private $registered_extensions = array(); 
	
    public function __construct( $args ) {
		
        parent::__construct( $args, mpp_get_type_taxname() );
		
		$this->registered_extensions = $args['extensions'];
        
		$this->extensions = mpp_get_media_extensions( $this->get_slug() );
		//$this->extensions = mpp_string_to_array( $this->extensions );
    }
    /**
	 * An array of allowed extensions( as updated by site admin in the MediaPress settings )
	 * @return array of file extensions e.g ( 'gif', 'png', 'jpeg' ) 
	 */
    public function get_allowed_extensions() {
		
        return $this->extensions;
    }
	
    /**
	 * An array of registered extensions( as registered by developer while using mpp_register_type,
	 * It may be different from the active extensions allowed )
	 * @return array of file extensions e.g ( 'gif', 'png', 'jpeg' ) 
	 */
	public function get_registered_extensions() {
		
		return $this->registered_extensions;
	}
    
}

/**
 * Gallery|Media Component 
 */
class MPP_Component extends MPP_Taxonomy {
    /**
	 *
	 * @var MPP_Features 
	 */
	private $features;
	
    public function __construct( $args ) {
		
        parent::__construct( $args, mpp_get_component_taxname() );
		
		$this->features = new MPP_Features();
    }
	
	/**
	 * Check if component supports this feature
	 * @param string $feature feature name
	 * @return boolean
	 */
	public function supports( $feature, $value = false ) {
		
		return $this->features->supports( $feature, $value );
	}
	
	public function add_support( $feature, $value, $single = false ) {
		
		return $this->features->register( $feature, $value, $single );
	}
	public function remove_support( $feature, $value = null ) {
		
		return $this->features->deregister( $feature, $value );
	}
	/**
	 * Array
	 * @param type $feature
	 * @return type
	 */
	public function get_supported_values( $feature ){
		
		return $this->supports->get( $feature );
	}
}
