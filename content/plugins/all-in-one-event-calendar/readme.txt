=== All-in-One Event Calendar ===
Contributors: hubrik, vtowel, yani.iliev, nicolapeluchetti, jbutkus, lpawlik, bangelov
Tags: calendar, events, ics, ics feed, wordpress ical importer, google
calendar, ical, iCalendar, all-in-one, events sync, events widget,
calendar widget
Requires WordPress at least: 3.5
Tested up to: 4.0.1
Stable tag: 2.1.8
License: GNU General Public License, version 3 (GPL-3.0)

A calendar system with many views, upcoming events widget, color-coded
categories, recurrence, and import/export of facebook events and .ics
feeds.

== Description ==

Welcome to the [All-in-One Event Calendar Plugin](http://time.ly/),
from [Timely](http://time.ly/). The All-in-One Event Calendar is a
beautiful way to list your events in WordPress and easily share them
with the rest of the world.

Our calendar system combines a clean visual design, solid
architectural patterns and a powerful set of features to create the
most advanced calendar system available for WordPress.

Download the free Core edition at [time.ly](http://time.ly/) and
choose from 3 custom designed themes for your Calendar, or develop
your own! Additionally, you can install add-ons that give you
Posterboard view, Facebook integration, inline Calendar Theme editing
and more.

= New in version 2.0 =

* Made plugin modular, allowing users to install extensions for
required features.
* Improved performance by delaying resources initialization until
needed.
* Implemented new theme layer using Twig
(http://twig.sensiolabs.org/), which will allow the Calendar to render
new views in JavaScript.
* Created time manipulation layer, which will allow editing of
event's timezone, and also leverage system ability to track timezone
definition changes when processing, or rendering of time entity comes
into action.
* Timezone information is stored with the event during event creation
and/or import ensuring event is treated with respect to it's original
timezone.
* Implemented soft-deactivation feature to help prevent fatal errors
which notifies user if an error occurs and makes plugin behave as if
it was deactivated.
* Allowing smooth interoperability with 3rd party cache plugins
primarily by not providing another cache layer which would cripple
behaviour of former ones.
* Added thin compatibility layer which supports up-to date
PHP/WordPress versions and provides support for older releases.
* Created calendar feeds extension layer, which is meant to allow
adding new types of feeds in addition to already existing ICS.
* Created configuration abstraction layer, which is safe to use with
3rd party caching plugins.
* Fixed repeated cron additions, which was causing intensive database
writes in some configurations.
* Extended meta-data management layer to save from unnecessary calls
to underlying database.
* Implemented robust and extensible filtering layer allowing to create
new filter types on demand.
* Improved internationalization support with respect to WordPress and
3rd party plugins behaviour.
* Updated to Bootstrap 3 for better performance and responsiveness.
* Moved Front End Event Submission, Superwidget, Platform Mode,
Posterboard, Stream View, Agenda View, Facebook and Twitter to
extensions. Please view their release notes for details.
* Fixed DST issue causing times to shift one hour.
* Clicking the browser back button returns site visitors to view they
were on.
* Fixed issue where the widget would not display a full day's events.
* Fully hiding password protected events internal description.
* Added improved database migration method which should allow for safe
database schema changes.
* Improved error messages rendering to include more details for
tracing.

= Calendar Features For Users =

This plugin has many features we hope will prove useful to users,
including:

* **Recurring** events.
* **Filtering** by event category or tag.
* Easy **sharing** with Google Calendar, Apple iCal, MS Outlook and
any other system that accepts iCalendar (.ics) feeds.
* Embedded **Google Maps**.
* **Color-coded** events based on category.
* Featured **event images and category images**.
* **Month**, **week**, **day**, **agenda**, and **posterboard**
**views.
* **Upcoming Events** widget.
* Direct links to **filtered calendar views**.
* **Facebook** integration.
* Inline **Calendar Theme editor**.

= Features for Website and Blog Owners =

* Import other calendars automatically to display in your calendar.
* Categorize and tag imported calendar feeds automatically.
* Events from [The Events
Calendar](http://wordpress.org/extend/plugins/the-events-calendar/).
* Plugin can also be easily imported.

Importing and exporting iCalendar (.ics) feeds is one of the strongest
features of the All-in-One Event Calendar system. Enter an event on
one site and you can have it appear automatically in another website's
calendar. You can even send events from a specific category or tag (or
combination of categories and tags).

Why is this cool? It allows event creators to create one event and
have it displayed on a few or thousands of calendars with no extra
work. And it also allows calendar owners to populate their calendar
from other calendar feeds without having to go through the hassle of
creating new events. For example, a soccer league can send its game
schedule to a community sports calendar, which, in turn, can send only
featured games (from all the sports leagues it aggregates) to a
community calendar, which features sports as just one category.

= Additional Features =

The All-in-One Event Calendar Plugin also has a few features that will
prove useful for website and blog owners:

* Each event is SEO-optimized.
* Each event links to the original calendar.
* Your calendar can be embedded into a WordPress page without needing
to create template files or modify the theme.

= Video =

http://vimeo.com/55904173

= Helpful Links =

* [**Get help from our Support Site »**](http://support.time.ly).
* [**Check out our great community forum »**](http://community.time.ly).

== Frequently Asked Questions ==

[**Learn more with our detailed documentation »**](http://time.ly/support/)

= Shortcodes =

* Monthly view: **[ai1ec view="monthly"]**
* Weekly view: **[ai1ec view="weekly"]**
* Agenda view: **[ai1ec view="agenda"]**
* Posterboard view: **[ai1ec view="posterboard"]**
* Default view as per settings: **[ai1ec]**

* Filter by event category name: **[ai1ec cat_name="Holidays"]**
* Filter by event category names (separate names by comma):
**[ai1ec cat_name="Lunar Cycles,zodia-date-ranges"]**
* Filter by event category id: **[ai1ec cat_id="1"]**
* Filter by event category ids (separate IDs by comma):
**[ai1ec cat_id="1, 2"]**

* Filter by event tag name: **[ai1ec tag_name="tips-and-tricks"]**
* Filter by event tag names (separate names by comma):
**[ai1ec tag_name="creative writing,performing arts"]**
* Filter by event tag id: **[ai1ec tag_id="1"]**
* Filter by event tag ids (separate IDs by comma):
**[ai1ec tag_id="1, 2"]**

* Filter by post id: **[ai1ec post_id="1"]**
* Filter by post ids (separate IDs by comma):
**[ai1ec post_id="1, 2"]**

== Changelog ==

= Version 2.1.8 =
* Fixed issue where core themes were sometimes incorrectly treated as
legacy ones
* Fixed issue where some elements in child themes weren't correctly
rendered
* Fixed issue where event details link was being malformed in some
cases
* Improved compatibility with some 3rd party themes

= Version 2.1.7 =
* Fixed issue where a stopping error may have been encountered when
3rd party plugins do not properly use include_once family functions
* Fixed issue where address autocomplete wasn't properly disabled
* Fixed issue where clicking "Back to calendar" was redirecting to
default calendar when a site had more than one calendar embedded via
shortcode
* Fixed issue where on some browsers and operating systems extra
characters were being rendered on screen
* Fixed issue where some 3rd party plugins were injecting non-readable
data into the event excerpt view
* Fixed issue where all-day view was not rendering correctly at all
times
* Improved print-view to use compact agenda view

= Version 2.1.6 =
* If an event's timezone is different from the site's it will now be
displayed on the event details page
* Improved calendar view customization by allowing selection of fonts
* Made CSS cached filename unique on every theme save to improve caching
compatibility
* Improved AJAX failure handling
* Improved button layout on HTML4 sites
* Improved the UI of the filter bar when empty
* Improved CSS rendering in widgets, to avoid conflicts
* Improved performance by re-compiling CSS afer changes require it
* Made it possible to use area between filter bar and main calendar as a
widget area with certain themes
* Prevent potential issues with incompatible add-on versions by checking
them during activation
* Showing add-ons available for All-in-One Event Calendar in a
dedicated page
* Fixed issue with unescaped HTML in the widget title 
* Fixed wording - using proper WordPress name wherever applicable 
* Fixed issue with double-escaped HTML in Agenda view (strange
characters in titles) 
* Fixed invalid constant use which was causing some strings to be not
translatable 
* Fixed event title rendering in a widget 
* Fixed span class appearing on all day events in the sidebar widget 
* Fixed imported all day events appearing a month ahead in Month View 
* Fixed an issue where base64 fonts caused errors with older versions of
PHP 
* Fixed an issue with ics feeds importing past events 
* Fixed an issue where theme options need to be resaved after update 
* Fixed an issue where font awesome icons were missing in Firefox 
* Fixed a conflict with sortcodes and front end rendering 
* Fixed an issue where an event missing a timezone caused a fatal error 
* Fixed a styling issue with Select2 fields on the settings page 
* Fixed an issue where the post your event button did not display on a
calendar set with shortcode 
* Fixed an issue where some calendars displayed extra space below
Posterboard 
* Fixed an issue where certain feeds would create double images 
* Fixed a navigation issue with a calendar embedded by shortcode 
* Fixed issue where Agenda View displaed multiple images 
* Fixed an issue where clicking a link in js widgets did not open the
modal 
* Fixed issue where clearing filters changed spacing 
* Fixed issue where clearing filters reset the calendar to default view 
* Fixed issue where in some cases views would not change 
* Fixed issue where featured images were missing from Streamview in
Firefox 
* Fixed issue where the timezone of events imported from Google was set
to UTC

= Version 2.1.5 =
* Confirmed compatibility with WordPress 4.0 and added new Timely icon

= Version 2.1.4 =
* Improved context awareness of cache clean-up function to protect
from accidentally removing files that do not belong to the plugin

= Version 2.1.3 =
* Added possibility to keep old events during ICS feeds update
* Made subscribe dropdown button mobile friendly
* Implemented microformats 2 improving SEO and reducing theme and plugin
conflicts
* Improved compatibility with JetPack - sharing elements no longer
appear on empty pop-over elements
* Enabled translation of some previously untranslatable strings
* Made it possible to translate view names
* Fixed ICS import which was failing due to unrecognized timezones for
excluded dates
* Fixed pagination in Agenda view
* Fixed `the_title` filter to only add hEvent class names to our post
titles
* Improved cache behavior - no longer stressing when write to file cache
fails
* Improved cache fall-back to database when faster means are unavailable
* Fixed potential error which could have prevented settings from being
saved on some systems
* Fixed JetPack compatibility
* Fixed widget pop-up which was displaying event sharing information in
some cases

= Version 2.1.2 =
* Fixed issue where settings weren't saving in some cases
* Fixed issue where permalinks for events weren't working until "save"
  was clicked in permalinks settings page
* Fixed issue where excluded dates from ICS feeds weren't correctly
  imported

= Version 2.1.1 =
* Added ability to select mobile specific default views
* Improved filter bar layout for mobile rendering
* Improved CSS load times by using cached CSS file when possible
* Changed single event alias from /ai1ec_event to /event
* Allow multiple calendars on a single page
* Allow developers to modify values before they are passed to templates
* Improved CSS compiling decreasing page load times
* When file cache is not available CSS is stored in database and output in
  <style> tag directly for  increased performance and to mitigate potential
  security risks
* Introduced use of microformats for improved SEO
* Improved error handling on ICS feeds page (more extensive reporting,
  clear message on allowed URL formats)
* Improved exclude dates selector widget and exclude dates overview in
  Dashboard
* Improved information displayed when links for tags/categories are
  clicked
* Removed duplication of Publish button
* Allow the filter menu to be affixed to the window
* Allow time zone selection during event creation
* Improved display of robots.txt field in Settings as well as
  robots.txt handling
* Fixed import/export of events with no end time or date
* Fixed todays date marking in Agenda-like views
* Fixed expand map link
* Removed urls from print view
* Restored address autocomplete region biasing function
* Fixed fatal error preventing CSS compilation
* Fixed section 508 compatibility
* Improved security by preventing XSRF on forms and links where
  applicable

= Version 2.0.13 =
* Fixed week view where events spanning multiple days were incorrectly
rendered;
* Changed default single event URI to `/event` and make it possible to
translate them (translation string - 'event')
* Updated screenshots for base themes;
* Fixed month view popup location;

= Version 2.0.12 =
* Fixed ICS import issue where feed meta information was duplicated
across feeds;
* Restored timezones selector to Settings page (visible when none is
chosen);
* Improved compatibility with 3rd party themes (Compasso);
* Improved contact information import/export via ICS feeds;
* Improved CSS files generation to reduce use of `!important`
declaration on custom rules;
* Restored filters `active` state (displaying tags/categories filters
as active when selected);

= Version 2.0.11 =
* Fixed issue where incorrectly recognized unique identifiers (UIDs)
from ICS feeds caused the creation of duplicate events.

= Version 2.0.10 =
* Improved "Subscribe" to calendar button;
* Modified subscription options, removing some elements from content
when subscribing in products that do not support rich formatting;
* Restored option to choose week/day view start/end times;
* Restored borders to Agenda View;
* Moved translations from Twig files to ensure GlotPress captures all
translation strings;
* Fixed issue where past dates (like 1922-05-05) selected in the
frontend form datepicker caused Core to deactivate;
* Fixed issue where older versions of WordPress did not show all Event
menu items (add/edit);
* Improved UID usage - events are now first recognized by UID thus
avoiding overwriting of events previously imported from some feed;
* Fixed issue where ICS import was failing on systems where use of
undeclared variable caused processing to stop;
* During update unused legacy themes will now be removed where
possible;

= Version 2.0.9 =
* Improved plugin performance by reducing the number of database
queries made during average front-end page load by at least 30%
(optimized WordPress data retrieval patterns);
* Fixed issue where special characters (like quotes) were escaped when
used in Settings;
* Fixed issue where monthly view popover displayed time one minute
early;
* Improved CSS loading performance by making option auto-loadable;
* Fixed issue where folder conflict (vendor/{Twig,twig}) was causing
problems on some systems;
* Fixed issue where using a manual timezone caused a conflict when
navigating to particular date;
* Fixed issue where all events were displaying as the same duration on
a Single Day View and Week View;
* Fixed issue where it was impossible to deactivate multiple plugins;
* Fixed issue where an error was raised when no cache providers where
available on some systems;
* Fixed issue where Upload image button in Front End Events form was
unresponsive;
* Fixed issue where CSS files were not properly included on some
systems;
* Fixed compatibility with WPML;
* Fixed issue where ICS feeds were not automatically updating at the
set intervals;
* Fixed instructions for child themes creation found in Gamma theme
files;
* Fixed issue where some servers mistakenly reported cache directory
as writable causing the plugin to be disabled;
* Restored notification to finish plugin configuration after
installation;
* Fixed issue where datepicker on the frontend was rendered
incorrectly with some themes;

= Version 2.0.8 =
* Fixed issue where it was impossible to edit some of the events;
* Fixed issue where it was impossible to trash custom post types in
some cases;
* Fixed issue where it was impossible to edit some recurrent events;

= Version 2.0.7 =
* Restored option to disable GZIP compression;
* Fixed issue where it was impossible to clear default tags/categories
selection;
* Fixed issue where Agenda widget wasn't displaying start time;
* Fixed issue where tags/categories were incorrectly processed for ICS
exported events;
* Fixed issue where comments weren't properly disabled on imported ICS
feeds;
* Fixed issue where database upgrade was failing when some database
tables were empty;
* Fixed issue where exclude dates were not correctly exported for
Google Calendar;
* Fixed issue where timezones weren't properly converted for some old
events;
* Fixed issue where shortcode for a day view was incorrectly defined;
* Fixed issue where HTML characters appeared in categories/tags names;
* Fixed issue where it was impossible to edit single event instance
(entire set was edited instead);
* Fixed calendar navigation when page slug has UTF-8 values
(non-English characters) in it;
* Fixed issue where category colour wasn't properly displayed in all
calendar views;
* Fixed issue where Postbox elements open/close functionality was
broken;
* Adjusted http://time.ly information panel in Settings page;

= Version 2.0.6 =
* Initial public release of 2.0 on http://WordPress.org repository;
* Restored ability to remove Category image;
* Fixed licence keys activation;
* Fixed issue where All-in-One Event Calendar JavaScript wasn't
loading in some configurations;
* Improved event page layout;
* Fixed issue where Upcoming Events widget wasn't properly wrapped;
* Fixed issue where migrating from versions prior to 1.10 caused
events date time to have offset added in some cases;
* Fixed issue where manual offset times were not recognized;
* Fixed issue where upgrading was causing fatal errors in some cases;
* Fixed issue where templates cache directory problems were not
properly diagnosed.

= Version 2.0.5 =
* Restored translation files;
* Restored filtering by tags/categories in All Events page;
* Fixed issue where feeds weren’t exporting in some cases;
* Fixed issue where “Buy Tickets” button was directing to invalid URL
in some cases;
* Fixed Add to Google Calendar link;
* Fixed issue where widget was causing plugin to be disabled in some
cases;
* Modified themes rendering by giving child themes higher priority;
* Fixed issue where some links were invalid when pretty permalinks
were disabled;
* Fixed issue where Show year option wasn’t working.

= Version 2.0.4 =
* Introduced support for legacy themes (requires add-on for full
operation).

= Version 2.0.3 =
* Improved database structure conversion handling.

= Version 2.0.2 =
* Updated vendor libraries.

= Version 2.0.1 =
* Made plugin modular, allowing users to install extensions for
required features;
* Improved performance by delaying resources initialization until
needed;
* Implemented new theme layer using Twig
(http://twig.sensiolabs.org/), which will allow the Calendar to render
new views in JavaScript;
* Created time manipulation layer, which will allow editing of event’s
timezone, and also leverage system ability to track timezone
definition changes when processing, or rendering of time entity comes
into action;
* Timezone information is stored with the event during event creation
and/or import ensuring event is treated with respect to it’s original
timezone;
* Implemented soft-deactivation feature to help prevent fatal errors
which notifies user if an error occurs and makes plugin behave as if
it was deactivated;
* Allowing smooth interoperability with 3rd party cache plugins
primarily by not providing another cache layer which would cripple
behaviour of former ones;
* Added thin compatibility layer which supports up-to date
PHP/WordPress versions and provides support for older releases;
* Created calendar feeds extension layer, which is meant to allow
adding new types of feeds in addition to already existing ICS;
* Created configuration abstraction layer, which is safe to use with
3rd party caching plugins;
* Fixed repeated cron additions, which was causing intensive database
writes in some configurations;
* Extended meta-data management layer to save from unnecessary calls
to underlying database;
* Implemented robust and extensible filtering layer allowing to create
new filter types on demand;
* Improved internationalization support with respect to WordPress and
3rd party plugins behaviour;
* Updated to Bootstrap 3 for better performance and responsiveness;
* Moved Front End Event Submission, Superwidget, Platform Mode,
Posterboard, Stream View, Agenda View, Facebook and Twitter to
extensions. Please view their release notes for details;
* Fixed DST issue causing times to shift one hour;
* Clicking the browser back button returns site visitors to view they
were on;
* Fixed issue where the widget would not display a full day’s events;
* Created new Venues extension allowing user to create and save
venues;
* Fixed publish to Facebook to include event address and image;
* Updated Facebook vendor library;
* New setting to exclude importing private events;
* New setting to exclude importing declined events;
* Added ability to make fields required on Front End Submission form;
* Created short-code to place form on a page;
* Eliminated Posterboard view loading dependency on webfonts;
* Upgraded Masonry for smoother animation effects and better
performance.

= Version 2.0.0 =
* Initial release of modified architecture plugin. See above for
detailed architectural changes.

= Version 1.10.11-standard =
* Modified date and time storage to protect values from potential
changes caused by MySQL.

= Version 1.10.10-standard =
* Fixed an issue where Standard users are asked to update to the Lite
version.
* Collect Standard user's email address to send free licences for new
non core extensions.

= Version 1.10.9-standard =
* Increasing version number from 1.10.1-Standard to 1.10.9-Standard to
work around WordPress behaviour suggesting upgrade to 1.10.4-Lite.

= Version 1.10.1-standard =
* Added "last" option to monthly recurrence patterns
* Modified exported UID to reflect event origin
* Fixed issue where category colour wasn't seen on Event Categories
admin page
* Fixed issue where some names recieved "MAILTO:" prefix when
importing feeds
* Fixed several database handling cases which may have caused plugin
installation failure on some systems
* Improved plugin performance especially when handling larger number
of events
* Modified addressing scheme to use tilde (~) instead of colon (:) for
arguments identification to resolve an issue with some Microsoft IIS
servers
* Fixed issue where events exported to Facebook had backslash added
before certain symbols
* Fixed issue that caused a JavaScript error to app
ear on admin dashboard in some cases
* Added possibility to turn off compression of CSS files
* Fixed issue that prevented "Reveal whole day" button from working
* Fixed several possible vulnerabilities (courtesy for finding goes to
Christian Mehlmauer)
* Fixed issue with WordPress Total Cache plugin that caused server
error
* Fixed issue that prevented removal of assigned category colours

= Version 1.10-standard =
* Made improvements to page rendering times
* Addressed issue with errors occurring on some hosting environments
(__PHP_Incomplete_Class)
* Improved event start/end time display in Posterboard view; "Show
year" setting now also applies to Posterboard view

= Version 1.9.6-standard =
* Fixed issue with plugin update failing due to permissions error
* Fixed issue with admin area menu entries disappearing for
non-administrative users
* Fixed issue with .ics feeds failing to import when some of the
events have no end time
* Fixed issue with Page Not Found (error 404) being experienced on
custom taxonomy archive pages
* Fixed issue with preselected categories/tags filter not working
* Fixed issue with Subscribe link not working on iPad
* Fixed issue with AJAX requests failing when FORCE_SSL_ADMIN constant
is defined
* Fixed issue with apostrophes having prepended backslashes in
Location field
* Fixed issue with source code being shown as plain text in Agenda
view with certain plugins activated
* Improved time zone display in event creation form
* Fixed issues with Roots theme, font loading, and Post Your Event
form category/tag selectors
* Fixed issue with events disappearing from Agenda-like calendar views
before they end
* Fixed issue with all-day events not ending on correct date

= Version 1.9.5-standard =
* Fixed an issue where the time of events, occurring on or after DST
change, has incorrect offset (see
http://help.time.ly/customer/portal/articles/1038017 for more details)
* Fixed an issue where subscribed .ics feeds are being flushed rather
than being refreshed during automatic cron update
* Fixed incorrect formatting of events' end date/times
* Fixed an issue where geographical information import/export via .ics
feeds is causing loss of data

= Version 1.9.4-standard =
* Fixed issue with exported ICS feed containing invalid characters
* Fixed issue with calendar failing to apply filters when embedded via
shortcode
* Addressing issue with database update failing with unclear error
* Fixed issue with referencing non-existent default WordPress roles
* Fixed issue with slash symbols polluting some values
* Fixed issue with all-day events not ending on correct date
* Modified uninstall process to remove plugin-specific options
* Fixed issue with events not being imported on some server
configurations

= Version 1.9.3-standard =
* Fixed issue with .ics file being generated with invalid fields
* Implemented better integration with qTranslate
* Made an improvement to webfont loading
* Fixed issue with URL in CSS containing duplicate full-path
* Improved address handling on some specific server configurations
* Improved .ics import handling of all-day events
* When database upgrade fails, now displays friendly message

= Version 1.9.2-standard =
* Fixed issue with calendar toolbar not updating in shortcode-embedded
calendar
* Renamed "Instantaneous" checkbox to "No end time"
* Added option to skip in_the_loop() check to resolve conflicts with
certain themes (Gonzo, Simplicity, etc.)
* Fixed second URL issue with calendar page sometimes missing trailing
slash

= Version 1.9.1-standard =
* Restored the old front-end Post Your Event button (links to Add New
Event screen in WP dashboard)
* Fixed URL issue with calendar page being a subpage of a parent
* Fixed URL issue with calendar page sometimes missing trailing slash

= Version 1.9-standard =
* Inline Calendar Theme editor
* Edit one instance of a recurring event
* Improved Agenda and Posterboard Event Filtering
* Better URL structure: link to a specific month/day/week
* Revised Calendar UI and better filtering
* Prominently featured event images, and Event Category default image
* RSS event feed
* WPML compatibility
* Setting to select initial category and tag filters
* Instantaneous events: events with start time only
* Limit Upcoming Events widget to # of events
* Better preservation of event location, categories and tags on
import/export
* Performance optimizations
* Ticket Purchase URL field
* Added support for PHP unsupported timezones
* PHP and Javascript refactoring
* Security updates
* Removed ?wp_cron=... from URL
* Import photos from Facebook Events
* Added Website URL field
* UI improvements to administration screens
* Bug fixes, UI enhancements, and more

= Version 1.8.4-premium =
* Added support for WordPress 3.5

= Version 1.8.3-premium =
* Fixed an issue with google maps
* Fixed an sql problem in duplicate controller
* Fixed an upgrade theme issue

= Version 1.8.2-premium =
* Added compatibility when the official Facebook plugin is installed

= Version 1.8.1-premium =
* Added support for WordPress v3.2 - WP_Scripts::get_data method
didn't exist before WP v3.3

= Version 1.8-premium =
* "Posterboard" view option for event display
* Ability to have only certain calendar views enabled
* Refactored Javascript to reduce conflicts with themes and plugins
* Facebook Integration - Import and Export events to Facebook
* Front End UI enhancements
* Updated ical parser

= Version 1.7.1 Premium =
* AIOEC-186 AIOEC-195: Added compatibility for WordPress 3.4
* AIOEC-120: Internet Explorer - admin + frontend UI compatibility
* AIOEC-193: On single events page, the "pm" (or am) appears on the
following line in Skeptical Wootheme
* AIOEC-195: Theme screenshots do not show up in 3.4

= Version 1.7 Premium =
* Restored support for WordPress 3.2.x, which fixes numerous
JavaScript issues in that version of WordPress
* Updated jQuery loading to avoid theme, slider, other issues
* Removed opaque background from calendar containers to better match
WP theme background
* Updated multi-day UI
* Improved UI for latitude / longitude
* un-minified css for easier editing

= Version 1.6.3 Premium =
* Added support for server running versions of php below 5.2.9

= Version 1.6.2 Premium =
* Fixed bug that was breaking adding/importing/editing events
* Enabled updates and update notifications when there is a newer
version

= Version 1.6.1 Premium =
* Fixed bug that was breaking widget management screen
* Removed some warnings from month view in certain setups

= Version 1.6 Premium =
* Choose new Calendar Themes
* Duplicate Events
* Create Print View
* Add location details that allow latitude and longitude for areas
poorly covered by Google Maps
* Turn on/off autocomplete for addresses
* See more intuitive views of multi-day events on weekly and monthly
calendars
* Calendar administration role to allow for dedicated calendar
application
* Security updates
* Bug fixes

= Version 1.5 =
* Added daily view
* Various bug fixes
* Added new translations
* Added support for featured images
* Better support for Multisite Ajax
* Added support for DURATION property in iCalendar specs
* Resolved FORCE_SSL_ADMIN issue

= Version 1.4 =
* Export ICS feeds with utf8 header
* Import/Download ICS feeds with CURL if available, otherwise keep the
current method
* Better UTF8 support for imported events
* Use local version jquery tools instead of the CDN copy
* Improved system for catching errors and trying best to find a
possible route to proceed without having to quit/fail
* Fixed various Notice level errors
* Fixed bug with recurrence/exception rules not properly being
converted to GMT
* Added EXDATE support and EXDATE UI to allow selection of specific
dates.
* Added filter by feed source on All events page
* Improved caching of stored events
* Fixed getOffset problem - notify me if it still happens for you

= Version 1.3 =
* Added shortcodes support.[#36](http://trac.the-seed.ca/ticket/36)
(Howto is under Frequently Asked Questions tab)
* Added support to exclude events using
[EXRULE](http://www.kanzaki.com/docs/ical/exrule.html)
* Added Czech translation
* Added Danish translation
* Updated Swedish translation

= Version 1.2.5 =
* Reviewed plugin's security. The plugin is as safe to use as is
WordPress itself.
* Fixed: instance_id not corresponding with correct data
[#275](http://trac.the-seed.ca/ticket/275)
* Fixed: Call-time pass-by-reference warning
[#268](http://trac.the-seed.ca/ticket/268)
* Improvement: Added support for custom fields

= Version 1.2.4 =
* Improvement: Added a lower version of iCalcreator for environments
with PHP versions below 5.3.0

= Version 1.2.3 =
* Improvement: Days of the week in month recurrence
[#170](http://trac.the-seed.ca/ticket/170)
* Improvement: Make Month view, Week view compatible with touchscreen
devices [#210](http://trac.the-seed.ca/ticket/210)
* Improvement: Improve error handling in get_timezone_offset
function[#219](http://trac.the-seed.ca/ticket/219)
* Improvement: Update iCalcreator class
[#256](http://trac.the-seed.ca/ticket/256)
* Fixed: Widget Limit options (category, tag, etc) multiselect fails
to display properly [#192](http://trac.the-seed.ca/ticket/192)
* Fixed: Private Events Show in Calendar and Upcoming
Events. [#201](http://trac.the-seed.ca/ticket/201)
* Fixed: Dates getting mixed up between Ai1EC calendars
[#229](http://trac.the-seed.ca/ticket/229)
* Fixed: Error displayed when event is a draft
[#239](http://trac.the-seed.ca/ticket/239)
* Fixed: PHP Notice errors from widget
[#255](http://trac.the-seed.ca/ticket/255)

= Version 1.2.2 =
* Fixed: Issue with Week view having an improper width
[#208](http://trac.the-seed.ca/ticket/208)

= Version 1.2.1 =
* Fixed: Exporting single event was exporting the whole calendar
[#183](http://trac.the-seed.ca/ticket/183)
* Fixed: Widget date was off by one in certain cases
[#151](http://trac.the-seed.ca/ticket/151)
* Fixed: Trashed events were still being displayed
[#169](http://trac.the-seed.ca/ticket/169)
* Fixed: All day events were exporting with timezone specific time
ranges [#30](http://trac.the-seed.ca/ticket/30)
* Fixed: End date was able to be before the start date
[#172](http://trac.the-seed.ca/ticket/172)
* Fixed: 404 or bad ICS URLs now provide a warning message rather than
fail silently [#204](http://trac.the-seed.ca/ticket/204)
* Fixed: Added cachebuster to google export URL to avoid Google
Calendar errors [#160](http://trac.the-seed.ca/ticket/160)
* Fixed: Week view was always using AM and PM
[#190](http://trac.the-seed.ca/ticket/190)
* Fixed: Repeat_box was too small for some translations
[#165](http://trac.the-seed.ca/ticket/165)

= Version 1.2 =
* Added scrollable Week view
[#117](http://trac.the-seed.ca/ticket/117)
* Fixed some notice-level errors

= Version 1.1.3 =
* Fixed: last date issue for recurring events "until" end date
[#147](http://trac.theseednetwork.com/ticket/147)
* Fixed an issue with settings page not saving changes.
* Fixed issues when subscribing to calendars.
* Export only published events
[#95](http://trac.theseednetwork.com/ticket/95)
* Added translation patch. Thank you josjo!
[#150](http://trac.theseednetwork.com/ticket/150)
* Add language and region awareness in functions for Google Map. Thank
you josjo! [#102](http://trac.theseednetwork.com/ticket/102)
* Small translation error in class-ai1ec-app-helper.php. Thank you
josjo! [#94](http://trac.theseednetwork.com/ticket/94)
* Added Dutch, Spanish, and Swedish translations. For up to date
language files, visit [ticket
#78](http://trac.theseednetwork.com/ticket/78).

= Version 1.1.2 =
* Fixed: Problem in repeat UI when selecting months before October
[#136](http://trac.theseednetwork.com/ticket/136)
* Fixed: Append instance_id only to events permalink
[#140](http://trac.theseednetwork.com/ticket/140)
* Fixed: Events ending on date problem
[#141](http://trac.theseednetwork.com/ticket/141)
* Feature: Added French translations

= Version 1.1.1 =
* Fixes a problem when plugin is enabled for first time

= Version 1.1 =
* Feature: New recurrence UI when adding events
[#40](http://trac.theseednetwork.com/ticket/40)
* Feature: Translate recurrence rule to Human readable format that
allows localization [#40](http://trac.theseednetwork.com/ticket/40)
* Feature: Add Filter by Categories, Tags to Widget
[#44](http://trac.theseednetwork.com/ticket/44)
* Feature: Add option to keep all events expanded in the agenda view
[#33](http://trac.theseednetwork.com/ticket/33)
* Feature: Make it possible to globalize the date picker. Thank you
josjo! [#52](http://trac.theseednetwork.com/ticket/52)
* Fixed: On recurring events show the date time of the current event
and NOT the original event
[#39](http://trac.theseednetwork.com/ticket/39)
* Fixed: Events posted in Standard time from Daylight Savings Time are
wrong [#42](http://trac.theseednetwork.com/ticket/42)
* Fixed: Multi-day Events listing twice
[#56](http://trac.theseednetwork.com/ticket/56)
* Fixed: %e is not supported in gmstrftime on Windows
[#53](http://trac.theseednetwork.com/ticket/53)
* Improved: IE9 Support
[#11](http://trac.theseednetwork.com/ticket/11)
* Improved: Corrected as many as possible HTML validation errors
[#9](http://trac.theseednetwork.com/ticket/9)
* Improved: Optimization changes for better performance.

= Version 1.0.9 =
* Fixed a problem with timezone dropdown list

= Version 1.0.8 =
* Added better if not full localization support
[#25](http://trac.theseednetwork.com/ticket/25)
[#23](http://trac.theseednetwork.com/ticket/23)
[#10](http://trac.theseednetwork.com/ticket/10) - thank you josjo
* Added qTranslate support and output to post data using WordPress
filters [#1](http://trac.theseednetwork.com/ticket/1)
* Added uninstall support
[#7](http://trac.theseednetwork.com/ticket/7)
* Added 24h time in time pickers
[#26](http://trac.theseednetwork.com/ticket/26) - thank you josjo
* Fixed an issue when event duration time is decremented in single
(detailed) view [#2](http://trac.theseednetwork.com/ticket/2)
* Fixed an issue with times for ics imported events
[#6](http://trac.theseednetwork.com/ticket/6)
* Better timezone control
[#27](http://trac.theseednetwork.com/ticket/27)
* Fixed the category filter in agenda view
[#12](http://trac.theseednetwork.com/ticket/12)
* Fixed event date being set to null when using quick edit
[#16](http://trac.theseednetwork.com/ticket/16)
* Fixed a bug in time pickers
[#17](http://trac.theseednetwork.com/ticket/17) - thank you josjo
* Deprecated function split() is removed
[#8](http://trac.theseednetwork.com/ticket/8)

= Version 1.0.7 =
* Fixed issue with some MySQL version
* Added better localization support - thank you josjo
* Added layout/formatting improvements
* Fixed issues when re-importing ics feeds

= Version 1.0.6 =
* Fixed issue with importing of iCalendar feeds that define time zone
per-property (e.g., Yahoo! Calendar feeds)
* Fixed numerous theme-related layout/formatting issues
* Fixed issue with all-day events after daylight savings time showing
in duplicate
* Fixed issue where private events would not show at all in the
front-end
* Fixed duplicate import issue with certain feeds that do not uniquely
identify events (e.g., ESPN)
* Added option to General Settings for inputting dates in US format
* Added option to General Settings for excluding events from search
results
* Added error messages for iCalendar feed validation
* Improved support for multiple locales

= Version 1.0.5 =
* Added agenda-like Upcoming Events widget
* Added tooltips to category color squares
* Fixed Firefox-specific JavaScript errors and layout bugs
* Added useful links to plugins list page
* Fixed bug where feed frequency setting wasn't being updated
* Made iCalendar subscription buttons optional

= Version 1.0.4 =
* Improved layout of buttons around map in single event view
* Set Content-Type to `text/calendar` for exported iCalendar feeds
* Added Donate button to Settings screen

= Version 1.0.3 =
* Changed plugin name from `All-in-One Events Calendar` to `All-in-One
Event Calendar`
* **Important notice:** When upgrading to version `1.0.3` you must
reactivate the plugin.

= Version 1.0.2 =
* Fixed the URL for settings page that is displayed in the notice

= Version 1.0.1 =
* Fixed bug where calendar appears on every page before it's been
configured
* Displayed appropriate setup notice when user lacks administrator
capabilities

= Version 1.0 =
* Initial release

== Installation ==

1. Upload `all-in-one-event-calendar` to the `/wp-content/plugins/`
directory.
2. Activate the plugin through the **Plugins** menu item in the
WordPress Dashboard.
3. Once the plugin is activated, follow the instructions in the notice
to configure it.

**Important notice:** When upgrading from version `1.0.2` or below you
  must reactivate the plugin.

= For advanced users: =

To place the calendar in a DOM/HTML element besides the default page
content container without modifying the theme:

1. Navigate to **Settings** > **Calendar** in the WordPress Dashboard.
2. Enter a CSS or jQuery-style selector of the target element in the
**Contain calendar in this DOM element** field.
3. Click **Update**.

== Screenshots ==

1. Add new event - part 1
2. Add new event - with recurrence
3. Event categories
4. Event categories with color picker
5. Front-end: Month view of calendar
6. Front-end: Month view of calendar with mouse cursor hovering over
event
7. Front-end: Month view of calendar with active category filter
8. Front-end: Month view of calendar with active tag filter
9. Front-end: Week view of calendar
10. Front-end: Agenda view of calendar
11. Settings page
12. Upcoming Events widget
13. Upcoming Events widget - configuration options

== Upgrade Notice ==

= 2.0 =
I you believe you are missing functionality, please visit our site and
download the correspoding Add-on.

= 1.0.3 =
When upgrading to from below `1.0.3` you must reactivate the plugin.
