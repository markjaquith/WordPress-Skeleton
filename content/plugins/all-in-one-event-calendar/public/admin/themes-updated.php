<div class="wrap">

	<?php screen_icon(); ?>

	<h2><?php _e( 'Update Calendar Themes', AI1EC_PLUGIN_NAME ); ?></h2>

	<?php echo $msg; ?>

	<?php if ( $errors ): ?>
		<?php foreach ( $errors as $error ): ?>
			<?php echo $error; ?>
		<?php endforeach; ?>
	<?php endif; ?>

	<p><a class="button" href="<?php echo AI1EC_SETTINGS_BASE_URL; ?>"><?php _e( 'All-in-One Event Calendar Settings »', AI1EC_PLUGIN_NAME ); ?></a></p>
</div>
