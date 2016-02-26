<div class="wrap"> 
<h2>Shop Settings</h2>
<table class="form-table">

<form method="post">
	<?php do_action( 'wcvendors_settings_before_paypal' );

	if ( $paypal_address !== 'false' ) { ?>

	<tr>
	<th><?php _e( 'PayPal Address', 'wcvendors' ); ?></th>
	<td><input type="email" name="pv_paypal" id="pv_paypal" placeholder="some@email.com"
			   value="<?php echo get_user_meta( $user_id, 'pv_paypal', true ); ?>"/>
		<p class="description">
			<?php _e( 'Your PayPal address is used to send you your commission.', 'wcvendors' ); ?><br/>
		</p>
	</td>
	</tr>
	<?php } ?>
	<?php do_action( 'wcvendors_settings_after_paypal' ); ?>
	<tr>
	<th><?php _e( 'Shop Name', 'wcvendors' ); ?></th>
	<td><input type="text" name="pv_shop_name" id="pv_shop_name" placeholder="Your shop name" value="<?php echo get_user_meta( $user_id, 'pv_shop_name', true ); ?>"/>
		<p class="description"><?php _e( 'Your shop name is public and must be unique.', 'wcvendors' ); ?></p>
	</td>
	</tr>
	<?php do_action( 'wcvendors_settings_after_shop_name' ); ?>

	<tr>
	<th><?php echo apply_filters( 'wcvendors_seller_info_label', __( 'Seller info', 'wcvendors' ) ); ?></th>
	<td><?php

		if ( $global_html || $has_html ) {
			$old_post          = $GLOBALS[ 'post' ];
			$GLOBALS[ 'post' ] = 0;
			wp_editor( $seller_info, 'pv_seller_info' );
			$GLOBALS[ 'post' ] = $old_post;
		} else {
			?><textarea class="large-text" rows="10" id="pv_seller_info_unhtml" style="width:95%"
						name="pv_seller_info"><?php echo $seller_info; ?></textarea><?php
		}
		?>
		<p class="description"><?php _e( 'This is displayed on each of your products.', 'wcvendors' ); ?></p>
	</td>
	</tr>
	<?php do_action( 'wcvendors_settings_after_seller_info' ); ?>
	<?php if ( $shop_description !== 'false' ) { ?>
	<tr>
	<th><?php _e( 'Shop Description', 'wcvendors' ); ?></th>
	<td><?php

		if ( $global_html || $has_html ) {
			$old_post          = $GLOBALS[ 'post' ];
			$GLOBALS[ 'post' ] = 0;
			wp_editor( $description, 'pv_shop_description' );
			$GLOBALS[ 'post' ] = $old_post;
		} else {
			?><textarea class="large-text" rows="10" id="pv_shop_description_unhtml" style="width:95%" name="pv_shop_description"><?php echo $description; ?></textarea><?php
		}
		?>
		<p class="description"><?php printf( __( 'This is displayed on your <a href="%s">shop page</a>.', 'wcvendors' ), $shop_page ); ?></p>
	</td>
	</tr>

	<?php do_action( 'wcvendors_settings_after_shop_description' ); ?>
	<?php } ?>
	<?php wp_nonce_field( 'save-shop-settings-admin', 'wc-vendors-nonce' ); ?>
	<tr>
	<td colspa="2">
		<input type="submit" class="button button-primary" name="vendor_application_submit" value="<?php _e( 'Save Shop Settings', 'wcvendors' ); ?>"/>
	</td>
	</tr>
</form>
</table>
</div>

