<?php
$item_meta = new WC_Order_Item_Meta( $item );
$item_meta = $item_meta->display( false, true );

if ($count > 1) : ?>

<tr>

	<?php endif; ?>

	<?php if (!empty( $item_meta ) && $item_meta != '<dl class="variation"></dl>') : ?>

	<td colspan="5">
		<?php echo $item_meta; ?>
	</td>

<td colspan="2">

<?php else : ?>

	<td colspan="100%">

		<?php endif; ?>

		<?php printf( __( 'Quantity: %d', 'wcvendors' ), $item[ 'qty' ] ); ?>
	</td>

	<?php if ($count > 1) : ?>

</tr>

<?php endif; ?>