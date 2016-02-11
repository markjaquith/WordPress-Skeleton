<?php
/**
 * Output a list of roles belonging to the current user.
 *
 * @var $roles array All applicable roles in name => label pairs.
 */
?><div class="md-multiple-roles">
	<?php if ( $roles ) :
		foreach( $roles as $name => $label ) :
			$roles[$name] = '<a href="users.php?role=' . $name . '">' . $label . '</a>';
		endforeach;
		echo implode( ', ', $roles );
	else : ?>
		<span class="md-multiple-roles-no-role">None</span>
	<?php endif; ?>
</div><!-- .md-multiple-roles -->