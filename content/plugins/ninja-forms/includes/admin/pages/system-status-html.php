<div class="nf-box">
	<div>
		<h4><?php _e( 'Please include this information when requesting support:', 'ninja-forms' ); ?> </h4>
		<p class="submit debug-report"><a href="#" class="button-primary"><?php _e( 'Get System Report', 'ninja-forms' ); ?></a></p>
	</div>
	<div id="debug-report"><textarea readonly="readonly"></textarea></div>
</div>
<br/>
<table class="nf-status-table" cellspacing="0">
	<thead>
		<tr>
			<th colspan="2"><?php _e( 'Environment', 'ninja-forms' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><?php _e( 'Home URL','ninja-forms' ); ?>:</td>
			<td><?php echo home_url(); ?></td>
		</tr>
		<tr>
			<td><?php _e( 'Site URL','ninja-forms' ); ?>:</td>
			<td><?php echo site_url(); ?></td>
		</tr>
		<tr>
			<td><?php _e( 'Ninja Forms Version','ninja-forms' ); ?>:</td>
			<td><?php echo esc_html( NF_PLUGIN_VERSION ); ?></td>
		</tr>
		<tr>
			<td><?php _e( 'WP Version','ninja-forms' ); ?>:</td>
			<td><?php bloginfo('version'); ?></td>
		</tr>
		<tr>
			<td><?php _e( 'WP Multisite Enabled','ninja-forms' ); ?>:</td>
			<td><?php if ( is_multisite() ) echo __( 'Yes', 'ninja-forms' ); else echo __( 'No', 'ninja-forms' ); ?></td>
		</tr>
		<tr>
			<td><?php _e( 'Web Server Info','ninja-forms' ); ?>:</td>
			<td><?php echo esc_html( $_SERVER['SERVER_SOFTWARE'] ); ?></td>
		</tr>
		<tr>
			<td><?php _e( 'PHP Version','ninja-forms' ); ?>:</td>
			<td><?php if ( function_exists( 'phpversion' ) ) echo esc_html( phpversion() ); ?></td>
		</tr>
		<tr>
			<td><?php _e( 'MySQL Version','ninja-forms' ); ?>:</td>
			<td>
				<?php
				/** @global wpdb $wpdb */
				global $wpdb;
				echo $wpdb->db_version();
				?>
			</td>
		</tr>
		<tr>
			<td><?php _e( 'PHP Locale','ninja-forms' ); ?>:</td>
			<td><?php
				$locale = localeconv();
				foreach ( $locale as $key => $val )
					if ( is_string( $val ) )
						echo $key . ': ' . $val . '</br>';
			?></td>
		</tr>
		<tr>
			<td><?php _e( 'WP Memory Limit','ninja-forms' ); ?>:</td>
			<td><?php
				$memory = ninja_forms_letters_to_numbers( WP_MEMORY_LIMIT );
				echo size_format( $memory );
				?>
			</td>
		</tr>
		<tr>
			<td><?php _e( 'WP Debug Mode', 'ninja-forms' ); ?>:</td>
			<td><?php if ( defined('WP_DEBUG') && WP_DEBUG ) echo __( 'Yes', 'ninja-forms' ); else echo __( 'No', 'ninja-forms' ); ?></td>
		</tr>
		<tr>
			<td><?php _e( 'WP Language', 'ninja-forms' ); ?>:</td>
			<td><?php if ( defined( 'WPLANG' ) && WPLANG ) echo WPLANG; else  _e( 'Default', 'ninja-forms' ); ?></td>
		</tr>
		<tr>
			<td><?php _e( 'WP Max Upload Size','ninja-forms' ); ?>:</td>
			<td><?php echo size_format( wp_max_upload_size() ); ?></td>
		</tr>
		<?php if ( function_exists( 'ini_get' ) ) : ?>
			<tr>
				<td><?php _e('PHP Post Max Size','ninja-forms' ); ?>:</td>
				<td><?php echo size_format( ninja_forms_letters_to_numbers( ini_get('post_max_size') ) ); ?></td>
			</tr>
			<tr>
				<td><?php _e('Max Input Nesting Level','ninja-forms' ); ?>:</td>
				<td><?php echo ini_get('max_input_nesting_level'); ?></td>
			</tr>
			<tr>
				<td><?php _e('PHP Time Limit','ninja-forms' ); ?>:</td>
				<td><?php echo ini_get('max_execution_time'); ?></td>
			</tr>
			<tr>
				<td><?php _e( 'PHP Max Input Vars','ninja-forms' ); ?>:</td>
				<td><?php echo ini_get('max_input_vars'); ?></td>
			</tr>
			<tr>
				<td><?php _e( 'SUHOSIN Installed','ninja-forms' ); ?>:</td>
				<td><?php echo extension_loaded( 'suhosin' ) ? __( 'Yes', 'ninja-forms' ) : __( 'No', 'ninja-forms' ); ?></td>
			</tr>
			<tr>
				<td><?php _e( 'SMTP','ninja-forms' ); ?>:</td>
				<td><?php echo ini_get('SMTP'); ?></td>
			</tr>
			<tr>
				<td>smtp_port:</td>
				<td><?php echo ini_get('smtp_port'); ?></td>
			</tr>
		<?php endif; ?>
		<tr>
			<td><?php _e( 'Default Timezone','ninja-forms' ); ?>:</td>
			<td><?php
				$default_timezone = date_default_timezone_get();
				if ( 'UTC' !== $default_timezone ) {
					echo sprintf( __( 'Default timezone is %s - it should be UTC', 'ninja-forms' ), $default_timezone );
				} else {
					echo sprintf( __( 'Default timezone is %s', 'ninja-forms' ), $default_timezone );
				} ?>
			</td>
		</tr>
		<?php
			$posting = array();

			// fsockopen/cURL
			$posting['fsockopen_curl']['name'] = 'fsockopen/cURL';
			if ( function_exists( 'fsockopen' ) || function_exists( 'curl_init' ) ) {
				if ( function_exists( 'fsockopen' ) && function_exists( 'curl_init' )) {
					$posting['fsockopen_curl']['note'] = __('Your server has fsockopen and cURL enabled.', 'ninja-forms' );
				} elseif ( function_exists( 'fsockopen' )) {
					$posting['fsockopen_curl']['note'] = __( 'Your server has fsockopen enabled, cURL is disabled.', 'ninja-forms' );
				} else {
					$posting['fsockopen_curl']['note'] = __( 'Your server has cURL enabled, fsockopen is disabled.', 'ninja-forms' );
				}
				$posting['fsockopen_curl']['success'] = true;
			} else {
				$posting['fsockopen_curl']['note'] = __( 'Your server does not have fsockopen or cURL enabled - PayPal IPN and other scripts which communicate with other servers will not work. Contact your hosting provider.', 'ninja-forms' );
				$posting['fsockopen_curl']['success'] = false;
			}

			// SOAP
			$posting['soap_client']['name'] = __( 'SOAP Client','ninja-forms' );
			if ( class_exists( 'SoapClient' ) ) {
				$posting['soap_client']['note'] = __('Your server has the SOAP Client class enabled.', 'ninja-forms' );
				$posting['soap_client']['success'] = true;
			} else {
				$posting['soap_client']['note'] = sprintf( __( 'Your server does not have the %sSOAP Client%s class enabled - some gateway plugins which use SOAP may not work as expected.', 'ninja-forms' ), '<a href="http://php.net/manual/en/class.soapclient.php">', '</a>' );
				$posting['soap_client']['success'] = false;
			}

			// WP Remote Post Check
			$posting['wp_remote_post']['name'] = __( 'WP Remote Post','ninja-forms');
			$request['cmd'] = '_notify-validate';
			$params = array(
				'sslverify' 	=> false,
				'timeout' 		=> 60,
				'user-agent'	=> 'Ninja Forms/' . NF_PLUGIN_VERSION,
				'body'			=> $request
			);
			$response = wp_remote_post( 'https://www.paypal.com/cgi-bin/webscr', $params );

			if ( ! is_wp_error( $response ) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 ) {
				$posting['wp_remote_post']['note'] = __('wp_remote_post() was successful - PayPal IPN is working.', 'ninja-forms' );
				$posting['wp_remote_post']['success'] = true;
			} elseif ( is_wp_error( $response ) ) {
				$posting['wp_remote_post']['note'] = __( 'wp_remote_post() failed. PayPal IPN won\'t work with your server. Contact your hosting provider. Error:', 'ninja-forms' ) . ' ' . $response->get_error_message();
				$posting['wp_remote_post']['success'] = false;
			} else {
				$posting['wp_remote_post']['note'] = __( 'wp_remote_post() failed. PayPal IPN may not work with your server.', 'ninja-forms' );
				$posting['wp_remote_post']['success'] = false;
			}
		?>
	</tbody>
	<thead>
		<tr>
			<th colspan="2"><?php _e( 'Plugins', 'ninja-forms' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><?php _e( 'Installed Plugins','ninja-forms' ); ?>:</td>
			<td><?php
				$active_plugins = (array) get_option( 'active_plugins', array() );

				if ( is_multisite() )
					$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );

				$all_plugins = array();

				foreach ( $active_plugins as $plugin ) {

					$plugin_data    = @get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
					$dirname        = dirname( $plugin );
					$version_string = '';

					if ( ! empty( $plugin_data['Name'] ) ) {

						// link the plugin name to the plugin url if available
						$plugin_name = $plugin_data['Name'];
						if ( ! empty( $plugin_data['PluginURI'] ) ) {
							$plugin_name = '<a href="' . $plugin_data['PluginURI'] . '" title="' . __( 'Visit plugin homepage' , 'ninja-forms' ) . '">' . $plugin_name . '</a>';
						}

						$all_plugins[] = $plugin_name . ' ' . __( 'by', 'ninja-forms' ) . ' ' . $plugin_data['Author'] . ' ' . __( 'version', 'ninja-forms' ) . ' ' . $plugin_data['Version'] . $version_string;

					}
				}

				if ( sizeof( $all_plugins ) == 0 )
					echo '-';
				else
					echo implode( ', <br/>', $all_plugins );

			?></td>
		</tr>
	</tbody>

</table>

<script type="text/javascript">
	/*
	@var i string default
	@var l how many repeat s
	@var s string to repeat
	@var w where s should indent
	*/
	jQuery.wc_strPad = function(i,l,s,w) {
		var o = i.toString();
		if (!s) { s = '0'; }
		while (o.length < l) {
			// empty
			if(w == 'undefined'){
				o = s + o;
			}else{
				o = o + s;
			}
		}
		return o;
	};


	jQuery('.debug-report a').click(function(){

		var paragraphContainer = jQuery( this ).parent();
		var report = "";

		jQuery('.nf-status-table thead, .nf-status-table tbody').each(function(){

			if ( jQuery( this ).is('thead') ) {

				report = report + "\n### " + jQuery.trim( jQuery( this ).text() ) + " ###\n\n";

			} else {

				jQuery('tr', jQuery( this )).each(function(){

					var the_name    = jQuery.wc_strPad( jQuery.trim( jQuery( this ).find('td:eq(0)').text() ), 25, ' ' );
					var the_value   = jQuery.trim( jQuery( this ).find('td:eq(1)').text() );
					var value_array = the_value.split( ', ' );

					if ( value_array.length > 1 ){

						// if value have a list of plugins ','
						// split to add new line
						var output = '';
						var temp_line ='';
						jQuery.each( value_array, function(key, line){
							var tab = ( key == 0 )?0:25;
							temp_line = temp_line + jQuery.wc_strPad( '', tab, ' ', 'f' ) + line +'\n';
						});

						the_value = temp_line;
					}

					report = report +''+ the_name + the_value + "\n";
				});

			}
		} );

		try {
			jQuery("#debug-report").slideDown();
			jQuery("#debug-report textarea").val( report ).focus().select();
			paragraphContainer.slideUp();
			return false;
		} catch(e){ console.log( e ); }

		return false;
	});
</script>
