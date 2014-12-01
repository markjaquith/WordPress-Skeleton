<p>
	<label for="<?php echo $title['id'] ?>"><?php _e( 'Title:', AI1EC_PLUGIN_NAME ) ?></label>
	<input class="widefat" id="<?php echo $title['id'] ?>" name="<?php echo $title['name'] ?>" type="text" value="<?php echo $title['value'] ?>" />
</p>

<p>
	<input type="radio" id="<?php echo $events_seek_type['id']; ?>_events" name="<?php echo $events_seek_type['name']; ?>" value="events"<?php
	if ( 'events' === $events_seek_type['value'] ) {
		echo ' checked="checked"';
	}
	?> />
	<label for="<?php echo $events_seek_type['id']; ?>_events"><?php _e( 'Number of events to show:', AI1EC_PLUGIN_NAME ) ?></label>
	<input id="<?php echo $events_per_page['id'] ?>" name="<?php echo $events_per_page['name'] ?>" type="text" size="3" value="<?php echo $events_per_page['value'] ?>" />
</p>
<p>
	<input type="radio" id="<?php echo $events_seek_type['id']; ?>_days" name="<?php echo $events_seek_type['name']; ?>" value="days"<?php
	if ( 'days' === $events_seek_type['value'] ) {
		echo ' checked="checked"';
	}
	?> />
	<label for="<?php echo $events_seek_type['id']; ?>_days"><?php _e( 'Number of days to show:', AI1EC_PLUGIN_NAME ) ?></label>
	<input id="<?php echo $days_per_page['id'] ?>" name="<?php echo $days_per_page['name'] ?>" type="text" size="3" value="<?php echo $days_per_page['value'] ?>" />
</p>

<p class="ai1ec-limit-by-container">
	<?php _e( 'Limit to:', AI1EC_PLUGIN_NAME ); ?>
	<br />
	<!-- Limit by Category -->
	<input id="<?php echo $limit_by_cat['id'] ?>" class="ai1ec-limit-by-cat" name="<?php echo $limit_by_cat['name'] ?>" type="checkbox" value="1" <?php if( $limit_by_cat['value'] ) echo 'checked="checked"' ?> />
	<label for="<?php echo $limit_by_cat['id'] ?>"><?php _e( 'Events with these <strong>Categories</strong>', AI1EC_PLUGIN_NAME ) ?></label>
</p>
<div class="ai1ec-limit-by-options-container" <?php if( ! $limit_by_cat['value'] ) { ?> style="display: none;" <?php } ?>>
	<!-- Limit by Category Select box -->
	<select id="<?php echo $cat_ids['id'] ?>" class="ai1ec-widget-cat-ids" name="<?php echo $cat_ids['name'] ?>[]" size="5" multiple="multiple">
		<?php foreach( $cat_ids['options'] as $event_cat ): ?>
			<option value="<?php echo $event_cat->term_id; ?>"<?php if( in_array( $event_cat->term_id, $cat_ids['value'] ) ) { ?> selected="selected"<?php } ?>><?php echo $event_cat->name; ?></option>
		<?php endforeach ?>
		<?php if( count( $cat_ids['options'] ) == 0 ) : ?>
			<option disabled><?php _e( 'No categories found.', AI1EC_PLUGIN_NAME ) ?></option>
		<?php endif ?>
	</select>
</div>
<p class="ai1ec-limit-by-container">
	<!-- Limit by Tag -->
	<input id="<?php echo $limit_by_tag['id'] ?>" class="ai1ec-limit-by-tag" name="<?php echo $limit_by_tag['name'] ?>" type="checkbox" value="1" <?php if( $limit_by_tag['value'] ) echo 'checked="checked"' ?> />
	<label for="<?php echo $limit_by_tag['id'] ?>"><?php _e( '<strong>Or</strong> events with these <strong>Tags</strong>', AI1EC_PLUGIN_NAME ) ?></label>
</p>
<div class="ai1ec-limit-by-options-container" <?php if( ! $limit_by_tag['value'] ) { ?> style="display: none;" <?php } ?>>
	<!-- Limit by Tag Select box -->
	<select id="<?php echo $tag_ids['id'] ?>" class="ai1ec-widget-tag-ids" name="<?php echo $tag_ids['name'] ?>[]" size="5" multiple="multiple">
		<?php foreach( $tag_ids['options'] as $event_tag ): ?>
			<option value="<?php echo $event_tag->term_id; ?>"<?php if( in_array( $event_tag->term_id, $tag_ids['value'] ) ) { ?> selected="selected"<?php } ?>><?php echo $event_tag->name; ?></option>
		<?php endforeach ?>
		<?php if( count( $tag_ids['options'] ) == 0 ) : ?>
			<option disabled><?php _e( 'No tags found.', AI1EC_PLUGIN_NAME ) ?></option>
		<?php endif ?>
	</select>
</div>
<br />
<p>
	<input id="<?php echo $show_calendar_button['id'] ?>" name="<?php echo $show_calendar_button['name'] ?>" type="checkbox" value="1" <?php if( $show_calendar_button['value'] ) echo 'checked="checked"' ?> />
	<label for="<?php echo $show_calendar_button['id'] ?>"><?php _e( 'Show <strong>View Calendar</strong> button', AI1EC_PLUGIN_NAME ) ?></label>
	<br />
	<input id="<?php echo $show_subscribe_buttons['id'] ?>" name="<?php echo $show_subscribe_buttons['name'] ?>" type="checkbox" value="1" <?php if( $show_subscribe_buttons['value'] ) echo 'checked="checked"' ?> />
	<label for="<?php echo $show_subscribe_buttons['id'] ?>"><?php _e( 'Show <strong>Subscribe</strong> buttons', AI1EC_PLUGIN_NAME ) ?></label>
	<br />
	<input id="<?php echo $hide_on_calendar_page['id'] ?>" name="<?php echo $hide_on_calendar_page['name'] ?>" type="checkbox" value="1" <?php if( $hide_on_calendar_page['value'] ) echo 'checked="checked"' ?> />
	<label for="<?php echo $hide_on_calendar_page['id'] ?>"><?php _e( 'Hide this widget on calendar page', AI1EC_PLUGIN_NAME ) ?></label>
</p>
