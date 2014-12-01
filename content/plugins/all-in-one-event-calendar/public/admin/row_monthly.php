<div class="ai1ec-form-group">
	<label for="ai1ec_monthly_count" class="ai1ec-control-label ai1ec-col-sm-3">
		<?php _e( 'Every', AI1EC_PLUGIN_NAME ); ?>:
	</label>
	<div class="ai1ec-col-sm-9">
		<?php echo $count; ?>
	</div>
</div>

<div class="ai1ec-form-group">
	<div class="ai1ec-col-sm-offset-3 ai1ec-col-sm-9">
		<div class="radio">
			<label for="ai1ec_monthly_type_bymonthday">
				<input type="radio" name="ai1ec_monthly_type"
					id="ai1ec_monthly_type_bymonthday" value="bymonthday" checked>
				<?php _e( 'On day of the month', AI1EC_PLUGIN_NAME ); ?>
			</label>
		</div>
		<div class="radio">
			<label for="ai1ec_monthly_type_byday">
				<input type="radio" name="ai1ec_monthly_type"
					id="ai1ec_monthly_type_byday" value="byday">
				<?php _e( 'On day of the week', AI1EC_PLUGIN_NAME ); ?>
			</label>
		</div>
	</div>
</div>

<div class="ai1ec-form-group">
	<div id="ai1ec_repeat_monthly_bymonthday" class="ai1ec-collapse ai1ec-in">
		<div class="ai1ec-col-sm-offset-3 ai1ec-col-sm-9">
			<?php echo $month; ?>
		</div>
	</div>

	<div id="ai1ec_repeat_monthly_byday" class="ai1ec-collapse">
		<label for="ai1ec_monthly_type_byday"
			class="ai1ec-control-label ai1ec-col-sm-3">
			<?php _e( 'Every', AI1EC_PLUGIN_NAME ); ?>:
		</label>
		<div class="ai1ec-col-sm-9">
			<?php echo $day_nums; ?>
			<?php echo $week_days; ?>
		</div>
	</div>
</div>
