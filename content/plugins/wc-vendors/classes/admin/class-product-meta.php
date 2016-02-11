<?php

/**
 * Product meta configurations
 *
 * @package ProductVendor
 */


class WCV_Product_Meta
{


	/**
	 * Constructor
	 */
	function __construct()
	{
		if ( !current_user_can( 'manage_woocommerce' ) ) return;

		// Allow products to have authors
		add_post_type_support( 'product', 'author' );

		add_action( 'add_meta_boxes', array( $this, 'change_author_meta_box_title' ) );
		add_action( 'wp_dropdown_users', array( $this, 'author_vendor_roles' ), 0, 1 );
		if ( apply_filters( 'wcv_product_commission_tab', true ) ) { 
			add_action( 'woocommerce_product_write_panel_tabs', array( $this, 'add_tab' ) );
			add_action( 'woocommerce_product_write_panels', array( $this, 'add_panel' ) );
			add_action( 'woocommerce_process_product_meta', array( $this, 'save_panel' ) );
		}

		add_action( 'woocommerce_product_quick_edit_end', array($this, 'display_vendor_dd_quick_edit') ); 
		add_action( 'woocommerce_product_quick_edit_save', array($this, 'save_vendor_quick_edit'), 2, 99 ); 
		add_action( 'manage_product_posts_custom_column', array($this, 'display_vendor_column'), 2, 99 ); 
		add_filter( 'manage_product_posts_columns', array($this, 'vendor_column_quickedit') ); 

	}


	/**
	 * Change the "Author" metabox to "Vendor"
	 */
	public function change_author_meta_box_title()
	{
		global $wp_meta_boxes;
		$wp_meta_boxes[ 'product' ][ 'normal' ][ 'core' ][ 'authordiv' ][ 'title' ] = __( 'Vendor', 'wcvendors' );;
	}


	/**
	 * Override the authors selectbox with +vendor roles
	 *
	 * @param html $output
	 *
	 * @return html
	 */
	public function author_vendor_roles( $output )
	{
		global $post;

		if ( empty( $post ) ) return $output;

		// Return if this isn't a WooCommerce product post type
		if ( $post->post_type != 'product' ) return $output;

		// Return if this isn't the vendor author override dropdown
		if ( !strpos( $output, 'post_author_override' ) ) return $output;

		$args = array(
			'selected' => $post->post_author,
			'id'       => 'post_author_override',
		);

		$output = $this->vendor_selectbox( $args );

		return $output;
	}


	/**
	 * Create a selectbox to display vendor & administrator roles
	 *
	 * @param array $args
	 *
	 * @return html
	 */
	public function vendor_selectbox( $args )
	{
		$default_args = array(
			'placeholder',
			'id',
			'class',
		);

		foreach ( $default_args as $key ) {
			if ( !is_array( $key ) && empty( $args[ $key ] ) ) $args[ $key ] = '';
			else if ( is_array( $key ) ) foreach ( $key as $val ) $args[ $key ][ $val ] = esc_attr( $args[ $key ][ $val ] );
		}
		extract( $args );

		$roles     = array( 'vendor', 'administrator' );
		$user_args = array( 'fields' => array( 'ID', 'user_login' ) );

		$output = "<select style='width:200px;' name='$id' id='$id' class='$class' data-placeholder='$placeholder'>\n";
		$output .= "\t<option value=''></option>\n";

		foreach ( $roles as $role ) {

			$new_args           = $user_args;
			$new_args[ 'role' ] = $role;
			$users              = get_users( $new_args );

			if ( empty( $users ) ) continue;
			foreach ( (array) $users as $user ) {
				$select = selected( $user->ID, $selected, false );
				$output .= "\t<option value='$user->ID' $select>$user->user_login</option>\n";
			}

		}
		$output .= "</select>";

		// Convert this selectbox with select2
		$output .= '
		<script type="text/javascript">jQuery(function() { jQuery("#' . $id . '").select2().focus(); } );</script>';		
		
		return $output;
	}


	/**
	 * Save commission rate of a product
	 *
	 * @param int $post_id
	 */
	public function save_panel( $post_id )
	{
		if ( isset( $_POST[ 'pv_commission_rate' ] ) ) {
			update_post_meta( $post_id, 'pv_commission_rate', is_numeric( $_POST[ 'pv_commission_rate' ] ) ? (float) $_POST[ 'pv_commission_rate' ] : false );
		}

	}


	/**
	 * Add the Commission tab to a product
	 */
	public function add_tab()
	{
		?>
		<li class="commission_tab">
			<a href="#commission"><?php _e( 'Commission', 'wcvendors' ) ?></a>
		</li> <?php
	}


	/**
	 * Add the Commission panel to a product
	 */
	public function add_panel()
	{
		global $post; ?>

		<div id="commission" class="panel woocommerce_options_panel">
			<fieldset>

				<p class='form-field commission_rate_field'>
					<label for='pv_commission_rate'><?php _e( 'Commission', 'wcvendors' ); ?> (%)</label>
					<input type='number' id='pv_commission_rate'
						   name='pv_commission_rate'
						   class='short'
						   max="100"
						   min="0"
						   step='any'
						   placeholder='<?php _e( 'Leave blank for default', 'wcvendors' ); ?>'
						   value="<?php echo get_post_meta( $post->ID, 'pv_commission_rate', true ); ?>"/>
				</p>

			</fieldset>
		</div> <?php

	}

	/* 
	*		Rename the Authors column to Vendor on products page
	*/ 
	public function vendor_column_quickedit($posts_columns) { 
		$posts_columns['author'] = __( 'Vendor', 'wcvendors' );

		return $posts_columns; 
	}

	/* 
	*	Display the vendor drop down on the quick edit screen
	*/	
	public function display_vendor_dd_quick_edit() { 

		global $post; 
		$selected = $post->post_author; 

		$roles     = array( 'vendor', 'administrator' );
		$user_args = array( 'fields' => array( 'ID', 'display_name' ) );

		$output = "<select style='width:200px;' name='post_author-new' class='select'>\n";

		foreach ( $roles as $role ) {

			$new_args           = $user_args;
			$new_args[ 'role' ] = $role;
			$users              = get_users( $new_args );

			if ( empty( $users ) ) continue;
			foreach ( (array) $users as $user ) {
				$select = selected( $user->ID, $selected, false );
				$output .= "\t<option value='$user->ID' $select>$user->display_name</option>\n";
			}

		}
		$output .= "</select>";

		 ?>
		<br class="clear" />
        <label class="inline-edit-author-new">
            <span class="title"><?php _e('Vendor', 'wcvendors' ); ?></span>
            <?php echo $output; ?>
        </label>
    <?php
	} 


	/* 
	*	Save the vendor on the quick edit screen 
	*/
	public function save_vendor_quick_edit( $product ) { 

		if ( $product->is_type('simple') || $product->is_type('external') ) {
		    if ( isset( $_REQUEST['_vendor'] ) ) {
		        $vendor = wc_clean($_REQUEST['_vendor']);
		        $product->post->post_author = $vendor; 
		    }
		}
		return $product; 
	}

	/* 
	*	Display hidden column data for js 
	*/
	public function display_vendor_column( $column, $post_id ){ 

		$vendor = get_post_field( 'post_author', $post_id );

		switch ( $column ) {
	    case 'name' :

	        ?>
	        <div class="hidden vendor" id="vendor_<?php echo $post_id; ?>">
	            <div id="post_author"><?php echo  $vendor; ?></div>
	        </div>
	        <?php

	        break;

	    default :
	        break;
		}

	}
}
