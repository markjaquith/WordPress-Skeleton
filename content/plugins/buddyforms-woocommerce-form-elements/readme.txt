=== BuddyForms WooCommerce Form Elements ===

Contributors: svenl77, buddyforms
Tags: buddypress, user, members, profiles, custom post types, taxonomy, frontend posting, frontend editing,
Requires at least: WordPress 3.9
Tested up to: 4.4.1
Stable tag: 1.2.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Let your WooCommerce Vendors Manage there Products from the Frontend

== Description ==

This is the BuddyForms WooCommerce Extension. Create powerful frontend management for your vendors. You need the BuddyForms plugin installed for the plugin to work. <a href="http://buddyforms.com" target="_blank">Get BuddyForms now!</a>

This plugin adds a new section to the BuddyForms Form Builder with all WooCommerce fields to create product forms to manage (create/edit) products from the frontend.

<b>WooCommerce Fields</b>

<ul>
<li>Product General Data like Product Type, Price</li>
<li>Inventory</li>
<li>Shipping</li>
<li>Linked Products</li>
<li>Attributes</li>
<li>Product Gallery</li>
</ul>

<b>Keep your User in the Frontend.</b>

Your users can become vendors and are able to manage their WooCommerce products from the front end. If you use BuddyPress, all can be integrated into the members profile with one click.


<b>Create a Marketplace.</b>

Create All Kind of marketplaces and let your user become the vendor.
like classifieds, advertisements, creative markets...


What else do I need to create a marketplace?

BuddyForms WooCommerce Form Elements is build for one purpose, to make it easy for you to manage creating and editing your WooCommerce products. This plugin is a clean, bloat free solution to front end edition of your WooCommerce products.

<strong>Features</strong>:
The plugin generates two different views.

1. For the list of vendor products
2. For the creation and edition screen.

When used with BuddyPress, the members product listing can be displayed publicly to show their products directly within their profile page.

If you wish to integrate WooCommerce with BuddyPress please use our <a href="http://buddyforms.com" title="WooCommerce BuddyPress Integration WordPress Plugin" target="_blank">WooCommerce and BuddyPress Profile synchronization plugin</a>. This plugin makes it very easy to integrate WooCommerce and other WooCommerce plugins directly within the BuddyPress profile pages.

If you need a vendor management you can use any. This is a lot of freedom for you. You can change your vendors extension if you are unhappy, but all the rest will work. We decided to leave the vendor payment management to other plugins.

There are already vendor plugins available from WooThemes and other developers.

Free Vendor Plugins
<ul>
<li><a href="https://wordpress.org/plugins/wc-vendors/" target="_blank">WP Vendors<a/></li>
</ul>

Paid Vendor Plugins
<ul>
<li><a href="http://www.woothemes.com/products/product-vendors/" target="_blank">Product Vendors<a/></li>
<li><a href="http://ignitewoo.com/woocommerce-extensions-plugins-themes/woocommerce-vendor-stores/" target="_blank">WooCommerce Vendor Stores<a/></li>
</ul>

for more information please read the documentation on How to Create a Marketplace with WordPress, WooCommerce and BuddyPress.

http://docs.buddyforms.com/article/151-create-a-social-marketplace-with-woocommerce-and-buddypress

== Documentation & Support ==

<h4>Extensive Documentation and Support</h4>

All code is clean and well documented (inline as well as in the documentation).

The BuddyForms documentation with many how-to’s is following now!

If you still get stuck somewhere, our support gets you back on the right track.
You can find all help buttons in your BuddyForms settings panel in your WP dashboard!

 == Installation ==

You can download and install BuddyForms WooCommerce Form Elements by using the built in WordPress plugin installer. If you download BuddyForms WooCommerce Form Elements manually, make sure it is uploaded to "/wp-content/plugins/".

 == Frequently Asked Questions ==

You need the BuddyForms plugin installed for the plugin to work.
<a href="http://buddyforms.com" target="_blank">Get BuddyForms now!</a>

The plugin should work with every theme. (Please let us know if you experience any issues with your theme.)


== Changelog ==

== 1.2.1 ==
WooCommerce Version 2.5.0 comes with a new function wc_help_tip. This functions was only loaded in the admin but we need it to work in the front end.

== 1.2 ==
Huge update
Merged all WoCommerce relevant form elements into one Form Element to avoid conflicts and make it more easy extendable.
Insert the class-ac-meta-box-data.php into the plugin to save the values
Remove the chipping option. Its not needed anymore

== 1.1.4 ==
add new hook bf_woocommerce_product_options_general_last to bf-wc-product-general.php
change the url to buddyforms.com
start developing variations support

== 1.1.3 ==
forgot to close a b tag

== 1.1.2 ==
<ul>
<li>Add new options to the inventory form element.</li>
<li>fixed an issue with the price field if the sales price was set to hidden.</li>
<li>removed the hide attribute from the price option. It doesn't make sense.</li>
</ul>

== 1.1.1 ==
<ul>
<li>add a new function buddyforms_woocommerce_updtae_visibility to add visibility = visible if the post status is set to published during submit.</li>
<li>fixed a bug in the taxonomies form handling if the taxonomy is used for a product attribute the post meta needs to be updated.</li>
<ul>

== 1.1 ==
<ul>
<li>Add support for WooCommerce 2.3</li>
<li>Update the form fields logic and css for WooCommerce</li>
<li>Load needed js for the fronted in WooCommerce 2.3</li>
<ul>

 == 1.0 ==
<ul>
<li>Initial release 1.0 ;)</li>
<ul>


 == Screenshots ==
