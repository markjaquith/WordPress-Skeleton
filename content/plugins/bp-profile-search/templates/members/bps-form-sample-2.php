<?php

/*
 * BP Profile Search - form template 'bps-form-sample-2'
 *
 * See http://dontdream.it/bp-profile-search/form-templates/ if you wish to modify this template or develop a new one.
 *
 */

	$F = bps_escaped_form_data ();

	$toggle_id = 'bps_toggle'. $F->id;
	$form_id = 'bps_'. $F->location. $F->id;

	if ($F->location != 'directory')
	{
		$action = $F->action;
	}
	else
	{
		$action = parse_url ($_SERVER['REQUEST_URI'], PHP_URL_PATH);
?>
	<div class="bps_header">
		<?php echo $F->header; ?>

<?php
		if ($F->toggle)
		{
?>
		<p>
		  <input id="<?php echo $toggle_id; ?>" type="submit" value="<?php echo $F->toggle_text; ?>">
		</p>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				$('#<?php echo $form_id; ?>').hide();
				$('#<?php echo $toggle_id; ?>').click(function(){
					$('#<?php echo $form_id; ?>').toggle();
				});
			});
		</script>
<?php
		}
?>
	</div>
<?php
	}

	echo "<form action='$action' method='$F->method' id='$form_id'>\n";

	foreach ($F->fields as $f)
	{
		if ($f->display == 'hidden')
		{
			echo "<input type='hidden' name='$f->code' value='$f->value'>\n";
			continue;
		}

		echo "<p>\n";

		switch ($f->display)
		{
		case 'range':
			echo "<label for='$f->code'>$f->label</label><br>\n";
			echo "<input style='width: 15%;' type='text' name='{$f->code}_min' id='$f->code' value='$f->min'>";
			echo '&nbsp;-&nbsp;';
			echo "<input style='width: 15%;' type='text' name='{$f->code}_max' value='$f->max'>\n";
			break;

		case 'textbox':
		case 'textarea':
			echo "<label for='$f->code'>$f->label</label><br>\n";
			echo "<input type='text' name='$f->code' id='$f->code' value='$f->value'>\n";
			break;

		case 'number':
			echo "<label for='$f->code'>$f->label</label><br>\n";
			echo "<input type='number' name='$f->code' id='$f->code' value='$f->value'>\n";
			break;

		case 'url':
			echo "<label for='$f->code'>$f->label</label><br>\n";
			echo "<input type='text' inputmode='url' name='$f->code' id='$f->code' value='$f->value'>\n";
			break;

		case 'selectbox':
		case 'multiselectbox':
		case 'radio':
		case 'checkbox':
			echo "<label for='$f->code'>$f->label</label><br>\n";
			echo "<select name='$f->code' id='$f->code'>\n";

			$no_selection = apply_filters ('bps_field_selectbox_no_selection', '', $f);
			if (is_string ($no_selection))
				echo "<option  value=''>$no_selection</option>\n";

			foreach ($f->options as $key => $label)
			{
				$selected = in_array ($key, $f->values)? "selected='selected'": "";
				echo "<option $selected value='$key'>$label</option>\n";
			}
			echo "</select>\n";
			break;

		default:
			echo "<p>BP Profile Search: don't know how to display the <em>$f->display</em> field type.</p>\n";
			break;
		}

		if ($f->description != '' && $f->description != '-')
			echo "<br><em>$f->description</em>\n";

		echo "</p>\n";
	}

	echo "<p>\n";
	echo "<input class='button' type='submit' value='". __('Search', 'buddypress'). "'>\n";
	echo "</p>\n";
	echo "<input type='hidden' name='bp_profile_search' value='$F->id'>\n";
	echo "</form>\n";

// BP Profile Search - end of template
