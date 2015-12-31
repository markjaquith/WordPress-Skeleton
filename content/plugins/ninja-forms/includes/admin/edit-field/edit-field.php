<?php if ( ! defined( 'ABSPATH' ) ) exit;
function ninja_forms_edit_field( $field_id, $new = false ){
	global $wpdb, $ninja_forms_fields;

	do_action( 'ninja_forms_edit_field_before_li', $field_id, $new );
	do_action( 'ninja_forms_edit_field_li', $field_id, $new );
	do_action( 'ninja_forms_edit_field_after_li', $field_id, $new );

}

function ninja_forms_edit_field_el_output($field_id, $type, $label = '', $name = '', $value = '', $width = 'wide', $options = '', $class = '', $desc = '', $label_class = '' ){
	global $ninja_forms_fields, $nf_rte_editors;

	$field_row = ninja_forms_get_field_by_id($field_id);
	$field_type = $field_row['type'];
	$reg_field = $ninja_forms_fields[$field_type];

	$class = 'code ninja-forms-'.$field_type.'-'.$name.' '.$class;
	$id = 'ninja_forms_field_'.$field_id.'_'.$name;
	if ( strpos( $name, '[' ) !== false ) {
		str_replace( ']', '', $name );
		$name = explode( '[', $name );
		if ( is_array ( $name ) ) {
			$tmp_name = 'ninja_forms_field_'.$field_id;
			foreach ( $name as $n ) {
				$tmp_name .= '['.$n.']';
			}
			$name = $tmp_name;
		} else {
			$name = 'ninja_forms_field_'.$field_id.'['.$name.']';
		}
	} else {
		$name = 'ninja_forms_field_'.$field_id.'['.$name.']';
	}

	?>
	<div class="description description-<?php echo $width;?> <?php echo $type;?>" id="<?php echo $name;?>_p">
	<?php
	if($type != 'rte'){
		$value = ninja_forms_esc_html_deep( $value );
	?>
		<span class="field-option">
			<?php
	}
	if($type != 'checkbox' AND $type != 'desc'){
		?>
		<label for="<?php echo $id;?>" id="<?php echo $id;?>_label" class="<?php echo $label_class;?>">
			<?php _e( $label , 'ninja-forms'); ?></label><br/>
		<?php
	}
	switch($type){
		case 'text':
		?>
			<input type="text" class="<?php echo $class;?>" name="<?php echo $name;?>" id="<?php echo $id;?>" value="<?php echo $value;?>" />
		<?php
		break;
		case 'number':
		?>
			<input type="number" class="<?php echo $class;?>" name="<?php echo $name;?>" id="<?php echo $id;?>" value="<?php echo $value;?>" />
		<?php
		break;
		case 'checkbox':
		?>
			<label for="<?php echo $id;?>" id="<?php echo $id;?>_label">
				<input type="hidden" value="0" name="<?php echo $name;?>">
				<input type="checkbox" value="1" name="<?php echo $name;?>" id="<?php echo $id;?>" class="<?php echo $class;?>" <?php checked($value, 1);?>>
				<?php _e( $label , 'ninja-forms'); ?>
			</label>
		<?php
		break;
		case 'select':
		?>
			<select id="<?php echo $id;?>" name="<?php echo $name;?>" class="<?php echo $class;?>">
				<?php
				if(is_array($options) AND !empty($options)){
					foreach($options as $opt){
						?>
						<option value="<?php echo $opt['value'];?>" <?php selected($opt['value'], $value); ?> ><?php _e( $opt['name'], 'ninja-forms'); ?></option>
						<?php
					}
				}
			?>
			</select>
		<?php
		break;
		case 'multi':
		?>
			<select multiple="multiple" id="<?php echo $id;?>" name="<?php echo $name;?>" class="<?php echo $class;?>">
				<?php
				if(is_array($options) AND !empty($options)){
					foreach($options as $opt){
						?>
						<option value="<?php echo $opt['value'];?>" <?php selected($opt['value'], $value); ?> ><?php _e( $opt['name'], 'ninja-forms'); ?></option>
						<?php
					}
				}
			?>
			</select>
		<?php
		break;
		case 'textarea':
		?>
			<textarea id="<?php echo $id;?>" name="<?php echo $name;?>" class="<?php echo $class;?>" rows="3" cols="20" ><?php echo $value;?></textarea>
		<?php
		break;
		case 'hidden':
		?>
			<input type="hidden" name="<?php echo $name;?>" value="<?php echo $value;?>">
		<?php
		break;
		case 'desc':
		?>
			<span class="desc"><label for="<?php echo $id;?>" id="<?php echo $id;?>_label"><?php _e($label, 'ninja-forms'); ?></label></span>
		<?php
		break;
		case 'rte':
			// Check if our current user has the RTE disabled.
			$user_id = get_current_user_id();
			$rich_editing = get_user_meta( $user_id, 'rich_editing', true );
			if ( 'true' == $rich_editing ) {
				$editor_id = str_replace( '[', '_', $name );
				$editor_id = str_replace( ']', '', $editor_id );
				
				$plugin_settings = nf_get_settings();
				if ( !isset( $plugin_settings['version_2_2_25_rte_fix'] ) OR $plugin_settings['version_2_2_25_rte_fix'] == '' ) {
					$value = html_entity_decode( $value );
					$plugin_settings['version_2_2_25_rte_fix'] = 1;
					update_option( 'ninja_forms_settings', $plugin_settings );
				}

				$args = apply_filters( 'ninja_forms_edit_field_rte', array( 'textarea_name' => $name ) );
				wp_editor( $value, $editor_id, $args );				

				// If we're using ajax, add this editor ID to our global var so that we can instantiate it on the front-end.
				if ( isset ( $_POST['action'] ) && ( $_POST['action'] == 'ninja_forms_new_field' || $_POST['action'] == 'nf_output_field_settings_html' ) )
					$nf_rte_editors[] = $editor_id;
			} else {
				?>
				<textarea id="<?php echo $id;?>" name="<?php echo $name;?>" class="<?php echo $class;?>" rows="3" cols="20" ><?php echo $value;?></textarea>
				<?php
			}

		break;
	}

	if($desc != ''){
	?>
		<span class="description">
			<?php _e($desc, 'ninja-forms'); ?>
		</span>
	<?php
		}
	if($type != 'rte'){
		?>
		</span>
		<?php
	}
	?>
	</div>
	<?php
}