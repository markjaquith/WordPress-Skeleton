<div class="pv_shop_name_container">
	<p><b><?php _e( 'Shop Name', 'wcvendors' ); ?></b><br/>
		<?php _e( 'Your shop name is public and must be unique.', 'wcvendors' ); ?><br/>

		<input type="text" name="pv_shop_name" id="pv_shop_name" placeholder="Your shop name"
			   value="<?php echo get_user_meta( $user_id, 'pv_shop_name', true ); ?>"/>
	</p>
</div>
