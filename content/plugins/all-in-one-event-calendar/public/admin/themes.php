<?php if( $activated ) : ?>
	<div id="message2" class="updated">
		<p>
			<?php printf( __( 'New theme activated. <a href="%s">Visit site</a>' ), home_url( '/' ) ); ?>
		</p>
	</div>
<?php elseif( $deleted ) : ?>
	<div id="message3" class="updated">
		<p>
			<?php _e( 'Theme deleted.', AI1EC_PLUGIN_NAME ) ?>
		</p>
	</div>
<?php endif; ?>

<div class="wrap">
	<?php screen_icon() ?>
	<h2><?php echo esc_html( $page_title ); ?></h2>
	<h3><?php _e( 'Current Calendar Theme', AI1EC_PLUGIN_NAME ); ?></h3>
	<div id="current-theme"<?php echo ( $ct->screenshot ) ? ' class="has-screenshot"' : '' ?>>
	<?php if ( $ct->screenshot ) : ?>
	<img src="<?php echo $ct->theme_root_uri . '/' . $ct->stylesheet . '/' . $ct->screenshot; ?>" alt="<?php esc_attr_e('Current theme preview', AI1EC_PLUGIN_NAME); ?>" />
	<?php endif; ?>
	<h4><?php
		/* translators: 1: theme title, 2: theme version, 3: theme author */
		printf(__('%1$s %2$s by %3$s', AI1EC_PLUGIN_NAME ), $ct->title, $ct->version, $ct->author) ; ?></h4>
	<p class="theme-description"><?php echo $ct->description; ?></p>
	<div class="theme-options">
		<?php if ( $ct->tags ) : ?>
		<p><?php _e( 'Tags:', AI1EC_PLUGIN_NAME ); ?> <?php echo join(', ', $ct->tags); ?></p>
		<?php endif; ?>
	</div>
	<?php theme_update_available($ct); ?>

	</div>

	<br class="clear" />
	<?php
	if (
		! current_user_can( 'switch_themes' ) &&
		! current_user_can( 'switch_ai1ec_themes' )
	) {
		echo '</div>';
		return false;
	}
	?>

	<h3><?php _e( 'Available Calendar Themes', AI1EC_PLUGIN_NAME ); ?></h3>

	<?php $wp_list_table->display(); ?>

	<br class="clear" />

	</div>
