<?php
$message_class = isset( $message_type ) ? $message_type : 'updated';
?>
<div class="message <?php echo $message_class ?>">
	<?php if( ! empty( $label ) ) : ?>
	<h3><?php echo $label ?></h3>
	<?php endif;  ?>
	<?php echo $msg ?>
	<?php if( isset( $button ) ) : ?>
		<div><input type="button" class="button <?php echo $button->class ?>" value="<?php echo $button->value ?>" /></div>
		<p></p>
	<?php endif ?>
</div>
