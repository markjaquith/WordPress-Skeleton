<h3><a name="ai1ec"><?php
_e( 'All in One Event Calendar', AI1EC_PLUGIN_NAME );
?></a></h3>
<table class="ai1ec-form">
	<tbody>
		<tr>
			<td class="ai1ec-first">
				<label for="ai1ec_user_timezone">
					<?php _e( 'Your preferred timezone', AI1EC_PLUGIN_NAME ); ?>?
				</label>
			</td>
			<td>
				<select name="ai1ec_user_timezone" id="ai1ec_user_timezone">
				<?php echo $tz_selector; ?>
				</select>
			</td>
		</tr>
	</tbody>
</table>
