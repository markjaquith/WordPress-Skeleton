<?php
/**
 * Class used for displaying venue table and handling interations
 */

/*
 *The WP_List_Table class isn't automatically available to plugins, so we need
 * to check if it's available and load it if necessary.
 */
if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class EO_Venue_List_Table extends WP_List_Table {
        
    /*
     * Constructor. Set some default configs.
     */
	function __construct(){
		global $status, $page;
		
		//Set parent defaults
		parent::__construct( array(
			'singular'  => 'venue',     //singular name of the listed records
			'plural'    => 'venues',    //plural name of the listed records
			'ajax'      => true        //does this table support ajax?
        	) );
	    }
    
    /*
     * For more detailed insight into how columns are handled, take a look at 
     * WP_List_Table::single_row_columns()
     * 
     * @param array $item A singular item (one full row's worth of data)
     * @param array $column_name The name/slug of the column to be processed
     * @return string Text or HTML to be placed inside the column <td>
     */
    function column_default($item, $column_name){
		$term_id = (int) $item->term_id;
		$address = eo_get_venue_address($term_id);
		
		 switch($column_name){
			case 'venue_slug':
				return esc_html($item->slug);
			case 'posts':
				return intval($item->count);
			default:
				$address_keys = array_keys($address);
				foreach( $address_keys as $key ){
					if( 'venue_'.$key == $column_name ){
						return esc_html($address[$key]);
					}
				}
				//TODO Hook for extra columns?
				return print_r($item,true); //Show the whole array for troubleshooting purposes
		}
    }
    
        
    /*
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td>
     */
    function column_name($item){
		$term_id = (int) $item->term_id;
		
		$delete_url = add_query_arg( 'action', 'delete', get_edit_term_link( $term_id, 'event-venue', 'event' ) );

        //Build row actions
        $actions = array(
            'edit'		=> sprintf( '<a href="%s">'.__('Edit').'</a>', get_edit_term_link( $term_id, 'event-venue', 'event' ) ),
            'delete'    => sprintf( '<a href="%s">'.__('Delete').'</a>', wp_nonce_url( $delete_url, 'eventorganiser_delete_venue_'.$item->slug ) ),
            'view'		=> sprintf( '<a href="%s">'.__('View').'</a>',  eo_get_venue_link($term_id) )
        );
        
        //Return the title contents
        return sprintf('<a href="%1$s" class="row-title">%2$s</a>%3$s',
            /*$1%s*/ get_edit_term_link( $term_id, 'event-venue', 'event' ),
            /*$2%s*/ esc_html( $item->name ),
            /*$3%s*/ $this->row_actions( $actions )
        );
    }
    
    /*
     * Checkbox column for Bulk Actions.
     * 
     * @see WP_List_Table::::single_row_columns()
     * @param array $item A singular item (one full row's worth of data)
     * @return string Text to be placed inside the column <td> (movie title only)
     */
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ 'event-venue',  
            /*$2%s*/ $item->slug       //The value of the checkbox should be the record's id
        );
    }
    
   
    /*
     * Set columns sortable
     * 
     * @return array An associative array containing all the columns that should be sortable: 'slugs'=>array('data_values',bool)
     */
    function get_sortable_columns() {
        $sortable_columns = array(
            'name'		     => array( 'name', true ),   //true means its sorted by default  
            'venue_address'  => array( 'address', false ),  
            'venue_city'	 => array( 'city', false ), 
            'venue_state'	 => array( 'state', false ),
            'venue_postcode' => array( 'postcode', false ),
            'venue_country'	 => array( 'country', false ),
            'venue_slug'	 => array( 'slug', false ),
            'posts'		     => array( 'count', false ),
        );
        return $sortable_columns;
    }


    /*
     * Set bulk actions
     * 
     * @return array An associative array containing all the bulk actions: 'slugs'=>'Visible Titles'
     */
    function get_bulk_actions() {
        $actions = array(
            'delete'    => __('Delete')
        );
        return $actions;
    }
    

     /*
     * Echos the row, after assigning it an ID based ont eh venue being shown. Assign appropriate class to alternate rows.
     */       
	function single_row( $item ) {
		static $row_class = '';
		$row_id = 'id="venue-'.$item->term_id.'"';
		$row_class = ( $row_class == '' ? ' class="alternate"' : '' );
		echo '<tr' .$row_class.' '.$row_id.'>';
		echo $this->single_row_columns( $item );
		echo '</tr>';
	}

    /*
     * Prepare venues for display
     * 
     * @uses $this->_column_headers
     * @uses $this->items
     * @uses $this->get_columns()
     * @uses $this->get_sortable_columns()
     * @uses $this->get_pagenum()
     * @uses $this->set_pagination_args()
     */
    function prepare_items() {

        //Retrieve page number for pagination
         $current_page = (int) $this->get_pagenum();

	//First, lets decide how many records per page to show
	$screen = get_current_screen();
	$per_page = $this->get_items_per_page( 'edit_event_venue_per_page' );

	//Get the columns, the hidden columns an sortable columns
	$columns = get_column_headers('event_page_venues');
	$hidden = get_hidden_columns('event_page_venues');
	$sortable = $this->get_sortable_columns();
	$this->_column_headers = array($columns, $hidden, $sortable);
	$taxonomy ='event-venue';

	$search = (!empty( $_REQUEST['s'] ) ? trim( stripslashes( $_REQUEST['s'] ) ) : '');
	$orderby =( !empty( $_REQUEST['orderby'] )  ? trim( stripslashes($_REQUEST['orderby'])) : '');
	$order =( !empty( $_REQUEST['order'] )  ? trim( stripslashes($_REQUEST['order'])) : '');

	//Display result
	$this->items = get_terms('event-venue',array(
			'hide_empty'=>false,
			'search'=>$search,
			'offset'=> ($current_page-1)*$per_page,
			'number'=>$per_page,
			 'orderby'=>$orderby,
			 'order'=>$order			
		)
	);

	$this->set_pagination_args( array(
		'total_items' => wp_count_terms('event-venue', compact( 'search', 'orderby' ) ),
		'per_page' => $per_page,
	) );     

    }
    
	function no_items() {
		$tax = get_taxonomy( 'event-venue' );
		echo esc_html( $tax->labels->not_found );
	}
    
}?>