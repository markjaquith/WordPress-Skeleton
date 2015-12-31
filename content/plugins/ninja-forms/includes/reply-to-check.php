<?php if ( ! defined( 'ABSPATH' ) ) exit;

function ninja_forms_replyto_change() {

	$plugin_settings = nf_get_settings();
	if ( !isset ( $plugin_settings['fix_field_reply_to'] ) or $plugin_settings['fix_field_reply_to'] != 1 ) {
		$fields = ninja_forms_get_all_fields();
		foreach ($fields as $field) {
			if ( $field['type'] = '_text' ) {
				$change_required = false;
				if ( isset( $field['data']['from_email'] ) and $field['data']['from_email'] == 1 ) {
					$field['data']['replyto_email'] = 1;
					unset( $field['data']['from_email'] );
					$change_required = true;
				} elseif ( isset( $field['data']['from_email'] ) and $field['data']['from_email'] == 0 ) {
					$field['data']['replyto_email'] = 0;
					unset( $field['data']['from_email'] );
					$change_required = true;
				}
				if ( $change_required ) {
					$data = serialize( $field['data'] );
					$args = array(
						'update_array' => array(
							'data' => $data,
						),
						'where' => array(
							'id' => $field['id'],
						),
					);
					ninja_forms_update_field( $args );
				}
			}
		}
		$plugin_settings['fix_field_reply_to'] = 1;
		update_option( 'ninja_forms_settings', $plugin_settings );
	}
}

add_action( 'init', 'ninja_forms_replyto_change' );