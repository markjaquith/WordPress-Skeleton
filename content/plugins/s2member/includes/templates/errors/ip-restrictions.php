<?php
if(!defined('WPINC')) // MUST have WordPress.
	 exit("Do not access this file directly.");
?>

<!DOCTYPE html>
<html>
<head>
	 <meta charset="UTF-8">
	 <title><?php echo _x('503: Service Temporarily Unavailable', "s2member-front", "s2member"); ?></title>
</head>
<body>
<?php echo _x('<strong>503: Service Temporarily Unavailable</strong><br />Too many IP addresses accessing one secure area<em>!</em><br />Please contact Support if you need assistance.', "s2member-front", "s2member"); ?>
</body>
</html>
