=== Plugin Name ===
Plugin Name: WP Symposium Blog Post
Description: Blog Post plugin for WP Symposium: integrates WP Symposium with your WordPress blog.
Author: AlphaGolf_fr
Contributors: AlphaGolf_fr
Link: http://wordpress.org/extend/plugins/wp-symposium-blogpost/
Tags: wp-symposium, blog
Requires at least: 3.0
Tested up to: 3.9
Stable tag: 1.6.0
Version: 1.6.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

You've setup a WordPress site and turned it into a Social Network using WP Symposium, but the blog part of your site is important to you as well? WP Symposium Blog Post is the perfect addition to your WP/WPS site.

WP Symposium Blog Post plugin integrates WP Symposium with your WordPress blog, by:

* Adding a new menu item to your authors' WPS Profile page to list their posts, and link to them.
* Conversely, adding a link on your authors' blog posts to their WPS Profile page.
* Adding one row to their WPS Activity each time they publish a post in the blog.
* Adding one row to members' WPS Activity each time they comment on a post or a page.

NB: this plugin requires WP Symposium to run. You need to activate at the minimum both the core module and the Profile module of WP Symposium to use this plugin.

== Installation ==

= Installing the plugin =

Use the WordPress feature to install the plugin from the WP Dashboard, Plugins > Add New.

Alternatively, download the ZIP file from wordpress.org, extract its content and upload the folder wp-symposium-blogpost via FTP in your path-to/wp-content/plugins folder.

A WP Symposium Blog Post plugin should then be available through the 'Plugins' menu in WordPress, so that you activate the plugin.

= Upgrading the plugin =

Use the WordPress feature to upgrade the plugin from the WP Dashboard.  This process will automatically de-activate and re-activate the plugin.

Alternatively, download the zip file and extract its content locally. Deactivate the previous version to avoid any warning due to changes, and upload the folder wp-symposium-blogpost via FTP in your path-to/wp-content/plugins folder.  Re-activate the plugin.

= Configuring the plugin for your site =

A new menu item will appear in the WP Dashboard, under "WP Symposium" and called "Blog Posts", where you will find options for the plugin.

If you haven't done so already, create a WP page that will hold members' profile information. It must contain one of the available shortcodes for the WPS Profile page, which can also be the shortcode for blog posts. The shortcode you are using for this page will set the default display for this WPS Profile page. It isn't important that you create a page dedicated to blog posts, those will appear as menu items at the profile page anyway.

If you are using WP Symposium Profile page with a horizontal menu, at the dashboard WPS Profile Options page, new menu items will appear at the bottom of the WPS Profile page structures, called "My Blog Posts" on own WP Symposium Profile page, and "Blog Posts" on others' profile. The corresponding menu items "My Blog Posts" and "Blog Post" will be displayed at the WPS Profile page, as per the menu structure. It is advised that you check that the blog post menu items fit in your menu structure, especially with regards to roles and to avoid empty submenus for roles not checked at the WPS Blog Post options page.

If you are using WP Symposium Profile page with a vertical menu, a new menu item on the Profile page will be called "My Blog Posts" on own WP Symposium Profile page, "Blog Posts" on others' profile. You don't need to do anything else here.

= Customizations =

The file developers.txt, located in the plugin folder, details the available hooks for this plugin, as well as its CSS classes. This also explain how to customize the default display of the plugin, like for instance: display custom post types, add anything to posts rows, display posts by rows of two, three, etc.

== Frequently Asked Questions ==

* Can I use Blog Post without WP Symposium?

No, you can't, this plugin requires WP Symposium as it'll reside within this plugin

* Can I customize WPS Blog Post?

Yes, have a look at developers.txt, in the zipped package, it explains how you can use hooks and CSS styles.

== Screenshots ==

1. A new menu item to the WP Symposium Profile page
2. A post and its comments: the post author's name links to his/her WPS Profile page, and commenters names are URL'ed with a link to their WPS Profile page, whenever they are subscribed to the site
3. New WPS Activity items for post publishing and commenting
4. The dashboard options page of the plugin

== Changelog ==

= 1.6.0 =

* Bugfix: make use of plugins_url() instead of WP internal constants
* Code clean-up: make use of add_query_arg() instead of PHP soup using the query string

= 1.5.0 =

* Issue with roles accessing to the plugin options page
* In Activity, reviewed posts are attributed to the admin instead of the contributor

= 1.4.0 =

* Option to add the excerpt or beginning of posts to the list of blog posts added to the WPS Profile

= 1.3.0 =

* At init, ensure WPS is actually running and its needed functions were loaded, both multisite and single site install
* Issue with the registration of the plugin options page
* Danish translation by Louise Gram

= 1.2.0 =

* Thanks to Simon, WPS third-part plugins can now seemingly implement WPS Profile pages shortcodes, so this release takes advantage of this feature
* Options to add the excerpt or beginning of posts and comments to the WPS Activity rows

= 1.1.0 =

* Update the Js file name, otherwise it leads to a 404 when pressing the "more" link...

= 1.0.0 =

Important! Due to a plugin filename change, you will need to manually re-activate the plugin after this upgrade. For those of you who upgrade manually via FTP, make sure you remove completely the plugin via FTP before reuploading it, to avoid duplicated entries in the plugin management dashboard.

Updated as per WP Symposium v13.04. This (somehow) long awaited update of the plugin will add the following features:

* Upon plugin activation, automatically add a blog posts menu item to the WPS Profile page horizontal menu
* Blog posts comments authors can be automatically URL'ed with their members' WPS Profile page if they are registrered on the site
* Comments can be added to activity, for posts and pages, as well as any custom post types defined on your WP site

In addition, it also corrects the following issues:

* Make use of get_permalink() so that posts permalinks are consistent with WP Permalinks settings
* Restore the option to direct the author's links to the WPS profile page blog posts, which had stopped working due to WPS internal changes
* Correct the wrapper name in the plugin CSS file

NB: I'm switching to a more standard way of incrementing version numbers. As of now,

* First digit will tag major updates with significant additions in functionalities
* Second digit will be used for minor updates and bug fixes
* Third digit will tag only beta versions and work-in-progress

Now time for a 1.0.0 release... Enjoy!

= 0.0.14 =

* Updated as per WP Symposium v12.11 - both plugins must be updated synchroneously.

= 0.0.13 =

* Add the ability for the admin to edit the WPS Profile menu items.

= 0.0.12 =

* Fixes a bug introduced with 0.0.11, where the number of posts cannot be saved from the admin dashboard.

= 0.0.11 =

* Add a JS test to make sure WP Symposium is running, in case WPS Blog Post would be installed without the mandatory WP Symposium layer
* Admin side, add a test on the number of posts to display, must be greater than zero
* Remove an extra <br/> before "Nothing to show" which was shifting it by one line, as compared with other views of the WPS Profile page
* Make use of WPS_DEBUG

= 0.0.10 =

* Add an option so that admins can choose whether the link generated in the post metainfo shall point to the Blog Posts section of the WPS Profile page, or to the default WPS Profile page as set in WP Dashboard > Symposium > Profile > Default profile view
* For a multisite install, remove the hook symposium_blogpost_query_others_hook and replace it with symposium_blogpost_query_hook to ensure consistancy of the query for all sites
* Correct a typo in an add_action, which was generating an error under given conditions
* some cleanup in code

= 0.0.9 =

A maintenance release that contains a few minor updates...

* Add a test on the number of posts in the admin dashboard, to prevent any odd value
* Some cleanup in code comments, as well as documentation

= 0.0.8 =

Fix the link generated on post metainfo to author's WPS Profile page:

* An extra ""="" was preventing some browsers from browsing
* the query string is now detected, so that this link works on non-permalink installs

Also uses update_option rather than add_option, so first installers should have initial values at the settings page

= 0.0.7 =

* Add a link to WPS Profile page in posts' meta using get_author_posts_url()

= 0.0.6 =

Fix a number of issues when comments are moderated...

* Moderated comments wait until admin approval to trigger the addition of a row in the Activity
* Add the comment to its author's Activity when it's edited by an admin, and not to the admin's
* Remove the item from the Activity if the comment is unpublished by the admin... And re-add it whenever admin changes mind...
* When removing a comment from Activity, remove child comments from Symposium table
* Use comment_author_email to identify a subscribed member who would post a comment as an anonymous, and add an option to not accept that, as it's not password-protected
* Use GMT date/time to store comment in Symposium table

= 0.0.5 =

* In a WPMS environment, add an option to display blog posts of a limited number of sites only

= 0.0.4 =

* At "(My) Blog Posts" page, make 'more' work...
* In a WPMS environment, get the list of all posts published by an author network-wide
* Improve display of rows by adding CSS styling (even/odd, as well as by blog_id in a WPMS environment)
* Make date/time format consistent with General Settings
* Remove the option to choose to update as well the author's Activity when a comment is published: not consistent with the rest of WPS, and raises an issue for comments from non-members

= 0.0.3 =

* Update directly WP Symposium table 'symposium_comments' instead of relying on WPS classes
* Add one option to define the number of posts to display in the Profile page, at first call and each time 'more' is pressed
* Options to choose whether comments on posts and pages shall update the member's Activity
* Option to choose to update as well the post / page author's Activity

= 0.0.2 =

* Naming issue, making WP Symposium always think this plugin has to be upgraded at the Install page

= 0.0.1 =

* First release
