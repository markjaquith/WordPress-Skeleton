<form method="post" name="add_comment" id="add-comment_<?php echo $order_id; ?>">

	<?php wp_nonce_field( 'add-comment' ); ?>

	<textarea name="comment_text" style="width:97%"></textarea>

	<input type="hidden" name="product_id" value="<?php echo $product_id ?>">
	<input type="hidden" name="order_id" value="<?php echo $order_id; ?>">

	<input class="btn btn-large btn-block" type="submit" name="submit_comment"
		   value="<?php _e( 'Add comment', 'wcvendors' ); ?>">

</form>