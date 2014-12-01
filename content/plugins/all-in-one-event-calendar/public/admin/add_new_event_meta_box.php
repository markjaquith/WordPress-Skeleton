<div class="timely ai1ec-panel-group ai1ec-form-inline"
	id="ai1ec-add-new-event-accordion">
	<?php foreach ( $boxes as $i => $box ) : ?>
		<div class="ai1ec-panel ai1ec-panel-default
		<?php echo 0 === $i ? 'ai1ec-overflow-visible' : '' ?>">
		<?php echo $box; ?>
		</div>
	<?php endforeach; ?>
</div>