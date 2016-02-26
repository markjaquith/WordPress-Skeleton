OptionsBuddy
-------------

OptionsBuddy is very lightweight object oriented framework to make it super easy to use WordPress Settings API. It does all the heavy lifting and makes the job of a developer breeze.
The whole idea is based on Fluent interface(http://en.wikipedia.org/wiki/Fluent_interface) and uses method chaining to make it very easy to use.

I developed it for personal use as I hate writing the same code again and again for using WordPress Settings API to generate my options.

OptionsBuddy can be used to create new Options Pages(check the options-buddy-example.php) or inject options to exiting pages(e.g reading/writing etc)

Example
----------

![Option Panel](https://raw.github.com/sbrajesh/options-buddy/master/screenshot.png "An example of Settings Page generated via the options-buddy-example.php")

Credits
-------- 
1. Tareq for the Sample data and form element generation callback. I liked his Settings API but it had a lot of limitations and It did not suit my requirement. So, I wrote my own new framework but reused callback methods for generating form elements(setting fields, except image field).
You can checkout his setings api here http://tareq.wedevs.com/2012/06/wordpress-settings-api-php-class
2. Matt for the html5 uploader example in WordPress  http://www.webmaster-source.com/2013/02/06/using-the-wordpress-3-5-media-uploader-in-your-plugin-or-theme/
