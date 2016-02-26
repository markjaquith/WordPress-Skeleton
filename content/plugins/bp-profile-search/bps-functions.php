<?php
include 'bps-form.php';
include 'bps-help.php';
include 'bps-search.php';

function bps_admin_js ()
{
	$translations = array (
		'field' => __('field', 'bp-profile-search'),
		'label' => __('label', 'bp-profile-search'),
		'description' => __('description', 'bp-profile-search'),
		'range' => __('Range', 'bp-profile-search'),
	);
	wp_enqueue_script ('bps-admin', plugins_url ('bps-admin.js', __FILE__), array ('jquery-ui-sortable'), BPS_VERSION);
	wp_localize_script ('bps-admin', 'bps_strings', $translations);
}

function bps_update_meta ()
{
	$bps_options = array ();

	list ($x, $fields) = bps_get_fields ();

	$bps_options['field_name'] = array ();
	$bps_options['field_label'] = array ();
	$bps_options['field_desc'] = array ();
	$bps_options['field_range'] = array ();

	$j = 0;
	$posted = isset ($_POST['bps_options'])? $_POST['bps_options']: array ();
	if (isset ($posted['field_name']))  foreach ($posted['field_name'] as $k => $id)
	{
		if (empty ($fields[$id]))  continue;

		$field = $fields[$id];
		$field_type = $field->type;
		$field_type = apply_filters ('bps_field_validation_type', $field_type, $field);
		$field_type = apply_filters ('bps_field_type_for_validation', $field_type, $field);
		$label = stripslashes ($posted['field_label'][$k]);
		$desc = stripslashes ($posted['field_desc'][$k]);

		$bps_options['field_name'][$j] = $id;
		$bps_options['field_label'][$j] = $l = $label;
		$bps_options['field_desc'][$j] = $d = $desc;
		$bps_options['field_range'][$j] = $r = isset ($posted['field_range'][$k]);

		if (bps_custom_field ($field_type))
		{
			list ($l, $d, $r) = apply_filters ('bps_field_validation', array ($l, $d, $r), $field);
			$bps_options['field_label'][$j] = $l;
			$bps_options['field_desc'][$j] = $d;
			$bps_options['field_range'][$j] = $r;
		}
		else
		{
			if ($field_type == 'datebox')  $bps_options['field_range'][$j] = true;
			if ($field_type == 'checkbox' || $field_type == 'multiselectbox')  $bps_options['field_range'][$j] = false;
		}

		if ($bps_options['field_range'][$j] == false)  $bps_options['field_range'][$j] = null;
		$j = $j + 1;
	}

	return $bps_options;
}

function bps_fields_box ($post)
{
	$bps_options = bps_meta ($post->ID);

	list ($groups, $fields) = bps_get_fields ();
	echo '<script>var bps_groups = ['. json_encode ($groups). '];</script>';
?>

	<div id="field_box" class="field_box">
<?php

	foreach ($bps_options['field_name'] as $k => $id)
	{
		if (empty ($fields[$id]))  continue;

		$field = $fields[$id];
		$label = esc_attr ($bps_options['field_label'][$k]);
		$default = esc_attr ($field->name);
		$showlabel = empty ($label)? "placeholder=\"$default\"": "value=\"$label\"";
		$desc = esc_attr ($bps_options['field_desc'][$k]);
		$default = esc_attr ($field->description);
		$showdesc = empty ($desc)? "placeholder=\"$default\"": "value=\"$desc\"";
?>

		<p id="field_div<?php echo $k; ?>" class="sortable">
			<span>&nbsp;&Xi; </span>
<?php
			bps_field_select ("bps_options[field_name][$k]", "field_name$k", $id);
?>
			<input type="text" name="bps_options[field_label][<?php echo $k; ?>]" id="field_label<?php echo $k; ?>" <?php echo $showlabel; ?> style="width: 16%" />
			<input type="text" name="bps_options[field_desc][<?php echo $k; ?>]" id="field_desc<?php echo $k; ?>" <?php echo $showdesc; ?> style="width: 32%" />
			<label><input type="checkbox" name="bps_options[field_range][<?php echo $k; ?>]" id="field_range<?php echo $k; ?>" value="<?php echo $k; ?>"<?php if (isset ($bps_options['field_range'][$k])) echo ' checked="checked"'; ?> /><?php _e('Range', 'bp-profile-search'); ?> </label>
			<a href="javascript:hide('field_div<?php echo $k; ?>')" class="delete">[x]</a>
		</p>
<?php
	}
?>
		<input type="hidden" id="field_next" value="<?php echo count ($bps_options['field_name']); ?>" />
	</div>
	<p><a href="javascript:add_field()"><?php _e('Add Field', 'bp-profile-search'); ?></a></p>
<?php
}

function bps_field_select ($name, $id, $value)
{
	list ($groups, $x) = bps_get_fields ();

	echo "<select name='$name' id='$id'>\n";
	foreach ($groups as $group => $fields)
	{
		$group = esc_attr ($group);
		echo "<optgroup label='$group'>\n";
		foreach ($fields as $field)
		{
			$selected = $field['id'] == $value? " selected='selected'": '';
			echo "<option value='$field[id]'$selected>$field[name]</option>\n";
		}
		echo "</optgroup>\n";
	}
	echo "</select>\n";

	return true;
}

function bps_get_fields ()
{
	global $group, $field;

	static $groups = array ();
	static $fields = array ();

	if (count ($groups))  return array ($groups, $fields);

	if (!function_exists ('bp_has_profile'))
	{
		printf ('<p class="bps_error">'. __('%s: The BuddyPress Extended Profiles component is not active.', 'bp-profile-search'). '</p>',
			'<strong>BP Profile Search '. BPS_VERSION. '</strong>');
		return array ($groups, $fields);
	}

	$args = array ('hide_empty_fields' => false, 'member_type' => bp_get_member_types ());
	if (bp_has_profile ($args))
	{
		while (bp_profile_groups ())
		{
			bp_the_profile_group (); 
			$group->name = str_replace ('&amp;', '&', stripslashes ($group->name));
			$groups[$group->name] = array ();

			while (bp_profile_fields ())
			{
				bp_the_profile_field ();
				$field->name = str_replace ('&amp;', '&', stripslashes ($field->name));
				$field->description = str_replace ('&amp;', '&', stripslashes ($field->description));
				$groups[$group->name][] = array ('id' => $field->id, 'name' => $field->name);
				$fields[$field->id] = $field;
			}
		}
	}

	list ($groups, $fields) = apply_filters ('bps_get_fields', array ($groups, $fields));
	return array ($groups, $fields);
}

function bps_custom_field ($type)
{
	return !in_array ($type, array ('textbox', 'number', 'url', 'textarea', 'selectbox', 'multiselectbox', 'radio', 'checkbox', 'datebox'));
}

function bps_get_widget ($form)
{
	$widgets = get_option ('widget_bps_widget');
	if ($widgets == false)  return __('unused', 'bp-profile-search');

	$titles = array ();
	foreach ($widgets as $key => $widget)
		if (isset ($widget['form']) && $widget['form'] == $form)  $titles[] = !empty ($widget['title'])? $widget['title']: __('(no title)');
		
	return count ($titles)? implode ('<br/>', $titles): __('unused', 'bp-profile-search');
}

function bps_field_options ($id)
{
	static $options = array ();

	if (isset ($options[$id]))  return $options[$id];

	$field = new BP_XProfile_Field ($id);
	if (empty ($field->id))  return array ();

	$options[$id] = array ();
	$rows = $field->get_children ();
	if (is_array ($rows))
		foreach ($rows as $row)
			$options[$id][stripslashes (trim ($row->name))] = stripslashes (trim ($row->name));

	return $options[$id];
}
