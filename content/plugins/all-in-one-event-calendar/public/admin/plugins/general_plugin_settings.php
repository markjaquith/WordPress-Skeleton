<h2><?php echo $plugin_name; ?></h2>
<?php if ( isset( $plugin_info ) ) : ?>
	<div class="ai1ec-clearfix"><?php echo $plugin_info; ?></div>
<?php endif; ?>
<?php foreach ( $plugin_settings as $setting) : ?>
	<?php
	$description = esc_html( $setting['setting-description'] );
	$value = esc_attr( $setting['setting-value'] );
	$id = esc_attr( $setting['setting-id'] );
	?>
	<div class="ai1ec-form-group">
		<label class="ai1ec-control-label" for="<?php echo $id; ?>">
			<?php echo $description; ?>
		</label>
		<input name="<?php echo $id; ?>" id="<?php echo $id; ?>" type="text"
			class="ai1ec-form-control" value="<?php echo $value; ?>">
	</div>
<?php endforeach; ?>
