<?php
if(!defined('WPINC')) // MUST have WordPress.
	exit('Do not access this file directly.');
?>

<IfModule authz_core_module>
	Require all denied
</IfModule>
<IfModule !authz_core_module>
	deny from all
</IfModule>
