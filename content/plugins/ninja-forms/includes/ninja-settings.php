<?php if ( ! defined( 'ABSPATH' ) ) exit;

function nf_get_settings(){
  $instance = Ninja_Forms();
  if ( ! empty ( $instance ) && ! empty ( $instance->plugin_settings ) ) {
	$settings = Ninja_Forms()->plugin_settings;
  } else {
  	$settings = Ninja_Forms()->get_plugin_settings();
  }

  return $settings;
} // nf_get_settings