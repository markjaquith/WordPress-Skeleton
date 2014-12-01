<div class="ai1ec-panel-heading">
	<a data-toggle="ai1ec-collapse"
		data-parent="#ai1ec-add-new-event-accordion"
		href="#ai1ec-event-contact-box">
		<i class="ai1ec-fa ai1ec-fa-phone ai1ec-fa-fw"></i>
		<?php _e( 'Organizer contact info', AI1EC_PLUGIN_NAME ); ?>
		<i class="ai1ec-fa ai1ec-fa-warning ai1ec-fa-fw ai1ec-hidden"></i>
	</a>
</div>
<div id="ai1ec-event-contact-box" class="ai1ec-panel-collapse ai1ec-collapse">
	<div class="ai1ec-panel-body">
		<table class="ai1ec-form">
			<tbody>
				<tr>
					<td class="ai1ec-first">
						<label for="ai1ec_contact_name">
							<?php _e( 'Contact name:', AI1EC_PLUGIN_NAME ); ?>
						</label>
					</td>
					<td>
						<input type="text" name="ai1ec_contact_name"
							id="ai1ec_contact_name"
							class="ai1ec-form-control"
							value="<?php echo esc_attr( $contact_name ); ?>">
					</td>
				</tr>
				<tr>
					<td>
						<label for="ai1ec_contact_phone">
							<?php _e( 'Phone:', AI1EC_PLUGIN_NAME ); ?>
						</label>
					</td>
					<td>
						<input type="text" name="ai1ec_contact_phone"
							id="ai1ec_contact_phone"
							class="ai1ec-form-control"
							value="<?php echo esc_attr( $contact_phone ); ?>">
					</td>
				</tr>
				<tr>
					<td>
						<label for="ai1ec_contact_email">
							<?php _e( 'E-mail:', AI1EC_PLUGIN_NAME ); ?>
						</label>
					</td>
					<td>
						<input type="text" name="ai1ec_contact_email"
							id="ai1ec_contact_email"
							class="ai1ec-form-control"
							value="<?php echo esc_attr( $contact_email ); ?>">
					</td>
				</tr>
				<tr>
					<td>
						<label for="ai1ec_contact_url">
							<?php _e( 'External URL:', AI1EC_PLUGIN_NAME ); ?>
						</label>
					</td>
					<td>
						<input type="text" name="ai1ec_contact_url"
							id="ai1ec_contact_url"
							class="ai1ec-form-control"
							value="<?php echo esc_attr( $event->get_nonloggable_url( $contact_url ) ); ?>">
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
