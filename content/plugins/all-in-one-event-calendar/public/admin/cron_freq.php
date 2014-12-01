<select name="cron_freq">
	<option value="hourly" <?php echo $cron_freq == 'hourly' ? 'selected' : ''; ?>>
		<?php _e( 'Hourly', AI1EC_PLUGIN_NAME ) ?>
	</option>
	<option value="twicedaily" <?php echo $cron_freq == 'twicedaily' ? 'selected' : '' ?>>
		<?php _e( 'Twice Daily', AI1EC_PLUGIN_NAME ) ?>
	</option>
	<option value="daily" <?php echo $cron_freq == 'daily' ? 'selected' : '' ?>>
		<?php _e( 'Daily', AI1EC_PLUGIN_NAME ) ?>
	</option>
</select>
