<div class="ai1ec-modal-content">

<div class="ai1ec-modal-header">
	<h4 class="ai1ec-modal-title">
		<?php _e( 'Select recurrence pattern:', AI1EC_PLUGIN_NAME ); ?>
	</h4>
</div>

<div class="ai1ec-modal-body ai1ec-form-horizontal">
	<div class="ai1ec-alert ai1ec-alert-danger ai1ec-hide"></div>

	<big>
		<ul class="ai1ec-nav ai1ec-nav-pills ai1ec-row">
			<li class="ai1ec-active ai1ec-col-xs-3 ai1ec-text-center">
				<a href="#ai1ec_daily_content" data-toggle="ai1ec-tab">
					<?php _e( 'Daily', AI1EC_PLUGIN_NAME ) ;?>
				</a>
			</li>
			<li class="ai1ec-col-xs-3 ai1ec-text-center">
				<a href="#ai1ec_weekly_content" data-toggle="ai1ec-tab">
					<?php _e( 'Weekly', AI1EC_PLUGIN_NAME ) ;?>
				</a>
			</li>
			<li class="ai1ec-col-xs-3 ai1ec-text-center">
				<a href="#ai1ec_monthly_content" data-toggle="ai1ec-tab">
					<?php _e( 'Monthly', AI1EC_PLUGIN_NAME ) ;?>
				</a>
			</li>
			<li class="ai1ec-col-xs-3 ai1ec-text-center">
				<a href="#ai1ec_yearly_content" data-toggle="ai1ec-tab">
					<?php _e( 'Yearly', AI1EC_PLUGIN_NAME ) ;?>
				</a>
			</li>
		</ul>

		<p></p>
	</big>

	<div class="ai1ec-tab-content">
		<div id="ai1ec_daily_content" data-freq="daily"
			class="ai1ec-tab-pane ai1ec-active">
			<?php echo $row_daily; ?>
		</div>
		<div id="ai1ec_weekly_content" data-freq="weekly"
			class="ai1ec-tab-pane">
			<?php echo $row_weekly; ?>
		</div>
		<div id="ai1ec_monthly_content" data-freq="monthly"
			class="ai1ec-tab-pane">
			<?php echo $row_monthly; ?>
		</div>
		<div id="ai1ec_yearly_content" data-freq="yearly"
			class="ai1ec-tab-pane">
			<?php echo $row_yearly; ?>
		</div>
	</div>
</div>

<div class="ai1ec-modal-footer">
	<div class="ai1ec-form-horizontal ai1ec-text-left">
		<div class="ai1ec-form-group">
			<label for="ai1ec_end" class="ai1ec-control-label ai1ec-col-sm-3">
				<?php _e( 'End', AI1EC_PLUGIN_NAME ); ?>:
			</label>
			<div class="ai1ec-col-sm-9">
				<?php echo $end; ?>
			</div>
		</div>

		<div id="ai1ec_count_holder" class="ai1ec-form-group ai1ec-collapse"
			data-toggle="false">
			<label for="ai1ec_count" class="ai1ec-control-label ai1ec-col-sm-3">
				<?php _e( 'Ending after', AI1EC_PLUGIN_NAME ); ?>:
			</label>
			<div class="ai1ec-col-sm-9">
				<?php echo $count; ?>
			</div>
		</div>

		<div id="ai1ec_until_holder" class="ai1ec-form-group ai1ec-collapse"
			data-toggle="false">
			<label for="ai1ec_until-date-input"
				class="ai1ec-control-label ai1ec-col-sm-3">
				<?php _e( 'On date', AI1EC_PLUGIN_NAME ); ?>:
			</label>
			<div class="ai1ec-col-sm-9">
				<input type="text" class="ai1ec-date-input" id="ai1ec_until-date-input">
				<input type="hidden" name="ai1ec_until_time" id="ai1ec_until-time"
					value="<?php echo ! is_null( $until ) && $until > 0 ? $until : ''; ?>">
			</div>
		</div>
	</div>

	<input type="hidden" id="ai1ec_is_box_repeat" value="<?php echo $repeat; ?>">

	<button type="button" id="ai1ec_repeat_apply"
		class="ai1ec-btn ai1ec-btn-primary ai1ec-btn-lg"
		data-loading-text="<?php echo esc_attr(
			'<i class="ai1ec-fa ai1ec-fa-spinner ai1ec-fa-fw ai1ec-fa-spin"></i> ' .
			__( 'Please wait&#8230;', AI1EC_PLUGIN_NAME ) ); ?>">
		<i class="ai1ec-fa ai1ec-fa-check ai1ec-fa-fw"></i>
		<?php _e( 'Apply', AI1EC_PLUGIN_NAME ); ?>
	</button>
	<a id="ai1ec_repeat_cancel"
		class="ai1ec-btn ai1ec-btn-default ai1ec-text-danger ai1ec-btn-lg">
		<i class="ai1ec-fa ai1ec-fa-undo ai1ec-fa-fw"></i
		><?php _e( 'Cancel', AI1EC_PLUGIN_NAME ); ?>
	</a>
</div>

</div>
