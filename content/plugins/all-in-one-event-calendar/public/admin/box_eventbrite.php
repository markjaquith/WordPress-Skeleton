<h4 class="box_h4"><?php _e( 'Eventbrite Ticketing', AI1EC_PLUGIN_NAME ); ?>:</h4>
<table>
	<tbody>
		<tr>
			<td>
				<label>
					<?php _e( 'Register this event with Eventbrite.com?', AI1EC_PLUGIN_NAME ); ?>
				</label>
			</td>
			<td>
				<input type="checkbox" name="<?php echo AI1EC_PLUGIN_NAME; ?>_eventbrite_yes" id="<?php echo AI1EC_PLUGIN_NAME; ?>_eventbrite_yes" />
				<label for="<?php echo AI1EC_PLUGIN_NAME; ?>_eventbrite_yes"><?php _e( 'Yes', AI1EC_PLUGIN_NAME ); ?></label>
				<input type="checkbox" name="<?php echo AI1EC_PLUGIN_NAME; ?>_eventbrite_no" id="<?php echo AI1EC_PLUGIN_NAME; ?>_eventbrite_no" />
				<label for="<?php echo AI1EC_PLUGIN_NAME; ?>_eventbrite_no"><?php _e( 'No', AI1EC_PLUGIN_NAME ); ?></label>
			</td>
		</tr>
	</tbody>
</table>

<div id="<?php echo AI1EC_PLUGIN_NAME; ?>_eventbrite_body">
	<h4>
		<?php _e( 'Set up your first ticket', AI1EC_PLUGIN_NAME ); ?>
		<small>
			<?php _e( 'To create multiple tickets per event, submit this form, then follow the link to Eventbrite.', AI1EC_PLUGIN_NAME ); ?>
		</small>
	</h4>
	<table>
		<tbody>
			<tr>
				<td>
					<label for="<?php echo AI1EC_PLUGIN_NAME; ?>_name">
						<?php _e( 'Name', AI1EC_PLUGIN_NAME ); ?>:
					</label>
				</td>
				<td>
					<input type="text" name="<?php echo AI1EC_PLUGIN_NAME; ?>_name" id="<?php echo AI1EC_PLUGIN_NAME; ?>_name" />
				</td>
			</tr>
			<tr>
				<td>
					<label for="<?php echo AI1EC_PLUGIN_NAME; ?>_description">
						<?php _e( 'Description', AI1EC_PLUGIN_NAME ); ?>:
					</label>
				</td>
				<td>
					<textarea name="<?php echo AI1EC_PLUGIN_NAME; ?>_description" id="<?php echo AI1EC_PLUGIN_NAME; ?>_description">
					</textarea>
				</td>
			</tr>
			<tr>
				<td>
					<label>
						<?php _e( 'Type', AI1EC_PLUGIN_NAME ); ?>:
					</label>
				</td>
				<td>
					<input type="radio" name="<?php echo AI1EC_PLUGIN_NAME; ?>_type" id="<?php echo AI1EC_PLUGIN_NAME; ?>_type_price" />
					<label for="<?php echo AI1EC_PLUGIN_NAME; ?>_type_price"><?php _e( 'Set Price', AI1EC_PLUGIN_NAME ); ?></label>
					<input type="radio" name="<?php echo AI1EC_PLUGIN_NAME; ?>_type" id="<?php echo AI1EC_PLUGIN_NAME; ?>_type_donation" />
					<label for="<?php echo AI1EC_PLUGIN_NAME; ?>_type_donation"><?php _e( 'Donation Based', AI1EC_PLUGIN_NAME ); ?></label>
				</td>
			</tr>
			<tr>
				<td>
				</td>
				<td>
					<small>
						<?php _e( "The price for this event's first ticket will be taken from the Cost field above.", AI1EC_PLUGIN_NAME ); ?>
					</small>
				</td>
			</tr>
			<tr>
				<td>
					<label for="<?php echo AI1EC_PLUGIN_NAME; ?>_quantity">
						<?php _e( 'Quantity', AI1EC_PLUGIN_NAME ); ?>:
					</label>
				</td>
				<td>
					<input type="text" name="<?php echo AI1EC_PLUGIN_NAME; ?>_quantity" id="<?php echo AI1EC_PLUGIN_NAME; ?>_quantity" />
				</td>
			</tr>
			<tr>
				<td>
					<label>
						<?php _e( 'Include Fee in Price', AI1EC_PLUGIN_NAME ); ?>:
					</label>
				</td>
				<td>
					<input type="radio" name="<?php echo AI1EC_PLUGIN_NAME; ?>_fee_in_price" id="<?php echo AI1EC_PLUGIN_NAME; ?>_add_fee" />
					<label for="<?php echo AI1EC_PLUGIN_NAME; ?>_add_fee"><?php _e( 'Add Service Fee on top of price', AI1EC_PLUGIN_NAME ); ?></label>
					<input type="radio" name="<?php echo AI1EC_PLUGIN_NAME; ?>_fee_in_price" id="<?php echo AI1EC_PLUGIN_NAME; ?>_include_fee" />
					<label for="<?php echo AI1EC_PLUGIN_NAME; ?>_include_fee"><?php _e( 'Include Service fee in price', AI1EC_PLUGIN_NAME ); ?></label>
				</td>
			</tr>
			<tr>
				<td>
					<label for="<?php echo AI1EC_PLUGIN_NAME; ?>">
						<?php _e( 'Payment Options', AI1EC_PLUGIN_NAME ); ?>:
					</label>
				</td>
				<td>
					<input type="checkbox" name="<?php echo AI1EC_PLUGIN_NAME; ?>_payment_paypal" id="<?php echo AI1EC_PLUGIN_NAME; ?>_payment_paypal" />
					<label for="<?php echo AI1EC_PLUGIN_NAME; ?>_payment_paypal"><?php _e( 'Paypal', AI1EC_PLUGIN_NAME ); ?></label>
					<input type="checkbox" name="<?php echo AI1EC_PLUGIN_NAME; ?>_payment_google" id="<?php echo AI1EC_PLUGIN_NAME; ?>_payment_google" />
					<label for="<?php echo AI1EC_PLUGIN_NAME; ?>_payment_google"><?php _e( 'Google Checkout', AI1EC_PLUGIN_NAME ); ?></label>
					<input type="checkbox" name="<?php echo AI1EC_PLUGIN_NAME; ?>_payment_check" id="<?php echo AI1EC_PLUGIN_NAME; ?>_payment_check" />
					<label for="<?php echo AI1EC_PLUGIN_NAME; ?>_payment_check"><?php _e( 'Check', AI1EC_PLUGIN_NAME ); ?></label>
					<input type="checkbox" name="<?php echo AI1EC_PLUGIN_NAME; ?>_payment_cash" id="<?php echo AI1EC_PLUGIN_NAME; ?>_payment_cash" />
					<label for="<?php echo AI1EC_PLUGIN_NAME; ?>_payment_cash"><?php _e( 'Cash', AI1EC_PLUGIN_NAME ); ?></label>
					<input type="checkbox" name="<?php echo AI1EC_PLUGIN_NAME; ?>_payment_invoice" id="<?php echo AI1EC_PLUGIN_NAME; ?>_payment_invoice" />
					<label for="<?php echo AI1EC_PLUGIN_NAME; ?>_payment_invoice"><?php _e( 'Send an Invoice', AI1EC_PLUGIN_NAME ); ?></label>
				</td>
			</tr>
		</tbody>
	</table>
</div>