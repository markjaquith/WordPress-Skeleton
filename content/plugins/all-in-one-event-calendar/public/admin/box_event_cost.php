<div class="ai1ec-panel-heading">
	<a data-toggle="ai1ec-collapse"
		data-parent="#ai1ec-add-new-event-accordion"
		href="#ai1ec-event-cost-box">
		<i class="ai1ec-fa ai1ec-fa-shopping-cart ai1ec-fa-fw"></i>
		<?php _e( 'Event cost and Tickets', AI1EC_PLUGIN_NAME ); ?>
		<i class="ai1ec-fa ai1ec-fa-warning ai1ec-fa-fw ai1ec-hidden"></i>
	</a>
</div>
<div id="ai1ec-event-cost-box" class="ai1ec-panel-collapse ai1ec-collapse">
	<div class="ai1ec-panel-body">
		<table class="ai1ec-form">
			<tbody>
				<tr>
					<td class="ai1ec-first ai1ec-cost-label">
						<label for="ai1ec_cost">
							<?php _e( 'Cost', AI1EC_PLUGIN_NAME ); ?>:
						</label>
					</td>
					<td>
						<input type="text"
							name="ai1ec_cost"
							class="ai1ec-form-control"
							id="ai1ec_cost" <?php
							if ( ! empty( $is_free ) ) {
								echo 'class="ai1ec-hidden" ';
							}
						 ?>value="<?php echo esc_attr( $cost ); ?>">
						<label for="ai1ec_is_free">
							<input class="checkbox"
								type="checkbox"
								name="ai1ec_is_free"
								id="ai1ec_is_free"
								value="1" <?php echo $is_free; ?>>
							<?php _e( 'Free event', AI1EC_PLUGIN_NAME ); ?>
						</label>
					</td>
				</tr>
				<tr>
					<td>
						<label for="ai1ec_ticket_url"><?php
							echo ( ! empty( $is_free ) )
									 ? __( 'Registration URL:', AI1EC_PLUGIN_NAME )
									 : __( 'Buy Tickets URL:', AI1EC_PLUGIN_NAME );
						?></label>
					</td>
					<td>
						<input type="text" name="ai1ec_ticket_url" id="ai1ec_ticket_url"
							class="ai1ec-form-control"
							value="<?php echo esc_attr( $event->get_nonloggable_url( $ticket_url ) ); ?>">
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
