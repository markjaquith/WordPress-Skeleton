<?php
if ( empty( $parent ) && empty( $children ) ) {
	return '';
}
?>
<div class="ai1ec-panel-heading">
	<a data-toggle="ai1ec-collapse"
		data-parent="#ai1ec-add-new-event-accordion"
		href="#ai1ec-event-children-box">
		<i class="ai1ec-fa ai1ec-fa-retweet"></i> <?php
		if ( $parent ) {
			_e( 'Base recurrence event', AI1EC_PLUGIN_NAME );
		} else {
			_e( 'Modified recurrence events', AI1EC_PLUGIN_NAME );
		}
	?>
	</a>
</div>
<div id="ai1ec-event-children-box" class="ai1ec-panel-collapse ai1ec-collapse">
	<div class="ai1ec-panel-body">
	<?php if ( $parent ) : ?>
	<?php _e( 'Edit parent:', AI1EC_PLUGIN_NAME ); ?>
	<a href="<?php echo get_edit_post_link( $parent->get( 'post_id' ) ); ?>"><?php
	echo apply_filters( 'the_title', $parent->get( 'post' )->post_title, $parent->get( 'post_id' ) );
	?></a>
	<?php else : /* children */ ?>
	<h4><?php _e( 'Modified Events', AI1EC_PLUGIN_NAME ); ?></h4>
	<ul>
		<?php foreach ( $children as $child ) : ?>
		<li>
			<?php _e( 'Edit:', AI1EC_PLUGIN_NAME ); ?>
			<a href="<?php echo get_edit_post_link( $child->get( 'post_id' ) ); ?>"><?php
			echo $child->get( 'post' )->post_title;
			?></a>, <?php echo $registry->get( 'view.event.time' )->get_timespan_html( $child, 'long' ); ?>
		</li>
		<?php endforeach; ?>
	</ul>
	<?php endif; ?>
	</div>
</div>
