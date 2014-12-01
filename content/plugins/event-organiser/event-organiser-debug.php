<?php
/****** DEBUG PAGE ******/
if ( !class_exists( 'EventOrganiser_Admin_Page' ) ){
    require_once( EVENT_ORGANISER_DIR.'classes/class-eventorganiser-admin-page.php' );
}
/**
 * @ignore
 */
class EventOrganiser_Debug_Page extends EventOrganiser_Admin_Page
{
	function set_constants(){
		$this->hook = 'edit.php?post_type=event';
		$this->title = __( 'System Info', 'eventorganiser' );
		$this->menu = __( 'System Info', 'eventorganiser' );
		$this->permissions = 'manage_options';
		$this->slug = 'debug';
	}

	function add_page(){		
		$this->page = add_submenu_page($this->hook,$this->title, $this->menu, $this->permissions,$this->slug,  array($this,'render_page'),10);
		add_action('load-' . $this->page,  array($this,'page_actions'),9);
		add_action('admin_print_scripts-' . $this->page,  array($this,'page_styles'),10);
		add_action('admin_print_styles-' . $this->page,  array($this,'page_scripts'),10);
		add_action("admin_footer-" . $this->page, array($this,'footer_scripts') );
		if( !defined( "WP_DEBUG" ) || !WP_DEBUG ){
			remove_submenu_page('edit.php?post_type=event',$this->slug);
		}
	}
	
	function page_actions(){
		
		
		$eo_debugger = new EventOrganiser_Debugger();
		$eo_debugger->set_prequiste( 'WordPress', '3.3', '4.0');
		//$eo_debugger->set_known_plugin_conflicts();
		//$eo_debugger->set_known_theme_conflicts();
		$eo_debugger->set_db_tables( 'eo_events', 'eo_venuemeta' );
		
		do_action_ref_array( 'eventorganiser_debugger_setup', array( &$eo_debugger ) );
		
		$this->debugger = $eo_debugger;
		
		if( !empty( $_GET['data'] ) && !empty( $_GET['data']['jqueryv'] ) ){
			$eo_debugger->set_jquery_version( $_GET['data']['jqueryv'] );
		}
		
		if( !empty( $_GET['eo-download-debug-file'] ) && current_user_can( 'manage_options' ) && wp_verify_nonce( $_GET['eo-download-debug-file'], 'eo-download-debug-file' ) ){
			$this->debugger->download_debug_info();
		}
		
		wp_enqueue_style('eventorganiser-style' );
	}

	function display(){
	?>
	<div class="wrap">
		<?php screen_icon( 'edit' );?>
		
		<h2><?php _e('System Info','eventorganiser');?> </h2>
		
		<?php  $eo_debugger = $this->debugger; ?>
		
		<p>
		<?php 
		_e( "This page highlights useful information for debugging. If you're reporting a bug, please include this information.", 'eventorganiser' );
		echo " ";
		_e( "The 'system info' link in under the Events admin tab is only visible to admins and only when <code>WP_DEBUG</code> is set to <code>true</code>.", 'eventorganiser' );
		?>
		</p>
		<p class="description">
		<?php 
		_e( "Most bugs arise from theme or plug-in conflicts. You can check this by disabling all other plug-ins and switching to TwentyTweleve.", 'eventorganiser' );
		echo " ";
		_e( "To help speed things along, if you report a bug please indicate if you have done so. Once the plug-in or theme has been identified it is often easy to resolve the issue.", 'eventorganiser' );
		echo " ";
		_e( "Below any <strong>known</strong> plug-in and theme conflicts are highlighted in red.", 'eventorganiser' );
		?>
		</p>
		
		<?php 
			printf( 
				'<p><a href="%s" data-eo-debug="downloadurl" class="button secondary">%s</a></p>',
				add_query_arg( 'eo-download-debug-file', wp_create_nonce( 'eo-download-debug-file' ) ),
				__( "Download system information file", 'eventorganiser' )
			);
		?>
				
		<table class="widefat">
				<tr>
					<th> Site url </th>
					<td><?php echo site_url(); ?></td>
				</tr>
				<tr>
					<th> Home url </th>
					<td><?php echo home_url(); ?></td>
				</tr>
				<tr>
					<th> Multisite </th>
					<td><?php echo is_multisite() ? 'Yes' : 'No' ?></td>
				</tr>
				
				<tr>
					<th> Permalink </th>
					<td>
					<?php echo get_option( 'permalink_structure' );?>
					</td>
				</tr>
				<tr>
					<th> Event Organsier version </th>
					<td><?php echo EVENT_ORGANISER_VER; ?></td>
				</tr>
				<tr>
					<th> WordPress </th>
					<td>
					<?php $eo_debugger->verbose_prequiste_check( 'WordPress', get_bloginfo( 'version' ) );?>
					</td>
				</tr>
				
				<tr>
					<th> jQuery Version </th>
					<td>
						<span data-eo-debug="jqueryversion"></span>
					</td>
				</tr>
				<script>
				jQuery(document).ready( function($) {
					var eventorganiser = eventorganiser || {}; 
					eventorganiser.add_query_arg = function( key, value, uri ) {
						  var re = new RegExp("([?|&])" + key + "=.*?(&|$)", "i");
						  separator = uri.indexOf('?') !== -1 ? "&" : "?";
						  if (uri.match(re)) {
						    return uri.replace(re, '$1' + key + "=" + value + '$2');
						  }
						  else {
						    return uri + separator + key + "=" + value;
						  }
						}
					
				    $('[data-eo-debug="jqueryversion"]').each(function() {
						
						var version	= $().jquery;
						$(this).text( version);

						$('[data-eo-debug="downloadurl"]').each(function(){
							var new_url = eventorganiser.add_query_arg( 'data[jqueryv]', version, $(this).attr('href') );
							$(this).attr('href',new_url);
						}); 
				    });
				});
				</script>
				
				<tr>
					<th> PHP Version </th>
					<td> <?php echo PHP_VERSION; ?></td>
				</tr>
				<tr>
					<th> MySQL Version </th>
					<td> 
					<?php
						global $wpdb;
						echo empty( $wpdb->use_mysqli ) ? mysql_get_server_info() : mysqli_get_server_info( $wpdb->dbh );
					?></td>
				</tr>    
				<tr>
					<th> Web Server </th>
					<td> <?php echo $_SERVER['SERVER_SOFTWARE']; ?></td>
				</tr>      
				<tr>
					<th> PHP Memory Usage </th>
					<th><?php echo $eo_debugger->verbose_memory_check(); ?>
				</tr>   
				<tr>
					<th> PHP Post Max Size </th>
					<td><?php echo ini_get('post_max_size'); ?></td>
				</tr>   
				<tr>
					<th> PHP Upload Max Size </th>
					<td><?php echo ini_get('upload_max_filesize'); ?></td>
				</tr>
				<tr>
					<th> PHP FSOCKOPEN Support </th>
					<td>  <?php echo (function_exists('fsockopen')) ? _e('Yes', 'eventorganiser') . "\n" : _e('No', 'eventorganiser') . "\n"; ?></td>
				</tr>
				<tr>
					<th> PHP cURL Support </th>
					<td>  <?php echo (function_exists('curl_init')) ? _e('Yes', 'eventorganiser') . "\n" : _e('No', 'eventorganiser') . "\n"; ?></td>
				</tr>
				<tr>
					<th> OpenSSL Support </th>
					<td>  <?php echo (function_exists('openssl_verify')) ? _e('Yes', 'eventorganiser') . "\n" : _e('No', 'eventorganiser') . "\n"; ?></td>
				</tr>
				<tr>
					<th> Plug-ins </th>
					<td>
					<?php $eo_debugger->verbose_plugin_check();?>
					</td>
				</tr>
				<tr>
					<th> Theme </th>
					<td>
					<?php $eo_debugger->verbose_theme_check();?>
					</td>
				</tr>
				<tr>
					<th> Databse Prefix: </th>
					<td><?php global $wpdb; echo $wpdb->prefix; ?></td>
				</tr>
				<tr>
					<th> Database tables </th>
					<td>
					<?php $eo_debugger->verbose_database_check();?>
					</td>
				</tr>
				<tr>
					<th> Database character set </th>
					<td>
					<?php $eo_debugger->verbose_database_charset_check();?>
					</td>
				</tr>
				<tr>
					<th> Debug mode </th>
					<td><?php echo defined( 'WP_DEBUG' ) && WP_DEBUG ? 'Enabled' : 'Disabled'; ?></td>
				</tr>
				<tr>
					<th> WP Cron </th>
					<td>
						<?php 
							if( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON )
								echo 'Disabled';
							elseif( defined( 'ALTERNATE_WP_CRON' ) && ALTERNATE_WP_CRON )
								echo 'Alternative';
    						else
								echo 'Enabled';
						?>
					</td>
				</tr>
				<tr>
					<th> Timezone </th>
					<td><?php echo eo_get_blog_timezone()->getName(); printf( ' ( %s / %s ) ', get_option( 'gmt_offset' ), get_option( 'timezone_string' ) )?></td>
				</tr>
				<tr>
					<th> Language </th>
					<td><?php echo defined( 'WP_LANG' ) && WP_LANG ? WP_LANG : 'en_us'; ?></td>
				</tr>
				<tr>
					<th> Date Format </th>
					<td><?php echo get_option( 'date_format' ); ?></td>
				</tr>
				<tr>
					<th> Time Format </th>
					<td><?php echo get_option( 'time_format' ); ?></td>
				</tr>
		</table>
		<?php 
			printf( 
				'<p class="description"> <span class="eo-debug-warning">*</span> %s </p>',
				__( 'Known plug-in & theme conflicts, highlighted in red, may be minor or have a simple resolution. Please contact support.', 'eventorganiser' )
			);
		?>
	</div><!--End .wrap -->
    <?php
	}

}
$venues_page = new EventOrganiser_Debug_Page();

class EventOrganiser_Debugger{

	var $prequiste = array();
	var $plugins = array();
	var $themes = array();
	var $db_tables = array();
	var $plugin = 'Event Organiser';

	var $ok_class = 'eo-debug-ok';
	var $warning_class = 'eo-debug-warning';
	var $alert_class = 'eo-debug-alert';
	
	var $jquery_version = false;
	
	function set_plugin( $plugin ){
		$this->plugin = $plugin;
	}
	
	function set_prequiste( $requirement, $min = false, $max = false ){
		$this->prequiste[$requirement] = compact( 'min', 'max' );
	}

	function set_known_plugin_conflicts( ){
		$args = func_get_args();
		if( $args ){
			$args = array_map( 'strtolower', $args );
			$this->plugins = array_merge( $this->plugins, $args );
		}
	}

	function set_known_theme_conflicts( ){
		$args = func_get_args();
		if( $args ){
			$args = array_map( 'strtolower', $args );
			$this->themes = array_merge( $this->themes,  $args );
		}
	}

	function set_db_tables( ){
		$args = func_get_args();
		$this->db_tables = array_merge( $this->db_tables, $args );
	}

	function set_jquery_version( $v ){
		$this->jquery_version = $v;
	}
	
	function get_memory_usage( $context = false ){
		$memory_usage =  round( memory_get_usage() / 1024 / 1024, 2);
		if( 'percent' == $context )
			return $percentage = round( $memory_usage / ini_get( 'memory_limit' ) * 100, 0 );
			
		return $memory_usage;
	}
	
	function get_cron_status(){
		if( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON )
			return 0;
		elseif( defined( 'ALTERNATE_WP_CRON' ) && ALTERNATE_WP_CRON )
			return 2;
		else
			return 1;
	}
	
	function check_prequiste( $requirement, $v ){
		if( !isset( $this->prequiste[$requirement] ) )
			return 1;

		$versions = $this->prequiste[$requirement];

		if( $versions['min'] && version_compare( $versions['min'], $v ) > 0 )
			return -1;

		if( $versions['max'] && version_compare( $versions['max'], $v ) < 0 )
			return 0;

		return 1;
	}

	function verbose_plugin_check(){
		$installed = get_plugins();

		foreach( $installed as $plugin_slug => $plugin_data ){
			if( ! is_plugin_active( $plugin_slug ) )
				continue;
				
			$class = in_array( strtolower( $plugin_slug ), $this->plugins ) ? $this->warning_class : '';
				
			printf(
			' <span class="%s"> %s %s </span> </br>',
			esc_attr( $class ),
			$plugin_data['Name'],
			$plugin_data['Version']
			);
		}
	}

	function verbose_theme_check(){
		if( version_compare( '3.4', get_bloginfo( 'version' ) ) <= 0 ){
			$theme = wp_get_theme();
			$class = in_array( strtolower( $theme->stylesheet ), $this->themes ) ? $this->warning_class : '';
			printf(
			' <span class="%s"> %s %s </span> </br>  %s',
				esc_attr( $class ),
				$theme->get('Name'),
				$theme->get('Version'),
				$theme->get('ThemeURI')
			);
		}else{
			$theme_name = get_current_theme();
			$class = in_array( strtolower( $theme_name ), $this->themes ) ? $this->warning_class : '';
			printf(
				' <span class="%s"> %s </span> </br>',
				esc_attr( $class ),
				$theme_name
			);
		}
	}


	function verbose_database_check(){
		if( $this->db_tables ){
			foreach( $this->db_tables as $db_table ){
				$class = $this->table_exists( $db_table ) ? $this->ok_class : $this->warning_class;
				printf( '<span class="%s"> %s </span></br>', esc_attr( $class ), esc_attr( $db_table ) );
			}
		}
	}

	function table_exists( $table ){
		global $wpdb;
		return $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->prefix.$table ) ) == $wpdb->prefix.$table;
	}

	function database_charset_check(){
		global $wpdb;
		return $wpdb->query( $wpdb->prepare( 'SHOW CHARACTER SET WHERE LOWER( Charset ) = LOWER( %s )', DB_CHARSET ) );
	}
	
	function verbose_database_charset_check(){
		global $wpdb;

		if( $this->database_charset_check()  )
			$class = '';
		else
			$class = $this->warning_class;

		printf( '<span class="%s"> %s </span></br>', esc_attr( $class ), esc_attr( DB_CHARSET ) );
	}

	function verbose_prequiste_check( $requirement, $v ){

		$versions = $this->prequiste[$requirement];

		if( 1 == $this->check_prequiste( $requirement, $v ) ){
			printf( '<span class="%s">%s</span>', esc_attr( $this->ok_class ), $v );
		}elseif( 0 == $this->check_prequiste( $requirement, $v ) ){
			printf(
			'<span class="%s">%s</span>. %s',
			esc_attr( $this->alert_class ),
			$v,
			sprintf(
				/// TRANSLATORS: [this plugin] has only been tested up to [required plugin] [required plugin version]
				__( '%s has only been tested up to %s %s', 'eventorganiser' ), $this->plugin, $requirement, $versions['max'] )
			);
		}elseif( -1 == $this->check_prequiste( $requirement, $v ) ){
			printf(
			'<span class="%s">%s</span>. %s',
			esc_attr( $this->warning_class ),
			$v,
			sprintf(
				/// TRANSLATORS: [this plugin] requires [required plugin] version [required plugin version] or higher
			 	__( '%s requires %s version %s or higher', 'eventorganiser' ), $this->plugin, $requirement, $versions['min'] )
			);
		}
	}

	
	function verbose_memory_check(){

		if( function_exists( 'memory_get_usage' ) ){
			$memory_usage =  round( memory_get_usage() / 1024 / 1024, 2);
			$percentage = round( $memory_usage / ini_get( 'memory_limit' ) * 100, 0 );
			printf( '%d / %d   <span class="%s">( %s )</span>',
				ceil( $memory_usage ),
				ini_get( 'memory_limit' ),
				$percentage > 90 ? $this->alert_class : $this->ok_class,
				$percentage . "%"
			);
		}else{
			printf( ' ? / %d  <span class="%s">( %s )</span>',
				ini_get( 'memory_limit' ),
				$this->alert_class,
				__( 'unknown', 'eventorganiser' )
			);
		}

	}
	
	public function download_debug_info(){
	
		global $wpdb;
		$installed = get_plugins();
		$active_plugins = array();
		foreach( $installed as $plugin_slug => $plugin_data ){
			if( ! is_plugin_active( $plugin_slug ) )
				continue;
	
			$active_plugins[] = $plugin_slug;
		};
	
		$theme = wp_get_theme();
	
		$db_tables = array();
		if( $this->db_tables ){
			foreach( $this->db_tables as $db_table ){
	
				if( $this->table_exists( $db_table ) ){
					$db_table = "**".$db_table."**";
				}
				$db_tables[] = $db_table;
			}
		}
		
		$options = array();
		$options['event-organiser'] = eventorganiser_get_option( false );
		
		/**
		 * Settings to include in an export.
		 * 
		 * These options are included in both the settings export on the settings
		 * page and the also printed in the system information file. By default
		 * they inlude Event Organiser's options, but others can be added.
		 * 
		 * The filtered value should be a (2+ dimensional) array, indexed by plugin/
		 * extension name.
		 * 
		 * @param array $options Array of user settings, indexed by plug-in/extension.
		 */
		$options = apply_filters( 'eventorganiser_export_settings', $options );
	
		$filename = 'event-organiser-system-info-'.get_bloginfo( 'name' ).'.md';
		$filename = sanitize_file_name( $filename );

		header( "Content-type: text/plain" );
		header('Content-disposition: attachment; filename=' . $filename );
	
		echo '## Event Organiser Sytem Informaton ##' . "\n";
		echo "\n";
		echo "\n";
	
		echo '### Site Information ###' . "\n";
		echo "\n";
		echo 'Site url' . "\t\t\t".site_url()."\n";
		echo 'Home url' . "\t\t\t".home_url()."\n";
		echo 'Multisite' ."\t\t\t".( is_multisite() ? 'Yes' : 'No' )."\n";
		echo 'Permalink' . "\t\t\t" . get_option( 'permalink_structure' ) . "\n";
	
		echo "\n";
		echo "\n";
		echo '### Versions ###' . "\n";
		echo "\n";
		
		echo 'Event Organiser' . "\t\t" . EVENT_ORGANISER_VER . "\n";
		if( $this->jquery_version )
			echo 'jQuery Version' . "\t\t" . $this->jquery_version . "\n";
		echo 'WordPress' . "\t\t\t" . get_bloginfo( 'version' ) ."\n";
		echo 'PHP Version' . "\t\t\t" . PHP_VERSION ."\n";
		global $wpdb;
		$ver = empty( $wpdb->use_mysqli ) ? mysql_get_server_info() : mysqli_get_server_info( $wpdb->dbh );
		echo 'MySQL Version' . "\t\t" . $ver ."\n";
	
		echo "\n";
		echo "\n";
		echo '### Server Information ###' . "\n";
		echo "\n";
		echo 'Web Server' ."\t\t\t" . $_SERVER['SERVER_SOFTWARE'] ."\n";
		echo 'PHP Memory Usage' ."\t" . $this->get_memory_usage( 'percent' ) . '%' ."\n";
		echo 'PHP Memory Limit' ."\t" . ini_get( 'memory_limit' ) ."\n";
		echo 'PHP Upload Max Size' ."\t" . ini_get('post_max_size') ."\n";
		echo 'PHP FSOCKOPEN support' ."\t" . ( function_exists('fsockopen')  ? 'Yes' : 'No' ) ."\n";
		echo 'PHP cURL support' ."\t" . ( function_exists('curl_init')  ? 'Yes' : 'No' ) ."\n";
		echo 'PHP openSSL support' ."\t" . ( function_exists('openssl_verify')  ? 'Yes' : 'No' ) ."\n";
			
		echo "\n";
		echo "\n";
		echo '### Plug-ins & Themes ###' . "\n";
		echo "\n";
		echo 'Active Plug-ins' ."\n\t-\t" . implode( "\n\t-\t", $active_plugins ) . "\n";
		echo 'Theme' . "\n\t-\t" . $theme->get('Name').' ('.$theme->get('Version').')' . "\n";
	
		echo "\n";
		echo "\n";
		echo '### Database ###' . "\n";
		echo "\n";
		echo "Database Prefix"."\t\t\t" . $wpdb->prefix . "\n";
		echo "Database tables"."\t\t\t" . implode( ', ', $db_tables ). "\n";
		echo "Database character set"."\t" . ( $this->database_charset_check() ? DB_CHARSET : "**".DB_CHARSET."**" ) . "\n";
	
		echo "\n";
		echo "\n";
		echo '### Site Settings ###' . "\n";
		echo 'Timezone' . "\t\t\t" . eo_get_blog_timezone()->getName() . sprintf( ' ( %s / %s ) ', get_option( 'gmt_offset' ), get_option( 'timezone_string' ) ) . "\n";
		echo 'WP Cron' . "\t\t\t" . ( $this->get_cron_status() == 0 ? 'Disabled' :  ( $this->get_cron_status() == 1 ? 'Enabled' : 'Alternative ') ) . "\n";
		echo 'WP Lang' . "\t\t\t" . ( defined( 'WP_LANG' ) && WP_LANG ? WP_LANG : 'en_us' ) . "\n";
		echo 'Date format' . "\t\t" . get_option( 'date_format' ) . "\n";
		echo 'Time format' . "\t\t" . get_option( 'time_format' );
							
		echo "\n";
		echo "\n";
		echo '### Event Organiser Settings ###' . "\n";
		foreach( $options['event-organiser'] as $option => $value ){
			if( is_array( $value ) )
				$value = implode( ', ', $value );
			echo "\n\t-\t**".esc_html( $option ).":**\t " . $value; 
		}
		
		echo "\n";
		echo "\n";
		echo '### Debug Mode ###' . "\n";
		echo "\n";
		echo 'Debug mode' . "\t\t\t" . ( defined( 'WP_DEBUG' ) && WP_DEBUG ? 'Enabled' : 'Disabled' ) . "\n";
		echo 'Script mode' . "\t\t\t" . ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? 'Enabled' : 'Disabled' ) . "\n";
		
		exit();
	}
}
