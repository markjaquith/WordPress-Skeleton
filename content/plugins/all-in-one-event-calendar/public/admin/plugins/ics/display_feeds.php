<p>
<?php _e(
    'Configure which other calendars your own calendar subscribes to.
    You can add any calendar that provides an iCalendar (.ics) feed.
    Enter the feed URL(s) below and the events from those feeds will be
    imported periodically.',
    AI1EC_PLUGIN_NAME ); ?>
</p>
<div id="ics-alerts"></div>
<div class="ai1ec-form-horizontal">
	<div class="ai1ec-form-group">
		<div class="ai1ec-col-md-8">
			<label class="ai1ec-control-label ai1ec-pull-left" for="cron_freq">
			  <?php _e( 'Check for new events', AI1EC_PLUGIN_NAME ) ?>:
			</label>
			<div class="ai1ec-col-sm-6">
				<?php echo $cron_freq ?>
			</div>
		</div>
		<div class="ai1ec-col-md-4">
			<button type="submit" name="ai1ec_save_settings" id="ai1ec_save_settings"
				class="ai1ec-btn ai1ec-btn-primary ai1ec-pull-right">
				<i class="ai1ec-fa ai1ec-fa-save ai1ec-fa-fw"></i>
				<?php _e( 'Save Settings', AI1EC_PLUGIN_NAME ); ?>
			</button>
		</div>
	</div>
</div>

<div id="ai1ec-feeds-after"
	class="ai1ec-feed-container ai1ec-well ai1ec-well-sm ai1ec-clearfix">
	<div class="ai1ec-form-group">
		<label for="ai1ec_feed_url">
			<?php _e( 'iCalendar/.ics Feed URL:', AI1EC_PLUGIN_NAME ) ?>
		</label>
		<input type="text" name="ai1ec_feed_url" id="ai1ec_feed_url"
			class="ai1ec-form-control">
	</div>
	<div class="ai1ec-row">
		<div class="ai1ec-col-sm-6">
			<?php $event_categories->render(); ?>
		</div>
		<div class="ai1ec-col-sm-6">
			<?php $event_tags->render(); ?>
		</div>
	</div>
	<div class="ai1ec-feed-comments-enabled">
		<label for="ai1ec_comments_enabled">
			<input type="checkbox" name="ai1ec_comments_enabled"
				id="ai1ec_comments_enabled" value="1">
			<?php _e( 'Allow comments on imported events', AI1EC_PLUGIN_NAME ); ?>
		</label>
	</div>
	<div class="ai1ec-feed-map-display-enabled">
		<label for="ai1ec_map_display_enabled">
			<input type="checkbox" name="ai1ec_map_display_enabled"
				id="ai1ec_map_display_enabled" value="1">
			<?php _e( 'Show map on imported events', AI1EC_PLUGIN_NAME ); ?>
		</label>
	</div>
	<div class="ai1ec-feed-add-tags-categories">
		<label for="ai1ec_add_tag_categories">
			<input type="checkbox" name="ai1ec_add_tag_categories"
				id="ai1ec_add_tag_categories" value="1">
			<?php _e( 'Import any tags/categories provided by feed, in addition those selected above', AI1EC_PLUGIN_NAME ); ?>
		</label>
	</div>
	<div class="ai1ec-feed-keep-old-events">
		<label for="ai1ec_keep_old_events">
			<input type="checkbox" name="ai1ec_keep_old_events"
				id="ai1ec_keep_old_events" value="1">
			<?php _e( 'Keep old events', AI1EC_PLUGIN_NAME ); ?>
		</label>
	</div>
	<div class="ai1ec-pull-right">
		<button type="button" id="ai1ec_add_new_ics"
			class="ai1ec-btn ai1ec-btn-primary ai1ec-btn-sm"
			data-loading-text="<?php echo esc_attr(
				'<i class="ai1ec-fa ai1ec-fa-spinner ai1ec-fa-spin ai1ec-fa-fw"></i> ' .
				__( 'Please wait&#8230;', AI1EC_PLUGIN_NAME ) ); ?>">
			<i class="ai1ec-fa ai1ec-fa-plus"></i>
			<?php _e( 'Add new subscription', AI1EC_PLUGIN_NAME ) ?>
		</button>
	</div>
</div>

<?php
echo $feed_rows;
echo $modal->render();
?>
