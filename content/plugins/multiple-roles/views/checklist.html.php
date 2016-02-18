<?php
/**
 * Output the roles checklist.
 *
 * @var $roles array All WordPress roles in name => label pairs.
 * @var $user_roles array An array of role names belonging to the current user.
 */
?><h3>Permissions</h3>
<table class="form-table">
	<tr>
		<th>Roles</th>
		<td>
			<?php foreach( $roles as $name => $label ) : ?>
				<label for="md-multiple-roles-<?php echo $name; ?>">
					<input
						id="md-multiple-roles-<?php echo $name; ?>"
						type="checkbox"
						name="md_multiple_roles[]"
						value="<?php echo $name; ?>"
						<?php checked( in_array( $name, $user_roles ) ); ?>
					/>
					<?php echo $label; ?>
				</label>
				<br />
			<?php endforeach; ?>
		</td>
	</tr>
</table>