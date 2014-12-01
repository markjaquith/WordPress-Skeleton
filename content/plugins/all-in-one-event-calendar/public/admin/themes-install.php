<?php if( ! validate_current_theme() ) : ?>
	<div id="message1" class="updated">
		<p>
			<?php _e( 'The active theme is broken.  Reverting to the default theme.', AI1EC_PLUGIN_NAME ); ?>
		</p>
	</div>
<?php elseif( isset( $_GET['activated'] ) ) :
		if( isset( $wp_registered_sidebars ) &&
         count( (array) $wp_registered_sidebars ) &&
         current_user_can('edit_theme_options') ) { ?>
			<div id="message2" class="updated">
				<p>
					<?php printf( __( 'New theme activated. This theme supports widgets, ' .
                            'please visit the <a href="%s">widgets settings</a>' .
                            ' screen to configure them.'),
                        admin_url( 'widgets.php' ) ); ?>
				</p>
			</div>
	<?php } else { ?>
			<div id="message2" class="updated">
				<p>
					<?php printf( __( 'New theme activated. <a href="%s">Visit site</a>' ), home_url( '/' ) ); ?>
				</p>
			</div>
	<?php } elseif( isset( $_GET['deleted'] ) ) : ?>
			<div id="message3" class="updated">
				<p>
					<?php _e( 'Theme deleted.', AI1EC_PLUGIN_NAME ) ?>
				</p>
			</div>
	<?php endif; ?>

	<div class="wrap">
		<?php
		screen_icon();
		if( ! is_multisite() && current_user_can( 'install_themes' ) ) : ?>
			<h2 class="nav-tab-wrapper">
				<a href="<?php echo admin_url( AI1EC_THEME_SELECTION_BASE_URL ) ?>" class="nav-tab nav-tab-active">
					<?php echo esc_html( __( 'Manage Themes' ) ); ?>
				</a>
				<a href="<?php echo admin_url( AI1EC_THEME_SELECTION_BASE_URL . '-install' ) ?>" class="nav-tab">
					<?php echo esc_html_x( 'Install Themes', 'theme' ); ?>
				</a>
			</h2>
		<?php else : ?>
			<h2><?php echo esc_html( __( 'Manage Themes' ) ); ?></h2>
		<?php endif; ?>

		<?php echo $html ?>
