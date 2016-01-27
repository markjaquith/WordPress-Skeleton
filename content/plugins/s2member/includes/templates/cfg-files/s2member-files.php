<?php
if(!defined('WPINC')) // MUST have WordPress.
	exit('Do not access this file directly.');

global /* A Multisite ``$base`` configuration? */ $base;
$ws_plugin__s2member_temp_s_base = (!empty($base)) ? $base : c_ws_plugin__s2member_utils_urls::parse_url (network_home_url ('/'), PHP_URL_PATH);
// This works on Multisite installs too. The function ``network_home_url ()`` defaults to ``home_url ()`` on standard WordPress installs.
// Do NOT use ``site`` URL. Must use the `home` URL here, because that's what WordPress uses in its own `mod_rewrite` implementation.
?>

<IfModule env_module>
# No GZIP for script-based file downloads.
	SetEnv no-gzip 1
</IfModule>

<IfModule rewrite_module>
# Enable symlinks (required for rewrites).
	Options +FollowSymLinks

# Enable rewrite and configure base.
	RewriteEngine On
	RewriteBase <?php echo $ws_plugin__s2member_temp_s_base . "\n"; ?>

# Initialize all environment variables we're using below.
	RewriteCond %{ENV:s2member_file_download_setup} !^complete$
	RewriteRule ^(.*)$ - [E=s2member_file_download_wp_vdir:0,E=s2member_file_download:$1,E=s2member_file_stream:0,E=s2member_file_inline:0,E=s2member_file_storage:0,E=s2member_file_remote:0,E=s2member_file_ssl:0,E=s2member_file_download_key:0,E=s2member_skip_confirmation:0,E=s2member_file_download_setup:complete]

# Handle virtual directories, common on multisite networks.
	RewriteCond %{ENV:s2member_file_download_wp_vdir_check} !^complete$
	RewriteCond %{THE_REQUEST} ^(?:GET|HEAD)(?:[\ ]+)(?:<?php echo preg_quote ($ws_plugin__s2member_temp_s_base, ' '); ?>)([_0-9a-zA-Z\-]+/)(?:wp-content/)
	RewriteRule ^(.*)$ - [E=s2member_file_download_wp_vdir:,E=s2member_file_download_wp_vdir:%1,E=s2member_file_download_wp_vdir_check:complete]

# Handle streaming download requests via the rewrite engine.
	RewriteCond %{ENV:s2member_file_download} ^(.*?)(?:s2member-file-stream/)(.+)$
	RewriteRule ^(.*)$ - [N,E=s2member_file_download:,E=s2member_file_download:%1%2,E=s2member_file_stream:,E=s2member_file_stream:&s2member_file_stream=yes]

	RewriteCond %{ENV:s2member_file_download} ^(.*?)(?:s2member-file-stream-(.+?)/)(.+)$
	RewriteRule ^(.*)$ - [N,E=s2member_file_download:,E=s2member_file_download:%1%3,E=s2member_file_stream:,E=s2member_file_stream:&s2member_file_stream=%2]

# Handle inline file requests via the rewrite engine.
	RewriteCond %{ENV:s2member_file_download} ^(.*?)(?:s2member-file-inline/)(.+)$
	RewriteRule ^(.*)$ - [N,E=s2member_file_download:,E=s2member_file_download:%1%2,E=s2member_file_inline:,E=s2member_file_inline:&s2member_file_inline=yes]

	RewriteCond %{ENV:s2member_file_download} ^(.*?)(?:s2member-file-inline-(.+?)/)(.+)$
	RewriteRule ^(.*)$ - [N,E=s2member_file_download:,E=s2member_file_download:%1%3,E=s2member_file_inline:,E=s2member_file_inline:&s2member_file_inline=%2]

# Handle storage specifications via the rewrite engine.
	RewriteCond %{ENV:s2member_file_download} ^(.*?)(?:s2member-file-storage-(.+?)/)(.+)$
	RewriteRule ^(.*)$ - [N,E=s2member_file_download:,E=s2member_file_download:%1%3,E=s2member_file_storage:,E=s2member_file_storage:&s2member_file_storage=%2]

# Handle remote authorization requests via the rewrite engine.
	RewriteCond %{ENV:s2member_file_download} ^(.*?)(?:s2member-file-remote/)(.+)$
	RewriteRule ^(.*)$ - [N,E=s2member_file_download:,E=s2member_file_download:%1%2,E=s2member_file_remote:,E=s2member_file_remote:&s2member_file_remote=yes]

	RewriteCond %{ENV:s2member_file_download} ^(.*?)(?:s2member-file-remote-(.+?)/)(.+)$
	RewriteRule ^(.*)$ - [N,E=s2member_file_download:,E=s2member_file_download:%1%3,E=s2member_file_remote:,E=s2member_file_remote:&s2member_file_remote=%2]

# Handle SSL file requests via the rewrite engine.
	RewriteCond %{ENV:s2member_file_download} ^(.*?)(?:s2member-file-ssl/)(.+)$
	RewriteRule ^(.*)$ - [N,E=s2member_file_download:,E=s2member_file_download:%1%2,E=s2member_file_ssl:,E=s2member_file_ssl:&s2member_file_ssl=yes]

	RewriteCond %{ENV:s2member_file_download} ^(.*?)(?:s2member-file-ssl-(.+?)/)(.+)$
	RewriteRule ^(.*)$ - [N,E=s2member_file_download:,E=s2member_file_download:%1%3,E=s2member_file_ssl:,E=s2member_file_ssl:&s2member_file_ssl=%2]

# Handle file download keys via the rewrite engine.
	RewriteCond %{ENV:s2member_file_download} ^(.*?)(?:s2member-file-download-key-(.+?)/)(.+)$
	RewriteRule ^(.*)$ - [N,E=s2member_file_download:,E=s2member_file_download:%1%3,E=s2member_file_download_key:,E=s2member_file_download_key:&s2member_file_download_key=%2]

# Handle confirmations having beek skipped via the rewrite engine.
	RewriteCond %{ENV:s2member_file_download} ^(.*?)(?:s2member-skip-confirmation/)(.+)$
	RewriteRule ^(.*)$ - [N,E=s2member_file_download:,E=s2member_file_download:%1%2,E=s2member_skip_confirmation:,E=s2member_skip_confirmation:&s2member_skip_confirmation=yes]

	RewriteCond %{ENV:s2member_file_download} ^(.*?)(?:s2member-skip-confirmation-(.+?)/)(.+)$
	RewriteRule ^(.*)$ - [N,E=s2member_file_download:,E=s2member_file_download:%1%3,E=s2member_skip_confirmation:,E=s2member_skip_confirmation:&s2member_skip_confirmation=%2]

# Cleanup variables not used in this request. Looking for `0` values.
	RewriteCond %{ENV:s2member_file_download_wp_vdir} ^0$
	RewriteRule ^(.*)$ - [E=s2member_file_download_wp_vdir:]

	RewriteCond %{ENV:s2member_file_stream} ^0$
	RewriteRule ^(.*)$ - [E=s2member_file_stream:]

	RewriteCond %{ENV:s2member_file_inline} ^0$
	RewriteRule ^(.*)$ - [E=s2member_file_inline:]

	RewriteCond %{ENV:s2member_file_storage} ^0$
	RewriteRule ^(.*)$ - [E=s2member_file_storage:]

	RewriteCond %{ENV:s2member_file_remote} ^0$
	RewriteRule ^(.*)$ - [E=s2member_file_remote:]

	RewriteCond %{ENV:s2member_file_ssl} ^0$
	RewriteRule ^(.*)$ - [E=s2member_file_ssl:]

	RewriteCond %{ENV:s2member_file_download_key} ^0$
	RewriteRule ^(.*)$ - [E=s2member_file_download_key:]

	RewriteCond %{ENV:s2member_skip_confirmation} ^0$
	RewriteRule ^(.*)$ - [E=s2member_skip_confirmation:]

# Put everything together now and process the internal rewrite.
	RewriteRule ^(.*)$ %{ENV:s2member_file_download_wp_vdir}?s2member_file_download=%{ENV:s2member_file_download}%{ENV:s2member_file_stream}%{ENV:s2member_file_inline}%{ENV:s2member_file_storage}%{ENV:s2member_file_remote}%{ENV:s2member_file_ssl}%{ENV:s2member_file_download_key}%{ENV:s2member_skip_confirmation} [QSA,L]
</IfModule>

<IfModule !rewrite_module>
	<IfModule authz_core_module>
		Require all denied
	</IfModule>
	<IfModule !authz_core_module>
		deny from all
	</IfModule>
</IfModule>

<?php unset($ws_plugin__s2member_temp_s_base); ?>
