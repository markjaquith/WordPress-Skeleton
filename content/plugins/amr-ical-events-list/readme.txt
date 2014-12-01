=== amr ical events lists ===
Contributors: anmari
Tags: event, events, event calendar, events calendar, event manager, diary, schedule, ical, ics, ics calendar, ical feed, ics feed, wordpress-ics-importer, calendar,  upcoming events, google, notes, todo, journal, freebusy, availability, widget, web calendar, agenda, happenings, shows, concerts, conferences, courses, timetable, schedule
Requires at least: 2.8
Tested up to: 4.0
Version: 4.16
Stable tag: trunk

Event Calendar or Agenda list, combine multiple ics files, handles recurring events. Very customisable.

== Description ==

Display event lists, big box calendars, upcoming events widgets or small calendar widgets. Encourage viewers to subscribe to or bookmark your events on their calendars.  This plugin offers a thorough Ical calendar parser - copes with all the ical recurrence possibilities, and a large part of the rest of the spec. The free version accepts one or many ical urls for ics files.  It produces a very stylable list of events, notes, todo's or freebusy info.

**Demo**

Test with your calendar's ics file at the [Demo site](http://test.icalevents.com)

**Events in wordpress**

If you also want a complete "inhouse" solution where you can create events in wordpress with multiple event classification options, and produce your own ics feeds from those, please see [events plugin home page](http://icalevents.com). amr-events is an extension of amr-ical-events-list and will use any configuration from it.

For more information, please see [plugin home page](http://icalevents.com) 

**Features**

Displays events from multiple calendars in out the box or with customised grouping, formatting and styling. Multiple pages or post or widget or both.

Lots of css hooks to style it the way you want. - Generate multiple css tags including for hcalendar miccroformat support.

List upcoming recurring or single events, notes, journal, freebusy information from many ical feeds. Offers a range of defaults and customisation options.

Group events by month/week/day or many other for presentation and styling. Offers your viewers the option to subscribe or add the events or the whole calendar to their calendars (google or other).

**Translations**
Many thanks to the following people for the translations.  Note that I am unable to verify these and they may be out of date with the current version.:

*   Spanish from Andrew at webhostinghub
*   Italian by Andrea aka sciamannikoo
*   Lithuanian by Nata Strazda from Web Hub
*   Polish by Kasia
*   French by fxbenard aka 3emeOeil
*   Dutch by Fred Onis
*   Danish by Georg feom blogso.dk
*   Belorussian by Alexander Ovsov from webhostinggeeks.com/science
*   Romainian by webgeek
*   Russian (partial) by ghost (antsar.info)
*   Hindi translation  by Ashish J. of Outshine solutions
*   Ukranian translation by Michael Yunat of getvoip.com/blog

If anyone would like to offer some translations, please do.  The Code Styling Localisation Plugin is very useful for this.  PLease send me both the .mo and .po files for your language for quicker upload.

**Requirements:**

PHP 5 > 5.20
php datetime class must be enabled (standard in php 5.2).  This is essential to provide robust timezone usage.

== Installation ==

1.  Download and activate as per usual in wordpress
2.  Create some events in your calendar application in a public calendar (eg: google calendar, facebook, ical.me.com ).  
3.  Find the public ics url for the calendar.  NB The url MUST be publicly accessible when not logged in - check by pasting the url into another browsers url field. If you cannot access it in a browser without being logged in, then the plugin will not be able to access it either.
4. Create a page for your calendar or agenda and enter one of the  shortcodes (preferably using the html view of the wp editor) .  EG:


[iCal yoururl.ics]
[largecalendar yoururl.ics]  
[smallcalendar yoururl.ics]  
[weekscalendar yoururl.ics]  

 NB (Enter the Ics url NOT As a hyperlink - it must be plain text.  Enter it in the html view, not the visual view of the editor.)

Optional:

[expandall] - if using collapsing groups. Enter this shortcode BEFORE the [events] or [ical] shortcodes.  Remember to tick 'use js to collapse groups' in the listing events styling settings and of course define a grouping for your list type.  See http://icalevents.com/4447-groupings-of-events-expanding-and-collapsing-the-groupings/



That's it!

**For the widgets (calendar and list)**

1.  Drag one of the events widgets to the chosen sidebar
2.  Enter http://yoururl.ics in the widgets input area, Save


Some calendar applications delay a bit before updating the ics file.  If you think the plugin is 'missing' a new event,  please open your ics link in a text editor like notepad.  Check that the event is actually in the ics  file.

There are many shortcode parameters and some highly configurable list types.  Please read:

http://icalevents.com/documentation/getting-started/
http://icalevents.com/documentation/shortcodes/
http://icalevents.com/documentation/list-types/

Note that some information may apply to the paid version amr-events - this is usually indicated.
amr-events has much additional functionality, including a taxonomy widget.


== Changelog ==
= Version 4.16 =
*  Fix: re VFREEBUST - Changes to the freebusy text were not saving, kept reverting to blank or the red cross.  It will now update.  If blanked out then the text found in the ics file will be used 'Busy' and translated if translation text is available.
*  Fix: a situation that most of you will never ever use - the pretty printing of the WKST ina recurrence rule (eg in the testing list type 10)

= Version 4.15 =
*  Better Checking for empty data sets in calendar formats, before trying to do array multi sort

= Version 4.14 =
*  Slight problem with some sites timezones due to the previous code changes and small bug in application of the new windows zone filter.  Have tested on a set of zones I have, but there is always weird data out there. Please let me know asap if you find anything strange.  Thanks.  

= Version 4.13 =
*  For sites on php versions < 5.3. Removed use of anonymous function for error handler on line 499 on amr-import-ical.php

= Version 4.12 =
*  Added ability to map windows zones in ics files to real timezones (IANA, php and Ohlson)
*  Added a timezone filter to facilitate any other mappings that may be required. apply_filters('amr-timezoneid-filter',$icstzid);
*  File included to update the mapping from the unicode.org xml file should it ever change. See timezones folder. rename the .txt to .php, execute.  
*  Changed default timezone if a timezone id cannot be parsed (maybe because some other application is doing ity's own weird thing). Will now be your wordpress php timezone, not php or UTC.
*  Cleaned up translation loading a little

= Version 4.11 =
*  Fixed a bug in the calendar properties section.  Column 1 was never showing - whoops. If you are now seeing your ics calendar name and you didn't want to.  Go to list type settings, calendar properties and assign a column of '0' to the X-WR-CALNAME.  Note not all ics files have a X-WR-CALNAME, so you may not see anything anyway.

= Version 4.10 =
*  event titles in small calendar daylinks will now be sorted by time with all day events first.
*  daylinks in small and large calendar had lost the listype and/or ability to use an agenda list type passed in the shortcode. This is fixed.  Note: links from the day of month will pass an agenda listype and day parameters back to same page to show just events for that day in agenda format.  If you want to use another page, then configure another calendar page and enter that url in the shortcode [largecalendar more_url=yourotherurl]

= Version 4.9 =
*  fixed a typo bug hopefully only recently introduced.  It affected recurring events with modified instances.

= Version 4.8 =
*  added more trapping of datetime exceptions to avoid fatal errors in some installs
*  fixed bug when recurring weekly with some days of week. 
*  ensured that rdates (specific recurring dates) are properly sorted if displayed in a wp event

= Version 4.7 =
*  removed use of php's date_default_timezone_set() as wordpress wants everything to stay in UTC (despite having timezone in settings).  Checked and updated all DateTime create calls to ensure that they either cloned existing dates & ths their zones, or explicitly used a global timezone got from the saved wordspress timezone.
*  fix: small bug where if rdates (specific recurrence dates not due to rules) existed, then DTSTART was not propoerly included.
*  cope a bit better with ics files that seem ok but have junk in them - ignore junk events with empty dates.
*  improved formatting of some attachments
*  tested with version 3.1 of amr-events

= Version 4.6 =
*  made nl2br2 a pluggable function so folks can add =0D-0A replacement checks.
*  cleaned up how custom language files were accessed.  You can still store them in WP_LANG_DIR / languages / plugindomain.locale.mo.   They will be loaded first, then any files in the plugin lang folder. 
*  cleaned up some strings and their text domain assignment
*  fixed a language bug where saved text strings were overwriting translated strings.

= Version 4.5 =
*  removed a filter line that should NOT have been in that update and was not picked up in testing due to cacheing

= Version 4.4 =
*  Fix:  20131002 - fix to the single event 'add to google calendar' for one day events.  Google used to accept events without end dates in the link.  It now wants an enddate, so this has been added.  Thanks to Kevin for identifying this.

= Version 4.3 =
*  Fix:  20130922 - minor fix for multisite users.  Removed a debug function.

= Version 4.2 =
*  Fix:  fixed a weekly calendar header bug that somehow got introduced recently
*  Fix:  some minor notices that occured in php 5.4.3 up to do with use of array_diff

= Version 4.1 =
*  Add:  as per request, added ability to parse custom fields such as Trumba's.  Filters etc added.  An example add on created to demonstrate.  See icalevents.com in a day or so for info.
*  Fix: A minor styling bug occured if your ics file had categories on some events but not others. Fixed. 
*  Add: As part of the fix, ability to filter ics events by category was added.
*  Change: some minor tweaks made to default css to make default event list widget look a little better in twentytwelve default theme without breaking the look in twentyeleven.
*  Add: added css tweak to remove underlining of abbr (microformat markup) classes.
*  Fix: Change wp_remote_get to use filter to change http timeout instead of passing args. If args passed, then ALL must be passed.  Rather let wordpress determine the best default settings ad filter as needed.  Thanks to ob1chewy for pointing out. 
*  Tested on wp 3.5.2 - alpha

= Version 4.0.30 =
*  Add: as per request, can now use as shortcode parameter: sort_later_events_first=1 to change the default sort.  By default listings start with events closest to start date and proceed into the future.   These will reverse sort.  They may be useful for past event listings - why does one want to show old events ?  Well they just do want to!
*  Add: as per request, can now use as shortcode parameter: exclude_in_progress=1 to exclude events that may be in progress across chose start date ie: they started before chosen startdate.  By default these show as their end date is after the chosen start date.
*  Fix: for those few of you on php 5.4 - fix deprecated pass by reference.

= Version 4.0.29 =
* Fix: Minor bug if one created a new listtype without copying and pasting from an existing listtype.  Fixed.
* Update: French lang update from fxbenard.com, also a dutch update was added a few months back - thanks!
* Fix: Thanks to tubtenkunga.org, a very specfic little bug when using monthly recurring 'nth' day of week.
* Fix: Cleaned up attendee parsing and default listing in case anyone using it!
* Add: Added ability to define what shows for VFREEBUSY components instead of the 'Busy'. See listing events in settings.

= Version 4.0.28 =
* Fix: Google at some point changed event publisher practice for all day end dates (but not yet documentation) to follow ics all day end date practice.  Event ends on the 'next' day.  Function 'add to  google' updated accordingly.
* Fix: The html5 'styles' were a bit too clean.  The html markup required for rich snippets gets missed.  Other htmlstyles were fine. Html markup using spans added back so that rich snippets would be available.  

= Version 4.0.27 =
* Fix: for sites with events list on home page AND somehow have another post on home page too (global $post ends up with post data, not homepage data (id) )
* Fix: day of week text showing in calendar on empty cells after end month from last update - removed


= Version 4.0.26 =
* Fix: slight admin screen html tweak so 'internal description will go to next line on wide screens
* Change: some error messages when you realy haven't set up your settings correctly, when now only be shown to the administrator
* Change:  added rel="next" and rel="prev" where appropriate to the various date navigation

= Version 4.0.25 =
* Fix: some code changes caused some problems with unset objects - particularly in calendar
* Fix: Widget or ungrouped list could have missing 'ul' if no grouping s in thelist and an "html list style" of "lists for rows" was used (This was old default for widgets).  Now fixed - ul will be issued even if there is no grouping.  Alternatively you may prefer to use "html5" html list style instead - see 'other' under configure listtype.
* Change: Deprecated the "lists for rows" html style option (still works, just discouraging use of it)
* Change: Removed '<span>'s that wrapped 'cell' content in the "lists for rows" html style.  Since html text could conceivably end up inside the "cell", this would result in invalid html - cannot have paragraph tag inside a span.  Overall this should be low impact; particularly as other html styles are more 'attractive' - html5 etc.
* Tested with amr-events 3.0.9

= Version 4.0.24 =
* Add: added ability to set http timeout for those whose ics file fetches are timing out with default wp setting. See advanced settings.
* Fix: Some notices in debug mode
* Fix: allowed for possibility that filemtime doesn't always return a value even if cached file exists.  was used in error message when problme refreshing external ics file
* Change: changed some default html choices to use html5 options rather (eg: widget, and eventinfo).  If youhaev saved options, they will be unaffacted.  You can choose html5 options by goingto list type settings, scroll to "other", open and choose HTML5Table or HTML5   

= Version 4.0.23 =
* Fix: Widget title had a hyperlink to calendar url - latest wp does not like that - weird results.  Link removed.
* Fix: with recent update for weekly horizontal, the boxcalendar padding at end of month got slightly broken - fixed.
* Add: some debug options to measure any timing issues (already can check memory etc), so can tell if it this plugin or some other plugin/theme


= Version 4.0.22 =
* Fix: Weekly horizontal calendar was STILL not working correctly . around a month end it behaved like a monthly calendar instead of a weekly schedule.
* Fix: If you saved the listype 11, it 'lost' the weeks calendar setting and you couldn't get it back without resetting. Could set to others, but not to weekscalendar - very weird one to debug and as is often the case it was a stupid typo.
* Update: a belorussian translation update was added.


= Version 4.0.21 =
* Fix: Decode entities (like &nbsp;) in content
* Fix: Weekly horizontal calendar was not working correctly
* Change: If Event urls in ics files are external to the site, they will have rel="external" and target="_blank" added via a pluggable function amr_format_url($url).
* Change: updated danish files from Team Blogos  (GeorgWP)
* Add: can now choose to add js to collapse and expand groupings.
* Add: Two level of groupings of events possible and can group by categories and tags etc if using amr-events

= Version 4.0.20 =
* Fix: if DTEND matches DTSTART, it is not necessarily an error, or an all day.
* Fix: Pluggables loaded too late for other plugins that want to apply filters to content.  Pluggable file will now load with priority 99 on 'plugins_loaded'.  So please load any pluggable functions with earlier priority.

= Version 4.0.19 =
* Fix: switching views and then doing month navigation did not keep final view, will now.
* Fix: to German translation file for the goto titles on month year navigation.
* Add: can now specify own text for 'reset' message on the 'look for more events' and a new option 'look for previous'.  Blanking out the text will hide that link options for previous and reset.  'Previous' and 'Reset' show when one has done at least 1 look more.
* Add: 'human time' ie the midday, midnight is now controllable via the Listing settings.
* Add: word-wrap to default css to stop very long words breaking out of boxes.
* Change: Default large calendar - moved start date time etc to details hover box.

= Version 4.0.18 =
* Fix: own css got lost somewhere due to change in options loading - sorry, back now
* Add: Lithuanina translation now available.
* Add: ics files can now use excerpts like internal events.  Plugin will now check for empty excerpts and apply the filter: excerpt length.    So ics excerpts will look similar as internal events excerpts.  Cannot do the "excerpt more" filter as that often generates a link which would be incoirrect for an ics file event.

= Version 4.0.17 =
* Fix: 'allday' was not detected quite correctly for multi days in ics files - fixed
* Fix: when months were used, pagination had the same increment for more and much more - fixed.
* Changes: some minor tweaks to admin screens.


= Version 4.0.16 =
* Fix: categories with spaces in the name are not liked by css classes.  Need the names not the slugs for wordpress queries, so names are now 'sluggified' when used as a css class. Note: you can also use t## where ## is the categrory or taxonomy id (css classes do not like numbers either).
* Update: French translation from fxbenard
* Please also see amr-events upgrade to 3.0.6

= Version 4.0.15 =
* Fix: categories in imported ics files were not handled properly - effected the css classes. they are now.  Thanks to chicagoave for flagging it.
* Add: you can now do in the shortcode, add ignore_query=all to make a totally static widget or list, will not event respond any query parameters - use carefully.

= Version 4.0.14 =
* Fix: If your ics file is imported okay, no update required.  This fix is for dusty's ruby rails method of listing DATETIME with timezones and also to accomodate custom x-modifications to a property (ie: ignore them more cleanly!)

= Version 4.0.13 =
* Fixed: it was possible to generate a fatal error when there was no end date / time on an event.

= Version 4.0.12 =
* Fixed: when there are no events, the old pagination was showing and ignoring what had been specified in the shortcode.  Also an error due to no 'last event date' being available.
* Fix: adding new list type and pasting a predefined list type at same time caused a minor glitch. Now add, then paste in the list type code.
* Change: for people using amr-events.  It was possible to use DTEND (the technical 'ics' end date).in the display of event information single view.  For all day events, this looks like one day more.  Humans need the end of the previous day (not the start of the next). The template now offers the human friend end date rather to use.   THis facilitates this, however you will need soon to be released amr-events 3.0.4.
* Add: for advanced users - more of the functions are now pluggable, especially the ones with icons. see [pluggables](http://icalevents.com/documentation/filters-and-pluggable-functions/)
* Change: move pluggable functions definition later in the wordpress actions so can be more easily overwritten
* Change: tweaked the default box calendar css to stop very wide content (like links) from breaking out of the hover box.
* Change: if system tries to use a list type that has been deleted, will now look for an available one to use. Will give message, but better than dieing.

= Version 4.0.11 =
* Change: A cleanup of the import ics url code - .ics urls that had ampersands in their urls were being handled well when the import function was called.  On older php versions they were being encoded with esc_url and not decoded.  Now using esc_url_raw which is not supposed to encode html entities.  Somehow a decode still seems to be required!
* It has also come to my attention that the php filter_var with FILTER_VALIDATE_URL will not handle internationalised domain names. If this affects anyone, please contact me.
* Fix: A filter function that cleared the large calendar caption was left in when it should not have been.  The ability to use the filter  'amr_events_table_caption' is still there should you want to do your own caption text, or not have the caption html.
* Reversal: the month dropdown navigation default got temporarily changed.  All was fine either way if you were using show_month_nav in your shortcode.  But if you weren't the dropdown as a default for the large calendar got lost between versions. Apologies.


= Version 4.0.10 =
* Fix: In some date time output, the date time localisation calls were not called so some months/day of weeks were not translating.
* Fix: multi day events with partial days will now show on the partial days too.
* Fix: upcoming widget was not responding to listtype change in advanced input field - will now

= Version 4.0.9 =
* Fix: Changed one line of code made some events look like allday and so appear to lose end time. !


= Version 4.0.8 =
* Fix: Delete one line of debug code which showed when there were no events.
* Change: the sanitisation around the admin list types before / after - to use wp functions rather than neat php.

= Version 4.0.7 =
* Revert: one line of code which 'broke' some situations with categories. Reverted. See also http://forum.anmari.com/topic.php?id=179 if you prefer to change manually.

= Version 4.0.6 =
* Add: a simpler google style "look for more" events type link that can be used instead of the complex semi pagination. add show_look_more=1 to the shortcode.
* Add: option to switch off day_of_month_links in the largecalendar. use day_links=0 in the shortcode.
* Update: Updated Italian translation.
* Fix: all day was not being picked up correctly (showed up in multi day all day events) - this applies actually to amr-events too.
* Fix: small calendar days link was not picking up agenda parameter.  Also the input form calendar page url was being overwritten by blank default more_url, so only entering more_url would work.  Form field should work.

= Version 4.0.5 =
* Add: Italian translation.
* Fix: some translations strings and function calls.
* Add: added filters for 'row html' ie html per event  and 'column html' - html per group of event.  This allows you to add own fields to all 'columns' - previously could add to content or excerpt using wordpress filters
* Add: added filter 'amr_events_table_caption' for box calendar table caption
* Add: added logic for box calendar caption - if month year drop down navigation shown, then clear the caption as it is redundant (uses the filter mentioned above).
* Add: a weekly horizontal option, and a sample weekly vertical
* Add: a filter 'amr_human_time', removable and pluggable to convert 12:00 am to midnight and 12:00 pm to midday, translateable.
* Change: to default css - add a max image size in event tables, so a big image does not force table too wide.
* Change: to default css - if larger images chosen, try to clear them so they line up else looks funny if text not as tall as large icon.
* Change: to boxcalendar - if no content for pseudo 2nd column, the hover details, then the wrap html is not generated - no empty hover box.

= Version 4.0.4 =
* Fix: large calendar sorting within a day (supposed to be fixed in 4.0.2) was not properly uploaded before.
* Change: only admin can run uninstall, and only from listing screen
* Fix: non-english installs can now also delete list types

= Version 4.0.3 =
* Change: added some more 'text' to the admin area to highlight how to configure a list type.  Also added a preview link to the configure screens.

= Version 4.0.2 =
* Fix: the sorting of events within a day in the large calendar got a bit messed by the new sorting for the multi day.  Now it sorts as follows: multidays to the top in order of earliest start date.  Then all days, then any other events in order of start time.
* Change: little default css tweak for anyone using large images - they are greater than the default theme line height which made the floats in the eventinfo go a bit wonky - cleared now

= Version 4.0.1 =
* Fix: put the upcoming events list widget title back - should not have been messed with.

= Version 4 =
* Add: can now have multi-month calendar listings - like a year view.  use months=n in the calendar shortcodes
* Add: better support for multi day in box calendar. Multiday (ie where duration is > 1 day) will now appear on each day box. Css tags are offered so that you can style them creatively (firstday, middledays, lastday).  The default css in 2010 based themes shows a solid bar of the events.  Please check your theme for padding if you wish a similar effect.
* Add: Import and Export of List types, delete, copy
* Change: NB: some html tweaks and a major css cleanup - PLEASE check all your output after updating, especially if you were using your own css.
* Change: NB: Default settings have also changed.  If you did not have your settings saved and were just using defaults, the listings will be a bit different.
* Change: change to way fields are listed for including in the listing templates - they will now be sorted by the column and  the order.
* Change: categories from a ics file in an event listing now have links to query the page with the events for just those categories of events
* Fix: Bigger images option will work now
* Fix: if you want your main calendar on the home page, the linking will now use the pageid to tell wordpress to stay on the home page and just pass the other query parameters on so the plugin can use them - else wordpress starts trying to figure out what kind of archive you want!


= Version 3.10.4 =
* Fix: Additional check added to prevent additional instances in recurring logic.  There has to be an initially 1  extra iteration to cope with negative bydays (like -2MO in a month).  However a double check is required to ensure that an extra instance is not inadvertently let through.

= Version 3.10.3 =
* Fix: Remove esc-textarea from admin fields - was preventing display of saved option

= Version 3.10.2 =
* Fix: Plugin was catering for multiple EXDATES or RDATES where these occurred on same declaration. It was not catering for multiple EXDATE declarations. It does both now.  Thank you to Wilmington  Guitars for having the data that helped flush this out and for taking the time to report it well enough for me to fix it quickly.

= Version 3.10.1 =
* Change: If NOT in a widget, then href title on event summary hyperlink will now have excerpt if using amr-events and if excerpt is availble else the word "more info".  It used to hover the whole post content.
* Fix:  remove htmlspecial chars from output of event summary - mucks up special chars now in latest wp
* Update: French translation files update from fxbenard for both amr-events and amr-ical-events-list

= Version 3.10 =
* Add:  A choice of icon sizes 16x16 or 32x32 and many useful additional icons courest of famfam and fatcow.  Css sprites and suggested css code is provided for css experts who wish to make their site more efficient and include the css in their theme css.  At a later stage thsi will be integrated for less expert users.
* Add: Attachments in ics files are now parsed a bit better. If a url is found, it will be made "clickable".  A pluggable function added to allow you to override html produced for your partcular files. Note there can be multiple ATTACH in an ics file.
* Add: made more of the admin text translateable
* Add: allowed for translation of ics file name and description so that you can change them, not just trnslate them
* Add: Added css classes to the box calendar events as per the list events
* Change: exchange WP_SITEURL for get_option('siteurl') - better
* Change: changed translation text domain to plugin name to avoid confusion.  Tried to clean up language files, had some problems and do hope I have not lost any translations.  With every upgrade there are usually some new strings.
* Fix: the date number was in the sameplace in the html if there were no events.
* Add: the large calendar date number will offer a link to a list view for the day's events if there are events

= Version 3.9.6 =
* Add: made some functions pluggable to allow greater customisation - see plugin website.
* Add: polish translation

= Version 3.9.5 =
* Add: html list style options for html5 on process of being added - more changes to come.
* Change: grouped events will now NEST within a group - they used to just be a kind of heading.  HTML and css tweaked so it looks the same.
* Add: aded some html5 styles as a experiment and especially for one of you - ability to have your listHTMLstyle - please be careful. See the example included in the plugin.
* Add: a check for no saved settings.  If defaults change from one upgrade to the next and the settingshad not been saved, this could be confusing - people should save their settings.
* Add: added filters to the code to allow folks with very advanced requirements to do very advanced things.  New filters so far are:
 amr_events_after_sort (after sorting but before limits applied) Passes array of events and must receive one back,
 amr_events_after_sort_and_constrain (after sorting but before limits applied) Passes array of events and must receive one back,
 amr_event_repeats ( can be used to only generate 'x' number of repeats for an event despite what is in the VEVENT COUNT field) - passes the current count value, must receive an integer back.
  amr-ical-feed-events (after extracting events for ics feed, but before producing feed).  Note a wp-query pre-getposts-filter could also be used. Passes array of events and must receive one back.
  amr-ical-event-url - passes current url and post id, must receive back a url
 * Add: added nonbreakingspaces into day and time default formats to help prevent browers wrapping them in the table.
 * Delete: Got rid of the # bookmark feature which was cumbersome, cluttered up the html, and I do not think anyone really cared about or even knew it was there.

= Version 3.9.3 =
*  Change: per barikso's request, messages output when there is a problem with the external ics file have changed.  They are more subtle now (!) with the message on hover, and have a class of error.  The class is so that you may choose to hide the messages if you wish.  The plugin will deliver cached content if possible so your site will not just look bad should the ics file fail. (I elected to go with a fake hyperlink as depending on the problem with your ics file there may be one or more messages.)
*  Change: defaults have been slightly tweaked, so if you had NOT saved your settings, you may see small changes (eg: timezone and refresh now do NOT show by default)
*  Change: settings pages restructured and some changes to make it look more like standard wordpress, not a separate look.  I dislike plugins that have their own adminlook and style different from wp.
*  Fix: replaced some functions deprecated in wp 3.1.
*  Fix: grouping logic had an error introduced in last version where last event of a group went into next group rather than previous - fixed.

= Version 3.9.2 =
*  Fix: the shortcut of just entering urls without having to say "ics=your url" was lost in a recent update. Sorry!  It's back.

= Version 3.9.1 =
*  Fix: removed a debug statemennt that displayed in free version
*  Fix: default event url should not be mandatory
*  Fix: for list type using "lists" not table, and when useses a grouping, the html validation failed due to a missing html tag.  This highlighted some minor things in the html that could be cleaned up - and so they have been.

= Version 3.9 =
*  Change: Table body and row html slightly re-organised to more sensible structure
*  Fix: Cleaned up some hcalendar markup to make it totally valid microformat markup
*  Fix: Fixed spacing on new ++ -- pagination options
*  Fix: Fixed very specific bug when using numeric bydays (eg: last sunday ) and wanting the small calendar to link to the calendar page, with the 1 day listing.  The logic was cutting off the dates generated before the "contract" to the last x.
*  Fix: Fixed the ignore_query logic so it ignores what it should and responds to what it should (eg: for calendars, it must still respond to 'start' at least.

= Version 3.8.1 =
* Special interim update for arkanto.  Adds a parameter "ignore_query=1" to the widgets or shortcodes parameters.  This tells the plugin to NOT respond to any parameters passed via the query string.  Please use this only after due consideration and understanding and probably only in the "upcoming events list".  This will force the list to be "static" and appear the same no matter whether in a category/tag/author archive or not.  If you are using the box calendar or taxonomy widget to link through to a agenda/calendar page, do NOT use this in the calendar page shortcode.

= Version 3.8 =
* Added shortcode parameter "pagination".  By default the pagination at the bottom of the calendar list is on.  If you add "pagination=0", the pseudo pagination at the bottom of the calendar/list will be switched off.
* Removed unnecessary "pretty print" of the recurring rule.  It will not say "Daily every day", it will say Daily.  Also it will not say "Daily every 2nd day", it will say "Every second day" - translateable.
* The COUNT limit was being applied too early, most of the time okay, but could give incorrect result in certain circumstances.
* DTSTART (hardly ever displayed - the original start of a recurring series) was being updated by most recent event date.  It should always stay as the original DTSTART, distinct from the event date.
* The ical spec oddity where DTEND is one day more than the human thinks it should, cropped up again - fixed.
* Slight change to pagination so that if one has clicked through from a single day in a box calendar, it is easier to "show more days".  Please note that using basic html it is entirely possible for you to add  your own pagination before or after the calendar or agenda shortcode.
* Fixed a small calendar problem with ics files that had recurring events defined in a timezone (UTC) where the main timezone was something else.  Now if you ensure that you consistently use  the same timezone in your website, the day links should link to the 'correct' day as the human would expect.

= Version 3.7 =
* Added CURL option to "follow redirection" to not "break" the calendar if your ics host decides to redirect the file.  Note: Your ics fetch may be slow if it is following a redirection.
* Timed certain day repeating dates (RDATES) will now show the correct time
* Timed events with excluded dates will now have events on the excluded days excluded.  Note: excluded days overwrite RDATES above if the dates and times are the same.  This is because it is possible according to the spec to have RRULES and RDATES in same event and one may wish to have an excluded date for the RRULE.  So it is possible to have the 3 co-existing, and thus a priority must be assigned.
* The paid version now allows use of featured images / post thumbnails

= Version 3.6 =
* Added code to cope with the php date_modify problem when adding months - it does funny things near the end of month, particularly around months like Feb.  Dates like last day of month, last sunday of month,  etc should now repeat correctly without skipping February.
* Added additional code in attempt to cope with people whose ics file urls have been "moved" - eg: the ical.me.com files.  At the least a message will display to let you know  the the ical server has not send the ical file, but a "moved" message.
* Rdates were not parsing if passed in an array - fixed
* Day for calendar was being assigned based on original day and timezone.  The day of week can be different if the display timezone is different from the events timezone.  Moved the conversion to the display timezone before the day assignment.  Do not move too soon as all recurring etc calcs must be done in original timezone, not display timezone.
* all day pretty printing improved - thanks to ben for the suggestions.  If the allday field is requested to be displayed, it will show a translated "all day" - you can add your own brackets if you want.  If not all day, nothing will be shown.
* added update of danish translation files for the free version. Thank You Georg.

= Version 3.5 =
* Fixed bug in pretty printing of recurrence rule
* Corrected recent bug that prevented correct parsing of recurrence rule
* Removed htmlentities from google map link as google does not want it encoded.


= Version 3.4 =
*  Fixed link bug if you clicked back and forth between agenda and calendar enough, it lost the page_id
*  Fixed bug that happened if your server had to have a CURL call (http request did not work).  Bug was introduced in 3.3 - Apologies.
*  Fixed version numbers around the place

= Version 3.3 =
*  Fixed link bug in small calendar widget - missing global variable
*  Added links to new support forum
*  Reworked some of the remote file fetching logic.  It was not working well with some hosts.  One could browse to the ics file, but not fetch it remotely.  It seemed to work for more hosts now (including facebook), but please let me know asap if you ahve any funnies.

= Version 3.2 =
*  Added French translation files
*  Minor bug fix for css file copying error (from plugin css to a uploads location for custom css so that it did not get overwritten) - only occurrs on some systems
*  Added option to include the 'box calendar' month year navigation in the agenda view.  Add show_month_nav=1 (or true) in the shortcode parameters.  To avoid unrequested changes for existing users, in agenda view, the month navigation will not show unless you add the parameter.  It will show in the calendar view.
*  More inline help in the configuration area
*  Download link for standard css file to make it quicker for you to edit it.
*  Added to css file to attempt to make default css apply to more themes. Since some themes are quiet different this may not always succeed.  The css is quite verbose as a result to cater for those who are not so css competent. If you are pedantic about this, please create a custom css and thin it down.

= Version 3.1.1 =
*  Some changes to the "add to google" link required for amr-events plugin where there may be html content.
*  Also added some additional inline help in the configuration area

= Version 3.1 =
*  Feature to switch off title text on event link in upcoming events widget was briefly lost when the small calendar widget was introduced. This fixes that.

= Version 3.0.9 =
*  very minor update for people who want greater than one month in agenda view, but still have calendar view (one month).  The agenda view in initial mode, still show future dates only (unless modified with offset parameter), calendar view will however show all dates for the current month.  IE: calendar view will now perform the same whether you use a [largecalendar] shortcode, or the [events] shortcode

= Version 3.0.8 =
*  tweaked default css and month navigation html a bit to accommodate websites with smaller content width for table.
*  forced initial large calendar load to months=1, despite shortcode entry.  This then allows you to have an 'agenda' view with multiple months but have correct functioning in the calendar box
*  fixed generation of ics url. It was not removing page and other parameters from the query string of the url page.  We do need to allow for parameters so we can handle ANY taxonomy, category, tag, author view etc and generate corresponding ics feed, but we cannot leave inappropriate ones in as they will mess up the gathering of events for the feed.

= Version 3.0.7 =
*  large and small box calendar formats available, and a calendar widget, as well as tab views to integrate with the list view. Default css provided.
*  minor bug fix where some query parameters (eg: taxonomies) were not being passed through so taxonomy widget use failed
*  update of the Danish translation

= Version 3.0.6 =
*  Change to the widget for the amr-events plugin that calls this code.  Does not affect free version.
*  Fixed some html validation errors in the admin side
*  Added some more explanatory text to admin screens to make things clearer I hope

= Version 3.0.5 =
*  List type name and internal memo description will now update. Not necessary for front end anyway - purely for admin info.  But it was an inexcusable error - the old upercase/lowercase problem!
*  Copes with Zimbra Timezones that Zimbra does not say it issues! Will also not fail now it it cannot make sense of the timezone - it will use the sites timezone with comment.
*  Also allowed for lowercase mailto (found in Zimbra file) when uppercase was expected

= Version 3.0.4 =
*  Fixed css file handling.  It will now copy the standard css file to a special css folder in the uploads folder, and then offer you the choice of which css file to use (or of course just use your theme's css).  The custom ccs file can be customised without risk of being overwritten by a plugin upgrade.  This also means that you can switch back to the default css to see if that works better for your site.
*  Some tweaks to the html code generated and to the css
*  Some tweaks to the default list type settings - you would have to 'reset' to see these.  SAVE your current settings first (ie print the page or screen dump first).
*  Allowed for php version with url validation bug.  If you have that version or earlier it will NOT validate your urls for you when you enter them.

= Version 3.0.3 =
*  Fixed bug in the trim_url function

= Version 3.0.2 =
*  Tweaked the list style options a bit to improve the look when there is no css
*  Tweaked the css
*  Fixed a bug that had crept in - it is now possible to add some html tags before and after fields
*  The link text for very long urls is now trimmed if it begins with http:// .  This should help if you find your theme css cannot cope with long urls.

= Version 3.0.1 =
*  Fixed the version numbers - need to be same in 3 places
*	Admin page html/css went a bit wonky - actually the wordpress admin navigation css - the subsubsub usually requires careful handling for the next piece of html.
*  The widget was overenthusiatically thinking it needed to get events from posts too.  fixed.
*  Applied clean url function to the widget event urls -  it will now validate
*  Removed opacity css for now (invalid css).  Better css control will be coming later

= Version 3.0 =
*  Fixed a minor bug with dates that did have an end date,but duration was 0.  Some php datetime installations did not take kindly to being told to add 0 anything.
*  Almost Totally rewrote the recurrence engine - it will now cater for ALL valid recurrence rules
*  Woo hoo - hopefully a big improvement in listing options - the start of many more!  Please note - they will change again - they need to be 'cleaner' in their html - will be along the same lines though, so have aplay if you wish.
*  Caters for all kinds of weird and wonderful timezone ids (in response to [lespaul](http://wordpress.org/support/topic/plugin-amr-ical-events-list-date-time-problem?replies=7).  See also [this note] (http://icalevents.com/2613-what-every-developer-ought-to-know-about-timezones-wrt-calendar-files/)

= Version 2.9.5 =
*  Will now cope with ics files that have their dates TZID's enclosed in quotes.
*  Offers options other than a table for the styling - this will help with themes that do not like tables in their widgets
*  Does not issue tabel header html if there is no heading
*  Adds a list type (increase your number of list types to 7 to see it) for eventinfo of plugin amr-events
*  Finally fixed the extra slashes that kep appearing in the week format string (use wordpress stripslashes_deep).  Now you can have Week x ! (use "\W\e\e\k W')

= Version 2.9.4 =
*  Some php installs have a datemodify function that issues a warning when passed a 0 (Not on my site, so sorry I did not pick it up!).   Calls to the php function, now check first modified to prevent this warning.

= Version 2.9.3 =
*  Fixed a very small bug that crept in after hoursoffset was added I think.  Offset were not working correctly.
*  Last refresh time (displayed on hover of refresh icon was being repeated - fixed.
*  Tweaked the summary event url business a bit.  If no url and no default event url either, then no link!

= Version 2.9.2 =
*  Offers event styling by event categories. IE: if there are categories in the ics file, these will be echoed as classes on the event row.  See (styling of events) [http://icalevents.com/2382-styling-of-ical-events/]
*  Fix for display bug noticed by [shanafourder] (http://wordpress.org/support/topic/426964?replies=3#post-1610258) where if the ics event spanned 2 days in it's original timezone. (since some ical generators reduce all events to the UTC timezone, since this very possible).  In this instance the end date would still be shown even if on conversion to display timezone it was the same day as the start date (normally suppressed).  Behaviour has been adjusted so that decision not to show end date is made in the display timezone.
*   Update of Danish translation from GeorgWP

= Version 2.9.1 =
*  Removed debug statements relating to unreleased new features - should NOT have been in 2.9.1. If you have 2.9, please update to 2.9.1 asap.
*  Simplified the css a bit, to let theme styling take more effect - removed the box around "today's" events and removed the alt styling on the rows.  If you liked these, then create a custom css in the plugin folder, copy the css from one of the older files.

= Version 2.9 =
*  Prompted by [Jillian's request](http://icalevents.com/troubleshooting/comment-page-1/#comment-607), the widget's event summary hyperlink with description text is now optional.  Untick the widget's option and it will give you a leaner widget, with no further information on the event, unless of course you add additional fields via the list type settings.
*  Fixed a new bug (introduced in last version update - sorry) that shows up with exceptions and modifications.  Thanks to Georg for advising.
*  Added the hours parameter similar to months in prior version.  This is an alternative to days, and months and will override those settings.  The startdatetime of the listing will be set to the beginning of the hour for consistency with the other parameters.
*  With hours added, the days parameter has been changed to start with time 00:00:00 (days used to be 24 hours starting from now).  They will now be clean days, and so the events from earlier in the day will show as history.  If you wish to stay with only current or future events, then enter the appropriate number of hours (ie: days*24). This allows [polyfade and Jaguwar](http://wordpress.org/support/topic/396038) to have their one day calendars (use days=1).

= Version 2.8 =
*  Fixed warning html (missing a closing tag) when the url is unavailable and there is still a cached file available.
*  Tweaked the code in the cacheing area to handle upload folder specs better (I hope).
*  Added a months parameter.  Number of months requested will override number of days and the start will be set to the beginning of the month.
*  Added monthsoffset parameter.  This allows you to go back in time by months instead of days.  Useful for pagination which is coming soon.
*  Changed the html around the calendar property images so that it worked better when no images are displayed. Changed the css file too.  Previous is still there as icallist271.css
*  Made some criteria code changes anticipating integration with custom event post type plugin coming soon.
*  Added option to not use the little icon images and just have plain link text.  THis would allow you to use custom css and text / image replacement techniques for your own images.

= Version 2.7.1 =
*  Added some exception handling to cope slightly more gracefully with any "bad" dates in the ics file.
*  At [Alec's request] (http://icalevents.com/troubleshooting/comment-page-1/#comment-581) titles have been added to the little images.  There were already titles on the hyperlinks and alt on the images.  Html has been revalidated.
*  Fixed a bug that occurred if you had one timezone in your wp and another in the ics file events and had recurring entries that went over a daylight saving change in the files. Recurring events will now have their repeats generated in the original timezone and then converted to the display timezone, not the other way around.
*  Updated the default css, so that text will align at the top of the table cells in the new twentyten default theme.

= Version 2.7 =
*  A bug fix for all day recurring events that have had a instance modified.  On the day of the modified instance, the plugin was showing original details, not the modified details, It will now include the modified instance and thus reject the old instance.
*  Revised Admin interface - the old interface was getting very slooooow.  So it has been broken into multiple pages and some javascript hide/show logic to reduce the volume of data on the screen. It may need a bit more tweaking later.  It still stores everything in the same one large option to avoid upgrade issues.
*  Moved admin styling to an enqueued file rather than in code.
*  Use of debug parameter will switch on all warnings and notices - this may show up warnings and notices for other plugins and/or wordpress too - Do not panic!
*  Default number of days increased - affects new install only.

= Version 2.6.12 =
*  A bug fix for yearly anniversaries at end of year.  On php versions less than 5.2.5 the date_modify function does not cope as well as later versions with a blank duration.  An error in the duration calc caused a blank duration for events repeating at end of year.

= Version 2.6.11 =
*  A bug fix for those who experienced date_modify unexpected character errors when using negative date or hour offsets.  The problem was not occuring on my site, so hard to verify that these changes will fix it, however I did find some code that although behaving itself on my sites, could conceivable cause a problem elsewhere.  Also cleaned up a few minor 'notices' that appeared when all levels of php messages were switched on.

= Version 2.6.10 =
*  Allowed for recurring event rules with numeric "BYDAYS" positive and negative.  See [examples](http://icalevents.com/2162-ical-positive-and-negative-numeric-bydays-now-implemented/)
*  Removed css styling for feeds as this was breaking some feeds and is not necessary for most people.  I tried many other ways (filtser and rss actions), but have not yet found an acceptable way to include the stylesheet for those few who may have calendars in your posts.
*  changed the bookmark name anchors to id's for html 5 validation
*  changed specific group id's to classes since if you have multiple calendars on one page with same grouping, this would fail html validation
*  ensure that whitespace was properly handled for the "add to google calendar" option for HTML5 Conformance.

= Version 2.6.9 =
*  Fixed Bug where new install need not get the default options for the widget.  Plugin had anticipated upgrades to a certain extent, but not a totally clean install.
*  Also did quick check through on wordpress 3.0 beta on shortcode and wigget - all seems fine.  Also checked it out on the new default wordpress theme twentyten - no problems there either (eg: in old default we had css problems due to li styling)

= Version 2.6.8 =
*  Fixed floating time creation problem recently introduced - it was creating in UTC timezone (and then converted to wordpress install timezone), when they should be created directly in the wordpress or plugin requested timezone. See [floating times](http://icalevents.com/2064-ical-local-or-floating-date-times/) for commentary.
*  Fixed bug where multiple changes to single instances within a recurring entry where not always handled correctly
*  Changed widget handling to use the multi instance widget API.  This means that you must at least be using wordpress 2.8.
*  Widget option setting is now simplified and follows the shortcode syntax.  So now anything you can do in the page or post with a shortcode, you can now also do in a widget. I have attempted to convert your prior settings to the new setup.  PLEASE check your widget is doing what you expect it to after the upgrade if you had made any special changes.  Note the widget defaults are still events=5, days=30, and listtype=4.  These do not have to be specified if you are happy with them.  See the shortcode usage section on the plugin webste front page.
*  Because of the change to allow multi-instance widgets, the provided css example now uses classes instead of id's to allow generic css for multiple widget instances.  You can still isolate individual event lists if you wish as the unique id's are still provided in the code.  The previous css file is still there if you wish to use that rather.


= Version 2.6.7 =
*  Fixed end time on non repeating events that did not have durations. (Bug introduced when making recent other fixes, so is not in earlier versions.)
*  Fixed some hmtl validation errors that had crept into the admin settings page.
*  Fixed some link cliakability errors found: Replaced the custom preg replace strings with the wordpress function make_clickable as it now copes with more urls.  Note the eccentric holidays calendar on googel is a great one to test this with.
*  Added a link from the settings page to a webpage explaining the date localisation options added.

= Version 2.6.6 =
*  Minor code change to do with modifications of singles instances within recurring series, with timezones.  This bug only occured in certain setups on certain servers and rather weirdly did not occur on preview, but only on publish.

= Version 2.6.5 =
*  Change cache logic so that if the remote ics url is unavailable, then the local cached file will be used if it exists.  The viewer is told the date and time of the last cache.
*  Tightened up some of the repeating logic
*  Fixed exceptions bugs where date modifications where not accurately treated.  It will now cope with event where an instance could be shifted either in/out of the current date range. Added &debugexc to debug exceptions.
*  Wrote own version of wordpress date localisation date_i18n function.  The wp function requires the dates to be converted back to UNIX.  My version uses the same logic but stays with the DateTime object.  This seems to give more consistent results when there are multiple timezones involved.
*  Added option to use either date localisation functions or to use none (eg; if your blog is in English).
*  It will default to no date localisation for english blogs and the amr function for non english.
*  Fixed bug where it lost/forgot to list the css file after upgrade or on initial install.
*  Added a jump to list type in the config menu for newbies who don't realise they should scroll sideways to see the list.  They are sideways so one can compare settings.
*  fixed minor bug to do with adding refresh query arguments on permalink sites.
*  Added code to deal with Mozilla Thunderbird issuing X-MOZ-GENERATION, instead of SEQUENCE for recurring entry instance modifications.
*  Added information on the last modification made in the ical file as sometimes for example google is slow is sending out the updated file.  IE: one sees the update on google, but on cache refresh, google send the previous version of the file.  This "last modified" information will be displayed after the "cache time" on the refresh button title text.

= Version 2.6.4 =
*  A further tweak on using the wordpress date_i18n function with and without timezones - using parameter gmt=false. I was not experiencing any problems on my server, however suspects that some whose server time is different from their wordpress time, may find this sorts out their problem.  Please check the settings page to see what the plugin say's the current time ins, and then further down what the various formaats display the time as to make sure the plugin is working well with your system.
*  Added more debug statements for use in assisting with other people's setups.   (Note can use &tzdebug in your calendar page url to only get timezone related debug statements.)
*  Fixed situtaion where another css file placed in the plugin directory was nt actually goingto be used! Thanks Matt for pointing that out.
*  Some language updates - more to come.

= Version 2.6.3 =
*  Well now, having spent a large part of the holiday getting down to the nuts and bolts of what needs to happen for complicated timezone situations and localisations - I think it is sorted out now Re 2.9 Don't upgrade yet if you haven't - wait for 2.9.1, or if you have upgraded go to 2.9.1 beta 1. I am not sure about 2.9.  It seemed to be that when I tested with a plain gmt offset setup in 2.9, things were a bit strange.  So all testing has been done in 2.9.1 beta. See also this 2.9.1 fix note http://core.trac.wordpress.org/ticket/11558

= Version 2.6.2 =
*  WARNING: change date and time formats to use wordpress's date_i18n (again) to get better localisation. If you want the date_i18n functrion to be used to localise your dates and times, then DO NOT use the strftime formats. Strftime formats can be used - they will not be pased to date_i18n.  See the date formats at http://www.php.net/manual/en/function.date.php.   So even though php says strftime localises, in wordpress it does not, but the other will!
*  Changed use of foreach ($arr as &$value) to modify the array as it seems some folks get a syntax error there, even though http://php.net/manual/en/control-structures.foreach.php says you can do it.  Other googling says the implementations may be inconsistent, so thos construct has been avoided.

= Version 2.6.1 =
*  Additional shortcode or url parameters added to allow the time offset to be specified in hours.  Previously could do in days only (positive or negative - ie forward or back in time).  Use hoursoffset=n   (plus or minus).
*	Date/time and Css logic added so that events in "progress" will be flagged with a class of "inprogress", else "history" for completed passed events or "future" for events not started.
*	The setting of the start time to the beginning of the current day has been removed - it will now set to the current time.  This means that only in progress or future events will show in a default setup.  If you wish to show events that have just passed, then use a negative hours offset.
*	For those who like to play around with the options without going back to admin options, you can do quite a bt through a URL or in the shortcode.  A recent addition is grouping=txt, where text is on of the allowed groupings as seen in the settings. EG: Day, Month, Year, Quarter, Astronomical Season, Traditional Season, Western Zodiac.

= Version 2.6 =
*  See (http://icalevents.com/1901-widgets-calendar-pages-and-event-urls/)  Event summaries/ titles in the widget will jump to the event detail in a calendar page if
    *  the calendar page has been specified in the widget
	*  the calendar page is using the same ics file as the widget (duh!)
    *  there is no event URL for that event in the ics file.  Google for example does not allow one to define a event URL.
*  Additional css file provided which includes css to hide the description if  displayed in widget, and then to display the description when hovering over the event.	See (http://icalevents.com/1908-hovers-lightboxes-or-clever-css/)
*  Fixed typos affecting language domain

= Version 2.5.11 =
*  Coped with weird tzid path spec that some ical generators seem to introduce.  Ical Spec is not clear, but it probably should not be there.
*  Changed startdate day to start 1 second after midnight to avoid isolated all day events from the previous day
*  Tweaked css so that historical events on "today" are styled like "history", not like "today"
*  Changed action when no events are listed - message becomes a "info" link - if you hover over it, then the parameters are shown.
*  Added some additional error trapping for those who have problems with their server setup.

= Version 2.5.10 =
*  Fixed a widget bug that got introduced somewhere down the track where the widget list type was not properly being deduced.  Thanks to Gary for identifying that the widget list type format was not being used.
*  Also tweaked the default widget cssa little so that grouping headings would float to the left for widgets only, in case one wanted to group within the widget (default is not to group).

= Version 2.5.9 =
*  Added pseudo clone function for people not on later versions of php, to mimic the clone command ( as per http://acko.net/blog/php-clone) so they won't get a parse error, but will later get told they need a better version of php!

= Version 2.5.8 =
*  Changed the call to php get_headers (to check if remoteurl exists) to the wordpress wp_remote_get so that people with servers which do not allow remote url open will not get errors.
*  Changed default css (previous css included as an option, in casae you prefered it - NB you must change name to avoid it getting overwritten later). The default css had some non functioning css where event times were meant to float up next to date, but were not.
*  Default css now uses opacity to "grey out" events in the past, rather than the same background colour as the 'alt'.  The background had confused people as they thought there was some kind of alt error.
Note an event is styled as "history" if it has started already, although it may not be finished yet.  historical dates only show if either they were earlier on the current day (all events on current day are shown by default) OR a startoffset has been specified.

= Version 2.5.7 =
*  Added multi-url support back into the widget and expanded the field a bit to give more space.  NOte: separate url's with commas.
*  Added more validation around the input there.
*  Tweaked the default css for the widget slightly to remove any theme related padding or margin's on the table and just use the list item spacing - should give a more consistent look with other widgets

= Version 2.5.6 =
*  Fixed bug where although corrected end date to (end date -1) - spec says all days ends on next day at 00:00:00 for single all days, it was not doing it for multi - days -resulting in an extra day
*  Adjusted code to ensure that an "already started" multi day event is still listed if it has not finished before current day. (Note: you can also use startoffset=-n  where n is an integer to force the start of the list back a few days.)
*  Attempted to correct for ics generators that do not follow the all day logic as noted [here] (http://www.innerjoin.org/iCalendar/all-day-events.html).   The php "WebCalendar" is at fault here too.  Unfortunately one can only correct for single day all day events.  For multi day, it is not possible to know whether a 2 day event was intended or not, or whether it is a correct implementation of the logic. Take it up with whoever is generating your ics file is this is a problem for you.
*  Changed css tags slighty to offer hcalendar microformat support:
*  - basically the fields that had come direct from the ics file were in the original uppercase (eg SUMMARY) however hcalendar says the classes should all be lowercase.
*  - removed the duplication of some classes from <td> - they are on the <tr>.  THis was breaking hcalendar.
*  - The matching css file has been checked - if you had your own css, you may need to check whether you need an adjustment.
*  - added url css tag for hcalendar support.

= Version 2.5.5 =
*  Fixed bug where check for ical all day, but single day (shows up as day1 start, day 2 end) caused a problem with other dall day, but multi day - we lost the end date!

= Version 2.5.4 =
*  Added warnings about needing to use shortcodes only - replace the ":" with a space in your caledar page if you have not already done so.

= Version 2.5.3 =
*  Made changes to cache folder creation due to possible errors experienced with people on shared servers with php safe mode enabled.  If you have problems, add ?debug or &debug to your events page url and refresh.  The debug messages may tell what you the problem is with your server.
*  fixed problem that had crept in that meant the debug option of a url by query string was not working
*  changed days default to 90, not 30 as many folks just wanting widget do not look at config settings, just widget settings.

= Version 2.5.2 =
*  Really fixed widget timezone now - it was going back to server timezone even though it had worked out the wordpress timezone - problem with bad choice of shortcode default!

= Version 2.5.1 =
*  Fixed bug: Code was added to handle keeping your settings while adding new features and field options.  This temporarily showed your updates, but then on next view of config page, the settings were back to default.  The recursive merge of old and new settings was defaulting the wrong way.  Looks like it is fixed on my system.  Please let me know asap if anyone still experiences problems.

= Version 2.5 =
*  Timezone bug corrected - should now pickup timezone correctly - Order of global timezone priority for display of events is 1 query url, 2 shortcode, 3 the wordpress timezone or offset.
*  fixed widget parameter funny - (note cannot override widget from query line, only calendar page can be overridden.)
*  added css to float widget calendar properties (icons) to the right, in case someone does want to show them on the widget.  (Set this up in the config).

= Version 2.4.2 =
*  Timezone in shortcode now possible.
*  Removed attempt to copy icallist.css to a custom css for local edit as that was hitting folder protection issues and confusing people - will rethink that, meantime you can drop your own copy file into the plugin directory if you wish, and the plugin will pick it up in the admin screen as an option.

= Version 2.4.1 =
*  Timezone fix - should get wordpress timezone correctly now, not server timezone.

= Version 2.4 =
*  **** NB Dropped the outdated filter method for specifying the spec as pre-warned. Now only using the wordpress shortcode.  This is a simple update to your calendar page. Use [iCal yoururl listtype=1] ***
*  fixed a bug which occured with recurring entries that were defined by COUNT
*  fixed a bug which occured when a single instance of a recurring series was modified.
*  added more css classes at row level as well as the first column.  First column is usually the date column, so now can just style the dates differently or the whole event or todo record.  You can style entries in many ways (eg style recurring entries differently, as well as by the status.  For possible status values - see http://www.kanzaki.com/docs/ical/status.html.
You can style by the component type (vevent, vtodo, vjournal, valarm)
*  added css classes so that you can style past, today and future events differently
= Version 2.3.8 =
*  added some more language information and files, cleaned up some of the translation.
*  Some people are experiencing timezone problems - this appears to be caused by the use of wordpress's date i18n to localise the formats.   Reverting to original code seems to remove the problem.   [Setting the server timezone may also correct the problem] (http://webdesign.anmari.com/timezones-wordpress-ical-php/)   Since correct dates are more important than correct formats, I have reversed the code, until there is more clarity on what date_i18n is doing and how to get timezone correct times using it.  If you needed it for your web, you can stay with the previous version or uncomment line 936 and comment out line 935 in amr-ical-events-list.php and then check times very carefully!

= Version 2.3.7 =
*  changed use of htmentities to htmlspecialchars - avoided probledm with dashes in event subjects.
*  added more explanatory text in readme

= Version 2.3.7 =
*  changed to use wordpress date_i18n for date and time, to achieve localised dates
*  cleaned up some text and added some rudimentary language files for German, Afrikaans,
*  use wordpress check for cache directory creation
*  reset now resets global options too, and few other minor rest problems fixed
*  default list types tweaked a bit - reset to see changes, but note you will lose your settings then

= Version 2.3.5.3 =
*  added checks for php version and datetime class for people who cannot read doco, or comments!
*  added ability to define a Default Event URL in the event that there is not one in the ics file.  Plugin will generate a dummy bookmark, with info cursor style and event description as hoevr text/title.  the dummy bookmark is stop page reloading and make link non-active.

= Version 2.3.5.2 =
*  fixed bug to do with combinations of timezone non specification and date values.
*  fixed some html validation bugs due to entities etc for sophisticated html in adding google event - google sort of half way handles html!
*  added a numbered css class hook amrcol'n'  to the td and th cells so that you can style the columns independently (eg: by width)
*  the css included now has the first column styled at a width of 20%
*  Please move to shortcode usage if you have not already, as I will eventually phase out the older mechanism.


= Version 2.3.5.1 =
*  fixed bug where if the start of the recurring was way way back in the past and the number of recurences in the limit did not get the recurrence date to the start date, then the instance was skipped.  Now is a parameter that allows 5000 recurrences - that should be plenty? We could get clever about this later.
*  Allow DTSTART to be shown - eg: for birthdays if you really want to tell the world, or maybe to indicate how long a show has been running?
*  Age (or for a how "Running since.." is in option list, but not listed for now....coming soon
*  Changed http to webcal at Brendan's suggestion - to subscribe rather than download.  Let me know if we should offer both.
*  Move location of cache file to the uploads folder.  This made more sense to me.  Note that your uploads folder should be a relative url as per the example given.  Wordpress seems to wokr with an absolute url however this will cause problems if you ever having to move your blog, so follow the default shown and go relative.  eg: to move up - "..\uploads".

= Version 2.3.4 =
*  Added Default Css to cater for themes that use list-style definitions such as background and before content.  We need to switch these off for the plugin code to look okay.  Once can of course also just edit the theme's stylesheet, but this may be easier for some.  Thanks to Jan for querying the problem.
*  Will handle shortcode usage now ie: [iCal "youricsurl1" "youricsurl2" listype="timetable"]

= Version 2.3.3 =
*  Changed the user access level to 8, so only admin can do setting changes, not editor, previous version allowed editor to change settings.
*  Fixed bug where the relocated refresh icon did not actually refresh if you had no "?" in the url.  Also allow 'refresh=true' instead of 'nocache'.
*  Changed form security to use new 2.7 wordpress "nonce" functions.  This prevents cross scripting in a stronger way than before.
*  added an uninstall option which will delete the option entries, either by request from the settings or when the plugin files are deleted (if using wordpress 2.7). Note the reset button will delete and recreate the default Amr iCal options in one go. The uninstall is added for completeness and for your use if you no longer need the plugin.
*  Made settings menu entry look prettier - tightened up the text and added calendar icon
*  "Bling" classes for the link icons added so that canbe not displayed when printing. A print stylesheet has also been added to achieve this.
*	Added alt text on the settings icon in the admin menu to ensure that the admin page still validates 100% with html - on my code anyway.
*  Added option to specify own css rather than automarically loading ical css.   You should ensure that the necessary css is in your theme stylesheet then.   This allows you to make your pages more efficient by reducing the number of files required to load.
*  An settings "RESET" will now also reset widget settings, not just the main settings.  Remeber to save any special settings if you do this.  A reset may be necessary if you have an old version and want to take advantage of new options and defaults.
*  Removed the line breaks for the widget event summary 'titles' that appear when you hover on the summary. This looks better and does not require any javascript.
*  Clarified the widget calendar page option and attempted to default it to what you might have called your calendar.  You may need to reset to see this happen.

= Version 2.3.2 =
*  Fixed bug if there was a url for the event.  (The url is entered as a hyperlink behind the summary text).  Thanks to Ron Blaisdell for finding this.  Currently in google one cannot setup a URL for a event.
*  Removed testmode comment when iCal url passed in query string, allow possibble "API" use.
*  Straight after importing events in the timezones specified by the ical file, they will be converted to the timezone of the wordpress installation.  THis ensures that "same day" and "until" functions.
*  Plugin will determine a default php timezone from the wordpress gmt offset if the automatic timezone plugin has not been installed.
*  If the wordpress timezone is different from the calendar timezone, one can click on the timezone icon and refresh the page in the calendar's timezone.
*  Set the defalt start time to the beginning of the day that the wordpress timezone is in, so that we
can also see events that might have just started.
*  Changed the refresh link to be next to the other calendar property icons and put the last cached time in the alt text and title rather than at bottom of calendar.  Also fixed how it reflected time relative to the server timezone.
*  In the "Add event to google", improved handling of complex details - google only handles simple html.  Note: bad calendar content can still break google (for example the valentines day entry has an errant "/")

= Version 2.3.1 =
*  Changed some error detection and reporting to improve user experience - moved many messages to comments if no data or bad url entered
*  Fixed the way the widget was interacting with the main plugin
*  Corrected an error that was visible when the calendar timezone and the wordpress timezone were different.  This showed up on single events only as google offers a UTC date, not a TZ date and the plugin was not dealing with this correctly.  Plugin will work now if wordpress timezone and calendar timezone are the same.  More work is required though to make it more robust and cater for different situations - coming soon.


= Version 2.3 =
*  Simplified css styling by deciding that a list of events was essentially a table and going back to the table html - this avoids problems with many less robust themes.
*  Css file spec changed to one at global level (Icallist.ccs)  If the file does not exist, it will assume that you have included the necessary styling in your theme stylesheet.
*  Added icons to allow for clean look, while still having functionality of options.


= Version 2.2 alpha =
*  Removes duplicated events that may be generated by your ical generator.  For example if one instance of a recurring event is edited.  Implementing the recurring rule generates an event instance that matches another event in the file.  They will have the same UID and date, but a different Sequence ID.
*  Improved the imezone and date handling uses PHP 5 dateTime class and timezone object functionality.  Somewhat tested - again good test situations are required - around daylightsaving time is really interesting.
*  column headings not in use yet (but enterable) - need to convert to table output - coming soon I hope.
*  calendar Subscribe link available if 'icsurl' requested in the settings for a list type.
*  can test by passing iCal=url:listtype=n in the query string of any wordpress page - the page content will be ignored.
*  css changed slightly - more testing required for impact on different themes.
*  removed the </p> added to make wp validate - not required anymore in latest version of wordpress ?
*  allows for other ical components such as todo lists, journals and freebusy (maybe for use as availability!) - this has been slightly tested, not up to my usual standard.  Good test files are required.  If you have a need for this and think there is an error, please send me your files or links to your public files.  It uses the same logic as the event, so differences may just be a question of layout and style.
*  improved conversion of urls to hyperlinks in long text fields like description - will now handle all sorts of links including bookmarks.  I had a bit of fun (not) dealing with <br> after urls!
*  changed some defaults - simplified - commented out some that are unlikley to be used.
*  allows for repeatable properties - in theory one could have multiple summary fields for one event etc.
*  Todo: implement more complex recurring rules, more thorough testing, some user documentation and ideas, simplify the css.

= Version 2.1 =
*  datetime formats, name and css file now update and save in admin menu- no need to go to config file;
*  deleted ridiculous grouping option solar term!!
*  added code for grouping options that people may actually want to use (Seasons, astronomical etc). [Seasons on wikipedia] (http://en.wikipedia.org/wiki/Season#Reckoning)
*  Zodiac grouping added just for the fun of it [Zodiac] (http://en.wikipedia.org/wiki/Zodiac)
*  Quarter grouping added - change dates in the config file if fiscal or tax groupings required.

= Version 2.01 =
*  added check for existance of validation function filter_var (introduced in 5.2).  No/Limited validation in admin if it does not exist.  Ask your host to update.
*  changed css to specify width for first col so that all rows look the same
*  switched timezone fields on by default in listtype 1.

= Version 2 =
*  repeating events, no table all nested lists, lots of configuration options.

= Version 1 =
*  Listed events without repeats into a table with nested lists. It allowed for a monthly break, a config file and had a default css file

= Version 0 =

== Frequently Asked Questions ==
 see the plugin website (http://icalevents.com)

== Screenshots ==

1. Screenshot with monthly grouping and "add to", timezone and subscribe to icons
2. Widget screenshot in Golden Essence Theme - description shows on hover of summary
3. Three Column calendar list
4. Freebusy in widget - shows non availability.  This example has weekly grouping.
5. Part of Admin screen showing options for a list type - multiple list types are provided for.
6. Part of admin screen showing how one can select the ical components and derieved pseudo components
7. Widget Admin screen, showing Title, No of events, List Type from plugin (default = 4 for widget), provision for multiple URL's, and link to calendar page.  the calendar page lin is inserted behind the title.
8.  iCal Specification on the page that you wish the calendar/'s to appear.
9. With locale set to German, showing german days of week, in Sandbox theme.
10. Just for fun - Multiple Groupings (unstyled here, but with styling tags, so imagine what you could do )

== Meta ==
Category: events
Language: en-EN
