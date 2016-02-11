WP Simple Settings Framework
================================

A minimalistic framework for Wordpress Settings API.

Quick start
------------

* [Download the latest release](https://github.com/Geczy/WP-Simple-Settings-Framework/zipball/master) (zip)

* Or, clone the repo, `git clone git://github.com/Geczy/WP-Simple-Settings-Framework.git`

Installation
------------
1. Include the framework in your Wordpress plugin by using:

	```php
	<?php
	add_action( 'init', 'sf_load_settings' );
	function sf_load_settings() {
		require 'classes/sf-class-settings.php';
		$settings_framework = new SF_Settings_API($id = 'my_plugin_name', $title = 'My Plugin Title', $menu = 'plugins.php', __FILE__);
	}
	```

	Optionally, you might want to make `$settings_framework` a global variable so that you can use the [helper functions](#helpers).

2. Open `sf-options.php` to begin configuring your options.

Features
------------

### Automatic settings page
Don't want it under the Plugins tab like in the screenshot? No problem, you can choose where you want it!

You can also change "Simple Settings" submenu to be anything you'd like.

![settings page example](http://i.imgur.com/aEGUD.png)

---

### Tooltips
![tooltips example](http://i.imgur.com/Z3Pnk.png)

Optional tooltips using [Twitter Bootstrap](http://twitter.github.com/bootstrap/javascript.html#tooltips)!

---

### Select box replacement
![select box replacement](http://i.imgur.com/ikOXH.png)

Utilizing [Select2](http://ivaynberg.github.com/select2/) to display select boxes. It's pretty cool!

---

### Multiple tabs
![multiple tabs example](http://i.imgur.com/OUM4i.png)

Create multiple tabs for your options.

---

### Input types

* Text
* Number
* Textarea
* Checkbox
* Radio
* Select
* WP Pages

Helpers
------------

Update or add a new option

```php
<?php
$settings_framework->update_option('your_option', 'new_value');
```

Get an existing option's value

```php
<?php
$settings_framework->get_option('your_option');
```

Example configuration
------------

Check out the [example config](https://github.com/Geczy/WP-Simple-Settings-Framework/blob/master/sf-options.php) for an idea of how to use every input type.

Here's an example of one type, though:

```php
<?php
$options[] = array(
	'name' => __( 'Name', 'geczy' ),
	'desc' => __( 'Please tell me who you are.', 'geczy' ),
	'id'   => 'text_sample',
	'type' => 'text',
);
```


Bug tracker
-----------

Have a bug? Please create an issue here on GitHub!

https://github.com/Geczy/WP-Simple-Settings-Framework/issues/

Copyright and License
---------------------

Copyright 2012 Matthew Gates

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

* Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
* Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
* Neither the names of the copyright holders nor the names of the contributors may be used to endorse or promote products derived from this software without specific prior written permission.

http://www.opensource.org/licenses/bsd-license.php
