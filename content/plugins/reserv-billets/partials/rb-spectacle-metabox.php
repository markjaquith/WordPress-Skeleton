<?php
/**
 * La metabox de test.
 * Va très fort probablement être changé.
 */
?>

<div id="rb-spectacle">
	<?php $post_meta = get_post_meta( get_the_ID() ); ?>
	<div id="rb-spectacle-data">
		<?php foreach ($post_meta as $pm_key => $pm_value) { ?>
		<p><strong><?=$pm_key?></strong> - <?=$pm_value[0]?></p>
		<?php } ?>
	</div>
</div><!-- #rb-spectacle-admin -->